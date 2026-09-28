#!/usr/bin/env python3
"""
Hyu Standalone DICOM PACS Server & Modality Worklist Engine
Menerima citra rontgen langsung dari mesin Fujifilm FDR/FDL via DICOM C-STORE
Menanggapi tes koneksi C-ECHO dan kueri antrian pasien Modality Worklist (C-FIND).
"""

import sys
import os
import argparse
import datetime
import sqlite3
import numpy as np
import pydicom
from pydicom.dataset import Dataset
from pydicom.uid import ExplicitVRLittleEndian, ImplicitVRLittleEndian, DeflatedExplicitVRLittleEndian

import cv2
from pynetdicom import (
    AE, 
    evt, 
    AllStoragePresentationContexts,
    VerificationPresentationContexts
)
from pynetdicom.sop_class import (
    Verification,
    ModalityWorklistInformationFind,
    ComputedRadiographyImageStorage,
    DigitalXRayImageStorageForPresentation,
    DigitalXRayImageStorageForProcessing,
    DigitalMammographyXRayImageStorageForPresentation,
    CTImageStorage,
    MRImageStorage,
    UltrasoundImageStorage,
    UltrasoundMultiFrameImageStorage,
    SecondaryCaptureImageStorage
)

# Direktori Dasar Project
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
STORAGE_SCANS_DIR = os.path.join(BASE_DIR, "storage", "app", "public", "scans")
PUBLIC_ASSETS_DIR = os.path.join(BASE_DIR, "public", "scan-assets")
DB_PATH = os.path.join(BASE_DIR, "database", "database.sqlite")

os.makedirs(STORAGE_SCANS_DIR, exist_ok=True)
os.makedirs(PUBLIC_ASSETS_DIR, exist_ok=True)


def get_db_connection():
    """Membuka koneksi ke SQLite database Laravel"""
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def extract_preview_jpg(ds, output_jpg_path):
    """Mengekstrak pixel array dari DICOM dataset menjadi gambar JPG 8-bit berkualitas tinggi"""
    try:
        pixel_array = ds.pixel_array.astype(float)
        
        # Handle multi-frame (USG video/slice)
        if len(pixel_array.shape) == 3 and pixel_array.shape[0] > 3:
            pixel_array = pixel_array[0]
            
        # Normalisasi ke 0-255 (8-bit)
        if pixel_array.max() > pixel_array.min():
            norm_img = ((pixel_array - pixel_array.min()) / (pixel_array.max() - pixel_array.min())) * 255.0
        else:
            norm_img = np.zeros_like(pixel_array)
            
        img_uint8 = np.uint8(norm_img)
        os.makedirs(os.path.dirname(output_jpg_path), exist_ok=True)
        cv2.imwrite(output_jpg_path, img_uint8)
        return True
    except Exception as e:
        print(f"[HYU PACS ERROR] Gagal ekstrak preview JPG: {e}")
        return False


def handle_store(event):
    """
    Handler saat mesin Fujifilm mengirim file citra DICOM via C-STORE
    """
    ds = event.dataset
    ds.file_meta = event.file_meta
    
    # Ambil metadata penting dari file DICOM
    patient_id = str(ds.get("PatientID", "UNKNOWN")).strip()
    patient_name = str(ds.get("PatientName", "UNKNOWN")).replace("^", " ").strip()
    accession_no = str(ds.get("AccessionNumber", "")).strip()
    modality = str(ds.get("Modality", "DX")).strip()
    study_desc = str(ds.get("StudyDescription", "Thorax PA View")).strip()
    sop_uid = str(ds.get("SOPInstanceUID", "")).strip()
    study_uid = str(ds.get("StudyInstanceUID", "")).strip()
    station_name = str(ds.get("StationName", event.assoc.requestor.ae_title or "FUJIFILM_FDR")).strip()

    now = datetime.datetime.now()
    timestamp_str = now.strftime("%Y%m%d_%H%M%S")
    
    # Buat nama file penyimpanan terstruktur
    subfolder = now.strftime("%Y/%m")
    save_dir = os.path.join(STORAGE_SCANS_DIR, subfolder)
    os.makedirs(save_dir, exist_ok=True)
    
    filename_clean = f"{patient_id}_{accession_no or timestamp_str}.dcm".replace(" ", "_")
    dcm_full_path = os.path.join(save_dir, filename_clean)
    rel_dcm_path = f"scans/{subfolder}/{filename_clean}"
    
    # 1. Simpan file DICOM asli
    ds.save_as(dcm_full_path, write_like_original=False)
    
    # 2. Ekstrak preview JPG untuk Web Viewer
    jpg_filename = f"scan_{patient_id}_{timestamp_str}.jpg".replace(" ", "_")
    jpg_full_path = os.path.join(PUBLIC_ASSETS_DIR, jpg_filename)
    rel_jpg_path = f"scan-assets/{jpg_filename}"
    
    extract_preview_jpg(ds, jpg_full_path)
    
    # Update juga file raw default untuk viewer cepat
    extract_preview_jpg(ds, os.path.join(PUBLIC_ASSETS_DIR, "raw_toraks.jpg"))
    # Copy juga ke sample_toraks.dcm root agar pipeline lama tetap sinkron
    try:
        ds.save_as(os.path.join(BASE_DIR, "sample_toraks.dcm"), write_like_original=False)
    except Exception:
        pass

    # 3. Update atau Insert data ke Database MySQL/SQLite Laravel
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        
        # Cari apakah order pasien sudah ada sebelumnya (dari Accession / MRN)
        cursor.execute(
            "SELECT id FROM medical_scans WHERE accession_number = ? OR patient_id = ? ORDER BY id DESC LIMIT 1",
            (accession_no, patient_id)
        )
        row = cursor.fetchone()
        
        if row and accession_no:
            # Update data order yang ada menjadi selesai rontgen
            scan_id = row['id']
            cursor.execute("""
                UPDATE medical_scans 
                SET scan_image_path = ?,
                    preview_image_path = ?,
                    dicom_raw_path = ?,
                    mcu_status = 'rontgen_selesai',
                    station_name = ?,
                    study_instance_uid = ?,
                    sop_instance_uid = ?,
                    modality = ?,
                    updated_at = datetime('now')
                WHERE id = ?
            """, (rel_jpg_path, rel_jpg_path, rel_dcm_path, station_name, study_uid, sop_uid, modality, scan_id))
            print(f"[HYU PACS] [OK] Sukses mengupdate Order #{scan_id} ({patient_name} - {patient_id}) -> Status: Citra Masuk")
        else:
            # Buat record baru otomatis jika rontgen dilakukan langsung di alat
            cursor.execute("""
                INSERT INTO medical_scans (
                    patient_id, accession_number, patient_name, modality, study_description,
                    scan_image_path, preview_image_path, dicom_raw_path, mcu_status,
                    station_name, study_instance_uid, sop_instance_uid, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'rontgen_selesai', ?, ?, ?, datetime('now'), datetime('now'))
            """, (
                patient_id, accession_no or f"ACC-{now.strftime('%Y%m%d%H%M')}", patient_name,
                modality, study_desc, rel_jpg_path, rel_jpg_path, rel_dcm_path,
                station_name, study_uid, sop_uid
            ))
            print(f"[HYU PACS] [OK] Sukses menerima Citra DICOM Baru ({patient_name} - {patient_id}) dari {station_name}")
            
        conn.commit()
        conn.close()
    except Exception as db_err:
        print(f"[HYU PACS DB ERROR] {db_err}")

    # Kembalikan status Success ke Mesin Fujifilm (0x0000 = Success)
    return 0x0000


def handle_echo(event):
    """Menanggapi tes koneksi DICOM C-ECHO (Ping) dari alat Fujifilm"""
    requestor_ae = event.assoc.requestor.ae_title
    print(f"[HYU PACS] [PING] DICOM Ping (C-ECHO) diterima dari: {requestor_ae} ({event.assoc.requestor.address}) -> Response: SUCCESS")
    return 0x0000


def handle_find(event):
    """
    Menanggapi kueri Modality Worklist (C-FIND) dari konsol Fujifilm.
    Mengirimkan daftar antrian pasien yang siap diperiksa di ruang rontgen.
    """
    model = event.model
    identifier = event.identifier
    requestor_ae = event.assoc.requestor.ae_title
    print(f"[HYU PACS] [WORKLIST] Worklist Query (C-FIND) diterima dari: {requestor_ae}")

    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM medical_scans WHERE mcu_status = 'siap_rontgen' ORDER BY id ASC")
        patients = cursor.fetchall()
        conn.close()

        for p in patients:
            # Buat dataset respon DICOM Worklist
            res = Dataset()
            res.PatientName = p['patient_name'] or "Pasien MCU"
            res.PatientID = p['patient_id'] or f"CDC-{p['id']:04d}"
            res.AccessionNumber = p['accession_number'] or f"ACC-{p['id']:04d}"
            res.Modality = p['modality'] or "DX"
            res.RequestedProcedureDescription = p['study_description'] or "Thorax PA"
            res.ScheduledStationAETitle = requestor_ae
            res.ScheduledProcedureStepStatus = "SCHEDULED"

            yield 0xFF00, res

    except Exception as e:
        print(f"[HYU PACS WORKLIST ERROR] {e}")

    # Kirim status selesai (0x0000 = Success)
    yield 0x0000, None


def start_server(host="0.0.0.0", port=4242, ae_title="HYU_PACS"):
    """Menjalankan daemon DICOM Application Entity (AE)"""
    ae = AE(ae_title=ae_title)

    # Tambahkan semua presentation context storage (CR, DX, CT, MR, US, Secondary Capture)
    ae.supported_contexts = AllStoragePresentationContexts
    ae.add_supported_context(Verification)
    ae.add_supported_context(ModalityWorklistInformationFind)

    handlers = [
        (evt.EVT_C_STORE, handle_store),
        (evt.EVT_C_ECHO, handle_echo),
        (evt.EVT_C_FIND, handle_find),
    ]

    print("=" * 70)
    print(" [HYU] MEDICAL IMAGING & STANDALONE PACS SERVER")
    print(f" * Server AE Title : {ae_title}")
    print(f" * IP Address      : {host}")
    print(f" * Port Listener   : {port}")
    print(f" * Modalitas Target: Fujifilm FDR/FDL, CR, DX, Ultrasound")
    print(f" * Status Database : Terhubung ke {DB_PATH}")
    print("=" * 70)
    print("[HYU PACS] Menunggu koneksi pengiriman citra dari alat medis...")

    ae.start_server((host, port), evt_handlers=handlers, block=True)


if __name__ == "__main__":
    env_port = int(os.environ.get("DICOM_PORT", "4242"))
    env_host = os.environ.get("DICOM_HOST", "0.0.0.0")
    env_ae = os.environ.get("DICOM_AE_TITLE", "HYU_PACS")

    parser = argparse.ArgumentParser(description="Hyu Standalone DICOM PACS Server & Modality Worklist Engine")
    parser.add_argument("--host", default=env_host, help=f"IP address listener (default: {env_host})")
    parser.add_argument("--port", type=int, default=env_port, help=f"Port DICOM listener, misal: 104, 4242, 11112, 8042 (default: {env_port})")
    parser.add_argument("--ae", default=env_ae, help=f"Application Entity Title (default: {env_ae})")
    
    args = parser.parse_args()
    start_server(host=args.host, port=args.port, ae_title=args.ae)

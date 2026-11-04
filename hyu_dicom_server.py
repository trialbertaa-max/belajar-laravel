#!/usr/bin/env python3
"""
Hyu Standalone DICOM PACS Server & Modality Worklist Engine
Menerima citra rontgen langsung dari mesin Fujifilm FDR/FDL via DICOM C-STORE
Menanggapi tes koneksi C-ECHO dan kueri antrian pasien Modality Worklist (C-FIND).
"""

import sys
import os
import re
import argparse
import datetime
import sqlite3
import numpy as np
import pydicom
from pydicom.dataset import Dataset
from pydicom.uid import ExplicitVRLittleEndian, ImplicitVRLittleEndian, DeflatedExplicitVRLittleEndian, generate_uid

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
    PatientRootQueryRetrieveInformationModelFind,
    StudyRootQueryRetrieveInformationModelFind,
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


def normalize_modality(raw_str):
    """
    Menormalkan string modalitas dari database/UI menjadi kode standar DICOM:
    - USG / Ultrasound / Echo -> 'US' (Mindray USG DP-10 / Consona / Resona)
    - Thorax / Rontgen / Dada / Kepala -> 'DX' atau 'CR' (Fujifilm FDR / DROC)
    - CT Scan -> 'CT'
    - MRI -> 'MR'
    - Mammography -> 'MG'
    """
    if not raw_str:
        return 'DX'
    s = str(raw_str).strip().upper()
    if 'USG' in s or 'ULTRASOUND' in s or 'ECHO' in s or s == 'US':
        return 'US'
    if 'CT' in s or 'TOMOGRAPHY' in s:
        return 'CT'
    if 'MR' in s or 'MAGNETIC' in s:
        return 'MR'
    if 'MG' in s or 'MAMMO' in s:
        return 'MG'
    if 'XA' in s or 'ANGIO' in s:
        return 'XA'
    if 'CR' in s:
        return 'CR'
    if s in ['CR', 'DX', 'CT', 'MR', 'US', 'XA', 'MG', 'RG']:
        return s
    return 'DX'


def extract_preview_jpg(ds, output_jpg_path):
    """Mengekstrak pixel array dari DICOM dataset menjadi gambar JPG 8-bit berkualitas tinggi (Mendukung USG Mindray Grayscale, Multi-frame cine loop, dan Color Doppler)"""
    try:
        pixel_array = ds.pixel_array
        
        # Handle multi-frame (USG video/cine loop atau multi-slice)
        num_frames = int(getattr(ds, 'NumberOfFrames', 1) or 1)
        if num_frames > 1 and pixel_array.ndim >= 3:
            pixel_array = pixel_array[0]
        elif pixel_array.ndim == 3 and pixel_array.shape[0] > 10 and pixel_array.shape[2] != 3:
            pixel_array = pixel_array[0]

        # Handle color ultrasound images (RGB or YBR)
        if pixel_array.ndim == 3 and pixel_array.shape[2] == 3:
            photo_interp = str(ds.get("PhotometricInterpretation", "RGB")).strip().upper()
            if "YBR" in photo_interp:
                img_bgr = cv2.cvtColor(pixel_array.astype(np.uint8), cv2.COLOR_YUV2BGR)
            else:
                img_bgr = cv2.cvtColor(pixel_array.astype(np.uint8), cv2.COLOR_RGB2BGR)
            os.makedirs(os.path.dirname(output_jpg_path), exist_ok=True)
            cv2.imwrite(output_jpg_path, img_bgr)
            return True

        # Handle 2D Grayscale (seperti Mindray DP-10 B-Mode)
        px_float = pixel_array.astype(float)
        if px_float.max() > px_float.min():
            norm_img = ((px_float - px_float.min()) / (px_float.max() - px_float.min())) * 255.0
        else:
            norm_img = np.zeros_like(px_float)
            
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
    other_patient_id = str(ds.get("OtherPatientIDs", "")).strip()
    patient_name = str(ds.get("PatientName", "UNKNOWN")).replace("^", " ").strip()
    accession_no = str(ds.get("AccessionNumber", "")).strip()
    modality = str(ds.get("Modality", "DX")).strip()
    study_desc = str(ds.get("StudyDescription", "Thorax PA View")).strip()
    sop_uid = str(ds.get("SOPInstanceUID", "")).strip()
    study_uid = str(ds.get("StudyInstanceUID", "")).strip()
    station_name = str(ds.get("StationName", event.assoc.requestor.ae_title or "FUJIFILM_FDR")).strip()

    now = datetime.datetime.now()
    timestamp_str = now.strftime("%Y%m%d_%H%M%S")
    
    # Buat nama file penyimpanan terstruktur (Gunakan OtherPatientIDs jika PatientID adalah auto ID alat)
    subfolder = now.strftime("%Y/%m")
    save_dir = os.path.join(STORAGE_SCANS_DIR, subfolder)
    os.makedirs(save_dir, exist_ok=True)

    resolved_id = other_patient_id if (other_patient_id and other_patient_id != "UNKNOWN") else patient_id
    safe_patient_id = re.sub(r'[^A-Za-z0-9_\-]', '', resolved_id) or "UNKNOWN"
    safe_accession_no = re.sub(r'[^A-Za-z0-9_\-]', '', accession_no) or timestamp_str
    
    filename_clean = f"{safe_patient_id}_{safe_accession_no}.dcm"
    dcm_full_path = os.path.join(save_dir, filename_clean)
    rel_dcm_path = f"scans/{subfolder}/{filename_clean}"
    
    # Validasi direktori target agar tidak bisa keluar dari STORAGE_SCANS_DIR
    if not os.path.abspath(dcm_full_path).startswith(os.path.abspath(STORAGE_SCANS_DIR)):
        print(f"[HYU PACS WARNING] Percobaan path traversal terdeteksi pada PatientID: {patient_id}")
        return 0xC000

    # 1. Simpan file DICOM asli
    ds.save_as(dcm_full_path, write_like_original=False)
    
    # 2. Ekstrak preview JPG untuk Web Viewer
    jpg_filename = f"scan_{safe_patient_id}_{timestamp_str}.jpg"
    jpg_full_path = os.path.join(PUBLIC_ASSETS_DIR, jpg_filename)
    rel_jpg_path = f"scan-assets/{jpg_filename}"

    if not os.path.abspath(jpg_full_path).startswith(os.path.abspath(PUBLIC_ASSETS_DIR)):
        print(f"[HYU PACS WARNING] Percobaan path traversal terdeteksi pada file preview JPG: {jpg_filename}")
        return 0xC000
    
    extract_preview_jpg(ds, jpg_full_path)
    
    # Update file raw_toraks & sample_toraks default jika Thorax, atau usg_sample jika USG
    clean_modality = normalize_modality(modality)
    body_part = str(ds.get("BodyPartExamined", "")).upper()
    study_desc_upper = study_desc.upper()
    is_chest_scan = "THORAX" in body_part or "CHEST" in study_desc_upper or "TORAKS" in study_desc_upper or "PARU" in study_desc_upper
    is_usg_scan = (clean_modality == 'US') or ("USG" in study_desc_upper) or ("ULTRASOUND" in study_desc_upper)

    if is_chest_scan:
        extract_preview_jpg(ds, os.path.join(PUBLIC_ASSETS_DIR, "raw_toraks.jpg"))
        try:
            ds.save_as(os.path.join(BASE_DIR, "sample_toraks.dcm"), write_like_original=False)
        except Exception:
            pass
    elif is_usg_scan:
        extract_preview_jpg(ds, os.path.join(PUBLIC_ASSETS_DIR, "usg_sample.jpg"))
        try:
            ds.save_as(os.path.join(BASE_DIR, "sample_usg.dcm"), write_like_original=False)
        except Exception:
            pass

    # 3. Update atau Insert data ke Database MySQL/SQLite Laravel
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        
        row = None
        
        # 1. Cek berdasarkan Accession Number jika dikirim
        if accession_no:
            cursor.execute(
                "SELECT id, patient_id, patient_name, accession_number FROM medical_scans WHERE accession_number = ? ORDER BY id DESC LIMIT 1",
                (accession_no,)
            )
            row = cursor.fetchone()

        # 2. Cek berdasarkan OtherPatientIDs (Khas Mindray DP-10: Worklist MRN disimpan di tag 0010,1000)
        if not row and other_patient_id:
            cursor.execute(
                "SELECT id, patient_id, patient_name, accession_number FROM medical_scans WHERE patient_id = ? OR accession_number = ? ORDER BY id DESC LIMIT 1",
                (other_patient_id, other_patient_id)
            )
            row = cursor.fetchone()

        # 3. Cek berdasarkan PatientID utama
        if not row and patient_id and patient_id != "UNKNOWN":
            cursor.execute(
                "SELECT id, patient_id, patient_name, accession_number FROM medical_scans WHERE patient_id = ? ORDER BY id DESC LIMIT 1",
                (patient_id,)
            )
            row = cursor.fetchone()

        # 4. Cek berdasarkan StudyInstanceUID (Jika multi-frame / citra lanjutan dari pasien yang sama)
        if not row and study_uid:
            cursor.execute(
                "SELECT id, patient_id, patient_name, accession_number FROM medical_scans WHERE study_instance_uid = ? ORDER BY id DESC LIMIT 1",
                (study_uid,)
            )
            row = cursor.fetchone()

        # 5. Smart Fallback untuk USG Mindray:
        # Jika PatientID adalah auto-generated ID (contoh: 20261104-145042-C969) dan ada nama pasien cocok di antrian
        if not row and clean_modality == 'US' and patient_name and patient_name not in ["", "UNKNOWN"]:
            clean_name = patient_name.replace("^", " ").strip()
            cursor.execute(
                "SELECT id, patient_id, patient_name, accession_number FROM medical_scans WHERE LOWER(patient_name) LIKE ? AND mcu_status IN ('siap_rontgen', 'siap_usg', 'siap_pemeriksaan') ORDER BY id DESC LIMIT 1",
                (f"%{clean_name.lower()}%",)
            )
            row = cursor.fetchone()

        if row:
            # Update data order yang ada menjadi selesai pemeriksaan
            scan_id = row['id']
            actual_patient_id = row['patient_id'] or resolved_id
            actual_patient_name = row['patient_name'] or (patient_name if patient_name != "UNKNOWN" else "Pasien USG")
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
            """, (rel_jpg_path, rel_jpg_path, rel_dcm_path, station_name, study_uid, sop_uid, clean_modality, scan_id))
            print(f"[HYU PACS] [OK] Sukses menautkan citra USG Mindray ke Order Antrian #{scan_id} ({actual_patient_name} - {actual_patient_id}) -> Status: Selesai Pemeriksaan")
        else:
            # Buat record baru otomatis jika pemeriksaan dilakukan langsung tanpa order worklist
            assigned_id = resolved_id or f"CDC-{now.strftime('%Y%m%d%H%M')}"
            assigned_acc = accession_no or f"ACC-{now.strftime('%Y%m%d%H%M')}"
            assigned_name = patient_name if (patient_name and patient_name != "UNKNOWN") else f"Pasien USG Mindray ({assigned_id})"
            cursor.execute("""
                INSERT INTO medical_scans (
                    patient_id, accession_number, patient_name, modality, study_description,
                    scan_image_path, preview_image_path, dicom_raw_path, mcu_status,
                    station_name, study_instance_uid, sop_instance_uid, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'rontgen_selesai', ?, ?, ?, datetime('now'), datetime('now'))
            """, (
                assigned_id, assigned_acc, assigned_name,
                clean_modality, study_desc, rel_jpg_path, rel_jpg_path, rel_dcm_path,
                station_name, study_uid, sop_uid
            ))
            print(f"[HYU PACS] [OK] Sukses menerima Citra DICOM Baru ({assigned_name} - {assigned_id}) dari {station_name}")
            
        conn.commit()
        conn.close()
    except Exception as db_err:
        print(f"[HYU PACS DB ERROR] {db_err}")

    # Kembalikan status Success ke Mesin (0x0000 = Success)
    return 0x0000


def handle_echo(event):
    """Menanggapi tes koneksi DICOM C-ECHO (Ping) dari alat medis (Mindray DP-10 / Fujifilm)"""
    requestor_ae = event.assoc.requestor.ae_title
    print(f"[HYU PACS] [PING] DICOM Ping (C-ECHO) diterima dari: {requestor_ae} ({event.assoc.requestor.address}) -> Response: SUCCESS")
    return 0x0000


def handle_find(event):
    """
    Menanggapi kueri Modality Worklist (C-FIND) dari konsol Modality (Mindray USG DP-10/DP-20, Fujifilm DROC/FDR, dll.)
    Mengirimkan daftar antrian pasien yang siap diperiksa.
    """
    try:
        requestor_ae = getattr(event.assoc.requestor, 'ae_title', 'UNKNOWN')
        requestor_ip = getattr(event.assoc.requestor, 'address', '0.0.0.0')
        print(f"\n[HYU PACS] [WORKLIST] >>> Kueri Worklist (C-FIND) diterima dari: {requestor_ae} ({requestor_ip})")

        conn = get_db_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM medical_scans WHERE mcu_status IN ('siap_rontgen', 'siap_usg', 'siap_pemeriksaan') ORDER BY id ASC")
        patients = cursor.fetchall()
        conn.close()

        count = 0
        now_dt = datetime.datetime.now()
        today_str = now_dt.strftime("%Y%m%d")
        time_str = now_dt.strftime("%H%M%S")

        # Parsing filter kueri dari SCU (Mindray USG DP-10, Fujifilm DROC, dll.)
        query_date = today_str
        query_modality = None
        query_patient_id = None
        query_accession = None
        query_patient_name = None

        if hasattr(event, 'identifier') and event.identifier:
            ident = event.identifier
            if 'PatientID' in ident and ident.PatientID:
                val = str(ident.PatientID).strip().replace('*', '')
                if val:
                    query_patient_id = val.upper()
            if 'AccessionNumber' in ident and ident.AccessionNumber:
                val = str(ident.AccessionNumber).strip().replace('*', '')
                if val:
                    query_accession = val.upper()
            if 'PatientName' in ident and ident.PatientName:
                val = str(ident.PatientName).strip().replace('*', '')
                if val:
                    query_patient_name = val.upper()
            if 'Modality' in ident and ident.Modality:
                query_modality = str(ident.Modality).strip().upper()

            # ScheduledProcedureStepSequence filtering (khas Mindray USG DP-10 & Fujifilm)
            if 'ScheduledProcedureStepSequence' in ident and len(ident.ScheduledProcedureStepSequence) > 0:
                sps_req = ident.ScheduledProcedureStepSequence[0]
                if hasattr(sps_req, 'ScheduledProcedureStepStartDate') and sps_req.ScheduledProcedureStepStartDate:
                    raw_qdate = str(sps_req.ScheduledProcedureStepStartDate).strip()
                    if '-' in raw_qdate:
                        parts = raw_qdate.split('-')
                        query_date = parts[0].strip() if parts[0].strip() else today_str
                    else:
                        query_date = raw_qdate
                    print(f"[HYU PACS] [WORKLIST] SCU memfilter tanggal: {query_date}")
                if hasattr(sps_req, 'Modality') and sps_req.Modality:
                    query_modality = str(sps_req.Modality).strip().upper()
                    print(f"[HYU PACS] [WORKLIST] SCU memfilter modalitas: {query_modality}")

        # Pastikan tanggal respon memenuhi format DICOM VR DA (8 digit numerik YYYYMMDD)
        resp_date = query_date if (len(query_date) == 8 and query_date.isdigit()) else today_str

        for p in patients:
            raw_mod = (p['modality'] or 'DX').strip()
            clean_modality = normalize_modality(raw_mod)

            # Filter Modalitas jika diminta oleh mesin (misal Mindray USG DP-10 hanya meminta Modality='US')
            if query_modality and query_modality not in ['*', '']:
                norm_qmod = normalize_modality(query_modality)
                if clean_modality != norm_qmod:
                    continue

            # Filter Nomor Rekam Medis (Patient ID)
            if query_patient_id and query_patient_id not in str(p['patient_id'] or '').upper():
                continue

            # Filter Accession Number
            if query_accession and query_accession not in str(p['accession_number'] or '').upper():
                continue

            # Filter Nama Pasien
            if query_patient_name and query_patient_name not in str(p['patient_name'] or '').upper():
                continue

            count += 1
            # Buat dataset respon DICOM Worklist standar NEMA PS 3.4 Annex K
            res = Dataset()
            res.SpecificCharacterSet = "ISO_IR 100"
            res.PatientName = p['patient_name'] or "Pasien MCU"
            res.PatientID = p['patient_id'] or f"CDC-{p['id']:04d}"
            
            # Format tanggal lahir & usia (Sangat penting untuk tampilan monitor Mindray DP-10)
            if p['birth_date']:
                res.PatientBirthDate = str(p['birth_date']).replace('-', '')
            elif p['age']:
                try:
                    res.PatientBirthDate = f"{now_dt.year - int(p['age'])}0101"
                except Exception:
                    res.PatientBirthDate = ""
            else:
                res.PatientBirthDate = ""

            if p['age']:
                try:
                    res.PatientAge = f"{int(p['age']):03d}Y"
                except Exception:
                    pass

            gender = (p['gender'] or 'O').upper()[:1]
            res.PatientSex = gender if gender in ['M', 'F'] else 'O'

            procedure_desc = p['study_description'] or p['modality'] or ("USG Abdomen" if clean_modality == 'US' else "Thorax PA")

            res.AccessionNumber = p['accession_number'] or f"ACC-{p['id']:04d}"
            res.StudyInstanceUID = p['study_instance_uid'] or generate_uid()
            res.RequestedProcedureID = f"RP-{p['id']:04d}"
            res.RequestedProcedureDescription = procedure_desc
            res.Modality = clean_modality
            res.InstitutionName = "KLINIK HYU RADIOLOGI & USG"

            # Scheduled Procedure Step Sequence (Wajib untuk konsol Mindray DP-10 & DROC / Fuji)
            sps_item = Dataset()
            sps_item.ScheduledStationAETitle = requestor_ae
            sps_item.ScheduledProcedureStepStartDate = resp_date
            sps_item.ScheduledProcedureStepStartTime = time_str
            sps_item.Modality = clean_modality
            sps_item.ScheduledPerformingPhysicianName = p['doctor_name'] or ("dr. Sp.Rad / Sp.OG" if clean_modality == 'US' else "Dokter Radiologi")
            sps_item.ScheduledProcedureStepDescription = procedure_desc
            sps_item.ScheduledProcedureStepID = f"SPS-{p['id']:04d}"
            
            default_station = "USG_MINDRAY_DP10" if clean_modality == 'US' else "FUJIFILM_FDR"
            sps_item.ScheduledStationName = p['station_name'] or default_station
            sps_item.ScheduledProcedureStepStatus = "SCHEDULED"

            res.ScheduledProcedureStepSequence = [sps_item]

            print(f"[HYU PACS] [WORKLIST] -> Kirim antrian #{p['id']}: {res.PatientName} (ID: {res.PatientID}, Modality: {clean_modality}, Ket: {procedure_desc})")
            yield 0xFF00, res

        print(f"[HYU PACS] [WORKLIST] Selesai mengirim total {count} pasien ke {requestor_ae}.\n")

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
    ae.add_supported_context(PatientRootQueryRetrieveInformationModelFind)
    ae.add_supported_context(StudyRootQueryRetrieveInformationModelFind)

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
    print(f" * Modalitas Target: DROC, Fujifilm FDR/FDL, CR, DX, Ultrasound")
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

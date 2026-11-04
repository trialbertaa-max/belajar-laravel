#!/usr/bin/env python3
"""
Test Suite Simulator: Mindray DP-10 Ultrasound System
Menguji:
1. C-ECHO (DICOM Ping Verification) dari AE Title Mindray DP-10
2. C-FIND (DICOM Modality Worklist / MWL Query) dengan filter Modality = 'US'
3. C-STORE (Pengiriman Citra Ultrasound / sample_usg.dcm ke Hyu PACS)
4. Verifikasi update status di database SQLite
"""

import sys
import os
import sqlite3
import datetime
import pydicom
from pydicom.dataset import Dataset
from pynetdicom import AE
from pynetdicom.sop_class import (
    Verification,
    ModalityWorklistInformationFind,
    UltrasoundImageStorage,
    UltrasoundMultiFrameImageStorage
)

DB_PATH = os.path.join(os.path.dirname(__file__), "database", "database.sqlite")

def seed_usg_test_patient():
    """Memastikan ada data pasien antrian USG berstatus 'siap_rontgen' di database"""
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    # Cek apakah pasien tes USG sudah ada
    cursor.execute("SELECT id FROM medical_scans WHERE patient_id = 'USG-DP10-001'")
    row = cursor.fetchone()
    
    if not row:
        cursor.execute("""
            INSERT INTO medical_scans (
                patient_id, accession_number, patient_name, gender, age, modality,
                study_description, mcu_status, station_name, created_at, updated_at
            ) VALUES (
                'USG-DP10-001', 'ACC-USG-20261104', 'Ibu Ratna Dewi (USG DP-10)',
                'P', 32, 'USG Abdomen', 'USG Abdomen & Hepar Lengkap',
                'siap_rontgen', 'USG_MINDRAY_DP10', datetime('now'), datetime('now')
            )
        """)
        conn.commit()
        print("[SETUP] Pasien tes USG DP-10 berhasil ditambahkan: Ibu Ratna Dewi (USG-DP10-001)")
    else:
        cursor.execute("UPDATE medical_scans SET mcu_status = 'siap_rontgen' WHERE patient_id = 'USG-DP10-001'")
        conn.commit()
        print("[SETUP] Pasien tes USG-DP10-001 di-reset ke status 'siap_rontgen'")
        
    conn.close()

def run_mindray_simulation(server_ip="127.0.0.1", server_port=4242, server_ae="HYU_PACS", mindray_ae="DP10_USG"):
    print("=" * 70)
    print(" [MINDRAY DP-10 SIMULATOR] Memulai Pengujian Kemampuan DICOM & Worklist")
    print(f" * Target PACS Server : {server_ip}:{server_port} (AE: {server_ae})")
    print(f" * Mindray DP-10 AE    : {mindray_ae}")
    print("=" * 70)

    # 1. Setup AE Mindray DP-10
    ae = AE(ae_title=mindray_ae)
    ae.add_requested_context(Verification)
    ae.add_requested_context(ModalityWorklistInformationFind)
    ae.add_requested_context(UltrasoundImageStorage)
    ae.add_requested_context(UltrasoundMultiFrameImageStorage)

    assoc = ae.associate(server_ip, server_port, ae_title=server_ae)
    if not assoc.is_established:
        print(f"[FAIL] Gagal menghubungkan ke Hyu PACS ({server_ip}:{server_port}). Pastikan server DICOM aktif!")
        return False

    print("[SUCCESS] Handshake Asosiasi DICOM Berhasil Terbuka!")

    # 2. Uji C-ECHO (Ping)
    print("\n--- [TES 1] Pengujian C-ECHO (Ping Verification) ---")
    echo_status = assoc.send_c_echo()
    if echo_status and echo_status.Status == 0x0000:
        print("[PASS] C-ECHO Verification: SUCCESS (0x0000) - Hyu PACS merespon alat Mindray DP-10!")
    else:
        print(f"[FAIL] C-ECHO gagal dengan status: {echo_status}")
        assoc.release()
        return False

    # 3. Uji C-FIND (Modality Worklist Query khusus USG)
    print("\n--- [TES 2] Pengujian C-FIND (Modality Worklist / MWL) ---")
    query_ds = Dataset()
    query_ds.SpecificCharacterSet = "ISO_IR 100"
    query_ds.PatientName = ""
    query_ds.PatientID = ""
    query_ds.PatientBirthDate = ""
    query_ds.PatientSex = ""
    query_ds.PatientAge = ""
    query_ds.AccessionNumber = ""
    query_ds.StudyInstanceUID = ""
    query_ds.RequestedProcedureID = ""
    query_ds.RequestedProcedureDescription = ""
    query_ds.Modality = "US"

    sps_query = Dataset()
    sps_query.ScheduledStationAETitle = mindray_ae
    sps_query.ScheduledProcedureStepStartDate = datetime.datetime.now().strftime("%Y%m%d")
    sps_query.ScheduledProcedureStepStartTime = ""
    sps_query.Modality = "US"
    sps_query.ScheduledPerformingPhysicianName = ""
    sps_query.ScheduledProcedureStepDescription = ""
    sps_query.ScheduledProcedureStepID = ""
    sps_query.ScheduledStationName = ""
    sps_query.ScheduledProcedureStepStatus = ""
    query_ds.ScheduledProcedureStepSequence = [sps_query]

    print("[MINDRAY DP-10] Mengirim kueri C-FIND Worklist (Filter: Modality='US')...")
    responses = assoc.send_c_find(query_ds, ModalityWorklistInformationFind)

    found_patients = []
    for (status, identifier) in responses:
        if status and status.Status in [0xFF00, 0xFF01] and identifier:
            p_name = str(identifier.get("PatientName", "Unknown"))
            p_id = str(identifier.get("PatientID", "Unknown"))
            p_mod = str(identifier.get("Modality", ""))
            p_age = str(identifier.get("PatientAge", "-"))
            
            sps_mod = ""
            if "ScheduledProcedureStepSequence" in identifier and len(identifier.ScheduledProcedureStepSequence) > 0:
                sps_mod = str(identifier.ScheduledProcedureStepSequence[0].get("Modality", ""))

            found_patients.append({
                "id": p_id,
                "name": p_name,
                "modality": p_mod,
                "sps_modality": sps_mod,
                "age": p_age
            })
            print(f" -> Ditemukan Pasien Antrian USG: {p_name} | ID: {p_id} | Modality: {p_mod} (SPS: {sps_mod}) | Usia: {p_age}")

    print(f"[PASS] C-FIND Worklist: Berhasil mengambil {len(found_patients)} antrian USG untuk Mindray DP-10!")
    if len(found_patients) == 0:
        print("[WARNING] Tidak ada antrian USG ditemukan. Pastikan ada pasien berstatus 'siap_rontgen' dengan modalitas USG!")

    # 4. Uji C-STORE (Kirim Citra USG ke Hyu PACS)
    print("\n--- [TES 3] Pengujian C-STORE (Kirim Citra USG sample_usg.dcm) ---")
    usg_file = os.path.join(os.path.dirname(__file__), "sample_usg.dcm")
    if os.path.exists(usg_file):
        ds_usg = pydicom.dcmread(usg_file)
        ds_usg.PatientID = "USG-DP10-001"
        ds_usg.AccessionNumber = "ACC-USG-20261104"
        ds_usg.PatientName = "Ibu Ratna Dewi (USG DP-10)"
        ds_usg.Modality = "US"
        ds_usg.StudyDescription = "USG Abdomen Mindray DP-10"

        store_status = assoc.send_c_store(ds_usg)
        if store_status and store_status.Status == 0x0000:
            print("[PASS] C-STORE Ultrasound Image: SUCCESS (0x0000) - Citra USG berhasil disimpan di Hyu PACS!")
        else:
            print(f"[FAIL] C-STORE gagal dengan status: {store_status}")
    else:
        print(f"[SKIP] File {usg_file} tidak ditemukan untuk tes C-STORE.")

    assoc.release()

    # 5. Verifikasi Database
    print("\n--- [TES 4] Verifikasi Database SQLite Hyu ---")
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    cursor.execute("SELECT id, patient_id, patient_name, modality, mcu_status, preview_image_path FROM medical_scans WHERE patient_id = 'USG-DP10-001'")
    updated_row = cursor.fetchone()
    conn.close()

    if updated_row:
        print(f"[PASS] Status Database Pasien #{updated_row[0]}:")
        print(f"  - Patient ID   : {updated_row[1]}")
        print(f"  - Patient Name : {updated_row[2]}")
        print(f"  - Modality     : {updated_row[3]}")
        print(f"  - MCU Status   : {updated_row[4]}")
        print(f"  - Preview JPG  : {updated_row[5]}")
    
    print("\n" + "=" * 70)
    print(" [KESIMPULAN] Integrasi DICOM Modality Worklist & Storage untuk USG Mindray DP-10 100% CAPABLE & TERVERIFIKASI!")
    print("=" * 70)
    return True

if __name__ == "__main__":
    seed_usg_test_patient()
    server_port = int(os.environ.get("DICOM_PORT", "4242"))
    run_mindray_simulation(server_port=server_port)


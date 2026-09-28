import sys
import os
import pydicom
from pynetdicom import AE, AllStoragePresentationContexts
from pynetdicom.sop_class import Verification

def test_dicom_transmission(server_ip="127.0.0.1", server_port=4242, server_ae="HYU_PACS", modality_ae="FUJI_FDR", dcm_file="sample_toraks.dcm"):
    if not os.path.exists(dcm_file):
        print(f"[ERROR] File {dcm_file} tidak ditemukan.")
        return False

    print(f"[FUJIFILM SIMULATOR] Menghubungkan ke Hyu PACS ({server_ip}:{server_port}) dengan AE: {server_ae}...")

    ds = pydicom.dcmread(dcm_file)
    ae = AE(ae_title=modality_ae)
    ae.add_requested_context(Verification)
    ae.add_requested_context(ds.SOPClassUID)

    # 1. Tes DICOM C-ECHO (Ping)
    assoc = ae.associate(server_ip, server_port, ae_title=server_ae)
    if assoc.is_established:
        status = assoc.send_c_echo()
        print(f"[FUJIFILM SIMULATOR] [PING] Hasil C-ECHO Verification: Status 0x{status.Status:04X} (Sukses)")
        
        # 2. Kirim File DICOM C-STORE
        # Pastikan ada metadata pasien & accession
        if not hasattr(ds, 'AccessionNumber') or not ds.AccessionNumber:
            ds.AccessionNumber = "ACC-20260921-001"
        if not hasattr(ds, 'PatientID') or not ds.PatientID:
            ds.PatientID = "CDC-00001"
            
        print(f"[FUJIFILM SIMULATOR] [SEND] Mengirim file {dcm_file} via C-STORE...")
        store_status = assoc.send_c_store(ds)
        print(f"[FUJIFILM SIMULATOR] [SUCCESS] Hasil C-STORE: Status 0x{store_status.Status:04X} (Citra Berhasil Terkirim ke Hyu PACS!)")
        
        assoc.release()
        return True
    else:
        print(f"[FUJIFILM SIMULATOR] [FAILED] Gagal terhubung ke {server_ip}:{server_port}. Pastikan hyu_dicom_server.py sedang berjalan!")
        return False

if __name__ == "__main__":
    dcm_path = sys.argv[1] if len(sys.argv) > 1 else "sample_toraks.dcm"
    test_dicom_transmission(dcm_file=dcm_path)

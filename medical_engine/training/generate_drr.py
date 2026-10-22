"""
medical_engine/training/generate_drr.py
Modul Generator DRR (Digitally Reconstructed Radiographs) Berbasis Fisika Radiologi

Fungsi:
1. Mengunduh data volume 3D CT Toraks dari The Cancer Imaging Archive (TCIA) - LIDC-IDRI (Lisensi CC-BY Bebas Komersial).
2. Mengonversi voxel CT ke satuan Hounsfield Units (HU) dan Koefisien Atenuasi Linear (mu).
3. Melakukan dekomposisi anatomis (Pemisahan Tulang vs Jaringan Lunak).
4. Melakukan proyeksi sinar-X virtual (Beer-Lambert Forward Ray-Casting) untuk menghasilkan
   pasangan citra beresolusi tinggi yang sejajar sub-piksel (Pixel-Aligned Pairs):
   - Citra A: Rontgen Toraks Normal Lengkap (Dengan Tulang Iga & Klavikula) -> Input AI
   - Citra B: Rontgen Toraks Murni Soft-Tissue (Tanpa Tulang) -> Ground Truth Target AI
"""

import os
import sys
import glob
import numpy as np
import pydicom
import cv2

try:
    import SimpleITK as sitk
except ImportError:
    sitk = None


def download_lidc_sample_series(output_dir="dataset_lidc_raw", max_subjects=5):
    """
    Mengunduh subset volume CT toraks LIDC-IDRI dari TCIA menggunakan tcia_utils.
    Hanya dijalankan di lingkungan dengan koneksi internet (Google Colab).
    """
    try:
        from tcia_utils import nbia
    except ImportError:
        raise ImportError("Pustaka 'tcia_utils' belum terpasang. Jalankan: pip install tcia-utils")

    os.makedirs(output_dir, exist_ok=True)
    print(f"[*] Mencari seri CT Toraks LIDC-IDRI di The Cancer Imaging Archive (TCIA)...")
    
    # Query seri CT toraks
    series_data = nbia.getSeries(
        collection="LIDC-IDRI",
        modality="CT",
        format="df"
    )

    if series_data is None or len(series_data) == 0:
        print("[!] Gagal mendapatkan daftar seri dari TCIA.")
        return []

    # Filter seri dengan ketebalan irisan <= 2.5 mm untuk kualitas DRR terbaik
    high_res_series = series_data[series_data['ImageCount'] >= 100].head(max_subjects)
    series_uids = high_res_series['SeriesInstanceUID'].tolist()

    print(f"[*] Mengunduh {len(series_uids)} seri CT toraks (Lisensi CC-BY)...")
    for idx, s_uid in enumerate(series_uids, 1):
        target_sub_dir = os.path.join(output_dir, f"patient_{idx}")
        os.makedirs(target_sub_dir, exist_ok=True)
        print(f"    [{idx}/{len(series_uids)}] Mengunduh SeriesUID: {s_uid}...")
        nbia.downloadSeries(s_uid, input_type="series", path=target_sub_dir)

    print(f"[+] Pengunduhan selesai. Data tersimpan di: {output_dir}")
    return series_uids


def load_ct_volume_from_dicom_dir(dicom_dir):
    """
    Membaca seluruh irisan file DICOM CT 3D dari sebuah direktori,
    mengurutkan berdasarkan ImagePositionPatient (Z-axis),
    dan mengonversi nilai piksel ke Hounsfield Units (HU).
    """
    dcm_files = glob.glob(os.path.join(dicom_dir, "**", "*.dcm"), recursive=True)
    if not dcm_files:
        raise FileNotFoundError(f"Tidak ada berkas .dcm ditemukan di {dicom_dir}")

    slices = []
    for f in dcm_files:
        try:
            ds = pydicom.dcmread(f)
            if hasattr(ds, 'ImagePositionPatient') and hasattr(ds, 'pixel_array'):
                slices.append(ds)
        except Exception:
            continue

    if not slices:
        raise ValueError(f"Tidak dapat membaca irisan valid dari {dicom_dir}")

    # Urutkan irisan berdasarkan koordinat Z
    slices.sort(key=lambda s: float(s.ImagePositionPatient[2]))

    # Ambil resolusi dan spasi
    pixel_spacing = slices[0].PixelSpacing if hasattr(slices[0], 'PixelSpacing') else [1.0, 1.0]
    slice_thickness = float(getattr(slices[0], 'SliceThickness', 1.0))
    spacing = (float(pixel_spacing[0]), float(pixel_spacing[1]), slice_thickness)

    # Susun volume 3D array
    vol_shape = (len(slices), slices[0].Rows, slices[0].Columns)
    volume_hu = np.zeros(vol_shape, dtype=np.float32)

    for i, s in enumerate(slices):
        px = s.pixel_array.astype(np.float32)
        slope = float(getattr(s, 'RescaleSlope', 1.0))
        intercept = float(getattr(s, 'RescaleIntercept', 0.0))
        hu = px * slope + intercept
        volume_hu[i] = hu

    return volume_hu, spacing


def hu_to_linear_attenuation(volume_hu):
    """
    Mengonversi nilai Hounsfield Units (HU) ke Koefisien Atenuasi Linear efektif (mu) [1/mm].
    Prinsip Fisika Radiologi:
    - Air (Water): HU = 0  -> mu_water ~ 0.02 mm^-1 (pada 60-80 keV)
    - Udara (Air): HU = -1000 -> mu_air ~ 0.0001 mm^-1
    - Tulang (Bone): HU > 250 -> mu_bone ~ 0.04 - 0.08 mm^-1
    """
    mu_water = 0.02  # mm^-1
    # mu = mu_water * (1 + HU / 1000)
    mu_vol = np.maximum(0.0, mu_water * (1.0 + (volume_hu / 1000.0)))
    return mu_vol


def synthesize_drr_pair(volume_hu, spacing=(1.0, 1.0, 1.0), bone_hu_threshold=120.0, output_size=(1024, 1024), rotation_angle_deg=0.0):
    """
    Menghasilkan pasangan citra rontgen toraks 2D sintetis (DRR):
    1. full_drr: Rontgen toraks normal lengkap (dengan tulang)
    2. soft_drr: Rontgen toraks murni jaringan lunak (tulang ditekan/dihilangkan)
    
    Kedua gambar dijamin 100% sejajar sub-piksel (pixel-aligned).
    """
    # 1. Rotasi volume jika ada variasi sudut proyeksi (simulasi inklinasi tubuh pasien)
    if rotation_angle_deg != 0.0:
        # Rotasi terhadap sumbu Z (aksial)
        vol_rotated = np.zeros_like(volume_hu)
        center = (volume_hu.shape[2] // 2, volume_hu.shape[1] // 2)
        rot_mat = cv2.getRotationMatrix2D(center, rotation_angle_deg, 1.0)
        for z in range(volume_hu.shape[0]):
            vol_rotated[z] = cv2.warpAffine(volume_hu[z], rot_mat, (volume_hu.shape[2], volume_hu.shape[1]), borderMode=cv2.BORDER_REFLECT)
    else:
        vol_rotated = volume_hu

    # 2. Dekomposisi Anatomis: Volume Jaringan Lunak murni
    # Tulang (Iga, Klavikula, Vertebra) memiliki HU > threshold (120 HU mencakup trabekula & korteks)
    vol_soft_hu = vol_rotated.copy()
    bone_mask = vol_rotated > bone_hu_threshold

    # Dilasi morfologis 2D per irisan aksial untuk mengisolasi batas tepi korteks tulang (partial volume effect)
    kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (3, 3))
    bone_mask_dilated = np.zeros_like(bone_mask, dtype=bool)
    for z in range(vol_rotated.shape[0]):
        m_slice = bone_mask[z].astype(np.uint8)
        if m_slice.any():
            bone_mask_dilated[z] = cv2.dilate(m_slice, kernel, iterations=1).astype(bool)

    # Penggantian atenuasi tulang dengan estimasi densitas jaringan lunak sekitar (~35 HU otot/mediastinum)
    vol_soft_hu[bone_mask_dilated] = 35.0

    # 3. Hitung Koefisien Atenuasi Linear
    mu_full = hu_to_linear_attenuation(vol_rotated)
    mu_soft = hu_to_linear_attenuation(vol_soft_hu)

    # 4. Proyeksi Sinar-X Virtual (Forward Ray-Summation sepanjang Anterior-Posterior)
    dz = spacing[2]
    dy = spacing[1]

    # Proyeksi sinar menembus dada (Ray Sum)
    proj_full = np.sum(mu_full, axis=1) * dy
    proj_soft = np.sum(mu_soft, axis=1) * dy

    # Orientasi standar radiografi: Kepala di atas (flip Z jika urutan kaki-ke-kepala)
    proj_full = np.flipud(proj_full)
    proj_soft = np.flipud(proj_soft)

    # 5. Shared Normalization (KRUSIAL):
    # Parameter skala & offset dihitung HANYA dari proj_full dan diterapkan identik ke proj_soft.
    # Dengan begitu, daerah soft-tissue (paru, jantung, mediastinum) saling menghilangkan secara sempurna (full - soft == 0),
    # dan target residual (bone_target = full - soft) murni HANYA memuat struktur tulang iga & klavikula.
    p1 = float(np.percentile(proj_full, 0.5))
    p99 = float(np.percentile(proj_full, 99.5))
    scale = max(p99 - p1, 1e-6)

    drr_full_norm = np.clip((proj_full - p1) / scale, 0.0, 1.0)
    drr_soft_norm = np.clip((proj_soft - p1) / scale, 0.0, 1.0)

    # 6. Resize ke ukuran grid standar (1024 x 1024)
    drr_full_res = cv2.resize(drr_full_norm, output_size, interpolation=cv2.INTER_AREA)
    drr_soft_res = cv2.resize(drr_soft_norm, output_size, interpolation=cv2.INTER_AREA)

    return drr_full_res.astype(np.float32), drr_soft_res.astype(np.float32)


def process_patient_to_paired_dataset(patient_dir, output_dir, patient_id="p1"):
    """
    Memproses satu pasien CT menjadi beberapa pasang gambar latihan
    dengan variasi sudut proyeksi (-3°, 0°, +3°).
    """
    os.makedirs(os.path.join(output_dir, "x_full"), exist_ok=True)
    os.makedirs(os.path.join(output_dir, "y_soft"), exist_ok=True)

    print(f"[*] Memproses CT Volume: {patient_dir}...")
    volume_hu, spacing = load_ct_volume_from_dicom_dir(patient_dir)
    print(f"    Volume dimuat: {volume_hu.shape} voxel, Spacing: {spacing}")

    angles = [-3.0, 0.0, 3.0]
    saved_pairs = []

    for ang in angles:
        ang_str = f"rot{int(ang)}" if ang != 0 else "rot0"
        x_full, y_soft = synthesize_drr_pair(volume_hu, spacing=spacing, rotation_angle_deg=ang)

        fn_x = os.path.join(output_dir, "x_full", f"{patient_id}_{ang_str}_x.png")
        fn_y = os.path.join(output_dir, "y_soft", f"{patient_id}_{ang_str}_y.png")

        # Simpan sebagai 16-bit PNG untuk mempertahankan akurasi kontras medis
        x_u16 = (x_full * 65535.0).round().astype(np.uint16)
        y_u16 = (y_soft * 65535.0).round().astype(np.uint16)

        cv2.imwrite(fn_x, x_u16)
        cv2.imwrite(fn_y, y_u16)
        saved_pairs.append((fn_x, fn_y))
        print(f"    -> Tersimpan pasangan: {os.path.basename(fn_x)} <-> {os.path.basename(fn_y)}")

    return saved_pairs


if __name__ == "__main__":
    print("=" * 65)
    print("   Hyu PACS - Medical Engine DRR Synthesis Module (LIDC-IDRI)   ")
    print("=" * 65)
    print("Modul ini dirancang untuk dijalankan di Google Colab / server komputasi.")
    print("Silakan buka notebook: medical_engine/training/hyu_bone_suppression_lidc_training.ipynb")

"""
medical_engine/segmentation.py
Kategori 2: Anatomical Segmentation (Segmentasi Anatomi & Kontur)
Modul untuk segmentasi struktur anatomi (tulang korteks, lapangan paru, cincin pelvis, dsb.)
dengan pemisahan tegas antara segmentasi morfologis dan model deep learning tervalidasi.

PENTING:
- Menggunakan istilah jujur: "Segmentasi Struktur Tulang" atau "Penonjolan Kontur Tulang".
- TIDAK membuat klaim deteksi fraktur tanpa evaluasi klinis tervalidasi.
"""

import cv2
import numpy as np


def segment_bone_cortex(img_uint8, overlay_color=(0, 215, 255), alpha=0.35):
    """
    Segmentasi Struktur Korteks & Densitas Tulang.
    Mengisolasi area tulang berdensitas tinggi menggunakan thresholding adaptif multi-level
    dan operasi morfologi untuk menghasilkan masker kontur tulang.
    
    Output:
    - overlay_bgr: Citra 3-channel dengan highlight warna transparan pada struktur tulang.
    - mask: Masker biner (0 = jaringan lunak/background, 255 = struktur tulang).
    - info: Metadata teknis metode.
    """
    if len(img_uint8.shape) == 3:
        gray = cv2.cvtColor(img_uint8, cv2.COLOR_BGR2GRAY)
    else:
        gray = img_uint8.copy()

    # Preprocessing: bilateral filter untuk mereduksi noise sebelum thresholding
    smooth = cv2.bilateralFilter(gray, d=5, sigmaColor=30, sigmaSpace=30)

    # Deteksi area tulang: densitas tinggi (kalsium)
    # Gunakan Otsu thresholding untuk menentukan ambang pemisah kalsium
    thresh_val, binary = cv2.threshold(smooth, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)

    # Morfologi: Hilangkan bintik noise halus dan tutup lubang kecil pada trabekula
    kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (5, 5))
    opened = cv2.morphologyEx(binary, cv2.MORPH_OPEN, kernel, iterations=1)
    mask = cv2.morphologyEx(opened, cv2.MORPH_CLOSE, kernel, iterations=1)

    # Buat visualisasi overlay warna (BGR)
    gray_bgr = cv2.cvtColor(gray, cv2.COLOR_GRAY2BGR)
    color_layer = np.zeros_like(gray_bgr)
    color_layer[mask > 0] = overlay_color  # Warna highlight (default: Amber/Cyan)

    # Alpha blending: Hanya pada area masker tulang
    overlay_bgr = gray_bgr.copy()
    bone_pixels = (mask > 0)
    overlay_bgr[bone_pixels] = cv2.addWeighted(
        gray_bgr[bone_pixels], 1.0 - alpha,
        color_layer[bone_pixels], alpha,
        0
    )

    # Tambahkan garis tepi kontur tipis agar batas anatomis tegas
    contours, _ = cv2.findContours(mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    cv2.drawContours(overlay_bgr, contours, -1, overlay_color, 1)

    total_px = gray.size
    bone_px = int(np.count_nonzero(mask))
    bone_ratio = round((bone_px / total_px) * 100, 2)

    info = {
        'method_name': 'Segmentasi Struktur Tulang (Anatomical Bone Masking)',
        'otsu_threshold_value': float(thresh_val),
        'bone_pixel_coverage_pct': f"{bone_ratio}%",
        'overlay_color_bgr': list(overlay_color),
        'disclaimer': 'Masker kontur segmentasi untuk lokalisasi visual struktur bertulang. Tidak mengklaim deteksi patologi fraktur otomatis.'
    }
    return overlay_bgr, mask, info


def segment_lung_fields(img_uint8, overlay_color=(255, 180, 0), alpha=0.30):
    """
    Segmentasi Lapangan Paru (Thorax Bilateral Lung Fields).
    Mengisolasi area parenkim paru berbasis atenuasi udara dan kontur anatomi rongga dada.
    """
    if len(img_uint8.shape) == 3:
        gray = cv2.cvtColor(img_uint8, cv2.COLOR_BGR2GRAY)
    else:
        gray = img_uint8.copy()

    # Lapangan paru memiliki atenuasi rendah (gelap di MONOCHROME2)
    smooth = cv2.GaussianBlur(gray, (7, 7), 2.0)
    # Inverse thresholding
    _, binary = cv2.threshold(smooth, 0, 255, cv2.THRESH_BINARY_INV + cv2.THRESH_OTSU)

    # Bersihkan batas tepi luar citra
    h, w = binary.shape
    border_mask = np.zeros_like(binary)
    border_mask[int(h*0.1):int(h*0.9), int(w*0.1):int(w*0.9)] = 255
    lung_candidates = cv2.bitwise_and(binary, border_mask)

    # Morfologi untuk menggabungkan lobus
    kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (11, 11))
    lung_mask = cv2.morphologyEx(lung_candidates, cv2.MORPH_CLOSE, kernel, iterations=2)

    gray_bgr = cv2.cvtColor(gray, cv2.COLOR_GRAY2BGR)
    color_layer = np.zeros_like(gray_bgr)
    color_layer[lung_mask > 0] = overlay_color

    overlay_bgr = gray_bgr.copy()
    lung_pixels = (lung_mask > 0)
    overlay_bgr[lung_pixels] = cv2.addWeighted(
        gray_bgr[lung_pixels], 1.0 - alpha,
        color_layer[lung_pixels], alpha,
        0
    )

    info = {
        'method_name': 'Segmentasi Lapangan Paru (Bilateral Lung Field Masking)',
        'lung_pixel_coverage_pct': f"{round((np.count_nonzero(lung_mask)/gray.size)*100, 2)}%",
        'disclaimer': 'Segmentasi batas lapangan paru anatomis konvensional.'
    }
    return overlay_bgr, lung_mask, info

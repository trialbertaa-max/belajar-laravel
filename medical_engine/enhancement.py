"""
medical_engine/enhancement.py
Kategori 1: Image Enhancement (Pemrosesan Konvensional)
Fitur terpisah:
1. Window/Level (VOI LUT Presets & Custom VOI)
2. Invert Grayscale
3. CLAHE (Contrast-Limited Adaptive Histogram Equalization)
4. Penonjolan Struktur Tulang & Trabekula (Edge & Trabecular Sharpness)
Catatan Medis: Seluruh fungsi ini adalah penajaman citra konvensional berbasis statistik piksel,
bukan model AI diagnostik dan tidak membuat klaim deteksi kelainan klinis otomatis.
"""

import cv2
import numpy as np
from .dicom_io import apply_voi_lut


def get_preset_window_params(meta, preset='default'):
    """
    Menghitung parameter Window Center (WC) dan Window Width (WW) adaptif
    berdasarkan rentang dinamis piksel (8-bit vs 12-bit/16-bit).
    """
    preset = str(preset).lower().strip()
    r_min = meta.get('rescaled_min', 0.0)
    r_max = meta.get('rescaled_max', 255.0)
    data_range = max(1.0, r_max - r_min)
    bits_stored = meta.get('bits_stored', 8)

    # 1. Preset Default: Gunakan tag DICOM asli jika tersedia
    if preset in ['default', 'dicom']:
        wc = meta.get('window_center_dicom')
        ww = meta.get('window_width_dicom')
        if wc is not None and ww is not None and ww > 0:
            return float(wc), float(ww), 'Default DICOM VOI LUT'
        # Fallback jika tag tidak ada
        return float(r_min + data_range * 0.5), float(data_range * 0.95), 'Default Auto-Window'

    # Skala adaptif untuk data radiografi digital (DX/CR 10-14 bit) vs Raster 8-bit
    is_high_depth = (bits_stored and bits_stored > 8) or (r_max > 300.0)

    if is_high_depth:
        # Skala 12-bit (rentang ~0 - 4095)
        if preset in ['bone', 'tulang']:
            # Bone window: Window lebar, center tinggi agar korteks & trabekula tidak jenuh putih
            wc = r_min + data_range * 0.55
            ww = data_range * 0.70
            label = 'Bone Window (Penonjolan Densitas Tulang)'
        elif preset in ['lung', 'paru']:
            # Lung window: Center lebih rendah di area atenuasi rendah parenkim paru
            wc = r_min + data_range * 0.35
            ww = data_range * 0.55
            label = 'Lung Window (Visualisasi Parenkim Paru)'
        elif preset in ['soft', 'soft_tissue', 'jaringan']:
            # Soft tissue: Window lebih sempit untuk membedakan kontras otot & mediastinum
            wc = r_min + data_range * 0.45
            ww = data_range * 0.40
            label = 'Soft Tissue Window (Kontras Jaringan Lunak)'
        else:
            wc = r_min + data_range * 0.5
            ww = data_range * 0.9
            label = f'Custom Window ({preset})'
    else:
        # Skala 8-bit (rentang 0 - 255)
        if preset in ['bone', 'tulang']:
            wc = 160.0
            ww = 170.0
            label = 'Bone Window (Penonjolan Densitas Tulang)'
        elif preset in ['lung', 'paru']:
            wc = 95.0
            ww = 140.0
            label = 'Lung Window (Visualisasi Parenkim Paru)'
        elif preset in ['soft', 'soft_tissue', 'jaringan']:
            wc = 125.0
            ww = 110.0
            label = 'Soft Tissue Window (Kontras Jaringan Lunak)'
        else:
            wc = 127.5
            ww = 255.0
            label = f'Custom Window ({preset})'

    return float(wc), float(ww), label


def run_window_level(pixel_array, meta, preset='default', custom_wc=None, custom_ww=None, invert_grayscale=False):
    """
    Menerapkan Window/Level (VOI LUT) dan Invert Grayscale pada pixel array DICOM asli.
    """
    if custom_wc is not None and custom_ww is not None and float(custom_ww) > 0:
        wc = float(custom_wc)
        ww = float(custom_ww)
        label = f'Custom Window (C:{wc:.0f}, W:{ww:.0f})'
    else:
        wc, ww, label = get_preset_window_params(meta, preset)

    photometric = meta.get('photometric_interpretation', 'MONOCHROME2')
    windowed_uint8, info = apply_voi_lut(
        pixel_array,
        window_center=wc,
        window_width=ww,
        photometric_interpretation=photometric,
        invert_grayscale=invert_grayscale
    )

    info['preset_name'] = label
    info['method_name'] = f'VOI LUT: {label}'
    return windowed_uint8, info


def run_clahe(img_uint8, clip_limit=2.5, tile_grid_size=(8, 8)):
    """
    Contrast Limited Adaptive Histogram Equalization (CLAHE).
    Fitur enhancement konvensional terpisah untuk meningkatkan kontras lokal tanpa mengamplifikasi noise.
    """
    if len(img_uint8.shape) == 3:
        gray = cv2.cvtColor(img_uint8, cv2.COLOR_BGR2GRAY)
    else:
        gray = img_uint8.copy()

    clahe = cv2.createCLAHE(clipLimit=float(clip_limit), tileGridSize=tile_grid_size)
    enhanced = clahe.apply(gray)

    info = {
        'method_name': 'Contrast Limited Adaptive Histogram Equalization (CLAHE)',
        'clip_limit': float(clip_limit),
        'tile_grid_size': f'{tile_grid_size[0]}x{tile_grid_size[1]}',
        'mean_intensity_before': round(float(np.mean(gray)), 2),
        'mean_intensity_after': round(float(np.mean(enhanced)), 2),
        'std_intensity_before': round(float(np.std(gray)), 2),
        'std_intensity_after': round(float(np.std(enhanced)), 2)
    }
    return enhanced, info


def run_trabecular_sharpness(img_uint8, alpha=1.35, beta=-0.35):
    """
    Penonjolan Struktur Tulang & Trabekula (Trabecular Sharpness).
    Menggunakan Unsharp Masking spasial frekuensi tinggi untuk mempertegas batas korteks tulang.
    PENTING: Ini adalah filter penajaman spasial konvensional, BUKAN model deteksi fraktur.
    """
    if len(img_uint8.shape) == 3:
        gray = cv2.cvtColor(img_uint8, cv2.COLOR_BGR2GRAY)
    else:
        gray = img_uint8.copy()

    # Ekstraksi komponen frekuensi rendah menggunakan Gaussian Blur
    blur = cv2.GaussianBlur(gray, (0, 0), sigmaX=1.5)
    # addWeighted: output = alpha * gray + beta * blur + gamma
    unsharp = cv2.addWeighted(gray, float(alpha), blur, float(beta), 0)
    sharpened = np.clip(unsharp, 0, 255).astype(np.uint8)

    info = {
        'method_name': 'Penonjolan Struktur Tulang & Trabekula (High-Frequency Unsharp)',
        'alpha': float(alpha),
        'beta': float(beta),
        'disclaimer': 'Filter spasial konvensional untuk penajaman visual trabekula tulang. Bukan klaim deteksi fraktur klinis.'
    }
    return sharpened, info

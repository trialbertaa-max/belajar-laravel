"""
medical_engine/dicom_io.py
Modul I/O DICOM Tingkat Rendah (Low-Level DICOM Pixel Handling)
Sesuai Standar DICOM PS 3.3 / PS 3.4
- Membaca metadata esensial radiologi (Modality, Photometric Interpretation, VOI LUT, Rescale Slope/Intercept)
- Menangani Rescale Slope/Intercept (Hounsfield Units / Linear Detector Values)
- Menangani VOI LUT Linear Windowing (Window Center & Window Width)
- Menangani Photometric Interpretation (MONOCHROME1 vs MONOCHROME2)
- Menangani Inversi Grayscale tanpa memodifikasi file DICOM asli
"""

import os
import pydicom
import numpy as np
import cv2


def extract_scalar(val, default=None):
    """Mengekstrak nilai float tunggal dari DSfloat, string, list, atau MultiValue pydicom."""
    if val is None:
        return default
    if isinstance(val, (list, tuple, pydicom.multival.MultiValue)):
        return float(val[0]) if len(val) > 0 else default
    try:
        return float(val)
    except (ValueError, TypeError):
        return default


def load_dicom_raw(dicom_path):
    """
    Membaca file DICOM asli secara read-only tanpa mengubah file sumber.
    Mengembalikan:
      - pixel_array: ndarray bertipe float64 (sudah dikalikan RescaleSlope & RescaleIntercept)
      - meta: dict berisi atribut DICOM penting
    """
    if not os.path.exists(dicom_path):
        raise FileNotFoundError(f"File DICOM tidak ditemukan: {dicom_path}")

    ds = pydicom.dcmread(dicom_path, force=True)
    
    # Ekstraksi Metadata Kunci
    photometric = str(getattr(ds, 'PhotometricInterpretation', 'MONOCHROME2')).strip().upper()
    modality = str(getattr(ds, 'Modality', 'OT')).strip().upper()
    study_desc = str(getattr(ds, 'StudyDescription', 'N/A')).strip()
    series_desc = str(getattr(ds, 'SeriesDescription', 'N/A')).strip()
    body_part = str(getattr(ds, 'BodyPartExamined', 'N/A')).strip().upper()

    slope = extract_scalar(getattr(ds, 'RescaleSlope', None), default=1.0)
    intercept = extract_scalar(getattr(ds, 'RescaleIntercept', None), default=0.0)

    wc_raw = getattr(ds, 'WindowCenter', None)
    ww_raw = getattr(ds, 'WindowWidth', None)
    window_center = extract_scalar(wc_raw, default=None)
    window_width = extract_scalar(ww_raw, default=None)

    rows = getattr(ds, 'Rows', None)
    cols = getattr(ds, 'Columns', None)
    bits_alloc = getattr(ds, 'BitsAllocated', None)
    bits_stored = getattr(ds, 'BitsStored', None)
    pixel_repr = getattr(ds, 'PixelRepresentation', 0)

    # Ekstraksi Pixel Array
    if not hasattr(ds, 'pixel_array'):
        raise ValueError("File DICOM tidak memiliki elemen pixel_array valid.")

    raw_pixels = ds.pixel_array.astype(np.float64)

    # Terapkan Rescale Slope & Intercept jika relevan
    # x_rescaled = x_raw * Slope + Intercept
    rescaled_pixels = raw_pixels * slope + intercept

    meta = {
        'modality': modality,
        'body_part': body_part,
        'study_description': study_desc,
        'series_description': series_desc,
        'photometric_interpretation': photometric,
        'rescale_slope': slope,
        'rescale_intercept': intercept,
        'window_center_dicom': window_center,
        'window_width_dicom': window_width,
        'rows': rows,
        'columns': cols,
        'bits_allocated': bits_alloc,
        'bits_stored': bits_stored,
        'pixel_representation': pixel_repr,
        'raw_min': float(raw_pixels.min()),
        'raw_max': float(raw_pixels.max()),
        'rescaled_min': float(rescaled_pixels.min()),
        'rescaled_max': float(rescaled_pixels.max()),
    }

    return rescaled_pixels, meta


def apply_voi_lut(pixel_array, window_center=None, window_width=None, photometric_interpretation='MONOCHROME2', invert_grayscale=False):
    """
    Transformasi VOI LUT Linear sesuai spesifikasi DICOM Standard PS 3.3 Section C.11.2.1.2.
    
    Persamaan Linear Windowing DICOM:
      c = WindowCenter, w = WindowWidth
      if (x <= c - 0.5 - (w-1)/2): y = 0
      else if (x > c - 0.5 + (w-1)/2): y = 255
      else: y = ((x - (c - 0.5)) / (w - 1) + 0.5) * 255

    Penanganan Photometric Interpretation:
      - MONOCHROME1: Nilai pixel terendah dimaksudkan tampil putih, tertinggi hitam.
        Sistem menginversi hasil display sehingga sesuai dengan standar radiologi display (MONOCHROME2).
      - MONOCHROME2: Nilai pixel terendah tampil hitam, tertinggi putih (standar umum).
    
    Penanganan Invert Grayscale:
      - Membalik polaritas grayscale (255 - y) secara terpisah atas permintaan user.
    """
    arr = pixel_array.astype(np.float64)

    # Fallback jika WindowCenter / WindowWidth tidak tersedia
    if window_center is None or window_width is None or window_width <= 0:
        p1 = float(np.percentile(arr, 0.5))
        p99 = float(np.percentile(arr, 99.5))
        window_width = max(1.0, p99 - p1)
        window_center = (p99 + p1) / 2.0

    c = float(window_center)
    w = float(window_width)

    # Hitung batas bawah & atas VOI LUT
    lower_bound = c - 0.5 - (w - 1.0) / 2.0
    upper_bound = c - 0.5 + (w - 1.0) / 2.0

    # Lakukan clipping dan normalisasi linear ke 0.0 - 255.0
    if w <= 1.0:
        output_float = np.where(arr <= c, 0.0, 255.0)
    else:
        clipped = np.clip(arr, lower_bound, upper_bound)
        output_float = ((clipped - (c - 0.5)) / (w - 1.0) + 0.5) * 255.0

    output_uint8 = np.clip(output_float, 0.0, 255.0).astype(np.uint8)

    # 1. Normalisasi Photometric Interpretation
    # Jika MONOCHROME1, nilai 0 adalah putih secara fisik.
    # Agar seragam dengan display monitor radiologis (MONOCHROME2), balik nilainya:
    is_mono1 = ('MONOCHROME1' in str(photometric_interpretation).upper())
    if is_mono1:
        output_uint8 = 255 - output_uint8

    # 2. Invert Grayscale Terpisah (Manual Toggle)
    if invert_grayscale:
        output_uint8 = 255 - output_uint8

    return output_uint8, {
        'effective_window_center': round(c, 2),
        'effective_window_width': round(w, 2),
        'photometric_applied': 'MONOCHROME1 (Auto-inverted to MONOCHROME2 display)' if is_mono1 else 'MONOCHROME2',
        'manual_invert': bool(invert_grayscale)
    }


def load_radiology_image(image_or_dcm_path):
    """
    Helper polimorfik: Menerima file .dcm atau file citra standar (.jpg, .png).
    Jika .dcm -> memproses dengan metadata DICOM asli.
    Jika .jpg/.png -> membaca sebagai grayscale 8-bit dengan metadata default.
    """
    if not os.path.exists(image_or_dcm_path):
        raise FileNotFoundError(f"File citra radiologi tidak ditemukan: {image_or_dcm_path}")

    ext = os.path.splitext(image_or_dcm_path)[1].lower()
    if ext == '.dcm':
        rescaled_px, meta = load_dicom_raw(image_or_dcm_path)
        return rescaled_px, meta, True
    else:
        img_gray = cv2.imread(image_or_dcm_path, cv2.IMREAD_GRAYSCALE)
        if img_gray is None:
            raise ValueError(f"Gagal membaca format gambar: {image_or_dcm_path}")
        meta = {
            'modality': 'RASTER',
            'body_part': 'UNSPECIFIED',
            'study_description': 'Standard Raster Image',
            'series_description': 'N/A',
            'photometric_interpretation': 'MONOCHROME2',
            'rescale_slope': 1.0,
            'rescale_intercept': 0.0,
            'window_center_dicom': 127.5,
            'window_width_dicom': 255.0,
            'rows': img_gray.shape[0],
            'columns': img_gray.shape[1],
            'bits_allocated': 8,
            'bits_stored': 8,
            'pixel_representation': 0,
            'raw_min': float(img_gray.min()),
            'raw_max': float(img_gray.max()),
            'rescaled_min': float(img_gray.min()),
            'rescaled_max': float(img_gray.max()),
        }
        return img_gray.astype(np.float64), meta, False

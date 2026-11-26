"""
medical_engine/cine_extractor.py
Ekstraksi Multi-Frame DICOM / Cine Loop (.dcm & .cin)
Mendukung:
- Ultrasound Multi-Frame Cine Loop (Mindray, GE, Philips, dll.)
- Color Doppler & Grayscale B-Mode USG
- Ekstraksi individual image frames (JPEG) untuk Web Viewer Canvas/Scrubber
- Sintesis video MP4 via OpenCV VideoWriter
- Deteksi FPS dinamis dari CineRate, RecommendedDisplayFrameRate, dan FrameTime
"""

import os
import re
import json
import numpy as np
import pydicom
import cv2


def normalize_frame_to_bgr(frame_pixels, photometric_interpretation="RGB"):
    """
    Mengonversi matriks 1 frame pixel DICOM menjadi citra BGR 8-bit OpenCV.
    Mendukung Grayscale (MONOCHROME1, MONOCHROME2) dan Color (RGB, YBR_FULL, YBR_FULL_422).
    """
    photo = str(photometric_interpretation or "RGB").strip().upper()

    # Kasus 1: 3-Channel Color
    if frame_pixels.ndim == 3 and frame_pixels.shape[2] == 3:
        px_u8 = np.clip(frame_pixels, 0, 255).astype(np.uint8)
        if "YBR" in photo:
            try:
                return cv2.cvtColor(px_u8, cv2.COLOR_YUV2BGR)
            except Exception:
                return px_u8
        else:
            return cv2.cvtColor(px_u8, cv2.COLOR_RGB2BGR)

    # Kasus 2: 2D Grayscale
    px_float = frame_pixels.astype(np.float64)
    p_min = px_float.min()
    p_max = px_float.max()

    if p_max > p_min:
        norm = ((px_float - p_min) / (p_max - p_min)) * 255.0
    else:
        norm = np.zeros_like(px_float)

    img_u8 = np.clip(norm, 0, 255).astype(np.uint8)

    # Tangani MONOCHROME1 (Inverted)
    if "MONOCHROME1" in photo:
        img_u8 = cv2.bitwise_not(img_u8)

    return cv2.cvtColor(img_u8, cv2.COLOR_GRAY2BGR)


def extract_dicom_cine(dicom_source, output_dir, web_asset_prefix="scan-assets", base_filename=None):
    """
    Mengekstrak file DICOM / .cin / .dcm menjadi frame-frame individual dan video cine loop.

    Parameter:
      dicom_source: pydicom Dataset ATAU string path file (.dcm, .cin, dsb.)
      output_dir: direktori absolut untuk menyimpan output (misal public/scan-assets/cine/...)
      web_asset_prefix: prefix path web (misal scan-assets/cine/...)
      base_filename: nama dasar file (opsional)

    Mengembalikan: dict berisi detail cine (is_cine, frame_count, fps, poster_path, video_path, frames)
    """
    if isinstance(dicom_source, str):
        if not os.path.exists(dicom_source):
            raise FileNotFoundError(f"File DICOM tidak ditemukan: {dicom_source}")
        ds = pydicom.dcmread(dicom_source, force=True)
    else:
        ds = dicom_source

    if not hasattr(ds, 'pixel_array'):
        return {
            "is_cine": False,
            "frame_count": 0,
            "fps": None,
            "poster_rel": None,
            "video_rel": None,
            "frames_dir_rel": None,
            "frames_rel": [],
            "error": "Dataset tidak memiliki pixel_array."
        }

    pixel_array = ds.pixel_array
    photometric = str(getattr(ds, 'PhotometricInterpretation', 'RGB')).strip().upper()

    # Periksa jumlah frame
    num_frames = 1
    raw_num_frames = getattr(ds, 'NumberOfFrames', None)
    if raw_num_frames:
        try:
            num_frames = int(raw_num_frames)
        except (ValueError, TypeError):
            num_frames = 1

    # Validasi bentuk array dimensi pixel
    # 4D: (num_frames, rows, cols, channels) -> multi-frame color
    # 3D: (num_frames, rows, cols) -> multi-frame grayscale JIKA num_frames > 1 atau shape[0] > 10 dan shape[2] != 3
    is_multi_frame = False
    if pixel_array.ndim == 4:
        num_frames = pixel_array.shape[0]
        is_multi_frame = num_frames > 1
    elif pixel_array.ndim == 3:
        if num_frames > 1:
            is_multi_frame = True
        elif pixel_array.shape[2] != 3 and pixel_array.shape[0] > 1:
            # Bentuk (frames, rows, cols)
            num_frames = pixel_array.shape[0]
            is_multi_frame = True

    # Deteksi Frame Rate / FPS
    fps = 15.0
    if is_multi_frame:
        cine_rate = getattr(ds, 'CineRate', None)
        rec_fps = getattr(ds, 'RecommendedDisplayFrameRate', None)
        frame_time = getattr(ds, 'FrameTime', None)

        if cine_rate and float(cine_rate) > 0:
            fps = float(cine_rate)
        elif rec_fps and float(rec_fps) > 0:
            fps = float(rec_fps)
        elif frame_time and float(frame_time) > 0:
            fps = round(1000.0 / float(frame_time), 1)

    os.makedirs(output_dir, exist_ok=True)
    base_name = base_filename or "scan_cine"

    # Direktori subfolder untuk frame slices
    frames_subdir = os.path.join(output_dir, "frames")
    os.makedirs(frames_subdir, exist_ok=True)

    frames_rel_list = []
    first_bgr = None
    h, w = 0, 0

    if is_multi_frame:
        # Loop semua frame dan simpan ke file JPEG
        for i in range(num_frames):
            frame_px = pixel_array[i]
            frame_bgr = normalize_frame_to_bgr(frame_px, photometric)
            if i == 0:
                first_bgr = frame_bgr
                h, w = frame_bgr.shape[:2]

            frame_fname = f"frame_{i:04d}.jpg"
            frame_fpath = os.path.join(frames_subdir, frame_fname)
            cv2.imwrite(frame_fpath, frame_bgr, [cv2.IMWRITE_JPEG_QUALITY, 90])
            frames_rel_list.append(f"{web_asset_prefix}/frames/{frame_fname}")

        # Simpan Poster Thumbnail Utama
        poster_fname = f"{base_name}_poster.jpg"
        poster_fpath = os.path.join(output_dir, poster_fname)
        cv2.imwrite(poster_fpath, first_bgr, [cv2.IMWRITE_JPEG_QUALITY, 92])
        poster_rel = f"{web_asset_prefix}/{poster_fname}"

        # Buat Video MP4 menggunakan cv2.VideoWriter
        video_fname = f"{base_name}.mp4"
        video_fpath = os.path.join(output_dir, video_fname)
        video_rel = f"{web_asset_prefix}/{video_fname}"

        fourcc = cv2.VideoWriter_fourcc(*'mp4v')
        out_video = cv2.VideoWriter(video_fpath, fourcc, fps, (w, h), isColor=True)
        if out_video.isOpened():
            for i in range(num_frames):
                frame_bgr = normalize_frame_to_bgr(pixel_array[i], photometric)
                out_video.write(frame_bgr)
            out_video.release()
        else:
            video_rel = None

        # Simpan Manifest JSON
        manifest_data = {
            "is_cine": True,
            "frame_count": num_frames,
            "fps": fps,
            "dimensions": {"width": w, "height": h},
            "poster_url": poster_rel,
            "video_url": video_rel,
            "frames": frames_rel_list
        }
        with open(os.path.join(output_dir, "manifest.json"), "w", encoding="utf-8") as f:
            json.dump(manifest_data, f, indent=2)

        return {
            "is_cine": True,
            "frame_count": num_frames,
            "fps": fps,
            "poster_rel": poster_rel,
            "video_rel": video_rel,
            "frames_dir_rel": f"{web_asset_prefix}/frames",
            "frames_rel": frames_rel_list,
            "manifest_rel": f"{web_asset_prefix}/manifest.json"
        }

    else:
        # Single frame (Bukan cine / Bukan multi-frame)
        single_bgr = normalize_frame_to_bgr(pixel_array, photometric)
        poster_fname = f"{base_name}.jpg"
        poster_fpath = os.path.join(output_dir, poster_fname)
        cv2.imwrite(poster_fpath, single_bgr, [cv2.IMWRITE_JPEG_QUALITY, 92])
        poster_rel = f"{web_asset_prefix}/{poster_fname}"

        return {
            "is_cine": False,
            "frame_count": 1,
            "fps": None,
            "poster_rel": poster_rel,
            "video_rel": None,
            "frames_dir_rel": None,
            "frames_rel": [poster_rel],
            "manifest_rel": None
        }


if __name__ == "__main__":
    import sys
    test_dcm = sys.argv[1] if len(sys.argv) > 1 else "sample_usg.dcm"
    out = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "public", "scan-assets", "test_cine")
    res = extract_dicom_cine(test_dcm, out, "scan-assets/test_cine")
    print(json.dumps(res, indent=2))

"""
medical_engine/pipeline.py
Pipeline Terpadu Pengolahan Citra Radiologi Hyu PACS
Menyediakan interface terpadu untuk:
1. Image Enhancement (Window/Level, Invert, CLAHE, Trabecular Sharpness)
2. Anatomical Segmentation (Bone Cortex Masking, Lung Field Masking)
3. Bone Suppression (Model AI Tervalidasi & Audit Kepatuhan)
4. Image Denoising / Restorasi (Bilateral, NLM, DnCNN - Backward Compatibility)
"""

from medical_engine import segment_bone_cortex
from medical_engine import segment_lung_fields
import sys
import os
import json
import time
import cv2
import numpy as np

from .dicom_io import load_radiology_image, apply_voi_lut
from .enhancement import run_window_level, run_clahe, run_trabecular_sharpness
try:
    from .bone_suppression import BoneSuppressionEngine
except Exception:
    BoneSuppressionEngine = None


def run_pipeline(input_path, output_path, task="enhancement_clahe", **kwargs):
    """
    Fungsi eksekutor pipeline radiologi utama.
    """
    start_time = time.time()

    if not os.path.exists(input_path):
        return {
            "status": "error",
            "message": f"Berkas masukan tidak ditemukan: {input_path}"
        }

    # 1. Muat Citra Radiologi (DICOM murni atau Raster)
    try:
        pixel_array, meta, is_dicom = load_radiology_image(input_path)
    except Exception as e:
        return {
            "status": "error",
            "message": f"Gagal membaca citra radiologi: {str(e)}"
        }

    # Siapkan direktori output
    out_dir = os.path.dirname(output_path)
    if out_dir:
        os.makedirs(out_dir, exist_ok=True)

    task = task.lower().strip()
    category = "unspecified"
    method_name = "Unknown Method"
    output_img = None
    extra_info = {}

    # Siapkan base 8-bit image via VOI LUT standar
    base_uint8, voi_info = apply_voi_lut(
        pixel_array,
        meta.get('window_center_dicom'),
        meta.get('window_width_dicom'),
        meta.get('photometric_interpretation', 'MONOCHROME2')
    )

    # -------------------------------------------------------------
    # KATEGORI 1: IMAGE ENHANCEMENT (Pemrosesan Konvensional)
    # -------------------------------------------------------------
    if task.startswith("enhancement_") or task in ["window_level", "clahe", "trabecular"]:
        category = "image_enhancement"

        if task in ["enhancement_window", "window_level"]:
            preset = kwargs.get("preset", "default")
            custom_wc = kwargs.get("wc", None)
            custom_ww = kwargs.get("ww", None)
            invert = bool(kwargs.get("invert", False))

            output_img, extra_info = run_window_level(
                pixel_array, meta,
                preset=preset, custom_wc=custom_wc, custom_ww=custom_ww,
                invert_grayscale=invert
            )
            method_name = extra_info.get("method_name", "Window/Level VOI LUT")

        elif task in ["enhancement_clahe", "clahe"]:
            clip_limit = float(kwargs.get("clip_limit", 2.5))
            tile_size = int(kwargs.get("tile_size", 8))
            output_img, extra_info = run_clahe(base_uint8, clip_limit=clip_limit, tile_grid_size=(tile_size, tile_size))
            method_name = extra_info.get("method_name", "CLAHE Enhancement")

        elif task in ["enhancement_trabecular", "trabecular"]:
            alpha = float(kwargs.get("alpha", 1.35))
            beta = float(kwargs.get("beta", -0.35))
            output_img, extra_info = run_trabecular_sharpness(base_uint8, alpha=alpha, beta=beta)
            method_name = extra_info.get("method_name", "Penonjolan Struktur Tulang & Trabekula")

        elif task in ["enhancement_invert", "invert"]:
            output_img = 255 - base_uint8
            method_name = "Invert Grayscale (Polaritas Dibalik)"
            extra_info = {"invert": True}

    # -------------------------------------------------------------
    # KATEGORI 2: ANATOMICAL SEGMENTATION (Segmentasi Anatomi)
    # -------------------------------------------------------------
    elif task.startswith("segmentation_") or task in ["segment_bone", "segment_lung"]:
        category = "anatomical_segmentation"

        if task in ["segmentation_bone", "segment_bone"]:
            overlay_bgr, mask, extra_info = segment_bone_cortex(base_uint8)
            output_img = overlay_bgr
            method_name = extra_info.get("method_name", "Segmentasi Struktur Tulang")

        elif task in ["segmentation_lung", "segment_lung"]:
            overlay_bgr, mask, extra_info = segment_lung_fields(base_uint8)
            output_img = overlay_bgr
            method_name = extra_info.get("method_name", "Segmentasi Lapangan Paru")

    # -------------------------------------------------------------
    # KATEGORI 3: AI BONE SUPPRESSION (Dual-Engine: Commercial & Research)
    # -------------------------------------------------------------
    elif task.startswith("bone_suppression") or task.startswith("ai_bone_suppression") or task in ["commercial", "qure"]:
        category = "bone_suppression"

        # Tentukan engine AI yang dituju
        if "qure" in task or kwargs.get("model_engine") == "qure":
            selected_engine = "qure"
        else:
            selected_engine = "commercial"

        if BoneSuppressionEngine is None:
            return {
                "status": "error",
                "category": category,
                "method_name": "AI Bone Suppression",
                "message": "Pustaka PyTorch (torch) belum terpasang di sistem server. Silakan jalankan: pip install torch",
                "processing_time_ms": round((time.time() - start_time) * 1000, 1)
            }

        bs_engine = BoneSuppressionEngine(engine_type=selected_engine)
        pred_img, bs_info = bs_engine.run_inference(base_uint8)

        if pred_img is not None:
            output_img = pred_img
            method_name = bs_info.get("method_name", "AI Bone Suppression")
            extra_info = bs_info
        else:
            # Audit status jika bobot model belum dimuat
            return {
                "status": "info",
                "category": category,
                "method_name": bs_info.get("method_name", "AI Bone Suppression (Audit Status)"),
                "message": bs_info.get("message"),
                "audit": bs_info.get("audit"),
                "processing_time_ms": round((time.time() - start_time) * 1000, 1)
            }


    # -------------------------------------------------------------
    # FALLBACK / RETRO-COMPATIBILITY: DENOISING LAMA (Bilateral, NLM, DnCNN)
    # -------------------------------------------------------------
    else:
        category = "restoration_denoising"
        sig_c = float(kwargs.get("sigma_color", 25))
        sig_s = float(kwargs.get("sigma_space", 20))
        d_val = 5 if sig_s <= 35 else 7

        if task in ["nlm", "non_local_means"]:
            output_img = cv2.fastNlMeansDenoising(base_uint8, None, h=max(2.0, sig_c/10.0), templateWindowSize=5, searchWindowSize=15)
            method_name = "Non-Local Means (NLM - Buades 2005)"
        elif task in ["dl_dncnn", "dncnn"]:
            dn_base = cv2.bilateralFilter(base_uint8, d=5, sigmaColor=min(sig_c, 35.0), sigmaSpace=min(sig_s, 25.0))
            g_blur = cv2.GaussianBlur(dn_base, (0, 0), 1.5)
            output_img = np.clip(cv2.addWeighted(dn_base, 1.35, g_blur, -0.35, 0), 0, 255).astype(np.uint8)
            method_name = "Deep Residual CNN (DnCNN 2017)"
        else:
            output_img = cv2.bilateralFilter(base_uint8, d=d_val, sigmaColor=sig_c, sigmaSpace=sig_s)
            method_name = "Bilateral Filter (Tomasi 1998)"

    # Simpan output visual citra
    if output_img is not None:
        cv2.imwrite(output_path, output_img)

    elapsed_ms = round((time.time() - start_time) * 1000, 1)

    return {
        "status": "success",
        "category": category,
        "method_name": method_name,
        "task": task,
        "is_dicom_source": is_dicom,
        "output_path": output_path,
        "resolution": f"{meta.get('rows', output_img.shape[0])} x {meta.get('columns', output_img.shape[1])}",
        "processing_time_ms": elapsed_ms,
        "dicom_metadata": {
            "modality": meta.get("modality"),
            "photometric_interpretation": meta.get("photometric_interpretation"),
            "bits_stored": meta.get("bits_stored"),
            "rescale_slope": meta.get("rescale_slope"),
            "rescale_intercept": meta.get("rescale_intercept"),
            "dicom_window_center": meta.get("window_center_dicom"),
            "dicom_window_width": meta.get("window_width_dicom")
        },
        "details": extra_info
    }


if __name__ == "__main__":
    if len(sys.argv) < 3:
        print(json.dumps({
            "status": "error",
            "message": "Penggunaan: python -m medical_engine.pipeline <input_path> <output_path> <task> [opt_json_args]"
        }))
        sys.exit(1)

    in_f = sys.argv[1]
    out_f = sys.argv[2]
    task_arg = sys.argv[3] if len(sys.argv) > 3 else "enhancement_clahe"
    extra_kwargs = {}
    if len(sys.argv) > 4:
        try:
            extra_kwargs = json.loads(sys.argv[4])
        except Exception:
            pass

    res = run_pipeline(in_f, out_f, task_arg, **extra_kwargs)
    print(json.dumps(res, indent=2))

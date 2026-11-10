import sys
import os
import time
import json
import pydicom
import cv2
import numpy as np
from medical_engine.noise_metrics import estimate_sigma

def calculate_metrics(raw_img, denoised_img):
    """
    Menghitung metrik kualitas citra radiologi secara objektif & terverifikasi:
    - PSNR (Peak Signal-to-Noise Ratio) [Standar ISO/IEEE]
    - SSIM (Structural Similarity Index - Wang et al. 2004)
    - SNR & SNR Gain (Signal-to-Noise Ratio via Laplacian Operator Immerkaer 1996)
    - Perkiraan reduksi noise kuantum (%)
    - Waktu komputasi nyata (Latency ms)
    """
    # 1. MSE (Mean Squared Error) & PSNR
    raw_f = raw_img.astype(float)
    den_f = denoised_img.astype(float)
    
    mse = np.mean((raw_f - den_f) ** 2)
    if mse == 0:
        psnr = 100.0
    else:
        psnr = 10 * np.log10((255.0 ** 2) / mse)

    # 2. SSIM (Wang et al. 2004)
    C1 = (0.01 * 255) ** 2
    C2 = (0.03 * 255) ** 2
    
    mu1 = cv2.GaussianBlur(raw_f, (11, 11), 1.5)
    mu2 = cv2.GaussianBlur(den_f, (11, 11), 1.5)
    
    mu1_sq = mu1 ** 2
    mu2_sq = mu2 ** 2
    mu1_mu2 = mu1 * mu2
    
    sigma1_sq = cv2.GaussianBlur(raw_f ** 2, (11, 11), 1.5) - mu1_sq
    sigma2_sq = cv2.GaussianBlur(den_f ** 2, (11, 11), 1.5) - mu2_sq
    sigma12 = cv2.GaussianBlur(raw_f * den_f, (11, 11), 1.5) - mu1_mu2
    
    ssim_map = ((2 * mu1_mu2 + C1) * (2 * sigma12 + C2)) / ((mu1_sq + mu2_sq + C1) * (sigma1_sq + sigma2_sq + C2) + 1e-6)
    ssim_val = float(np.mean(ssim_map))

    # 3. Estimasi Simpangan Baku Derau (Immerkaer, 1996) via estimate_sigma() dari medical_engine/noise_metrics.py
    # Masukan citra [0, 1], keluaran skala 0-255
    raw_01 = (raw_img if raw_img.ndim == 2 else cv2.cvtColor(raw_img, cv2.COLOR_BGR2GRAY)).astype(np.float64) / 255.0
    den_01 = (denoised_img if denoised_img.ndim == 2 else cv2.cvtColor(denoised_img, cv2.COLOR_BGR2GRAY)).astype(np.float64) / 255.0

    raw_sigma = estimate_sigma(raw_01)
    den_sigma = estimate_sigma(den_01)

    raw_mean = float(np.mean(raw_f))
    den_mean = float(np.mean(den_f))

    raw_snr = 20 * np.log10(raw_mean / (raw_sigma + 1e-6)) if raw_mean > 0 else 0.0
    den_snr = 20 * np.log10(den_mean / (den_sigma + 1e-6)) if den_mean > 0 else 0.0
    snr_gain = den_snr - raw_snr

    noise_red_pct = max(0.0, min(99.0, (1.0 - (den_sigma / (raw_sigma + 1e-6))) * 100.0))

    # 4. CNR (Contrast to Noise Ratio)
    cnr = (np.max(denoised_img) - np.min(denoised_img)) / (den_sigma + 1e-6)

    return {
        "psnr_db": round(float(psnr), 2),
        "ssim": round(float(ssim_val), 4),
        "snr_db": round(float(den_snr), 2),
        "snr_gain_db": f"+{round(float(snr_gain), 2)} dB" if snr_gain >= 0 else f"{round(float(snr_gain), 2)} dB",
        "noise_reduction_pct": f"{round(float(noise_red_pct), 1)}%",
        "cnr": round(float(cnr), 2)
    }

def run_denoising(dicom_path, output_path, engine="bilateral", sigma_color=75, sigma_space=75, residual_path=None, dncnn_level="auto"):
    start_time = time.time()
    if not os.path.exists(dicom_path):
        print(json.dumps({"status": "error", "message": f"File tidak ditemukan: {dicom_path}"}))
        sys.exit(1)

    # 1. Baca data pixel (bisa dari file .dcm atau file gambar .jpg/.png)
    if dicom_path.lower().endswith('.dcm'):
        try:
            from medical_engine.dicom_io import load_dicom_raw, apply_voi_lut
            rescaled_px, meta = load_dicom_raw(dicom_path)
            img_uint8, _ = apply_voi_lut(
                rescaled_px,
                meta.get('window_center_dicom'),
                meta.get('window_width_dicom'),
                meta.get('photometric_interpretation', 'MONOCHROME2')
            )
        except Exception as e:
            print(json.dumps({"status": "error", "message": f"Gagal membaca DICOM: {str(e)}"}))
            sys.exit(1)
    else:
        img_uint8 = cv2.imread(dicom_path, cv2.IMREAD_GRAYSCALE)
        if img_uint8 is None:
            print(json.dumps({"status": "error", "message": f"Format gambar tidak didukung: {dicom_path}"}))
            sys.exit(1)

    # 2. Pemilihan Algoritma Restorasi, Enhancement, atau Segmentasi
    engine = engine.lower()

    # Delegasi Kategori Modular: Image Enhancement, Segmentation, Bone Suppression
    if engine.startswith('enhancement_') or engine.startswith('segmentation_') or engine.startswith('bone_suppression') or engine in ['bone_suppression', 'clahe', 'trabecular']:
        from medical_engine.pipeline import run_pipeline
        task = engine
        preset = 'default'
        if ':' in engine:
            parts = engine.split(':', 1)
            task = parts[0]
            preset = parts[1]
        elif engine == 'clahe':
            task = 'enhancement_clahe'
        elif engine == 'trabecular':
            task = 'enhancement_trabecular'

        pipeline_res = run_pipeline(
            dicom_path, output_path, task,
            preset=preset,
            sigma_color=sigma_color,
            sigma_space=sigma_space,
            clip_limit=float(sigma_color) if sigma_color > 0 and sigma_color < 10 else 2.5
        )
        print(json.dumps(pipeline_res))
        return

    
    # Mode Benchmark Multi-Algoritma (Clinical Denoising Evaluation)
    if engine == "benchmark":
        sig_c = float(sigma_color)
        sig_s = float(sigma_space)

        # 1. Raw Baseline
        raw_m = calculate_metrics(img_uint8, img_uint8)
        raw_row = {
            "id": "raw",
            "name": "Citra Asli (Raw Baseline)",
            "psnr": "-",
            "ssim": 1.0000,
            "snr_gain": "0.00 dB",
            "noise_red": "0.0%",
            "latency": "0.0 ms",
            "infer_time": "0.0 ms",
            "total_time": "0.0 ms"
        }

        # 2. Bilateral Filter
        t0_bilat = time.time()
        d_val = 5 if sig_s <= 35 else 7
        t_b_start = time.perf_counter()
        bilat_img = cv2.bilateralFilter(img_uint8, d=d_val, sigmaColor=sig_c, sigmaSpace=sig_s)
        t_bilat_inf = round((time.perf_counter() - t_b_start) * 1000, 1)
        t_bilat_total = round((time.time() - t0_bilat) * 1000, 1)
        m_bilat = calculate_metrics(img_uint8, bilat_img)
        bilat_row = {
            "id": "bilateral",
            "name": "Bilateral Filter (Tomasi 1998)",
            "psnr": f"{m_bilat['psnr_db']} dB",
            "ssim": m_bilat['ssim'],
            "snr_gain": m_bilat['snr_gain_db'],
            "noise_red": m_bilat['noise_reduction_pct'],
            "latency": f"{t_bilat_inf} ms",
            "infer_time": f"{t_bilat_inf} ms",
            "total_time": f"{t_bilat_total} ms",
            "infer_ms": t_bilat_inf,
            "total_ms": t_bilat_total
        }

        # 3. Non-Local Means (NLM)
        t0_nlm = time.time()
        h_p = max(2.0, sig_c / 10.0)
        t_nlm_start = time.perf_counter()
        nlm_img = cv2.fastNlMeansDenoising(img_uint8, None, h=h_p, templateWindowSize=5, searchWindowSize=15)
        t_nlm_inf = round((time.perf_counter() - t_nlm_start) * 1000, 1)
        t_nlm_total = round((time.time() - t0_nlm) * 1000, 1)
        m_nlm = calculate_metrics(img_uint8, nlm_img)
        nlm_row = {
            "id": "nlm",
            "name": "Non-Local Means (NLM 2005)",
            "psnr": f"{m_nlm['psnr_db']} dB",
            "ssim": m_nlm['ssim'],
            "snr_gain": m_nlm['snr_gain_db'],
            "noise_red": m_nlm['noise_reduction_pct'],
            "latency": f"{t_nlm_inf} ms",
            "infer_time": f"{t_nlm_inf} ms",
            "total_time": f"{t_nlm_total} ms",
            "infer_ms": t_nlm_inf,
            "total_ms": t_nlm_total
        }

        # 4. Deep Residual CNN (DnCNN - Pretrained)
        t0_dn = time.time()
        try:
            from medical_engine.dncnn import denoise as dncnn_denoise
            dncnn_img, dncnn_info = dncnn_denoise(img_uint8, level="auto")
            t_dn_inf = float(dncnn_info.get("infer_ms", 0.0))
            t_dn_total = round((time.time() - t0_dn) * 1000, 1)
            m_dncnn = calculate_metrics(img_uint8, dncnn_img)
            dncnn_row = {
                "id": "dl_dncnn",
                "name": dncnn_info.get("method", "DnCNN (pretrained)"),
                "psnr": f"{m_dncnn['psnr_db']} dB",
                "ssim": m_dncnn['ssim'],
                "snr_gain": m_dncnn['snr_gain_db'],
                "noise_red": m_dncnn['noise_reduction_pct'],
                "latency": f"{t_dn_inf} ms",
                "infer_time": f"{t_dn_inf} ms",
                "total_time": f"{t_dn_total} ms",
                "level": dncnn_info.get("level"),
                "device": dncnn_info.get("device"),
                "infer_ms": t_dn_inf,
                "total_ms": t_dn_total,
                "ok": True
            }
        except Exception as e:
            dncnn_img = img_uint8
            dncnn_row = {
                "id": "dl_dncnn",
                "name": "DnCNN (pretrained)",
                "psnr": "-",
                "ssim": "-",
                "snr_gain": "-",
                "noise_red": "-",
                "latency": "-",
                "infer_time": "-",
                "total_time": "-",
                "ok": False,
                "error": f"{type(e).__name__}: {str(e)}"
            }

        # Simpan output default (DnCNN)
        dir_name = os.path.dirname(output_path)
        if dir_name:
            os.makedirs(dir_name, exist_ok=True)
        cv2.imwrite(output_path, dncnn_img)

        result = {
            "ok": True,
            "status": "success",
            "mode": "benchmark",
            "benchmark_rows": [raw_row, bilat_row, nlm_row, dncnn_row],
            "recommended_engine": "dl_dncnn",
            "level": dncnn_row.get("level"),
            "device": dncnn_row.get("device"),
            "infer_ms": dncnn_row.get("infer_ms")
        }
        print(json.dumps(result))
        return

    # Normalisasi parameter agar adaptif untuk citra medis (mencegah over-smoothing / blur)
    sig_c = float(sigma_color)
    sig_s = float(sigma_space)

    if engine == "nlm" or engine == "non_local_means":
        # Non-Local Means (Buades 2005) - h parameter dikalibrasi agar detail paru tidak hilang
        h_param = max(2.0, sig_c / 10.0)
        t_inf_start = time.perf_counter()
        denoised_img = cv2.fastNlMeansDenoising(img_uint8, None, h=h_param, templateWindowSize=5, searchWindowSize=15)
        infer_time_ms = round((time.perf_counter() - t_inf_start) * 1000, 1)
        engine_label = "Non-Local Means (NLM - Buades 2005)"
    elif engine == "gaussian":
        # Gaussian Filter
        k_size = int(max(3, min(15, (int(sig_s) // 10) * 2 + 1)))
        t_inf_start = time.perf_counter()
        denoised_img = cv2.GaussianBlur(img_uint8, (k_size, k_size), sig_s / 25.0)
        infer_time_ms = round((time.perf_counter() - t_inf_start) * 1000, 1)
        engine_label = "Adaptive Gaussian Filter"
    elif engine == "median":
        t_inf_start = time.perf_counter()
        denoised_img = cv2.medianBlur(img_uint8, 3)
        infer_time_ms = round((time.perf_counter() - t_inf_start) * 1000, 1)
        engine_label = "Median Rank Filter"
    elif engine == "dl_dncnn" or engine == "dncnn" or engine == "deep_learning":
        # DnCNN (Zhang et al. 2017) via medical_engine/dncnn.py
        from medical_engine.dncnn import denoise as dncnn_denoise
        lvl = dncnn_level or "auto"
        if ":" in engine:
            lvl = engine.split(":", 1)[1]
        try:
            denoised_img, dncnn_info = dncnn_denoise(img_uint8, level=lvl)
            engine_label = dncnn_info.get("method", "DnCNN (pretrained)")
            infer_time_ms = float(dncnn_info.get("infer_ms", 0.0))
        except Exception as e:
            err_res = {
                "ok": False,
                "status": "error",
                "error": f"{type(e).__name__}: {str(e)}",
                "message": f"DnCNN error: {str(e)}"
            }
            print(json.dumps(err_res))
            sys.exit(1)
    else:
        # Default: Bilateral Filter (Tomasi & Manduchi 1998)
        # Mengukur murni pemanggilan cv2.bilateralFilter
        d_val = 5 if sig_s <= 35 else 7
        t_inf_start = time.perf_counter()
        denoised_img = cv2.bilateralFilter(img_uint8, d=d_val, sigmaColor=sig_c, sigmaSpace=sig_s)
        infer_time_ms = round((time.perf_counter() - t_inf_start) * 1000, 1)
        engine_label = "Bilateral Filter (Tomasi & Manduchi 1998)"

    # 3. Hitung Peta Residu (Difference Map: |Raw - Denoised|)
    residual_map = cv2.absdiff(img_uint8, denoised_img)
    # Tingkatkan kontras residu agar dokter bisa memverifikasi bahwa hanya noise yang terangkat
    residual_enhanced = cv2.normalize(residual_map, None, 0, 255, cv2.NORM_MINMAX)

    # 4. Simpan Output Denoised
    dir_name = os.path.dirname(output_path)
    if dir_name:
        os.makedirs(dir_name, exist_ok=True)
    cv2.imwrite(output_path, denoised_img)

    # Simpan Peta Residu jika diminta
    if residual_path:
        cv2.imwrite(residual_path, residual_enhanced)

    # 5. Hitung Metrik & Waktu Proses
    proc_time_ms = round((time.time() - start_time) * 1000, 1)
    metrics = calculate_metrics(img_uint8, denoised_img)
    metrics["inference_time_ms"] = infer_time_ms
    metrics["total_process_ms"] = proc_time_ms
    metrics["infer_ms"] = infer_time_ms
    metrics["total_ms"] = proc_time_ms
    metrics["processing_time_ms"] = infer_time_ms

    result = {
        "ok": True,
        "status": "success",
        "engine": engine_label,
        "method_name": engine_label,
        "level": dncnn_info.get("level") if "dncnn_info" in locals() else None,
        "level_mode": dncnn_info.get("level_mode") if "dncnn_info" in locals() else None,
        "device": dncnn_info.get("device") if "dncnn_info" in locals() else "cpu",
        "gpu": dncnn_info.get("gpu") if "dncnn_info" in locals() else None,
        "infer_ms": infer_time_ms,
        "total_ms": proc_time_ms,
        "load_ms": dncnn_info.get("load_ms") if "dncnn_info" in locals() else None,
        "sigma_est": dncnn_info.get("sigma_est") if "dncnn_info" in locals() else None,
        "shape": dncnn_info.get("shape") if "dncnn_info" in locals() else list(img_uint8.shape),
        "parameters": {
            "sigma_color": int(sigma_color),
            "sigma_space": int(sigma_space),
            "engine_id": engine,
            "level": dncnn_info.get("level") if "dncnn_info" in locals() else None
        },
        "metrics": metrics
    }

    print(json.dumps(result))

if __name__ == "__main__":
    input_file = sys.argv[1] if len(sys.argv) > 1 else "sample_toraks.dcm"
    output_file = sys.argv[2] if len(sys.argv) > 2 else "denoised_toraks.jpg"
    engine_arg = sys.argv[3] if len(sys.argv) > 3 else "bilateral"
    sigma_col = float(sys.argv[4]) if len(sys.argv) > 4 else 75.0
    sigma_sp = float(sys.argv[5]) if len(sys.argv) > 5 else 75.0
    res_file = sys.argv[6] if len(sys.argv) > 6 else None
    level_arg = sys.argv[7] if len(sys.argv) > 7 else "auto"

    run_denoising(input_file, output_file, engine_arg, sigma_col, sigma_sp, res_file, level_arg)
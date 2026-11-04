"""
medical_engine/segmentation.py
Kategori 2: Anatomical Segmentation (Segmentasi Anatomi & Kontur)
Modul untuk segmentasi struktur anatomi (tulang korteks, lapangan paru, dsb.)
menggunakan isolasi kontur anatomis dan deep learning / morfologi terarah untuk
mengeliminasi artefak kotak pembatas, kebocoran latar belakang udara, dan noise jaringan lunak.

PENTING:
- Menggunakan istilah jujur: "Segmentasi Struktur Tulang" atau "Penonjolan Kontur Tulang".
- TIDAK membuat klaim deteksi fraktur tanpa evaluasi klinis tervalidasi.
"""

import cv2
import numpy as np

# Cek ketersediaan AI Bone Suppression Engine untuk segmentasi tulang presisi tinggi
try:
    from .bone_suppression import BoneSuppressionEngine
    AI_BONE_AVAILABLE = True
except (ImportError, Exception):
    try:
        from medical_engine.bone_suppression import BoneSuppressionEngine
        AI_BONE_AVAILABLE = True
    except (ImportError, Exception):
        BoneSuppressionEngine = None
        AI_BONE_AVAILABLE = False


def _extract_body_mask(gray, max_dim=512):
    """
    Ekstraksi masker biner kontur tubuh pasien untuk memisahkan
    tubuh dari udara luar/background hitam secara presisi.
    """
    h, w = gray.shape
    scale = max_dim / max(h, w) if max(h, w) > max_dim else 1.0
    sw, sh = max(1, int(w * scale)), max(1, int(h * scale))
    small = cv2.resize(gray, (sw, sh), interpolation=cv2.INTER_AREA)

    blur_small = cv2.GaussianBlur(small, (7, 7), 0)
    thresh_val, _ = cv2.threshold(blur_small, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)
    # Ambang batas adaptif konservatif untuk menjaga kulit pasien tidak terpotong
    body_thresh = max(12, int(thresh_val * 0.35))
    _, body_bin = cv2.threshold(blur_small, body_thresh, 255, cv2.THRESH_BINARY)

    contours, _ = cv2.findContours(body_bin, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    body_mask_small = np.zeros_like(small)
    if contours:
        c_max = max(contours, key=cv2.contourArea)
        if cv2.contourArea(c_max) > (sw * sh * 0.10):
            cv2.drawContours(body_mask_small, [c_max], -1, 255, -1)
            kernel_fill = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (21, 21))
            body_mask_small = cv2.morphologyEx(body_mask_small, cv2.MORPH_CLOSE, kernel_fill)
        else:
            body_mask_small[:] = 255
    else:
        body_mask_small[:] = 255

    body_mask = cv2.resize(body_mask_small, (w, h), interpolation=cv2.INTER_NEAREST)
    return body_mask, thresh_val


def _extract_lung_mask_data_driven(gray):
    """
    Ekstraksi masker lapangan paru bilateral alami berbasis anatomi toraks
    tanpa elips buatan. Menghasilkan kontur pleura organik yang mencapai
    sudut kostofrenikus dan kubah hemidiafragma secara presisi.
    """
    h, w = gray.shape
    scale = 512.0 / max(h, w)
    sw, sh = int(w * scale), int(h * scale)
    small = cv2.resize(gray, (sw, sh), interpolation=cv2.INTER_AREA)

    body_mask, otsu_val = _extract_body_mask(small, max_dim=512)
    body_inner = cv2.erode(body_mask, cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (max(5, int(sw * 0.035)), max(5, int(sh * 0.035)))))

    mid_x = int(sw * 0.5)

    # Koridor toraks konikal anatomis mengikuti pelebaran rongga dada ke arah kaudal
    roi_thorax = np.zeros_like(small)
    r_center = int(sw * 0.30)
    l_center = int(sw * 0.70)

    for y in range(int(sh * 0.06), int(sh * 0.82)):
        factor = (y - sh * 0.06) / (sh * 0.82 - sh * 0.06)
        half_w = int(sw * (0.24 + 0.18 * factor))
        x_start = max(0, mid_x - half_w)
        x_end = min(sw, mid_x + half_w)
        for x in range(x_start, x_end):
            if y < sh * 0.14:
                # Kubah apeks paru melengkung alami (menghilangkan garis datar atas)
                dist = abs(x - r_center) / (sw * 0.13) if x < mid_x else abs(x - l_center) / (sw * 0.13)
                if y >= int(sh * (0.07 + 0.06 * (dist ** 2))):
                    roi_thorax[y, x] = 255
            else:
                # Proteksi rongga subdiafragmatika medial kiri (eliminasi gelembung fundus gaster / magenblase)
                if x > mid_x and x < mid_x + int(sw * 0.22) and y > int(sh * 0.66):
                    continue
                roi_thorax[y, x] = 255

    roi_thorax = cv2.bitwise_and(roi_thorax, body_inner)

    roi_px = small[roi_thorax > 0]
    t_lung = np.percentile(roi_px, 52) if len(roi_px) > 0 else otsu_val
    raw_air = ((small < t_lung) & (roi_thorax > 0)).astype(np.uint8) * 255

    # Pemisah kolumna vertebra / mediastinum
    spine_w = max(4, int(sw * 0.035))
    raw_air[:, max(0, mid_x - spine_w) : min(sw, mid_x + spine_w)] = 0

    # Isolasi 2 komponen terbesar (paru kanan & paru kiri)
    n, labels, stats, centroids = cv2.connectedComponentsWithStats(raw_air)
    right_idx, left_idx = None, None
    max_r, max_l = 0, 0
    for i in range(1, n):
        area = stats[i, cv2.CC_STAT_AREA]
        cx, cy = centroids[i]
        if cx < mid_x and area > max_r:
            max_r = area
            right_idx = i
        elif cx > mid_x and area > max_l:
            max_l = area
            left_idx = i

    solid_lungs_small = np.zeros_like(small)
    for idx in [right_idx, left_idx]:
        if idx is not None:
            lobe_mask = (labels == idx).astype(np.uint8) * 255
            # Menghubungkan celah bayangan iga melintang secara lokal
            k_close = cv2.getStructuringElement(cv2.MORPH_RECT, (11, 21))
            lobe_closed = cv2.morphologyEx(lobe_mask, cv2.MORPH_CLOSE, k_close)
            cnts, _ = cv2.findContours(lobe_closed, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
            if cnts:
                # Mengisi parenkim paru secara padat tanpa gerigi iga
                cv2.drawContours(solid_lungs_small, [max(cnts, key=cv2.contourArea)], -1, 255, -1)

    # Smoothing kurvatura pleura dan diafragma organik
    k_smooth = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (15, 15))
    solid_lungs_small = cv2.morphologyEx(solid_lungs_small, cv2.MORPH_CLOSE, k_smooth)

    blurred = cv2.GaussianBlur(solid_lungs_small, (15, 15), 0)
    _, solid_lungs_smooth = cv2.threshold(blurred, 127, 255, cv2.THRESH_BINARY)

    full_lung_mask = cv2.resize(solid_lungs_smooth, (w, h), interpolation=cv2.INTER_LINEAR)
    full_lung_mask = (full_lung_mask > 127).astype(np.uint8) * 255
    return full_lung_mask, solid_lungs_smooth, otsu_val


def segment_bone_cortex(img_uint8, overlay_color=(0, 215, 255), alpha=0.35):
    """
    Segmentasi Struktur Korteks & Densitas Tulang.
    Mengisolasi area tulang berdensitas tinggi (iga, klavikula, kolumna vertebra)
    secara data-driven berbasis dekomposisi deep learning dan ridge trabekula,
    tanpa artefak pil silindris buatan atau vest poligonal.
    
    Output:
    - overlay_bgr: Citra 3-channel dengan highlight warna transparan pada struktur tulang.
    - mask: Masker biner (0 = jaringan lunak/background, 255 = struktur tulang).
    - info: Metadata teknis metode.
    """
    if len(img_uint8.shape) == 3:
        gray = cv2.cvtColor(img_uint8, cv2.COLOR_BGR2GRAY)
    else:
        gray = img_uint8.copy()

    h, w = gray.shape
    bone_mask = None
    engine_used = "Anatomical Morphology (Top-Hat Ridge)"

    # Ekstraksi masker lapangan paru alami untuk memandu batas sangkar iga
    _, small_lung_mask, otsu_val = _extract_lung_mask_data_driven(gray)

    scale = 512.0 / max(h, w)
    sw, sh = int(w * scale), int(h * scale)
    small_gray = cv2.resize(gray, (sw, sh), interpolation=cv2.INTER_AREA)
    body_mask, _ = _extract_body_mask(small_gray, max_dim=512)
    body_inner = cv2.erode(body_mask, cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (max(5, int(sw * 0.035)), max(5, int(sh * 0.035)))))

    if AI_BONE_AVAILABLE and BoneSuppressionEngine is not None:
        try:
            engine = BoneSuppressionEngine('commercial')
            suppressed, _ = engine.run_inference(gray)
            if suppressed is not None:
                diff = cv2.subtract(gray, suppressed)
                small_diff = cv2.resize(diff, (sw, sh), interpolation=cv2.INTER_AREA)

                # Filter terarah horizontal untuk mengeliminasi lipatan kulit vertikal aksila
                k_horiz = cv2.getStructuringElement(cv2.MORPH_RECT, (11, 3))
                diff_clean = cv2.morphologyEx(small_diff, cv2.MORPH_OPEN, k_horiz)

                # Ridge tophat
                clahe = cv2.createCLAHE(clipLimit=2.2, tileGridSize=(8, 8))
                small_clahe = clahe.apply(small_gray)
                k_tophat = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (9, 9))
                tophat = cv2.morphologyEx(small_clahe, cv2.MORPH_TOPHAT, k_tophat)

                norm_diff = cv2.normalize(diff_clean, None, 0, 255, cv2.NORM_MINMAX)
                norm_tophat = cv2.normalize(tophat, None, 0, 255, cv2.NORM_MINMAX)
                bone_signal = cv2.addWeighted(norm_diff, 0.65, norm_tophat, 0.35, 0)

                # Sangkar tulang toraks dipandu secara adaptif dari kurvatura paru pasien:
                # 1. Dilatasi paru ke lateral secara organik menggunakan kernel elips
                k_rib_cage = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (int(sw * 0.09), int(sh * 0.03)))
                rib_cage_zone = cv2.dilate(small_lung_mask, k_rib_cage)
                # 2. Jembatan melintasi mediastinum di bawah incisura jugularis (di bawah sh * 0.12)
                k_bridge_mid = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (int(sw * 0.25), int(sh * 0.03)))
                mid_body = np.zeros_like(rib_cage_zone)
                mid_body[int(sh * 0.12):, :] = rib_cage_zone[int(sh * 0.12):, :]
                mid_bridged = cv2.morphologyEx(mid_body, cv2.MORPH_CLOSE, k_bridge_mid)
                rib_cage_zone[int(sh * 0.12):, :] = mid_bridged[int(sh * 0.12):, :]
                # 3. Ekspansi lateral di sepanjang arkus klavikula
                k_clavicle = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (int(sw * 0.12), int(sh * 0.02)))
                clavicle_zone = cv2.dilate(rib_cage_zone, k_clavicle)

                bone_roi = cv2.bitwise_and(clavicle_zone, body_inner)
                bone_masked = cv2.bitwise_and(bone_signal, bone_signal, mask=bone_roi)

                roi_vals = bone_masked[bone_roi > 0]
                t_bone = np.percentile(roi_vals, 65) if len(roi_vals) > 0 else 50
                _, bone_bin = cv2.threshold(bone_masked, t_bone, 255, cv2.THRESH_BINARY)

                bone_bin = cv2.morphologyEx(bone_bin, cv2.MORPH_CLOSE, cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (5, 5)))
                bone_bin = cv2.morphologyEx(bone_bin, cv2.MORPH_OPEN, cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (3, 3)))

                num_labels, labels, stats, _ = cv2.connectedComponentsWithStats(bone_bin)
                clean_bone_small = np.zeros_like(bone_bin)
                for i in range(1, num_labels):
                    if stats[i, cv2.CC_STAT_AREA] >= 30:
                        clean_bone_small[labels == i] = 255

                bone_mask = cv2.resize(clean_bone_small, (w, h), interpolation=cv2.INTER_LINEAR)
                bone_mask = (bone_mask > 127).astype(np.uint8) * 255
                engine_used = "Deep Learning Bone Decomposition"
        except Exception:
            bone_mask = None

    if bone_mask is None:
        clahe = cv2.createCLAHE(clipLimit=2.5, tileGridSize=(8, 8))
        small_clahe = clahe.apply(small_gray)
        k_tophat = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (9, 9))
        tophat = cv2.morphologyEx(small_clahe, cv2.MORPH_TOPHAT, k_tophat)

        k_rib_cage = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (int(sw * 0.09), int(sh * 0.03)))
        rib_cage_zone = cv2.dilate(small_lung_mask, k_rib_cage)
        k_bridge_mid = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (int(sw * 0.25), int(sh * 0.03)))
        mid_body = np.zeros_like(rib_cage_zone)
        mid_body[int(sh * 0.12):, :] = rib_cage_zone[int(sh * 0.12):, :]
        mid_bridged = cv2.morphologyEx(mid_body, cv2.MORPH_CLOSE, k_bridge_mid)
        rib_cage_zone[int(sh * 0.12):, :] = mid_bridged[int(sh * 0.12):, :]
        k_clavicle = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (int(sw * 0.12), int(sh * 0.02)))
        clavicle_zone = cv2.dilate(rib_cage_zone, k_clavicle)

        bone_roi = cv2.bitwise_and(clavicle_zone, body_inner)
        bone_masked = cv2.bitwise_and(tophat, tophat, mask=bone_roi)

        roi_vals = bone_masked[bone_roi > 0]
        t_bone = np.percentile(roi_vals, 75) if len(roi_vals) > 0 else 30
        _, bone_bin = cv2.threshold(bone_masked, t_bone, 255, cv2.THRESH_BINARY)

        bone_bin = cv2.morphologyEx(bone_bin, cv2.MORPH_CLOSE, cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (5, 5)))
        num_labels, labels, stats, _ = cv2.connectedComponentsWithStats(bone_bin)
        clean_bone_small = np.zeros_like(bone_bin)
        for i in range(1, num_labels):
            if stats[i, cv2.CC_STAT_AREA] >= 30:
                clean_bone_small[labels == i] = 255

        bone_mask = cv2.resize(clean_bone_small, (w, h), interpolation=cv2.INTER_LINEAR)
        bone_mask = (bone_mask > 127).astype(np.uint8) * 255

    # 3. Alpha blending pada area masker tulang
    gray_bgr = cv2.cvtColor(gray, cv2.COLOR_GRAY2BGR)
    color_layer = np.zeros_like(gray_bgr)
    color_layer[bone_mask > 0] = overlay_color

    overlay_bgr = gray_bgr.copy()
    bone_pixels = (bone_mask > 0)
    overlay_bgr[bone_pixels] = cv2.addWeighted(
        gray_bgr[bone_pixels], 1.0 - alpha,
        color_layer[bone_pixels], alpha,
        0
    )

    # Garis tepi tipis agar batas anatomis tegas
    contours, _ = cv2.findContours(bone_mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    cv2.drawContours(overlay_bgr, contours, -1, overlay_color, 1)

    total_px = gray.size
    bone_px = int(np.count_nonzero(bone_mask))
    bone_ratio = round((bone_px / total_px) * 100, 2)

    info = {
        'method_name': f'Segmentasi Struktur Tulang ({engine_used})',
        'otsu_threshold_value': float(otsu_val),
        'bone_pixel_coverage_pct': f"{bone_ratio}%",
        'overlay_color_bgr': list(overlay_color),
        'disclaimer': 'Masker kontur segmentasi untuk lokalisasi visual struktur bertulang. Tidak mengklaim deteksi patologi fraktur otomatis.'
    }
    return overlay_bgr, bone_mask, info


def segment_lung_fields(img_uint8, overlay_color=(255, 180, 0), alpha=0.30):
    """
    Segmentasi Lapangan Paru (Thorax Bilateral Lung Fields).
    Mengisolasi area parenkim paru bilateral secara anatomis, solid, dan mulus
    dengan kurvatura kubah apeks dan diafragma alami tanpa gerigi iga atau artefak lambung.
    """
    if len(img_uint8.shape) == 3:
        gray = cv2.cvtColor(img_uint8, cv2.COLOR_BGR2GRAY)
    else:
        gray = img_uint8.copy()

    lung_mask, _, otsu_val = _extract_lung_mask_data_driven(gray)

    # Alpha blending overlay
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

    # Garis tepi tebal 2px rapi mengikuti pleura dan diafragma
    contours, _ = cv2.findContours(lung_mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    cv2.drawContours(overlay_bgr, contours, -1, overlay_color, 2)

    total_px = gray.size
    lung_px = int(np.count_nonzero(lung_mask))
    lung_ratio = round((lung_px / total_px) * 100, 2)

    info = {
        'method_name': 'Segmentasi Lapangan Paru (Bilateral Lung Field Masking)',
        'lung_pixel_coverage_pct': f"{lung_ratio}%",
        'disclaimer': 'Segmentasi batas lapangan paru anatomis konvensional.'
    }
    return overlay_bgr, lung_mask, info

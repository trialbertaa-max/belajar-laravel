# -*- coding: utf-8 -*-
"""
dncnn.py - Inferensi DnCNN untuk Web-Based DICOM Workstation (folder medical_engine).

Arsitektur  : DnCNN 17 lapisan, residual learning (Zhang et al., 2017, IEEE TIP 26(7), 3142-3155).
Bobot awal  : model grayscale non-blind sigma = 15, 25, 50 dari repositori KAIR (Kai Zhang),
              https://github.com/cszn/KAIR/releases/tag/v1.0 . Batch Normalization pada bobot
              rilis ini sudah digabung (merged) ke lapisan konvolusi, sehingga secara matematis
              setara dengan arsitektur Conv + BN + ReLU pada saat inferensi.
Versi       : 1.1 (10 Nov 2026) - pemanasan GPU sebelum pengukuran infer_ms (kolom baru "warmup_ms").
Status      : PRETRAINED pada citra natural, BELUM di-fine-tune pada radiografi toraks.
              Tampilkan sebagai "DnCNN (pretrained)" sampai bobot hasil fine-tuning VinDr-CXR tersedia.

Masukan     : citra grayscale hasil dicom_io.py (windowing VOI LUT -> uint8 [0, 255]) atau float [0, 1].
Keluaran    : citra terestorasi dengan tipe data yang sama (uint8 -> uint8, float -> float [0, 1]).

Pemakaian (dipanggil Laravel sebagai subprocess, sama seperti skrip filter lain):
    python dncnn.py --download                      # unduh 3 bobot resmi + cek SHA-256 (sekali saja)
    python dncnn.py --selftest                      # cek bobot, GPU, dan kinerja pada citra sintetis
    python dncnn.py --input in.png --output out.png --level 25
    python dncnn.py --input in.png --output out.png --level auto   # pilih model dari estimasi derau

Baris terakhir stdout selalu satu objek JSON, misalnya:
    {"ok": true, "method": "DnCNN (pretrained, sigma=25)", "level": 25, "sigma_est": 4.1,
     "device": "cuda", "load_ms": 35.2, "infer_ms": 812.4, "shape": [2999, 2999]}
"infer_ms" (dengan sinkronisasi GPU) dapat dicatat sebagai latensi inferensi (Subbab 3.13).
Inisialisasi cuDNN/kernel GPU satu kali per proses dicatat terpisah sebagai "warmup_ms".

Dapat juga diimpor dari pipeline.py:
    from medical_engine.dncnn import denoise
    out, info = denoise(img_uint8, level=25)
"""
import argparse
import hashlib
import json
import math
import os
import sys
import time

import numpy as np

try:
    import torch
    import torch.nn as nn
except ImportError:  # dilaporkan dengan rapi di main()
    torch = None

MODEL_LEVELS = (15, 25, 50)
WEIGHTS_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'weights')
RELEASE_URL = 'https://github.com/cszn/KAIR/releases/download/v1.0/dncnn_{}.pth'
SHA256 = {
    15: 'd1f48a581f42bd932de630a13e0b776ace33f6a24efa5572c112028f632a963f',
    25: '0451a70de9b672ae037270498fbb1c17a1c1c4403785df586ff65df5b858e5b0',
    50: '83c11202a88e7b238d08107060c909cb3e692f8177d3470d26d3f1a7b3475a83',
}
RECEPTIVE_RADIUS = 17          # 17 konvolusi 3x3 -> medan reseptif 35 x 35 piksel
TILE_OVERLAP = 32              # > RECEPTIVE_RADIUS, sehingga hasil per-ubin identik dengan citra utuh


# ----------------------------------------------------------------------------- model
if torch is not None:
    class DnCNN(nn.Module):
        """Tata letak lapisan identik dengan KAIR models/network_dncnn.py agar kunci state_dict cocok.
        act_mode='R'  : tanpa BN (BN sudah digabung) - dipakai bobot rilis resmi.
        act_mode='BR' : Conv + BN + ReLU - dipakai saat pelatihan/fine-tuning."""

        def __init__(self, in_nc=1, out_nc=1, nc=64, nb=17, act_mode='R'):
            super().__init__()
            layers = [nn.Conv2d(in_nc, nc, 3, padding=1, bias=True), nn.ReLU(inplace=True)]
            for _ in range(nb - 2):
                layers.append(nn.Conv2d(nc, nc, 3, padding=1, bias=True))
                if 'B' in act_mode:
                    layers.append(nn.BatchNorm2d(nc, momentum=0.9, eps=1e-4, affine=True))
                layers.append(nn.ReLU(inplace=True))
            layers.append(nn.Conv2d(nc, out_nc, 3, padding=1, bias=True))
            self.model = nn.Sequential(*layers)

        def forward(self, x):
            return x - self.model(x)          # residual learning: keluaran jaringan = estimasi derau


def _require_torch():
    if torch is None:
        raise RuntimeError('PyTorch tidak ditemukan pada interpreter Python ini.')


def weight_path(level, weights_dir=WEIGHTS_DIR):
    return os.path.join(weights_dir, 'dncnn_{}.pth'.format(level))


def sha256_of(path):
    h = hashlib.sha256()
    with open(path, 'rb') as f:
        for chunk in iter(lambda: f.read(1 << 20), b''):
            h.update(chunk)
    return h.hexdigest()


def load_model(path, device):
    """Muat bobot DnCNN (rilis resmi maupun hasil fine-tuning sendiri) dan bangun arsitektur yang cocok."""
    _require_torch()
    sd = torch.load(path, map_location='cpu', weights_only=True)
    if isinstance(sd, dict):
        for key in ('params', 'state_dict', 'model_state_dict'):
            if key in sd and isinstance(sd[key], dict):
                sd = sd[key]
                break
    sd = {(k[7:] if k.startswith('module.') else k): v for k, v in sd.items()}
    conv_idx = sorted(int(k.split('.')[1]) for k, v in sd.items()
                      if k.startswith('model.') and k.endswith('.weight') and v.dim() == 4)
    if not conv_idx:
        raise ValueError('Berkas bobot bukan state_dict DnCNN: {}'.format(path))
    first, last = sd['model.{}.weight'.format(conv_idx[0])], sd['model.{}.weight'.format(conv_idx[-1])]
    has_bn = any(k.endswith('running_mean') for k in sd)
    net = DnCNN(in_nc=first.shape[1], out_nc=last.shape[0], nc=first.shape[0], nb=len(conv_idx),
                act_mode='BR' if has_bn else 'R')
    net.load_state_dict(sd, strict=True)
    return net.eval().to(device)


_CACHE = {}
_WARM = set()


def get_model(level, device, weights_dir=WEIGHTS_DIR):
    key = (level, str(device), weights_dir)
    if key not in _CACHE:
        path = weight_path(level, weights_dir)
        if not os.path.isfile(path):
            raise FileNotFoundError('Bobot tidak ditemukan: {} (jalankan: python dncnn.py --download)'.format(path))
        _CACHE[key] = load_model(path, device)
    return _CACHE[key]


# ----------------------------------------------------------------------------- utilitas citra
def to_unit(img):
    """uint8 -> /255, uint16 -> /65535, float harus sudah di [0, 1]."""
    img = np.asarray(img)
    if img.ndim == 3:                                   # RGB/BGR tak sengaja -> grayscale
        img = img[..., :3].mean(axis=2).astype(img.dtype)
    if img.ndim != 2:
        raise ValueError('Citra harus 2 dimensi (grayscale), diterima shape {}'.format(img.shape))
    if img.dtype == np.uint8:
        return img.astype(np.float32) / 255.0
    if img.dtype == np.uint16:
        return img.astype(np.float32) / 65535.0
    out = img.astype(np.float32)
    if out.min() < -1e-6 or out.max() > 1.0 + 1e-6:
        raise ValueError('Citra float harus berada pada rentang [0, 1]')
    return out


def from_unit(x, like_dtype):
    x = np.clip(x, 0.0, 1.0)
    if like_dtype == np.uint8:
        return np.round(x * 255.0).astype(np.uint8)
    if like_dtype == np.uint16:
        return np.round(x * 65535.0).astype(np.uint16)
    return x.astype(np.float32)


def estimate_sigma(img01):
    """Estimasi simpangan baku derau Gaussian (Immerkaer, 1996) pada citra [0, 1]; hasil dalam skala 0-255."""
    I = np.asarray(img01, dtype=np.float64)
    H, W = I.shape
    if H < 3 or W < 3:
        return 0.0
    c = (I[:-2, :-2] - 2 * I[:-2, 1:-1] + I[:-2, 2:]
         - 2 * I[1:-1, :-2] + 4 * I[1:-1, 1:-1] - 2 * I[1:-1, 2:]
         + I[2:, :-2] - 2 * I[2:, 1:-1] + I[2:, 2:])
    sigma = math.sqrt(math.pi / 2.0) * np.abs(c).sum() / (6.0 * (W - 2) * (H - 2))
    return float(sigma * 255.0)


def pick_level(sigma255, available=MODEL_LEVELS):
    return min(available, key=lambda s: abs(s - sigma255))


def run_tiled(fn, img, tile, overlap=TILE_OVERLAP):
    """Jalankan fn (array 2D -> array 2D berukuran sama) per ubin dengan tumpang-tindih.
    Karena overlap > radius medan reseptif, hasilnya identik dengan pemrosesan citra utuh,
    tetapi kebutuhan memori GPU tetap kecil untuk citra 3000 x 3000 piksel."""
    H, W = img.shape
    if tile is None or tile <= 0 or (H <= tile and W <= tile):
        return fn(img)
    step = tile - 2 * overlap
    if step <= 0:
        raise ValueError('tile harus lebih besar dari 2 x overlap ({})'.format(2 * overlap))
    out = np.empty_like(img)
    for y0 in range(0, H, step):
        for x0 in range(0, W, step):
            y1, x1 = min(y0 + step, H), min(x0 + step, W)
            ys, xs = max(y0 - overlap, 0), max(x0 - overlap, 0)
            ye, xe = min(y1 + overlap, H), min(x1 + overlap, W)
            res = fn(img[ys:ye, xs:xe])
            out[y0:y1, x0:x1] = res[y0 - ys:y1 - ys, x0 - xs:x1 - xs]
    return out


def pick_device(name='auto'):
    _require_torch()
    if name == 'auto':
        return torch.device('cuda' if torch.cuda.is_available() else 'cpu')
    if name == 'cuda' and not torch.cuda.is_available():
        raise RuntimeError('CUDA tidak tersedia; gunakan --device cpu')
    return torch.device(name)


# ----------------------------------------------------------------------------- API utama
def denoise(image, level='auto', weights_dir=WEIGHTS_DIR, device='auto', tile=None):
    """Restorasi DnCNN. Mengembalikan (citra_keluaran, info_dict). Tipe data keluaran = tipe data masukan."""
    _require_torch()
    src_dtype = np.asarray(image).dtype
    x = to_unit(image)
    sigma_est = estimate_sigma(x)
    available = [s for s in MODEL_LEVELS if os.path.isfile(weight_path(s, weights_dir))]
    if not available:
        raise FileNotFoundError('Belum ada bobot di {} (jalankan: python dncnn.py --download)'.format(weights_dir))
    lvl = pick_level(sigma_est, available) if str(level) == 'auto' else int(level)
    dev = pick_device(device) if isinstance(device, str) else device
    if tile is None:
        tile = 1024 if dev.type == 'cuda' else 512

    t0 = time.perf_counter()
    net = get_model(lvl, dev, weights_dir)
    load_ms = (time.perf_counter() - t0) * 1000.0

    def fn(patch):
        with torch.inference_mode():
            t = torch.from_numpy(np.ascontiguousarray(patch))[None, None].to(dev)
            return net(t)[0, 0].float().cpu().numpy()

    # Pemanasan: inisialisasi cuDNN dan pemuatan kernel GPU terjadi pada forward pass pertama di setiap
    # proses. Biaya satu kali ini dicatat sebagai warmup_ms dan tidak dihitung sebagai latensi inferensi.
    warmup_ms = 0.0
    wkey = (lvl, str(dev), weights_dir, tile)
    if wkey not in _WARM:
        tw = time.perf_counter()
        wh = x.shape[0] if not tile or tile <= 0 else min(tile, x.shape[0])
        ww = x.shape[1] if not tile or tile <= 0 else min(tile, x.shape[1])
        fn(np.zeros((wh, ww), dtype=np.float32))
        if dev.type == 'cuda':
            torch.cuda.synchronize()
        warmup_ms = (time.perf_counter() - tw) * 1000.0
        _WARM.add(wkey)

    if dev.type == 'cuda':
        torch.cuda.synchronize()
    t1 = time.perf_counter()
    y = run_tiled(fn, x, tile)
    if dev.type == 'cuda':
        torch.cuda.synchronize()
    infer_ms = (time.perf_counter() - t1) * 1000.0

    info = {
        'ok': True,
        'method': 'DnCNN (pretrained, sigma={})'.format(lvl),
        'level': lvl,
        'level_mode': 'auto' if str(level) == 'auto' else 'manual',
        'sigma_est': round(sigma_est, 3),
        'device': dev.type,
        'gpu': torch.cuda.get_device_name(dev) if dev.type == 'cuda' else None,
        'load_ms': round(load_ms, 1),
        'warmup_ms': round(warmup_ms, 1),
        'infer_ms': round(infer_ms, 1),
        'shape': list(x.shape),
        'tile': tile,
    }
    return from_unit(y, src_dtype), info


# ----------------------------------------------------------------------------- I/O (aman untuk path Windows)
def read_image(path):
    if path.lower().endswith('.npy'):
        return np.load(path)
    import cv2
    buf = np.fromfile(path, dtype=np.uint8)
    img = cv2.imdecode(buf, cv2.IMREAD_UNCHANGED)
    if img is None:
        raise ValueError('Gagal membaca citra: {}'.format(path))
    if img.ndim == 3:
        img = cv2.cvtColor(img[..., :3], cv2.COLOR_BGR2GRAY)
    return img


def write_image(path, img):
    folder = os.path.dirname(os.path.abspath(path))
    os.makedirs(folder, exist_ok=True)
    if path.lower().endswith('.npy'):
        np.save(path, img)
        return
    import cv2
    if img.dtype not in (np.uint8, np.uint16):
        img = np.round(np.clip(img, 0, 1) * 255.0).astype(np.uint8)
    ext = os.path.splitext(path)[1] or '.png'
    ok, enc = cv2.imencode(ext, img)
    if not ok:
        raise ValueError('Gagal menulis citra: {}'.format(path))
    enc.tofile(path)


# ----------------------------------------------------------------------------- unduh & uji mandiri
def download(weights_dir=WEIGHTS_DIR):
    import urllib.request
    os.makedirs(weights_dir, exist_ok=True)
    result = {}
    for s in MODEL_LEVELS:
        path = weight_path(s, weights_dir)
        if not (os.path.isfile(path) and sha256_of(path) == SHA256[s]):
            tmp = path + '.part'
            urllib.request.urlretrieve(RELEASE_URL.format(s), tmp)
            if sha256_of(tmp) != SHA256[s]:
                os.remove(tmp)
                raise ValueError('Checksum SHA-256 dncnn_{}.pth tidak cocok; unduhan dibatalkan'.format(s))
            os.replace(tmp, path)
        result['dncnn_{}.pth'.format(s)] = 'ok'
    return {'ok': True, 'weights_dir': weights_dir, 'files': result}


def selftest(weights_dir=WEIGHTS_DIR, device='auto'):
    _require_torch()
    report = {'ok': True, 'torch': torch.__version__, 'cuda_available': torch.cuda.is_available(), 'weights': {}}
    for s in MODEL_LEVELS:
        p = weight_path(s, weights_dir)
        report['weights']['dncnn_{}.pth'.format(s)] = ('ok' if os.path.isfile(p) and sha256_of(p) == SHA256[s]
                                                       else ('checksum berbeda' if os.path.isfile(p) else 'tidak ada'))
    # citra sintetis halus (gradien + lingkaran) 600 x 600 -> menguji juga pemrosesan per-ubin
    yy, xx = np.mgrid[0:600, 0:600].astype(np.float32)
    clean = 0.2 + 0.5 * (xx / 600.0)
    for cy, cx, r, v in ((200, 220, 110, 0.35), (380, 400, 140, 0.25)):
        clean[(yy - cy) ** 2 + (xx - cx) ** 2 < r * r] += v
    clean = np.clip(clean, 0, 1)
    rng = np.random.default_rng(2026)
    tests = {}
    for s in MODEL_LEVELS:
        if report['weights']['dncnn_{}.pth'.format(s)] == 'tidak ada':
            continue
        noisy = np.clip(clean + rng.normal(0, s / 255.0, clean.shape).astype(np.float32), 0, 1)
        den, info = denoise(noisy, level=s, weights_dir=weights_dir, device=device, tile=256)
        psnr_in = 10 * math.log10(1.0 / float(np.mean((noisy - clean) ** 2)))
        psnr_out = 10 * math.log10(1.0 / float(np.mean((den - clean) ** 2)))
        tests['sigma_{}'.format(s)] = {'psnr_noisy': round(psnr_in, 2), 'psnr_dncnn': round(psnr_out, 2),
                                       'gain_db': round(psnr_out - psnr_in, 2), 'device': info['device'],
                                       'infer_ms': info['infer_ms']}
        if psnr_out - psnr_in < 5.0:
            report['ok'] = False
    report['tests'] = tests
    if not tests or any(v != 'ok' for v in report['weights'].values()):
        report['ok'] = False
    return report


# ----------------------------------------------------------------------------- CLI
def main(argv=None):
    ap = argparse.ArgumentParser(description='Inferensi DnCNN (pretrained) untuk medical_engine')
    ap.add_argument('--input', help='citra masukan (.png/.jpg/.tif/.npy), grayscale')
    ap.add_argument('--output', help='path citra keluaran (.png/.npy)')
    ap.add_argument('--level', default='auto', help='15, 25, 50, atau auto (default: auto)')
    ap.add_argument('--weights-dir', default=WEIGHTS_DIR)
    ap.add_argument('--device', default='auto', choices=['auto', 'cuda', 'cpu'])
    ap.add_argument('--tile', type=int, default=None, help='ukuran ubin (default 1024 di GPU, 512 di CPU)')
    ap.add_argument('--download', action='store_true', help='unduh bobot resmi dan verifikasi SHA-256')
    ap.add_argument('--selftest', action='store_true', help='uji bobot, perangkat, dan kinerja')
    a = ap.parse_args(argv)
    try:
        if a.download:
            res = download(a.weights_dir)
        elif a.selftest:
            res = selftest(a.weights_dir, a.device)
        else:
            if not a.input or not a.output:
                ap.error('--input dan --output wajib diisi (atau gunakan --download / --selftest)')
            if a.level != 'auto' and int(a.level) not in MODEL_LEVELS:
                ap.error('--level harus 15, 25, 50, atau auto')
            img = read_image(a.input)
            out, res = denoise(img, level=a.level, weights_dir=a.weights_dir, device=a.device, tile=a.tile)
            write_image(a.output, out)
            res['output'] = a.output
    except Exception as e:  # selalu kembalikan JSON agar mudah dibaca Laravel
        print(json.dumps({'ok': False, 'error': '{}: {}'.format(type(e).__name__, e)}, ensure_ascii=False))
        return 1
    print(json.dumps(res, ensure_ascii=False))
    return 0 if res.get('ok', False) else 1


if __name__ == '__main__':
    sys.exit(main())

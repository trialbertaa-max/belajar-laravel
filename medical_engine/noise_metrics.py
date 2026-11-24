# -*- coding: utf-8 -*-
"""
noise_metrics.py - Estimasi metrik derau tanpa citra acuan bersih (ground truth).
Mengimplementasikan estimator ragam derau cepat Immerkaer (1996).
Hanya membutuhkan numpy dan math (ringan, tanpa dependensi PyTorch).
"""
import math
import numpy as np


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


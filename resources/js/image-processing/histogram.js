/**
 * histogram.js - 256-bin grayscale luminance histogram from RGBA or 16-bit pixel data.
 */

/**
 * @param {Uint8ClampedArray|Uint16Array} data  RGBA flat pixel array or 16-bit intensity array
 * @param {boolean} is16Bit
 * @returns {Uint32Array} 256-bin luminance histogram
 */
export function computeHistogramFromPixels(data, is16Bit = false) {
    const hist = new Uint32Array(256);
    if (!data || data.length === 0) return hist;

    if (is16Bit && data instanceof Uint16Array) {
        for (let i = 0; i < data.length; i++) {
            const val = data[i];
            if (Number.isFinite(val)) {
                const bin = Math.min(255, Math.max(0, val >> 4));
                hist[bin]++;
            }
        }
        return hist;
    }

    for (let i = 0; i < data.length; i += 4) {
        const r = data[i] || 0;
        const g = data[i + 1] || 0;
        const b = data[i + 2] || 0;
        // ITU-R BT.601 integer luma
        const lum = (77 * r + 150 * g + 29 * b) >> 8;
        hist[lum < 0 ? 0 : lum > 255 ? 255 : lum]++;
    }
    return hist;
}


/**
 * spline.js - Monotone cubic Hermite spline (Fritsch-Carlson).
 * Builds a 256-entry (or 4096-entry for 16-bit) LUT from control points [{x,y}].
 */

export function buildLUT(points, size = 256) {
    const SIZE = (size && size > 0) ? size : 256;
    const lut  = (SIZE > 256) ? new Uint16Array(SIZE) : new Uint8Array(SIZE);
    const maxVal = SIZE - 1;

    if (!points || !Array.isArray(points) || points.length === 0) {
        for (let i = 0; i < SIZE; i++) lut[i] = i;
        return lut;
    }

    const pts = points
        .filter(p => p && Number.isFinite(p.x) && Number.isFinite(p.y))
        .sort((a, b) => a.x - b.x)
        .map(p => ({
            x: Math.max(0, Math.min(1, p.x)),
            y: Math.max(0, Math.min(1, p.y))
        }));

    const n = pts.length;
    if (n === 0) {
        for (let i = 0; i < SIZE; i++) lut[i] = i;
        return lut;
    }
    if (n === 1) {
        lut.fill(Math.round(pts[0].y * maxVal));
        return lut;
    }

    // Ensure endpoints at x=0 and x=1 exist
    if (pts[0].x > 0) {
        pts.unshift({ x: 0, y: pts[0].y });
    }
    if (pts[pts.length - 1].x < 1) {
        pts.push({ x: 1, y: pts[pts.length - 1].y });
    }

    const mCount = pts.length;
    const h = [], delta = [];
    for (let i = 0; i < mCount - 1; i++) {
        h[i]     = Math.max(1e-10, pts[i + 1].x - pts[i].x);
        delta[i] = (pts[i + 1].y - pts[i].y) / h[i];
    }

    const m = new Array(mCount);
    m[0] = delta[0];
    m[mCount - 1] = delta[mCount - 2];
    for (let i = 1; i < mCount - 1; i++) {
        m[i] = (delta[i - 1] + delta[i]) / 2;
    }

    for (let i = 0; i < mCount - 1; i++) {
        if (Math.abs(delta[i]) < 1e-10) {
            m[i] = m[i + 1] = 0;
        } else {
            const alpha = m[i] / delta[i];
            const beta  = m[i + 1] / delta[i];
            const sq    = alpha * alpha + beta * beta;
            if (sq > 9) {
                const tau = 3 / Math.sqrt(sq);
                m[i]     = tau * alpha * delta[i];
                m[i + 1] = tau * beta  * delta[i];
            }
        }
    }

    for (let k = 0; k < SIZE; k++) {
        const t = k / maxVal;
        let seg = mCount - 2;
        for (let i = 0; i < mCount - 1; i++) {
            if (t <= pts[i + 1].x + 1e-10) {
                seg = i;
                break;
            }
        }

        const x0 = pts[seg].x, y0 = pts[seg].y;
        const x1 = pts[seg + 1].x, y1 = pts[seg + 1].y;
        const hi = Math.max(1e-10, x1 - x0);
        const tt = Math.max(0, Math.min(1, (t - x0) / hi));
        const t2 = tt * tt, t3 = t2 * tt;

        const val = (2 * t3 - 3 * t2 + 1) * y0 + (t3 - 2 * t2 + tt) * hi * m[seg]
                  + (-2 * t3 + 3 * t2) * y1    + (t3 - t2) * hi * m[seg + 1];

        const safeVal = Number.isFinite(val) ? val : t;
        lut[k] = Math.round(Math.max(0, Math.min(1, safeVal)) * maxVal);
    }

    return lut;
}

export function identityLUT(size = 256) {
    const SIZE = size || 256;
    const lut = (SIZE > 256) ? new Uint16Array(SIZE) : new Uint8Array(SIZE);
    for (let i = 0; i < SIZE; i++) lut[i] = i;
    return lut;
}


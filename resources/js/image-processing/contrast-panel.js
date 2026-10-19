/**
 * contrast-panel.js
 * Hyu PACS - Pemrosesan Kontras (Tone Curve + Histogram + Brightness/Contrast)
 * Includes: Basic view + Advanced Adjustment panel (Amplifier, Latitude, Detail,
 *           Noise Mode, Algorithm, LUT, Style — Fujifilm console style).
 * Mounts into #paneContrast already present in the Blade template.
 * Exposed as window.ContrastPanel for integration with the viewer.
 */

import { PRESETS, PRESET_ORDER } from './curve-presets.js';
import { buildLUT }               from './spline.js';
import { computeHistogramFromPixels } from './histogram.js';

// ─────────────────────────────────────────────
// LUT curves for advanced Algorithm / LUT
// ─────────────────────────────────────────────
const ADV_LUT_CURVES = {
    sigmoid1: (() => {
        const pts = [];
        for (let i = 0; i <= 10; i++) {
            const x = i / 10;
            const y = 1 / (1 + Math.exp(-10 * (x - 0.5)));
            pts.push({ x, y: Math.max(0, Math.min(1, y)) });
        }
        return pts;
    })(),
    sigmoid2: (() => {
        const pts = [];
        for (let i = 0; i <= 10; i++) {
            const x = i / 10;
            const y = 1 / (1 + Math.exp(-6 * (x - 0.5)));
            pts.push({ x, y: Math.max(0, Math.min(1, y)) });
        }
        return pts;
    })(),
    linear: [{ x: 0, y: 0 }, { x: 0.5, y: 0.5 }, { x: 1, y: 1 }],
    gamma: (() => {
        const pts = [];
        for (let i = 0; i <= 8; i++) {
            const x = i / 8;
            pts.push({ x, y: Math.pow(x, 0.45) });
        }
        return pts;
    })(),
    log: (() => {
        const pts = [];
        for (let i = 0; i <= 8; i++) {
            const x = i / 8;
            pts.push({ x, y: x === 0 ? 0 : Math.min(1, Math.log1p(x * 10) / Math.log1p(10)) });
        }
        return pts;
    })(),
};

// ─────────────────────────────────────────────
// State per viewport
// ─────────────────────────────────────────────
function createDefaultSettings() {
    return {
        // Basic
        preset:     'Balanced',
        points:     deepClone(PRESETS['Balanced'].points),
        brightness: 0,
        contrast:   0,
        // Advanced
        amplifier:  20,   // 1–40 (gain multiplier, neutral = 20)
        latitude:   20,   // 1–40 (dynamic range width, neutral = 20)
        detail:     20,   // 1–40 (edge sharpening, neutral = 20)
        noiseMode:  10,   // 1–20 (noise reduction, neutral = 10)
        algorithm:  'symphony',
        lut:        'sigmoid1',
        style:      'Bone',
        // Saved param snapshot for "Save Param" / "Reset Param"
        savedParams: null,
        // View state
        advancedOpen: false,
    };
}

function createDefaultAdvSaved(s) {
    return {
        amplifier:  s.amplifier,
        latitude:   s.latitude,
        detail:     s.detail,
        noiseMode:  s.noiseMode,
        brightness: s.brightness,
        contrast:   s.contrast,
        algorithm:  s.algorithm,
        lut:        s.lut,
        style:      s.style,
    };
}

const vpSettings = {
    1: createDefaultSettings(),
    2: createDefaultSettings()
};

let activeVp  = 1;
let panelOpen = false;

// Cache per viewport: { data: Uint8ClampedArray, width, height }
const vpCache     = { 1: null, 2: null };
const vpBlobUrls  = { 1: null, 2: null };
const vpHistCache = { 1: null, 2: null };

// Processing worker
let worker     = null;
let latestId   = 0;
let pendingRes = null;
let processing = false;

// Curve canvas drag state
const drag = { active: false, idx: -1 };

// RAF scheduling
let rafId = null;

// ─────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────
function deepClone(arr) {
    if (!arr) return [];
    return arr.map(p => ({ x: p.x, y: p.y }));
}

function getActiveSettings() {
    if (!vpSettings[activeVp]) vpSettings[activeVp] = createDefaultSettings();
    return vpSettings[activeVp];
}

function isMonochrome1ForViewport(vpId) {
    if (window.vpState && window.vpState[vpId]) {
        const st = window.vpState[vpId];
        if (st.isMonochrome1 || st.photometric === 'MONOCHROME1') return true;
    }
    return false;
}

function isLinearCurve(points) {
    if (!points || points.length < 2) return true;
    for (let i = 0; i < points.length; i++) {
        if (Math.abs(points[i].x - points[i].y) > 0.015) return false;
    }
    return true;
}

function getWorker() {
    if (!worker) {
        try {
            const workerSrc = new URL('./contrast-worker.js', import.meta.url).href;
            worker = new Worker(workerSrc);
            worker.onmessage = (e) => {
                if (e.data.id === latestId && pendingRes) {
                    const resolve = pendingRes;
                    pendingRes = null;
                    processing = false;
                    resolve(e.data.out);
                }
            };
            worker.onerror = (err) => {
                console.warn('[ContrastPanel] Worker error, falling back to main thread:', err);
                if (pendingRes) {
                    const resolve = pendingRes;
                    pendingRes = null;
                    processing = false;
                    resolve(null);
                }
            };
        } catch (e) {
            console.warn('[ContrastPanel] Web Worker not available:', e);
            worker = null;
        }
    }
    return worker;
}

// ─────────────────────────────────────────────
// Advanced processing helpers
// ─────────────────────────────────────────────

/** Apply Amplifier: multiplicative gain, neutral at amp=20 → factor 1.0 */
function applyAmplifier(data, amp) {
    if (amp === 20) return data; // neutral
    const factor = amp / 20; // 0.05 – 2.0
    for (let i = 0; i < data.length; i += 4) {
        if (data[i + 3] === 0) continue;
        data[i]     = Math.min(255, Math.round(data[i]     * factor));
        data[i + 1] = Math.min(255, Math.round(data[i + 1] * factor));
        data[i + 2] = Math.min(255, Math.round(data[i + 2] * factor));
    }
    return data;
}

/** Apply Latitude: dynamic range compression/expansion (window width), neutral = 20 */
function applyLatitude(data, lat) {
    if (lat === 20) return data;
    // lat 1 = very narrow window (high compression), 40 = full window
    // Map [1,40] → stretch factor [3.0, 0.5]
    const stretch = 1.5 + (20 - lat) * 0.05;
    const offset  = 128;
    for (let i = 0; i < data.length; i += 4) {
        if (data[i + 3] === 0) continue;
        data[i]     = Math.max(0, Math.min(255, Math.round((data[i]     - offset) * stretch + offset)));
        data[i + 1] = Math.max(0, Math.min(255, Math.round((data[i + 1] - offset) * stretch + offset)));
        data[i + 2] = Math.max(0, Math.min(255, Math.round((data[i + 2] - offset) * stretch + offset)));
    }
    return data;
}

/** Apply Detail (unsharp mask approximation using 3x3 box blur delta), neutral = 20 */
function applyDetail(data, width, height, detail) {
    if (detail === 20) return data;
    const amount = (detail - 20) / 40; // -0.5 to +0.5 — positive = sharpen
    if (Math.abs(amount) < 0.01) return data;

    const blurred = boxBlur3x3(data, width, height);
    for (let i = 0; i < data.length; i += 4) {
        if (data[i + 3] === 0) continue;
        for (let c = 0; c < 3; c++) {
            const orig = data[i + c];
            const blur = blurred[i + c];
            data[i + c] = Math.max(0, Math.min(255, Math.round(orig + amount * (orig - blur) * 3)));
        }
    }
    return data;
}

/** Simple 3×3 box blur for detail sharpening */
function boxBlur3x3(src, w, h) {
    const out = new Uint8ClampedArray(src.length);
    for (let y = 0; y < h; y++) {
        for (let x = 0; x < w; x++) {
            let sumR = 0, sumG = 0, sumB = 0, count = 0;
            for (let dy = -1; dy <= 1; dy++) {
                const ny = y + dy;
                if (ny < 0 || ny >= h) continue;
                for (let dx = -1; dx <= 1; dx++) {
                    const nx = x + dx;
                    if (nx < 0 || nx >= w) continue;
                    const idx = (ny * w + nx) * 4;
                    sumR += src[idx];
                    sumG += src[idx + 1];
                    sumB += src[idx + 2];
                    count++;
                }
            }
            const i = (y * w + x) * 4;
            out[i]     = sumR / count;
            out[i + 1] = sumG / count;
            out[i + 2] = sumB / count;
            out[i + 3] = src[i + 3];
        }
    }
    return out;
}

/** Apply Noise Mode: simple averaging blur, neutral = 10, scale 1=none, 20=heavy */
function applyNoiseMode(data, width, height, noiseMode) {
    if (noiseMode <= 1) return data;
    // noiseMode 1 = no blur, 20 = 5-pass blur
    const passes = Math.floor((noiseMode - 1) / 5);  // 0-3 passes
    if (passes === 0) return data;
    for (let p = 0; p < passes; p++) {
        const temp = boxBlur3x3(data, width, height);
        data.set(temp);
    }
    return data;
}

/**
 * Build a curve LUT based on the selected advanced LUT type,
 * combined with the active preset points (basic panel).
 * The advanced LUT modulates/overrides the basic preset.
 */
function buildAdvancedLUT(settings) {
    const baseLUT = buildLUT(settings.points); // from basic preset / customized

    // If lut is 'linear', just use the base curve
    if (settings.lut === 'linear') return baseLUT;

    const advPoints = ADV_LUT_CURVES[settings.lut] || ADV_LUT_CURVES['sigmoid1'];
    const advLUT    = buildLUT(advPoints);

    // Compose: result[i] = advLUT[ baseLUT[i] ]
    const composed = new Uint8Array(256);
    for (let i = 0; i < 256; i++) {
        composed[i] = advLUT[baseLUT[i]];
    }
    return composed;
}

/** Main inline processing with ALL advanced params */
function processInlineAdvanced(data, width, height, settings, isMono1) {
    const brightness = settings.brightness || 0;
    const contrast   = settings.contrast   || 0;

    // Build LUT (advanced mode uses the adv LUT curve)
    const lut     = buildAdvancedLUT(settings);
    const bShift  = Math.round((Math.max(-8, Math.min(8, brightness)) / 8) * 64);
    const cFactor = Math.pow(2, Math.max(-8, Math.min(8, contrast)) / 4);
    const out     = new Uint8ClampedArray(data.length);

    for (let i = 0; i < data.length; i += 4) {
        const a = data[i + 3];
        if (a === 0) { out[i + 3] = 0; continue; }

        const inR = isMono1 ? (255 - data[i])     : data[i];
        const inG = isMono1 ? (255 - data[i + 1]) : data[i + 1];
        const inB = isMono1 ? (255 - data[i + 2]) : data[i + 2];

        let r = lut[inR] + bShift;
        let g = lut[inG] + bShift;
        let b = lut[inB] + bShift;

        r = Math.round((r - 128) * cFactor + 128);
        g = Math.round((g - 128) * cFactor + 128);
        b = Math.round((b - 128) * cFactor + 128);

        out[i]     = r < 0 ? 0 : r > 255 ? 255 : r;
        out[i + 1] = g < 0 ? 0 : g > 255 ? 255 : g;
        out[i + 2] = b < 0 ? 0 : b > 255 ? 255 : b;
        out[i + 3] = a;
    }

    return out;
}

/** Basic inline processing (no advanced params) */
function processInline(data, lut, brightness, contrast, isMono1) {
    const bShift  = Math.round((Math.max(-8, Math.min(8, brightness || 0)) / 8) * 64);
    const cFactor = Math.pow(2, Math.max(-8, Math.min(8, contrast || 0)) / 4);
    const out     = new Uint8ClampedArray(data.length);
    for (let i = 0; i < data.length; i += 4) {
        const a = data[i + 3];
        if (a === 0) { out[i + 3] = 0; continue; }
        const inR = isMono1 ? (255 - data[i])     : data[i];
        const inG = isMono1 ? (255 - data[i + 1]) : data[i + 1];
        const inB = isMono1 ? (255 - data[i + 2]) : data[i + 2];
        let r = lut[inR] + bShift;
        let g = lut[inG] + bShift;
        let b = lut[inB] + bShift;
        r = Math.round((r - 128) * cFactor + 128);
        g = Math.round((g - 128) * cFactor + 128);
        b = Math.round((b - 128) * cFactor + 128);
        out[i]     = r < 0 ? 0 : r > 255 ? 255 : r;
        out[i + 1] = g < 0 ? 0 : g > 255 ? 255 : g;
        out[i + 2] = b < 0 ? 0 : b > 255 ? 255 : b;
        out[i + 3] = a;
    }
    return out;
}

// ─────────────────────────────────────────────
// Pixel capture
// ─────────────────────────────────────────────
async function captureRawPixels(vpId) {
    const rawSrc = (window.vpState && window.vpState[vpId])
        ? (window.vpState[vpId].denoisedSrc || window.vpState[vpId].rawSrc)
        : null;

    const img = document.getElementById('imgVp' + vpId);
    if (!img) return;

    let sourceImg = img;

    if (img.src && img.src.startsWith('blob:') && rawSrc) {
        const cleanImg = new Image();
        cleanImg.crossOrigin = 'anonymous';
        cleanImg.src = rawSrc;
        await new Promise(resolve => {
            if (cleanImg.complete && cleanImg.naturalWidth) return resolve();
            cleanImg.onload = resolve;
            cleanImg.onerror = resolve;
        });
        if (cleanImg.naturalWidth && cleanImg.naturalHeight) {
            sourceImg = cleanImg;
        }
    } else {
        if (!img.complete || !img.naturalWidth) {
            await new Promise(ok => {
                img.addEventListener('load',  ok, { once: true });
                img.addEventListener('error', ok, { once: true });
            });
        }
    }

    const w = sourceImg.naturalWidth;
    const h = sourceImg.naturalHeight;
    if (!w || !h) return;

    try {
        let cvs;
        if (typeof OffscreenCanvas !== 'undefined') {
            cvs = new OffscreenCanvas(w, h);
        } else {
            cvs = document.createElement('canvas');
            cvs.width = w;
            cvs.height = h;
        }
        const ctx = cvs.getContext('2d');
        ctx.drawImage(sourceImg, 0, 0);
        const id = ctx.getImageData(0, 0, w, h);
        vpCache[vpId]     = { data: id.data.slice(), width: w, height: h };
        vpHistCache[vpId] = computeHistogramFromPixels(id.data);
    } catch (err) {
        console.warn('[ContrastPanel] captureRawPixels failed:', err);
    }
}

// ─────────────────────────────────────────────
// Apply processing → replace img.src via blob
// ─────────────────────────────────────────────
async function applyToViewport(vpId) {
    const cache = vpCache[vpId];
    if (!cache) return;

    const current = vpSettings[vpId] || createDefaultSettings();
    const isMono1 = isMonochrome1ForViewport(vpId);
    const isAdv   = current.advancedOpen;

    // If completely identity settings, just restore original
    if (!isAdv
        && current.preset === 'Balanced'
        && current.brightness === 0
        && current.contrast === 0
        && isLinearCurve(current.points)
        && !isMono1) {
        restoreOriginal(vpId);
        return;
    }

    let processed = null;
    const { data, width, height } = cache;

    if (isAdv) {
        // Advanced processing: LUT + brightness/contrast from advanced state,
        // then amplifier, latitude, detail, noise on a mutable copy
        processed = processInlineAdvanced(data, width, height, current, isMono1);

        // Post-LUT spatial filters (on a Uint8ClampedArray, need mutable copy)
        // Apply noise reduction first, then detail enhancement
        const mutable = new Uint8ClampedArray(processed);
        applyNoiseMode(mutable, width, height, current.noiseMode);
        applyDetail(mutable, width, height, current.detail);
        applyAmplifier(mutable, current.amplifier);
        applyLatitude(mutable, current.latitude);
        processed = mutable;
    } else {
        // Basic processing: use worker for large images
        const lut = buildLUT(current.points);
        const wrk = getWorker();
        if (data.length > 1_600_000 && wrk) {
            processing = true;
            const id   = ++latestId;
            const copy = data.slice();
            processed  = await new Promise(res => {
                pendingRes = res;
                wrk.postMessage(
                    { id, pixelData: copy, lut, brightness: current.brightness, contrast: current.contrast, isMonochrome1: isMono1 },
                    [copy.buffer]
                );
            });
        }
        if (!processed) {
            processed = processInline(data, lut, current.brightness, current.contrast, isMono1);
        }
    }

    if (!processed) return;

    const cvs = document.createElement('canvas');
    cvs.width  = width;
    cvs.height = height;
    cvs.getContext('2d').putImageData(new ImageData(processed, width, height), 0, 0);

    cvs.toBlob(blob => {
        if (!blob) return;
        const img = document.getElementById('imgVp' + vpId);
        if (!img) return;
        if (vpBlobUrls[vpId]) URL.revokeObjectURL(vpBlobUrls[vpId]);
        vpBlobUrls[vpId] = URL.createObjectURL(blob);
        img.src = vpBlobUrls[vpId];
    }, 'image/jpeg', 0.95);
}

// ─────────────────────────────────────────────
// RAF scheduler
// ─────────────────────────────────────────────
function scheduleRender() {
    if (rafId) return;
    rafId = requestAnimationFrame(() => {
        rafId = null;
        renderCurveCanvas();
        if (panelOpen) applyToViewport(activeVp);
    });
}

// ─────────────────────────────────────────────
// Restore original image
// ─────────────────────────────────────────────
function restoreOriginal(vpId) {
    const img = document.getElementById('imgVp' + vpId);
    if (!img) return;
    if (vpBlobUrls[vpId]) {
        URL.revokeObjectURL(vpBlobUrls[vpId]);
        vpBlobUrls[vpId] = null;
    }
    const rawSrc = (window.vpState && window.vpState[vpId])
        ? (window.vpState[vpId].denoisedSrc || window.vpState[vpId].rawSrc)
        : null;
    if (rawSrc) img.src = rawSrc;
}

// ─────────────────────────────────────────────
// Curve canvas rendering
// ─────────────────────────────────────────────
function renderCurveCanvas() {
    const canvas = document.getElementById('cpCurveCanvas');
    if (!canvas) return;

    const rect = canvas.getBoundingClientRect();
    if (rect.width > 0 && Math.abs(canvas.width - Math.round(rect.width)) > 1) {
        canvas.width = Math.round(rect.width);
    }

    const ctx = canvas.getContext('2d');
    const W = canvas.width, H = canvas.height;
    const current = getActiveSettings();

    ctx.fillStyle = '#0a1020';
    ctx.fillRect(0, 0, W, H);

    ctx.strokeStyle = 'rgba(255,255,255,0.06)';
    ctx.lineWidth   = 1;
    for (let i = 1; i < 4; i++) {
        const x = (i / 4) * W, y = (i / 4) * H;
        ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, H); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(W, y); ctx.stroke();
    }

    const hist = vpHistCache[activeVp];
    if (hist) {
        let maxVal = 0;
        for (let i = 0; i < 256; i++) if (hist[i] > maxVal) maxVal = hist[i];
        if (maxVal > 0) {
            const bw = W / 256;
            ctx.fillStyle = 'rgba(100,130,160,0.35)';
            for (let i = 0; i < 256; i++) {
                const bh = (hist[i] / maxVal) * H;
                ctx.fillRect(i * bw, H - bh, bw + 0.5, bh);
            }
        }
    } else {
        ctx.fillStyle = 'rgba(148,163,184,0.25)';
        ctx.font = '10px Inter, system-ui, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('Tidak ada data histogram', W / 2, H / 2 - 10);
        ctx.textAlign = 'start';
    }

    ctx.beginPath();
    ctx.strokeStyle = 'rgba(255,255,255,0.12)';
    ctx.lineWidth   = 1;
    ctx.setLineDash([4, 5]);
    ctx.moveTo(0, H); ctx.lineTo(W, 0);
    ctx.stroke(); ctx.setLineDash([]);

    // Draw the effective LUT curve (advanced uses composed LUT)
    const lut = current.advancedOpen ? buildAdvancedLUT(current) : buildLUT(current.points);
    ctx.beginPath();
    ctx.strokeStyle = '#f0f4ff';
    ctx.lineWidth   = 2;
    ctx.shadowColor = 'rgba(56,189,248,0.5)';
    ctx.shadowBlur  = 6;
    for (let i = 0; i < 256; i++) {
        const cx = (i / 255) * W;
        const cy = H - (lut[i] / 255) * H;
        if (i === 0) ctx.moveTo(cx, cy); else ctx.lineTo(cx, cy);
    }
    ctx.stroke();
    ctx.shadowBlur = 0;

    // Control point handles (only in basic / curve-edit mode)
    if (!current.advancedOpen) {
        current.points.forEach((pt, idx) => {
            const cx = pt.x * W;
            const cy = H - pt.y * H;
            const isFixed = (idx === 0 || idx === current.points.length - 1);
            ctx.fillStyle   = drag.active && drag.idx === idx ? '#38bdf8' : '#ffffff';
            ctx.strokeStyle = isFixed ? '#64748b' : '#38bdf8';
            ctx.lineWidth   = 2;
            ctx.shadowColor = '#38bdf8';
            ctx.shadowBlur  = drag.active && drag.idx === idx ? 10 : 4;
            ctx.beginPath();
            ctx.rect(cx - 5, cy - 5, 10, 10);
            ctx.fill(); ctx.stroke();
            ctx.shadowBlur = 0;
        });
    }
}

// ─────────────────────────────────────────────
// Curve canvas drag interaction
// ─────────────────────────────────────────────
function initCurveInteraction() {
    const canvas = document.getElementById('cpCurveCanvas');
    if (!canvas) return;

    function ptFromEvent(e) {
        const r   = canvas.getBoundingClientRect();
        const src = e.touches ? e.touches[0] : e;
        return {
            x: Math.max(0, Math.min(1, (src.clientX - r.left)  / r.width)),
            y: Math.max(0, Math.min(1, 1 - (src.clientY - r.top) / r.height))
        };
    }

    function nearestPoint(pt) {
        const current = getActiveSettings();
        if (current.advancedOpen) return -1; // disable drag in advanced mode
        const W = canvas.width, H = canvas.height;
        let best = -1, bestDist = 18;
        current.points.forEach((p, i) => {
            const dx = (p.x - pt.x) * W;
            const dy = (p.y - pt.y) * H;
            const d  = Math.sqrt(dx * dx + dy * dy);
            if (d < bestDist) { bestDist = d; best = i; }
        });
        return best;
    }

    function onDown(e) {
        if (e.cancelable) e.preventDefault();
        const pt  = ptFromEvent(e);
        const idx = nearestPoint(pt);
        if (idx >= 0) {
            drag.active = true;
            drag.idx    = idx;
            renderCurveCanvas();
        }
    }

    function onMove(e) {
        if (!drag.active) return;
        if (e.cancelable) e.preventDefault();
        const pt = ptFromEvent(e);
        const current = getActiveSettings();
        if (current.advancedOpen) { drag.active = false; return; }
        const n = current.points.length;
        const xMin = drag.idx === 0     ? 0 : current.points[drag.idx - 1].x + 0.02;
        const xMax = drag.idx === n - 1 ? 1 : current.points[drag.idx + 1].x - 0.02;
        if (drag.idx === 0) {
            current.points[0].x = 0;
            current.points[0].y = Math.max(0, Math.min(1, pt.y));
        } else if (drag.idx === n - 1) {
            current.points[n - 1].x = 1;
            current.points[n - 1].y = Math.max(0, Math.min(1, pt.y));
        } else {
            current.points[drag.idx].x = Math.max(xMin, Math.min(xMax, pt.x));
            current.points[drag.idx].y = Math.max(0,    Math.min(1,    pt.y));
        }
        if (current.preset !== 'Customized') setPreset('Customized', false);
        scheduleRender();
    }

    function onUp() {
        if (drag.active) { drag.active = false; drag.idx = -1; renderCurveCanvas(); }
    }

    canvas.addEventListener('mousedown',  onDown, { passive: false });
    canvas.addEventListener('touchstart', onDown, { passive: false });
    window.addEventListener('mousemove',  onMove, { passive: false });
    window.addEventListener('touchmove',  onMove, { passive: false });
    window.addEventListener('mouseup',    onUp);
    window.addEventListener('touchend',   onUp);
}

// ─────────────────────────────────────────────
// Preset management
// ─────────────────────────────────────────────
function setPreset(name, updatePoints = true) {
    const current = getActiveSettings();
    current.preset = name;
    if (updatePoints && PRESETS[name]) {
        current.points = deepClone(PRESETS[name].points);
    }
    syncPresetButtonsUI();
}

function syncPresetButtonsUI() {
    const current = getActiveSettings();
    document.querySelectorAll('.cp-preset-btn').forEach(btn => {
        btn.classList.toggle('cp-preset-active', btn.dataset.preset === current.preset);
    });
}

function mountPresetButtons() {
    const grid = document.getElementById('cpPresetGrid');
    if (!grid) return;
    grid.innerHTML = '';
    PRESET_ORDER.forEach(name => {
        const btn          = document.createElement('button');
        const current      = getActiveSettings();
        btn.className      = 'cp-preset-btn' + (name === current.preset ? ' cp-preset-active' : '');
        btn.dataset.preset = name;
        btn.textContent    = name;
        btn.title          = name + ' preset';
        btn.addEventListener('click', () => { setPreset(name, true); scheduleRender(); });
        grid.appendChild(btn);
    });
}

// ─────────────────────────────────────────────
// Basic sliders
// ─────────────────────────────────────────────
function updateBasicSliderVisual(inputId, valId, value) {
    const input = document.getElementById(inputId);
    const valEl = document.getElementById(valId);
    if (input) {
        input.value = value;
        const pct = Math.max(0, Math.min(100, ((value - (-8)) / 16) * 100));
        input.style.background = `linear-gradient(to right, #0284c7 0%, #0284c7 ${pct}%, #1e293b ${pct}%, #1e293b 100%)`;
    }
    if (valEl) {
        valEl.textContent = value > 0 ? '+' + value : String(value);
    }
}

function updateFujiSliderVisual(inputId, bubbleId, valId, value, min, max, isSigned = false) {
    const input  = document.getElementById(inputId);
    const bubble = document.getElementById(bubbleId);
    const valEl  = valId ? document.getElementById(valId) : null;
    const pct = Math.max(0, Math.min(100, ((value - min) / (max - min)) * 100));

    if (input) {
        input.value = value;
        input.style.setProperty('--pct', pct + '%');
    }
    const label = isSigned && value > 0 ? '+' + value : String(value);
    if (bubble) {
        bubble.textContent = label;
        bubble.style.left = `calc(${pct}% + ${(50 - pct) * 0.14}px)`;
    }
    if (valEl) {
        valEl.textContent = label;
    }
}

function syncSlidersUI() {
    const current = getActiveSettings();
    updateBasicSliderVisual('cpBrightness', 'cpBrightnessVal', current.brightness);
    updateBasicSliderVisual('cpContrast',   'cpContrastVal',   current.contrast);
    syncAdvancedUI();
}

function mountSliders() {
    const bRange = document.getElementById('cpBrightness');
    const bMinus = document.getElementById('cpBrightnessMinus');
    const bPlus  = document.getElementById('cpBrightnessPlus');
    if (bRange) bRange.addEventListener('input', () => {
        const c = getActiveSettings();
        c.brightness = parseFloat(bRange.value) || 0;
        updateBasicSliderVisual('cpBrightness', 'cpBrightnessVal', c.brightness);
        syncAdvancedUI();
        scheduleRender();
    });
    if (bMinus) bMinus.addEventListener('click', () => stepBasic('brightness', -1));
    if (bPlus)  bPlus.addEventListener('click',  () => stepBasic('brightness', +1));

    const cRange = document.getElementById('cpContrast');
    const cMinus = document.getElementById('cpContrastMinus');
    const cPlus  = document.getElementById('cpContrastPlus');
    if (cRange) cRange.addEventListener('input', () => {
        const c = getActiveSettings();
        c.contrast = parseFloat(cRange.value) || 0;
        updateBasicSliderVisual('cpContrast', 'cpContrastVal', c.contrast);
        syncAdvancedUI();
        scheduleRender();
    });
    if (cMinus) cMinus.addEventListener('click', () => stepBasic('contrast', -1));
    if (cPlus)  cPlus.addEventListener('click',  () => stepBasic('contrast', +1));
}

function stepBasic(param, delta) {
    const current = getActiveSettings();
    const newVal  = Math.max(-8, Math.min(8, (current[param] || 0) + delta));
    current[param] = newVal;
    const inputId = param === 'brightness' ? 'cpBrightness' : 'cpContrast';
    const valId   = param === 'brightness' ? 'cpBrightnessVal' : 'cpContrastVal';
    updateBasicSliderVisual(inputId, valId, newVal);
    syncAdvancedUI();
    scheduleRender();
}

// ─────────────────────────────────────────────
// Reset button (basic)
// ─────────────────────────────────────────────
function mountResetButton() {
    const btn = document.getElementById('cpResetBtn');
    if (!btn) return;
    btn.addEventListener('click', () => resetViewport(activeVp));
}

function resetViewport(vpId) {
    vpSettings[vpId] = createDefaultSettings();
    syncPresetButtonsUI();
    syncSlidersUI();
    restoreOriginal(vpId);
    renderCurveCanvas();
}

// ─────────────────────────────────────────────
// Advanced panel: show / return
// ─────────────────────────────────────────────
function showAdvancedPanel() {
    const basicEl = document.getElementById('cpBasicView');
    const advEl   = document.getElementById('cpAdvView');
    if (!basicEl || !advEl) return;
    basicEl.classList.add('cp-basic-hidden');
    advEl.classList.add('cp-adv-visible');
    getActiveSettings().advancedOpen = true;
    syncAdvancedUI();
    renderCurveCanvas();
}

function returnToBasic() {
    const basicEl = document.getElementById('cpBasicView');
    const advEl   = document.getElementById('cpAdvView');
    if (!basicEl || !advEl) return;
    advEl.classList.remove('cp-adv-visible');
    basicEl.classList.remove('cp-basic-hidden');
    getActiveSettings().advancedOpen = false;
    syncSlidersUI();
    renderCurveCanvas();
    scheduleRender();
}

// ─────────────────────────────────────────────
// Advanced panel: mount all controls
// ─────────────────────────────────────────────
function mountAdvancedPanel() {
    // Advance button (opens advanced panel from basic view)
    const advBtn = document.getElementById('cpAdvanceBtn');
    if (advBtn) advBtn.addEventListener('click', showAdvancedPanel);

    // Return button
    const retBtn = document.getElementById('cpAdvReturnBtn');
    if (retBtn) retBtn.addEventListener('click', returnToBasic);

    // Helper to wire an adv slider
    function wireAdvSlider(inputId, bubbleId, minusId, plusId, valId, param, min, max, isSigned = false) {
        const input = document.getElementById(inputId);
        const minus = document.getElementById(minusId);
        const plus  = document.getElementById(plusId);

        if (input) input.addEventListener('input', () => {
            const c = getActiveSettings();
            c[param] = parseInt(input.value, 10);
            updateFujiSliderVisual(inputId, bubbleId, valId, c[param], min, max, isSigned);
            scheduleRender();
        });
        if (minus) minus.addEventListener('click', () => stepAdv(param, -1, min, max, inputId, bubbleId, valId, isSigned));
        if (plus)  plus.addEventListener('click',  () => stepAdv(param, +1, min, max, inputId, bubbleId, valId, isSigned));
    }

    wireAdvSlider('cpAdvAmplifier', 'cpAdvAmplifierBubble', 'cpAdvAmpMinus',   'cpAdvAmpPlus',   'cpAdvAmplifierVal', 'amplifier',  1, 40);
    wireAdvSlider('cpAdvLatitude',  'cpAdvLatitudeBubble',  'cpAdvLatMinus',   'cpAdvLatPlus',   'cpAdvLatitudeVal',  'latitude',   1, 40);
    wireAdvSlider('cpAdvDetail',    'cpAdvDetailBubble',    'cpAdvDetMinus',   'cpAdvDetPlus',   'cpAdvDetailVal',    'detail',     1, 40);
    wireAdvSlider('cpAdvNoiseMode', 'cpAdvNoiseModeBubble', 'cpAdvNoiseMinus', 'cpAdvNoisePlus', 'cpAdvNoiseModeVal', 'noiseMode',  1, 20);

    // Advanced Brightness/Contrast (mirrors basic — they share the same state)
    const abrInput = document.getElementById('cpAdvBrightness');
    const abrMinus = document.getElementById('cpAdvBrightMinus');
    const abrPlus  = document.getElementById('cpAdvBrightPlus');
    if (abrInput) abrInput.addEventListener('input', () => {
        const c = getActiveSettings();
        c.brightness = parseFloat(abrInput.value) || 0;
        updateFujiSliderVisual('cpAdvBrightness', 'cpAdvBrightnessBubble', 'cpAdvBrightnessVal', c.brightness, -8, 8, true);
        updateBasicSliderVisual('cpBrightness', 'cpBrightnessVal', c.brightness);
        scheduleRender();
    });
    if (abrMinus) abrMinus.addEventListener('click', () => stepAdvMirror('brightness', -1));
    if (abrPlus)  abrPlus.addEventListener('click',  () => stepAdvMirror('brightness', +1));

    const acInput = document.getElementById('cpAdvContrast');
    const acMinus = document.getElementById('cpAdvContMinus');
    const acPlus  = document.getElementById('cpAdvContPlus');
    if (acInput) acInput.addEventListener('input', () => {
        const c = getActiveSettings();
        c.contrast = parseFloat(acInput.value) || 0;
        updateFujiSliderVisual('cpAdvContrast', 'cpAdvContrastBubble', 'cpAdvContrastVal', c.contrast, -8, 8, true);
        updateBasicSliderVisual('cpContrast', 'cpContrastVal', c.contrast);
        scheduleRender();
    });
    if (acMinus) acMinus.addEventListener('click', () => stepAdvMirror('contrast', -1));
    if (acPlus)  acPlus.addEventListener('click',  () => stepAdvMirror('contrast', +1));

    // Dropdowns
    const algSel   = document.getElementById('cpAdvAlgorithm');
    const lutSel   = document.getElementById('cpAdvLUT');
    const styleSel = document.getElementById('cpAdvStyle');

    if (algSel) algSel.addEventListener('change', () => {
        getActiveSettings().algorithm = algSel.value;
        scheduleRender();
    });
    if (lutSel) lutSel.addEventListener('change', () => {
        getActiveSettings().lut = lutSel.value;
        scheduleRender();
    });
    if (styleSel) styleSel.addEventListener('change', () => {
        const name = styleSel.value;
        getActiveSettings().style = name;
        if (PRESETS[name]) {
            setPreset(name, true);
        }
        scheduleRender();
    });

    // Reset Param button
    const resetParam = document.getElementById('cpAdvResetParam');
    if (resetParam) resetParam.addEventListener('click', () => {
        const c = getActiveSettings();
        if (c.savedParams) {
            // Restore saved snapshot
            Object.assign(c, c.savedParams);
        } else {
            // Reset to factory defaults
            c.amplifier = 20; c.latitude = 20; c.detail = 20; c.noiseMode = 10;
            c.brightness = 0; c.contrast  = 0;
            c.algorithm  = 'symphony'; c.lut = 'sigmoid1'; c.style = 'Bone';
            setPreset('Bone', true);
        }
        syncAdvancedUI();
        syncSlidersUI();
        scheduleRender();
    });

    // Save Param button
    const saveParam = document.getElementById('cpAdvSaveParam');
    if (saveParam) saveParam.addEventListener('click', () => {
        const c = getActiveSettings();
        c.savedParams = createDefaultAdvSaved(c);
        // Flash confirmation
        const flash = document.getElementById('cpAdvSavedFlash');
        if (flash) {
            flash.style.display = 'block';
            flash.style.animation = 'none';
            requestAnimationFrame(() => {
                flash.style.animation = 'cpFadeFlash 1.5s forwards';
                setTimeout(() => { flash.style.display = 'none'; }, 1600);
            });
        }
    });
}

function stepAdv(param, delta, min, max, inputId, bubbleId, valId, isSigned = false) {
    const c  = getActiveSettings();
    const v  = Math.max(min, Math.min(max, (c[param] || min) + delta));
    c[param] = v;
    updateFujiSliderVisual(inputId, bubbleId, valId, v, min, max, isSigned);
    scheduleRender();
}

function stepAdvMirror(param, delta) {
    const c = getActiveSettings();
    const v = Math.max(-8, Math.min(8, (c[param] || 0) + delta));
    c[param] = v;
    if (param === 'brightness') {
        updateFujiSliderVisual('cpAdvBrightness', 'cpAdvBrightnessBubble', 'cpAdvBrightnessVal', v, -8, 8, true);
        updateBasicSliderVisual('cpBrightness', 'cpBrightnessVal', v);
    } else {
        updateFujiSliderVisual('cpAdvContrast', 'cpAdvContrastBubble', 'cpAdvContrastVal', v, -8, 8, true);
        updateBasicSliderVisual('cpContrast', 'cpContrastVal', v);
    }
    scheduleRender();
}

/** Sync all advanced panel UI controls from state */
function syncAdvancedUI() {
    const c = getActiveSettings();

    updateFujiSliderVisual('cpAdvAmplifier',  'cpAdvAmplifierBubble',  'cpAdvAmplifierVal',  c.amplifier,  1, 40);
    updateFujiSliderVisual('cpAdvLatitude',   'cpAdvLatitudeBubble',   'cpAdvLatitudeVal',   c.latitude,   1, 40);
    updateFujiSliderVisual('cpAdvDetail',     'cpAdvDetailBubble',     'cpAdvDetailVal',     c.detail,     1, 40);
    updateFujiSliderVisual('cpAdvNoiseMode',  'cpAdvNoiseModeBubble',  'cpAdvNoiseModeVal',  c.noiseMode,  1, 20);
    updateFujiSliderVisual('cpAdvBrightness', 'cpAdvBrightnessBubble', 'cpAdvBrightnessVal', c.brightness, -8,  8, true);
    updateFujiSliderVisual('cpAdvContrast',   'cpAdvContrastBubble',   'cpAdvContrastVal',   c.contrast,   -8,  8, true);

    const algSel   = document.getElementById('cpAdvAlgorithm');
    const lutSel   = document.getElementById('cpAdvLUT');
    const styleSel = document.getElementById('cpAdvStyle');
    if (algSel)   algSel.value   = c.algorithm || 'symphony';
    if (lutSel)   lutSel.value   = c.lut       || 'sigmoid1';
    if (styleSel) styleSel.value = c.style      || 'Bone';

    // Sync advanced view visibility
    const basicEl = document.getElementById('cpBasicView');
    const advEl   = document.getElementById('cpAdvView');
    if (basicEl && advEl) {
        if (c.advancedOpen) {
            basicEl.classList.add('cp-basic-hidden');
            advEl.classList.add('cp-adv-visible');
        } else {
            basicEl.classList.remove('cp-basic-hidden');
            advEl.classList.remove('cp-adv-visible');
        }
    }
}

// ─────────────────────────────────────────────
// Public API  (window.ContrastPanel)
// ─────────────────────────────────────────────

async function activate(vpId) {
    if (vpId !== undefined) activeVp = vpId;
    panelOpen = true;

    syncPresetButtonsUI();
    syncSlidersUI();   // also calls syncAdvancedUI

    const cpBadge = document.getElementById('cpTargetBadge');
    if (cpBadge) cpBadge.textContent = activeVp === 1 ? 'VP-A' : 'VP-B';

    if (!vpCache[activeVp]) await captureRawPixels(activeVp);
    renderCurveCanvas();
    scheduleRender();
}

function onImageLoad(vpId) {
    vpCache[vpId]     = null;
    vpHistCache[vpId] = null;
    if (vpBlobUrls[vpId]) {
        URL.revokeObjectURL(vpBlobUrls[vpId]);
        vpBlobUrls[vpId] = null;
    }
    if (panelOpen && vpId === activeVp) {
        captureRawPixels(vpId).then(() => { renderCurveCanvas(); scheduleRender(); });
    }
}

function deactivate() {
    panelOpen = false;
}

function onResetViewer() {
    [1, 2].forEach(id => {
        vpSettings[id] = createDefaultSettings();
        if (vpBlobUrls[id]) { URL.revokeObjectURL(vpBlobUrls[id]); vpBlobUrls[id] = null; }
    });
    syncPresetButtonsUI();
    syncSlidersUI();
    renderCurveCanvas();
}

// ─────────────────────────────────────────────
// Init
// ─────────────────────────────────────────────
function init() {
    mountPresetButtons();
    initCurveInteraction();
    mountSliders();
    mountResetButton();
    mountAdvancedPanel();
    renderCurveCanvas();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

window.ContrastPanel = {
    activate,
    deactivate,
    onImageLoad,
    onResetViewer,
    resetViewport
};

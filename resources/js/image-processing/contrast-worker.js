/**
 * contrast-worker.js
 * Classic Web Worker (no ES module imports).
 * Receives RGBA pixel data + LUT + brightness/contrast + MONOCHROME1 flag.
 *
 * Message in:  { id, pixelData: Uint8ClampedArray, lut: Uint8Array, brightness: -8..8, contrast: -8..8, isMonochrome1: boolean }
 * Message out: { id, out: Uint8ClampedArray }
 */
self.onmessage = function (e) {
    var id       = e.data.id;
    var src      = e.data.pixelData;
    var lut      = e.data.lut;
    var bSlider  = Number.isFinite(e.data.brightness) ? e.data.brightness : 0;
    var cSlider  = Number.isFinite(e.data.contrast)   ? e.data.contrast   : 0;
    var isMono1  = !!e.data.isMonochrome1;

    var bShift  = Math.round((Math.max(-8, Math.min(8, bSlider)) / 8) * 64);   // max +-64 pixel shift
    var cFactor = Math.pow(2, Math.max(-8, Math.min(8, cSlider)) / 4);         // exponential contrast

    var out = new Uint8ClampedArray(src.length);

    for (var i = 0; i < src.length; i += 4) {
        var a = src[i + 3];
        if (a === 0) { out[i + 3] = 0; continue; }

        var inR = isMono1 ? (255 - src[i])     : src[i];
        var inG = isMono1 ? (255 - src[i + 1]) : src[i + 1];
        var inB = isMono1 ? (255 - src[i + 2]) : src[i + 2];

        var r = lut[inR];
        var g = lut[inG];
        var b = lut[inB];

        r += bShift; g += bShift; b += bShift;

        r = Math.round((r - 128) * cFactor + 128);
        g = Math.round((g - 128) * cFactor + 128);
        b = Math.round((b - 128) * cFactor + 128);

        out[i]     = r < 0 ? 0 : r > 255 ? 255 : r;
        out[i + 1] = g < 0 ? 0 : g > 255 ? 255 : g;
        out[i + 2] = b < 0 ? 0 : b > 255 ? 255 : b;
        out[i + 3] = a;
    }

    self.postMessage({ id: id, out: out }, [out.buffer]);
};


/**
 * curve-presets.js
 * Tone-curve presets for Hyu PACS Contrast Processing panel.
 * Each preset: [{x, y}] in [0,1]. x=input, y=output. Sorted by x, monotone.
 */

export const PRESETS = {
    'Balanced': {
        points: [
            { x: 0,    y: 0    },
            { x: 0.5,  y: 0.5  },
            { x: 1,    y: 1    }
        ]
    },
    'HighContrast': {
        points: [
            { x: 0,    y: 0    },
            { x: 0.25, y: 0.08 },
            { x: 0.75, y: 0.92 },
            { x: 1,    y: 1    }
        ]
    },
    'Soft Tissue': {
        points: [
            { x: 0,    y: 0.02 },
            { x: 0.3,  y: 0.40 },
            { x: 0.65, y: 0.65 },
            { x: 1,    y: 0.98 }
        ]
    },
    'Bone': {
        points: [
            { x: 0,    y: 0    },
            { x: 0.15, y: 0.04 },
            { x: 0.50, y: 0.50 },
            { x: 0.85, y: 0.96 },
            { x: 1,    y: 1    }
        ]
    },
    'LUT': {
        points: [
            { x: 0,    y: 0.05 },
            { x: 0.25, y: 0.32 },
            { x: 0.5,  y: 0.55 },
            { x: 0.75, y: 0.78 },
            { x: 1,    y: 0.95 }
        ]
    },
    'Customized': {
        points: [
            { x: 0,    y: 0    },
            { x: 0.5,  y: 0.5  },
            { x: 1,    y: 1    }
        ]
    }
};

/** Display order for the 2-column preset grid */
export const PRESET_ORDER = [
    'Balanced',
    'HighContrast',
    'Soft Tissue',
    'Bone',
    'LUT',
    'Customized'
];

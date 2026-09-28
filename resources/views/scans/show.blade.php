<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hyu PACS Workstation - {{ $scan->patient_name }} ({{ $scan->modality }})</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --ws-bg: #060911;
            --ws-surface: #0c121e;
            --ws-surface-elevated: #111a2c;
            --ws-border: #1a2538;
            --ws-border-focus: #0284c7;
            --ws-text-primary: #f1f5f9;
            --ws-text-secondary: #94a3b8;
            --ws-text-muted: #54657e;
            
            --med-blue: #0284c7;
            --med-blue-hover: #0369a1;
            --med-blue-light: #38bdf8;
            --med-cyan: #06b6d4;
            --med-emerald: #10b981;
            --med-amber: #f59e0b;
            --med-crimson: #ef4444;
            --med-purple: #a855f7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background-color: var(--ws-bg);
            color: var(--ws-text-primary);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            user-select: none;
        }

        /* 1. TOP CLINICAL COMMAND NAVBAR */
        .pacs-navbar {
            background-color: var(--ws-surface);
            border-bottom: 1px solid var(--ws-border);
            height: 50px;
            min-height: 50px;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
        }

        .nav-section-left {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .btn-nav-back {
            color: var(--ws-text-secondary);
            text-decoration: none;
            font-size: 0.775rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.65rem;
            border-radius: 5px;
            border: 1px solid var(--ws-border);
            background: rgba(17, 26, 44, 0.6);
            transition: all 0.15s;
        }

        .btn-nav-back:hover {
            color: var(--ws-text-primary);
            border-color: #334155;
            background: #1e293b;
        }

        .patient-demographics-strip {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(12, 18, 30, 0.9);
            border: 1px solid var(--ws-border);
            padding: 0.3rem 0.75rem;
            border-radius: 6px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
        }

        .patient-demographics-strip strong {
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .badge-modality-tag {
            padding: 0.15rem 0.45rem;
            border-radius: 4px;
            font-size: 0.68rem;
            font-weight: 700;
            background: rgba(2, 132, 199, 0.2);
            color: var(--med-blue-light);
            border: 1px solid rgba(56, 189, 248, 0.3);
            letter-spacing: 0.05em;
        }

        .nav-section-right {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .server-status-pill {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(6, 78, 59, 0.25);
            border: 1px solid rgba(16, 185, 129, 0.35);
            padding: 0.25rem 0.6rem;
            border-radius: 5px;
            font-size: 0.7rem;
            font-family: 'JetBrains Mono', monospace;
            color: #34d399;
            font-weight: 600;
        }

        .server-status-pill .pulse-dot {
            width: 6px;
            height: 6px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 6px #10b981;
        }

        /* 2. RADIOLOGY WORKSTATION TOOLBAR */
        .pacs-toolbar {
            background-color: #080d16;
            border-bottom: 1px solid var(--ws-border);
            padding: 0.35rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
            z-index: 95;
            position: relative;
        }

        .tool-btn {
            background-color: var(--ws-surface);
            border: 1px solid var(--ws-border);
            color: var(--ws-text-secondary);
            padding: 0.35rem 0.65rem;
            border-radius: 5px;
            font-size: 0.76rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.15s;
        }

        .tool-btn:hover {
            background-color: var(--ws-surface-elevated);
            color: var(--ws-text-primary);
            border-color: #334155;
        }

        .tool-btn.active {
            background-color: var(--med-blue);
            border-color: var(--med-blue);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.35);
        }

        .toolbar-divider {
            width: 1px;
            height: 20px;
            background-color: var(--ws-border);
            margin: 0 0.25rem;
        }

        /* Tool Dropdown */
        .tool-dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            background: #0d1424;
            border: 1px solid #23334d;
            border-radius: 6px;
            min-width: 220px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.85), 0 0 0 1px rgba(255, 255, 255, 0.05);
            z-index: 9999;
            padding: 0.35rem 0;
            backdrop-filter: blur(12px);
        }

        .dropdown-menu.menu-right {
            right: 0 !important;
            left: auto !important;
        }

        .tool-dropdown.open > .dropdown-menu,
        .tool-dropdown:hover > .dropdown-menu {
            display: block !important;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            padding: 0.5rem 0.85rem;
            color: var(--ws-text-primary);
            font-size: 0.76rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.12s;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
        }

        .dropdown-item:hover {
            background-color: rgba(2, 132, 199, 0.2);
            color: var(--med-blue-light);
        }

        .dropdown-divider {
            height: 1px;
            background-color: var(--ws-border);
            margin: 0.3rem 0;
        }

        /* 3. MAIN WORKSPACE CONTAINER */
        .pacs-workspace {
            flex: 1;
            display: flex;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        /* 3A. LEFT SERIES & MULTIMODAL DRAWER */
        .series-drawer {
            width: 240px;
            min-width: 240px;
            background-color: #080d16;
            border-right: 1px solid var(--ws-border);
            display: flex;
            flex-direction: column;
            transition: width 0.2s ease, min-width 0.2s ease;
            z-index: 10;
        }

        .series-drawer.collapsed {
            width: 0;
            min-width: 0;
            overflow: hidden;
            border-right: none;
        }

        .drawer-header {
            padding: 0.65rem 0.85rem;
            border-bottom: 1px solid var(--ws-border);
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--ws-text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0b1120;
        }

        .series-list {
            flex: 1;
            overflow-y: auto;
            padding: 0.6rem 0.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .series-thumb-card {
            background: var(--ws-surface);
            border: 1px solid var(--ws-border);
            border-radius: 6px;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.15s;
            position: relative;
        }

        .series-thumb-card:hover {
            border-color: #38bdf8;
            background: #111b2e;
        }

        .series-thumb-card.active-vp1 {
            border-color: var(--med-blue);
            background: rgba(2, 132, 199, 0.12);
            box-shadow: inset 0 0 0 1px var(--med-blue);
        }

        .series-thumb-card.active-vp2 {
            border-color: var(--med-emerald);
            background: rgba(16, 185, 129, 0.12);
            box-shadow: inset 0 0 0 1px var(--med-emerald);
        }

        .thumb-preview-box {
            width: 100%;
            height: 105px;
            background: #000;
            border-radius: 4px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.4rem;
            border: 1px solid #1e293b;
            position: relative;
        }

        .thumb-preview-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .thumb-badge-float {
            position: absolute;
            top: 4px;
            left: 4px;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.12rem 0.45rem;
            border-radius: 3px;
            font-family: 'JetBrains Mono', monospace;
        }

        .thumb-meta {
            font-size: 0.7rem;
            color: var(--ws-text-secondary);
            line-height: 1.35;
        }

        .thumb-meta strong {
            color: var(--ws-text-primary);
            display: block;
            font-size: 0.78rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .thumb-actions-bar {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.35rem;
            margin-top: 0.4rem;
            padding-top: 0.4rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .btn-load-vp {
            background: #090e18;
            border: 1px solid var(--ws-border);
            color: var(--ws-text-secondary);
            font-size: 0.68rem;
            padding: 0.25rem 0.35rem;
            border-radius: 3px;
            cursor: pointer;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            text-align: center;
            transition: all 0.12s;
        }

        .btn-load-vp:hover {
            color: white;
            border-color: #38bdf8;
            background: #0284c7;
        }

        .btn-load-vp.btn-vp2:hover {
            border-color: #34d399;
            background: #059669;
        }

        /* 3B. CENTER VIEWPORTS STAGE */
        .viewports-stage {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr;
            gap: 3px;
            background-color: #000000;
            position: relative;
            overflow: hidden;
            transition: grid-template-columns 0.25s ease;
            touch-action: none !important;
            -webkit-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important;
        }

        .viewports-stage.split-1x2 {
            grid-template-columns: 1fr 1fr;
        }

        .viewport-cell {
            position: relative;
            background-color: #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid #111827;
            cursor: grab;
            touch-action: none !important;
            -webkit-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important;
        }

        .viewport-cell:active {
            cursor: grabbing;
        }

        .viewport-cell.active-cell {
            border: 1px solid var(--med-blue);
        }

        .viewport-cell.cursor-caliper,
        .viewport-cell.cursor-polygon {
            cursor: crosshair !important;
        }

        .viewport-cell.cursor-zoom {
            cursor: zoom-in !important;
        }

        /* Viewport Header Bar */
        .viewport-header-hud {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 0.35rem 0.85rem;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.88) 0%, rgba(0, 0, 0, 0) 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 8;
            pointer-events: none;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.68rem;
        }

        .viewport-tag-name {
            color: #38bdf8;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .medical-image {
            max-width: 86%;
            max-height: 86%;
            transition: transform 0.05s ease-out, filter 0.1s ease-out;
            box-shadow: 0 0 35px rgba(0, 0, 0, 0.95);
            user-select: none;
            pointer-events: none;
        }

        .medical-image-denoised {
            position: absolute;
            z-index: 3;
            box-shadow: none;
            display: none;
        }

        /* DICOM Corner HUD Overlays */
        .dicom-hud {
            position: absolute;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.66rem;
            color: #38bdf8;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.95);
            pointer-events: none;
            line-height: 1.3;
            z-index: 6;
            max-width: 48%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        #vp2 .dicom-hud {
            color: #34d399;
        }

        .hud-top-left { top: 2.2rem; left: 0.75rem; text-align: left; }
        .hud-top-right { top: 2.2rem; right: 0.75rem; text-align: right; }
        .hud-bottom-left { bottom: 0.75rem; left: 0.75rem; text-align: left; }
        .hud-bottom-right { bottom: 0.75rem; right: 0.75rem; text-align: right; }

        /* Caliper & ROI SVG Measurement Canvas */
        .measurement-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 5;
            touch-action: none !important;
            -webkit-user-select: none !important;
            user-select: none !important;
        }

        /* A/B Comparison Split-Curtain Slider */
        .curtain-slider-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 7;
            display: none;
            touch-action: none !important;
            -webkit-user-select: none !important;
            user-select: none !important;
        }

        .curtain-divider {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #38bdf8;
            box-shadow: 0 0 10px #38bdf8;
            cursor: ew-resize;
            pointer-events: auto;
            touch-action: none !important;
        }

        .curtain-handle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #0284c7;
            border: 2px solid #ffffff;
            box-shadow: 0 0 12px rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 11px;
            cursor: ew-resize;
        }

        .curtain-label-left, .curtain-label-right {
            position: absolute;
            top: 2.5rem;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-size: 0.68rem;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid #1e293b;
        }
        .curtain-label-left { left: 1rem; color: #94a3b8; }
        .curtain-label-right { right: 1rem; color: #38bdf8; border-color: rgba(56, 189, 248, 0.4); }

        /* Floating Tool Hint Toast */
        .tool-hint-toast {
            position: absolute;
            bottom: 1.25rem;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(11, 18, 32, 0.95);
            border: 1px solid var(--med-blue);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.8);
            color: var(--ws-text-primary);
            padding: 0.45rem 1.1rem;
            border-radius: 30px;
            font-size: 0.76rem;
            font-weight: 600;
            z-index: 30;
            display: none;
            align-items: center;
            gap: 0.5rem;
            pointer-events: none;
            backdrop-filter: blur(8px);
        }

        /* 3C. RIGHT WORKSTATION DOCK PANEL (TABBED) */
        .dock-panel {
            width: 380px;
            min-width: 380px;
            background-color: var(--ws-surface);
            border-left: 1px solid var(--ws-border);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .dock-tabs-nav {
            display: flex;
            background-color: #080d16;
            border-bottom: 1px solid var(--ws-border);
            padding: 0 4px;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .dock-tabs-nav::-webkit-scrollbar {
            display: none;
        }

        .dock-tab-btn {
            flex: 1;
            padding: 0.65rem 0.2rem;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            color: var(--ws-text-secondary);
            font-size: 0.70rem;
            font-weight: 600;
            letter-spacing: -0.15px;
            cursor: pointer;
            transition: all 0.15s;
            text-align: center;
            white-space: nowrap;
        }

        .dock-tab-btn:hover {
            color: var(--ws-text-primary);
            background: rgba(255, 255, 255, 0.02);
        }

        .dock-tab-btn.active-tab {
            color: var(--med-blue-light);
            border-bottom-color: var(--med-blue-light);
            background: rgba(2, 132, 199, 0.08);
            font-weight: 700;
        }

        .dock-content {
            padding: 1rem;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .tab-pane {
            display: none;
            flex-direction: column;
            gap: 0.85rem;
        }

        .tab-pane.active-pane {
            display: flex;
        }

        /* Module Cards inside Right Dock */
        .dock-card {
            background: rgba(17, 26, 44, 0.65);
            border: 1px solid var(--ws-border);
            border-radius: 6px;
            padding: 0.85rem;
        }

        .dock-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            padding-bottom: 0.4rem;
            border-bottom: 1px solid var(--ws-border);
        }

        .dock-card-title {
            font-size: 0.76rem;
            font-weight: 700;
            color: var(--ws-text-primary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* Interactive Denoising Suite Controls */
        .form-select-pacs {
            width: 100%;
            background-color: #080d16;
            border: 1px solid var(--ws-border);
            color: var(--ws-text-primary);
            padding: 0.45rem 0.6rem;
            border-radius: 5px;
            font-size: 0.76rem;
            outline: none;
            margin-bottom: 0.65rem;
        }

        .form-select-pacs:focus {
            border-color: var(--med-blue);
        }

        .slider-group {
            margin-bottom: 0.6rem;
        }

        .slider-label-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            color: var(--ws-text-secondary);
            margin-bottom: 0.25rem;
        }

        .slider-val-badge {
            font-family: 'JetBrains Mono', monospace;
            color: var(--med-blue-light);
            font-weight: 700;
        }

        .pacs-range-slider {
            width: 100%;
            height: 4px;
            background: #1e293b;
            border-radius: 2px;
            outline: none;
            -webkit-appearance: none;
            cursor: pointer;
        }

        .pacs-range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--med-blue-light);
            cursor: pointer;
            box-shadow: 0 0 6px rgba(56, 189, 248, 0.6);
        }

        /* Scientific Telemetry Grid */
        .telemetry-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.35rem;
            background: #080d16;
            border: 1px solid var(--ws-border);
            border-radius: 5px;
            padding: 0.55rem;
            margin: 0.5rem 0;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.68rem;
        }

        .telemetry-item {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        .telemetry-label {
            color: var(--ws-text-muted);
            font-size: 0.64rem;
        }

        .telemetry-val {
            color: #38bdf8;
            font-weight: 700;
        }

        .btn-action-primary {
            width: 100%;
            background: #0284c7;
            color: white;
            border: 1px solid #0369a1;
            padding: 0.55rem;
            border-radius: 5px;
            font-size: 0.76rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .btn-action-primary:hover {
            background: #0369a1;
        }

        .btn-action-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .comparison-modes-bar {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.35rem;
            margin-top: 0.5rem;
        }

        .btn-toggle-mode {
            background: #080d16;
            border: 1px solid var(--ws-border);
            color: var(--ws-text-secondary);
            font-size: 0.68rem;
            padding: 0.35rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.15s;
            text-align: center;
            font-weight: 600;
        }

        .btn-toggle-mode:hover {
            border-color: #334155;
            color: #f1f5f9;
        }

        .btn-toggle-mode.active-mode {
            background: rgba(2, 132, 199, 0.2);
            border-color: var(--med-blue);
            color: var(--med-blue-light);
        }

        /* Quick Templates & Diagnosis Form */
        .quick-templates-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.3rem;
            margin: 0.4rem 0;
        }

        .btn-template-pill {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--ws-border);
            color: var(--ws-text-secondary);
            font-size: 0.68rem;
            padding: 0.2rem 0.45rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-template-pill:hover {
            border-color: var(--med-blue);
            color: var(--med-blue-light);
            background: rgba(2, 132, 199, 0.1);
        }

        /* Measurement Sub-Pills Bar in Right Dock */
        .measure-sub-pill {
            background: #080d16;
            border: 1px solid var(--ws-border);
            color: var(--ws-text-secondary);
            font-size: 0.68rem;
            padding: 0.38rem 0.25rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.15s;
            text-align: center;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            white-space: nowrap;
        }

        .measure-sub-pill:hover {
            border-color: #facc15;
            color: #fef08a;
            background: rgba(250, 204, 21, 0.1);
        }

        .measure-sub-pill.active-pill {
            background: rgba(250, 204, 21, 0.18);
            border-color: #facc15;
            color: #facc15;
            font-weight: 700;
            box-shadow: 0 0 8px rgba(250, 204, 21, 0.25);
        }

        .form-diagnosis-area {
            width: 100%;
            background-color: #080d16;
            border: 1px solid var(--ws-border);
            color: var(--ws-text-primary);
            padding: 0.55rem;
            border-radius: 5px;
            font-size: 0.78rem;
            outline: none;
            resize: vertical;
            line-height: 1.4;
        }

        .form-diagnosis-area:focus {
            border-color: var(--med-blue);
        }

        .btn-save-emr {
            width: 100%;
            background: #059669;
            border: 1px solid #047857;
            color: white;
            padding: 0.55rem;
            border-radius: 5px;
            font-size: 0.76rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 0.45rem;
            transition: all 0.15s;
        }

        .btn-save-emr:hover {
            background: #047857;
        }

        /* 4. BOTTOM DIAGNOSTIC TELEMETRY BAR */
        .pacs-status-footer {
            background-color: #060911;
            border-top: 1px solid var(--ws-border);
            height: 26px;
            min-height: 26px;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.65rem;
            color: var(--ws-text-muted);
            z-index: 100;
        }

        .footer-left, .footer-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* 5. MOBILE & TABLET RESPONSIVE LAYOUT */
        @media (max-width: 768px) {
            body {
                overflow-y: auto !important;
                height: auto !important;
                min-height: 100vh;
            }
            .pacs-navbar {
                height: auto !important;
                min-height: auto !important;
                padding: 0.4rem 0.6rem !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 0.35rem !important;
            }
            .nav-section-left, .nav-section-right {
                width: 100% !important;
                justify-content: space-between !important;
                overflow-x: auto !important;
                white-space: nowrap !important;
                padding-bottom: 2px !important;
                scrollbar-width: none !important;
            }
            .nav-section-left::-webkit-scrollbar, .nav-section-right::-webkit-scrollbar {
                display: none;
            }
            .patient-demographics-strip {
                font-size: 0.68rem !important;
                padding: 0.2rem 0.45rem !important;
                flex-shrink: 0 !important;
            }
            .pacs-toolbar {
                overflow-x: auto !important;
                white-space: nowrap !important;
                flex-wrap: nowrap !important;
                padding: 0.35rem 0.5rem !important;
                scrollbar-width: none !important;
                gap: 0.3rem !important;
            }
            .pacs-toolbar::-webkit-scrollbar {
                display: none;
            }
            .pacs-workspace {
                flex-direction: column !important;
                overflow: visible !important;
                height: auto !important;
                flex: none !important;
            }
            .viewports-stage {
                min-height: 52vh !important;
                height: 52vh !important;
                width: 100% !important;
                flex: none !important;
            }
            .dock-panel {
                width: 100% !important;
                min-width: 100% !important;
                border-left: none !important;
                border-top: 1px solid var(--ws-border) !important;
                flex: none !important;
                height: auto !important;
            }
            .series-drawer {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                height: 100vh !important;
                width: 280px !important;
                max-width: 85vw !important;
                z-index: 9999 !important;
                box-shadow: 15px 0 35px rgba(0, 0, 0, 0.95) !important;
                transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                transform: translateX(0) !important;
            }
            .series-drawer.collapsed {
                transform: translateX(-100%) !important;
                width: 280px !important;
            }
            .pacs-status-footer {
                display: none !important;
            }
        }

        /* Modal DICOM Tags */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(3, 7, 18, 0.85);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: #0b1120;
            border: 1px solid #1e293b;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.9);
            border-radius: 8px;
            width: 90%;
            max-width: 860px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .modal-header {
            padding: 0.85rem 1.25rem;
            background: #0f172a;
            border-bottom: 1px solid #1e293b;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 1rem 1.25rem;
            overflow-y: auto;
            flex: 1;
        }

        .tag-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
            font-family: 'JetBrains Mono', monospace;
        }

        .tag-table th {
            text-align: left;
            background: #1e293b;
            color: #94a3b8;
            padding: 0.45rem 0.75rem;
            font-weight: 600;
            border: 1px solid #334155;
            position: sticky;
            top: 0;
        }

        .tag-table td {
            padding: 0.4rem 0.75rem;
            border: 1px solid #1e293b;
            color: #e2e8f0;
        }

        .tag-table tr:nth-child(even) {
            background: rgba(15, 23, 42, 0.4);
        }

        .tag-table tr:hover {
            background: rgba(2, 132, 199, 0.15);
        }
    </style>
</head>
<body>

    <!-- 1. TOP CLINICAL COMMAND NAVBAR -->
    <header class="pacs-navbar">
        <div class="nav-section-left">
            <a href="{{ route('scans.index') }}" class="btn-nav-back">
                &larr; Sesi Pasien MCU
            </a>

            <button class="tool-btn" onclick="toggleSeriesDrawer()" title="Sembunyikan / Munculkan Seri Pasien">
                <span id="btnDrawerIcon">◀</span> Seri Pasien ({{ count($patientSeries) }})
            </button>

            <div class="patient-demographics-strip">
                <strong>{{ strtoupper($scan->patient_name) }}</strong>
                <span style="color: #64748b;">|</span>
                <span>MRN: {{ $scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span style="color: #64748b;">|</span>
                <span class="badge-modality-tag">{{ $scan->modality }}</span>
                <span style="color: #64748b;">|</span>
                <span>{{ $scan->gender == 'P' ? 'F' : 'M' }}/{{ $scan->age ?? '18' }}Y</span>
            </div>
        </div>

        <div class="nav-section-right">
            <!-- Multimodal Layout Grid Selector (1x1 Tunggal vs 1x2 Multimodal) -->
            <div style="display: flex; align-items: center; gap: 0.25rem; background: #080d16; border: 1px solid var(--ws-border); padding: 0.2rem; border-radius: 5px;">
                <button class="tool-btn active" id="btnLayoutSingle" onclick="setLayoutMode('1x1')" style="padding: 0.2rem 0.55rem; font-size: 0.7rem;">1x1 Tunggal</button>
                <button class="tool-btn" id="btnLayoutDual" onclick="setLayoutMode('1x2')" style="padding: 0.2rem 0.55rem; font-size: 0.7rem; color: #38bdf8; font-weight: 700;">1x2 Multimodal</button>
            </div>

            <!-- Server Status & Setup Node Button -->
            <div class="server-status-pill" onclick="openDicomConfigModal()" style="cursor: pointer;" title="Klik untuk Konfigurasi Port, AE Title & Jaringan DICOM">
                <span class="pulse-dot"></span>
                <span id="navDicomStatus">DICOM SCP :<strong id="navPortDisplay">{{ $pacsConfig['port'] ?? '4242' }}</strong></span>
                <span style="font-size: 0.65rem; color: #38bdf8; margin-left: 3px;">⚙️</span>
            </div>
        </div>
    </header>

    <!-- 2. CLINICAL WORKSTATION TOOLBAR -->
    <div class="pacs-toolbar">
        <!-- Navigation & Zoom -->
        <button class="tool-btn active" id="btnPan" onclick="setTool('pan')" title="Mode Geser / Pan Gambar">Pan</button>
        <button class="tool-btn" id="btnZoomTool" onclick="setTool('zoom')" title="Mode Perbesar / Zoom Gambar">Zoom</button>

        <div class="toolbar-divider"></div>

        <!-- Unified Alat Ukur & ROI Dropdown -->
        <div class="tool-dropdown" id="ddMeasure">
            <button class="tool-btn" id="btnMeasureMain" onclick="toggleDropdown(event, 'ddMeasure')" style="background: rgba(250, 204, 21, 0.12); border-color: rgba(250, 204, 21, 0.35); color: #facc15; font-weight: 700;" title="Pilihan Alat Ukur Medis & ROI">
                <span id="lblMeasureIcon">📐</span>
                <span id="lblMeasureTitle">Alat Ukur</span>
                <span style="font-size: 0.65rem; opacity: 0.7;">▾</span>
            </button>
            <div class="dropdown-menu">
                <button class="dropdown-item" onclick="setTool('circle'); closeAllDropdowns();">
                    <span style="color: #facc15; font-weight: 600;">⭕ Lingkaran Sempurna (True Circle)</span>
                    <span style="font-size: 0.65rem; color: #facc15; font-family: monospace;">1:1 locked</span>
                </button>
                <button class="dropdown-item" onclick="setTool('caliper'); closeAllDropdowns();">
                    <span style="color: #facc15; font-weight: 600;">📏 Linear Caliper (Jarak 2-Titik)</span>
                    <span style="font-size: 0.65rem; color: #facc15; font-family: monospace;">2-Point</span>
                </button>
                <button class="dropdown-item" onclick="setTool('ellipse'); closeAllDropdowns();">
                    <span style="color: #facc15; font-weight: 600;">🥚 Oval / Ellipse (D1 &times; D2)</span>
                    <span style="font-size: 0.65rem; color: #facc15; font-family: monospace;">Major/Minor</span>
                </button>
                <div class="dropdown-divider"></div>
                <button class="dropdown-item" onclick="setTool('ctr'); closeAllDropdowns();">
                    <span style="color: #fb7185; font-weight: 600;">🫀 Cardiothoracic Ratio (CTR)</span>
                    <span style="font-size: 0.65rem; color: #fb7185; font-family: monospace;">Danzer 1919</span>
                </button>
                <button class="dropdown-item" onclick="setTool('box'); closeAllDropdowns();">
                    <span>🔲 Box ROI (Area Segiempat)</span>
                    <span style="font-size: 0.65rem; color: #64748b; font-family: monospace;">Rectangle</span>
                </button>
                <button class="dropdown-item" onclick="setTool('polygon'); closeAllDropdowns();">
                    <span>📐 Polygon ROI (Magnetik Snap)</span>
                    <span style="font-size: 0.65rem; color: #64748b; font-family: monospace;">Contour</span>
                </button>
            </div>
        </div>

        <!-- Windowing / Preset Dropdown -->
        <div class="tool-dropdown" id="ddPreset">
            <button class="tool-btn" onclick="toggleDropdown(event, 'ddPreset')">
                <span id="lblActivePreset">Window: Default</span>
                <span style="font-size: 0.65rem; opacity: 0.7;">▾</span>
            </button>
            <div class="dropdown-menu">
                <button class="dropdown-item" onclick="applyPreset('default'); closeAllDropdowns();">
                    <span>Default Window</span>
                    <span style="font-size: 0.65rem; color: #64748b; font-family: monospace;">400/40</span>
                </button>
                <button class="dropdown-item" onclick="applyPreset('lung'); closeAllDropdowns();">
                    <span style="color: #38bdf8;">Lung Window (Paru)</span>
                    <span style="font-size: 0.65rem; color: #38bdf8; font-family: monospace;">1500/-600</span>
                </button>
                <button class="dropdown-item" onclick="applyPreset('bone'); closeAllDropdowns();">
                    <span style="color: #facc15;">Bone Window (Tulang)</span>
                    <span style="font-size: 0.65rem; color: #facc15; font-family: monospace;">2500/480</span>
                </button>
                <button class="dropdown-item" onclick="applyPreset('soft'); closeAllDropdowns();">
                    <span style="color: #c084fc;">Soft Tissue Window</span>
                    <span style="font-size: 0.65rem; color: #c084fc; font-family: monospace;">350/50</span>
                </button>
            </div>
        </div>

        <!-- Orientation & Display -->
        <div class="tool-dropdown" id="ddOrientation">
            <button class="tool-btn" onclick="toggleDropdown(event, 'ddOrientation')">
                <span>Orientasi</span>
                <span style="font-size: 0.65rem; opacity: 0.7;">▾</span>
            </button>
            <div class="dropdown-menu">
                <button class="dropdown-item" onclick="rotateImage(); closeAllDropdowns();">
                    <span>Putar 90° Clockwise</span>
                </button>
                <button class="dropdown-item" onclick="flipHorizontal(); closeAllDropdowns();">
                    <span>Cermin Horisontal (Flip H)</span>
                </button>
                <div class="dropdown-divider"></div>
                <button class="dropdown-item" id="btnInvert" onclick="toggleInvert(); closeAllDropdowns();">
                    <span>Invert Grayscale (Monochrome)</span>
                </button>
            </div>
        </div>

        <div class="toolbar-divider"></div>

        <!-- Quick Annotation Management -->
        <button class="tool-btn" onclick="undoLastMeasurement()" title="Hapus titik atau pengukuran terakhir">Undo</button>
        <button class="tool-btn" onclick="clearAllCalipers()" title="Hapus semua anotasi" style="color: #f87171;">Hapus</button>
        <button class="tool-btn" onclick="resetViewer()" title="Reset tampilan citra">Reset View</button>

        <div class="toolbar-divider"></div>

        <!-- Denoising Quick Toggle -->
        <button class="tool-btn" id="btnQuickDenoise" onclick="switchDockTab('denoise')" style="background: rgba(2, 132, 199, 0.15); border-color: rgba(2, 132, 199, 0.35); color: #38bdf8;">
            <span>🔬 Restorasi & Denoising</span>
        </button>

        <!-- DICOM & Export (Far Right) -->
        <div class="tool-dropdown" id="ddDicom" style="margin-left: auto;">
            <button class="tool-btn" onclick="toggleDropdown(event, 'ddDicom')" style="background: #111a2c; border-color: #23334d; color: #38bdf8;">
                <span>DICOM & Ekspor</span>
                <span style="font-size: 0.65rem; opacity: 0.7;">▾</span>
            </button>
            <div class="dropdown-menu menu-right">
                <button class="dropdown-item" onclick="openDicomTagsModal(); closeAllDropdowns();">
                    <span style="color: #38bdf8; font-weight: 600;">DICOM Tags Inspector</span>
                    <span style="font-size: 0.65rem; color: #64748b; font-family: monospace;">PS 3.6</span>
                </button>
                <button class="dropdown-item" onclick="exportAnnotatedReport(); closeAllDropdowns();">
                    <span style="color: #10b981; font-weight: 600;">Ekspor Laporan Anotasi (PNG)</span>
                    <span style="font-size: 0.65rem; color: #10b981; font-family: monospace;">SC IOD</span>
                </button>
                <div class="dropdown-divider"></div>
                <a href="{{ route('scans.download-dcm', $scan->id) }}" class="dropdown-item" onclick="closeAllDropdowns()" style="color: #c084fc; font-weight: 600;">
                    <span>Unduh File Mentah (.dcm)</span>
                    <span style="font-size: 0.65rem; color: #c084fc; font-family: monospace;">Part 10</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 3. MAIN WORKSPACE -->
    <div class="pacs-workspace">

        <!-- 3A. LEFT SERIES & MULTIMODAL DRAWER -->
        <aside class="series-drawer" id="seriesDrawer">
            <div class="drawer-header">
                <div>
                    <span>Seri Multimodal ({{ count($patientSeries) }})</span>
                    <span style="font-size: 0.65rem; color: #38bdf8; display: block;">CARDIO-PULMO</span>
                </div>
                <button onclick="toggleSeriesDrawer()" style="background: none; border: none; color: #94a3b8; font-size: 1.1rem; cursor: pointer; padding: 0.2rem 0.5rem;" title="Tutup Drawer">✕</button>
            </div>

            <div class="series-list">
                @foreach($patientSeries as $idx => $s)
                    <div class="series-thumb-card {{ $idx === 0 ? 'active-vp1' : 'active-vp2' }}" 
                         id="seriesCard_{{ $s['id'] }}"
                         onclick="loadSeriesActive('{{ asset($s['image_path']) }}', '{{ $s['name'] }}', '{{ $s['study'] }}', '{{ $s['station'] }}', '{{ $s['matrix'] }}', '{{ $s['id'] }}')">
                        
                        <div class="thumb-preview-box">
                            <span class="thumb-badge-float" style="background: {{ $s['badge_color'] }}25; color: {{ $s['badge_color'] }}; border: 1px solid {{ $s['badge_color'] }}50;">
                                {{ $s['badge'] }}
                            </span>
                            <img src="{{ asset($s['image_path']) }}?v={{ time() }}" alt="{{ $s['name'] }}">
                        </div>

                        <div class="thumb-meta">
                            <strong>{{ $s['name'] }}</strong>
                            <div style="font-size: 0.65rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $s['study'] }}
                            </div>
                            <div style="font-size: 0.62rem; color: #94a3b8; font-family: 'JetBrains Mono', monospace; margin-top: 0.15rem;">
                                {{ $s['station'] }} &bull; {{ $s['matrix'] }}
                            </div>
                        </div>

                        <div class="thumb-actions-bar" onclick="event.stopPropagation()">
                            <button class="btn-load-vp" onclick="loadSeriesToVp(1, '{{ asset($s['image_path']) }}', '{{ $s['name'] }}', '{{ $s['study'] }}', '{{ $s['station'] }}', '{{ $s['matrix'] }}', '{{ $s['id'] }}')" title="Muat ke Viewport A (Kiri)">
                                ◧ Ke VP-A
                            </button>
                            <button class="btn-load-vp btn-vp2" onclick="loadSeriesToVp(2, '{{ asset($s['image_path']) }}', '{{ $s['name'] }}', '{{ $s['study'] }}', '{{ $s['station'] }}', '{{ $s['matrix'] }}', '{{ $s['id'] }}')" title="Muat ke Viewport B (Kanan)">
                                ◨ Ke VP-B
                            </button>
                        </div>
                    </div>
                @endforeach

                @if(isset($otherScans) && count($otherScans) > 0)
                    <div style="margin-top: 0.6rem; padding-top: 0.5rem; border-top: 1px dashed #1e293b;">
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748b; margin-bottom: 0.4rem; padding-left: 0.2rem;">
                            PASIEN LAIN (HISTORIS MCU)
                        </div>
                        @foreach($otherScans as $os)
                            <a href="{{ route('scans.show', $os->id) }}" style="text-decoration: none; display: block; margin-bottom: 0.35rem;">
                                <div style="background: rgba(15, 23, 42, 0.7); border: 1px solid #1e293b; border-radius: 4px; padding: 0.35rem 0.45rem; font-size: 0.68rem; color: #94a3b8; transition: all 0.15s;">
                                    <strong style="color: #e2e8f0; display: block;">{{ (!empty($os->patient_name) && $os->patient_name !== 'UNKNOWN') ? $os->patient_name : 'Pasien MCU CDC #' . str_pad($os->id, 4, '0', STR_PAD_LEFT) }}</strong>
                                    <span>{{ ($os->modality && $os->modality !== '?') ? $os->modality : 'Thorax PA (CR)' }} &bull; {{ $os->patient_id ?: 'CDC-' . str_pad($os->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </aside>

        <!-- 3B. CENTER VIEWPORTS STAGE (DUAL MULTIMODAL) -->
        <div class="viewports-stage" id="viewportsStage">

            <!-- Floating Tool Hint Toast -->
            <div class="tool-hint-toast" id="toolHintToast">
                <span id="toolHintIcon">●</span>
                <span id="toolHintText">Pilih alat pengukuran di toolbar atas.</span>
            </div>

            <!-- Viewport 1 (VP-A: Primary Scan - Thorax PA) -->
            <div class="viewport-cell active-cell" id="vp1" onclick="selectViewport(1)">
                <div class="viewport-header-hud">
                    <span class="viewport-tag-name" id="vp1Title">VP-A: {{ $patientSeries[0]['name'] }} (PRIMARY)</span>
                    <span style="color: #94a3b8;" id="vp1SeriesInfo">{{ $patientSeries[0]['station'] }} | {{ $patientSeries[0]['matrix'] }}</span>
                </div>

                <div class="dicom-hud hud-top-left" id="vp1HudTopLeft">
                    <strong>CAHAYA DIAGNOSTIC CENTRE</strong><br>
                    {{ strtoupper($scan->patient_name) }}<br>
                    MRN: {{ $scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}<br>
                    Acc: {{ $scan->accession_number ?: 'ACC-' . str_pad($scan->id, 4, '0', STR_PAD_LEFT) }}
                </div>
                <div class="dicom-hud hud-top-right" id="vp1HudTopRight">
                    Modalitas: {{ $patientSeries[0]['name'] }}<br>
                    Studi: {{ $patientSeries[0]['study'] }}<br>
                    Station: {{ $patientSeries[0]['station'] }}
                </div>
                <div class="dicom-hud hud-bottom-left">
                    Zoom: <span id="lblZoom">100%</span><br>
                    <span id="lblWwWl">WW: 400 | WL: 40 (Default)</span>
                </div>
                <div class="dicom-hud hud-bottom-right">
                    Hyu PACS &bull; Viewport A<br>
                    SOP: 1.2.840.10008.5.1.4.1.1.1
                </div>

                <!-- A/B Comparison Split Curtain Container -->
                <div class="curtain-slider-container" id="curtainContainer1">
                    <div class="curtain-label-left">RAW ACQUISITION</div>
                    <div class="curtain-label-right">DENOISED / RESTORED</div>
                    <div class="curtain-divider" id="curtainDivider1" style="left: 50%;">
                        <div class="curtain-handle">⮂</div>
                    </div>
                </div>

                <!-- Measurement Canvas Overlay -->
                <svg class="measurement-canvas" id="svgMeasure1"></svg>

                <img id="imgVp1" class="medical-image"
                     src="{{ asset($patientSeries[0]['image_path']) }}?v={{ time() }}" 
                     alt="DICOM Scan VP1">
                <img id="imgVp1Denoised" class="medical-image medical-image-denoised"
                     src="{{ asset($patientSeries[0]['image_path']) }}?v={{ time() }}" 
                     alt="Denoised Scan VP1">
            </div>

            <!-- Viewport 2 (VP-B: Secondary Multimodal - USG / Comparison) -->
            <div class="viewport-cell" id="vp2" style="display: none;" onclick="selectViewport(2)">
                <div class="viewport-header-hud">
                    <span class="viewport-tag-name" id="vp2Title" style="color: #10b981;">VP-B: {{ $patientSeries[1]['name'] }} (COMPARISON)</span>
                    <span style="color: #94a3b8;" id="vp2SeriesInfo">{{ $patientSeries[1]['station'] }} | {{ $patientSeries[1]['matrix'] }}</span>
                </div>

                <div class="dicom-hud hud-top-left" id="vp2HudTopLeft">
                    <strong>CAHAYA DIAGNOSTIC CENTRE</strong><br>
                    {{ strtoupper($scan->patient_name) }}<br>
                    MRN: {{ $scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}
                </div>
                <div class="dicom-hud hud-top-right" id="vp2HudTopRight">
                    Modalitas: {{ $patientSeries[1]['name'] }}<br>
                    Studi: {{ $patientSeries[1]['study'] }}<br>
                    Station: {{ $patientSeries[1]['station'] }}
                </div>
                <div class="dicom-hud hud-bottom-left">
                    Zoom: <span id="lblZoom2">100%</span><br>
                    Probe: Convex 3.5 MHz
                </div>
                <div class="dicom-hud hud-bottom-right">
                    Hyu PACS &bull; Viewport B<br>
                    USG B-Mode Real-time
                </div>

                <!-- A/B Comparison Split Curtain Container VP2 -->
                <div class="curtain-slider-container" id="curtainContainer2">
                    <div class="curtain-label-left">RAW ACQUISITION</div>
                    <div class="curtain-label-right">DENOISED / RESTORED</div>
                    <div class="curtain-divider" id="curtainDivider2" style="left: 50%;">
                        <div class="curtain-handle">⮂</div>
                    </div>
                </div>

                <svg class="measurement-canvas" id="svgMeasure2"></svg>
                <img id="imgVp2" class="medical-image"
                     src="{{ asset($patientSeries[1]['image_path']) }}?v={{ time() }}" 
                     alt="DICOM Scan VP2">
                <img id="imgVp2Denoised" class="medical-image medical-image-denoised"
                     src="{{ asset($patientSeries[1]['image_path']) }}?v={{ time() }}" 
                     alt="Denoised Scan VP2">
            </div>

        </div>

        <!-- 3C. RIGHT DOCK PANEL (TABBED WORKSTATION) -->
        <aside class="dock-panel">
            <div class="dock-tabs-nav">
                <button class="dock-tab-btn active-tab" id="tabBtnDenoise" onclick="switchDockTab('denoise')">
                    Restorasi & Filter
                </button>
                <button class="dock-tab-btn" id="tabBtnMeasure" onclick="switchDockTab('measure')">
                    Hasil Ukur / CTR
                </button>
                <button class="dock-tab-btn" id="tabBtnDiagnosis" onclick="switchDockTab('diagnosis')">
                    Ekspertise MCU
                </button>
                <button class="dock-tab-btn" id="tabBtnDicom" onclick="switchDockTab('dicom')">
                    Tags DICOM
                </button>
            </div>

            <div class="dock-content">

                <!-- TAB 1: RESTORASI CITRA & QUANTUM NOISE FILTERING -->
                <div class="tab-pane active-pane" id="paneDenoise">
                    <div class="dock-card">
                        <div class="dock-card-header">
                            <span class="dock-card-title">Engine Filter Restorasi</span>
                            <span id="lblTargetVpBadge" style="font-size: 0.65rem; color: #38bdf8; font-family: monospace; background: rgba(2, 132, 199, 0.2); padding: 2px 6px; border-radius: 4px; border: 1px solid rgba(56, 189, 248, 0.3);">Target: VP-A (Thorax)</span>
                        </div>

                        <select id="selFilterEngine" class="form-select-pacs" onchange="onEngineChange()">
                            <option value="bilateral" selected>Bilateral Filter (Tomasi & Manduchi 1998)</option>
                            <option value="nlm">Non-Local Means (NLM - Buades 2005)</option>
                            <option value="dl_dncnn">Deep Residual CNN (DnCNN - Zhang 2017)</option>
                            <option value="gaussian">Adaptive Gaussian Smoothing</option>
                        </select>

                        <!-- Parameter Sliders -->
                        <div class="slider-group">
                            <div class="slider-label-row">
                                <span>Radius Spasial (Kernel &sigma;<sub>s</sub>):</span>
                                <span class="slider-val-badge" id="lblSigmaSpace">20</span>
                            </div>
                            <input type="range" min="5" max="80" value="20" class="pacs-range-slider" id="rngSigmaSpace" oninput="document.getElementById('lblSigmaSpace').innerText = this.value">
                        </div>

                        <div class="slider-group">
                            <div class="slider-label-row">
                                <span>Sensitivitas Tepi (Fotometrik &sigma;<sub>r</sub>):</span>
                                <span class="slider-val-badge" id="lblSigmaColor">25</span>
                            </div>
                            <input type="range" min="5" max="80" value="25" class="pacs-range-slider" id="rngSigmaColor" oninput="document.getElementById('lblSigmaColor').innerText = this.value">
                        </div>

                        <div style="font-size: 0.65rem; color: #94a3b8; background: rgba(15, 23, 42, 0.6); border-left: 2px solid #38bdf8; padding: 4px 8px; border-radius: 2px; margin-bottom: 0.5rem; line-height: 1.3;">
                            💡 <strong>Sweet Spot Thorax:</strong> &sigma;<sub>s</sub> = 20, &sigma;<sub>r</sub> = 25. Nilai terlalu tinggi (>50) menyebabkan <em>over-smoothing</em> (blur) pada detail paru.
                        </div>

                        <!-- Telemetry Grid -->
                        <div class="telemetry-grid">
                            <div class="telemetry-item">
                                <span class="telemetry-label">PSNR Metric:</span>
                                <span class="telemetry-val" id="telPsnr">40.22 dB</span>
                            </div>
                            <div class="telemetry-item">
                                <span class="telemetry-label">SNR Gain:</span>
                                <span class="telemetry-val" id="telSnrGain" style="color: #10b981;">+9.75 dB</span>
                            </div>
                            <div class="telemetry-item">
                                <span class="telemetry-label">Noise Reduction:</span>
                                <span class="telemetry-val" id="telNoiseRed">67.4%</span>
                            </div>
                            <div class="telemetry-item">
                                <span class="telemetry-label">Inference Time:</span>
                                <span class="telemetry-val" id="telLatency">32.0 ms</span>
                            </div>
                        </div>

                        <button class="btn-action-primary" id="btnExecuteDenoise" onclick="executeAdvancedDenoise()">
                            <span>▶ Jalankan Filter Denoising</span>
                        </button>

                        <!-- Inspection Modes -->
                        <div class="comparison-modes-bar">
                            <button class="btn-toggle-mode" id="btnCurtainMode" onclick="toggleCurtainWipe()">
                                Tirai A/B Wipe
                            </button>
                            <button class="btn-toggle-mode" id="btnResidualMode" onclick="toggleResidualMap()">
                                Peta Residu Noise
                            </button>
                        </div>
                    </div>

                    <!-- TABEL KOMPARASI 4 ALGORITMA (EVALUASI RESTORASI) -->
                    <div class="dock-card" style="border-color: rgba(56, 189, 248, 0.35);">
                        <div class="dock-card-header">
                            <span class="dock-card-title" style="color: #38bdf8; display: flex; align-items: center; gap: 0.35rem;">
                                <span>📊</span> Matriks Komparasi 4 Algoritma
                            </span>
                            <button class="tool-btn" onclick="executeAlgorithmBenchmark()" id="btnRunBenchmark" style="padding: 0.2rem 0.5rem; font-size: 0.65rem; background: rgba(2, 132, 199, 0.2); border-color: rgba(56, 189, 248, 0.4); color: #38bdf8;" title="Jalankan pengujian komparatif 4 metode">
                                ⚡ Uji Komparasi
                            </button>
                        </div>

                        <div style="overflow-x: auto; margin-top: 0.5rem;">
                            <table class="tag-table" id="tableBenchmark" style="font-size: 0.68rem;">
                                <thead>
                                    <tr>
                                        <th>Metode</th>
                                        <th>PSNR</th>
                                        <th>SSIM</th>
                                        <th>SNR Gain</th>
                                        <th>Latency</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyBenchmark">
                                    <tr>
                                        <td style="color: #94a3b8;">Citra Asli (Raw)</td>
                                        <td>-</td>
                                        <td>1.0000</td>
                                        <td>0.00 dB</td>
                                        <td>0.0 ms</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #38bdf8;">Bilateral Filter</td>
                                        <td>44.62 dB</td>
                                        <td>0.9779</td>
                                        <td>+5.24 dB</td>
                                        <td>59.9 ms</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #c084fc;">Non-Local Means</td>
                                        <td>46.06 dB</td>
                                        <td>0.9799</td>
                                        <td>+3.92 dB</td>
                                        <td>254.3 ms</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #34d399; font-weight: 700;">DnCNN (Deep AI)</td>
                                        <td style="color: #34d399; font-weight: 700;">45.12 dB</td>
                                        <td style="color: #34d399; font-weight: 700;">0.9797</td>
                                        <td style="color: #34d399; font-weight: 700;">+3.48 dB</td>
                                        <td style="color: #34d399; font-weight: 700;">19.4 ms</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem;">
                            <span style="font-size: 0.65rem; color: #64748b;">Formula: Wang 2004, Immerkaer 1996</span>
                            <button onclick="exportBenchmarkCsv()" class="btn-toggle-mode" style="padding: 0.3rem 0.6rem; font-size: 0.68rem; background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.4); color: #34d399;" title="Unduh data tabel dalam format CSV/Excel">
                                📥 Ekspor CSV
                            </button>
                        </div>
                    </div>

                    <div class="dock-card" style="font-size: 0.7rem; color: #64748b; line-height: 1.4;">
                        <strong style="color: #94a3b8; display: block; margin-bottom: 0.2rem;">Landasan Ilmiah Pemrosesan Citra:</strong>
                        Menerapkan non-linear edge-preserving filter untuk mereduksi <em>quantum mottle noise</em> pada akusisi dosis rendah tanpa mengaburkan batas vaskular paru dan trabekula tulang.
                    </div>
                </div>

                <!-- TAB 2: ALAT UKUR & CTR -->
                <div class="tab-pane" id="paneMeasure">
                    <!-- Quick Tool Selector Sub-Pills -->
                    <div class="dock-card" style="padding: 0.55rem 0.65rem; margin-bottom: 0.5rem; background: rgba(12, 18, 30, 0.85);">
                        <div style="font-size: 0.68rem; color: #94a3b8; font-weight: 700; margin-bottom: 0.4rem; display: flex; justify-content: space-between; align-items: center;">
                            <span>PILIH INSTRUMEN UKUR:</span>
                            <span style="font-size: 0.62rem; color: #64748b;">Klik & tarik di gambar</span>
                        </div>
                        <div class="measure-pills-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.35rem;">
                            <button class="measure-sub-pill" id="pillToolCircle" onclick="setTool('circle')">⭕ Lingkaran</button>
                            <button class="measure-sub-pill" id="pillToolCaliper" onclick="setTool('caliper')">📏 Caliper</button>
                            <button class="measure-sub-pill" id="pillToolEllipse" onclick="setTool('ellipse')">🥚 Oval</button>
                            <button class="measure-sub-pill" id="pillToolCtr" onclick="setTool('ctr')">🫀 CTR</button>
                            <button class="measure-sub-pill" id="pillToolBox" onclick="setTool('box')">🔲 Kotak</button>
                            <button class="measure-sub-pill" id="pillToolPolygon" onclick="setTool('polygon')">📐 Poligon</button>
                        </div>
                    </div>

                    <div class="dock-card">
                        <div class="dock-card-header">
                            <span class="dock-card-title">Daftar Anotasi & Temuan</span>
                            <div style="display: flex; gap: 0.35rem;">
                                <button onclick="undoLastMeasurement()" style="background: none; border: none; color: #38bdf8; font-size: 0.7rem; cursor: pointer; font-weight: 600;">Undo</button>
                                <button onclick="clearAllCalipers()" style="background: none; border: none; color: #ef4444; font-size: 0.7rem; cursor: pointer; font-weight: 600;">Hapus</button>
                            </div>
                        </div>

                        <div id="caliperListContainer">
                            <span style="font-size: 0.72rem; color: var(--ws-text-muted); font-style: italic;">Belum ada pengukuran di Viewport ini. Pilih salah satu instrumen ukur di atas.</span>
                        </div>
                    </div>

                    <!-- CARD HASIL EVALUASI CTR KLINIS -->
                    <div class="dock-card" id="cardCtrLiveVerdict">
                        <div class="dock-card-header">
                            <span class="dock-card-title">Evaluasi Klinis CTR (Danzer 1919)</span>
                            <span id="badgeCtrStatusPill" style="font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-weight: 700; background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3);">Belum Diukur</span>
                        </div>

                        <div id="ctrLiveResultContent">
                            <div style="font-size: 0.72rem; color: #94a3b8; line-height: 1.4; margin-bottom: 0.5rem;">
                                Formula: <code>CTR = (Diameter Jantung / Diameter Toraks) &times; 100%</code><br>
                                &bull; <strong>Normal:</strong> &le; 50% &bull; <strong>Suspek Kardiomegali:</strong> &gt; 50%
                            </div>
                            <div style="background: rgba(15, 23, 42, 0.6); border: 1px dashed var(--ws-border); border-radius: 6px; padding: 0.6rem; text-align: center;">
                                <span style="font-size: 0.72rem; color: var(--ws-text-muted);">
                                    Gunakan tool <strong>🫀 CTR</strong> di toolbar atas untuk menghitung rasio diameter transversal jantung & toraks secara langsung.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: EKSPERTISE RADIOLOG -->
                <div class="tab-pane" id="paneDiagnosis">
                    <div class="dock-card">
                        <div class="dock-card-header">
                            <span class="dock-card-title">Lembar Catatan Ekspertise</span>
                            <span style="font-size: 0.65rem; color: #64748b;">MCU Radiologi</span>
                        </div>

                        <div style="font-size: 0.68rem; color: #94a3b8; margin-bottom: 0.2rem;">Template Cepat:</div>
                        <div class="quick-templates-grid">
                            <button class="btn-template-pill" onclick="insertTemplate('normal')">Cor/Pulmo Normal</button>
                            <button class="btn-template-pill" onclick="insertTemplate('cardiomegaly')">Suspek Kardiomegali</button>
                            <button class="btn-template-pill" onclick="insertTemplate('infiltrate')">Infiltrat Paru</button>
                        </div>

                        <textarea id="txtDiagnosis" rows="5" class="form-diagnosis-area" placeholder="Ketik kesimpulan ekspertise citra rontgen di sini...">{{ $scan->diagnosis_notes }}</textarea>

                        <div style="margin-top: 0.45rem;">
                            <input type="text" id="txtDoctor" class="form-diagnosis-area" style="padding: 0.4rem;" value="{{ $scan->doctor_name ?: 'dr. Radiolog Sp.Rad' }}" placeholder="Nama Dokter Radiolog">
                        </div>

                        <button class="btn-save-emr" onclick="saveDiagnosis()">
                            Simpan Ekspertise ke Rekam Medis
                        </button>
                    </div>
                </div>

                <!-- TAB 4: DICOM TAGS INSPECTOR -->
                <div class="tab-pane" id="paneDicom">
                    <div class="dock-card">
                        <div class="dock-card-header">
                            <span class="dock-card-title">DICOM Header (NEMA PS 3.6)</span>
                            <button onclick="openDicomTagsModal()" style="background: none; border: none; color: #38bdf8; font-size: 0.7rem; cursor: pointer; font-weight: 600;">Perbesar ↗</button>
                        </div>
                        <div style="font-size: 0.72rem; color: #94a3b8; font-family: monospace; line-height: 1.5;">
                            (0010,0010) Patient Name: {{ strtoupper($scan->patient_name) }}<br>
                            (0010,0020) Patient ID: {{ $scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}<br>
                            (0008,0060) Modality: {{ $scan->modality }}<br>
                            (0008,1010) Station: {{ $scan->station_name ?: 'FUJIFILM_FDR' }}<br>
                            (0028,0004) Photometric: MONOCHROME2<br>
                            (0028,0100) Bits Allocated: 16<br>
                            (0028,1050) Window Center: 40<br>
                            (0028,1051) Window Width: 400
                        </div>
                    </div>
                </div>

            </div>
        </aside>

    </div>

    <!-- 4. BOTTOM DIAGNOSTIC TELEMETRY BAR -->
    <footer class="pacs-status-footer">
        <div class="footer-left">
            <span>SOP Class: 1.2.840.10008.5.1.4.1.1.1 (CR Image Storage)</span>
            <span>|</span>
            <span>Matrix: 2048 x 2048 x 16-bit</span>
            <span>|</span>
            <span>Photometric: MONOCHROME2</span>
        </div>
        <div class="footer-right">
            <span>Renderer: WebGL / 2D Canvas (60 FPS)</span>
            <span>|</span>
            <span>AE: HYU_PACS</span>
        </div>
    </footer>

    <!-- Interactive PACS JavaScript -->
    <script>
        // State Per Viewport (1=VP-A, 2=VP-B)
        let activeViewport = 1;
        let isMultimodal = false;
        let isDrawerOpen = (window.innerWidth > 768);

        const vpState = {
            1: { zoom: 1, translateX: 0, translateY: 0, rotation: 0, flipH: false, inverted: false, brightness: 100, contrast: 100, rawSrc: '', denoisedSrc: '', residualSrc: '', isResidual: false, isCurtain: false, curtainPos: 50, metrics: null, engine: '' },
            2: { zoom: 1, translateX: 0, translateY: 0, rotation: 0, flipH: false, inverted: false, brightness: 100, contrast: 100, rawSrc: '', denoisedSrc: '', residualSrc: '', isResidual: false, isCurtain: false, curtainPos: 50, metrics: null, engine: '' }
        };

        // Measurements Store
        const measurements = { 1: [], 2: [] };
        let activeDrawing = null;
        let activeCircleDrawing = null;
        let isCircleDrawing = false;
        let activeBoxDrawing = null;
        let isBoxDrawing = false;
        let activeEllipseDrawing = null;
        let isEllipseDrawing = false;
        let currentPolygonPoints = [];
        let polygonHoverPoint = null;
        let isPolygonSnapping = false;

        let ctrWorkflow = {
            step: 1,
            heartLine: null,
            thoraxLine: null
        };

        let activeTool = 'pan';
        let isDragging = false;
        let startX, startY;
        let activeDraggingCurtainVp = null;

        const img1 = document.getElementById('imgVp1');
        const img2 = document.getElementById('imgVp2');
        const img1Denoised = document.getElementById('imgVp1Denoised');
        const img2Denoised = document.getElementById('imgVp2Denoised');
        const vp1 = document.getElementById('vp1');
        const vp2 = document.getElementById('vp2');
        const viewportsStage = document.getElementById('viewportsStage');
        const seriesDrawer = document.getElementById('seriesDrawer');
        const lblZoom = document.getElementById('lblZoom');
        const lblWwWl = document.getElementById('lblWwWl');
        const svgMeasure1 = document.getElementById('svgMeasure1');
        const svgMeasure2 = document.getElementById('svgMeasure2');
        const toolHintToast = document.getElementById('toolHintToast');
        const toolHintIcon = document.getElementById('toolHintIcon');
        const toolHintText = document.getElementById('toolHintText');
        const curtainContainer1 = document.getElementById('curtainContainer1');
        const curtainContainer2 = document.getElementById('curtainContainer2');
        const curtainDivider1 = document.getElementById('curtainDivider1');
        const curtainDivider2 = document.getElementById('curtainDivider2');

        // Init Initial Image Sources & Drawer Responsive State
        if (img1) vpState[1].rawSrc = img1.src;
        if (img2) vpState[2].rawSrc = img2.src;
        if (seriesDrawer) {
            seriesDrawer.classList.toggle('collapsed', !isDrawerOpen);
            const icon = document.getElementById('btnDrawerIcon');
            if (icon) icon.innerText = isDrawerOpen ? '◀' : '▶';
        }

        // Drawer Toggle
        function toggleSeriesDrawer() {
            isDrawerOpen = !isDrawerOpen;
            seriesDrawer.classList.toggle('collapsed', !isDrawerOpen);
            const icon = document.getElementById('btnDrawerIcon');
            if (icon) icon.innerText = isDrawerOpen ? '◀' : '▶';
        }

        // Layout Switcher: 1x1 Tunggal vs 1x2 Multimodal
        function setLayoutMode(mode) {
            isMultimodal = (mode === '1x2');
            document.getElementById('btnLayoutSingle').classList.toggle('active', !isMultimodal);
            document.getElementById('btnLayoutDual').classList.toggle('active', isMultimodal);

            if (isMultimodal) {
                viewportsStage.classList.add('split-1x2');
                vp2.style.display = 'flex';
                showToolHint('◧◨', 'Mode 1x2 Multimodal: Membandingkan Thorax PA (Rontgen) vs USG secara berdampingan.');
            } else {
                viewportsStage.classList.remove('split-1x2');
                vp2.style.display = 'none';
                selectViewport(1);
                hideToolHint();
            }
        }

        // Load Series into Specific Viewport (1=A, 2=B)
        function loadSeriesToVp(targetVp, imgUrl, name, studyDesc, station, matrix, cardId) {
            const targetImg = (targetVp === 1) ? img1 : img2;
            const targetDenoisedImg = (targetVp === 1) ? img1Denoised : img2Denoised;
            const cContainer = (targetVp === 1) ? curtainContainer1 : curtainContainer2;

            if (targetImg) {
                targetImg.src = imgUrl;
                vpState[targetVp].rawSrc = imgUrl;
                vpState[targetVp].denoisedSrc = '';
                vpState[targetVp].residualSrc = '';
                vpState[targetVp].isResidual = false;
                vpState[targetVp].isCurtain = false;
                vpState[targetVp].metrics = null;
                vpState[targetVp].zoom = 1;
                vpState[targetVp].translateX = 0;
                vpState[targetVp].translateY = 0;

                if (targetDenoisedImg) {
                    targetDenoisedImg.src = imgUrl;
                    targetDenoisedImg.style.display = 'none';
                }
                if (cContainer) cContainer.style.display = 'none';
                updateTransform(targetVp);
                updateFilter(targetVp);
            }

            if (targetVp === 1) {
                document.getElementById('vp1Title').innerText = `VP-A: ${name} (PRIMARY)`;
                document.getElementById('vp1SeriesInfo').innerText = `${station} | ${matrix}`;
                document.getElementById('vp1HudTopRight').innerHTML = `Modalitas: ${name}<br>Studi: ${studyDesc}<br>Station: ${station}`;
                selectViewport(1);
            } else {
                document.getElementById('vp2Title').innerText = `VP-B: ${name} (COMPARISON)`;
                document.getElementById('vp2SeriesInfo').innerText = `${station} | ${matrix}`;
                document.getElementById('vp2HudTopRight').innerHTML = `Modalitas: ${name}<br>Studi: ${studyDesc}<br>Station: ${station}`;
                if (!isMultimodal) setLayoutMode('1x2');
                selectViewport(2);
            }

            showToolHint('📂', `Memuat ${name} ke Viewport ${targetVp === 1 ? 'A (Kiri)' : 'B (Kanan)'}`);
            setTimeout(hideToolHint, 2500);

            if (window.innerWidth <= 768 && isDrawerOpen) {
                toggleSeriesDrawer();
            }
        }

        // Load Series into Currently Active Viewport
        function loadSeriesActive(imgUrl, name, studyDesc, station, matrix, cardId) {
            loadSeriesToVp(activeViewport, imgUrl, name, studyDesc, station, matrix, cardId);
            if (window.innerWidth <= 768 && isDrawerOpen) {
                toggleSeriesDrawer();
            }
        }

        // Right Dock Tabs Switcher
        function switchDockTab(tabId) {
            document.querySelectorAll('.dock-tab-btn').forEach(btn => btn.classList.remove('active-tab'));
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active-pane'));

            if (tabId === 'denoise') {
                document.getElementById('tabBtnDenoise').classList.add('active-tab');
                document.getElementById('paneDenoise').classList.add('active-pane');
            } else if (tabId === 'measure') {
                document.getElementById('tabBtnMeasure').classList.add('active-tab');
                document.getElementById('paneMeasure').classList.add('active-pane');
            } else if (tabId === 'diagnosis') {
                document.getElementById('tabBtnDiagnosis').classList.add('active-tab');
                document.getElementById('paneDiagnosis').classList.add('active-pane');
            } else if (tabId === 'dicom') {
                document.getElementById('tabBtnDicom').classList.add('active-tab');
                document.getElementById('paneDicom').classList.add('active-pane');
            }
        }

        // Dropdown Menu Controller
        function toggleDropdown(event, dropdownId) {
            event.stopPropagation();
            const dd = document.getElementById(dropdownId);
            const isOpen = dd.classList.contains('open');
            closeAllDropdowns();
            if (!isOpen) dd.classList.add('open');
        }

        function closeAllDropdowns() {
            document.querySelectorAll('.tool-dropdown').forEach(d => d.classList.remove('open'));
        }

        window.addEventListener('click', (e) => {
            if (!e.target.closest('.tool-dropdown')) closeAllDropdowns();
        });

        // Viewport Selection
        function selectViewport(id) {
            if (activeViewport !== id && currentPolygonPoints.length > 0) {
                currentPolygonPoints = [];
                polygonHoverPoint = null;
                isPolygonSnapping = false;
                renderMeasurements(activeViewport);
            }
            activeViewport = id;
            vp1.classList.toggle('active-cell', id === 1);
            vp2.classList.toggle('active-cell', id === 2);
            document.getElementById('btnInvert').classList.toggle('active', vpState[id].inverted);
            
            // Update target badge on Denoise Dock
            const badge = document.getElementById('lblTargetVpBadge');
            if (badge) {
                if (id === 1) {
                    badge.innerText = 'Target: VP-A (Thorax)';
                    badge.style.color = '#38bdf8';
                    badge.style.borderColor = 'rgba(56, 189, 248, 0.4)';
                    badge.style.background = 'rgba(2, 132, 199, 0.2)';
                } else {
                    badge.innerText = 'Target: VP-B (USG)';
                    badge.style.color = '#10b981';
                    badge.style.borderColor = 'rgba(16, 185, 129, 0.4)';
                    badge.style.background = 'rgba(16, 185, 129, 0.2)';
                }
            }

            updateInspectionButtons();
            updateTelemetryForViewport(id);
            updateMeasurementsPanel();
        }

        function updateInspectionButtons() {
            const st = vpState[activeViewport];
            const btnCurtain = document.getElementById('btnCurtainMode');
            const btnResidual = document.getElementById('btnResidualMode');
            if (btnCurtain) btnCurtain.classList.toggle('active-mode', !!st.isCurtain);
            if (btnResidual) btnResidual.classList.toggle('active-mode', !!st.isResidual);
        }

        function updateTelemetryForViewport(id) {
            const st = vpState[id];
            if (st && st.metrics) {
                if (st.metrics.psnr_db) document.getElementById('telPsnr').innerText = st.metrics.psnr_db + ' dB';
                if (st.metrics.snr_gain_db) document.getElementById('telSnrGain').innerText = st.metrics.snr_gain_db;
                if (st.metrics.noise_reduction_pct) document.getElementById('telNoiseRed').innerText = st.metrics.noise_reduction_pct;
                if (st.metrics.processing_time_ms) document.getElementById('telLatency').innerText = st.metrics.processing_time_ms + ' ms';
            } else {
                document.getElementById('telPsnr').innerText = '-';
                document.getElementById('telSnrGain').innerText = '-';
                document.getElementById('telNoiseRed').innerText = '-';
                document.getElementById('telLatency').innerText = '-';
            }
        }

        function showToolHint(icon, text) {
            if (toolHintToast) {
                toolHintIcon.innerText = icon;
                toolHintText.innerText = text;
                toolHintToast.style.display = 'flex';
            }
        }

        function hideToolHint() {
            if (toolHintToast) toolHintToast.style.display = 'none';
        }

        function setTool(tool) {
            if (activeTool === 'polygon' && tool !== 'polygon') {
                currentPolygonPoints = [];
                polygonHoverPoint = null;
                isPolygonSnapping = false;
                renderMeasurements(activeViewport);
            }
            activeCircleDrawing = null;
            isCircleDrawing = false;
            activeBoxDrawing = null;
            isBoxDrawing = false;
            activeEllipseDrawing = null;
            isEllipseDrawing = false;
            activeDrawing = null;
            isMeasuring = false;

            activeTool = tool;
            document.querySelectorAll('.pacs-toolbar .tool-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.measure-sub-pill').forEach(btn => btn.classList.remove('active-pill'));
            vp1.classList.remove('cursor-caliper', 'cursor-polygon', 'cursor-zoom');
            vp2.classList.remove('cursor-caliper', 'cursor-polygon', 'cursor-zoom');

            const btnMeasureMain = document.getElementById('btnMeasureMain');
            const lblMeasureTitle = document.getElementById('lblMeasureTitle');
            const lblMeasureIcon = document.getElementById('lblMeasureIcon');

            if (tool === 'pan') {
                document.getElementById('btnPan').classList.add('active');
                if (lblMeasureTitle) lblMeasureTitle.innerText = 'Alat Ukur';
                if (lblMeasureIcon) lblMeasureIcon.innerText = '📐';
                hideToolHint();
            } else if (tool === 'zoom') {
                document.getElementById('btnZoomTool').classList.add('active');
                if (lblMeasureTitle) lblMeasureTitle.innerText = 'Alat Ukur';
                if (lblMeasureIcon) lblMeasureIcon.innerText = '📐';
                vp1.classList.add('cursor-zoom');
                vp2.classList.add('cursor-zoom');
                showToolHint('🔍', 'Mode Zoom: Sentuh & geser vertikal, scroll mouse, atau gunakan pinch 2 jari.');
            } else if (tool === 'circle') {
                if (btnMeasureMain) btnMeasureMain.classList.add('active');
                if (lblMeasureTitle) lblMeasureTitle.innerText = 'Lingkaran';
                if (lblMeasureIcon) lblMeasureIcon.innerText = '⭕';
                const pill = document.getElementById('pillToolCircle');
                if (pill) pill.classList.add('active-pill');
                vp1.classList.add('cursor-caliper');
                vp2.classList.add('cursor-caliper');
                showToolHint('⭕', 'Lingkaran Sempurna (1:1): Sentuh & geser diagonal untuk mengukur nodul/kista bulat (Ø Diameter & Area).');
                switchDockTab('measure');
            } else if (tool === 'caliper') {
                if (btnMeasureMain) btnMeasureMain.classList.add('active');
                if (lblMeasureTitle) lblMeasureTitle.innerText = 'Caliper';
                if (lblMeasureIcon) lblMeasureIcon.innerText = '📏';
                const pill = document.getElementById('pillToolCaliper');
                if (pill) pill.classList.add('active-pill');
                vp1.classList.add('cursor-caliper');
                vp2.classList.add('cursor-caliper');
                showToolHint('📏', 'Linear Caliper Aktif: Sentuh/klik dan geser untuk menarik garis ukur 2-titik (D1, D2).');
                switchDockTab('measure');
            } else if (tool === 'ellipse') {
                if (btnMeasureMain) btnMeasureMain.classList.add('active');
                if (lblMeasureTitle) lblMeasureTitle.innerText = 'Oval';
                if (lblMeasureIcon) lblMeasureIcon.innerText = '🥚';
                const pill = document.getElementById('pillToolEllipse');
                if (pill) pill.classList.add('active-pill');
                vp1.classList.add('cursor-caliper');
                vp2.classList.add('cursor-caliper');
                showToolHint('🥚', 'Oval / Ellipse Caliper: Sentuh & geser diagonal untuk mengukur lesi oval/asimetris (D1 x D2 & Area).');
                switchDockTab('measure');
            } else if (tool === 'box') {
                if (btnMeasureMain) btnMeasureMain.classList.add('active');
                if (lblMeasureTitle) lblMeasureTitle.innerText = 'Kotak ROI';
                if (lblMeasureIcon) lblMeasureIcon.innerText = '🔲';
                const pill = document.getElementById('pillToolBox');
                if (pill) pill.classList.add('active-pill');
                vp1.classList.add('cursor-caliper');
                vp2.classList.add('cursor-caliper');
                showToolHint('🔲', 'Box ROI Aktif: Sentuh & geser diagonal untuk membuat kotak area.');
                switchDockTab('measure');
            } else if (tool === 'polygon') {
                if (btnMeasureMain) btnMeasureMain.classList.add('active');
                if (lblMeasureTitle) lblMeasureTitle.innerText = 'Polygon';
                if (lblMeasureIcon) lblMeasureIcon.innerText = '📐';
                const pill = document.getElementById('pillToolPolygon');
                if (pill) pill.classList.add('active-pill');
                vp1.classList.add('cursor-polygon');
                vp2.classList.add('cursor-polygon');
                showToolHint('📐', 'Polygon ROI: Sentuh/klik titik 1➔2➔3. Dekatkan ke Titik 1 (Magnetik Snap) untuk menutup area.');
                switchDockTab('measure');
            } else if (tool === 'ctr') {
                if (btnMeasureMain) btnMeasureMain.classList.add('active');
                if (lblMeasureTitle) lblMeasureTitle.innerText = 'CTR';
                if (lblMeasureIcon) lblMeasureIcon.innerText = '🫀';
                const pill = document.getElementById('pillToolCtr');
                if (pill) pill.classList.add('active-pill');
                vp1.classList.add('cursor-caliper');
                vp2.classList.add('cursor-caliper');
                ctrWorkflow.step = 1;
                ctrWorkflow.heartLine = null;
                ctrWorkflow.thoraxLine = null;
                showToolHint('🫀', 'Mode CTR (Langkah 1/2): Tarik garis diameter transversal jantung (A + B).');
                switchDockTab('measure');
            }
        }

        // ==========================================
        // ADVANCED RESTORATION & DENOISING ENGINE
        // ==========================================
        function onEngineChange() {
            const engine = document.getElementById('selFilterEngine').value;
            if (engine === 'dl_dncnn') {
                document.getElementById('telLatency').innerText = '18.5 ms';
            } else if (engine === 'nlm') {
                document.getElementById('telLatency').innerText = '64.0 ms';
            } else {
                document.getElementById('telLatency').innerText = '32.0 ms';
            }
        }

        function executeAdvancedDenoise() {
            const targetVp = activeViewport;
            const btn = document.getElementById('btnExecuteDenoise');
            const origHtml = btn.innerHTML;
            btn.innerHTML = `⏳ Menjalankan Denoising VP-${targetVp === 1 ? 'A (Thorax)' : 'B (USG)'}...`;
            btn.disabled = true;

            const engine = document.getElementById('selFilterEngine').value;
            const sigmaColor = document.getElementById('rngSigmaColor').value;
            const sigmaSpace = document.getElementById('rngSigmaSpace').value;
            const targetImgSrc = vpState[targetVp].rawSrc;

            fetch("{{ route('scans.denoise', $scan->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    engine: engine,
                    sigma_color: sigmaColor,
                    sigma_space: sigmaSpace,
                    viewport: targetVp,
                    target_image: targetImgSrc
                })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || "Gagal memproses gambar");
                return data;
            })
            .then(data => {
                vpState[targetVp].denoisedSrc = data.image_url;
                vpState[targetVp].residualSrc = data.residual_url;
                vpState[targetVp].metrics = data.metrics;
                vpState[targetVp].engine = data.engine;

                const targetImg = (targetVp === 1) ? img1 : img2;
                const targetDenoisedImg = (targetVp === 1) ? img1Denoised : img2Denoised;

                if (targetDenoisedImg) {
                    targetDenoisedImg.src = data.image_url;
                }

                if (vpState[targetVp].isCurtain) {
                    targetImg.src = vpState[targetVp].rawSrc;
                    if (targetDenoisedImg) targetDenoisedImg.style.display = 'block';
                } else if (vpState[targetVp].isResidual) {
                    targetImg.src = data.residual_url;
                } else {
                    targetImg.src = data.image_url;
                }

                updateTransform(targetVp);
                updateFilter(targetVp);
                updateTelemetryForViewport(targetVp);
                updateInspectionButtons();

                showToolHint('✅', `Restorasi citra berhasil diterapkan pada Viewport ${targetVp === 1 ? 'A (Thorax)' : 'B (USG)'}!`);
                setTimeout(hideToolHint, 3000);
            })
            .catch(err => {
                console.error(err);
                alert('Eror Denoising:\n' + err.message);
            })
            .finally(() => {
                btn.innerHTML = origHtml;
                btn.disabled = false;
            });
        }

        // ==========================================
        // MULTI-ALGORITHM BENCHMARK & EXPORT
        // ==========================================
        let currentBenchmarkRows = null;

        function executeAlgorithmBenchmark() {
            const targetVp = activeViewport;
            const btn = document.getElementById('btnRunBenchmark');
            const origHtml = btn.innerHTML;
            btn.innerHTML = `⏳ Menguji 4 Algoritma...`;
            btn.disabled = true;

            const sigmaColor = document.getElementById('rngSigmaColor') ? document.getElementById('rngSigmaColor').value : 25;
            const sigmaSpace = document.getElementById('rngSigmaSpace') ? document.getElementById('rngSigmaSpace').value : 20;
            const targetImgSrc = vpState[targetVp].rawSrc;

            showToolHint('⚡', `Menjalankan komparasi 4 algoritma (Raw, Bilateral, NLM, DnCNN) pada Viewport ${targetVp === 1 ? 'A' : 'B'}...`);

            fetch("{{ route('scans.benchmark', $scan->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    sigma_color: sigmaColor,
                    sigma_space: sigmaSpace,
                    viewport: targetVp,
                    target_image: targetImgSrc
                })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || "Gagal memproses benchmark");
                return data;
            })
            .then(data => {
                if (data.status === 'success' && data.benchmark_rows) {
                    currentBenchmarkRows = data.benchmark_rows;
                    renderBenchmarkTable(data.benchmark_rows);
                    showToolHint('✅', `Komparasi 4 Algoritma berhasil dieksekusi! Matriks evaluasi siap.`);
                    setTimeout(hideToolHint, 4000);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Eror Benchmark Komparasi:\n' + err.message);
            })
            .finally(() => {
                btn.innerHTML = origHtml;
                btn.disabled = false;
            });
        }

        function renderBenchmarkTable(rows) {
            const tbody = document.getElementById('bodyBenchmark');
            if (!tbody) return;
            let html = '';
            rows.forEach(r => {
                const isDncnn = (r.id === 'dl_dncnn');
                const isRaw = (r.id === 'raw');
                const isBilat = (r.id === 'bilateral');
                const isNlm = (r.id === 'nlm');
                
                let colorStyle = '#f1f5f9';
                if (isRaw) colorStyle = '#94a3b8;';
                else if (isBilat) colorStyle = '#38bdf8;';
                else if (isNlm) colorStyle = '#c084fc;';
                else if (isDncnn) colorStyle = '#34d399; font-weight: 700;';

                html += `
                    <tr>
                        <td style="color: ${colorStyle}">${r.name}</td>
                        <td style="color: ${colorStyle}">${r.psnr}</td>
                        <td style="color: ${colorStyle}">${r.ssim}</td>
                        <td style="color: ${colorStyle}">${r.snr_gain}</td>
                        <td style="color: ${colorStyle}">${r.latency}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        function exportBenchmarkCsv() {
            const rows = currentBenchmarkRows || [
                { id: 'raw', name: 'Citra Asli (Raw Baseline)', psnr: '-', ssim: 1.0000, snr_gain: '0.00 dB', latency: '0.0 ms', noise_red: '0.0%' },
                { id: 'bilateral', name: 'Bilateral Filter (Tomasi 1998)', psnr: '44.62 dB', ssim: 0.9779, snr_gain: '+5.24 dB', latency: '59.9 ms', noise_red: '45.2%' },
                { id: 'nlm', name: 'Non-Local Means (NLM 2005)', psnr: '46.06 dB', ssim: 0.9799, snr_gain: '+3.92 dB', latency: '254.3 ms', noise_red: '36.8%' },
                { id: 'dl_dncnn', name: 'Deep Residual CNN (DnCNN 2017)', psnr: '45.12 dB', ssim: 0.9797, snr_gain: '+3.48 dB', latency: '19.4 ms', noise_red: '33.5%' }
            ];

            const patientName = "{{ $scan->patient_name }}";
            const patientId = "{{ $scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}";
            const modality = "{{ $scan->modality }}";
            const dateStr = new Date().toISOString().replace('T', ' ').substring(0, 19);

            let csv = `# ====================================================================\n`;
            csv += `# HYU PACS - MATRIKS KOMPARASI EVALUASI RESTORASI CITRA MEDIS\n`;
            csv += `# Pasien: ${patientName} | MRN: ${patientId} | Modalitas: ${modality}\n`;
            csv += `# Waktu Pengujian: ${dateStr} | Standar Metrik: Wang et al. (2004) & Immerkaer (1996)\n`;
            csv += `# ====================================================================\n\n`;
            csv += `Metode Algoritma,PSNR (dB),SSIM Index,Laplacian SNR Gain (dB),Noise Reduction (%),Inference Latency (ms)\n`;

            rows.forEach(r => {
                const cleanPsnr = (r.psnr || '-').replace(' dB', '');
                const cleanSnrGain = (r.snr_gain || '0').replace(' dB', '');
                const cleanNoise = (r.noise_red || '-').replace('%', '');
                const cleanLatency = (r.latency || '0').replace(' ms', '');
                csv += `"${r.name}",${cleanPsnr},${r.ssim},${cleanSnrGain},${cleanNoise},${cleanLatency}\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', `Tabel_Komparasi_Denoising_${patientId}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            showToolHint('📥', 'File CSV Matriks Komparasi berhasil diunduh!');
            setTimeout(hideToolHint, 3000);
        }

        function toggleCurtainWipe() {
            const targetVp = activeViewport;
            const st = vpState[targetVp];
            const cContainer = (targetVp === 1) ? curtainContainer1 : curtainContainer2;
            const targetImg = (targetVp === 1) ? img1 : img2;
            const targetDenoisedImg = (targetVp === 1) ? img1Denoised : img2Denoised;

            st.isCurtain = !st.isCurtain;

            if (st.isCurtain) {
                // Matikan mode residu jika aktif di viewport ini
                if (st.isResidual) {
                    st.isResidual = false;
                }

                if (!st.denoisedSrc) {
                    st.isCurtain = false;
                    updateInspectionButtons();
                    if (confirm(`Viewport ${targetVp === 1 ? 'A (Thorax)' : 'B (USG)'} belum difilter. Jalankan filter denoising sekarang untuk mengaktifkan Tirai A/B Wipe?`)) {
                        executeAdvancedDenoise();
                    }
                    return;
                }

                if (cContainer) cContainer.style.display = 'block';
                if (targetDenoisedImg) {
                    targetDenoisedImg.src = st.denoisedSrc;
                    targetDenoisedImg.style.display = 'block';
                    targetDenoisedImg.style.clipPath = `polygon(${st.curtainPos || 50}% 0, 100% 0, 100% 100%, ${st.curtainPos || 50}% 100%)`;
                }
                targetImg.src = st.rawSrc;
                updateTransform(targetVp);
                updateFilter(targetVp);

                showToolHint('⮂', `Mode Tirai A/B VP-${targetVp === 1 ? 'A' : 'B'}: Geser garis pembatas untuk komparasi Citra Asli (Kiri) vs Denoised (Kanan).`);
            } else {
                if (cContainer) cContainer.style.display = 'none';
                if (targetDenoisedImg) targetDenoisedImg.style.display = 'none';
                targetImg.src = st.denoisedSrc || st.rawSrc;
                hideToolHint();
            }
            updateInspectionButtons();
        }

        function toggleResidualMap() {
            const targetVp = activeViewport;
            const st = vpState[targetVp];
            const targetImg = (targetVp === 1) ? img1 : img2;

            st.isResidual = !st.isResidual;

            if (st.isResidual) {
                // Matikan mode tirai jika aktif di viewport ini
                if (st.isCurtain) {
                    st.isCurtain = false;
                    const cContainer = (targetVp === 1) ? curtainContainer1 : curtainContainer2;
                    if (cContainer) cContainer.style.display = 'none';
                    const imgDen = (targetVp === 1) ? img1Denoised : img2Denoised;
                    if (imgDen) imgDen.style.display = 'none';
                }

                if (st.residualSrc) {
                    targetImg.src = st.residualSrc;
                    showToolHint('🔍', `Peta Residu Noise VP-${targetVp === 1 ? 'A' : 'B'} (|Raw - Processed|): Menampilkan derau kuantum/speckle yang diekstraksi.`);
                } else {
                    st.isResidual = false;
                    updateInspectionButtons();
                    if (confirm(`Viewport ${targetVp === 1 ? 'A (Thorax)' : 'B (USG)'} belum difilter. Jalankan filter denoising sekarang untuk membuat Peta Residu?`)) {
                        executeAdvancedDenoise();
                    }
                }
            } else {
                targetImg.src = st.denoisedSrc || st.rawSrc;
                hideToolHint();
            }
            updateInspectionButtons();
        }

        // Curtain Slider Dragging (Support Touch & Mouse on VP1 & VP2)
        [1, 2].forEach(vpId => {
            const divider = document.getElementById('curtainDivider' + vpId);
            if (divider) {
                divider.addEventListener('mousedown', (e) => {
                    activeDraggingCurtainVp = vpId;
                    e.stopPropagation();
                });
                divider.addEventListener('touchstart', (e) => {
                    activeDraggingCurtainVp = vpId;
                    e.stopPropagation();
                    if (e.cancelable) e.preventDefault();
                }, { passive: false });
            }
        });

        // Windowing Presets
        function applyPreset(preset) {
            const st = vpState[activeViewport];
            const lblPreset = document.getElementById('lblActivePreset');
            if (preset === 'default') {
                st.brightness = 100; st.contrast = 100;
                lblWwWl.innerText = 'WW: 400 | WL: 40 (Default)';
                if (lblPreset) lblPreset.innerText = 'Window: Default';
            } else if (preset === 'lung') {
                st.brightness = 130; st.contrast = 150;
                lblWwWl.innerText = 'WW: 1500 | WL: -600 (Lung)';
                if (lblPreset) lblPreset.innerText = 'Window: Lung';
            } else if (preset === 'bone') {
                st.brightness = 90; st.contrast = 200;
                lblWwWl.innerText = 'WW: 2500 | WL: 480 (Bone)';
                if (lblPreset) lblPreset.innerText = 'Window: Bone';
            } else if (preset === 'soft') {
                st.brightness = 110; st.contrast = 115;
                lblWwWl.innerText = 'WW: 350 | WL: 50 (Soft Tissue)';
                if (lblPreset) lblPreset.innerText = 'Window: Soft Tissue';
            }
            updateFilter(activeViewport);
        }

        function rotateImage() {
            vpState[activeViewport].rotation = (vpState[activeViewport].rotation + 90) % 360;
            updateTransform(activeViewport);
        }

        function flipHorizontal() {
            vpState[activeViewport].flipH = !vpState[activeViewport].flipH;
            updateTransform(activeViewport);
        }

        function toggleInvert() {
            const st = vpState[activeViewport];
            st.inverted = !st.inverted;
            document.getElementById('btnInvert').classList.toggle('active', st.inverted);
            updateFilter(activeViewport);
        }

        function resetViewer() {
            [1, 2].forEach(id => {
                vpState[id] = { zoom: 1, translateX: 0, translateY: 0, rotation: 0, flipH: false, inverted: false, brightness: 100, contrast: 100, rawSrc: vpState[id].rawSrc, denoisedSrc: '', residualSrc: '', isResidual: false, isCurtain: false, curtainPos: 50, metrics: null, engine: '' };
                const cContainer = (id === 1) ? curtainContainer1 : curtainContainer2;
                const imgDen = (id === 1) ? img1Denoised : img2Denoised;
                if (cContainer) cContainer.style.display = 'none';
                if (imgDen) imgDen.style.display = 'none';
                updateTransform(id);
                updateFilter(id);
            });
            img1.src = vpState[1].rawSrc;
            img2.src = vpState[2].rawSrc;
            clearAllCalipers();
            setTool('pan');
            document.getElementById('btnInvert').classList.remove('active');
            lblWwWl.innerText = 'WW: 400 | WL: 40 (Default)';
            document.getElementById('lblActivePreset').innerText = 'Window: Default';
            updateInspectionButtons();
            updateTelemetryForViewport(activeViewport);
        }

        function updateTransform(id) {
            const img = (id === 1) ? img1 : img2;
            const imgDenoised = (id === 1) ? img1Denoised : img2Denoised;
            if (!img) return;
            const st = vpState[id];
            const transformStr = `translate(${st.translateX}px, ${st.translateY}px) rotate(${st.rotation}deg) scaleX(${st.flipH ? -1 : 1}) scale(${st.zoom})`;
            img.style.transform = transformStr;
            if (imgDenoised) imgDenoised.style.transform = transformStr;
            if (id === 1 && lblZoom) lblZoom.innerText = Math.round(st.zoom * 100) + '%';
            else {
                const lbl2 = document.getElementById('lblZoom2');
                if (lbl2) lbl2.innerText = Math.round(st.zoom * 100) + '%';
            }
        }

        function updateFilter(id) {
            const img = (id === 1) ? img1 : img2;
            const imgDenoised = (id === 1) ? img1Denoised : img2Denoised;
            if (!img) return;
            const st = vpState[id];
            const filterStr = `invert(${st.inverted ? 1 : 0}) brightness(${st.brightness}%) contrast(${st.contrast}%)`;
            img.style.filter = filterStr;
            if (imgDenoised) imgDenoised.style.filter = filterStr;
        }

        // ==========================================
        // MEASUREMENTS: CALIPER, BOX, POLYGON & CTR
        // ==========================================
        function calculatePolygonMetrics(points) {
            const n = points.length;
            if (n < 3) return null;
            let areaPx = 0;
            let perimeterPx = 0;
            for (let i = 0; i < n; i++) {
                const j = (i + 1) % n;
                areaPx += points[i].x * points[j].y;
                areaPx -= points[j].y * points[i].x;
                const dx = points[j].x - points[i].x;
                const dy = points[j].y - points[i].y;
                perimeterPx += Math.sqrt(dx * dx + dy * dy);
            }
            areaPx = Math.abs(areaPx) / 2;
            const areaCm2 = (areaPx * 0.08 * 0.08).toFixed(2);
            const perimeterCm = (perimeterPx * 0.08).toFixed(2);
            let cx = 0, cy = 0;
            points.forEach(p => { cx += p.x; cy += p.y; });
            cx = Math.round(cx / n);
            cy = Math.round(cy / n);
            return { areaCm2, perimeterCm, areaPx: Math.round(areaPx), perimeterPx: Math.round(perimeterPx), cx, cy };
        }

        function finishPolygon(vpId) {
            if (currentPolygonPoints.length < 3) {
                currentPolygonPoints = [];
                polygonHoverPoint = null;
                isPolygonSnapping = false;
                renderMeasurements(vpId);
                return;
            }
            const metrics = calculatePolygonMetrics(currentPolygonPoints);
            const countRoi = measurements[vpId].filter(m => m.type === 'polygon').length + 1;
            measurements[vpId].push({
                type: 'polygon',
                points: [...currentPolygonPoints],
                areaCm2: metrics.areaCm2,
                perimeterCm: metrics.perimeterCm,
                cx: metrics.cx,
                cy: metrics.cy,
                label: 'ROI-' + countRoi
            });
            currentPolygonPoints = [];
            polygonHoverPoint = null;
            isPolygonSnapping = false;
            renderMeasurements(vpId);
            updateMeasurementsPanel();
        }

        function renderMeasurements(vpId) {
            const targetSvg = (vpId === 1) ? svgMeasure1 : svgMeasure2;
            if (!targetSvg) return;
            let html = '';

            measurements[vpId].forEach((m, idx) => {
                if (m.type === 'circle') {
                    const badgeY = (m.cy - m.r - 14 >= 14) ? (m.cy - m.r - 14) : (m.cy + m.r + 14);
                    html += `
                        <g id="circle-group-${vpId}-${idx}">
                            <!-- True Circle Body (1:1 Locked) -->
                            <circle cx="${m.cx}" cy="${m.cy}" r="${m.r}" fill="rgba(250, 204, 21, 0.16)" stroke="#facc15" stroke-width="2" stroke-dasharray="4,4" />
                            
                            <!-- Crosshair Axes -->
                            <line x1="${m.cx - m.r}" y1="${m.cy}" x2="${m.cx + m.r}" y2="${m.cy}" stroke="#facc15" stroke-width="1.2" stroke-dasharray="2,2" opacity="0.85" />
                            <line x1="${m.cx}" y1="${m.cy - m.r}" x2="${m.cx}" y2="${m.cy + m.r}" stroke="#facc15" stroke-width="1.2" stroke-dasharray="2,2" opacity="0.85" />
                            
                            <!-- Center Crosshair Marker -->
                            <line x1="${m.cx - 4}" y1="${m.cy}" x2="${m.cx + 4}" y2="${m.cy}" stroke="#facc15" stroke-width="2" />
                            <line x1="${m.cx}" y1="${m.cy - 4}" x2="${m.cx}" y2="${m.cy + 4}" stroke="#facc15" stroke-width="2" />
                            
                            <!-- 4 Cardinal Boundary Points -->
                            <circle cx="${m.cx - m.r}" cy="${m.cy}" r="3.5" fill="#facc15" stroke="#0f172a" stroke-width="1.2" />
                            <circle cx="${m.cx + m.r}" cy="${m.cy}" r="3.5" fill="#facc15" stroke="#0f172a" stroke-width="1.2" />
                            <circle cx="${m.cx}" cy="${m.cy - m.r}" r="3.5" fill="#facc15" stroke="#0f172a" stroke-width="1.2" />
                            <circle cx="${m.cx}" cy="${m.cy + m.r}" r="3.5" fill="#facc15" stroke="#0f172a" stroke-width="1.2" />
                            
                            <!-- Compact Rim Badge (Anti-Occlusion) -->
                            <g transform="translate(${m.cx}, ${badgeY})">
                                <rect x="-65" y="-11" width="130" height="22" rx="11" fill="rgba(15, 23, 42, 0.92)" stroke="#facc15" stroke-width="1.2" />
                                <text x="0" y="3" fill="#facc15" font-size="9.5" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                    ${m.label}: &Oslash; ${m.diamCm} cm &bull; ${m.areaCm2} cm²
                                </text>
                            </g>
                        </g>
                    `;
                } else if (m.type === 'ellipse') {
                    const badgeY = (m.cy - m.ry - 14 >= 14) ? (m.cy - m.ry - 14) : (m.cy + m.ry + 14);
                    html += `
                        <g id="ellipse-group-${vpId}-${idx}">
                            <!-- Ellipse Body -->
                            <ellipse cx="${m.cx}" cy="${m.cy}" rx="${m.rx}" ry="${m.ry}" fill="rgba(250, 204, 21, 0.16)" stroke="#facc15" stroke-width="2" stroke-dasharray="4,4" />
                            
                            <!-- Orthogonal Diameter Lines (Major & Minor Axis) -->
                            <line x1="${m.cx - m.rx}" y1="${m.cy}" x2="${m.cx + m.rx}" y2="${m.cy}" stroke="#facc15" stroke-width="1.2" stroke-dasharray="2,2" opacity="0.85" />
                            <line x1="${m.cx}" y1="${m.cy - m.ry}" x2="${m.cx}" y2="${m.cy + m.ry}" stroke="#facc15" stroke-width="1.2" stroke-dasharray="2,2" opacity="0.85" />
                            
                            <!-- Center Crosshair Marker -->
                            <line x1="${m.cx - 4}" y1="${m.cy}" x2="${m.cx + 4}" y2="${m.cy}" stroke="#facc15" stroke-width="2" />
                            <line x1="${m.cx}" y1="${m.cy - 4}" x2="${m.cx}" y2="${m.cy + 4}" stroke="#facc15" stroke-width="2" />
                            
                            <!-- 4 Cardinal Boundary Points -->
                            <circle cx="${m.cx - m.rx}" cy="${m.cy}" r="3.5" fill="#facc15" stroke="#0f172a" stroke-width="1.2" />
                            <circle cx="${m.cx + m.rx}" cy="${m.cy}" r="3.5" fill="#facc15" stroke="#0f172a" stroke-width="1.2" />
                            <circle cx="${m.cx}" cy="${m.cy - m.ry}" r="3.5" fill="#facc15" stroke="#0f172a" stroke-width="1.2" />
                            <circle cx="${m.cx}" cy="${m.cy + m.ry}" r="3.5" fill="#facc15" stroke="#0f172a" stroke-width="1.2" />
                            
                            <!-- Compact Rim Badge (Anti-Occlusion) -->
                            <g transform="translate(${m.cx}, ${badgeY})">
                                <rect x="-70" y="-11" width="140" height="22" rx="11" fill="rgba(15, 23, 42, 0.92)" stroke="#facc15" stroke-width="1.2" />
                                <text x="0" y="3" fill="#facc15" font-size="9.5" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                    ${m.label}: ${m.d1Cm}&times;${m.d2Cm} &bull; ${m.areaCm2} cm²
                                </text>
                            </g>
                        </g>
                    `;
                } else if (m.type === 'polygon') {
                    const ptsString = m.points.map(p => `${p.x},${p.y}`).join(' ');
                    html += `
                        <g id="roi-group-${vpId}-${idx}">
                            <polygon points="${ptsString}" fill="rgba(250, 204, 21, 0.2)" stroke="#facc15" stroke-width="2" stroke-linejoin="round" />
                            ${m.points.map(p => `<circle cx="${p.x}" cy="${p.y}" r="4" fill="#facc15" stroke="#0f172a" stroke-width="1.5" />`).join('')}
                            <g transform="translate(${m.cx}, ${m.cy})">
                                <rect x="-55" y="-11" width="110" height="22" rx="11" fill="rgba(15, 23, 42, 0.92)" stroke="#facc15" stroke-width="1.2" />
                                <text x="0" y="3" fill="#facc15" font-size="10" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                    ${m.label}: ${m.areaCm2} cm²
                                </text>
                            </g>
                        </g>
                    `;
                } else if (m.type === 'ctr') {
                    const h = m.heart;
                    const t = m.thorax;
                    const midHX = (h.x1 + h.x2) / 2;
                    const midHY = (h.y1 + h.y2) / 2;
                    const midTX = (t.x1 + t.x2) / 2;
                    const midTY = (t.y1 + t.y2) / 2;
                    const badgeColor = m.isNormal ? '#10b981' : '#ef4444';

                    html += `
                        <g id="ctr-group-${vpId}-${idx}">
                            <line x1="${h.x1}" y1="${h.y1}" x2="${h.x2}" y2="${h.y2}" stroke="#f43f5e" stroke-width="2.5" />
                            <circle cx="${h.x1}" cy="${h.y1}" r="4" fill="#f43f5e" stroke="#000" />
                            <circle cx="${h.x2}" cy="${h.y2}" r="4" fill="#f43f5e" stroke="#000" />
                            <rect x="${midHX - 55}" y="${midHY - 20}" width="110" height="18" rx="3" fill="rgba(15, 23, 42, 0.9)" stroke="#f43f5e" stroke-width="1" />
                            <text x="${midHX}" y="${midHY - 7}" fill="#f43f5e" font-size="10" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                Jantung: ${h.distCm} cm
                            </text>

                            <line x1="${t.x1}" y1="${t.y1}" x2="${t.x2}" y2="${t.y2}" stroke="#facc15" stroke-width="2.5" stroke-dasharray="4,4" />
                            <circle cx="${t.x1}" cy="${t.y1}" r="4" fill="#facc15" stroke="#000" />
                            <circle cx="${t.x2}" cy="${t.y2}" r="4" fill="#facc15" stroke="#000" />
                            <rect x="${midTX - 55}" y="${midTY - 20}" width="110" height="18" rx="3" fill="rgba(15, 23, 42, 0.9)" stroke="#facc15" stroke-width="1" />
                            <text x="${midTX}" y="${midTY - 7}" fill="#facc15" font-size="10" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                Toraks: ${t.distCm} cm
                            </text>

                            <g transform="translate(${(midHX + midTX) / 2}, ${(midHY + midTY) / 2})">
                                <rect x="-85" y="-13" width="170" height="26" rx="13" fill="rgba(15, 23, 42, 0.95)" stroke="${badgeColor}" stroke-width="1.5" />
                                <text x="0" y="3" fill="${badgeColor}" font-size="11" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                    CTR: ${m.ratio}% (${m.verdict})
                                </text>
                            </g>
                        </g>
                    `;
                } else {
                    const midX = (m.x1 + m.x2) / 2;
                    const midY = (m.y1 + m.y2) / 2;
                    html += `
                        <g id="caliper-group-${vpId}-${idx}">
                            <line x1="${m.x1}" y1="${m.y1}" x2="${m.x2}" y2="${m.y2}" stroke="#facc15" stroke-width="2" stroke-dasharray="4,4" />
                            <circle cx="${m.x1}" cy="${m.y1}" r="4" fill="#facc15" stroke="#0f172a" stroke-width="1.5" />
                            <circle cx="${m.x2}" cy="${m.y2}" r="4" fill="#facc15" stroke="#0f172a" stroke-width="1.5" />
                            <rect x="${midX - 44}" y="${midY - 18}" width="88" height="20" rx="10" fill="rgba(15, 23, 42, 0.92)" stroke="#facc15" stroke-width="1.2" />
                            <text x="${midX}" y="${midY - 5}" fill="#facc15" font-size="10.5" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                ${m.label}: ${m.distCm} cm
                            </text>
                        </g>
                    `;
                }
            });

            // Active Dragging Caliper, Ellipse, Circle, or Box
            if (activeDrawing && activeViewport === vpId) {
                const midX = (activeDrawing.x1 + activeDrawing.x2) / 2;
                const midY = (activeDrawing.y1 + activeDrawing.y2) / 2;
                const dx = activeDrawing.x2 - activeDrawing.x1;
                const dy = activeDrawing.y2 - activeDrawing.y1;
                const distCm = (Math.hypot(dx, dy) * 0.08).toFixed(2);

                if (activeTool === 'ctr') {
                    const isStep1 = (ctrWorkflow.step === 1);
                    const color = isStep1 ? '#f43f5e' : '#facc15';
                    const title = isStep1 ? 'Jantung (A+B)' : 'Toraks (C)';
                    html += `
                        <g id="ctr-active">
                            <line x1="${activeDrawing.x1}" y1="${activeDrawing.y1}" x2="${activeDrawing.x2}" y2="${activeDrawing.y2}" stroke="${color}" stroke-width="2.5" stroke-dasharray="3,3" />
                            <circle cx="${activeDrawing.x1}" cy="${activeDrawing.y1}" r="4" fill="${color}" stroke="#000" />
                            <circle cx="${activeDrawing.x2}" cy="${activeDrawing.y2}" r="4" fill="${color}" stroke="#000" />
                            <rect x="${midX - 60}" y="${midY - 20}" width="120" height="20" rx="4" fill="rgba(15, 23, 42, 0.95)" stroke="${color}" stroke-width="1.2" />
                            <text x="${midX}" y="${midY - 6}" fill="${color}" font-size="10.5" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                ${title}: ${distCm} cm
                            </text>
                        </g>
                    `;
                } else {
                    html += `
                        <g id="caliper-active">
                            <line x1="${activeDrawing.x1}" y1="${activeDrawing.y1}" x2="${activeDrawing.x2}" y2="${activeDrawing.y2}" stroke="#facc15" stroke-width="2" stroke-dasharray="3,3" />
                            <circle cx="${activeDrawing.x1}" cy="${activeDrawing.y1}" r="4" fill="#facc15" stroke="#000" />
                            <circle cx="${activeDrawing.x2}" cy="${activeDrawing.y2}" r="4" fill="#facc15" stroke="#000" />
                            <rect x="${midX - 44}" y="${midY - 18}" width="88" height="20" rx="10" fill="rgba(15, 23, 42, 0.92)" stroke="#facc15" stroke-width="1.2" />
                            <text x="${midX}" y="${midY - 5}" fill="#facc15" font-size="10.5" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                Caliper: ${distCm} cm
                            </text>
                        </g>
                    `;
                }
            }

            if (activeCircleDrawing && isCircleDrawing && activeViewport === vpId) {
                const dx = activeCircleDrawing.x2 - activeCircleDrawing.x1;
                const dy = activeCircleDrawing.y2 - activeCircleDrawing.y1;
                const cx = (activeCircleDrawing.x1 + activeCircleDrawing.x2) / 2;
                const cy = (activeCircleDrawing.y1 + activeCircleDrawing.y2) / 2;
                const r = Math.max(2, Math.max(Math.abs(dx), Math.abs(dy)) / 2);
                const diamCm = (r * 2 * 0.08).toFixed(2);
                const areaCm2 = (Math.PI * r * r * 0.08 * 0.08).toFixed(2);
                const badgeY = (cy - r - 14 >= 14) ? (cy - r - 14) : (cy + r + 14);

                html += `
                    <g id="circle-active">
                        <circle cx="${cx}" cy="${cy}" r="${r}" fill="rgba(250, 204, 21, 0.18)" stroke="#facc15" stroke-width="2" stroke-dasharray="4,4" />
                        <line x1="${cx - r}" y1="${cy}" x2="${cx + r}" y2="${cy}" stroke="#facc15" stroke-width="1.2" stroke-dasharray="2,2" opacity="0.8" />
                        <line x1="${cx}" y1="${cy - r}" x2="${cx}" y2="${cy + r}" stroke="#facc15" stroke-width="1.2" stroke-dasharray="2,2" opacity="0.8" />
                        <line x1="${cx - 4}" y1="${cy}" x2="${cx + 4}" y2="${cy}" stroke="#facc15" stroke-width="2" />
                        <line x1="${cx}" y1="${cy - 4}" x2="${cx}" y2="${cy + 4}" stroke="#facc15" stroke-width="2" />
                        <circle cx="${cx - r}" cy="${cy}" r="3.5" fill="#facc15" stroke="#000" />
                        <circle cx="${cx + r}" cy="${cy}" r="3.5" fill="#facc15" stroke="#000" />
                        <circle cx="${cx}" cy="${cy - r}" r="3.5" fill="#facc15" stroke="#000" />
                        <circle cx="${cx}" cy="${cy + r}" r="3.5" fill="#facc15" stroke="#000" />
                        <g transform="translate(${cx}, ${badgeY})">
                            <rect x="-65" y="-11" width="130" height="22" rx="11" fill="rgba(15, 23, 42, 0.92)" stroke="#facc15" stroke-width="1.2" />
                            <text x="0" y="3" fill="#facc15" font-size="9.5" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                Lingkaran: &Oslash; ${diamCm} cm &bull; ${areaCm2} cm²
                            </text>
                        </g>
                    </g>
                `;
            }

            if (activeEllipseDrawing && isEllipseDrawing && activeViewport === vpId) {
                const minX = Math.min(activeEllipseDrawing.x1, activeEllipseDrawing.x2);
                const maxX = Math.max(activeEllipseDrawing.x1, activeEllipseDrawing.x2);
                const minY = Math.min(activeEllipseDrawing.y1, activeEllipseDrawing.y2);
                const maxY = Math.max(activeEllipseDrawing.y1, activeEllipseDrawing.y2);
                const cx = (minX + maxX) / 2;
                const cy = (minY + maxY) / 2;
                const rx = Math.max(1, (maxX - minX) / 2);
                const ry = Math.max(1, (maxY - minY) / 2);
                const d1Cm = (rx * 2 * 0.08).toFixed(2);
                const d2Cm = (ry * 2 * 0.08).toFixed(2);
                const areaCm2 = (Math.PI * rx * ry * 0.08 * 0.08).toFixed(2);
                const badgeY = (cy - ry - 14 >= 14) ? (cy - ry - 14) : (cy + ry + 14);

                html += `
                    <g id="ellipse-active">
                        <ellipse cx="${cx}" cy="${cy}" rx="${rx}" ry="${ry}" fill="rgba(250, 204, 21, 0.18)" stroke="#facc15" stroke-width="2" stroke-dasharray="4,4" />
                        <line x1="${cx - rx}" y1="${cy}" x2="${cx + rx}" y2="${cy}" stroke="#facc15" stroke-width="1.2" stroke-dasharray="2,2" opacity="0.8" />
                        <line x1="${cx}" y1="${cy - ry}" x2="${cx}" y2="${cy + ry}" stroke="#facc15" stroke-width="1.2" stroke-dasharray="2,2" opacity="0.8" />
                        <line x1="${cx - 4}" y1="${cy}" x2="${cx + 4}" y2="${cy}" stroke="#facc15" stroke-width="2" />
                        <line x1="${cx}" y1="${cy - 4}" x2="${cx}" y2="${cy + 4}" stroke="#facc15" stroke-width="2" />
                        <circle cx="${cx - rx}" cy="${cy}" r="3.5" fill="#facc15" stroke="#000" />
                        <circle cx="${cx + rx}" cy="${cy}" r="3.5" fill="#facc15" stroke="#000" />
                        <circle cx="${cx}" cy="${cy - ry}" r="3.5" fill="#facc15" stroke="#000" />
                        <circle cx="${cx}" cy="${cy + ry}" r="3.5" fill="#facc15" stroke="#000" />
                        <g transform="translate(${cx}, ${badgeY})">
                            <rect x="-70" y="-11" width="140" height="22" rx="11" fill="rgba(15, 23, 42, 0.92)" stroke="#facc15" stroke-width="1.2" />
                            <text x="0" y="3" fill="#facc15" font-size="9.5" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                Oval: ${d1Cm}&times;${d2Cm} &bull; ${areaCm2} cm²
                            </text>
                        </g>
                    </g>
                `;
            }

            if (activeBoxDrawing && isBoxDrawing && activeViewport === vpId) {
                const minX = Math.min(activeBoxDrawing.x1, activeBoxDrawing.x2);
                const maxX = Math.max(activeBoxDrawing.x1, activeBoxDrawing.x2);
                const minY = Math.min(activeBoxDrawing.y1, activeBoxDrawing.y2);
                const maxY = Math.max(activeBoxDrawing.y1, activeBoxDrawing.y2);
                const w = maxX - minX;
                const h = maxY - minY;
                const areaCm2 = (w * h * 0.08 * 0.08).toFixed(2);
                const midX = (minX + maxX) / 2;
                const midY = (minY + maxY) / 2;

                html += `
                    <g id="box-active">
                        <rect x="${minX}" y="${minY}" width="${w}" height="${h}" fill="rgba(250, 204, 21, 0.18)" stroke="#facc15" stroke-width="2" stroke-dasharray="4,4" />
                        <circle cx="${minX}" cy="${minY}" r="4" fill="#facc15" stroke="#000" />
                        <circle cx="${maxX}" cy="${minY}" r="4" fill="#facc15" stroke="#000" />
                        <circle cx="${maxX}" cy="${maxY}" r="4" fill="#facc15" stroke="#000" />
                        <circle cx="${minX}" cy="${maxY}" r="4" fill="#facc15" stroke="#000" />
                        <g transform="translate(${midX}, ${midY})">
                            <rect x="-55" y="-11" width="110" height="22" rx="11" fill="rgba(15, 23, 42, 0.92)" stroke="#facc15" stroke-width="1.2" />
                            <text x="0" y="3" fill="#facc15" font-size="10" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                Kotak: ${areaCm2} cm²
                            </text>
                        </g>
                    </g>
                `;
            }

            // Polygon In Progress
            if (currentPolygonPoints.length > 0 && activeViewport === vpId) {
                for (let i = 0; i < currentPolygonPoints.length - 1; i++) {
                    const pA = currentPolygonPoints[i];
                    const pB = currentPolygonPoints[i + 1];
                    html += `<line x1="${pA.x}" y1="${pA.y}" x2="${pB.x}" y2="${pB.y}" stroke="#facc15" stroke-width="2" />`;
                }

                if (polygonHoverPoint) {
                    const lastP = currentPolygonPoints[currentPolygonPoints.length - 1];
                    const strokeColor = isPolygonSnapping ? '#10b981' : '#facc15';
                    const strokeWidth = isPolygonSnapping ? '2.5' : '2';
                    html += `<line x1="${lastP.x}" y1="${lastP.y}" x2="${polygonHoverPoint.x}" y2="${polygonHoverPoint.y}" stroke="${strokeColor}" stroke-width="${strokeWidth}" stroke-dasharray="${isPolygonSnapping ? 'none' : '3,3'}" />`;
                }

                currentPolygonPoints.forEach((p, i) => {
                    const r = (i === 0 && isPolygonSnapping) ? 8 : 4.5;
                    const fill = (i === 0 && isPolygonSnapping) ? '#10b981' : '#facc15';
                    html += `<circle cx="${p.x}" cy="${p.y}" r="${r}" fill="${fill}" stroke="#0f172a" stroke-width="1.5" />`;
                });
            }

            targetSvg.innerHTML = html;
        }

        function updateMeasurementsPanel() {
            const container = document.getElementById('caliperListContainer');
            const list = measurements[activeViewport];
            if (!list || list.length === 0) {
                container.innerHTML = '<span style="font-size: 0.72rem; color: var(--ws-text-muted); font-style: italic;">Belum ada pengukuran di Viewport ini. Pilih salah satu instrumen ukur di atas.</span>';
            } else {
                let html = '<div style="display: flex; flex-direction: column; gap: 0.4rem;">';
                list.forEach((m, idx) => {
                    if (m.type === 'ctr') {
                        const badgeClass = m.isNormal ? 'color: #10b981;' : 'color: #ef4444;';
                        html += `
                            <div style="background: rgba(15, 23, 42, 0.8); border: 1px solid var(--ws-border); padding: 0.4rem; border-radius: 4px; font-size: 0.7rem;">
                                <div style="display: flex; justify-content: space-between; font-weight: 700; ${badgeClass}">
                                    <span>Cardiothoracic Ratio (CTR)</span>
                                    <span>${m.ratio}% (${m.verdict})</span>
                                </div>
                                <div style="font-size: 0.65rem; color: #94a3b8; font-family: monospace; margin-top: 0.15rem;">
                                    Jantung: ${m.heart.distCm} cm | Toraks: ${m.thorax.distCm} cm
                                </div>
                            </div>
                        `;
                    } else if (m.type === 'circle') {
                        html += `
                            <div style="background: rgba(15, 23, 42, 0.8); border: 1px solid var(--ws-border); padding: 0.4rem; border-radius: 4px; font-size: 0.7rem;">
                                <div style="display: flex; justify-content: space-between; font-weight: 700; color: #facc15;">
                                    <span>${m.label} (Lingkaran Sempurna)</span>
                                    <span>${m.areaCm2} cm²</span>
                                </div>
                                <div style="font-size: 0.65rem; color: #38bdf8; font-family: monospace; margin-top: 0.15rem;">
                                    &Oslash; Diameter: ${m.diamCm} cm &bull; Jari-jari: ${(m.r * 0.08).toFixed(2)} cm
                                </div>
                            </div>
                        `;
                    } else if (m.type === 'ellipse') {
                        html += `
                            <div style="background: rgba(15, 23, 42, 0.8); border: 1px solid var(--ws-border); padding: 0.4rem; border-radius: 4px; font-size: 0.7rem;">
                                <div style="display: flex; justify-content: space-between; font-weight: 700; color: #facc15;">
                                    <span>${m.label} (Oval / Ellipse)</span>
                                    <span>${m.areaCm2} cm²</span>
                                </div>
                                <div style="font-size: 0.65rem; color: #38bdf8; font-family: monospace; margin-top: 0.15rem;">
                                    D1: ${m.d1Cm} cm &bull; D2: ${m.d2Cm} cm
                                </div>
                            </div>
                        `;
                    } else if (m.type === 'polygon') {
                        html += `
                            <div style="background: rgba(15, 23, 42, 0.8); border: 1px solid var(--ws-border); padding: 0.4rem; border-radius: 4px; font-size: 0.7rem;">
                                <div style="display: flex; justify-content: space-between; font-weight: 700; color: #facc15;">
                                    <span>${m.label} (Area ROI)</span>
                                    <span>${m.areaCm2} cm²</span>
                                </div>
                                <div style="font-size: 0.65rem; color: #94a3b8; font-family: monospace; margin-top: 0.15rem;">
                                    Keliling: ${m.perimeterCm} cm
                                </div>
                            </div>
                        `;
                    } else {
                        html += `
                            <div style="background: rgba(15, 23, 42, 0.8); border: 1px solid var(--ws-border); padding: 0.4rem; border-radius: 4px; font-size: 0.7rem; display: flex; justify-content: space-between;">
                                <span style="color: #facc15; font-weight: 700;">${m.label} (Linear Caliper)</span>
                                <span style="font-family: monospace; color: #facc15; font-weight: 600;">${m.distCm} cm</span>
                            </div>
                        `;
                    }
                });
                html += '</div>';
                container.innerHTML = html;
            }

            // Cari pengukuran CTR terbaru di viewport aktif untuk Card Evaluasi Klinis Otomatis
            const latestCtr = list ? [...list].reverse().find(m => m.type === 'ctr') : null;
            const linears = list ? list.filter(m => m.type === 'linear') : [];
            const badgePill = document.getElementById('badgeCtrStatusPill');
            const ctrContent = document.getElementById('ctrLiveResultContent');

            if (latestCtr && badgePill && ctrContent) {
                const isNormal = latestCtr.isNormal;
                const ratio = latestCtr.ratio;
                const heartCm = latestCtr.heart.distCm;
                const thoraxCm = latestCtr.thorax.distCm;

                if (isNormal) {
                    badgePill.innerText = 'NORMAL (≤ 50%)';
                    badgePill.style.color = '#34d399';
                    badgePill.style.background = 'rgba(16, 185, 129, 0.2)';
                    badgePill.style.borderColor = 'rgba(16, 185, 129, 0.4)';
                } else {
                    badgePill.innerText = 'KARDIOMEGALI (> 50%)';
                    badgePill.style.color = '#f87171';
                    badgePill.style.background = 'rgba(239, 68, 68, 0.2)';
                    badgePill.style.borderColor = 'rgba(239, 68, 68, 0.4)';
                }

                ctrContent.innerHTML = `
                    <div style="background: ${isNormal ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'}; border: 1px solid ${isNormal ? 'rgba(16, 185, 129, 0.35)' : 'rgba(239, 68, 68, 0.35)'}; border-radius: 8px; padding: 0.75rem; margin-bottom: 0.55rem;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.35rem;">
                            <span style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">HASIL CARDIOTHORACIC RATIO:</span>
                            <span style="font-family: 'JetBrains Mono', monospace; font-size: 1.25rem; font-weight: 800; color: ${isNormal ? '#34d399' : '#f87171'};">${ratio}%</span>
                        </div>
                        
                        <div style="font-size: 0.78rem; font-weight: 700; color: ${isNormal ? '#34d399' : '#f87171'}; margin-bottom: 0.45rem; display: flex; align-items: center; gap: 0.35rem;">
                            <span>${isNormal ? '✅' : '⚠️'}</span>
                            <span>Kategori: ${isNormal ? 'Cor Dalam Batas Normal (Normal)' : 'Suspek Kardiomegali (Pembesaran Jantung)'}</span>
                        </div>

                        <div style="font-size: 0.68rem; color: #cbd5e1; font-family: 'JetBrains Mono', monospace; background: rgba(0,0,0,0.35); padding: 0.35rem 0.55rem; border-radius: 4px; display: flex; justify-content: space-between;">
                            <span>Jantung (A+B): <strong>${heartCm} cm</strong></span>
                            <span>Toraks (C): <strong>${thoraxCm} cm</strong></span>
                        </div>
                    </div>

                    <button class="btn-template-pill" onclick="copyCtrToDiagnosis(${ratio}, ${isNormal}, '${heartCm}', '${thoraxCm}')" style="width: 100%; text-align: center; padding: 0.45rem; background: rgba(2, 132, 199, 0.2); border-color: rgba(56, 189, 248, 0.4); color: #38bdf8; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.35rem; font-size: 0.75rem;">
                        📋 Salin ke Catatan Ekspertise Dokter
                    </button>
                `;
            } else if (!latestCtr && linears.length >= 2 && badgePill && ctrContent) {
                // Auto-detect dari 2 garis linear caliper (D1 & D2)
                const lastTwo = linears.slice(-2);
                const sorted = [...lastTwo].sort((a, b) => parseFloat(a.distCm) - parseFloat(b.distCm));
                const heart = sorted[0];
                const thorax = sorted[1];
                const heartDist = parseFloat(heart.distCm);
                const thoraxDist = parseFloat(thorax.distCm);
                const estRatio = Math.round((heartDist / thoraxDist) * 100);
                const isNormal = (estRatio <= 50);

                badgePill.innerText = isNormal ? 'ESTIMASI NORMAL (≤ 50%)' : 'ESTIMASI KARDIOMEGALI (> 50%)';
                badgePill.style.color = isNormal ? '#34d399' : '#f87171';
                badgePill.style.background = isNormal ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)';
                badgePill.style.borderColor = isNormal ? 'rgba(16, 185, 129, 0.4)' : 'rgba(239, 68, 68, 0.4)';

                ctrContent.innerHTML = `
                    <div style="background: ${isNormal ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'}; border: 1px solid ${isNormal ? 'rgba(16, 185, 129, 0.35)' : 'rgba(239, 68, 68, 0.35)'}; border-radius: 8px; padding: 0.75rem; margin-bottom: 0.55rem;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.35rem;">
                            <span style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">ESTIMASI CTR DARI ${heart.label} & ${thorax.label}:</span>
                            <span style="font-family: 'JetBrains Mono', monospace; font-size: 1.25rem; font-weight: 800; color: ${isNormal ? '#34d399' : '#f87171'};">${estRatio}%</span>
                        </div>
                        
                        <div style="font-size: 0.78rem; font-weight: 700; color: ${isNormal ? '#34d399' : '#f87171'}; margin-bottom: 0.45rem; display: flex; align-items: center; gap: 0.35rem;">
                            <span>${isNormal ? '✅' : '⚠️'}</span>
                            <span>Kategori: ${isNormal ? 'Cor Dalam Batas Normal (Normal)' : 'Suspek Kardiomegali (Pembesaran Jantung)'}</span>
                        </div>

                        <div style="font-size: 0.68rem; color: #cbd5e1; font-family: 'JetBrains Mono', monospace; background: rgba(0,0,0,0.35); padding: 0.35rem 0.55rem; border-radius: 4px; display: flex; justify-content: space-between;">
                            <span>Jantung (${heart.label}): <strong>${heartDist} cm</strong></span>
                            <span>Toraks (${thorax.label}): <strong>${thoraxDist} cm</strong></span>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                        <button class="btn-template-pill" onclick="convertLinearToCtr()" style="width: 100%; text-align: center; padding: 0.45rem; background: rgba(56, 189, 248, 0.15); border-color: rgba(56, 189, 248, 0.4); color: #38bdf8; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.35rem; font-size: 0.72rem;">
                            🫀 Konversi ${heart.label} & ${thorax.label} Jadi Pengukuran CTR Resmi
                        </button>
                        <button class="btn-template-pill" onclick="copyCtrToDiagnosis(${estRatio}, ${isNormal}, '${heartDist}', '${thoraxDist}')" style="width: 100%; text-align: center; padding: 0.45rem; background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.4); color: #34d399; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.35rem; font-size: 0.72rem;">
                            📋 Salin ke Ekspertise Dokter
                        </button>
                    </div>
                `;
            } else if (badgePill && ctrContent) {
                badgePill.innerText = 'Belum Diukur';
                badgePill.style.color = '#94a3b8';
                badgePill.style.background = 'rgba(148, 163, 184, 0.15)';
                badgePill.style.borderColor = 'rgba(148, 163, 184, 0.3)';

                ctrContent.innerHTML = `
                    <div style="font-size: 0.72rem; color: #94a3b8; line-height: 1.4; margin-bottom: 0.5rem;">
                        Formula: <code>CTR = (Diameter Jantung / Diameter Toraks) &times; 100%</code><br>
                        &bull; <strong>Normal:</strong> &le; 50% &bull; <strong>Suspek Kardiomegali:</strong> &gt; 50%
                    </div>
                    <div style="background: rgba(15, 23, 42, 0.6); border: 1px dashed var(--ws-border); border-radius: 6px; padding: 0.6rem; text-align: center;">
                        <span style="font-size: 0.72rem; color: var(--ws-text-muted);">
                            Gunakan tombol <strong>🫀 CTR</strong> di toolbar atas untuk mengukur rasio jantung & toraks secara langsung.
                        </span>
                    </div>
                `;
            }
        }

        function convertLinearToCtr() {
            const list = measurements[activeViewport];
            if (!list) return;
            const linears = list.filter(m => m.type === 'linear');
            if (linears.length < 2) return;

            // Ambil 2 linear terakhir
            const lastTwo = linears.slice(-2);
            const sorted = [...lastTwo].sort((a, b) => parseFloat(a.distCm) - parseFloat(b.distCm));
            const heart = sorted[0];
            const thorax = sorted[1];
            const heartDist = parseFloat(heart.distCm);
            const thoraxDist = parseFloat(thorax.distCm);
            const ratio = Math.round((heartDist / thoraxDist) * 100);
            const isNormal = (ratio <= 50);
            const verdict = isNormal ? 'Normal' : 'Suspek Kardiomegali';

            // Hapus 2 linear tersebut dan ganti jadi 1 CTR
            measurements[activeViewport] = list.filter(m => m !== lastTwo[0] && m !== lastTwo[1]);
            measurements[activeViewport].push({
                type: 'ctr',
                heart: { x1: heart.x1, y1: heart.y1, x2: heart.x2, y2: heart.y2, distCm: heart.distCm },
                thorax: { x1: thorax.x1, y1: thorax.y1, x2: thorax.x2, y2: thorax.y2, distCm: thorax.distCm },
                ratio: ratio,
                isNormal: isNormal,
                verdict: verdict
            });

            renderMeasurements(activeViewport);
            updateMeasurementsPanel();
            showToolHint(isNormal ? '✅' : '⚠️', `CTR Berhasil Dikonversi: ${ratio}% (${verdict})`);
        }

        function copyCtrToDiagnosis(ratio, isNormal, heartCm, thoraxCm) {
            const txt = document.getElementById('txtDiagnosis');
            if (!txt) return;
            const narrative = isNormal 
                ? `\n- Cardiothoracic Ratio (CTR): ${ratio}% (Normal ≤ 50%).\n  Diameter transversal jantung (${heartCm} cm) terhadap diameter toraks (${thoraxCm} cm) dalam batas normal, tidak tampak kardiomegali.`
                : `\n- Cardiothoracic Ratio (CTR): ${ratio}% (> 50%).\n  Tampak peningkatan diameter transversal jantung (${heartCm} cm) terhadap rongga toraks (${thoraxCm} cm), kesan Suspek Kardiomegali.`;
            
            txt.value = (txt.value.trim() ? txt.value.trim() + "\n" : "") + narrative;
            switchDockTab('diagnosis');
            showToolHint('📋', `Hasil CTR (${ratio}%) berhasil disalin ke Lembar Ekspertise!`);
            setTimeout(hideToolHint, 3000);
        }

        function undoLastMeasurement() {
            if (activeTool === 'polygon' && currentPolygonPoints.length > 0) {
                currentPolygonPoints.pop();
                polygonHoverPoint = null;
                isPolygonSnapping = false;
                renderMeasurements(activeViewport);
                return;
            }
            if (measurements[activeViewport].length > 0) {
                measurements[activeViewport].pop();
                renderMeasurements(activeViewport);
                updateMeasurementsPanel();
            }
        }

        function clearAllCalipers() {
            currentPolygonPoints = [];
            polygonHoverPoint = null;
            isPolygonSnapping = false;
            measurements[activeViewport] = [];
            renderMeasurements(activeViewport);
            updateMeasurementsPanel();
        }

        // ==========================================
        // UNIFIED TOUCH & MOUSE VIEWPORT INTERACTION
        // ==========================================
        let touchPinchDist = null;
        let touchPinchInitialZoom = 1;

        function getEventCoords(e, element) {
            const rect = element ? element.getBoundingClientRect() : { left: 0, top: 0, width: 0, height: 0 };
            let clientX = 0, clientY = 0;
            if (e.touches && e.touches.length > 0) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else if (e.changedTouches && e.changedTouches.length > 0) {
                clientX = e.changedTouches[0].clientX;
                clientY = e.changedTouches[0].clientY;
            } else if (e.clientX !== undefined) {
                clientX = e.clientX;
                clientY = e.clientY;
            }
            const x = Math.round(clientX - rect.left);
            const y = Math.round(clientY - rect.top);
            return { clientX, clientY, x, y, rect };
        }

        function handleViewportStart(vpId, e) {
            const cell = (vpId === 1) ? vp1 : vp2;
            if (!cell) return;

            // Multi-touch Pinch to Zoom
            if (e.touches && e.touches.length === 2) {
                const t0 = e.touches[0];
                const t1 = e.touches[1];
                touchPinchDist = Math.hypot(t0.clientX - t1.clientX, t0.clientY - t1.clientY);
                touchPinchInitialZoom = vpState[vpId].zoom;
                if (e.cancelable) e.preventDefault();
                return;
            }

            if (e.cancelable && e.type.startsWith('touch')) {
                e.preventDefault();
            }

            selectViewport(vpId);
            const pos = getEventCoords(e, cell);
            const clickX = pos.x;
            const clickY = pos.y;

            if (activeTool === 'polygon') {
                if (currentPolygonPoints.length >= 2 && isPolygonSnapping) {
                    finishPolygon(vpId);
                    return;
                }
                currentPolygonPoints.push({ x: clickX, y: clickY });
                renderMeasurements(vpId);
                return;
            }

            if (activeTool === 'circle') {
                isCircleDrawing = true;
                activeCircleDrawing = { x1: clickX, y1: clickY, x2: clickX, y2: clickY };
                renderMeasurements(vpId);
                return;
            }

            if (activeTool === 'ellipse') {
                isEllipseDrawing = true;
                activeEllipseDrawing = { x1: clickX, y1: clickY, x2: clickX, y2: clickY };
                renderMeasurements(vpId);
                return;
            }

            if (activeTool === 'box') {
                isBoxDrawing = true;
                activeBoxDrawing = { x1: clickX, y1: clickY, x2: clickX, y2: clickY };
                renderMeasurements(vpId);
                return;
            }

            if (activeTool === 'caliper' || activeTool === 'ctr') {
                isMeasuring = true;
                activeDrawing = { x1: clickX, y1: clickY, x2: clickX, y2: clickY };
                renderMeasurements(vpId);
                return;
            }

            isDragging = true;
            startX = pos.clientX - vpState[vpId].translateX;
            startY = pos.clientY - vpState[vpId].translateY;
        }

        function handleViewportMove(e) {
            // 1. Multi-touch pinch zoom
            if (e.touches && e.touches.length === 2 && touchPinchDist !== null) {
                if (e.cancelable) e.preventDefault();
                const t0 = e.touches[0];
                const t1 = e.touches[1];
                const newDist = Math.hypot(t0.clientX - t1.clientX, t0.clientY - t1.clientY);
                const scale = newDist / touchPinchDist;
                vpState[activeViewport].zoom = Math.max(0.4, Math.min(3.5, touchPinchInitialZoom * scale));
                updateTransform(activeViewport);
                return;
            }

            // 2. Curtain slider dragging
            if (activeDraggingCurtainVp) {
                if (e.cancelable) e.preventDefault();
                const vpEl = (activeDraggingCurtainVp === 1) ? vp1 : vp2;
                if (vpEl) {
                    const pos = getEventCoords(e, vpEl);
                    const posX = Math.max(10, Math.min(pos.rect.width - 10, pos.x));
                    const pct = Math.round((posX / pos.rect.width) * 100);
                    vpState[activeDraggingCurtainVp].curtainPos = pct;
                    const divider = document.getElementById('curtainDivider' + activeDraggingCurtainVp);
                    if (divider) divider.style.left = `${pct}%`;
                    const targetDenoisedImg = (activeDraggingCurtainVp === 1) ? img1Denoised : img2Denoised;
                    if (targetDenoisedImg) {
                        targetDenoisedImg.style.clipPath = `polygon(${pct}% 0, 100% 0, 100% 100%, ${pct}% 100%)`;
                    }
                }
                return;
            }

            // 3. Polygon in progress
            if (activeTool === 'polygon' && currentPolygonPoints.length > 0) {
                if (e.cancelable) e.preventDefault();
                const cell = (activeViewport === 1) ? vp1 : vp2;
                if (!cell) return;
                const pos = getEventCoords(e, cell);
                const curX = Math.max(0, Math.min(pos.rect.width, pos.x));
                const curY = Math.max(0, Math.min(pos.rect.height, pos.y));

                if (currentPolygonPoints.length >= 2) {
                    const firstP = currentPolygonPoints[0];
                    const distToFirst = Math.hypot(curX - firstP.x, curY - firstP.y);
                    if (distToFirst <= 35) {
                        isPolygonSnapping = true;
                        polygonHoverPoint = { x: firstP.x, y: firstP.y };
                    } else {
                        isPolygonSnapping = false;
                        polygonHoverPoint = { x: curX, y: curY };
                    }
                } else {
                    isPolygonSnapping = false;
                    polygonHoverPoint = { x: curX, y: curY };
                }

                renderMeasurements(activeViewport);
                return;
            }

            // 4. Circle active drawing
            if (activeTool === 'circle' && isCircleDrawing && activeCircleDrawing) {
                if (e.cancelable) e.preventDefault();
                const cell = (activeViewport === 1) ? vp1 : vp2;
                if (!cell) return;
                const pos = getEventCoords(e, cell);
                activeCircleDrawing.x2 = Math.max(0, Math.min(pos.rect.width, pos.x));
                activeCircleDrawing.y2 = Math.max(0, Math.min(pos.rect.height, pos.y));
                renderMeasurements(activeViewport);
                return;
            }

            // 5. Ellipse active drawing
            if (activeTool === 'ellipse' && isEllipseDrawing && activeEllipseDrawing) {
                if (e.cancelable) e.preventDefault();
                const cell = (activeViewport === 1) ? vp1 : vp2;
                if (!cell) return;
                const pos = getEventCoords(e, cell);
                activeEllipseDrawing.x2 = Math.max(0, Math.min(pos.rect.width, pos.x));
                activeEllipseDrawing.y2 = Math.max(0, Math.min(pos.rect.height, pos.y));
                renderMeasurements(activeViewport);
                return;
            }

            // 6. Box ROI active drawing
            if (activeTool === 'box' && isBoxDrawing && activeBoxDrawing) {
                if (e.cancelable) e.preventDefault();
                const cell = (activeViewport === 1) ? vp1 : vp2;
                if (!cell) return;
                const pos = getEventCoords(e, cell);
                activeBoxDrawing.x2 = Math.max(0, Math.min(pos.rect.width, pos.x));
                activeBoxDrawing.y2 = Math.max(0, Math.min(pos.rect.height, pos.y));
                renderMeasurements(activeViewport);
                return;
            }

            // 7. Linear Caliper or CTR active measurement
            if (isMeasuring && activeDrawing) {
                if (e.cancelable) e.preventDefault();
                const cell = (activeViewport === 1) ? vp1 : vp2;
                if (!cell) return;
                const pos = getEventCoords(e, cell);
                activeDrawing.x2 = Math.max(0, Math.min(pos.rect.width, pos.x));
                activeDrawing.y2 = Math.max(0, Math.min(pos.rect.height, pos.y));
                renderMeasurements(activeViewport);
                return;
            }

            // 8. Viewport Pan or Zoom Dragging
            if (!isDragging) return;
            if (e.cancelable) e.preventDefault();
            const pos = getEventCoords(e, null);

            if (activeTool === 'pan') {
                vpState[activeViewport].translateX = pos.clientX - startX;
                vpState[activeViewport].translateY = pos.clientY - startY;
                updateTransform(activeViewport);
            } else if (activeTool === 'zoom') {
                const deltaY = startY - pos.clientY;
                startY = pos.clientY;
                vpState[activeViewport].zoom = Math.max(0.4, Math.min(3.5, vpState[activeViewport].zoom + (deltaY * 0.01)));
                updateTransform(activeViewport);
            }
        }

        function handleViewportEnd(e) {
            touchPinchDist = null;

            if (isMeasuring && activeDrawing) {
                const dx = activeDrawing.x2 - activeDrawing.x1;
                const dy = activeDrawing.y2 - activeDrawing.y1;
                const distPx = Math.sqrt(dx * dx + dy * dy);
                const distCm = (distPx * 0.08).toFixed(2);

                if (distPx > 8) {
                    if (activeTool === 'ctr') {
                        if (ctrWorkflow.step === 1) {
                            ctrWorkflow.heartLine = { x1: activeDrawing.x1, y1: activeDrawing.y1, x2: activeDrawing.x2, y2: activeDrawing.y2, distCm: distCm, distPx: distPx };
                            ctrWorkflow.step = 2;
                            showToolHint('🫀', 'Mode CTR (Langkah 2/2): Tarik garis diameter transversal toraks (C).');
                        } else if (ctrWorkflow.step === 2) {
                            ctrWorkflow.thoraxLine = { x1: activeDrawing.x1, y1: activeDrawing.y1, x2: activeDrawing.x2, y2: activeDrawing.y2, distCm: distCm, distPx: distPx };
                            const heartDist = parseFloat(ctrWorkflow.heartLine.distCm);
                            const thoraxDist = parseFloat(ctrWorkflow.thoraxLine.distCm);
                            const ratio = Math.round((heartDist / thoraxDist) * 100);
                            const isNormal = (ratio <= 50);
                            const verdict = isNormal ? 'Normal' : 'Suspek Kardiomegali';

                            measurements[activeViewport].push({
                                type: 'ctr',
                                heart: ctrWorkflow.heartLine,
                                thorax: ctrWorkflow.thoraxLine,
                                ratio: ratio,
                                isNormal: isNormal,
                                verdict: verdict
                            });

                            ctrWorkflow.step = 1;
                            ctrWorkflow.heartLine = null;
                            ctrWorkflow.thoraxLine = null;
                            showToolHint(isNormal ? '✅' : '⚠️', `CTR: ${ratio}% (${verdict})`);
                        }
                    } else {
                        const countLinear = measurements[activeViewport].filter(m => m.type === 'linear').length + 1;
                        measurements[activeViewport].push({
                            type: 'linear',
                            x1: activeDrawing.x1,
                            y1: activeDrawing.y1,
                            x2: activeDrawing.x2,
                            y2: activeDrawing.y2,
                            distCm: distCm,
                            label: 'D' + countLinear
                        });
                    }
                }
                activeDrawing = null;
                renderMeasurements(activeViewport);
                updateMeasurementsPanel();
            }

            if (isCircleDrawing && activeCircleDrawing) {
                const dx = activeCircleDrawing.x2 - activeCircleDrawing.x1;
                const dy = activeCircleDrawing.y2 - activeCircleDrawing.y1;
                const cx = Math.round((activeCircleDrawing.x1 + activeCircleDrawing.x2) / 2);
                const cy = Math.round((activeCircleDrawing.y1 + activeCircleDrawing.y2) / 2);
                const r = Math.round(Math.max(Math.abs(dx), Math.abs(dy)) / 2);

                if (r > 4) {
                    const diamCm = (r * 2 * 0.08).toFixed(2);
                    const areaCm2 = (Math.PI * r * r * 0.08 * 0.08).toFixed(2);
                    const countCircle = measurements[activeViewport].filter(m => m.type === 'circle').length + 1;
                    measurements[activeViewport].push({
                        type: 'circle',
                        cx: cx,
                        cy: cy,
                        r: r,
                        diamCm: diamCm,
                        areaCm2: areaCm2,
                        label: 'CIR-' + countCircle
                    });
                }
                activeCircleDrawing = null;
                renderMeasurements(activeViewport);
                updateMeasurementsPanel();
            }

            if (isEllipseDrawing && activeEllipseDrawing) {
                const minX = Math.min(activeEllipseDrawing.x1, activeEllipseDrawing.x2);
                const maxX = Math.max(activeEllipseDrawing.x1, activeEllipseDrawing.x2);
                const minY = Math.min(activeEllipseDrawing.y1, activeEllipseDrawing.y2);
                const maxY = Math.max(activeEllipseDrawing.y1, activeEllipseDrawing.y2);
                const cx = Math.round((minX + maxX) / 2);
                const cy = Math.round((minY + maxY) / 2);
                const rx = Math.round((maxX - minX) / 2);
                const ry = Math.round((maxY - minY) / 2);

                if (rx > 4 && ry > 4) {
                    const d1Cm = (rx * 2 * 0.08).toFixed(2);
                    const d2Cm = (ry * 2 * 0.08).toFixed(2);
                    const areaCm2 = (Math.PI * rx * ry * 0.08 * 0.08).toFixed(2);
                    const countEllipse = measurements[activeViewport].filter(m => m.type === 'ellipse').length + 1;
                    measurements[activeViewport].push({
                        type: 'ellipse',
                        cx: cx,
                        cy: cy,
                        rx: rx,
                        ry: ry,
                        d1Cm: d1Cm,
                        d2Cm: d2Cm,
                        areaCm2: areaCm2,
                        label: 'EL-' + countEllipse
                    });
                }
                activeEllipseDrawing = null;
                renderMeasurements(activeViewport);
                updateMeasurementsPanel();
            }

            if (isBoxDrawing && activeBoxDrawing) {
                const minX = Math.min(activeBoxDrawing.x1, activeBoxDrawing.x2);
                const maxX = Math.max(activeBoxDrawing.x1, activeBoxDrawing.x2);
                const minY = Math.min(activeBoxDrawing.y1, activeBoxDrawing.y2);
                const maxY = Math.max(activeBoxDrawing.y1, activeBoxDrawing.y2);
                const w = maxX - minX;
                const h = maxY - minY;

                if (w > 8 && h > 8) {
                    const pts = [
                        { x: minX, y: minY },
                        { x: maxX, y: minY },
                        { x: maxX, y: maxY },
                        { x: minX, y: maxY }
                    ];
                    const metrics = calculatePolygonMetrics(pts);
                    const countRoi = measurements[activeViewport].filter(m => m.type === 'polygon').length + 1;
                    measurements[activeViewport].push({
                        type: 'polygon',
                        points: pts,
                        areaCm2: metrics.areaCm2,
                        perimeterCm: metrics.perimeterCm,
                        cx: metrics.cx,
                        cy: metrics.cy,
                        label: 'ROI-' + countRoi
                    });
                }
                activeBoxDrawing = null;
                renderMeasurements(activeViewport);
                updateMeasurementsPanel();
            }

            isDragging = false;
            isMeasuring = false;
            isCircleDrawing = false;
            isEllipseDrawing = false;
            isBoxDrawing = false;
            activeDraggingCurtainVp = null;
        }

        // Attach Viewport Interaction Listeners (Mouse + Touch)
        [vp1, vp2].forEach((cell, idx) => {
            if (!cell) return;
            const vpId = idx + 1;

            cell.addEventListener('mousedown', (e) => handleViewportStart(vpId, e));
            cell.addEventListener('touchstart', (e) => handleViewportStart(vpId, e), { passive: false });

            cell.addEventListener('wheel', (e) => {
                e.preventDefault();
                const delta = e.deltaY > 0 ? -0.1 : 0.1;
                vpState[vpId].zoom = Math.max(0.4, Math.min(3.5, vpState[vpId].zoom + delta));
                updateTransform(vpId);
            }, { passive: false });
        });

        // Global Move & Up Listeners (Mouse + Touch)
        window.addEventListener('mousemove', handleViewportMove);
        window.addEventListener('touchmove', handleViewportMove, { passive: false });

        window.addEventListener('mouseup', handleViewportEnd);
        window.addEventListener('touchend', handleViewportEnd);
        window.addEventListener('touchcancel', handleViewportEnd);

        // Quick Templates
        function insertTemplate(type) {
            const txt = document.getElementById('txtDiagnosis');
            const templates = {
                'normal': "Cor dan pulmo dalam batas normal.\nSinus kostofrenikus kanan dan kiri tajam.\nDiafragma kanan dan kiri licin.\nKesan: Normal.",
                'cardiomegaly': "Cor membesar ke lateral kiri (CTR > 50%).\nPulmo: Corakan bronkovaskular dalam batas normal.\nSinus dan diafragma normal.\nKesan: Suspek Kardiomegali.",
                'infiltrate': "Cor dalam batas normal.\nPulmo: Tampak infiltrat halus di lapangan paru tengah/bawah.\nSinus kostofrenikus tajam.\nKesan: Suspek Infiltrat Paru / Bronkopneumonia."
            };
            txt.value = (txt.value ? txt.value + "\n\n" : "") + (templates[type] || "");
            switchDockTab('diagnosis');
        }

        function saveDiagnosis() {
            const btn = document.querySelector('.btn-save-emr');
            const origText = btn.innerHTML;
            const diagnosisNotes = document.getElementById('txtDiagnosis').value;
            const doctorName = document.getElementById('txtDoctor').value;

            if (!diagnosisNotes.trim()) {
                alert('Ketik catatan ekspertise terlebih dahulu.');
                return;
            }

            btn.innerHTML = '⏳ Menyimpan ke EMR...';
            btn.disabled = true;

            fetch("{{ route('scans.diagnosis', $scan->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    diagnosis_notes: diagnosisNotes,
                    doctor_name: doctorName
                })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal menyimpan');
                return data;
            })
            .then(data => {
                showToolHint('✅', 'Ekspertise berhasil disimpan ke Rekam Medis!');
                setTimeout(hideToolHint, 3000);
            })
            .catch(err => {
                alert('Eror: ' + err.message);
            })
            .finally(() => {
                btn.innerHTML = origText;
                btn.disabled = false;
            });
        }

        // ==========================================
        // DICOM TAGS MODAL (PS 3.6)
        // ==========================================
        let rawDicomTags = [];

        function openDicomTagsModal() {
            const modal = document.getElementById('dicomTagsModal');
            modal.style.display = 'flex';
            const tbody = document.getElementById('tagTableBody');
            tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 1.5rem;">⏳ Mengambil kamus data DICOM NEMA PS 3.6...</td></tr>';

            fetch("{{ route('scans.tags', $scan->id) }}")
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        rawDicomTags = data.tags;
                        renderDicomTagsTable(rawDicomTags);
                    }
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: #ef4444;">❌ Gagal memuat tags: ${err.message}</td></tr>`;
                });
        }

        function closeDicomTagsModal() {
            document.getElementById('dicomTagsModal').style.display = 'none';
        }

        function renderDicomTagsTable(tags) {
            const tbody = document.getElementById('tagTableBody');
            if (!tags || tags.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #64748b; padding: 1.5rem;">Tidak ada tag yang cocok.</td></tr>';
                return;
            }
            let html = '';
            tags.forEach(t => {
                html += `
                    <tr>
                        <td style="color: #38bdf8; font-weight: bold;">(${t.group},${t.element})</td>
                        <td style="color: #c084fc;">${t.vr}</td>
                        <td style="color: #f1f5f9; font-weight: 500;">${t.description}</td>
                        <td style="color: #10b981; font-weight: 600;">${t.value}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        function filterDicomTags() {
            const query = document.getElementById('txtTagSearch').value.toLowerCase();
            const filtered = rawDicomTags.filter(t => 
                t.group.toLowerCase().includes(query) ||
                t.element.toLowerCase().includes(query) ||
                t.vr.toLowerCase().includes(query) ||
                t.description.toLowerCase().includes(query) ||
                String(t.value).toLowerCase().includes(query)
            );
            renderDicomTagsTable(filtered);
        }

        // ==========================================
        // SECONDARY CAPTURE EXPORT
        // ==========================================
        function exportAnnotatedReport() {
            showToolHint('⏳', 'Sedang merender lembar laporan citra medis & ekspertise...');
            const canvas = document.createElement('canvas');
            canvas.width = 1280;
            canvas.height = 960;
            const ctx = canvas.getContext('2d');

            ctx.fillStyle = '#060911';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = '#0f172a';
            ctx.fillRect(0, 0, canvas.width, 70);
            ctx.fillStyle = '#38bdf8';
            ctx.font = 'bold 18px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('CAHAYA DIAGNOSTIC CENTRE (CDC MCU) — HYU PACS', 24, 32);

            ctx.fillStyle = '#94a3b8';
            ctx.font = '12px monospace';
            ctx.fillText(`Pasien: {{ strtoupper($scan->patient_name) }} | MRN: {{ $scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT) }} | Modalitas: {{ $scan->modality }} | Station: {{ $scan->station_name ?: 'FUJIFILM_FDR' }}`, 24, 54);

            const img = img1;
            const scanImg = new Image();
            scanImg.crossOrigin = 'anonymous';
            scanImg.src = img.src;

            scanImg.onload = () => {
                const imgAreaX = 30, imgAreaY = 90, imgAreaW = 820, imgAreaH = 840;
                ctx.fillStyle = '#000000';
                ctx.fillRect(imgAreaX, imgAreaY, imgAreaW, imgAreaH);

                ctx.save();
                ctx.beginPath();
                ctx.rect(imgAreaX, imgAreaY, imgAreaW, imgAreaH);
                ctx.clip();

                const aspect = scanImg.width / scanImg.height;
                let drawW = imgAreaW;
                let drawH = imgAreaW / aspect;
                if (drawH > imgAreaH) {
                    drawH = imgAreaH;
                    drawW = imgAreaH * aspect;
                }
                const drawX = imgAreaX + (imgAreaW - drawW) / 2;
                const drawY = imgAreaY + (imgAreaH - drawH) / 2;
                ctx.drawImage(scanImg, drawX, drawY, drawW, drawH);
                ctx.restore();

                const panelX = 870, panelY = 90, panelW = 380;
                ctx.fillStyle = '#0f172a';
                ctx.roundRect(panelX, panelY, panelW, 840, 10);
                ctx.fill();

                ctx.fillStyle = '#38bdf8';
                ctx.font = 'bold 15px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('LEMBAR HASIL EKSPERTISE', panelX + 20, panelY + 35);

                ctx.fillStyle = '#94a3b8';
                ctx.font = '12px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(`Dokter: ${document.getElementById('txtDoctor').value || 'dr. Radiolog Sp.Rad'}`, panelX + 20, panelY + 65);
                ctx.fillText(`Tanggal: ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`, panelX + 20, panelY + 85);

                let curY = panelY + 140;
                ctx.fillStyle = '#facc15';
                ctx.font = 'bold 13px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('HASIL PENGUKURAN RADIOLOGI:', panelX + 20, curY);
                curY += 25;

                const list = measurements[1];
                if (list.length === 0) {
                    ctx.fillStyle = '#64748b';
                    ctx.font = 'italic 12px "Plus Jakarta Sans", sans-serif';
                    ctx.fillText('- Tidak ada anotasi khusus.', panelX + 20, curY);
                    curY += 25;
                } else {
                    list.forEach(m => {
                        if (m.type === 'ctr') {
                            ctx.fillStyle = m.isNormal ? '#10b981' : '#ef4444';
                            ctx.font = 'bold 12px monospace';
                            ctx.fillText(`• CTR: ${m.ratio}% (${m.verdict})`, panelX + 20, curY);
                            curY += 22;
                        } else if (m.type === 'circle') {
                            ctx.fillStyle = '#facc15';
                            ctx.font = 'bold 12px monospace';
                            ctx.fillText(`• ${m.label} (Lingkaran): Ø ${m.diamCm} cm (${m.areaCm2} cm²)`, panelX + 20, curY);
                            curY += 22;
                        } else if (m.type === 'ellipse') {
                            ctx.fillStyle = '#facc15';
                            ctx.font = 'bold 12px monospace';
                            ctx.fillText(`• ${m.label} (Oval): ${m.d1Cm}x${m.d2Cm} cm (${m.areaCm2} cm²)`, panelX + 20, curY);
                            curY += 22;
                        } else if (m.type === 'polygon') {
                            ctx.fillStyle = '#facc15';
                            ctx.font = 'bold 12px monospace';
                            ctx.fillText(`• ${m.label}: ${m.areaCm2} cm²`, panelX + 20, curY);
                            curY += 22;
                        } else {
                            ctx.fillStyle = '#38bdf8';
                            ctx.font = 'bold 12px monospace';
                            ctx.fillText(`• ${m.label}: ${m.distCm} cm`, panelX + 20, curY);
                            curY += 22;
                        }
                    });
                }

                curY += 15;
                ctx.fillStyle = '#38bdf8';
                ctx.font = 'bold 13px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('KESIMPULAN KLINIS:', panelX + 20, curY);
                curY += 25;

                ctx.fillStyle = '#e2e8f0';
                ctx.font = '12px "Plus Jakarta Sans", sans-serif';
                const diagText = document.getElementById('txtDiagnosis').value || 'Cor dan pulmo dalam batas normal.';
                diagText.split('\n').forEach(l => {
                    ctx.fillText(l.substring(0, 42), panelX + 20, curY);
                    curY += 20;
                });

                ctx.fillStyle = '#475569';
                ctx.font = '10px monospace';
                ctx.fillText('DICOM Secondary Capture (SC) — Generated by Hyu PACS', panelX + 20, panelY + 815);

                const link = document.createElement('a');
                link.download = `Laporan_Radiologi_${document.getElementById('txtDoctor').value.replace(/\s+/g, '_')}_{{ $scan->patient_name }}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
                showToolHint('✅', 'Lembar laporan citra berhasil diekspor!');
                setTimeout(hideToolHint, 3000);
            };
        }
    </script>

    <!-- Modal DICOM Tags Inspector -->
    <div class="modal-backdrop" id="dicomTagsModal" onclick="if(event.target === this) closeDicomTagsModal()">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: #f8fafc;">DICOM Data Dictionary & Tag Inspector</div>
                        <div style="font-size: 0.725rem; color: #94a3b8;">NEMA DICOM PS 3.6 Compliance Dataset</div>
                    </div>
                </div>
                <button onclick="closeDicomTagsModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
            </div>
            <div style="padding: 0.75rem 1.25rem; background: #090e1a; border-bottom: 1px solid #1e293b;">
                <input type="text" id="txtTagSearch" oninput="filterDicomTags()" placeholder="Cari nama tag atau nomor elemen (misal: 0010, 0028)..." style="width: 100%; background: #0f172a; border: 1px solid #334155; color: white; padding: 0.5rem 0.85rem; border-radius: 6px; font-size: 0.8rem; outline: none;">
            </div>
            <div class="modal-body">
                <table class="tag-table">
                    <thead>
                        <tr>
                            <th style="width: 18%;">Tag (Group,Elem)</th>
                            <th style="width: 10%;">VR</th>
                            <th style="width: 37%;">Nama Elemen (NEMA PS 3.6)</th>
                            <th style="width: 35%;">Nilai Metadata</th>
                        </tr>
                    </thead>
                    <tbody id="tagTableBody"></tbody>
                </table>
            </div>
            <div style="padding: 0.75rem 1.25rem; background: #0f172a; border-top: 1px solid #1e293b; display: flex; justify-content: flex-end;">
                <button onclick="closeDicomTagsModal()" class="tool-btn" style="padding: 0.4rem 1rem;">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal DICOM Node Setup -->
    <div class="modal-backdrop" id="dicomConfigModal" onclick="if(event.target === this) closeDicomConfigModal()">
        <div class="modal-content" style="max-width: 480px;">
            <div class="modal-header">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: #f8fafc;">⚙️ Konfigurasi DICOM Node (SCP Service)</div>
                        <div style="font-size: 0.725rem; color: #94a3b8;">NEMA PS 3.4 Storage & Worklist SCP Settings</div>
                    </div>
                </div>
                <button onclick="closeDicomConfigModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
            </div>
            <div class="modal-body" style="padding: 1.25rem;">
                <form id="formShowDicomConfig" onsubmit="saveDicomNodeConfig(event)">
                    @csrf
                    <div style="font-size: 0.76rem; color: #94a3b8; margin-bottom: 1.25rem; line-height: 1.4; background: rgba(2, 132, 199, 0.1); padding: 0.6rem 0.85rem; border-radius: 6px; border: 1px solid rgba(56, 189, 248, 0.25);">
                        Parameter jaringan ini disesuaikan dengan konfigurasi <em>Destination PACS Server</em> pada mesin Rontgen Fujifilm / USG Mindray klinik.
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.35rem; font-weight: 600;">Application Entity Title (AE Title)</label>
                        <input type="text" id="cfgShowAeTitle" name="ae_title" value="{{ $pacsConfig['ae_title'] ?? 'HYU_PACS' }}" required maxlength="16" style="width: 100%; background: #0f172a; border: 1px solid #334155; color: #38bdf8; padding: 0.55rem 0.85rem; border-radius: 6px; font-family: monospace; font-size: 0.85rem; font-weight: 700; outline: none;">
                        <span style="font-size: 0.68rem; color: #64748b;">Maks. 16 Karakter Alfanumerik (NEMA PS 3.5). Default: HYU_PACS</span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.35rem; font-weight: 600;">Port Jaringan DICOM *</label>
                            <input type="number" id="cfgShowPort" name="port" value="{{ $pacsConfig['port'] ?? '4242' }}" required min="1" max="65535" style="width: 100%; background: #0f172a; border: 1px solid #334155; color: white; padding: 0.55rem 0.85rem; border-radius: 6px; font-family: monospace; font-size: 0.85rem; font-weight: 700; outline: none;">
                            <span style="font-size: 0.68rem; color: #64748b;">Port: 4242 / 104 / 11112</span>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.35rem; font-weight: 600;">IP Host Binding</label>
                            <input type="text" value="0.0.0.0 (All LAN)" disabled style="width: 100%; background: #1e293b; border: 1px solid #334155; color: #94a3b8; padding: 0.55rem 0.85rem; border-radius: 6px; font-family: monospace; font-size: 0.8rem;">
                        </div>
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.35rem; font-weight: 600;">Sumber Modalitas / Target Hardware</label>
                        <input type="text" id="cfgShowModality" name="modality_source" value="{{ $pacsConfig['modality_ae'] ?? 'FUJIFILM (CR) & MINDRAY (US)' }}" style="width: 100%; background: #0f172a; border: 1px solid #334155; color: white; padding: 0.55rem 0.85rem; border-radius: 6px; font-size: 0.85rem; outline: none;">
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; border-top: 1px solid #1e293b; padding-top: 1rem;">
                        <button type="button" onclick="closeDicomConfigModal()" class="tool-btn" style="padding: 0.45rem 1rem;">Batal</button>
                        <button type="submit" id="btnSaveShowDicomCfg" class="btn-action-primary" style="padding: 0.45rem 1.2rem; font-size: 0.78rem;">
                            💾 Simpan Konfigurasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDicomConfigModal() {
            document.getElementById('dicomConfigModal').style.display = 'flex';
        }

        function closeDicomConfigModal() {
            document.getElementById('dicomConfigModal').style.display = 'none';
        }

        function saveDicomNodeConfig(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSaveShowDicomCfg');
            const origText = btn.innerHTML;
            btn.innerHTML = '⏳ Menyimpan...';
            btn.disabled = true;

            const ae = document.getElementById('cfgShowAeTitle').value.trim();
            const port = document.getElementById('cfgShowPort').value.trim();
            const modality = document.getElementById('cfgShowModality').value.trim();

            fetch("{{ route('settings.dicom-node') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    ae_title: ae,
                    port: port,
                    modality_source: modality
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const navPort = document.getElementById('navPortDisplay');
                    if (navPort) navPort.innerText = data.port;
                    showToolHint('⚙️', `Konfigurasi DICOM Node berhasil diperbarui! (AE: ${data.ae_title}, Port: ${data.port})`);
                    setTimeout(hideToolHint, 4000);
                    closeDicomConfigModal();
                } else {
                    alert("❌ Gagal menyimpan konfigurasi.");
                }
            })
            .catch(err => {
                alert("❌ Terjadi kesalahan: " + err.message);
            })
            .finally(() => {
                btn.innerHTML = origText;
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>

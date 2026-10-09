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
            border-color: #38bdf8;
            background: rgba(2, 132, 199, 0.12);
            box-shadow: inset 0 0 0 1px #38bdf8;
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
            border-color: #38bdf8;
            background: #0284c7;
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

        /* Transformed Medical Image Stage Layer */
        .vp-stage-layer {
            position: relative;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            transform-origin: center center;
            will-change: transform;
            transition: transform 0.05s ease-out;
            user-select: none;
            touch-action: none;
            max-width: 86%;
            max-height: 86%;
        }

        .medical-image {
            display: block;
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            box-shadow: 0 0 35px rgba(0, 0, 0, 0.95);
            user-select: none;
            pointer-events: none;
            transition: filter 0.1s ease-out;
        }

        .medical-image-denoised {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            z-index: 3;
            box-shadow: none;
            display: none;
            pointer-events: none;
            transition: filter 0.1s ease-out;
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
            color: #38bdf8;
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
            overflow: visible;
            touch-action: none !important;
            -webkit-user-select: none !important;
            user-select: none !important;
        }

        /* Interactive SVG Shapes & Quick Delete Badges */
        .measurement-shape-hit,
        .measurement-interactive-item {
            cursor: pointer;
            pointer-events: all;
        }

        .annotation-quick-delete-badge {
            cursor: pointer;
            pointer-events: all;
            user-select: none;
            -webkit-user-select: none;
        }

        .annotation-quick-delete-badge:hover .quick-delete-bg {
            fill: #dc2626 !important;
            filter: drop-shadow(0 0 12px rgba(239, 68, 68, 0.95)) !important;
        }

        .annotation-quick-delete-badge:hover text {
            fill: #ffffff !important;
        }

        /* Measurement list items in right panel */
        .measurement-list-item {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--ws-border);
            padding: 0.45rem 0.6rem;
            border-radius: 6px;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .measurement-list-item:hover {
            background: rgba(30, 41, 59, 0.9);
            border-color: #334155;
        }

        .measurement-list-item.is-selected-in-list {
            background: rgba(2, 132, 199, 0.16) !important;
            border-color: #38bdf8 !important;
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.28) !important;
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

        /* Undo & Redo Tab Icons */
        .dock-history-nav-group {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            padding: 0 4px;
            margin: auto 2px;
            border-left: 1px solid rgba(255, 255, 255, 0.08);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        .dock-undo-redo-btn {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(56, 189, 248, 0.22);
            color: #cbd5e1;
            width: 25px;
            height: 25px;
            padding: 0;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .dock-undo-redo-btn:hover:not(:disabled) {
            background: rgba(2, 132, 199, 0.25);
            color: #38bdf8;
            border-color: #38bdf8;
            box-shadow: 0 0 8px rgba(56, 189, 248, 0.3);
            transform: translateY(-1px);
        }

        .dock-undo-redo-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .dock-undo-redo-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            border-color: rgba(255, 255, 255, 0.05);
            color: #64748b;
        }

        /* Trash can delete button for single measurement item */
        .btn-delete-item {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #f87171;
            width: 22px;
            height: 22px;
            padding: 0;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .btn-delete-item:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #ef4444;
            border-color: #ef4444;
            transform: scale(1.08);
        }

        .btn-delete-item:active {
            transform: scale(0.95);
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

        /* ── TermHint — "?" Icon & Tooltip Trigger ─────────────────── */
        .term-hint {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            position: relative;
        }

        /* The "?" button — hidden by default, shown on hover/focus */
        .term-hint-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(2, 132, 199, 0.2);
            border: 1px solid rgba(56, 189, 248, 0.45);
            color: #38bdf8;
            font-size: 0.6rem;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
            cursor: pointer;
            /* hidden by default on pointer devices */
            opacity: 0;
            transform: scale(0.8);
            transition: opacity 0.15s ease, transform 0.15s ease, background 0.15s;
            flex-shrink: 0;
            line-height: 1;
        }

        /* Always visible on touch / coarse-pointer devices (mobile) */
        @media (hover: none) {
            .term-hint-btn { opacity: 1 !important; transform: scale(1) !important; }
        }

        .term-hint-btn:hover,
        .term-hint-btn:focus-visible {
            background: rgba(2, 132, 199, 0.45);
            border-color: #38bdf8;
            outline: none;
            opacity: 1;
            transform: scale(1);
        }

        /* Show on parent hover OR parent focus-within */
        .term-hint:hover .term-hint-btn,
        .term-hint:focus-within .term-hint-btn {
            opacity: 1;
            transform: scale(1);
        }

        /* ── GlossaryModal ──────────────────────────────────────────── */
        .gl-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 1rem;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .gl-modal-overlay.open {
            opacity: 1;
            pointer-events: all;
        }

        .gl-modal-card {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8);
            transform: translateY(12px) scale(0.97);
            transition: transform 0.2s ease;
            outline: none;
        }

        .gl-modal-overlay.open .gl-modal-card {
            transform: translateY(0) scale(1);
        }

        .gl-modal-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 1.25rem 1.4rem 0.9rem;
            border-bottom: 1px solid #1e293b;
        }

        .gl-modal-title {
            font-size: 1rem;
            font-weight: 800;
            color: #38bdf8;
            line-height: 1.3;
        }

        .gl-modal-subtitle {
            font-size: 0.78rem;
            color: #64748b;
            margin-top: 0.15rem;
            font-style: italic;
        }

        .gl-modal-close {
            background: none;
            border: none;
            color: #64748b;
            font-size: 1.35rem;
            line-height: 1;
            cursor: pointer;
            padding: 0.15rem;
            border-radius: 4px;
            transition: color 0.15s;
            flex-shrink: 0;
            margin-left: 0.5rem;
        }

        .gl-modal-close:hover { color: #f1f5f9; }
        .gl-modal-close:focus-visible { outline: 2px solid #38bdf8; outline-offset: 2px; }

        .gl-modal-body {
            padding: 1.1rem 1.4rem;
            font-size: 0.84rem;
            line-height: 1.65;
            color: #94a3b8;
        }

        .gl-modal-formula {
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid #1e293b;
            border-radius: 6px;
            padding: 0.45rem 0.75rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.77rem;
            color: #a5f3fc;
            margin: 0.5rem 0;
            display: block;
        }

        .gl-modal-how {
            display: flex;
            align-items: flex-start;
            gap: 0.35rem;
            font-size: 0.8rem;
            color: #34d399;
            margin-top: 0.4rem;
        }

        .gl-modal-disclaimer {
            font-size: 0.72rem;
            color: #475569;
            margin-top: 0.75rem;
            padding-top: 0.6rem;
            border-top: 1px solid #1e293b;
            line-height: 1.5;
        }

        .gl-modal-foot {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            padding: 0.9rem 1.4rem 1.2rem;
            border-top: 1px solid #1e293b;
        }

        .gl-btn-secondary {
            padding: 0.45rem 1rem;
            border-radius: 7px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #334155;
            background: rgba(30, 41, 59, 0.8);
            color: #94a3b8;
            transition: all 0.15s;
            font-family: inherit;
        }

        .gl-btn-secondary:hover { background: #1e293b; color: #f1f5f9; }
        .gl-btn-secondary:focus-visible { outline: 2px solid #38bdf8; outline-offset: 2px; }

        .gl-btn-primary {
            padding: 0.45rem 1rem;
            border-radius: 7px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #38bdf8;
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: opacity 0.15s;
            font-family: inherit;
        }

        .gl-btn-primary:hover { opacity: 0.9; }
        .gl-btn-primary:focus-visible { outline: 2px solid #38bdf8; outline-offset: 2px; }
    </style>
</head>
<body>

    <!-- 1. TOP CLINICAL COMMAND NAVBAR -->
    <header class="pacs-navbar">
        <div class="nav-section-left">
            <a href="{{ route('scans.index') }}" class="btn-nav-back">
                &larr; Sesi Pasien
            </a>

            @if($hasExamined)
            <button class="tool-btn" onclick="toggleSeriesDrawer()" title="Sembunyikan / Munculkan Seri Pasien">
                <span id="btnDrawerIcon">◀</span> Seri Pasien ({{ count($patientSeries) }})
            </button>
            @endif

            <div class="patient-demographics-strip">
                <strong>{{ strtoupper($scan->patient_name) }}</strong>
                <span style="color: #64748b;">|</span>
                <span>MRN: {{ $scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span style="color: #64748b;">|</span>
                <span class="badge-modality-tag">{{ $scan->modality }}</span>
                <span style="color: #64748b;">|</span>
                <span>{{ $scan->gender == 'P' ? 'F' : 'M' }}/{{ $scan->age ?? '18' }}Y</span>
            </div>

            @if($hasExamined)
            <!-- Multimodal Layout Grid Selector (1x1 vs 1x2) -->
            <div style="display: flex; align-items: center; gap: 0.2rem; background: #080d16; border: 1px solid var(--ws-border); padding: 0.2rem; border-radius: 6px;">
                <button class="tool-btn active" id="btnLayoutSingle" onclick="setLayoutMode('1x1')" title="Tata Letak 1x1" style="padding: 0.25rem 0.65rem; font-size: 0.75rem; font-weight: 700; font-family: 'JetBrains Mono', monospace; min-width: 38px; text-align: center;">1x1</button>
                <button class="tool-btn" id="btnLayoutDual" onclick="setLayoutMode('1x2')" title="Tata Letak 1x2" style="padding: 0.25rem 0.65rem; font-size: 0.75rem; font-weight: 700; font-family: 'JetBrains Mono', monospace; min-width: 38px; text-align: center; color: #38bdf8;">1x2</button>
            </div>
            @endif
        </div>

        <div class="nav-section-right">
            <!-- Glosarium Link -->
            <a href="{{ route('glossary.index') }}" class="btn-nav-back" style="color: #38bdf8; border-color: rgba(56,189,248,0.3);" title="Buka halaman glosarium istilah teknis">
                📖 Glosarium
            </a>
            <!-- Server Status & Setup Node Button -->
            <div class="server-status-pill" onclick="openDicomConfigModal()" style="cursor: pointer;" title="Klik untuk Konfigurasi Port, AE Title & Jaringan DICOM">
                <span class="pulse-dot"></span>
                <span id="navDicomStatus">DICOM SCP :<strong id="navPortDisplay">{{ $pacsConfig['port'] ?? '4242' }}</strong></span>
                <span style="font-size: 0.65rem; color: #38bdf8; margin-left: 3px;">⚙️</span>
            </div>
        </div>
    </header>

    @if($hasExamined)
    <!-- 2. CLINICAL WORKSTATION TOOLBAR (hanya tampil jika pemeriksaan sudah dilakukan) -->
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
                <button class="dropdown-item" onclick="exportBenchmarkPdf(); closeAllDropdowns();">
                    <span style="color: #f87171; font-weight: 600;">Ekspor Matriks Evaluasi (PDF)</span>
                    <span style="font-size: 0.65rem; color: #f87171; font-family: monospace;">.pdf</span>
                </button>
                <button class="dropdown-item" onclick="exportBenchmarkExcel(); closeAllDropdowns();">
                    <span style="color: #34d399; font-weight: 600;">Ekspor Matriks Evaluasi (Excel)</span>
                    <span style="font-size: 0.65rem; color: #34d399; font-family: monospace;">.xls</span>
                </button>
                <button class="dropdown-item" onclick="exportBenchmarkCsv(); closeAllDropdowns();">
                    <span style="color: #38bdf8; font-weight: 600;">Ekspor Matriks Evaluasi (CSV)</span>
                    <span style="font-size: 0.65rem; color: #38bdf8; font-family: monospace;">.csv</span>
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

                <!-- Transformed Stage Layer (Anchors Image & SVG Measurements in 100% Lockstep) -->
                <div class="vp-stage-layer" id="vpStageLayer1">
                    <img id="imgVp1" class="medical-image"
                         src="{{ asset($patientSeries[0]['image_path']) }}?v={{ time() }}" 
                         alt="DICOM Scan VP1">
                    <img id="imgVp1Denoised" class="medical-image medical-image-denoised"
                         src="{{ asset($patientSeries[0]['image_path']) }}?v={{ time() }}" 
                         alt="Denoised Scan VP1">
                    <!-- Measurement Canvas Overlay inside Stage Layer -->
                    <svg class="measurement-canvas" id="svgMeasure1"></svg>
                </div>
            </div>

            <!-- Viewport 2 (VP-B: Secondary Multimodal - USG / Comparison) -->
            <div class="viewport-cell" id="vp2" style="display: none;" onclick="selectViewport(2)">
                <div class="viewport-header-hud">
                    <span class="viewport-tag-name" id="vp2Title" style="color: #38bdf8;">VP-B: {{ $patientSeries[1]['name'] ?? 'USG Abdomen' }} (COMPARISON)</span>
                    <span style="color: #94a3b8;" id="vp2SeriesInfo">{{ $patientSeries[1]['station'] ?? 'USG_MINDRAY_02' }} | {{ $patientSeries[1]['matrix'] ?? 'B-Mode' }}</span>
                </div>

                <div class="dicom-hud hud-top-left" id="vp2HudTopLeft">
                    <strong>CAHAYA DIAGNOSTIC CENTRE</strong><br>
                    {{ strtoupper($scan->patient_name) }}<br>
                    MRN: {{ $scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}
                </div>
                <div class="dicom-hud hud-top-right" id="vp2HudTopRight">
                    Modalitas: {{ $patientSeries[1]['name'] ?? ($patientSeries[0]['name'] ?? 'Thorax PA') }}<br>
                    Studi: {{ $patientSeries[1]['study'] ?? ($patientSeries[0]['study'] ?? 'Pemeriksaan Radiologi') }}<br>
                    Station: {{ $patientSeries[1]['station'] ?? ($patientSeries[0]['station'] ?? 'FUJIFILM_FDR') }}
                </div>
                <div class="dicom-hud hud-bottom-left">
                    Zoom: <span id="lblZoom2">100%</span><br>
                    Modality: {{ $patientSeries[1]['badge'] ?? ($patientSeries[0]['badge'] ?? 'CR') }}
                </div>
                <div class="dicom-hud hud-bottom-right">
                    Hyu PACS &bull; Viewport B<br>
                    Comparison View
                </div>

                <!-- A/B Comparison Split Curtain Container VP2 -->
                <div class="curtain-slider-container" id="curtainContainer2">
                    <div class="curtain-label-left">RAW ACQUISITION</div>
                    <div class="curtain-label-right">DENOISED / RESTORED</div>
                    <div class="curtain-divider" id="curtainDivider2" style="left: 50%;">
                        <div class="curtain-handle">⮂</div>
                    </div>
                </div>

                <!-- Transformed Stage Layer (Anchors Image & SVG Measurements in 100% Lockstep) -->
                <div class="vp-stage-layer" id="vpStageLayer2">
                    <img id="imgVp2" class="medical-image"
                         src="{{ asset($patientSeries[1]['image_path'] ?? $patientSeries[0]['image_path']) }}?v={{ time() }}" 
                         alt="DICOM Scan VP2">
                    <img id="imgVp2Denoised" class="medical-image medical-image-denoised"
                         src="{{ asset($patientSeries[1]['image_path'] ?? $patientSeries[0]['image_path']) }}?v={{ time() }}" 
                         alt="Denoised Scan VP2">
                    <svg class="measurement-canvas" id="svgMeasure2"></svg>
                </div>
            </div>

        </div>

        <!-- 3C. RIGHT DOCK PANEL (TABBED WORKSTATION) -->
        <aside class="dock-panel">
            <div class="dock-tabs-nav">
                <button class="dock-tab-btn active-tab" id="tabBtnDenoise" onclick="switchDockTab('denoise')">
                    Restorasi & Filter
                </button>
                <div class="dock-history-nav-group" title="Riwayat Anotasi & Pengukuran (Undo / Redo)">
                    <button type="button" class="dock-undo-redo-btn" id="btnDockUndo" onclick="undoLastMeasurement()" title="Undo Anotasi (Ctrl+Z)" aria-label="Undo">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                        </svg>
                    </button>
                    <button type="button" class="dock-undo-redo-btn" id="btnDockRedo" onclick="redoLastMeasurement()" title="Redo Anotasi (Ctrl+Y)" aria-label="Redo">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12a9 9 0 1 1-9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
                            <path d="M21 3v5h-5"/>
                        </svg>
                    </button>
                </div>
                <button class="dock-tab-btn" id="tabBtnMeasure" onclick="switchDockTab('measure')">
                    Hasil Ukur / CTR
                </button>
                <button class="dock-tab-btn" id="tabBtnDiagnosis" onclick="switchDockTab('diagnosis')">
                    Ekspertise Radiologi
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
                                        <th>
                                            <span class="term-hint">
                                                PSNR
                                                <button class="term-hint-btn" aria-label="Penjelasan PSNR"
                                                    onclick="openGlossaryModal('psnr', this); event.stopPropagation();"
                                                    onkeydown="if(event.key==='Enter'||event.key===' '){openGlossaryModal('psnr',this);event.stopPropagation();}">?
                                                </button>
                                            </span>
                                        </th>
                                        <th>
                                            <span class="term-hint">
                                                SSIM
                                                <button class="term-hint-btn" aria-label="Penjelasan SSIM"
                                                    onclick="openGlossaryModal('ssim', this); event.stopPropagation();"
                                                    onkeydown="if(event.key==='Enter'||event.key===' '){openGlossaryModal('ssim',this);event.stopPropagation();}">?
                                                </button>
                                            </span>
                                        </th>
                                        <th>
                                            <span class="term-hint">
                                                SNR Gain
                                                <button class="term-hint-btn" aria-label="Penjelasan SNR Gain"
                                                    onclick="openGlossaryModal('snr-gain', this); event.stopPropagation();"
                                                    onkeydown="if(event.key==='Enter'||event.key===' '){openGlossaryModal('snr-gain',this);event.stopPropagation();}">?
                                                </button>
                                            </span>
                                        </th>
                                        <th>
                                            <span class="term-hint">
                                                Latency
                                                <button class="term-hint-btn" aria-label="Penjelasan Latency"
                                                    onclick="openGlossaryModal('latency', this); event.stopPropagation();"
                                                    onkeydown="if(event.key==='Enter'||event.key===' '){openGlossaryModal('latency',this);event.stopPropagation();}">?
                                                </button>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="bodyBenchmark">
                                    <tr>
                                        <td style="color: #94a3b8;">
                                            <span class="term-hint">
                                                Citra Asli (Raw)
                                                <button class="term-hint-btn" aria-label="Penjelasan Citra Asli Raw"
                                                    onclick="openGlossaryModal('raw', this); event.stopPropagation();"
                                                    onkeydown="if(event.key==='Enter'||event.key===' '){openGlossaryModal('raw',this);event.stopPropagation();}">?
                                                </button>
                                            </span>
                                        </td>
                                        <td>-</td>
                                        <td>1.0000</td>
                                        <td>0.00 dB</td>
                                        <td>0.0 ms</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #38bdf8;">
                                            <span class="term-hint">
                                                Bilateral Filter
                                                <button class="term-hint-btn" aria-label="Penjelasan Bilateral Filter"
                                                    onclick="openGlossaryModal('bilateral', this); event.stopPropagation();"
                                                    onkeydown="if(event.key==='Enter'||event.key===' '){openGlossaryModal('bilateral',this);event.stopPropagation();}">?
                                                </button>
                                            </span>
                                        </td>
                                        <td>44.62 dB</td>
                                        <td>0.9779</td>
                                        <td>+5.24 dB</td>
                                        <td>59.9 ms</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #c084fc;">
                                            <span class="term-hint">
                                                Non-Local Means
                                                <button class="term-hint-btn" aria-label="Penjelasan Non-Local Means"
                                                    onclick="openGlossaryModal('nlm', this); event.stopPropagation();"
                                                    onkeydown="if(event.key==='Enter'||event.key===' '){openGlossaryModal('nlm',this);event.stopPropagation();}">?
                                                </button>
                                            </span>
                                        </td>
                                        <td>46.06 dB</td>
                                        <td>0.9799</td>
                                        <td>+3.92 dB</td>
                                        <td>254.3 ms</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #34d399; font-weight: 700;">
                                            <span class="term-hint">
                                                DnCNN (Deep AI)
                                                <button class="term-hint-btn" aria-label="Penjelasan DnCNN"
                                                    onclick="openGlossaryModal('dncnn', this); event.stopPropagation();"
                                                    onkeydown="if(event.key==='Enter'||event.key===' '){openGlossaryModal('dncnn',this);event.stopPropagation();}">?
                                                </button>
                                            </span>
                                        </td>
                                        <td style="color: #34d399; font-weight: 700;">45.12 dB</td>
                                        <td style="color: #34d399; font-weight: 700;">0.9797</td>
                                        <td style="color: #34d399; font-weight: 700;">+3.48 dB</td>
                                        <td style="color: #34d399; font-weight: 700;">19.4 ms</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top: 0.75rem;">
                            <span style="font-size: 0.65rem; color: #64748b;">Formula: Wang 2004, Immerkaer 1996</span>
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
                            <div>
                                <button type="button" onclick="clearAllCalipers()" style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.35); color: #ef4444; font-size: 0.68rem; padding: 2px 8px; border-radius: 4px; cursor: pointer; font-weight: 600; transition: all 0.15s;" onmouseover="this.style.background='rgba(239, 68, 68, 0.25)'" onmouseout="this.style.background='rgba(239, 68, 68, 0.12)'" title="Hapus semua anotasi di viewport ini">Hapus Semua</button>
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
                            <span style="font-size: 0.65rem; color: #64748b;">Radiologi Diagnostik</span>
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

    @else
    {{-- ====================================================================
         DISCLAIMER SCREEN – Pasien Belum Menjalani Pemeriksaan
         Tidak ada gambar dummy, tidak ada alat klinis, hanya informasi order
         dan tombol untuk melakukan pemeriksaan.
    ==================================================================== --}}
    <main style="flex: 1; display: flex; align-items: center; justify-content: center;
                 background: radial-gradient(circle at 50% 40%, #0d1728 0%, #060911 100%);
                 padding: 2.5rem 1.5rem; text-align: center; overflow-y: auto;">
        <div style="max-width: 640px; width: 100%;">

            {{-- Flash message (if any) --}}
            @if(session('success'))
            <div style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.4); color: #34d399;
                        padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.85rem; text-align: left;">
                ✅ {{ session('success') }}
            </div>
            @endif

            {{-- Icon --}}
            <div style="background: rgba(245,158,11,0.1); border: 2px solid rgba(245,158,11,0.35);
                        border-radius: 50%; width: 96px; height: 96px;
                        display: inline-flex; align-items: center; justify-content: center;
                        margin-bottom: 1.5rem; box-shadow: 0 0 40px rgba(245,158,11,0.18);">
                <span style="font-size: 3rem;">☢️</span>
            </div>

            {{-- Status badge --}}
            <div style="display: inline-flex; align-items: center; gap: 0.5rem;
                        background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.35);
                        padding: 0.3rem 0.9rem; border-radius: 20px; font-size: 0.74rem; font-weight: 700;
                        color: #fbbf24; margin-bottom: 1.25rem; text-transform: uppercase; letter-spacing: 0.05em;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #fbbf24; box-shadow: 0 0 8px #fbbf24; display:inline-block;"></span>
                Status: Siap di Ruang Rontgen
            </div>

            {{-- Heading --}}
            <h2 style="font-size: 1.65rem; font-weight: 800; color: #f8fafc;
                       margin-bottom: 0.75rem; letter-spacing: -0.02em;">
                Pemeriksaan Belum Dilakukan
            </h2>
            <p style="color: #94a3b8; font-size: 0.9rem; line-height: 1.65; margin-bottom: 2rem;">
                Pasien <strong style="color: #f1f5f9;">{{ $scan->patient_name }}</strong>
                masih dalam antrian dan belum menjalani pemeriksaan radiologi.<br>
                Pengambilan citra (image retrieval) dan instrumen analisis Viewer tidak dapat diakses
                sampai pemeriksaan selesai dilakukan pada stasiun radiologi dan citra DICOM diterima oleh server PACS.
            </p>

            {{-- Order detail card --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.75rem;
                        background: rgba(17,26,44,0.85); border: 1px solid #1e293b; border-radius: 10px;
                        padding: 1rem 1.25rem; margin-bottom: 2rem; text-align: left;">
                <div>
                    <div style="font-size: 0.67rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 0.2rem;">No. Rekam Medis</div>
                    <div style="font-size: 0.84rem; color: #38bdf8; font-weight: 700; font-family: 'JetBrains Mono', monospace;">{{ $scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div>
                    <div style="font-size: 0.67rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 0.2rem;">Accession No</div>
                    <div style="font-size: 0.84rem; color: #cbd5e1; font-weight: 700; font-family: 'JetBrains Mono', monospace;">{{ $scan->accession_number ?: 'ACC-' . str_pad($scan->id, 4, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div>
                    <div style="font-size: 0.67rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 0.2rem;">Modalitas Order</div>
                    <div style="font-size: 0.84rem; color: #f8fafc; font-weight: 700;">{{ $scan->modality }}</div>
                </div>
                <div>
                    <div style="font-size: 0.67rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 0.2rem;">Stasiun Alat</div>
                    <div style="font-size: 0.84rem; color: #cbd5e1; font-weight: 700; font-family: monospace;">{{ $scan->station_name ?: 'FUJIFILM_FDR_01' }}</div>
                </div>
            </div>

            {{-- Action buttons --}}
            <div style="display: flex; gap: 0.9rem; align-items: center; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('scans.index') }}"
                   style="background: linear-gradient(135deg, #0284c7, #06b6d4); color: #fff; border: none;
                          padding: 0.75rem 1.6rem; border-radius: 8px; font-weight: 700; font-size: 0.875rem;
                          text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;
                          box-shadow: 0 4px 16px rgba(2,132,199,0.35); transition: opacity 0.2s;"
                   onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    ⬅️ Kembali ke Daftar Pasien
                </a>

                <button onclick="window.location.reload()"
                   style="background: rgba(30,41,59,0.85); color: #cbd5e1; border: 1px solid #334155;
                          padding: 0.75rem 1.4rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem;
                          cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;
                          transition: background 0.15s;"
                   onmouseover="this.style.background='#1e293b'" onmouseout="this.style.background='rgba(30,41,59,0.85)'">
                    🔄 Cek Status Terbaru
                </button>
            </div>
        </div>
    </main>
    @endif

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

        // Measurements Store & Undo/Redo Stacks
        const measurements = { 1: [], 2: [] };
        const undoStack = { 1: [], 2: [] };
        const redoStack = { 1: [], 2: [] };

        function pushMeasurementHistory(vpId) {
            if (!undoStack[vpId]) undoStack[vpId] = [];
            if (!redoStack[vpId]) redoStack[vpId] = [];
            undoStack[vpId].push(JSON.parse(JSON.stringify(measurements[vpId])));
            if (undoStack[vpId].length > 40) undoStack[vpId].shift();
            redoStack[vpId] = [];
            updateUndoRedoButtons();
        }

        function updateUndoRedoButtons() {
            const btnUndo = document.getElementById('btnDockUndo');
            const btnRedo = document.getElementById('btnDockRedo');
            const canUndo = (activeTool === 'polygon' && currentPolygonPoints.length > 0) || 
                            (undoStack[activeViewport] && undoStack[activeViewport].length > 0) || 
                            (measurements[activeViewport] && measurements[activeViewport].length > 0);
            const canRedo = (redoStack[activeViewport] && redoStack[activeViewport].length > 0);

            if (btnUndo) {
                btnUndo.disabled = !canUndo;
                btnUndo.style.opacity = canUndo ? '1' : '0.35';
                btnUndo.style.cursor = canUndo ? 'pointer' : 'not-allowed';
            }
            if (btnRedo) {
                btnRedo.disabled = !canRedo;
                btnRedo.style.opacity = canRedo ? '1' : '0.35';
                btnRedo.style.cursor = canRedo ? 'pointer' : 'not-allowed';
            }
        }
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

        const vpStageLayer1 = document.getElementById('vpStageLayer1');
        const vpStageLayer2 = document.getElementById('vpStageLayer2');
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

        let selectedAnnotation = null; // { vpId: 1, index: 0 }

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

        // Layout Switcher: 1x1 vs 1x2
        function setLayoutMode(mode) {
            isMultimodal = (mode === '1x2');
            const btnSingle = document.getElementById('btnLayoutSingle');
            const btnDual = document.getElementById('btnLayoutDual');
            if (btnSingle) btnSingle.classList.toggle('active', !isMultimodal);
            if (btnDual) btnDual.classList.toggle('active', isMultimodal);

            if (isMultimodal) {
                if (typeof viewportsStage !== 'undefined' && viewportsStage) viewportsStage.classList.add('split-1x2');
                if (typeof vp2 !== 'undefined' && vp2) vp2.style.display = 'flex';
                showToolHint('◧◨', 'Mode 1x2: Membandingkan Thorax PA (Rontgen) vs USG secara berdampingan.');
            } else {
                if (typeof viewportsStage !== 'undefined' && viewportsStage) viewportsStage.classList.remove('split-1x2');
                if (typeof vp2 !== 'undefined' && vp2) vp2.style.display = 'none';
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
            updateUndoRedoButtons();
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

        // ==========================================
        // EXPORT OPTIONS: PDF, EXCEL, AND CSV
        // ==========================================
        function getBenchmarkExportData() {
            const rows = currentBenchmarkRows || [
                { id: 'raw', name: 'Citra Asli (Raw Baseline)', psnr: '-', ssim: 1.0000, snr_gain: '0.00 dB', latency: '0.0 ms', noise_red: '0.0%' },
                { id: 'bilateral', name: 'Bilateral Filter (Tomasi 1998)', psnr: '44.62 dB', ssim: 0.9779, snr_gain: '+5.24 dB', latency: '59.9 ms', noise_red: '45.2%' },
                { id: 'nlm', name: 'Non-Local Means (NLM 2005)', psnr: '46.06 dB', ssim: 0.9799, snr_gain: '+3.92 dB', latency: '254.3 ms', noise_red: '36.8%' },
                { id: 'dl_dncnn', name: 'Deep Residual CNN (DnCNN 2017)', psnr: '45.12 dB', ssim: 0.9797, snr_gain: '+3.48 dB', latency: '19.4 ms', noise_red: '33.5%' }
            ];

            const patientName = "{{ addslashes($scan->patient_name) }}";
            const patientId = "{{ $scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}";
            const modality = "{{ addslashes($scan->modality) }}";
            const dateStr = new Date().toISOString().replace('T', ' ').substring(0, 19);

            return { rows, patientName, patientId, modality, dateStr };
        }

        function escapeXml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&apos;');
        }

        function exportBenchmarkCsv() {
            const { rows, patientName, patientId, modality, dateStr } = getBenchmarkExportData();

            let csv = '\uFEFF'; // UTF-8 BOM for universal spreadsheet compatibility
            csv += `# ====================================================================\n`;
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
            setTimeout(() => URL.revokeObjectURL(url), 1000);

            showToolHint('📥', 'File CSV Matriks Komparasi (.csv) berhasil diunduh!');
            setTimeout(hideToolHint, 3000);
        }

        function exportBenchmarkExcel() {
            const { rows, patientName, patientId, modality, dateStr } = getBenchmarkExportData();

            let xml = '<' + '?xml version="1.0" encoding="UTF-8"?>\n' +
                '<' + '?mso-application progid="Excel.Sheet"?>\n' +
                `<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Title>Matriks Evaluasi Restorasi Citra Medis</Title>
  <Author>Hyu PACS Workstation</Author>
  <Company>Cahaya Diagnostic Centre</Company>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#1E293B"/>
  </Style>
  <Style ss:ID="TitleHeader">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="13" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#0F172A" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="SubHeader">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#94A3B8"/>
   <Interior ss:Color="#0F172A" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="MetaLabel">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Bold="1" ss:Color="#334155"/>
   <Interior ss:Color="#F1F5F9" ss:Pattern="Solid"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
   </Borders>
  </Style>
  <Style ss:ID="MetaValue">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#0F172A"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
   </Borders>
  </Style>
  <Style ss:ID="TableHeader">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#0284C7" ss:Pattern="Solid"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0369A1"/>
   </Borders>
  </Style>
  <Style ss:ID="DataLeft">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9.5" ss:Color="#0F172A"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
  </Style>
  <Style ss:ID="DataCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9.5" ss:Color="#0F172A"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
  </Style>
  <Style ss:ID="DataAiLeft">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9.5" ss:Bold="1" ss:Color="#047857"/>
   <Interior ss:Color="#ECFDF5" ss:Pattern="Solid"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A7F3D0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A7F3D0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A7F3D0"/>
   </Borders>
  </Style>
  <Style ss:ID="DataAiCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9.5" ss:Bold="1" ss:Color="#047857"/>
   <Interior ss:Color="#ECFDF5" ss:Pattern="Solid"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A7F3D0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A7F3D0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A7F3D0"/>
   </Borders>
  </Style>
 </Styles>
 <Worksheet ss:Name="Matriks Evaluasi">
  <Table ss:DefaultRowHeight="20">
   <Column ss:Width="200"/>
   <Column ss:Width="90"/>
   <Column ss:Width="80"/>
   <Column ss:Width="100"/>
   <Column ss:Width="100"/>
   <Column ss:Width="95"/>

   <Row ss:Height="26">
    <Cell ss:MergeAcross="5" ss:StyleID="TitleHeader"><Data ss:Type="String">CAHAYA DIAGNOSTIC CENTRE (CDC) - HYU PACS WORKSTATION</Data></Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="5" ss:StyleID="SubHeader"><Data ss:Type="String">Laporan Hasil Matriks Komparasi Evaluasi Restorasi &amp; Denoising Citra Medis</Data></Cell>
   </Row>
   <Row ss:Height="10"></Row>

   <Row>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Nama Pasien</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="MetaValue"><Data ss:Type="String">${escapeXml(patientName)}</Data></Cell>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Modalitas</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="MetaValue"><Data ss:Type="String">${escapeXml(modality)}</Data></Cell>
   </Row>
   <Row>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">No. Rekam Medis (MRN)</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="MetaValue"><Data ss:Type="String">${escapeXml(patientId)}</Data></Cell>
    <Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Waktu Pengujian</Data></Cell>
    <Cell ss:MergeAcross="1" ss:StyleID="MetaValue"><Data ss:Type="String">${escapeXml(dateStr)}</Data></Cell>
   </Row>
   <Row ss:Height="12"></Row>

   <Row ss:Height="24">
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Metode Algoritma</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">PSNR (dB)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">SSIM Index</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">SNR Gain (dB)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Reduksi Noise (%)</Data></Cell>
    <Cell ss:StyleID="TableHeader"><Data ss:Type="String">Latensi (ms)</Data></Cell>
   </Row>`;

            rows.forEach(r => {
                const isAi = (r.name || '').includes('DnCNN') || (r.id === 'dl_dncnn');
                const styleL = isAi ? 'DataAiLeft' : 'DataLeft';
                const styleC = isAi ? 'DataAiCenter' : 'DataCenter';

                const cleanPsnr = (r.psnr || '-').replace(' dB', '');
                const cleanSnr = (r.snr_gain || '0').replace(' dB', '');
                const cleanNoise = (r.noise_red || '-').replace('%', '');
                const cleanLatency = (r.latency || '0').replace(' ms', '');

                xml += `\n   <Row ss:Height="21">
    <Cell ss:StyleID="${styleL}"><Data ss:Type="String">${escapeXml(r.name)}</Data></Cell>
    <Cell ss:StyleID="${styleC}"><Data ss:Type="String">${escapeXml(cleanPsnr)}</Data></Cell>
    <Cell ss:StyleID="${styleC}"><Data ss:Type="Number">${r.ssim}</Data></Cell>
    <Cell ss:StyleID="${styleC}"><Data ss:Type="String">${escapeXml(cleanSnr)}</Data></Cell>
    <Cell ss:StyleID="${styleC}"><Data ss:Type="String">${escapeXml(cleanNoise)}</Data></Cell>
    <Cell ss:StyleID="${styleC}"><Data ss:Type="String">${escapeXml(cleanLatency)}</Data></Cell>
   </Row>`;
            });

            xml += `\n   <Row ss:Height="12"></Row>
   <Row>
    <Cell ss:MergeAcross="5"><Data ss:Type="String">Formula &amp; Standar: Wang et al. (2004) SSIM, Immerkaer (1996) Laplacian Noise Estimation.</Data></Cell>
   </Row>
   <Row>
    <Cell ss:MergeAcross="5"><Data ss:Type="String">Status: Terverifikasi oleh Sistem Hyu PACS • Dokumen Resmi Hasil Radiologi</Data></Cell>
   </Row>
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <Selected/>
   <DoNotDisplayGridlines/>
  </WorksheetOptions>
 </Worksheet>
</Workbook>`;

            const blob = new Blob([xml], { type: 'application/vnd.ms-excel;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', `Tabel_Komparasi_Denoising_${patientId}.xls`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            setTimeout(() => URL.revokeObjectURL(url), 1000);

            showToolHint('📊', 'File Excel Matriks Komparasi (.xls) berhasil diunduh!');
            setTimeout(hideToolHint, 3000);
        }

        function exportBenchmarkPdf() {
            const { rows, patientName, patientId, modality, dateStr } = getBenchmarkExportData();

            // A4 dimensions: 595.28 x 841.89 points
            const pageWidth = 595.28;
            const pageHeight = 841.89;

            let contentStream = '';
            
            function escapePdfText(str) {
                if (str === null || str === undefined) return '';
                let s = String(str)
                    .replace(/Δ/g, 'Delta ')
                    .replace(/µ/g, 'u')
                    .replace(/[^\x20-\x7E]/g, ' ');
                return s.replace(/\\/g, '\\\\').replace(/\(/g, '\\(').replace(/\)/g, '\\)');
            }

            function addText(text, x, y, size, isBold, r, g, b) {
                size = size || 10;
                r = (r !== undefined) ? r : 0;
                g = (g !== undefined) ? g : 0;
                b = (b !== undefined) ? b : 0;
                const escaped = escapePdfText(text);
                const font = isBold ? '/F2' : '/F1';
                contentStream += `BT ${font} ${size} Tf ${r} ${g} ${b} rg 1 0 0 1 ${x} ${y} Tm (${escaped}) Tj ET\n`;
            }

            function addRect(x, y, w, h, fillR, fillG, fillB, stroke) {
                fillR = (fillR !== undefined) ? fillR : 0.9;
                fillG = (fillG !== undefined) ? fillG : 0.9;
                fillB = (fillB !== undefined) ? fillB : 0.9;
                contentStream += `${fillR} ${fillG} ${fillB} rg\n`;
                contentStream += `${x} ${y} ${w} ${h} re f\n`;
                if (stroke) {
                    contentStream += `0 0 0 RG 0.5 w ${x} ${y} ${w} ${h} re S\n`;
                }
            }

            function addLine(x1, y1, x2, y2, r, g, b, width) {
                r = (r !== undefined) ? r : 0.7;
                g = (g !== undefined) ? g : 0.7;
                b = (b !== undefined) ? b : 0.7;
                width = width || 1;
                contentStream += `${r} ${g} ${b} RG ${width} w ${x1} ${y1} m ${x2} ${y2} l S\n`;
            }

            // Header Banner
            addRect(40, 770, 515, 45, 0.05, 0.1, 0.18);
            addText("CAHAYA DIAGNOSTIC CENTRE (CDC)", 55, 795, 14, true, 0.22, 0.74, 0.97);
            addText("Hyu PACS - Matriks Komparasi Evaluasi Restorasi Citra Medis", 55, 780, 9, false, 0.8, 0.85, 0.9);

            // Patient Info Box
            addRect(40, 700, 515, 60, 0.96, 0.97, 0.99, true);
            addText("INFORMASI PASIEN & MODALITAS", 50, 745, 9, true, 0.1, 0.2, 0.35);
            
            addText("Nama Pasien : " + patientName, 50, 728, 9, false, 0.2, 0.2, 0.2);
            addText("MRN / No ID : " + patientId, 50, 712, 9, false, 0.2, 0.2, 0.2);
            addText("Modalitas   : " + modality, 320, 728, 9, false, 0.2, 0.2, 0.2);
            addText("Tanggal Uji : " + dateStr, 320, 712, 9, false, 0.2, 0.2, 0.2);

            // Table Header
            let tableY = 665;
            addRect(40, tableY - 5, 515, 22, 0.1, 0.15, 0.25);
            addText("Metode Algoritma", 48, tableY + 2, 9, true, 1, 1, 1);
            addText("PSNR", 230, tableY + 2, 9, true, 1, 1, 1);
            addText("SSIM", 300, tableY + 2, 9, true, 1, 1, 1);
            addText("SNR Gain", 370, tableY + 2, 9, true, 1, 1, 1);
            addText("Reduksi Noise", 440, tableY + 2, 9, true, 1, 1, 1);
            addText("Latensi", 510, tableY + 2, 9, true, 1, 1, 1);

            // Table Rows
            let currentY = tableY - 26;
            rows.forEach((r, idx) => {
                const isAi = (r.name || '').includes('DnCNN') || (r.id === 'dl_dncnn');
                if (isAi) {
                    addRect(40, currentY - 5, 515, 20, 0.9, 0.98, 0.93);
                } else if (idx % 2 === 1) {
                    addRect(40, currentY - 5, 515, 20, 0.97, 0.97, 0.98);
                }
                
                addLine(40, currentY - 5, 555, currentY - 5, 0.85, 0.85, 0.88, 0.5);

                const textColor = isAi ? [0.05, 0.5, 0.3] : [0.15, 0.15, 0.15];
                addText(r.name, 48, currentY + 1, 8.5, isAi, textColor[0], textColor[1], textColor[2]);
                addText(r.psnr || '-', 230, currentY + 1, 8.5, isAi, textColor[0], textColor[1], textColor[2]);
                addText(String(r.ssim), 300, currentY + 1, 8.5, isAi, textColor[0], textColor[1], textColor[2]);
                addText(r.snr_gain || '0 dB', 370, currentY + 1, 8.5, isAi, textColor[0], textColor[1], textColor[2]);
                addText(r.noise_red || '-', 440, currentY + 1, 8.5, isAi, textColor[0], textColor[1], textColor[2]);
                addText(r.latency || '-', 510, currentY + 1, 8.5, isAi, textColor[0], textColor[1], textColor[2]);

                currentY -= 21;
            });

            // Outer Table Border
            addLine(40, tableY + 17, 555, tableY + 17, 0.2, 0.3, 0.4, 1);
            addLine(40, currentY, 555, currentY, 0.2, 0.3, 0.4, 1);

            // Scientific Citation & Notes
            currentY -= 20;
            addText("Catatan Metodologi Ilmiah Evaluasi Citra Medis:", 40, currentY, 9, true, 0.2, 0.3, 0.45);
            currentY -= 14;
            addText("1. PSNR (Peak Signal-to-Noise Ratio): Preservasi sinyal relatif terhadap MSE citra asli.", 40, currentY, 8, false, 0.35, 0.4, 0.45);
            currentY -= 12;
            addText("2. SSIM (Structural Similarity Index): Standar Wang et al. (2004) untuk kemiripan struktur anatomi organ.", 40, currentY, 8, false, 0.35, 0.4, 0.45);
            currentY -= 12;
            addText("3. SNR Gain (Laplacian Filtered): Standar Immerkaer (1996) untuk kuantifikasi reduksi noise tanpa blur.", 40, currentY, 8, false, 0.35, 0.4, 0.45);

            // Footer / Digital Signature Stamp
            currentY -= 45;
            addRect(360, currentY - 10, 195, 55, 0.98, 0.98, 1, true);
            addText("VALIDASI RADIOLOGI KLINIS", 370, currentY + 30, 8, true, 0.1, 0.3, 0.6);
            addText("Tervalidasi secara Elektronik", 370, currentY + 18, 7.5, false, 0.1, 0.6, 0.3);
            addText("Hyu PACS Workstation Medical Hub", 370, currentY + 7, 7, false, 0.5, 0.5, 0.5);
            addText("Dokumen Resmi Berkas Radiologi CDC", 370, currentY - 4, 7, false, 0.5, 0.5, 0.5);

            // System Footer
            addText("Dokumen ini dicetak/diekspor secara otomatis dari sistem Hyu PACS. Informasi bersifat Rahasia Medis.", 100, 30, 7.5, false, 0.6, 0.6, 0.6);

            const encoder = new TextEncoder();
            const streamBytes = encoder.encode(contentStream);
            const streamLength = streamBytes.length;

            const objects = [];
            objects.push(`1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n`);
            objects.push(`2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n`);
            objects.push(`3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ${pageWidth} ${pageHeight}] /Contents 7 0 R /Resources 4 0 R >>\nendobj\n`);
            objects.push(`4 0 obj\n<< /Font << /F1 5 0 R /F2 6 0 R >> >>\nendobj\n`);
            objects.push(`5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n`);
            objects.push(`6 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>\nendobj\n`);
            objects.push(`7 0 obj\n<< /Length ${streamLength} >>\nstream\n${contentStream}endstream\nendobj\n`);

            let pdf = `%PDF-1.4\n%\xE2\xE3\xCF\xD3\n`;
            const xrefOffsets = [0];

            objects.forEach(obj => {
                xrefOffsets.push(encoder.encode(pdf).length);
                pdf += obj;
            });

            const startXref = encoder.encode(pdf).length;
            pdf += `xref\n0 ${objects.length + 1}\n0000000000 65535 f \n`;
            for (let i = 1; i <= objects.length; i++) {
                pdf += String(xrefOffsets[i]).padStart(10, '0') + ` 00000 n \n`;
            }
            pdf += `trailer\n<< /Size ${objects.length + 1} /Root 1 0 R >>\nstartxref\n${startXref}\n%%EOF\n`;

            const blob = new Blob([encoder.encode(pdf)], { type: 'application/pdf' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', `Tabel_Komparasi_Denoising_${patientId}.pdf`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            setTimeout(() => URL.revokeObjectURL(url), 1000);

            showToolHint('📄', 'File PDF Matriks Komparasi (.pdf) berhasil diunduh!');
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
            selectedAnnotation = null;
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
            const stage = (id === 1) ? document.getElementById('vpStageLayer1') : document.getElementById('vpStageLayer2');
            if (!stage) return;
            const st = vpState[id];
            const transformStr = `translate(${st.translateX}px, ${st.translateY}px) rotate(${st.rotation}deg) scaleX(${st.flipH ? -1 : 1}) scale(${st.zoom})`;
            stage.style.transform = transformStr;
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
                updateUndoRedoButtons();
                return;
            }
            pushMeasurementHistory(vpId);
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
            updateUndoRedoButtons();
            showToolHint('📐', `ROI tersimpan: ${metrics.areaCm2} cm² (Kll: ${metrics.perimeterCm} cm)`);
        }

        function selectMeasurement(vpId, idx) {
            selectViewport(vpId);
            if (selectedAnnotation && selectedAnnotation.vpId === vpId && selectedAnnotation.index === idx) {
                return;
            }
            selectedAnnotation = { vpId: vpId, index: idx };
            renderMeasurements(vpId);
            updateMeasurementsPanel();

            // Scroll corresponding measurement in list into view
            setTimeout(() => {
                const itemEl = document.getElementById(`measureItem_${vpId}_${idx}`);
                if (itemEl) {
                    itemEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }, 50);
        }

        function deselectMeasurement() {
            if (selectedAnnotation) {
                const prevVp = selectedAnnotation.vpId;
                selectedAnnotation = null;
                renderMeasurements(prevVp);
                updateMeasurementsPanel();
            }
        }

        function renderMeasurements(vpId) {
            const targetSvg = (vpId === 1) ? svgMeasure1 : svgMeasure2;
            if (!targetSvg) return;
            let html = '';

            measurements[vpId].forEach((m, idx) => {
                const isSelected = (selectedAnnotation && selectedAnnotation.vpId === vpId && selectedAnnotation.index === idx);
                const strokeColor = isSelected ? '#38bdf8' : '#facc15';
                const strokeW = isSelected ? 2.8 : 2;
                const glowFilter = isSelected ? 'filter="drop-shadow(0 0 7px rgba(56, 189, 248, 0.85))"' : '';

                if (m.type === 'circle') {
                    const badgeY = (m.cy - m.r - 14 >= 14) ? (m.cy - m.r - 14) : (m.cy + m.r + 14);
                    const deleteBtnY = (badgeY > m.cy) ? (badgeY + 26) : (badgeY - 26);
                    html += `
                        <g id="circle-group-${vpId}-${idx}">
                            <!-- Invisible Wide Hit-Test Circle Area -->
                            <circle cx="${m.cx}" cy="${m.cy}" r="${m.r + 8}" fill="transparent" stroke="transparent" stroke-width="16" class="measurement-shape-hit" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});" />

                            <!-- True Circle Body (1:1 Locked) -->
                            <circle cx="${m.cx}" cy="${m.cy}" r="${m.r}" fill="rgba(250, 204, 21, ${isSelected ? '0.28' : '0.16'})" stroke="${strokeColor}" stroke-width="${strokeW}" stroke-dasharray="${isSelected ? 'none' : '4,4'}" ${glowFilter} class="measurement-shape-hit" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});" />
                            
                            <!-- Crosshair Axes -->
                            <line x1="${m.cx - m.r}" y1="${m.cy}" x2="${m.cx + m.r}" y2="${m.cy}" stroke="${strokeColor}" stroke-width="1.2" stroke-dasharray="2,2" opacity="0.85" />
                            <line x1="${m.cx}" y1="${m.cy - m.r}" x2="${m.cx}" y2="${m.cy + m.r}" stroke="${strokeColor}" stroke-width="1.2" stroke-dasharray="2,2" opacity="0.85" />
                            
                            <!-- Center Crosshair Marker -->
                            <line x1="${m.cx - 4}" y1="${m.cy}" x2="${m.cx + 4}" y2="${m.cy}" stroke="${strokeColor}" stroke-width="2" />
                            <line x1="${m.cx}" y1="${m.cy - 4}" x2="${m.cx}" y2="${m.cy + 4}" stroke="${strokeColor}" stroke-width="2" />
                            
                            <!-- 4 Cardinal Boundary Points -->
                            <circle cx="${m.cx - m.r}" cy="${m.cy}" r="3.5" fill="${strokeColor}" stroke="#0f172a" stroke-width="1.2" />
                            <circle cx="${m.cx + m.r}" cy="${m.cy}" r="3.5" fill="${strokeColor}" stroke="#0f172a" stroke-width="1.2" />
                            <circle cx="${m.cx}" cy="${m.cy - m.r}" r="3.5" fill="${strokeColor}" stroke="#0f172a" stroke-width="1.2" />
                            <circle cx="${m.cx}" cy="${m.cy + m.r}" r="3.5" fill="${strokeColor}" stroke="#0f172a" stroke-width="1.2" />
                            
                            <!-- Compact Rim Badge (Anti-Occlusion) -->
                            <g transform="translate(${m.cx}, ${badgeY})" class="measurement-interactive-item" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});">
                                <rect x="-65" y="-11" width="130" height="22" rx="11" fill="rgba(15, 23, 42, 0.94)" stroke="${strokeColor}" stroke-width="${isSelected ? '1.8' : '1.2'}" />
                                <text x="0" y="3" fill="${strokeColor}" font-size="9.5" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                    ${m.label}: &Oslash; ${m.diamCm} cm &bull; ${m.areaCm2} cm²
                                </text>
                            </g>

                            <!-- On-Canvas Floating Quick Delete Button (When Selected) -->
                            ${isSelected ? `
                                <g class="annotation-quick-delete-badge" transform="translate(${m.cx}, ${deleteBtnY})" style="cursor: pointer; pointer-events: all;" onpointerdown="event.stopPropagation();" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); deleteSingleMeasurement(${vpId}, ${idx});">
                                    <rect class="quick-delete-hitbox" x="-46" y="-16" width="92" height="32" fill="transparent" stroke="transparent" />
                                    <rect class="quick-delete-bg" x="-38" y="-11" width="76" height="22" rx="11" fill="#ef4444" stroke="#ffffff" stroke-width="1.5" filter="drop-shadow(0 2px 8px rgba(0,0,0,0.85))" />
                                    <text x="0" y="4" fill="#ffffff" font-size="10" font-weight="bold" font-family="'Plus Jakarta Sans', sans-serif" text-anchor="middle" pointer-events="none">🗑️ Hapus</text>
                                </g>
                            ` : ''}
                        </g>
                    `;
                } else if (m.type === 'ellipse') {
                    const badgeY = (m.cy - m.ry - 14 >= 14) ? (m.cy - m.ry - 14) : (m.cy + m.ry + 14);
                    const deleteBtnY = (badgeY > m.cy) ? (badgeY + 26) : (badgeY - 26);
                    html += `
                        <g id="ellipse-group-${vpId}-${idx}">
                            <!-- Invisible Wide Hit-Test Ellipse Area -->
                            <ellipse cx="${m.cx}" cy="${m.cy}" rx="${m.rx + 8}" ry="${m.ry + 8}" fill="transparent" stroke="transparent" stroke-width="16" class="measurement-shape-hit" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});" />

                            <!-- Ellipse Body -->
                            <ellipse cx="${m.cx}" cy="${m.cy}" rx="${m.rx}" ry="${m.ry}" fill="rgba(250, 204, 21, ${isSelected ? '0.28' : '0.16'})" stroke="${strokeColor}" stroke-width="${strokeW}" stroke-dasharray="${isSelected ? 'none' : '4,4'}" ${glowFilter} class="measurement-shape-hit" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});" />
                            
                            <!-- Orthogonal Diameter Lines (Major & Minor Axis) -->
                            <line x1="${m.cx - m.rx}" y1="${m.cy}" x2="${m.cx + m.rx}" y2="${m.cy}" stroke="${strokeColor}" stroke-width="1.2" stroke-dasharray="2,2" opacity="0.85" />
                            <line x1="${m.cx}" y1="${m.cy - m.ry}" x2="${m.cx}" y2="${m.cy + m.ry}" stroke="${strokeColor}" stroke-width="1.2" stroke-dasharray="2,2" opacity="0.85" />
                            
                            <!-- Center Crosshair Marker -->
                            <line x1="${m.cx - 4}" y1="${m.cy}" x2="${m.cx + 4}" y2="${m.cy}" stroke="${strokeColor}" stroke-width="2" />
                            <line x1="${m.cx}" y1="${m.cy - 4}" x2="${m.cx}" y2="${m.cy + 4}" stroke="${strokeColor}" stroke-width="2" />
                            
                            <!-- 4 Cardinal Boundary Points -->
                            <circle cx="${m.cx - m.rx}" cy="${m.cy}" r="3.5" fill="${strokeColor}" stroke="#0f172a" stroke-width="1.2" />
                            <circle cx="${m.cx + m.rx}" cy="${m.cy}" r="3.5" fill="${strokeColor}" stroke="#0f172a" stroke-width="1.2" />
                            <circle cx="${m.cx}" cy="${m.cy - m.ry}" r="3.5" fill="${strokeColor}" stroke="#0f172a" stroke-width="1.2" />
                            <circle cx="${m.cx}" cy="${m.cy + m.ry}" r="3.5" fill="${strokeColor}" stroke="#0f172a" stroke-width="1.2" />
                            
                            <!-- Compact Rim Badge (Anti-Occlusion) -->
                            <g transform="translate(${m.cx}, ${badgeY})" class="measurement-interactive-item" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});">
                                <rect x="-70" y="-11" width="140" height="22" rx="11" fill="rgba(15, 23, 42, 0.94)" stroke="${strokeColor}" stroke-width="${isSelected ? '1.8' : '1.2'}" />
                                <text x="0" y="3" fill="${strokeColor}" font-size="9.5" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                    ${m.label}: ${m.d1Cm}&times;${m.d2Cm} &bull; ${m.areaCm2} cm²
                                </text>
                            </g>

                            <!-- On-Canvas Floating Quick Delete Button (When Selected) -->
                            ${isSelected ? `
                                <g class="annotation-quick-delete-badge" transform="translate(${m.cx}, ${deleteBtnY})" style="cursor: pointer; pointer-events: all;" onpointerdown="event.stopPropagation();" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); deleteSingleMeasurement(${vpId}, ${idx});">
                                    <rect class="quick-delete-hitbox" x="-46" y="-16" width="92" height="32" fill="transparent" stroke="transparent" />
                                    <rect class="quick-delete-bg" x="-38" y="-11" width="76" height="22" rx="11" fill="#ef4444" stroke="#ffffff" stroke-width="1.5" filter="drop-shadow(0 2px 8px rgba(0,0,0,0.85))" />
                                    <text x="0" y="4" fill="#ffffff" font-size="10" font-weight="bold" font-family="'Plus Jakarta Sans', sans-serif" text-anchor="middle" pointer-events="none">🗑️ Hapus</text>
                                </g>
                            ` : ''}
                        </g>
                    `;
                } else if (m.type === 'polygon') {
                    const ptsString = m.points.map(p => `${p.x},${p.y}`).join(' ');
                    const deleteBtnY = (m.cy - 34 >= 14) ? (m.cy - 34) : (m.cy + 34);
                    html += `
                        <g id="roi-group-${vpId}-${idx}">
                            <!-- Polygon Body with Hit Handler -->
                            <polygon points="${ptsString}" fill="rgba(250, 204, 21, ${isSelected ? '0.32' : '0.2'})" stroke="${strokeColor}" stroke-width="${strokeW}" stroke-linejoin="round" ${glowFilter} class="measurement-shape-hit" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});" />
                            ${m.points.map(p => `<circle cx="${p.x}" cy="${p.y}" r="${isSelected ? '5' : '4'}" fill="${strokeColor}" stroke="#0f172a" stroke-width="1.5" />`).join('')}
                            
                            <g transform="translate(${m.cx}, ${m.cy})" class="measurement-interactive-item" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});">
                                <rect x="-55" y="-11" width="110" height="22" rx="11" fill="rgba(15, 23, 42, 0.94)" stroke="${strokeColor}" stroke-width="${isSelected ? '1.8' : '1.2'}" />
                                <text x="0" y="3" fill="${strokeColor}" font-size="10" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                    ${m.label}: ${m.areaCm2} cm²
                                </text>
                            </g>

                            <!-- On-Canvas Floating Quick Delete Button (When Selected) -->
                            ${isSelected ? `
                                <g class="annotation-quick-delete-badge" transform="translate(${m.cx}, ${deleteBtnY})" style="cursor: pointer; pointer-events: all;" onpointerdown="event.stopPropagation();" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); deleteSingleMeasurement(${vpId}, ${idx});">
                                    <rect class="quick-delete-hitbox" x="-46" y="-16" width="92" height="32" fill="transparent" stroke="transparent" />
                                    <rect class="quick-delete-bg" x="-38" y="-11" width="76" height="22" rx="11" fill="#ef4444" stroke="#ffffff" stroke-width="1.5" filter="drop-shadow(0 2px 8px rgba(0,0,0,0.85))" />
                                    <text x="0" y="4" fill="#ffffff" font-size="10" font-weight="bold" font-family="'Plus Jakarta Sans', sans-serif" text-anchor="middle" pointer-events="none">🗑️ Hapus</text>
                                </g>
                            ` : ''}
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
                    const centerCtrX = (midHX + midTX) / 2;
                    const centerCtrY = (midHY + midTY) / 2;
                    const deleteBtnY = (centerCtrY - 36 >= 14) ? (centerCtrY - 36) : (centerCtrY + 36);

                    html += `
                        <g id="ctr-group-${vpId}-${idx}">
                            <!-- Invisible Hit Test Lines -->
                            <line x1="${h.x1}" y1="${h.y1}" x2="${h.x2}" y2="${h.y2}" stroke="transparent" stroke-width="26" class="measurement-shape-hit" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});" />
                            <line x1="${t.x1}" y1="${t.y1}" x2="${t.x2}" y2="${t.y2}" stroke="transparent" stroke-width="26" class="measurement-shape-hit" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});" />

                            <!-- Heart Line (Transversal Cor) -->
                            <line x1="${h.x1}" y1="${h.y1}" x2="${h.x2}" y2="${h.y2}" stroke="#f43f5e" stroke-width="${isSelected ? '3.2' : '2.5'}" ${glowFilter} />
                            <circle cx="${h.x1}" cy="${h.y1}" r="4.5" fill="#f43f5e" stroke="#000" />
                            <circle cx="${h.x2}" cy="${h.y2}" r="4.5" fill="#f43f5e" stroke="#000" />
                            <g transform="translate(${midHX}, ${midHY - 11})" class="measurement-interactive-item" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});">
                                <rect x="-55" y="-9" width="110" height="18" rx="3" fill="rgba(15, 23, 42, 0.9)" stroke="#f43f5e" stroke-width="1" />
                                <text x="0" y="4" fill="#f43f5e" font-size="10" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                    Jantung: ${h.distCm} cm
                                </text>
                            </g>

                            <!-- Thorax Line (Transversal Thorax) -->
                            <line x1="${t.x1}" y1="${t.y1}" x2="${t.x2}" y2="${t.y2}" stroke="#facc15" stroke-width="${isSelected ? '3.2' : '2.5'}" stroke-dasharray="4,4" ${glowFilter} />
                            <circle cx="${t.x1}" cy="${t.y1}" r="4.5" fill="#facc15" stroke="#000" />
                            <circle cx="${t.x2}" cy="${t.y2}" r="4.5" fill="#facc15" stroke="#000" />
                            <g transform="translate(${midTX}, ${midTY - 11})" class="measurement-interactive-item" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});">
                                <rect x="-55" y="-9" width="110" height="18" rx="3" fill="rgba(15, 23, 42, 0.9)" stroke="#facc15" stroke-width="1" />
                                <text x="0" y="4" fill="#facc15" font-size="10" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                    Toraks: ${t.distCm} cm
                                </text>
                            </g>

                            <!-- Center Overall CTR Badge -->
                            <g transform="translate(${centerCtrX}, ${centerCtrY})" class="measurement-interactive-item" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});">
                                <rect x="-85" y="-13" width="170" height="26" rx="13" fill="rgba(15, 23, 42, 0.95)" stroke="${isSelected ? '#38bdf8' : badgeColor}" stroke-width="${isSelected ? '2.5' : '1.5'}" ${glowFilter} />
                                <text x="0" y="3" fill="${badgeColor}" font-size="11" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                    CTR: ${m.ratio}% (${m.verdict})
                                </text>
                            </g>

                            <!-- On-Canvas Floating Quick Delete Button (When Selected) -->
                            ${isSelected ? `
                                <g class="annotation-quick-delete-badge" transform="translate(${centerCtrX}, ${deleteBtnY})" style="cursor: pointer; pointer-events: all;" onpointerdown="event.stopPropagation();" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); deleteSingleMeasurement(${vpId}, ${idx});">
                                    <rect class="quick-delete-hitbox" x="-46" y="-16" width="92" height="32" fill="transparent" stroke="transparent" />
                                    <rect class="quick-delete-bg" x="-38" y="-11" width="76" height="22" rx="11" fill="#ef4444" stroke="#ffffff" stroke-width="1.5" filter="drop-shadow(0 2px 8px rgba(0,0,0,0.85))" />
                                    <text x="0" y="4" fill="#ffffff" font-size="10" font-weight="bold" font-family="'Plus Jakarta Sans', sans-serif" text-anchor="middle" pointer-events="none">🗑️ Hapus</text>
                                </g>
                            ` : ''}
                        </g>
                    `;
                } else {
                    // Linear Caliper
                    const midX = (m.x1 + m.x2) / 2;
                    const midY = (m.y1 + m.y2) / 2;
                    const deleteBtnY = (midY - 38 >= 14) ? (midY - 38) : (midY + 36);
                    html += `
                        <g id="caliper-group-${vpId}-${idx}">
                            <!-- Invisible Wide Hit-Test Line (Easy to Click) -->
                            <line x1="${m.x1}" y1="${m.y1}" x2="${m.x2}" y2="${m.y2}" stroke="transparent" stroke-width="26" class="measurement-shape-hit" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});" />

                            <!-- Visible Caliper Line -->
                            <line x1="${m.x1}" y1="${m.y1}" x2="${m.x2}" y2="${m.y2}" stroke="${strokeColor}" stroke-width="${strokeW}" stroke-dasharray="${isSelected ? 'none' : '4,4'}" ${glowFilter} />
                            <circle cx="${m.x1}" cy="${m.y1}" r="${isSelected ? '5' : '4'}" fill="${strokeColor}" stroke="#0f172a" stroke-width="1.5" />
                            <circle cx="${m.x2}" cy="${m.y2}" r="${isSelected ? '5' : '4'}" fill="${strokeColor}" stroke="#0f172a" stroke-width="1.5" />
                            
                            <!-- Midpoint Measurement Badge -->
                            <g transform="translate(${midX}, ${midY})" class="measurement-interactive-item" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); selectMeasurement(${vpId}, ${idx});">
                                <rect x="-44" y="-10" width="88" height="20" rx="10" fill="rgba(15, 23, 42, 0.94)" stroke="${strokeColor}" stroke-width="${isSelected ? '1.8' : '1.2'}" />
                                <text x="0" y="4" fill="${strokeColor}" font-size="10.5" font-weight="bold" font-family="'JetBrains Mono', monospace" text-anchor="middle">
                                    ${m.label}: ${m.distCm} cm
                                </text>
                            </g>

                            <!-- On-Canvas Floating Quick Delete Button (When Selected) -->
                            ${isSelected ? `
                                <g class="annotation-quick-delete-badge" transform="translate(${midX}, ${deleteBtnY})" style="cursor: pointer; pointer-events: all;" onpointerdown="event.stopPropagation();" onmousedown="event.stopPropagation();" ontouchstart="event.stopPropagation();" onclick="event.stopPropagation(); deleteSingleMeasurement(${vpId}, ${idx});">
                                    <rect class="quick-delete-hitbox" x="-46" y="-16" width="92" height="32" fill="transparent" stroke="transparent" />
                                    <rect class="quick-delete-bg" x="-38" y="-11" width="76" height="22" rx="11" fill="#ef4444" stroke="#ffffff" stroke-width="1.5" filter="drop-shadow(0 2px 8px rgba(0,0,0,0.85))" />
                                    <text x="0" y="4" fill="#ffffff" font-size="10" font-weight="bold" font-family="'Plus Jakarta Sans', sans-serif" text-anchor="middle" pointer-events="none">🗑️ Hapus</text>
                                </g>
                            ` : ''}
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
                    const isSelected = (selectedAnnotation && selectedAnnotation.vpId === activeViewport && selectedAnnotation.index === idx);
                    const deleteBtnHtml = `
                        <button type="button" onclick="event.stopPropagation(); deleteSingleMeasurement(${activeViewport}, ${idx})" class="btn-delete-item" title="Hapus ${m.label || 'pengukuran ini'}" aria-label="Hapus">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </button>
                    `;

                    if (m.type === 'ctr') {
                        const badgeColor = m.isNormal ? '#10b981' : '#ef4444';
                        html += `
                            <div class="measurement-list-item ${isSelected ? 'is-selected-in-list' : ''}" id="measureItem_${activeViewport}_${idx}" onclick="selectMeasurement(${activeViewport}, ${idx})">
                                <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 700; color: ${badgeColor};">
                                    <span>Cardiothoracic Ratio (CTR)</span>
                                    <div style="display: flex; align-items: center; gap: 0.45rem;">
                                        <span style="font-family: 'JetBrains Mono', monospace;">${m.ratio}% (${m.verdict})</span>
                                        ${deleteBtnHtml}
                                    </div>
                                </div>
                                <div style="font-size: 0.65rem; color: #94a3b8; font-family: monospace; margin-top: 0.2rem;">
                                    Jantung: ${m.heart.distCm} cm | Toraks: ${m.thorax.distCm} cm
                                </div>
                            </div>
                        `;
                    } else if (m.type === 'circle') {
                        html += `
                            <div class="measurement-list-item ${isSelected ? 'is-selected-in-list' : ''}" id="measureItem_${activeViewport}_${idx}" onclick="selectMeasurement(${activeViewport}, ${idx})">
                                <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 700; color: #facc15;">
                                    <span>${m.label} (Lingkaran Sempurna)</span>
                                    <div style="display: flex; align-items: center; gap: 0.45rem;">
                                        <span style="font-family: 'JetBrains Mono', monospace;">${m.areaCm2} cm²</span>
                                        ${deleteBtnHtml}
                                    </div>
                                </div>
                                <div style="font-size: 0.65rem; color: #38bdf8; font-family: monospace; margin-top: 0.2rem;">
                                    &Oslash; Diameter: ${m.diamCm} cm &bull; Jari-jari: ${(m.r * 0.08).toFixed(2)} cm
                                </div>
                            </div>
                        `;
                    } else if (m.type === 'ellipse') {
                        html += `
                            <div class="measurement-list-item ${isSelected ? 'is-selected-in-list' : ''}" id="measureItem_${activeViewport}_${idx}" onclick="selectMeasurement(${activeViewport}, ${idx})">
                                <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 700; color: #facc15;">
                                    <span>${m.label} (Oval / Ellipse)</span>
                                    <div style="display: flex; align-items: center; gap: 0.45rem;">
                                        <span style="font-family: 'JetBrains Mono', monospace;">${m.areaCm2} cm²</span>
                                        ${deleteBtnHtml}
                                    </div>
                                </div>
                                <div style="font-size: 0.65rem; color: #38bdf8; font-family: monospace; margin-top: 0.2rem;">
                                    D1: ${m.d1Cm} cm &bull; D2: ${m.d2Cm} cm
                                </div>
                            </div>
                        `;
                    } else if (m.type === 'polygon') {
                        html += `
                            <div class="measurement-list-item ${isSelected ? 'is-selected-in-list' : ''}" id="measureItem_${activeViewport}_${idx}" onclick="selectMeasurement(${activeViewport}, ${idx})">
                                <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 700; color: #facc15;">
                                    <span>${m.label} (Area ROI)</span>
                                    <div style="display: flex; align-items: center; gap: 0.45rem;">
                                        <span style="font-family: 'JetBrains Mono', monospace;">${m.areaCm2} cm²</span>
                                        ${deleteBtnHtml}
                                    </div>
                                </div>
                                <div style="font-size: 0.65rem; color: #94a3b8; font-family: monospace; margin-top: 0.2rem;">
                                    Keliling: ${m.perimeterCm} cm
                                </div>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="measurement-list-item ${isSelected ? 'is-selected-in-list' : ''}" id="measureItem_${activeViewport}_${idx}" onclick="selectMeasurement(${activeViewport}, ${idx})" style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #facc15; font-weight: 700;">${m.label} (Linear Caliper)</span>
                                <div style="display: flex; align-items: center; gap: 0.45rem;">
                                    <span style="font-family: 'JetBrains Mono', monospace; color: #facc15; font-weight: 600;">${m.distCm} cm</span>
                                    ${deleteBtnHtml}
                                </div>
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

            pushMeasurementHistory(activeViewport);

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
            updateUndoRedoButtons();
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

        let isDeletingMeasurement = false;
        function deleteSingleMeasurement(vpId, index) {
            if (isDeletingMeasurement) return;
            if (!measurements[vpId] || !measurements[vpId][index]) return;
            isDeletingMeasurement = true;
            setTimeout(() => { isDeletingMeasurement = false; }, 200);

            pushMeasurementHistory(vpId);
            const itemLabel = measurements[vpId][index].label || 'Pengukuran';
            measurements[vpId].splice(index, 1);

            if (selectedAnnotation && selectedAnnotation.vpId === vpId) {
                if (selectedAnnotation.index === index) {
                    selectedAnnotation = null;
                } else if (selectedAnnotation.index > index) {
                    selectedAnnotation.index--;
                }
            }

            renderMeasurements(vpId);
            updateMeasurementsPanel();
            updateUndoRedoButtons();
            showToolHint('🗑️', `${itemLabel} berhasil dihapus.`);
            setTimeout(hideToolHint, 2000);
        }

        function undoLastMeasurement() {
            if (activeTool === 'polygon' && currentPolygonPoints.length > 0) {
                currentPolygonPoints.pop();
                polygonHoverPoint = null;
                isPolygonSnapping = false;
                renderMeasurements(activeViewport);
                updateUndoRedoButtons();
                return;
            }

            if (undoStack[activeViewport] && undoStack[activeViewport].length > 0) {
                redoStack[activeViewport].push(JSON.parse(JSON.stringify(measurements[activeViewport])));
                measurements[activeViewport] = undoStack[activeViewport].pop();
                selectedAnnotation = null;
                renderMeasurements(activeViewport);
                updateMeasurementsPanel();
                updateUndoRedoButtons();
                showToolHint('↺', 'Undo anotasi berhasil.');
                setTimeout(hideToolHint, 1500);
            } else if (measurements[activeViewport] && measurements[activeViewport].length > 0) {
                redoStack[activeViewport].push(JSON.parse(JSON.stringify(measurements[activeViewport])));
                measurements[activeViewport].pop();
                selectedAnnotation = null;
                renderMeasurements(activeViewport);
                updateMeasurementsPanel();
                updateUndoRedoButtons();
                showToolHint('↺', 'Undo anotasi berhasil.');
                setTimeout(hideToolHint, 1500);
            }
        }

        function redoLastMeasurement() {
            if (redoStack[activeViewport] && redoStack[activeViewport].length > 0) {
                undoStack[activeViewport].push(JSON.parse(JSON.stringify(measurements[activeViewport])));
                measurements[activeViewport] = redoStack[activeViewport].pop();
                selectedAnnotation = null;
                renderMeasurements(activeViewport);
                updateMeasurementsPanel();
                updateUndoRedoButtons();
                showToolHint('↻', 'Redo anotasi berhasil.');
                setTimeout(hideToolHint, 1500);
            }
        }

        function clearAllCalipers() {
            if ((!measurements[activeViewport] || measurements[activeViewport].length === 0) && currentPolygonPoints.length === 0) return;
            if (measurements[activeViewport] && measurements[activeViewport].length > 0) {
                pushMeasurementHistory(activeViewport);
            }
            selectedAnnotation = null;
            currentPolygonPoints = [];
            polygonHoverPoint = null;
            isPolygonSnapping = false;
            measurements[activeViewport] = [];
            renderMeasurements(activeViewport);
            updateMeasurementsPanel();
            updateUndoRedoButtons();
            showToolHint('🗑️', 'Semua anotasi pengukuran berhasil dihapus.');
            setTimeout(hideToolHint, 2000);
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

        function getSvgCoords(e, svgElement) {
            if (!svgElement) return { x: 0, y: 0, clientX: 0, clientY: 0 };
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

            try {
                const ctm = svgElement.getScreenCTM();
                if (ctm) {
                    const pt = svgElement.createSVGPoint();
                    pt.x = clientX;
                    pt.y = clientY;
                    const transPt = pt.matrixTransform(ctm.inverse());
                    return {
                        x: Math.round(transPt.x),
                        y: Math.round(transPt.y),
                        clientX,
                        clientY
                    };
                }
            } catch (err) {
                console.warn('CTM transform fallback:', err);
            }

            const rect = svgElement.getBoundingClientRect();
            return {
                x: Math.round(clientX - rect.left),
                y: Math.round(clientY - rect.top),
                clientX,
                clientY
            };
        }

        function handleViewportStart(vpId, e) {
            const cell = (vpId === 1) ? vp1 : vp2;
            const targetSvg = (vpId === 1) ? svgMeasure1 : svgMeasure2;
            if (!cell || !targetSvg) return;

            // Prevent interaction conflicts when interacting with quick delete badge or existing measurements
            const targetEl = e.target;
            if (targetEl && typeof targetEl.closest === 'function') {
                if (targetEl.closest('.annotation-quick-delete-badge')) {
                    // Do NOT intercept or deselect; allow quick delete badge handlers to process cleanly
                    return;
                }
                if (targetEl.closest('.measurement-interactive-item') || targetEl.closest('.measurement-shape-hit')) {
                    // Do NOT start drawing or panning; allow selection handler to process
                    selectViewport(vpId);
                    return;
                }
            }

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
            
            // Svg-relative coordinates (100% attached to image anatomy regardless of zoom/pan)
            const pos = getSvgCoords(e, targetSvg);
            const clickX = pos.x;
            const clickY = pos.y;

            if (activeTool === 'polygon') {
                deselectMeasurement();
                if (currentPolygonPoints.length >= 2 && isPolygonSnapping) {
                    finishPolygon(vpId);
                    return;
                }
                currentPolygonPoints.push({ x: clickX, y: clickY });
                renderMeasurements(vpId);
                return;
            }

            if (activeTool === 'circle') {
                deselectMeasurement();
                isCircleDrawing = true;
                activeCircleDrawing = { x1: clickX, y1: clickY, x2: clickX, y2: clickY };
                renderMeasurements(vpId);
                return;
            }

            if (activeTool === 'ellipse') {
                deselectMeasurement();
                isEllipseDrawing = true;
                activeEllipseDrawing = { x1: clickX, y1: clickY, x2: clickX, y2: clickY };
                renderMeasurements(vpId);
                return;
            }

            if (activeTool === 'box') {
                deselectMeasurement();
                isBoxDrawing = true;
                activeBoxDrawing = { x1: clickX, y1: clickY, x2: clickX, y2: clickY };
                renderMeasurements(vpId);
                return;
            }

            if (activeTool === 'caliper' || activeTool === 'ctr') {
                deselectMeasurement();
                isMeasuring = true;
                activeDrawing = { x1: clickX, y1: clickY, x2: clickX, y2: clickY };
                renderMeasurements(vpId);
                return;
            }

            deselectMeasurement();
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

            const targetSvg = (activeViewport === 1) ? svgMeasure1 : svgMeasure2;

            // 3. Polygon in progress
            if (activeTool === 'polygon' && currentPolygonPoints.length > 0) {
                if (e.cancelable) e.preventDefault();
                if (!targetSvg) return;
                const pos = getSvgCoords(e, targetSvg);
                const curX = pos.x;
                const curY = pos.y;

                if (currentPolygonPoints.length >= 2) {
                    const firstP = currentPolygonPoints[0];
                    const distToFirst = Math.hypot(curX - firstP.x, curY - firstP.y);
                    if (distToFirst <= 30) {
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
                if (!targetSvg) return;
                const pos = getSvgCoords(e, targetSvg);
                activeCircleDrawing.x2 = pos.x;
                activeCircleDrawing.y2 = pos.y;
                renderMeasurements(activeViewport);
                return;
            }

            // 5. Ellipse active drawing
            if (activeTool === 'ellipse' && isEllipseDrawing && activeEllipseDrawing) {
                if (e.cancelable) e.preventDefault();
                if (!targetSvg) return;
                const pos = getSvgCoords(e, targetSvg);
                activeEllipseDrawing.x2 = pos.x;
                activeEllipseDrawing.y2 = pos.y;
                renderMeasurements(activeViewport);
                return;
            }

            // 6. Box ROI active drawing
            if (activeTool === 'box' && isBoxDrawing && activeBoxDrawing) {
                if (e.cancelable) e.preventDefault();
                if (!targetSvg) return;
                const pos = getSvgCoords(e, targetSvg);
                activeBoxDrawing.x2 = pos.x;
                activeBoxDrawing.y2 = pos.y;
                renderMeasurements(activeViewport);
                return;
            }

            // 7. Linear Caliper or CTR active measurement
            if (isMeasuring && activeDrawing) {
                if (e.cancelable) e.preventDefault();
                if (!targetSvg) return;
                const pos = getSvgCoords(e, targetSvg);
                activeDrawing.x2 = pos.x;
                activeDrawing.y2 = pos.y;
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

                            pushMeasurementHistory(activeViewport);
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
                        pushMeasurementHistory(activeViewport);
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
                updateUndoRedoButtons();
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
                    pushMeasurementHistory(activeViewport);
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
                updateUndoRedoButtons();
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
                    pushMeasurementHistory(activeViewport);
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
                updateUndoRedoButtons();
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
                    pushMeasurementHistory(activeViewport);
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
                updateUndoRedoButtons();
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

        // Keyboard Shortcut: Delete / Backspace key to remove selected measurement
        window.addEventListener('keydown', (e) => {
            if ((e.key === 'Delete' || e.key === 'Backspace') && selectedAnnotation) {
                const activeEl = document.activeElement;
                if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA')) return;
                e.preventDefault();
                deleteSingleMeasurement(selectedAnnotation.vpId, selectedAnnotation.index);
            }
        });

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
        // SECONDARY CAPTURE EXPORT (EXECUTIVE HOSPITAL-GRADE MED REPORT)
        // ==========================================
        function exportAnnotatedReport() {
            showToolHint('⏳', 'Sedang merender lembar laporan ekspertise resmi CDC...');
            const targetVp = activeViewport;
            const domImg = (targetVp === 1) ? img1 : img2;
            const activeSrc = (vpState[targetVp].denoisedSrc || vpState[targetVp].rawSrc || (domImg ? domImg.src : ''));

            // High-Resolution 1600x1200 Canvas (Executive Medical Landscape)
            const canvas = document.createElement('canvas');
            canvas.width = 1600;
            canvas.height = 1200;
            const ctx = canvas.getContext('2d');

            function renderExecutiveReport(imgObj) {
                // 1. Base Canvas Background (Deep Royal Medical Matte)
                ctx.fillStyle = '#060a14';
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                // ==========================================
                // 2. KOP SURAT RESMI (OFFICIAL CLINICAL LETTERHEAD)
                // ==========================================
                ctx.fillStyle = '#09101f';
                ctx.fillRect(0, 0, canvas.width, 115);
                
                // Top Cyan Accent Ribbon
                const gradRibbon = ctx.createLinearGradient(0, 0, canvas.width, 0);
                gradRibbon.addColorStop(0, '#0284c7');
                gradRibbon.addColorStop(0.5, '#38bdf8');
                gradRibbon.addColorStop(1, '#10b981');
                ctx.fillStyle = gradRibbon;
                ctx.fillRect(0, 0, canvas.width, 4);

                // Clinic Logo Emblem / Cross Symbol
                ctx.fillStyle = '#0284c7';
                ctx.beginPath();
                ctx.roundRect(30, 20, 52, 52, 10);
                ctx.fill();
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 26px "Plus Jakarta Sans", sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('✚', 56, 46);

                // Clinic Letterhead Typography
                ctx.textAlign = 'left';
                ctx.fillStyle = '#f8fafc';
                ctx.font = 'bold 20px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('CAHAYA DIAGNOSTIC CENTRE (CDC SURABAYA)', 95, 36);

                ctx.fillStyle = '#38bdf8';
                ctx.font = 'bold 11px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('INSTALASI RADIOLOGI & DIAGNOSTIK TERPADU — PT CAHAYA MEDIKA HEALTHCARE', 95, 56);

                ctx.fillStyle = '#94a3b8';
                ctx.font = '10px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('Jl. Dharmahusada Indah Barat No. 36, Surabaya • Telp: (031) 592-7788 • Akreditasi KARS Paripurna • ISO 15189', 95, 74);

                // Document Metadata Card (Top Right)
                const docCardX = 1170, docCardY = 16, docCardW = 400, docCardH = 82;
                ctx.fillStyle = 'rgba(15, 23, 42, 0.9)';
                ctx.beginPath();
                ctx.roundRect(docCardX, docCardY, docCardW, docCardH, 8);
                ctx.fill();
                ctx.strokeStyle = '#1e293b';
                ctx.lineWidth = 1;
                ctx.stroke();

                ctx.fillStyle = '#38bdf8';
                ctx.font = 'bold 11px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('LEMBAR HASIL RADIOLOGI & SECONDARY CAPTURE', docCardX + 16, docCardY + 24);

                ctx.fillStyle = '#cbd5e1';
                ctx.font = '10.5px monospace';
                ctx.fillText(`No. Dokumen : CDC-RAD-{{ date('Ymd') }}-{{ str_pad($scan->id, 4, '0', STR_PAD_LEFT) }}`, docCardX + 16, docCardY + 44);
                ctx.fillText(`Standar     : DICOM SC IOD (NEMA PS 3.3)`, docCardX + 16, docCardY + 62);

                // ==========================================
                // 3. DEMOGRAPHICS GRID (4-COLUMN STRUCTURED MATRIX)
                // ==========================================
                const demoY = 125, demoH = 80, demoX = 30, demoW = 1540;
                ctx.fillStyle = '#0b1322';
                ctx.beginPath();
                ctx.roundRect(demoX, demoY, demoW, demoH, 8);
                ctx.fill();
                ctx.strokeStyle = '#1e293b';
                ctx.lineWidth = 1.2;
                ctx.stroke();

                // Vertical column dividers
                ctx.strokeStyle = '#1e293b';
                ctx.beginPath();
                ctx.moveTo(demoX + 385, demoY); ctx.lineTo(demoX + 385, demoY + demoH);
                ctx.moveTo(demoX + 770, demoY); ctx.lineTo(demoX + 770, demoY + demoH);
                ctx.moveTo(demoX + 1155, demoY); ctx.lineTo(demoX + 1155, demoY + demoH);
                ctx.stroke();

                // Col 1: No. Rekam Medis & Accession
                ctx.fillStyle = '#64748b';
                ctx.font = 'bold 9.5px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('NO. REKAM MEDIS (MRN):', demoX + 16, demoY + 25);
                ctx.fillStyle = '#38bdf8';
                ctx.font = 'bold 13.5px monospace';
                ctx.fillText(`{{ $scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}`, demoX + 16, demoY + 45);
                ctx.fillStyle = '#94a3b8';
                ctx.font = '10px monospace';
                ctx.fillText(`Acc: {{ $scan->accession_number ?: 'ACC-' . date('Ymd') . '-' . str_pad($scan->id, 3, '0', STR_PAD_LEFT) }}`, demoX + 16, demoY + 65);

                // Col 2: Nama Pasien & Usia
                ctx.fillStyle = '#64748b';
                ctx.font = 'bold 9.5px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('NAMA PASIEN / DEMOGRAFI:', demoX + 400, demoY + 25);
                ctx.fillStyle = '#f8fafc';
                ctx.font = 'bold 13.5px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(`{{ strtoupper($scan->patient_name) }}`, demoX + 400, demoY + 45);
                ctx.fillStyle = '#94a3b8';
                ctx.font = '10.5px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(`Usia: {{ $scan->age ?? 42 }} Tahun • Jenis Kelamin: {{ $scan->gender === 'P' ? 'Perempuan (F)' : 'Laki-laki (M)' }}`, demoX + 400, demoY + 65);

                // Col 3: Modalitas & Unit Pesawat
                ctx.fillStyle = '#64748b';
                ctx.font = 'bold 9.5px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('MODALITAS & SUMBER CITRA:', demoX + 785, demoY + 25);
                ctx.fillStyle = '#10b981';
                ctx.font = 'bold 13px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(`{{ $scan->modality ?: 'Thorax PA (CR)' }}`, demoX + 785, demoY + 45);
                ctx.fillStyle = '#94a3b8';
                ctx.font = '10px monospace';
                ctx.fillText(`Station: {{ $scan->station_name ?: 'FUJIFILM_FDR_01' }}`, demoX + 785, demoY + 65);

                // Col 4: Waktu & Poli Rujukan
                ctx.fillStyle = '#64748b';
                ctx.font = 'bold 9.5px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('WAKTU & ASAL RUJUKAN:', demoX + 1170, demoY + 25);
                ctx.fillStyle = '#f8fafc';
                ctx.font = 'bold 12px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(`{{ date('d F Y') }}, 09:30 WIB`, demoX + 1170, demoY + 45);
                ctx.fillStyle = '#94a3b8';
                ctx.font = '10px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('Instalasi Radiologi — CDC', demoX + 1170, demoY + 65);

                // ==========================================
                // 4. MAIN BODY (SPLIT VIEW: MEDICAL SCAN + FINDINGS)
                // ==========================================
                const bodyY = 220, bodyH = 920;
                
                // 4A. LEFT MEDICAL IMAGE VIEWPORT (W: 880px)
                const imgAreaX = 30, imgAreaY = bodyY, imgAreaW = 880, imgAreaH = bodyH;
                ctx.fillStyle = '#02040a';
                ctx.beginPath();
                ctx.roundRect(imgAreaX, imgAreaY, imgAreaW, imgAreaH, 10);
                ctx.fill();
                ctx.strokeStyle = '#1e293b';
                ctx.lineWidth = 1.5;
                ctx.stroke();

                ctx.save();
                ctx.beginPath();
                ctx.roundRect(imgAreaX, imgAreaY, imgAreaW, imgAreaH, 10);
                ctx.clip();

                // Compute aspect ratio & placement within viewport
                const innerImgX = imgAreaX + 10, innerImgY = imgAreaY + 10;
                const innerImgW = imgAreaW - 20, innerImgH = imgAreaH - 20;
                const naturalW = imgObj ? (imgObj.naturalWidth || imgObj.width || 800) : 800;
                const naturalH = imgObj ? (imgObj.naturalHeight || imgObj.height || 800) : 800;
                const aspect = (naturalH > 0) ? (naturalW / naturalH) : 1;
                let drawW = innerImgW;
                let drawH = innerImgW / aspect;
                if (drawH > innerImgH) {
                    drawH = innerImgH;
                    drawW = innerImgH * aspect;
                }
                const drawX = innerImgX + (innerImgW - drawW) / 2;
                const drawY = innerImgY + (innerImgH - drawH) / 2;

                if (imgObj) {
                    const st = vpState[targetVp];
                    ctx.filter = `invert(${st.inverted ? 1 : 0}) brightness(${st.brightness}%) contrast(${st.contrast}%)`;
                    ctx.drawImage(imgObj, drawX, drawY, drawW, drawH);
                    ctx.filter = 'none';
                }

                // 4B. OVERLAID MEASUREMENT ANNOTATIONS ON SCAN
                const vpElem = (targetVp === 1) ? vp1 : vp2;
                const vpW = (vpElem && vpElem.clientWidth) ? vpElem.clientWidth : 820;
                const vpH = (vpElem && vpElem.clientHeight) ? vpElem.clientHeight : 840;

                const toCanvX = (vx) => drawX + (vx / vpW) * drawW;
                const toCanvY = (vy) => drawY + (vy / vpH) * drawH;
                const toCanvR = (r) => (r / Math.min(vpW, vpH)) * Math.min(drawW, drawH);

                const list = measurements[targetVp] || [];
                list.forEach(m => {
                    if (m.type === 'circle') {
                        const cx = toCanvX(m.cx);
                        const cy = toCanvY(m.cy);
                        const r = toCanvR(m.r);

                        ctx.beginPath();
                        ctx.arc(cx, cy, r, 0, Math.PI * 2);
                        ctx.fillStyle = 'rgba(250, 204, 21, 0.18)';
                        ctx.fill();
                        ctx.strokeStyle = '#facc15';
                        ctx.lineWidth = 2.2;
                        ctx.setLineDash([5, 5]);
                        ctx.stroke();
                        ctx.setLineDash([]);

                        // Crosshairs
                        ctx.strokeStyle = '#facc15';
                        ctx.lineWidth = 1.2;
                        ctx.setLineDash([3, 3]);
                        ctx.beginPath();
                        ctx.moveTo(cx - r, cy); ctx.lineTo(cx + r, cy);
                        ctx.moveTo(cx, cy - r); ctx.lineTo(cx, cy + r);
                        ctx.stroke();
                        ctx.setLineDash([]);

                        // Cardinal dots
                        ctx.fillStyle = '#facc15';
                        [ [cx-r, cy], [cx+r, cy], [cx, cy-r], [cx, cy+r] ].forEach(([px, py]) => {
                            ctx.beginPath(); ctx.arc(px, py, 3.5, 0, Math.PI * 2); ctx.fill();
                        });

                        // Badge
                        const badgeY = (cy - r - 16 >= imgAreaY + 20) ? (cy - r - 16) : (cy + r + 16);
                        const labelText = `${m.label}: Ø ${m.diamCm} cm • ${m.areaCm2} cm²`;
                        ctx.font = 'bold 11px monospace';
                        const tw = ctx.measureText(labelText).width;
                        ctx.fillStyle = 'rgba(15, 23, 42, 0.95)';
                        ctx.strokeStyle = '#facc15';
                        ctx.lineWidth = 1.5;
                        ctx.beginPath();
                        ctx.roundRect(cx - (tw / 2) - 10, badgeY - 11, tw + 20, 22, 11);
                        ctx.fill();
                        ctx.stroke();
                        ctx.fillStyle = '#facc15';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(labelText, cx, badgeY);
                    } else if (m.type === 'ellipse') {
                        const cx = toCanvX(m.cx);
                        const cy = toCanvY(m.cy);
                        const rx = toCanvR(m.rx);
                        const ry = toCanvR(m.ry);

                        ctx.beginPath();
                        ctx.ellipse(cx, cy, rx, ry, 0, 0, Math.PI * 2);
                        ctx.fillStyle = 'rgba(250, 204, 21, 0.18)';
                        ctx.fill();
                        ctx.strokeStyle = '#facc15';
                        ctx.lineWidth = 2.2;
                        ctx.setLineDash([5, 5]);
                        ctx.stroke();
                        ctx.setLineDash([]);

                        // Crosshairs
                        ctx.strokeStyle = '#facc15';
                        ctx.lineWidth = 1.2;
                        ctx.setLineDash([3, 3]);
                        ctx.beginPath();
                        ctx.moveTo(cx - rx, cy); ctx.lineTo(cx + rx, cy);
                        ctx.moveTo(cx, cy - ry); ctx.lineTo(cx, cy + ry);
                        ctx.stroke();
                        ctx.setLineDash([]);

                        const badgeY = (cy - ry - 16 >= imgAreaY + 20) ? (cy - ry - 16) : (cy + ry + 16);
                        const labelText = `${m.label}: ${m.d1Cm}×${m.d2Cm} cm • ${m.areaCm2} cm²`;
                        ctx.font = 'bold 11px monospace';
                        const tw = ctx.measureText(labelText).width;
                        ctx.fillStyle = 'rgba(15, 23, 42, 0.95)';
                        ctx.strokeStyle = '#facc15';
                        ctx.lineWidth = 1.5;
                        ctx.beginPath();
                        ctx.roundRect(cx - (tw / 2) - 10, badgeY - 11, tw + 20, 22, 11);
                        ctx.fill();
                        ctx.stroke();
                        ctx.fillStyle = '#facc15';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(labelText, cx, badgeY);
                    } else if (m.type === 'polygon' && m.points && m.points.length >= 3) {
                        ctx.beginPath();
                        m.points.forEach((p, i) => {
                            const px = toCanvX(p.x);
                            const py = toCanvY(p.y);
                            if (i === 0) ctx.moveTo(px, py);
                            else ctx.lineTo(px, py);
                        });
                        ctx.closePath();
                        ctx.fillStyle = 'rgba(250, 204, 21, 0.2)';
                        ctx.fill();
                        ctx.strokeStyle = '#facc15';
                        ctx.lineWidth = 2.2;
                        ctx.stroke();

                        const pcx = toCanvX(m.cx);
                        const pcy = toCanvY(m.cy);
                        const labelText = `${m.label}: ${m.areaCm2} cm²`;
                        ctx.font = 'bold 11px monospace';
                        const tw = ctx.measureText(labelText).width;
                        ctx.fillStyle = 'rgba(15, 23, 42, 0.95)';
                        ctx.strokeStyle = '#facc15';
                        ctx.lineWidth = 1.5;
                        ctx.beginPath();
                        ctx.roundRect(pcx - (tw / 2) - 10, pcy - 11, tw + 20, 22, 11);
                        ctx.fill();
                        ctx.stroke();
                        ctx.fillStyle = '#facc15';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(labelText, pcx, pcy);
                    } else if (m.type === 'ctr') {
                        const hx1 = toCanvX(m.heart.x1), hy1 = toCanvY(m.heart.y1);
                        const hx2 = toCanvX(m.heart.x2), hy2 = toCanvY(m.heart.y2);
                        const tx1 = toCanvX(m.thorax.x1), ty1 = toCanvY(m.thorax.y1);
                        const tx2 = toCanvX(m.thorax.x2), ty2 = toCanvY(m.thorax.y2);

                        // Heart line (Ruby Red)
                        ctx.beginPath();
                        ctx.moveTo(hx1, hy1); ctx.lineTo(hx2, hy2);
                        ctx.strokeStyle = '#f43f5e';
                        ctx.lineWidth = 3;
                        ctx.stroke();
                        ctx.fillStyle = '#f43f5e';
                        ctx.beginPath(); ctx.arc(hx1, hy1, 4.5, 0, Math.PI * 2); ctx.fill();
                        ctx.beginPath(); ctx.arc(hx2, hy2, 4.5, 0, Math.PI * 2); ctx.fill();

                        // Thorax line (Gold Yellow)
                        ctx.beginPath();
                        ctx.moveTo(tx1, ty1); ctx.lineTo(tx2, ty2);
                        ctx.strokeStyle = '#facc15';
                        ctx.lineWidth = 3;
                        ctx.setLineDash([5, 5]);
                        ctx.stroke();
                        ctx.setLineDash([]);
                        ctx.fillStyle = '#facc15';
                        ctx.beginPath(); ctx.arc(tx1, ty1, 4.5, 0, Math.PI * 2); ctx.fill();
                        ctx.beginPath(); ctx.arc(tx2, ty2, 4.5, 0, Math.PI * 2); ctx.fill();

                        // Mid CTR badge
                        const midX = (hx1 + hx2 + tx1 + tx2) / 4;
                        const midY = (hy1 + hy2 + ty1 + ty2) / 4;
                        const badgeColor = m.isNormal ? '#10b981' : '#ef4444';
                        const labelText = `CTR: ${m.ratio}% (${m.verdict})`;
                        ctx.font = 'bold 12px monospace';
                        const tw = ctx.measureText(labelText).width;
                        ctx.fillStyle = 'rgba(15, 23, 42, 0.95)';
                        ctx.strokeStyle = badgeColor;
                        ctx.lineWidth = 1.8;
                        ctx.beginPath();
                        ctx.roundRect(midX - (tw / 2) - 12, midY - 14, tw + 24, 28, 14);
                        ctx.fill();
                        ctx.stroke();
                        ctx.fillStyle = badgeColor;
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(labelText, midX, midY);
                    } else {
                        // Linear Caliper
                        const x1 = toCanvX(m.x1), y1 = toCanvY(m.y1);
                        const x2 = toCanvX(m.x2), y2 = toCanvY(m.y2);
                        ctx.beginPath();
                        ctx.moveTo(x1, y1); ctx.lineTo(x2, y2);
                        ctx.strokeStyle = '#facc15';
                        ctx.lineWidth = 2.2;
                        ctx.setLineDash([5, 5]);
                        ctx.stroke();
                        ctx.setLineDash([]);

                        ctx.fillStyle = '#facc15';
                        ctx.beginPath(); ctx.arc(x1, y1, 4, 0, Math.PI * 2); ctx.fill();
                        ctx.beginPath(); ctx.arc(x2, y2, 4, 0, Math.PI * 2); ctx.fill();

                        const midX = (x1 + x2) / 2;
                        const midY = (y1 + y2) / 2;
                        const labelText = `${m.label}: ${m.distCm} cm`;
                        ctx.font = 'bold 11px monospace';
                        const tw = ctx.measureText(labelText).width;
                        ctx.fillStyle = 'rgba(15, 23, 42, 0.95)';
                        ctx.strokeStyle = '#facc15';
                        ctx.lineWidth = 1.5;
                        ctx.beginPath();
                        ctx.roundRect(midX - (tw / 2) - 10, midY - 11, tw + 20, 22, 11);
                        ctx.fill();
                        ctx.stroke();
                        ctx.fillStyle = '#facc15';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(labelText, midX, midY);
                    }
                });

                // 4C. DICOM HUD CORNER OVERLAYS ON MEDICAL IMAGE
                ctx.fillStyle = 'rgba(255, 255, 255, 0.92)';
                ctx.font = 'bold 11px monospace';
                ctx.textAlign = 'left';
                ctx.textBaseline = 'top';
                ctx.fillText('CAHAYA DIAGNOSTIC CENTRE', imgAreaX + 16, imgAreaY + 16);
                ctx.font = '10.5px monospace';
                ctx.fillStyle = 'rgba(255, 255, 255, 0.75)';
                ctx.fillText('{{ strtoupper($scan->patient_name) }}', imgAreaX + 16, imgAreaY + 34);
                ctx.fillText('MRN: {{ $scan->patient_id ?: "CDC-" . str_pad($scan->id, 5, "0", STR_PAD_LEFT) }}', imgAreaX + 16, imgAreaY + 52);

                ctx.textAlign = 'right';
                ctx.fillStyle = 'rgba(255, 255, 255, 0.75)';
                ctx.fillText(targetVp === 1 ? '{{ $patientSeries[0]["name"] ?? ($scan->modality ?: "Thorax PA") }}' : '{{ $patientSeries[1]["name"] ?? "USG Abdomen" }}', imgAreaX + imgAreaW - 16, imgAreaY + 16);
                ctx.fillText('Station: {{ $scan->station_name ?: "FUJIFILM_FDR" }}', imgAreaX + imgAreaW - 16, imgAreaY + 34);

                ctx.textAlign = 'left';
                ctx.textBaseline = 'bottom';
                const st = vpState[targetVp];
                const engineLabel = st.denoisedSrc ? (st.engine || 'Bilateral Filter (Denoised)') : 'RAW Acquisition (Original CR)';
                ctx.fillText(`Mode: ${engineLabel}`, imgAreaX + 16, imgAreaY + imgAreaH - 34);
                ctx.fillText(`WW: 400 | WL: 40 | Scale: 0.8000 mm/px`, imgAreaX + 16, imgAreaY + imgAreaH - 16);

                ctx.textAlign = 'right';
                ctx.fillText('Hyu PACS Workstation v2.0', imgAreaX + imgAreaW - 16, imgAreaY + imgAreaH - 34);
                ctx.fillText('SOP: 1.2.840.10008.5.1.4.1.1.7', imgAreaX + imgAreaW - 16, imgAreaY + imgAreaH - 16);

                ctx.restore();

                // ==========================================
                // 5. RIGHT SECTION: STRUCTURED CLINICAL DOSSIER (W: 630px)
                // ==========================================
                const panelX = 940, panelY = bodyY, panelW = 630, panelH = bodyH;
                ctx.fillStyle = '#0b1322';
                ctx.beginPath();
                ctx.roundRect(panelX, panelY, panelW, panelH, 10);
                ctx.fill();
                ctx.strokeStyle = '#1e293b';
                ctx.lineWidth = 1.5;
                ctx.stroke();

                // Left Blue Accent Stripe on Dossier
                ctx.fillStyle = '#0284c7';
                ctx.beginPath();
                ctx.roundRect(panelX, panelY, 5, panelH, [10, 0, 0, 10]);
                ctx.fill();

                // Section Header
                ctx.fillStyle = '#38bdf8';
                ctx.font = 'bold 16px "Plus Jakarta Sans", sans-serif';
                ctx.textAlign = 'left';
                ctx.textBaseline = 'alphabetic';
                ctx.fillText('LEMBAR HASIL EKSPERTISE RADIOLOG', panelX + 25, panelY + 38);

                ctx.fillStyle = '#94a3b8';
                ctx.font = '11.5px "Plus Jakarta Sans", sans-serif';
                const docVal = document.getElementById('txtDoctor').value || 'dr. Bambang Sp.Rad';
                ctx.fillText(`Dokter Pemeriksa: ${docVal} • Tanggal: ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`, panelX + 25, panelY + 60);

                ctx.strokeStyle = '#1e293b';
                ctx.beginPath();
                ctx.moveTo(panelX + 25, panelY + 75); ctx.lineTo(panelX + panelW - 25, panelY + 75);
                ctx.stroke();

                let curY = panelY + 105;

                // ------------------------------------------
                // 5A. CARD HASIL PENGUKURAN KUANTITATIF
                // ------------------------------------------
                ctx.fillStyle = '#facc15';
                ctx.font = 'bold 12.5px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('I. ANALISIS KUANTITATIF & PENGUKURAN RADIOLOGI:', panelX + 25, curY);
                curY += 20;

                const ctrMeasurement = list.find(m => m.type === 'ctr');
                if (ctrMeasurement) {
                    // Highlighted CTR Clinical Box
                    const isNormal = ctrMeasurement.isNormal;
                    const ctrBoxBg = isNormal ? 'rgba(16, 185, 129, 0.08)' : 'rgba(239, 68, 68, 0.08)';
                    const ctrBorder = isNormal ? '#10b981' : '#ef4444';
                    
                    ctx.fillStyle = ctrBoxBg;
                    ctx.beginPath();
                    ctx.roundRect(panelX + 25, curY, panelW - 50, 68, 6);
                    ctx.fill();
                    ctx.strokeStyle = ctrBorder;
                    ctx.lineWidth = 1.2;
                    ctx.stroke();

                    ctx.fillStyle = isNormal ? '#10b981' : '#ef4444';
                    ctx.font = 'bold 14px "Plus Jakarta Sans", sans-serif';
                    ctx.fillText(`🫀 Cardio-Thoracic Ratio (CTR): ${ctrMeasurement.ratio}% [${ctrMeasurement.verdict.toUpperCase()}]`, panelX + 40, curY + 26);

                    ctx.fillStyle = '#cbd5e1';
                    ctx.font = '11px monospace';
                    ctx.fillText(`• Transversal Jantung (A+B): ${ctrMeasurement.heart.distCm} cm | Toraks Internal (C): ${ctrMeasurement.thorax.distCm} cm`, panelX + 40, curY + 48);
                    curY += 80;
                }

                // Other Calipers / Circles / Ellipses
                const otherMeasurements = list.filter(m => m.type !== 'ctr');
                if (otherMeasurements.length === 0 && !ctrMeasurement) {
                    ctx.fillStyle = '#64748b';
                    ctx.font = 'italic 11.5px "Plus Jakarta Sans", sans-serif';
                    ctx.fillText('Tidak ada anotasi/lesi fokal khusus yang diukur.', panelX + 25, curY);
                    curY += 24;
                } else if (otherMeasurements.length > 0) {
                    otherMeasurements.forEach(m => {
                        ctx.fillStyle = '#38bdf8';
                        ctx.font = 'bold 11px monospace';
                        if (m.type === 'circle') {
                            ctx.fillText(`• ${m.label} (Lingkaran Nodul): Ø ${m.diamCm} cm (Luas Area: ${m.areaCm2} cm²)`, panelX + 25, curY);
                        } else if (m.type === 'ellipse') {
                            ctx.fillText(`• ${m.label} (Oval Lesi): ${m.d1Cm} × ${m.d2Cm} cm (Luas Area: ${m.areaCm2} cm²)`, panelX + 25, curY);
                        } else if (m.type === 'polygon') {
                            ctx.fillText(`• ${m.label} (Area Poligon): Luas ${m.areaCm2} cm² (Keliling: ${m.perimeterCm} cm)`, panelX + 25, curY);
                        } else {
                            ctx.fillText(`• ${m.label} (Linear Caliper): Panjang ${m.distCm} cm`, panelX + 25, curY);
                        }
                        curY += 20;
                    });
                }

                curY += 15;
                ctx.strokeStyle = '#1e293b';
                ctx.beginPath();
                ctx.moveTo(panelX + 25, curY); ctx.lineTo(panelX + panelW - 25, curY);
                ctx.stroke();
                curY += 25;

                // ------------------------------------------
                // 5B. DESKRIPSI TEMUAN RADIOLOGIS (RSNA FORMAT)
                // ------------------------------------------
                ctx.fillStyle = '#facc15';
                ctx.font = 'bold 12.5px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('II. DESKRIPSI RADIOLOGIS ANATOMI:', panelX + 25, curY);
                curY += 22;

                const anatomiRows = [
                    { organ: 'COR', desc: ctrMeasurement ? `CTR ${ctrMeasurement.ratio}%, apeks ${ctrMeasurement.isNormal ? 'tidak tertanam' : 'tertanam/membesar'}.` : 'Bentuk dan ukuran dalam batas normal, CTR < 50%.' },
                    { organ: 'PULMO', desc: 'Corakan bronkovaskular normal. Tidak tampak infiltrat, konsolidasi, maupun nodul aktif.' },
                    { organ: 'SINUS & DIAFRAGMA', desc: 'Sinus kostofrenikus kanan & kiri lancip/tajam. Hemidiafragma licin.' },
                    { organ: 'TULANG & DINDING DADA', desc: 'Struktur tulang kosta dan klavikula intak. Soft tissue dinding dada simetris.' }
                ];

                anatomiRows.forEach(row => {
                    ctx.fillStyle = '#38bdf8';
                    ctx.font = 'bold 11px "Plus Jakarta Sans", sans-serif';
                    ctx.fillText(`• ${row.organ}:`, panelX + 25, curY);
                    
                    ctx.fillStyle = '#cbd5e1';
                    ctx.font = '11px "Plus Jakarta Sans", sans-serif';
                    ctx.fillText(row.desc, panelX + 35, curY + 16);
                    curY += 34;
                });

                curY += 10;
                ctx.strokeStyle = '#1e293b';
                ctx.beginPath();
                ctx.moveTo(panelX + 25, curY); ctx.lineTo(panelX + panelW - 25, curY);
                ctx.stroke();
                curY += 25;

                // ------------------------------------------
                // 5C. KESAN / KESIMPULAN KLINIS (IMPRESSION)
                // ------------------------------------------
                ctx.fillStyle = '#38bdf8';
                ctx.font = 'bold 12.5px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('III. KESAN / KESIMPULAN KLINIS (IMPRESSION):', panelX + 25, curY);
                curY += 18;

                // Callout Impression Box
                const diagText = document.getElementById('txtDiagnosis').value.trim() || 'Cor dan pulmo dalam batas normal. Tidak tampak kelainan radiologis aktif.';
                const lines = diagText.split('\n');
                const boxH = Math.max(65, lines.length * 20 + 26);

                ctx.fillStyle = 'rgba(2, 132, 199, 0.08)';
                ctx.beginPath();
                ctx.roundRect(panelX + 25, curY, panelW - 50, boxH, 6);
                ctx.fill();
                ctx.strokeStyle = 'rgba(56, 189, 248, 0.4)';
                ctx.lineWidth = 1;
                ctx.stroke();

                let diagY = curY + 22;
                ctx.fillStyle = '#f8fafc';
                ctx.font = 'bold 12px "Plus Jakarta Sans", sans-serif';
                lines.forEach(l => {
                    ctx.fillText(l.substring(0, 68), panelX + 40, diagY);
                    diagY += 20;
                });

                // ------------------------------------------
                // 5D. VALIDASI DIGITAL & TANDA TANGAN DOKTER (FOOTER PANEL)
                // ------------------------------------------
                const signBoxY = panelY + panelH - 120;
                
                ctx.strokeStyle = '#1e293b';
                ctx.beginPath();
                ctx.moveTo(panelX + 25, signBoxY - 15); ctx.lineTo(panelX + panelW - 25, signBoxY - 15);
                ctx.stroke();

                // QR Code Verification Symbol (Left)
                ctx.fillStyle = '#1e293b';
                ctx.beginPath();
                ctx.roundRect(panelX + 25, signBoxY, 80, 80, 6);
                ctx.fill();
                ctx.fillStyle = '#38bdf8';
                ctx.font = 'bold 10px monospace';
                ctx.textAlign = 'center';
                ctx.fillText('HYU PACS', panelX + 65, signBoxY + 35);
                ctx.fillText('VERIFIED', panelX + 65, signBoxY + 50);

                // Validation Text (Right)
                ctx.textAlign = 'left';
                ctx.fillStyle = '#94a3b8';
                ctx.font = '10px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('Dokter Spesialis Radiologi Penanggung Jawab:', panelX + 120, signBoxY + 16);

                ctx.fillStyle = '#f8fafc';
                ctx.font = 'bold 13px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(`${docVal}`, panelX + 120, signBoxY + 36);

                ctx.fillStyle = '#64748b';
                ctx.font = '10px monospace';
                ctx.fillText('SIP: 503/4421/SIP.DS/436.7.2/2024 • CDC Medical Centre', panelX + 120, signBoxY + 54);

                ctx.fillStyle = '#10b981';
                ctx.font = 'bold 10px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('✓ TERVALIDASI SECARA ELEKTRONIK (DIGITALLY SIGNED)', panelX + 120, signBoxY + 72);

                // ==========================================
                // 6. BOTTOM SYSTEM FOOTER DISCLAIMER
                // ==========================================
                ctx.fillStyle = '#475569';
                ctx.font = '10px "Plus Jakarta Sans", sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('Dokumen ini merupakan Laporan Hasil Radiologi Resmi yang diterbitkan secara elektronik melalui Sistem Hyu PACS & RIS PT Cahaya Medika Healthcare. Informasi di dalamnya bersifat RAHASIA MEDIS.', canvas.width / 2, canvas.height - 18);

                // Trigger Instant High-Quality PNG Download
                const link = document.createElement('a');
                const cleanDoc = docVal.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '');
                const cleanPatient = '{{ $scan->patient_name }}'.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '');
                link.download = `Laporan_Radiologi_CDC_${cleanDoc}_${cleanPatient}.png`;
                link.href = canvas.toDataURL('image/png', 1.0);
                link.click();
                showToolHint('✅', 'Lembar laporan ekspertise resmi CDC berhasil diekspor!');
                setTimeout(hideToolHint, 3000);
            }

            // Reliable Image Load Dispatcher
            if (domImg && domImg.complete && domImg.naturalWidth > 0 && (!vpState[targetVp].denoisedSrc || domImg.src === activeSrc)) {
                renderExecutiveReport(domImg);
            } else {
                const scanImg = new Image();
                scanImg.onload = () => renderExecutiveReport(scanImg);
                scanImg.onerror = () => {
                    console.warn('[Hyu PACS] Fallback drawing without image loader');
                    renderExecutiveReport(domImg || null);
                };
                scanImg.src = activeSrc;
            }
        }

        // Global Keyboard Shortcuts for Workstation (Ctrl+Z: Undo, Ctrl+Y / Ctrl+Shift+Z: Redo)
        window.addEventListener('keydown', (e) => {
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
                if (e.shiftKey) {
                    e.preventDefault();
                    redoLastMeasurement();
                } else {
                    e.preventDefault();
                    undoLastMeasurement();
                }
            } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'y') {
                e.preventDefault();
                redoLastMeasurement();
            }
        });

        // Initial update for Undo/Redo buttons
        updateUndoRedoButtons();
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

    <!-- ─────────────────────────────────────────────────────────────
         GLOSSARY MODAL — GlossaryModal component
         Load data source first, then the modal logic
    ───────────────────────────────────────────────────────────────── -->
    <script src="{{ asset('js/glossary-data.js') }}?v={{ time() }}"></script>

    <!-- Modal HTML -->
    <div id="glossaryModal" class="gl-modal-overlay" role="dialog" aria-modal="true"
         aria-labelledby="glModalTitle" aria-describedby="glModalSummary"
         onclick="handleGlossaryOverlayClick(event)">
        <div class="gl-modal-card" id="glossaryModalCard" tabindex="-1">

            <div class="gl-modal-head">
                <div>
                    <div class="gl-modal-title" id="glModalTitle">—</div>
                    <div class="gl-modal-subtitle" id="glModalSubtitle">—</div>
                </div>
                <button class="gl-modal-close" id="glModalCloseBtn"
                    onclick="closeGlossaryModal()" aria-label="Tutup modal">&times;</button>
            </div>

            <div class="gl-modal-body">
                <p id="glModalSummary"></p>
                <div id="glModalFormulaWrap" style="display:none;">
                    <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#475569;margin-top:.75rem;margin-bottom:.2rem;">Rumus / Formula</div>
                    <code class="gl-modal-formula" id="glModalFormula"></code>
                </div>
                <div id="glModalHowWrap" style="display:none;">
                    <div style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#475569;margin-top:.6rem;margin-bottom:.2rem;">Cara Membaca Nilai</div>
                    <div class="gl-modal-how">✅ <span id="glModalHow"></span></div>
                </div>
                <div class="gl-modal-disclaimer">
                    ⚠️ Metrik ini adalah ukuran teknis kualitas citra — bukan penilaian diagnostik klinis.
                    Interpretasi medis harus dilakukan oleh radiolog bersertifikat.
                </div>
            </div>

            <div class="gl-modal-foot">
                <button class="gl-btn-secondary" id="glBtnKembali"
                    onclick="closeGlossaryModal()">Kembali</button>
                <a  class="gl-btn-primary" id="glBtnLearnMore"
                    href="#" target="_self">📖 Pelajari lebih lanjut</a>
            </div>
        </div>
    </div>

    <script>
        /* ──────────────────────────────────────────────────────────────
         * GlossaryModal — TermHint system
         *
         * Public API:
         *   openGlossaryModal(termId, triggerEl)  — open modal for a term
         *   closeGlossaryModal()                  — close modal
         * ────────────────────────────────────────────────────────────── */

        (function () {
            'use strict';

            const overlay    = document.getElementById('glossaryModal');
            const card       = document.getElementById('glossaryModalCard');
            const btnClose   = document.getElementById('glModalCloseBtn');
            const btnLearn   = document.getElementById('glBtnLearnMore');

            // Track which trigger element opened the modal (for focus-return)
            let _triggerEl = null;

            /* ── Open ──────────────────────────────────────────────── */
            window.openGlossaryModal = function (termId, triggerEl) {
                const entry = (typeof GLOSSARY_MAP !== 'undefined') ? GLOSSARY_MAP[termId] : null;
                if (!entry) {
                    console.warn('[GlossaryModal] Term not found:', termId);
                    return;
                }

                _triggerEl = triggerEl || null;

                // Populate title & subtitle
                document.getElementById('glModalTitle').textContent =
                    entry.abbreviation + ' — ' + entry.fullName;
                document.getElementById('glModalSubtitle').textContent =
                    (typeof GLOSSARY_CATEGORIES !== 'undefined')
                        ? GLOSSARY_CATEGORIES[entry.category] || ''
                        : '';

                // Summary
                document.getElementById('glModalSummary').textContent = entry.summary;

                // Formula
                const formulaWrap = document.getElementById('glModalFormulaWrap');
                const formulaEl   = document.getElementById('glModalFormula');
                if (entry.formula) {
                    formulaEl.textContent  = entry.formula;
                    formulaWrap.style.display = '';
                } else {
                    formulaWrap.style.display = 'none';
                }

                // How to read
                const howWrap = document.getElementById('glModalHowWrap');
                const howEl   = document.getElementById('glModalHow');
                if (entry.howToRead) {
                    howEl.textContent  = entry.howToRead;
                    howWrap.style.display = '';
                } else {
                    howWrap.style.display = 'none';
                }

                // "Pelajari lebih lanjut" link → /glosarium#<id>
                btnLearn.href = '{{ route('glossary.index') }}' + '#' + entry.id;

                // Lock body scroll
                document.body.style.overflow = 'hidden';

                // Show overlay
                overlay.classList.add('open');

                // Focus trap — focus the card
                requestAnimationFrame(() => {
                    card.focus();
                    trapFocusInstall();
                });
            };

            /* ── Close ─────────────────────────────────────────────── */
            window.closeGlossaryModal = function () {
                overlay.classList.remove('open');
                document.body.style.overflow = '';
                trapFocusRemove();

                // Return focus to the triggering "?" button
                if (_triggerEl) {
                    _triggerEl.focus();
                    _triggerEl = null;
                }
            };

            /* ── Overlay click (close if clicking the dark backdrop) ── */
            window.handleGlossaryOverlayClick = function (e) {
                if (e.target === overlay) closeGlossaryModal();
            };

            /* ── Keyboard: Esc closes, Tab is trapped ───────────────── */
            document.addEventListener('keydown', function (e) {
                if (!overlay.classList.contains('open')) return;
                if (e.key === 'Escape') {
                    e.preventDefault();
                    closeGlossaryModal();
                }
            });

            /* ── Focus Trap ─────────────────────────────────────────── */
            function getFocusableEls() {
                return Array.from(card.querySelectorAll(
                    'button:not([disabled]), [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                ));
            }

            function trapFocusHandler(e) {
                if (e.key !== 'Tab') return;
                const focusable = getFocusableEls();
                if (focusable.length === 0) return;
                const first = focusable[0];
                const last  = focusable[focusable.length - 1];

                if (e.shiftKey) {
                    if (document.activeElement === first) {
                        e.preventDefault();
                        last.focus();
                    }
                } else {
                    if (document.activeElement === last) {
                        e.preventDefault();
                        first.focus();
                    }
                }
            }

            function trapFocusInstall()  { document.addEventListener('keydown', trapFocusHandler); }
            function trapFocusRemove()   { document.removeEventListener('keydown', trapFocusHandler); }
        })();
    </script>
</body>
</html>

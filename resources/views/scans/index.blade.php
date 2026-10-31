<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hyu - Pusat Operasional MCU & Standalone PACS Radiologi</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #090e17;
            --card-bg: #111a2e;
            --card-border: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #0284c7;
            --primary-hover: #0369a1;
            --accent-cyan: #06b6d4;
            --accent-green: #10b981;
            --accent-purple: #8b5cf6;
            --accent-amber: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1.5rem;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
        }

        /* Top Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--card-border);
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .brand-title {
            font-size: 1.5rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-logo {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1.1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            color: white;
            border-color: #38bdf8;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }

        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: rgba(30, 41, 59, 0.8);
            color: var(--text-main);
            border-color: var(--card-border);
        }

        .btn-secondary:hover {
            background-color: #1e293b;
            border-color: #475569;
        }

        .btn-fuji {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(6, 182, 212, 0.2));
            color: #c084fc;
            border-color: rgba(139, 92, 246, 0.4);
        }

        .btn-fuji:hover {
            background: rgba(139, 92, 246, 0.3);
            color: #e9d5ff;
        }

        /* DICOM Node Info Card */
        .pacs-info-card {
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.9), rgba(17, 26, 46, 0.9));
            border: 1px solid rgba(56, 189, 248, 0.3);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .pacs-node-badges {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.78rem;
        }

        .pacs-pill {
            background-color: rgba(2, 132, 199, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: #38bdf8;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
        }

        /* KPI Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .stat-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 1.15rem 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .stat-card.stat-all::before { background-color: var(--primary); }
        .stat-card.stat-waiting::before { background-color: var(--accent-amber); }
        .stat-card.stat-ready::before { background-color: var(--accent-cyan); }
        .stat-card.stat-done::before { background-color: var(--accent-green); }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
        }

        /* Filter Bar */
        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-tabs {
            display: flex;
            gap: 0.4rem;
            background-color: rgba(15, 23, 42, 0.6);
            padding: 0.3rem;
            border-radius: 8px;
            border: 1px solid var(--card-border);
        }

        .filter-tab {
            padding: 0.35rem 0.8rem;
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 6px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.15s;
        }

        .filter-tab:hover, .filter-tab.active {
            background-color: var(--primary);
            color: white;
        }

        .search-box {
            display: flex;
            gap: 0.4rem;
        }

        .search-input {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            font-size: 0.825rem;
            outline: none;
            min-width: 260px;
        }

        .search-input:focus {
            border-color: var(--primary);
        }

        /* Table Card */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem 0.75rem;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            .header-actions {
                width: 100%;
                justify-content: flex-start;
                overflow-x: auto;
            }
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
            }
            .filter-tabs {
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 0.35rem;
                scrollbar-width: none;
            }
            .filter-tabs::-webkit-scrollbar {
                display: none;
            }
            .search-box {
                width: 100%;
            }
            .search-input {
                min-width: 0;
                flex: 1;
            }
            .card table {
                min-width: 850px;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background-color: rgba(15, 23, 42, 0.8);
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--card-border);
        }

        td {
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid var(--card-border);
            font-size: 0.875rem;
            vertical-align: middle;
        }

        tr:hover td {
            background-color: rgba(255, 255, 255, 0.02);
        }

        /* Status Badges */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.65rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-waiting {
            background-color: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge-ready {
            background-color: rgba(6, 182, 212, 0.15);
            color: #22d3ee;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        .badge-done {
            background-color: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-modality {
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: rgba(139, 92, 246, 0.15);
            color: #c084fc;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        /* Modal Form */
        .modal-backdrop, .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(6px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal-content {
            background-color: #0f172a;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            width: 100%;
            max-width: 520px;
            padding: 1.75rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.8);
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--card-border);
        }

        .btn-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            line-height: 1;
            padding: 0.2rem;
            transition: color 0.15s;
        }

        .btn-close:hover {
            color: white;
        }

        /* MCU Header Action Buttons */
        .mcu-header-actions {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .mcu-header-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.38rem;
            padding: 0.34rem 0.72rem;
            font-size: 0.74rem;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            line-height: 1.1;
            transition: all 0.18s ease;
            border: 1px solid transparent;
            font-family: inherit;
        }

        .mcu-header-btn-upload {
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.22), rgba(14, 165, 233, 0.12));
            border-color: rgba(56, 189, 248, 0.45);
            color: #38bdf8;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.18);
        }

        .mcu-header-btn-upload:hover {
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.4), rgba(14, 165, 233, 0.28));
            border-color: #38bdf8;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.3);
            transform: translateY(-1px);
        }

        .mcu-header-btn-upload:active {
            transform: translateY(0);
        }

        .mcu-header-btn-template {
            background: rgba(30, 41, 59, 0.75);
            border-color: rgba(148, 163, 184, 0.25);
            color: #94a3b8;
        }

        .mcu-header-btn-template:hover {
            background: rgba(51, 65, 85, 0.85);
            border-color: rgba(148, 163, 184, 0.5);
            color: #f1f5f9;
            transform: translateY(-1px);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-control {
            width: 100%;
            background-color: #1e293b;
            border: 1px solid #334155;
            color: var(--text-main);
            padding: 0.55rem 0.85rem;
            border-radius: 6px;
            font-size: 0.85rem;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--card-border);
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .pagination-info {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .pagination-links {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .pagination-links a,
        .pagination-links span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.5rem;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }

        .pagination-links a {
            background-color: rgba(30, 41, 59, 0.8);
            color: var(--text-muted);
            border-color: var(--card-border);
        }

        .pagination-links a:hover {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination-links span.current {
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            color: white;
            border-color: #38bdf8;
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.4);
        }

        .pagination-links span.disabled {
            color: #334155;
            border-color: #1e293b;
            cursor: not-allowed;
            background-color: rgba(15, 23, 42, 0.5);
        }

        /* ==========================================================================
           WORKSTATION PROTOCOL PICKER (FULL SCREEN CLINICAL CONSOLE - 180 PROTOKOL)
           ========================================================================== */
        #modalProtocolPicker {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #040812;
            display: none;
            z-index: 10050;
            overflow: hidden;
            animation: pickerFadeIn 0.15s ease-out;
        }

        @keyframes pickerFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .protocol-modal-dialog {
            background: #060b14;
            width: 100vw;
            height: 100vh;
            max-width: 100vw;
            max-height: 100vh;
            border-radius: 0;
            border: none;
            display: flex;
            flex-direction: column;
            box-shadow: none;
            overflow: hidden;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        /* Top Header */
        .protocol-header {
            background: #0b1324;
            border-bottom: 1px solid #1c2b44;
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .protocol-header-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            flex-wrap: wrap;
        }

        .protocol-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #f8fafc;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .protocol-tool-btn {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background: #111a2e;
            border: 1px solid #1e293b;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: all 0.15s ease;
        }

        .protocol-tool-btn:hover {
            background: #1e293b;
            color: #f8fafc;
            border-color: #38bdf8;
        }

        .protocol-tool-btn.active {
            background: rgba(2, 132, 199, 0.25);
            color: #38bdf8;
            border-color: #38bdf8;
            box-shadow: 0 0 10px rgba(56, 189, 248, 0.4);
        }

        .protocol-search-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 320px;
        }

        .protocol-search-wrap input {
            width: 100%;
            background: #040812;
            border: 1px solid #1e2d44;
            border-radius: 6px;
            padding: 0.52rem 2.2rem 0.52rem 2.3rem;
            font-size: 0.84rem;
            color: #f8fafc;
            outline: none;
            transition: all 0.2s ease;
        }

        .protocol-search-wrap input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.25);
        }

        .protocol-search-wrap .protocol-search-icon {
            position: absolute;
            left: 0.75rem;
            color: #64748b;
            pointer-events: none;
        }

        .protocol-search-wrap #protocolSearchClear {
            position: absolute;
            right: 0.65rem;
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1.25rem;
            cursor: pointer;
            line-height: 1;
            padding: 0;
        }

        /* Tooltip custom - Aligned safely to NEVER clip off screen edges */
        .protocol-tool-btn .custom-tooltip {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            top: calc(100% + 8px);
            bottom: auto;
            left: 50%;
            transform: translateX(-50%);
            background: #020617;
            color: #f8fafc;
            border: 1px solid rgba(56, 189, 248, 0.45);
            padding: 0.4rem 0.7rem;
            border-radius: 5px;
            font-size: 0.74rem;
            font-weight: 600;
            white-space: nowrap;
            pointer-events: none;
            z-index: 99999;
            box-shadow: 0 6px 18px rgba(0,0,0,0.9);
            transition: opacity 0.15s ease, visibility 0.15s ease;
        }

        #btnSortFreq .custom-tooltip {
            left: 0 !important;
            transform: none !important;
        }

        .protocol-tool-btn:hover .custom-tooltip,
        .skel-tab-btn:hover .custom-tooltip {
            visibility: visible;
            opacity: 1;
        }

        /* Body Split Layout */
        .protocol-body {
            display: flex;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        /* Left Column: Skeleton */
        .protocol-anatomy-col {
            width: 320px;
            flex-shrink: 0;
            background: #040812;
            border-right: 1px solid #142033;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0.85rem 1rem;
            gap: 0.65rem;
            height: 100%;
            overflow: hidden;
        }

        .skel-hud {
            width: 100%;
            padding: 0.5rem 0.75rem;
            background: rgba(11, 19, 36, 0.95);
            border: 1px solid rgba(56, 189, 248, 0.35);
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #38bdf8;
            text-align: center;
            letter-spacing: 0.02em;
            min-height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.5);
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .skeleton-container {
            flex: 1;
            width: 100%;
            min-height: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: radial-gradient(circle at center, rgba(14, 165, 233, 0.06) 0%, #040812 75%);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid rgba(30, 41, 59, 0.6);
        }

        .skeleton-svg {
            width: 100%;
            height: 100%;
            max-height: calc(100vh - 210px);
            display: block;
            margin: 0 auto;
            user-select: none;
        }

        .skel-glow-layer {
            opacity: 0;
            transition: opacity 0.15s ease, filter 0.15s ease;
            pointer-events: none;
            mix-blend-mode: screen;
        }

        .skel-glow-layer.hovered {
            opacity: 0.85;
            filter: drop-shadow(0 0 4px rgba(0, 240, 255, 0.85));
        }

        .skel-glow-layer.active {
            opacity: 1 !important;
            filter: drop-shadow(0 0 3px #00f0ff) drop-shadow(0 0 6px rgba(0, 240, 255, 0.7)) !important;
        }

        .skel-region {
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }

        .skel-region path {
            fill: transparent !important;
            stroke: none !important;
            pointer-events: all;
        }

        .skel-tabs-wrap {
            display: flex;
            gap: 0.65rem;
            width: 100%;
            flex-shrink: 0;
            position: relative;
        }

        .skel-tab-btn {
            flex: 1;
            background: #0f1a30;
            border: 1px solid #1c2b44;
            border-radius: 6px;
            padding: 0.65rem 0.5rem;
            font-size: 0.82rem;
            font-weight: 700;
            color: #94a3b8;
            cursor: pointer;
            position: relative;
            transition: all 0.15s ease;
            text-align: center;
        }

        .skel-tab-btn:hover {
            background: #172644;
            color: #f8fafc;
            border-color: #38bdf8;
        }

        .skel-tab-btn.active {
            background: rgba(2, 132, 199, 0.28);
            color: #38bdf8;
            border-color: #00f0ff;
            box-shadow: 0 0 12px rgba(0, 240, 255, 0.4);
        }

        /* Tooltip tabOftenUsed: align to left edge so it NEVER clips! */
        #tabOftenUsed .custom-tooltip {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: calc(100% + 8px);
            left: 0 !important;
            transform: none !important;
            background: #020617;
            color: #f8fafc;
            border: 1px solid rgba(56, 189, 248, 0.45);
            padding: 0.4rem 0.7rem;
            border-radius: 5px;
            font-size: 0.74rem;
            font-weight: 600;
            white-space: nowrap;
            pointer-events: none;
            z-index: 99999;
            box-shadow: 0 4px 16px rgba(0,0,0,0.85);
            transition: opacity 0.15s ease, visibility 0.15s ease;
        }

        /* Tooltip tabAdvanced: align to right edge */
        #tabAdvanced .custom-tooltip {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: calc(100% + 8px);
            right: 0 !important;
            left: auto !important;
            transform: none !important;
            background: #020617;
            color: #f8fafc;
            border: 1px solid rgba(56, 189, 248, 0.45);
            padding: 0.4rem 0.7rem;
            border-radius: 5px;
            font-size: 0.74rem;
            font-weight: 600;
            white-space: nowrap;
            pointer-events: none;
            z-index: 99999;
            box-shadow: 0 4px 16px rgba(0,0,0,0.85);
            transition: opacity 0.15s ease, visibility 0.15s ease;
        }

        #tabOftenUsed:hover .custom-tooltip,
        #tabAdvanced:hover .custom-tooltip {
            visibility: visible;
            opacity: 1;
        }

        /* Right Column: Protocols Grid */
        .protocol-main-col {
            background: #070d18;
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 1.25rem 1.75rem;
            min-width: 0;
            min-height: 0;
            height: 100%;
        }

        .protocol-grid-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
            overflow-y: auto;
            flex: 1;
            padding: 0.25rem 0.5rem 1rem 0;
        }

        .protocol-grid-container::-webkit-scrollbar {
            width: 7px;
        }

        .protocol-grid-container::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
            border-radius: 4px;
        }

        .protocol-grid-container::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }

        .protocol-grid-container::-webkit-scrollbar-thumb:hover {
            background: #38bdf8;
        }

        .protocol-card {
            background: #10192e;
            border: 1px solid #1a283e;
            border-radius: 6px;
            padding: 0.85rem 0.75rem;
            color: #cbd5e1;
            font-size: 0.86rem;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 56px;
            line-height: 1.3;
            user-select: none;
            word-break: break-word;
            position: relative;
        }

        .protocol-card:hover {
            background: #17243c;
            border-color: #38bdf8;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4);
        }

        .protocol-card.selected {
            background: rgba(14, 165, 233, 0.26) !important;
            border-color: #00f0ff !important;
            color: #38bdf8 !important;
            font-weight: 700 !important;
            box-shadow: 0 0 14px rgba(0, 240, 255, 0.45), inset 0 0 8px rgba(0, 240, 255, 0.25) !important;
        }

        .protocol-card-tag {
            font-size: 0.66rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.25rem;
        }

        /* Bottom Selection Tray */
        .protocol-picker-tray {
            padding: 0.75rem 1.75rem;
            background: #040812;
            border-top: 1px solid #142033;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        /* Footer Action Buttons */
        .protocol-picker-footer {
            padding: 0.85rem 1.75rem;
            background: #0b1324;
            border-top: 1px solid #1c2b44;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            flex-shrink: 0;
        }
    </style>

</head>
<body>

<div class="container">

    <!-- Flash Message -->
    @if(session('success'))
        <div class="alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Header Section -->
    <header class="header">
        <div>
            <div class="brand-title">
                <span class="brand-logo">Hyu</span>
                <span>MCU Central & PACS Radiologi</span>
            </div>
            <p class="subtitle">Cahaya Diagnostic Centre (CDC) &bull; Medical Check Up & Sistem Radiologi Terintegrasi</p>
        </div>

        <div class="header-actions">
            <button class="btn btn-fuji" onclick="triggerSimulateFuji()">
                📡 Simulasi Kirim Citra Fujifilm (C-STORE)
            </button>
            <button class="btn btn-primary" onclick="openOrderModal()">
                ➕ Buat Order MCU Baru
            </button>
        </div>
    </header>

    <!-- DICOM Node & Network Parameter Status -->
    <div class="pacs-info-card">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.2rem;">🏥</span>
            <div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #f8fafc;">
                    Hyu DICOM Node Listener (C-STORE / C-ECHO SCP)
                </div>
                <div style="font-size: 0.75rem; color: #94a3b8;">
                    Layanan terpadu penerima transmisi citra radiologi & Modality Worklist (DICOM 3.0)
                </div>
            </div>
        </div>

        <div class="pacs-node-badges">
            <span class="pacs-pill">AE Title: <strong id="lblIdxAeTitle">{{ $pacsConfig['ae_title'] }}</strong></span>
            <span class="pacs-pill">Port: <strong id="lblIdxPort">{{ $pacsConfig['port'] }}</strong></span>
            <span class="pacs-pill">Target: <strong id="lblIdxTarget">{{ $pacsConfig['modality_ae'] }}</strong></span>
            <span style="color: #10b981; font-weight: 700; font-size: 0.75rem;">● Server Standalone Aktif</span>
            <button class="btn btn-secondary" onclick="openDicomModal()" style="padding: 0.25rem 0.65rem; font-size: 0.72rem; border-color: rgba(56, 189, 248, 0.35); color: #38bdf8; margin-left: 0.25rem;">
                ⚙️ Setup Node
            </button>
        </div>
    </div>

    <!-- Pos Pelacakan MCU KPI Stats -->
    <div class="stats-grid">
        <div class="stat-card stat-all">
            <div class="stat-label">Total Pemeriksaan MCU</div>
            <div class="stat-value" style="color: #38bdf8;">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card stat-waiting">
            <div class="stat-label">🟡 Siap di Ruang Rontgen</div>
            <div class="stat-value" style="color: #fbbf24;">{{ $stats['siap_rontgen'] }}</div>
        </div>
        <div class="stat-card stat-ready">
            <div class="stat-label">🟢 Citra Masuk (Siap Baca)</div>
            <div class="stat-value" style="color: #22d3ee;">{{ $stats['rontgen_selesai'] }}</div>
        </div>
        <div class="stat-card stat-done">
            <div class="stat-label">🔵 Selesai Ekspertise Dokter</div>
            <div class="stat-value" style="color: #34d399;">{{ $stats['selesai_diagnosa'] }}</div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="filter-bar">
        <div class="filter-tabs">
            <a href="{{ route('scans.index') }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">Semua Pos ({{ $stats['total'] }})</a>
            <a href="{{ route('scans.index', ['status' => 'siap_rontgen']) }}" class="filter-tab {{ request('status') == 'siap_rontgen' ? 'active' : '' }}">Antrian Rontgen ({{ $stats['siap_rontgen'] }})</a>
            <a href="{{ route('scans.index', ['status' => 'rontgen_selesai']) }}" class="filter-tab {{ request('status') == 'rontgen_selesai' ? 'active' : '' }}">Siap Baca ({{ $stats['rontgen_selesai'] }})</a>
            <a href="{{ route('scans.index', ['status' => 'selesai_diagnosa']) }}" class="filter-tab {{ request('status') == 'selesai_diagnosa' ? 'active' : '' }}">Selesai ({{ $stats['selesai_diagnosa'] }})</a>
        </div>

        <form action="{{ route('scans.index') }}" method="GET" class="search-box">
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari nama, No. RM, Accession No...">
            <button type="submit" class="btn btn-secondary" style="padding: 0.45rem 0.85rem;">🔍 Cari</button>
        </form>
    </div>

    <!-- Table Card -->
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>No. RM & Accession</th>
                    <th>Nama Pasien</th>
                    <th>Modalitas & Studi</th>
                    <th>Status Pos MCU</th>
                    <th>Stasiun / Alat</th>
                    <th>Catatan Klinis / Diagnosa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($scans as $scan)
                    <tr>
                        <td>
                            <a href="{{ route('scans.show', $scan->id) }}" style="text-decoration: none; color: inherit; display: block;">
                                <div style="font-weight: 700; color: #38bdf8; font-family: 'JetBrains Mono', monospace;">
                                    {{ $scan->patient_id ?: '#CDC-' . str_pad($scan->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                                <div style="font-size: 0.72rem; color: var(--text-muted); font-family: monospace;">
                                    {{ $scan->accession_number ?: 'ACC-' . str_pad($scan->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('scans.show', $scan->id) }}" style="text-decoration: none; color: inherit; display: block;">
                                <div style="font-weight: 700; color: #f8fafc;">{{ $scan->patient_name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                    {{ $scan->gender == 'P' ? 'Perempuan' : 'Laki-laki' }}, {{ $scan->age ? $scan->age . ' th' : '-' }}
                                </div>
                            </a>
                        </td>
                        <td>
                            <span class="badge-modality">{{ $scan->modality }}</span>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem;">
                                {{ $scan->study_description ?: 'Thorax View' }}
                            </div>
                        </td>
                        <td>
                            <span class="badge-status {{ $scan->status_badge_class }}">
                                {{ $scan->status_label }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size: 0.75rem; font-family: monospace; color: #cbd5e1;">
                                {{ $scan->station_name ?: 'FUJIFILM_FDR' }}
                            </span>
                        </td>
                        <td style="color: var(--text-muted); max-width: 280px; font-size: 0.825rem; line-height: 1.4;">
                            {{ $scan->diagnosis_notes ?: ($scan->order_notes ?: 'Menunggu pemeriksaan rontgen...') }}
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.4rem; align-items: center; flex-wrap: wrap;">
                                @if($scan->mcu_status === 'siap_rontgen')
                                    {{-- Pasien belum diperiksa: citra belum tersedia, tidak ada aksi ambil citra --}}
                                    <span class="badge-status badge-waiting" style="padding: 0.35rem 0.65rem; font-size: 0.76rem; border: 1px dashed rgba(245,158,11,0.5); cursor: default;" title="Pemeriksaan rontgen belum dilakukan. Citra medis belum tersedia.">
                                        ⏳ Belum Diperiksa
                                    </span>
                                    <a href="{{ route('scans.show', $scan->id) }}" class="btn btn-secondary" style="padding: 0.35rem 0.6rem; font-size: 0.76rem; color: #94a3b8; border-color: #334155;" title="Lihat Informasi Order MCU">
                                        📋 Detail Order
                                    </a>
                                @else
                                    {{-- Pasien sudah diperiksa & citra tersedia: buka PACS Viewer --}}
                                    <a href="{{ route('scans.show', $scan->id) }}" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.78rem;">
                                        🔍 Viewer
                                    </a>
                                @endif
                                <button type="button" class="btn btn-secondary" style="padding: 0.35rem 0.55rem; font-size: 0.78rem; color: #ef4444;" title="Hapus Order" onclick="openDeleteModal('{{ route('scans.destroy', $scan->id) }}', '{{ addslashes($scan->patient_name) }}', '{{ addslashes($scan->patient_id ?: 'CDC-' . str_pad($scan->id, 5, '0', STR_PAD_LEFT)) }}', '{{ addslashes($scan->accession_number ?: '-') }}')">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                            Belum ada antrian pemeriksaan pasien. Klik <strong>+ Buat Order MCU Baru</strong> untuk memulai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">
            <div class="pagination-info">
                @if ($scans->total() > 0)
                    Menampilkan <strong>{{ $scans->firstItem() }}–{{ $scans->lastItem() }}</strong>
                    dari <strong>{{ $scans->total() }}</strong> data pemeriksaan
                @else
                    Tidak ada data pemeriksaan
                @endif
            </div>
            <div class="pagination-links">
                {{-- Previous --}}
                <span class="disabled">&#8249;</span>

                {{-- Page Numbers: only show when more than 1 page --}}
                @if ($scans->hasPages())
                    @foreach ($scans->getUrlRange(1, $scans->lastPage()) as $page => $url)
                        @if ($page == $scans->currentPage())
                            <span class="current">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @else
                    <span class="current">1</span>
                @endif

                {{-- Next --}}
                @if ($scans->hasMorePages())
                    <a href="{{ $scans->nextPageUrl() }}">&#8250;</a>
                @else
                    <span class="disabled">&#8250;</span>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- Modal Dialog: Buat Order MCU Baru -->
<div class="modal-backdrop" id="modalOrder">
    <div class="modal-content" style="max-width: 660px;">
        <div class="modal-header" style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.15rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--card-border);">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <div style="font-weight: 700; font-size: 1rem; color: #f8fafc; letter-spacing: -0.01em;">➕ Buat Sesi / Order MCU Baru (Hyu)</div>
                
                <!-- Action Buttons: Upload Excel & Unduh Template -->
                <div class="mcu-header-actions">
                    <input type="file" id="mcuExcelFile" accept=".xlsx,.xls,.csv" style="display: none;" onchange="handleMcuExcelUpload(this)">
                    
                    <button type="button" id="mcuUploadBtn" class="mcu-header-btn mcu-header-btn-upload" onclick="document.getElementById('mcuExcelFile').click()" title="Upload file Excel untuk mengisi form otomatis">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span>Upload Excel</span>
                    </button>

                    <a href="{{ route('mcu.import.template') }}" class="mcu-header-btn mcu-header-btn-template" title="Unduh format template Excel MCU (.xlsx)">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Unduh Template</span>
                    </a>
                </div>
            </div>

            <button type="button" class="btn-close" onclick="closeOrderModal()" title="Tutup">&times;</button>
        </div>

        <!-- Banner Notifikasi & Preview Table MCU (Hanya muncul saat file diupload/diproses) -->
        <div id="mcuUploadNotification" style="display: none; margin-bottom: 1.15rem;">
            <div id="mcuUploadStatus" style="padding: 0.6rem 0.85rem; border-radius: 8px; font-size: 0.78rem; display: flex; align-items: center; justify-content: space-between; gap: 0.6rem; border: 1px solid transparent;">
                <div id="mcuUploadStatusContent" style="display: flex; align-items: center; gap: 0.5rem; flex: 1; flex-wrap: wrap;"></div>
                <button type="button" onclick="dismissMcuStatus()" style="background: none; border: none; color: inherit; opacity: 0.6; cursor: pointer; font-size: 1.15rem; line-height: 1; padding: 0 0.2rem;" title="Tutup pemberitahuan">&times;</button>
            </div>
            
            <!-- Tabel Preview Multi-Baris -->
            <div id="mcuPreviewTableWrap" style="margin-top: 0.65rem; display: none; max-height: 250px; overflow-y: auto; border-radius: 8px; border: 1px solid rgba(56, 189, 248, 0.2); background: rgba(15, 23, 42, 0.95); box-shadow: inset 0 2px 4px rgba(0,0,0,0.4);">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.74rem;">
                    <thead>
                        <tr style="background: rgba(30, 41, 59, 0.9); position: sticky; top: 0; z-index: 2;">
                            <th style="padding: 0.45rem 0.6rem; text-align: left; color: #94a3b8; font-weight: 600; border-bottom: 1px solid #334155;">#</th>
                            <th style="padding: 0.45rem 0.6rem; text-align: left; color: #94a3b8; font-weight: 600; border-bottom: 1px solid #334155;">Nama</th>
                            <th style="padding: 0.45rem 0.6rem; text-align: left; color: #94a3b8; font-weight: 600; border-bottom: 1px solid #334155;">MRN/NIK</th>
                            <th style="padding: 0.45rem 0.6rem; text-align: left; color: #94a3b8; font-weight: 600; border-bottom: 1px solid #334155;">Modalitas</th>
                            <th style="padding: 0.45rem 0.6rem; text-align: left; color: #94a3b8; font-weight: 600; border-bottom: 1px solid #334155;">Status</th>
                            <th style="padding: 0.45rem 0.6rem; text-align: center; color: #94a3b8; font-weight: 600; border-bottom: 1px solid #334155;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mcuPreviewTableBody"></tbody>
                </table>
            </div>
        </div>

        <form action="{{ route('scans.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap Pasien *</label>
                <input type="text" name="patient_name" class="form-control" placeholder="Contoh: Budi Santoso" required>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">No. Rekam Medis (MRN / NIK)</label>
                    <input type="text" name="patient_id" class="form-control" placeholder="Otomatis jika kosong">
                </div>
                <div class="form-group">
                    <label class="form-label">Usia (Tahun)</label>
                    <input type="number" name="age" class="form-control" value="30">
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="gender" class="form-control">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                        <span>Pemeriksaan Terpilih *</span>
                        <span id="selectedModalityCategoryBadge" style="font-size: 0.7rem; padding: 0.15rem 0.5rem; border-radius: 4px; background: rgba(56, 189, 248, 0.15); color: #38bdf8; font-weight: 700; border: 1px solid rgba(56, 189, 248, 0.35); text-transform: uppercase; letter-spacing: 0.03em;">Thorax</span>
                    </label>
                    <input type="hidden" name="modality" id="selectedModalityInput" value="Chest PA" required>
                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                        <div id="selectedModalityDisplayBox" style="flex: 1; padding: 0.55rem 0.85rem; background: #070d18; border: 1px solid #1e293b; border-radius: 6px; color: #f8fafc; font-weight: 700; font-size: 0.88rem; display: flex; align-items: center; gap: 0.6rem; min-height: 42px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.5);">
                            <span id="selectedModalityIcon" style="font-size: 1.1rem; filter: drop-shadow(0 0 4px rgba(56, 189, 248, 0.5));">🫁</span>
                            <span id="selectedModalityText" style="color: #38bdf8; letter-spacing: 0.01em;">Chest PA</span>
                        </div>
                        <button type="button" class="btn" onclick="openProtocolPickerModal()" style="white-space: nowrap; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; border: 1px solid #38bdf8; padding: 0.55rem 0.95rem; border-radius: 6px; font-weight: 700; font-size: 0.84rem; display: inline-flex; align-items: center; gap: 0.45rem; cursor: pointer; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35); transition: all 0.2s ease;" title="Buka katalog workstation 180 pemeriksaan radiologi">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Tambah Pemeriksaan</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Indikasi Klinis / Deskripsi Sesi</label>
                <input type="text" name="study_description" class="form-control" placeholder="Contoh: MCU Karyawan PT Semen Gresik">
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Tambahan untuk Petugas Rontgen</label>
                <textarea name="order_notes" rows="2" class="form-control" placeholder="Catatan pengantar khusus..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeOrderModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan & Kirim ke Worklist</button>
            </div>
        </form>
    </div>
</div>


<!-- MODAL: WORKSTATION PROTOCOL PICKER (FULL SCREEN CONSOLE - 180 PROTOKOL LENGKAP) -->
<div id="modalProtocolPicker" class="modal-backdrop">
    <div class="protocol-modal-dialog">
        <!-- TOP HEADER -->
        <div class="protocol-header">
            <div class="protocol-header-left">
                <div class="protocol-title">
                    <span>Tambah Pemeriksaan</span>
                </div>

                <!-- Sort Buttons -->
                <div style="display: flex; align-items: center; gap: 0.45rem;">
                    <button type="button" id="btnSortFreq" class="protocol-tool-btn active" onclick="setProtocolSort('default')">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20V10"></path>
                            <path d="M18 20V4"></path>
                            <path d="M6 20v-4"></path>
                        </svg>
                        <span class="custom-tooltip">Urutkan Berdasarkan Frekuensi</span>
                    </button>
                    <button type="button" id="btnSortAz" class="protocol-tool-btn" onclick="setProtocolSort('az')">
                        <span style="font-size: 0.78rem; font-weight: 800; font-family: monospace;">A-Z</span>
                        <span class="custom-tooltip">Urutkan Berdasarkan Abjad (A–Z)</span>
                    </button>
                </div>

                <!-- Real-time Search Box -->
                <div class="protocol-search-wrap">
                    <svg class="protocol-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="protocolSearchInput" placeholder="Cari pemeriksaan, proyeksi, atau organ..." autocomplete="off" oninput="handleProtocolSearch(this.value)">
                    <button type="button" id="protocolSearchClear" onclick="clearProtocolSearch()" style="display: none;">&times;</button>
                </div>
            </div>

            <!-- Close Modal -->
            <button type="button" class="btn-close" onclick="closeProtocolPickerModal()" title="Tutup" style="font-size: 1.8rem;">&times;</button>
        </div>

        <!-- MAIN SPLIT BODY -->
        <div class="protocol-body">
            <!-- LEFT COLUMN: SKELETON & TABS -->
            <div class="protocol-anatomy-col">
                <!-- Floating Medical English Anatomy HUD -->
                <div id="skelRegionHud" class="skel-hud">Select Anatomical Region</div>

                <!-- Real Anatomical Radiographic Human Skeleton with Interactive Glowing Regions -->
                <div class="skeleton-container">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 768 1376" id="skeletonSvg" class="skeleton-svg">
                        <!-- True 3D Medical Radiographic Human Skeleton Base Image -->
                        <image href="/images/workstation_skeleton.jpg" xlink:href="/images/workstation_skeleton.jpg" x="0" y="0" width="768" height="1376" preserveAspectRatio="xMidYMid meet" />

                        <!-- Organic Glowing Organ Overlays (Pixel-perfect on actual bones) -->
                        <image id="glow-head" class="skel-glow-layer" href="/images/parts/skeleton_part_head.png?v=3" xlink:href="/images/parts/skeleton_part_head.png?v=3" x="0" y="0" width="768" height="1376" />
                        <image id="glow-spine" class="skel-glow-layer" href="/images/parts/skeleton_part_spine.png?v=3" xlink:href="/images/parts/skeleton_part_spine.png?v=3" x="0" y="0" width="768" height="1376" />
                        <image id="glow-thorax" class="skel-glow-layer" href="/images/parts/skeleton_part_thorax.png?v=3" xlink:href="/images/parts/skeleton_part_thorax.png?v=3" x="0" y="0" width="768" height="1376" />
                        <image id="glow-pelvis" class="skel-glow-layer" href="/images/parts/skeleton_part_pelvis.png?v=3" xlink:href="/images/parts/skeleton_part_pelvis.png?v=3" x="0" y="0" width="768" height="1376" />
                        <image id="glow-right_arm" class="skel-glow-layer" href="/images/parts/skeleton_part_right_arm.png?v=3" xlink:href="/images/parts/skeleton_part_right_arm.png?v=3" x="0" y="0" width="768" height="1376" />
                        <image id="glow-left_arm" class="skel-glow-layer" href="/images/parts/skeleton_part_left_arm.png?v=3" xlink:href="/images/parts/skeleton_part_left_arm.png?v=3" x="0" y="0" width="768" height="1376" />
                        <image id="glow-right_leg" class="skel-glow-layer" href="/images/parts/skeleton_part_right_leg.png?v=3" xlink:href="/images/parts/skeleton_part_right_leg.png?v=3" x="0" y="0" width="768" height="1376" />
                        <image id="glow-left_leg" class="skel-glow-layer" href="/images/parts/skeleton_part_left_leg.png?v=3" xlink:href="/images/parts/skeleton_part_left_leg.png?v=3" x="0" y="0" width="768" height="1376" />

                        <!-- Interactive Clickable Hitbox Zones (Transparent, Zero Rigid Boxes) -->
                        <g id="skel-head" class="skel-region" data-category="head" data-label="Head / Skull" tabindex="0" role="button">
                            <path d="M 295 50 L 472 50 L 472 214 L 295 214 Z" />
                        </g>

                        <g id="skel-right-arm" class="skel-region" data-category="right_arm" data-label="Right Upper Limb" tabindex="0" role="button">
                            <path d="M 75 260 L 250 260 L 250 535 L 246 830 L 75 830 Z" />
                        </g>

                        <g id="skel-left-arm" class="skel-region" data-category="left_arm" data-label="Left Upper Limb" tabindex="0" role="button">
                            <path d="M 525 260 L 700 260 L 700 830 L 532 830 L 525 535 Z" />
                        </g>

                        <g id="skel-thorax" class="skel-region" data-category="thorax" data-label="Thorax" tabindex="0" role="button">
                            <path d="M 250 265 L 525 265 L 525 535 L 250 535 Z" />
                        </g>

                        <g id="skel-pelvis" class="skel-region" data-category="pelvis" data-label="Pelvis &amp; Abdomen" tabindex="0" role="button">
                            <path d="M 245 535 L 532 535 L 532 720 L 245 720 Z" />
                        </g>

                        <g id="skel-right-leg" class="skel-region" data-category="right_leg" data-label="Right Lower Limb" tabindex="0" role="button">
                            <path d="M 240 720 L 388 720 L 388 1370 L 240 1370 Z" />
                        </g>

                        <g id="skel-left-leg" class="skel-region" data-category="left_leg" data-label="Left Lower Limb" tabindex="0" role="button">
                            <path d="M 388 720 L 540 720 L 540 1370 L 388 1370 Z" />
                        </g>

                        <!-- Spine sits on the TOP layer of the SVG DOM so midline hover & clicks are 100% responsive -->
                        <g id="skel-spine" class="skel-region" data-category="spine" data-label="Spine" tabindex="0" role="button">
                            <path d="M 352 210 L 424 210 L 424 690 L 352 690 Z" />
                        </g>
                    </svg>
                </div>

                <!-- Bottom Tabs below Skeleton (Aligned so tooltips NEVER get cut off) -->
                <div class="skel-tabs-wrap">
                    <button type="button" id="tabOftenUsed" class="skel-tab-btn active" onclick="selectProtocolCategory('often_used')">
                        <span>Often Used</span>
                        <span class="custom-tooltip">Pemeriksaan yang Sering Digunakan</span>
                    </button>
                    <button type="button" id="tabAdvanced" class="skel-tab-btn" onclick="selectProtocolCategory('advanced')">
                        <span>Advanced</span>
                        <span class="custom-tooltip">Pemeriksaan Lanjutan</span>
                    </button>
                </div>
            </div>

            <!-- RIGHT COLUMN: PROTOCOLS 4-COLUMN GRID -->
            <div class="protocol-main-col">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; padding-bottom: 0.6rem; border-bottom: 1px solid #1c2b44;">
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                        <span id="protocolGridTitle" style="font-weight: 700; font-size: 1.05rem; color: #f1f5f9;">Often Used</span>
                        <span id="protocolGridCount" style="font-size: 0.74rem; background: rgba(56, 189, 248, 0.15); color: #38bdf8; padding: 0.18rem 0.65rem; border-radius: 9999px; border: 1px solid rgba(56, 189, 248, 0.35); font-weight: 700;">30 Pemeriksaan</span>
                    </div>
                    <span style="font-size: 0.76rem; color: #64748b; font-style: italic;">Klik 1x untuk memilih • Klik 2x untuk konfirmasi cepat</span>
                </div>

                <div id="protocolGridContainer" class="protocol-grid-container">
                    <!-- Populated dynamically via JavaScript -->
                </div>
            </div>
        </div>

        <!-- BOTTOM TRAY (SELECTION SUMMARY) -->
        <div class="protocol-picker-tray">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; font-weight: 700;">Pemeriksaan Terpilih:</span>
                <span id="pickerSelectedName" style="color: #00f0ff; font-weight: 800; font-size: 1.02rem; font-family: 'JetBrains Mono', monospace; text-shadow: 0 0 10px rgba(0, 240, 255, 0.45);">Chest PA</span>
                <span id="pickerSelectedCategoryBadge" style="font-size: 0.74rem; background: #111a2e; color: #38bdf8; padding: 0.2rem 0.6rem; border-radius: 4px; border: 1px solid rgba(56, 189, 248, 0.35); font-weight: 600;">Thorax</span>
            </div>
            <div style="font-size: 0.74rem; color: #64748b;">Standar Protokol DICOM CR / DX Konsol Digital Radiologi</div>
        </div>

        <!-- FOOTER BAR (ACTIONS IN BAHASA INDONESIA) -->
        <div class="protocol-picker-footer">
            <button type="button" class="btn btn-secondary" onclick="closeProtocolPickerModal()" style="min-width: 120px; font-weight: 600;">
                Kembali
            </button>
            <button type="button" class="btn btn-primary" onclick="confirmProtocolSelection()" style="min-width: 190px; font-weight: 700; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border-color: #00f0ff; box-shadow: 0 0 15px rgba(0, 240, 255, 0.35);">
                Pilih Pemeriksaan
            </button>
            <button type="button" class="btn btn-secondary" onclick="closeProtocolPickerModal()" style="min-width: 110px; border-color: #334155; color: #94a3b8;">
                Batal
            </button>
        </div>
    </div>
</div>
<!-- MODAL: SETUP KONFIGURASI DICOM NODE -->
<div id="modalDicomConfig" class="modal-backdrop">
    <div class="modal-content" style="max-width: 480px;">
        <div class="modal-header">
            <h3 class="modal-title" style="display: flex; align-items: center; gap: 0.5rem;">
                <span>⚙️</span> Konfigurasi DICOM Node (NEMA PS 3.4 SCP)
            </h3>
            <button class="btn-close" onclick="closeDicomModal()">&times;</button>
        </div>

        <form id="formDicomConfig" onsubmit="saveDicomConfig(event)">
            @csrf
            <div style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 1.25rem; line-height: 1.4; background: rgba(2, 132, 199, 0.1); padding: 0.6rem 0.85rem; border-radius: 6px; border: 1px solid rgba(56, 189, 248, 0.25);">
                Parameter jaringan ini disesuaikan dengan konfigurasi <em>Destination PACS Server</em> pada mesin Rontgen Fujifilm / USG Mindray klinik.
            </div>

            <div class="form-group">
                <label class="form-label">Application Entity Title (AE Title)</label>
                <input type="text" id="cfgAeTitle" name="ae_title" class="form-control" value="{{ $pacsConfig['ae_title'] }}" required maxlength="16" style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #38bdf8;">
                <span style="font-size: 0.70rem; color: var(--text-muted);">Maks. 16 Karakter Alfanumerik (Standar NEMA). Default: HYU_PACS</span>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Nomor Port Jaringan DICOM *</label>
                    <input type="number" id="cfgPort" name="port" class="form-control" value="{{ $pacsConfig['port'] }}" required min="1" max="65535" style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                    <span style="font-size: 0.70rem; color: var(--text-muted);">Port Listener: 4242 / 104 / 11112</span>
                </div>
                <div class="form-group">
                    <label class="form-label">IP Binding / Host</label>
                    <input type="text" class="form-control" value="0.0.0.0 (All LAN)" disabled style="opacity: 0.7; font-family: 'JetBrains Mono', monospace; font-size: 0.8rem;">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Target Sumber Modalitas (Hardware Klinik)</label>
                <input type="text" id="cfgModality" name="modality_source" class="form-control" value="{{ $pacsConfig['modality_ae'] }}" placeholder="Contoh: FUJIFILM (CR) & MINDRAY (US)">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="closeDicomModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSaveDicomCfg">
                    💾 Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: HASIL TRANSMISI DICOM C-STORE (SCP) -->
<div id="modalCstoreResult" class="modal-backdrop">
    <div class="modal-content" style="max-width: 520px; border: 1px solid rgba(56, 189, 248, 0.35); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.85), 0 0 35px rgba(2, 132, 199, 0.15);">
        <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 0.85rem;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <span id="cstoreModalHeaderIcon" style="font-size: 1.25rem;">📡</span>
                <span id="cstoreModalHeaderTitle" style="font-weight: 800; font-size: 0.95rem; letter-spacing: 0.04em; text-transform: uppercase; color: #f1f5f9;">
                    TRANSMISI DICOM C-STORE
                </span>
            </div>
            <button class="btn-close" onclick="closeCstoreModal()">&times;</button>
        </div>

        <div style="padding-top: 0.5rem;">
            <!-- Status Badge -->
            <div id="cstoreStatusBadge" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 0.9rem;">
                <span id="cstoreStatusDot">●</span>
                <span id="cstoreStatusText">STATUS 0x0000 (SUCCESS)</span>
            </div>

            <!-- Message Text -->
            <p id="cstoreMessageText" style="font-size: 0.85rem; color: #cbd5e1; line-height: 1.5; margin-bottom: 1rem;">
                Citra rontgen dari mesin Fujifilm FDR D-EVO berhasil diterima dan diarsipkan oleh Hyu PACS SCP.
            </p>

            <!-- Telemetry Details Box -->
            <div id="cstoreDetailsBox" style="background: rgba(15, 23, 42, 0.75); border: 1px solid rgba(51, 65, 85, 0.6); border-radius: 8px; padding: 0.85rem; font-size: 0.75rem; color: #94a3b8; font-family: 'JetBrains Mono', monospace; line-height: 1.6; margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(51, 65, 85, 0.6); padding-bottom: 0.3rem; margin-bottom: 0.4rem;">
                    <span style="color: #64748b;">PASIEN / MRN</span>
                    <strong id="cstoreDetailPatient" style="color: #f1f5f9;">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(51, 65, 85, 0.6); padding-bottom: 0.3rem; margin-bottom: 0.4rem;">
                    <span style="color: #64748b;">ACCESSION NO.</span>
                    <strong id="cstoreDetailAccession" style="color: #38bdf8;">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(51, 65, 85, 0.6); padding-bottom: 0.3rem; margin-bottom: 0.4rem;">
                    <span style="color: #64748b;">STASIUN AKUISISI</span>
                    <span id="cstoreDetailStation" style="color: #cbd5e1;">FUJIFILM_FDR_D-EVO</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">SOP CLASS UID</span>
                    <span id="cstoreDetailSop" style="color: #cbd5e1; font-size: 0.7rem;">Digital X-Ray (PS 3.4)</span>
                </div>
            </div>

            <!-- Footer Actions -->
            <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid rgba(255, 255, 255, 0.08); padding-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="closeCstoreModal()">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnCstoreAction" onclick="refreshAfterCstore()" style="background: linear-gradient(135deg, #0284c7, #0369a1); border: 1px solid #38bdf8; font-weight: 700; letter-spacing: 0.03em;">
                    🔄 Buka Daftar & Sesi Pasien
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: KONFIRMASI HAPUS PEMERIKSAAN -->
<div id="modalConfirmDelete" class="modal-backdrop">
    <div class="modal-content" style="max-width: 480px; border: 1px solid rgba(239, 68, 68, 0.45); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.9), 0 0 35px rgba(239, 68, 68, 0.2);">
        <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 0.85rem;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <span style="font-size: 1.25rem;">⚠️</span>
                <span style="font-weight: 800; font-size: 0.95rem; letter-spacing: 0.04em; text-transform: uppercase; color: #f87171;">
                    KONFIRMASI HAPUS DATA PEMERIKSAAN
                </span>
            </div>
            <button class="btn-close" onclick="closeDeleteModal()">&times;</button>
        </div>

        <div style="padding-top: 0.5rem;">
            <!-- Warning Badge -->
            <div style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 0.9rem; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.45); color: #f87171;">
                <span>●</span>
                <span>TINDAKAN PERMANEN</span>
            </div>

            <p style="font-size: 0.85rem; color: #cbd5e1; line-height: 1.5; margin-bottom: 1rem;">
                Apakah Anda yakin ingin menghapus data pemeriksaan pasien ini? Rekam sesi, arsip citra DICOM, dan hasil ekspertise terkait akan dihapus secara permanen dari server Hyu PACS.
            </p>

            <!-- Details Telemetry Box -->
            <div style="background: rgba(15, 23, 42, 0.75); border: 1px solid rgba(51, 65, 85, 0.6); border-radius: 8px; padding: 0.85rem; font-size: 0.75rem; color: #94a3b8; font-family: 'JetBrains Mono', monospace; line-height: 1.6; margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(51, 65, 85, 0.6); padding-bottom: 0.35rem; margin-bottom: 0.4rem;">
                    <span style="color: #64748b;">NAMA PASIEN</span>
                    <strong id="delModalPatientName" style="color: #f1f5f9;">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(51, 65, 85, 0.6); padding-bottom: 0.35rem; margin-bottom: 0.4rem;">
                    <span style="color: #64748b;">NO. REKAM MEDIS (MRN)</span>
                    <strong id="delModalPatientId" style="color: #38bdf8;">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">ACCESSION NO.</span>
                    <span id="delModalAccession" style="color: #cbd5e1;">-</span>
                </div>
            </div>

            <!-- Form submission -->
            <form id="formConfirmDelete" method="POST" action="">
                @csrf
                @method('DELETE')
                <div style="display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid rgba(255, 255, 255, 0.08); padding-top: 1rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                        Batal
                    </button>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #dc2626, #b91c1c); border: 1px solid #ef4444; color: #ffffff; font-weight: 700; letter-spacing: 0.03em; padding: 0.5rem 1.15rem; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35); cursor: pointer;">
                        🗑️ Ya, Hapus Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openOrderModal() {
        document.getElementById('modalOrder').style.display = 'flex';
    }

    function closeOrderModal() {
        document.getElementById('modalOrder').style.display = 'none';
        // Reset upload state saat modal ditutup
        resetMcuUploadState();
    }

    function resetMcuUploadState() {
        var fileInput = document.getElementById('mcuExcelFile');
        if (fileInput) fileInput.value = '';
        var notif = document.getElementById('mcuUploadNotification');
        if (notif) notif.style.display = 'none';
        var contentEl = document.getElementById('mcuUploadStatusContent');
        if (contentEl) contentEl.innerHTML = '';
        var tableWrap = document.getElementById('mcuPreviewTableWrap');
        if (tableWrap) tableWrap.style.display = 'none';
        var tableBody = document.getElementById('mcuPreviewTableBody');
        if (tableBody) tableBody.innerHTML = '';
    }

    function dismissMcuStatus() {
        var notif = document.getElementById('mcuUploadNotification');
        if (notif) notif.style.display = 'none';
        var tableWrap = document.getElementById('mcuPreviewTableWrap');
        if (tableWrap) tableWrap.style.display = 'none';
    }

    function showMcuStatus(type, html) {
        var notif = document.getElementById('mcuUploadNotification');
        var statusEl = document.getElementById('mcuUploadStatus');
        var contentEl = document.getElementById('mcuUploadStatusContent');
        if (!notif || !statusEl || !contentEl) return;

        contentEl.innerHTML = html;

        if (type === 'loading') {
            statusEl.style.background = 'rgba(2, 132, 199, 0.12)';
            statusEl.style.borderColor = 'rgba(56, 189, 248, 0.35)';
            statusEl.style.color = '#38bdf8';
        } else if (type === 'success') {
            statusEl.style.background = 'rgba(16, 185, 129, 0.12)';
            statusEl.style.borderColor = 'rgba(16, 185, 129, 0.35)';
            statusEl.style.color = '#34d399';
        } else if (type === 'error') {
            statusEl.style.background = 'rgba(239, 68, 68, 0.12)';
            statusEl.style.borderColor = 'rgba(239, 68, 68, 0.35)';
            statusEl.style.color = '#f87171';
        } else {
            statusEl.style.background = 'rgba(30, 41, 59, 0.85)';
            statusEl.style.borderColor = 'rgba(56, 189, 248, 0.35)';
            statusEl.style.color = '#e2e8f0';
        }

        notif.style.display = 'block';
    }

    function handleMcuExcelUpload(input) {
        if (!input.files || !input.files[0]) return;

        var file = input.files[0];
        var tableWrap = document.getElementById('mcuPreviewTableWrap');
        var tableBody = document.getElementById('mcuPreviewTableBody');
        var uploadBtn = document.getElementById('mcuUploadBtn') || input.previousElementSibling;

        // Validasi ekstensi di frontend
        var allowedExt = ['xlsx', 'xls', 'csv'];
        var ext = file.name.split('.').pop().toLowerCase();
        if (allowedExt.indexOf(ext) === -1) {
            showMcuStatus('error', '<span>❌ Format file tidak didukung. Gunakan file <strong>.xlsx</strong>, <strong>.xls</strong>, atau <strong>.csv</strong>.</span>');
            input.value = '';
            return;
        }

        // Validasi ukuran (2MB)
        if (file.size > 2 * 1024 * 1024) {
            showMcuStatus('error', '<span>❌ Ukuran file terlalu besar (maksimal 2 MB).</span>');
            input.value = '';
            return;
        }

        showMcuStatus('loading', '<span>⏳ Memproses file <strong>' + escapeHtml(file.name) + '</strong>...</span>');
        tableWrap.style.display = 'none';
        tableBody.innerHTML = '';

        // Nonaktifkan tombol upload
        if (uploadBtn) { uploadBtn.disabled = true; uploadBtn.style.opacity = '0.6'; }

        var formData = new FormData();
        formData.append('file', file);

        fetch("{{ route('mcu.import.preview') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(function(res) {
            return res.json().then(function(data) {
                return { ok: res.ok, data: data };
            });
        })
        .then(function(result) {
            if (!result.ok) {
                var errMsg = result.data.message || 'Terjadi kesalahan saat memproses file.';
                showMcuStatus('error', '<span>❌ ' + escapeHtml(errMsg) + '</span>');
                return;
            }

            var resp = result.data;
            var rows = resp.rows || [];
            var summary = resp.summary || {};

            if (rows.length === 0) {
                showMcuStatus('error', '<span>❌ Tidak ada data yang ditemukan di dalam file.</span>');
                return;
            }

            // Jika 1 baris valid → langsung isi form
            if (rows.length === 1 && rows[0].errors.length === 0) {
                fillFormFromRow(rows[0].data);
                showMcuStatus('success', '<span>✅ Data pasien <strong>' + escapeHtml(rows[0].data.nama_lengkap || file.name) + '</strong> berhasil mengisi form. Silakan cek & simpan.</span>');
                return;
            }

            // Multi-baris → tampilkan tabel preview
            showMcuStatus('info', '<span>📋 <strong>' + summary.total + ' baris</strong> terbaca dari <em>' + escapeHtml(file.name) + '</em> (<span style="color: #34d399; font-weight:600;">' + summary.valid + ' valid</span>' +
                (summary.invalid > 0 ? ', <span style="color: #f87171; font-weight:600;">' + summary.invalid + ' error</span>' : '') +
                '). Pilih baris pada tabel untuk mengisi form:</span>');

            tableBody.innerHTML = '';
            rows.forEach(function(row, idx) {
                var tr = document.createElement('tr');
                var hasError = row.errors.length > 0;
                tr.style.borderBottom = '1px solid rgba(51, 65, 85, 0.4)';
                if (hasError) tr.style.background = 'rgba(239, 68, 68, 0.08)';

                tr.innerHTML =
                    '<td style="padding: 0.4rem 0.6rem; color: #94a3b8;">' + row.index + '</td>' +
                    '<td style="padding: 0.4rem 0.6rem; color: #f1f5f9; font-weight: 500;">' + escapeHtml(row.data.nama_lengkap || '-') + '</td>' +
                    '<td style="padding: 0.4rem 0.6rem; color: #cbd5e1; font-family: monospace; font-size: 0.72rem;">' + escapeHtml(row.data.mrn_nik || '-') + '</td>' +
                    '<td style="padding: 0.4rem 0.6rem; color: #cbd5e1;">' + escapeHtml(row.data.modalitas || '-') + '</td>' +
                    '<td style="padding: 0.4rem 0.6rem;">' +
                        (hasError
                            ? '<span style="color: #f87171; font-size: 0.7rem; font-weight: 600;" title="' + escapeHtml(row.errors.join('; ')) + '">❌ ' + escapeHtml(row.errors[0]) + '</span>'
                            : '<span style="color: #34d399; font-size: 0.7rem; font-weight: 600;">✅ Valid</span>') +
                    '</td>' +
                    '<td style="padding: 0.4rem 0.6rem; text-align: center;"></td>';

                // Tombol aksi
                var actionTd = tr.lastElementChild;
                if (!hasError) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = 'Isi ke form';
                    btn.style.cssText = 'font-size: 0.7rem; padding: 0.22rem 0.6rem; border-radius: 4px; border: 1px solid #38bdf8; background: rgba(2, 132, 199, 0.25); color: #38bdf8; cursor: pointer; font-weight: 600; transition: all 0.15s ease;';
                    btn.onmouseover = function() { this.style.background = '#0284c7'; this.style.color = '#fff'; };
                    btn.onmouseout = function() { this.style.background = 'rgba(2, 132, 199, 0.25)'; this.style.color = '#38bdf8'; };
                    btn.setAttribute('data-row-index', idx);
                    btn.addEventListener('click', function() {
                        fillFormFromRow(row.data);
                        showMcuStatus('success', '<span>✅ Data baris #' + row.index + ' (<strong>' + escapeHtml(row.data.nama_lengkap || '') + '</strong>) telah mengisi form. Periksa dan simpan.</span>');
                    });
                    actionTd.appendChild(btn);
                } else {
                    actionTd.innerHTML = '<span style="font-size: 0.7rem; color: #64748b;">—</span>';
                }

                tableBody.appendChild(tr);
            });

            tableWrap.style.display = 'block';
        })
        .catch(function(err) {
            showMcuStatus('error', '<span>❌ Gagal mengirim file ke server: ' + escapeHtml(err.message || 'Kesalahan jaringan') + '</span>');
        })
        .finally(function() {
            if (uploadBtn) { uploadBtn.disabled = false; uploadBtn.style.opacity = '1'; }
            // Reset input agar file yang sama bisa diupload ulang jika perlu
            input.value = '';
        });
    }

    function fillFormFromRow(data) {
        var form = document.querySelector('#modalOrder form');
        if (!form) return;

        // Nama
        var nameInput = form.querySelector('input[name="patient_name"]');
        if (nameInput && data.nama_lengkap) {
            nameInput.value = data.nama_lengkap;
            nameInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        // MRN / NIK
        var idInput = form.querySelector('input[name="patient_id"]');
        if (idInput) {
            idInput.value = data.mrn_nik || '';
            idInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        // Usia
        var ageInput = form.querySelector('input[name="age"]');
        if (ageInput) {
            ageInput.value = data.usia !== null && data.usia !== undefined ? data.usia : 30;
            ageInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        // Jenis Kelamin
        var genderSelect = form.querySelector('select[name="gender"]');
        if (genderSelect && data.jenis_kelamin) {
            genderSelect.value = data.jenis_kelamin;
            genderSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // Modalitas (Mendukung 180+ Protokol Workstation)
        var modalityInput = form.querySelector('input[name="modality"]');
        var modalitySelect = form.querySelector('select[name="modality"]');
        if (data.modalitas) {
            if (modalityInput) {
                modalityInput.value = data.modalitas;
                updateSelectedModalityBadge(data.modalitas);
            }
            if (modalitySelect) {
                modalitySelect.value = data.modalitas;
                modalitySelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        // Indikasi Klinis
        var descInput = form.querySelector('input[name="study_description"]');
        if (descInput) {
            descInput.value = data.indikasi_klinis || '';
            descInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        // Catatan Petugas
        var notesInput = form.querySelector('textarea[name="order_notes"]');
        if (notesInput) {
            notesInput.value = data.catatan_petugas || '';
            notesInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function openDicomModal() {
        document.getElementById('modalDicomConfig').style.display = 'flex';
    }

    function closeDicomModal() {
        document.getElementById('modalDicomConfig').style.display = 'none';
    }

    function closeCstoreModal() {
        document.getElementById('modalCstoreResult').style.display = 'none';
    }

    function refreshAfterCstore() {
        closeCstoreModal();
        window.location.reload();
    }

    function openDeleteModal(deleteUrl, patientName, patientId, accessionNo) {
        document.getElementById('formConfirmDelete').action = deleteUrl;
        document.getElementById('delModalPatientName').textContent = patientName || '-';
        document.getElementById('delModalPatientId').textContent = patientId || '-';
        document.getElementById('delModalAccession').textContent = accessionNo || '-';
        document.getElementById('modalConfirmDelete').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('modalConfirmDelete').style.display = 'none';
    }

    window.onclick = function(event) {
        const modalProtocol = document.getElementById('modalProtocolPicker');
        const modalOrder = document.getElementById('modalOrder');
        const modalDicom = document.getElementById('modalDicomConfig');
        const modalCstore = document.getElementById('modalCstoreResult');
        const modalDelete = document.getElementById('modalConfirmDelete');
        if (event.target === modalProtocol) closeProtocolPickerModal();
        if (event.target === modalOrder) closeOrderModal();
        if (event.target === modalDicom) closeDicomModal();
        if (event.target === modalCstore) closeCstoreModal();
        if (event.target === modalDelete) closeDeleteModal();
    };

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeProtocolPickerModal();
            closeOrderModal();
            closeDicomModal();
            closeCstoreModal();
            closeDeleteModal();
        }
    });

    function showCstoreModal(info) {
        const modal = document.getElementById('modalCstoreResult');
        const iconEl = document.getElementById('cstoreModalHeaderIcon');
        const titleEl = document.getElementById('cstoreModalHeaderTitle');
        const badgeEl = document.getElementById('cstoreStatusBadge');
        const statusTextEl = document.getElementById('cstoreStatusText');
        const msgEl = document.getElementById('cstoreMessageText');
        const detailsBox = document.getElementById('cstoreDetailsBox');
        const actionBtn = document.getElementById('btnCstoreAction');

        titleEl.textContent = info.title;
        msgEl.textContent = info.message;

        if (info.success) {
            iconEl.textContent = '📡';
            badgeEl.style.background = 'rgba(16, 185, 129, 0.15)';
            badgeEl.style.border = '1px solid rgba(16, 185, 129, 0.45)';
            badgeEl.style.color = '#34d399';
            statusTextEl.textContent = info.statusCode || 'STATUS 0x0000 (SUCCESS)';

            detailsBox.style.display = 'block';
            document.getElementById('cstoreDetailPatient').textContent = info.patientName || '-';
            document.getElementById('cstoreDetailAccession').textContent = info.accessionNumber || '-';
            document.getElementById('cstoreDetailStation').textContent = info.station || 'FUJIFILM_FDR_D-EVO';
            document.getElementById('cstoreDetailSop').textContent = info.sopClass || 'Digital X-Ray (PS 3.4)';

            actionBtn.style.display = 'inline-flex';
            actionBtn.textContent = '🔄 Buka Daftar & Sesi Pasien';
        } else {
            iconEl.textContent = '⚠️';
            badgeEl.style.background = 'rgba(239, 68, 68, 0.15)';
            badgeEl.style.border = '1px solid rgba(239, 68, 68, 0.45)';
            badgeEl.style.color = '#f87171';
            statusTextEl.textContent = info.statusCode || 'STATUS 0x0110 (FAILURE)';

            detailsBox.style.display = 'none';
            actionBtn.style.display = 'none';
        }

        modal.style.display = 'flex';
    }

    function saveDicomConfig(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveDicomCfg');
        const origText = btn.innerHTML;
        btn.innerHTML = '⏳ Menyimpan...';
        btn.disabled = true;

        const ae = document.getElementById('cfgAeTitle').value.trim();
        const port = document.getElementById('cfgPort').value.trim();
        const modality = document.getElementById('cfgModality').value.trim();

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
                document.getElementById('lblIdxAeTitle').innerText = data.ae_title;
                document.getElementById('lblIdxPort').innerText = data.port;
                document.getElementById('lblIdxTarget').innerText = data.modality_source;
                closeDicomModal();
                showCstoreModal({
                    success: true,
                    title: 'KONFIGURASI DICOM NODE TERSIMPAN',
                    message: data.message || 'Parameter jaringan DICOM SCP berhasil diperbarui.',
                    patientName: 'Application Entity: ' + data.ae_title,
                    accessionNumber: 'Port Listener: ' + data.port,
                    station: data.modality_source || 'FUJIFILM / MINDRAY',
                    sopClass: 'NEMA PS 3.4 DICOM SCP Service',
                    statusCode: 'STATUS 0x0000 (CONFIG SAVED)'
                });
            } else {
                showCstoreModal({
                    success: false,
                    title: 'GAGAL MENYIMPAN KONFIGURASI',
                    message: 'Parameter konfigurasi DICOM tidak valid atau gagal disimpan.',
                    statusCode: 'STATUS 0x0110 (CONFIG ERROR)'
                });
            }
        })
        .catch(err => {
            showCstoreModal({
                success: false,
                title: 'TERJADI KESALAHAN JARINGAN',
                message: err.message || 'Gagal berkomunikasi dengan server.',
                statusCode: 'STATUS 0x0110 (NETWORK ERROR)'
            });
        })
        .finally(() => {
            btn.innerHTML = origText;
            btn.disabled = false;
        });
    }

    function triggerSimulateFuji() {
        const btn = document.querySelector('.btn-fuji');
        const origText = btn.innerHTML;
        btn.innerHTML = '⏳ Mentransmisikan DICOM via C-STORE...';
        btn.disabled = true;

        fetch("{{ route('scans.simulate-fuji') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(async res => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok && data.status !== 'success') {
                throw new Error(data.message || 'Gagal simulasi transmisi C-STORE dari stasiun modalitas.');
            }
            return data;
        })
        .then(data => {
            showCstoreModal({
                success: true,
                title: data.title || 'TRANSMISI DICOM C-STORE BERHASIL',
                message: data.message || 'Citra rontgen dari mesin Fujifilm FDR D-EVO berhasil diterima dan diarsipkan oleh Hyu PACS SCP.',
                patientName: data.patient_name ? `${data.patient_name} (${data.patient_id || '-'})` : 'Pasien MCU Terkini',
                accessionNumber: data.accession_number || '-',
                station: data.station || 'FUJIFILM_FDR_D-EVO',
                sopClass: data.sop_class || 'Digital X-Ray (1.2.840.10008.5.1.4.1.1.1)',
                statusCode: data.status_code || 'STATUS 0x0000 (SUCCESS)'
            });
        })
        .catch(err => {
            showCstoreModal({
                success: false,
                title: 'GAGAL TRANSMISI DICOM C-STORE',
                message: err.message || 'Terjadi kesalahan saat memproses transmisi C-STORE.',
                statusCode: 'STATUS 0x0110 (FAILURE)'
            });
        })
        .finally(() => {
            btn.innerHTML = origText;
            btn.disabled = false;
        });
    }

    // ==========================================================================
    // WORKSTATION PROTOCOL PICKER DATASET & CONTROLLER (180 PROTOKOL LENGKAP)
    // ==========================================================================
    const WORKSTATION_PROTOCOLS = {
        often_used: [
            'Sternum OBL,right anterior', 'Sternum OBL,left anterior', 'Sternum LAT,right', 'Skull PA Axial',
            'Skull LAT,left', 'Skull AP Axial', 'Skull AP', 'Sinuses, Waters', 'Right Hip LAT', 'Right Hip Axial',
            'Right Clavicle AP', 'Right Ankle OBL', 'Right Ankle LAT', 'Ribs OBL,right anterior', 'Ribs OBL,left anterior',
            'Pelvis LAT,right', 'Pelvis LAT,left', 'Nasal Bone LAT,right', 'Nasal Bone LAT,left', 'Left Hip LAT',
            'Left Hip Axial', 'Left Clavicle AP', 'Left Ankle OBL', 'Chest PA', 'Chest OBL,right anterior',
            'Chest OBL,left anterior', 'Chest LAT,left', 'Chest AP', 'Abdomen LAT,left', 'Abdomen AP,erect'
        ],
        head: [
            'Zygomatic Arche', 'Stenvers view', 'Skull Townes', 'Skull PA Axial', 'Skull PA', 'Skull LAT,right',
            'Skull LAT,left', 'Skull AP Axial', 'Skull AP', 'Sinuses, Waters', 'Sella Turcica', 'Petrous Bone',
            'Orbit PA', 'Nasal Bone LAT,right', 'Nasal Bone LAT,left', 'Nasal Bone Axial', 'Mastoid',
            'Mandible,jaw open', 'Mandible,jaw close'
        ],
        thorax: [
            'Sternum OBL,right anterior', 'Sternum OBL,left anterior', 'Sternum LAT,right', 'Sternum LAT,left',
            'Right Clavicle AP Axial', 'Right Clavicle AP', 'Ribs OBL,right anterior', 'Ribs OBL,left anterior',
            'Rib PA', 'Left Clavicle AP Axial', 'Left Clavicle AP', 'Chest PA', 'Chest OBL,right anterior',
            'Chest OBL,left anterior', 'Chest LAT,right', 'Chest LAT,left', 'Chest AP'
        ],
        spine: [
            'T-spine LAT,right', 'T-spine LAT,left', 'T-spine AP', 'Sacrum LAT,right', 'Sacrum LAT,left',
            'Sacrum AP', 'L-spine OBL,right', 'L-spine OBL,left', 'L-spine LAT,right', 'L-spine LAT,left',
            'L-spine AP', 'Coccyx LAT,right', 'Coccyx LAT,left', 'Coccyx AP', 'C-spine OBL,right',
            'C-spine OBL,left', 'C-spine LAT,right', 'C-spine LAT,left', 'C-spine AP'
        ],
        right_arm: [
            'Right Wrist PA', 'Right Wrist LAT', 'Right Shoulder Transaxillary', 'Right Shoulder AP',
            'Right Scapula LAT', 'Right Scapula AP', 'Right Humerus LAT', 'Right Humerus AP', 'Right Hand PA',
            'Right Hand OBL', 'Right Hand LAT', 'Right Forearm LAT', 'Right Forearm AP', 'Right Finger PA',
            'Right Finger LAT', 'Right Elbow LAT', 'Right Elbow AP'
        ],
        left_arm: [
            'Left Wrist PA', 'Left Wrist LAT', 'Left Shoulder Transaxillary', 'Left Shoulder AP',
            'Left Scapula LAT', 'Left Scapula AP', 'Left Humerus LAT', 'Left Humerus AP', 'Left Hand PA',
            'Left Hand OBL', 'Left Hand LAT', 'Left Forearm LAT', 'Left Forearm AP', 'Left Finger PA',
            'Left Finger LAT', 'Left Elbow LAT', 'Left Elbow AP'
        ],
        pelvis: [
            'Pelvis LAT,right', 'Pelvis LAT,left', 'Pelvis AP', 'IVP9', 'IVP8', 'IVP7', 'IVP6', 'IVP5',
            'IVP4', 'IVP3', 'IVP2', 'IVP10', 'IVP1', 'Abdomen LAT,right', 'Abdomen LAT,left',
            'Abdomen AP,supine', 'Abdomen AP,erect'
        ],
        right_leg: [
            'Right Toe OBL', 'Right Toe LAT', 'Right Toe AP', 'Right Tibia LAT', 'Right Tibia AP',
            'Right Patella Tangential', 'Right Patella AP', 'Right Knee LAT', 'Right Knee AP', 'Right Hip LAT',
            'Right Hip Axial', 'Right Foot OBL', 'Right Foot LAT,standing', 'Right Foot LAT', 'Right Foot AP',
            'Right Femur LAT', 'Right Femur AP', 'Right Calcaneus LAT', 'Right Calcaneus Axial', 'Right Ankle OBL',
            'Right Ankle LAT', 'Hip AP'
        ],
        left_leg: [
            'Left Toe OBL', 'Left Toe LAT', 'Left Toe AP', 'Left Tibia LAT', 'Left Tibia AP',
            'Left Patella Tangential', 'Left Patella AP', 'Left Knee LAT', 'Left Knee AP', 'Left Hip LAT',
            'Left Hip Axial', 'Left Foot OBL', 'Left Foot LAT,standing', 'Left Foot LAT', 'Left Foot AP',
            'Left Femur LAT', 'Left Femur AP', 'Left Calcaneus LAT', 'Left Calcaneus Axial', 'Left Ankle OBL',
            'Left Ankle LAT', 'Hip AP'
        ],
        advanced: [
            'IVP Series (1-10)', 'Whole Spine AP Standing', 'Whole Spine LAT Standing', 'Scoliosis Series AP/LAT',
            'Scanogram (Leg Length Discrepancy)', 'Barium Swallow / Meal', 'Barium Enema Double Contrast',
            'HSG (Hysterosalpingography)', 'Urethrocystography', 'Fistulography', 'T-Tube Cholangiography',
            'Arthrography', 'Stress View Knee Valgus/Varus', 'Stress View Ankle Inversion', 'Mandible Panorex View'
        ]
    };

    const PROTOCOL_CATEGORY_INFO = {
        often_used: { en: 'Often Used', id: 'Often Used', icon: '⚡' },
        head: { en: 'Head / Skull', id: 'Head / Skull', icon: '🧠' },
        spine: { en: 'Spine', id: 'Spine', icon: '🦴' },
        thorax: { en: 'Thorax', id: 'Thorax', icon: '🫁' },
        right_arm: { en: 'Right Upper Limb', id: 'Right Upper Limb', icon: '💪' },
        left_arm: { en: 'Left Upper Limb', id: 'Left Upper Limb', icon: '💪' },
        pelvis: { en: 'Pelvis & Abdomen', id: 'Pelvis & Abdomen', icon: '🩻' },
        right_leg: { en: 'Right Lower Limb', id: 'Right Lower Limb', icon: '🦵' },
        left_leg: { en: 'Left Lower Limb', id: 'Left Lower Limb', icon: '🦵' },
        advanced: { en: 'Advanced', id: 'Advanced', icon: '🔬' }
    };

    let currentPickerCategory = 'often_used';
    let currentPickerSort = 'default';
    let currentPickerSearch = '';
    let tempSelectedProtocol = 'Chest PA';

    function openProtocolPickerModal() {
        const modal = document.getElementById('modalProtocolPicker');
        if (!modal) return;
        modal.style.display = 'flex';

        // Set initial temp selection from hidden input
        const currentVal = document.getElementById('selectedModalityInput')?.value || 'Chest PA';
        tempSelectedProtocol = currentVal;
        updatePickerTraySelection(tempSelectedProtocol);

        // Find initial category for currentVal
        const foundCat = findCategoryForProtocol(tempSelectedProtocol);
        selectProtocolCategory(foundCat || 'often_used', false);
    }

    function closeProtocolPickerModal() {
        const modal = document.getElementById('modalProtocolPicker');
        if (modal) modal.style.display = 'none';
        clearProtocolSearch();
    }

    function selectProtocolCategory(categoryKey, clearSearch = true) {
        if (clearSearch && currentPickerSearch) {
            clearProtocolSearch(false);
        }

        currentPickerCategory = categoryKey;

        // Reset skeleton and glowing layers
        document.querySelectorAll('.skel-region').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.skel-glow-layer').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.skel-tab-btn').forEach(el => el.classList.remove('active'));

        const hudEl = document.getElementById('skelRegionHud');
        const info = PROTOCOL_CATEGORY_INFO[categoryKey] || { en: 'Select Anatomical Region' };

        if (categoryKey === 'often_used') {
            document.getElementById('tabOftenUsed')?.classList.add('active');
            if (hudEl) hudEl.textContent = 'Often Used (Frequently Performed)';
        } else if (categoryKey === 'advanced') {
            document.getElementById('tabAdvanced')?.classList.add('active');
            if (hudEl) hudEl.textContent = 'Advanced (Specialized Protocols)';
        } else {
            const skelEl = document.getElementById('skel-' + categoryKey.replace('_', '-'));
            if (skelEl) skelEl.classList.add('active');
            const glowEl = document.getElementById('glow-' + categoryKey);
            if (glowEl) glowEl.classList.add('active');
            if (hudEl) hudEl.textContent = info.en;
        }

        renderProtocolGrid();
    }

    function renderProtocolGrid() {
        const container = document.getElementById('protocolGridContainer');
        const titleEl = document.getElementById('protocolGridTitle');
        const countEl = document.getElementById('protocolGridCount');
        if (!container) return;

        let list = [];
        let isSearching = Boolean(currentPickerSearch.trim());

        if (isSearching) {
            const q = currentPickerSearch.toLowerCase().trim();
            const seen = new Set();
            for (const [catKey, items] of Object.entries(WORKSTATION_PROTOCOLS)) {
                for (const item of items) {
                    if (item.toLowerCase().includes(q) && !seen.has(item)) {
                        seen.add(item);
                        list.push({ name: item, category: catKey });
                    }
                }
            }
            if (titleEl) titleEl.textContent = 'Hasil Pencarian: "' + currentPickerSearch + '"';
            if (countEl) countEl.textContent = list.length + ' Pemeriksaan';
        } else {
            const items = WORKSTATION_PROTOCOLS[currentPickerCategory] || [];
            list = items.map(name => ({ name: name, category: currentPickerCategory }));
            const info = PROTOCOL_CATEGORY_INFO[currentPickerCategory];
            if (titleEl) titleEl.textContent = info ? info.en : currentPickerCategory;
            if (countEl) countEl.textContent = list.length + ' Pemeriksaan';
        }

        // Apply sort
        if (currentPickerSort === 'az') {
            list.sort((a, b) => a.name.localeCompare(b.name));
        }

        container.innerHTML = '';

        if (list.length === 0) {
            container.innerHTML = `
                <div style="grid-column: 1 / -1; padding: 3rem 1rem; text-align: center; color: #64748b;">
                    <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">🔍</div>
                    <div style="font-weight: 600; color: #94a3b8; font-size: 0.92rem;">Tidak ada pemeriksaan yang cocok</div>
                    <div style="font-size: 0.78rem; margin-top: 0.25rem;">Coba gunakan kata kunci organ atau proyeksi lain.</div>
                </div>
            `;
            return;
        }

        list.forEach(item => {
            const card = document.createElement('div');
            card.className = 'protocol-card' + (item.name === tempSelectedProtocol ? ' selected' : '');
            
            const catInfo = PROTOCOL_CATEGORY_INFO[item.category] || { en: item.category };
            
            card.innerHTML = `
                ${isSearching ? `<span class="protocol-card-tag">${escapeHtml(catInfo.en)}</span>` : ''}
                <span>${escapeHtml(item.name)}</span>
            `;

            // Single click selects
            card.onclick = function() {
                tempSelectedProtocol = item.name;
                updatePickerTraySelection(item.name, item.category);
                container.querySelectorAll('.protocol-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
            };

            // Double click selects and confirms immediately
            card.ondblclick = function() {
                tempSelectedProtocol = item.name;
                confirmProtocolSelection();
            };

            container.appendChild(card);
        });
    }

    function setProtocolSort(mode) {
        currentPickerSort = mode;
        const btnFreq = document.getElementById('btnSortFreq');
        const btnAz = document.getElementById('btnSortAz');
        if (mode === 'az') {
            btnFreq?.classList.remove('active');
            btnAz?.classList.add('active');
        } else {
            btnFreq?.classList.add('active');
            btnAz?.classList.remove('active');
        }
        renderProtocolGrid();
    }

    function handleProtocolSearch(query) {
        currentPickerSearch = query || '';
        const clearBtn = document.getElementById('protocolSearchClear');
        if (clearBtn) clearBtn.style.display = currentPickerSearch ? 'block' : 'none';
        renderProtocolGrid();
    }

    function clearProtocolSearch(reRender = true) {
        currentPickerSearch = '';
        const input = document.getElementById('protocolSearchInput');
        if (input) input.value = '';
        const clearBtn = document.getElementById('protocolSearchClear');
        if (clearBtn) clearBtn.style.display = 'none';
        if (reRender) renderProtocolGrid();
    }

    function updatePickerTraySelection(protocolName, categoryKey = null) {
        const nameEl = document.getElementById('pickerSelectedName');
        const catBadge = document.getElementById('pickerSelectedCategoryBadge');
        if (nameEl) nameEl.textContent = protocolName;

        const cat = categoryKey || findCategoryForProtocol(protocolName);
        const info = PROTOCOL_CATEGORY_INFO[cat];
        if (catBadge) {
            catBadge.textContent = info ? info.en : 'General';
        }
    }

    function confirmProtocolSelection() {
        if (!tempSelectedProtocol) return;
        const input = document.getElementById('selectedModalityInput');
        if (input) {
            input.value = tempSelectedProtocol;
            updateSelectedModalityBadge(tempSelectedProtocol);
        }
        closeProtocolPickerModal();
    }

    function updateSelectedModalityBadge(protocolName) {
        const textEl = document.getElementById('selectedModalityText');
        const badgeEl = document.getElementById('selectedModalityCategoryBadge');
        const iconEl = document.getElementById('selectedModalityIcon');
        const inputEl = document.getElementById('selectedModalityInput');

        if (inputEl) inputEl.value = protocolName;
        if (textEl) textEl.textContent = protocolName;

        const cat = findCategoryForProtocol(protocolName);
        const info = PROTOCOL_CATEGORY_INFO[cat] || { en: 'Rontgen / CR', icon: '🩻' };

        if (badgeEl) badgeEl.textContent = info.en;
        if (iconEl) iconEl.textContent = info.icon || '🩻';
    }

    function findCategoryForProtocol(protocolName) {
        if (!protocolName) return 'often_used';
        for (const [catKey, items] of Object.entries(WORKSTATION_PROTOCOLS)) {
            if (catKey === 'often_used') continue; // Prioritaskan anatomi spesifik
            if (items.some(p => p.toLowerCase() === protocolName.toLowerCase())) {
                return catKey;
            }
        }
        // Cek often_used jika tidak ditemukan di anatomi spesifik
        if (WORKSTATION_PROTOCOLS.often_used.some(p => p.toLowerCase() === protocolName.toLowerCase())) {
            return 'often_used';
        }
        // Fallback berdasarkan kata kunci
        const lower = protocolName.toLowerCase();
        if (lower.includes('chest') || lower.includes('sternum') || lower.includes('rib')) return 'thorax';
        if (lower.includes('skull') || lower.includes('nasal') || lower.includes('mandible') || lower.includes('sinus')) return 'head';
        if (lower.includes('spine') || lower.includes('sacrum') || lower.includes('coccyx')) return 'spine';
        if (lower.includes('arm') || lower.includes('wrist') || lower.includes('hand') || lower.includes('elbow') || lower.includes('finger') || lower.includes('shoulder')) return 'right_arm';
        if (lower.includes('leg') || lower.includes('ankle') || lower.includes('knee') || lower.includes('femur') || lower.includes('foot') || lower.includes('toe')) return 'right_leg';
        if (lower.includes('pelvis') || lower.includes('abdomen') || lower.includes('ivp')) return 'pelvis';
        return 'often_used';
    }

    // Attach Skeleton Region Hover and Click Events
    document.addEventListener('DOMContentLoaded', function() {
        const regions = document.querySelectorAll('.skel-region');
        const hudEl = document.getElementById('skelRegionHud');

        regions.forEach(region => {
            const cat = region.getAttribute('data-category');
            const label = region.getAttribute('data-label');

            region.addEventListener('mouseenter', function() {
                if (hudEl) hudEl.textContent = label;
                const glowEl = document.getElementById('glow-' + cat);
                if (glowEl && cat !== currentPickerCategory) {
                    glowEl.classList.add('hovered');
                }
            });

            region.addEventListener('mouseleave', function() {
                const glowEl = document.getElementById('glow-' + cat);
                if (glowEl) glowEl.classList.remove('hovered');
                if (hudEl) {
                    const activeInfo = PROTOCOL_CATEGORY_INFO[currentPickerCategory];
                    hudEl.textContent = activeInfo ? activeInfo.en : 'Select Anatomical Region';
                }
            });

            region.addEventListener('click', function() {
                selectProtocolCategory(cat);
            });
        });
    });

</script>

</body>
</html>

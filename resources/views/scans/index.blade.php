<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hyu - Pusat Operasional MCU & Standalone PACS Radiologi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        /* ── Staging Table (Excel CRUD) Styles ── */
        .modal-content-wide {
            max-width: 1240px !important;
            width: 96vw !important;
            max-height: 92vh !important;
            display: flex !important;
            flex-direction: column !important;
            transition: max-width 0.25s ease, width 0.25s ease;
        }

        .staging-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding: 0.65rem 0.9rem;
            border-radius: 8px;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(56, 189, 248, 0.25);
            margin-bottom: 0.85rem;
        }

        .staging-summary-info {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.82rem;
            flex-wrap: wrap;
        }

        .staging-file-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.55rem;
            border-radius: 5px;
            background: rgba(2, 132, 199, 0.2);
            color: #38bdf8;
            font-weight: 600;
            font-size: 0.75rem;
            border: 1px solid rgba(56, 189, 248, 0.35);
        }

        .staging-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.65rem;
            margin-bottom: 0.75rem;
        }

        .staging-tabs {
            display: flex;
            gap: 0.35rem;
            align-items: center;
        }

        .staging-tab {
            padding: 0.35rem 0.7rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--card-border);
            background: rgba(30, 41, 59, 0.7);
            color: var(--text-muted);
            transition: all 0.15s;
        }

        .staging-tab:hover {
            color: #f1f5f9;
            background: rgba(51, 65, 85, 0.8);
        }

        .staging-tab.active {
            background: var(--primary);
            color: #fff;
            border-color: #38bdf8;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.35);
        }

        .staging-search-wrap {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
            max-width: 340px;
        }

        .staging-search-input {
            width: 100%;
            background-color: #1e293b;
            border: 1px solid #334155;
            color: var(--text-main);
            padding: 0.38rem 0.7rem;
            border-radius: 6px;
            font-size: 0.78rem;
            outline: none;
            transition: border-color 0.15s;
        }

        .staging-search-input:focus {
            border-color: #38bdf8;
        }

        .staging-table-container {
            flex: 1;
            overflow-y: auto;
            overflow-x: auto;
            max-height: calc(88vh - 290px);
            min-height: 280px;
            border: 1px solid var(--card-border);
            border-radius: 8px;
            background: rgba(15, 23, 42, 0.65);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .staging-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.78rem;
            text-align: left;
        }

        .staging-table th {
            position: sticky;
            top: 0;
            background: #0f172a;
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.55rem 0.65rem;
            border-bottom: 2px solid var(--card-border);
            z-index: 5;
            white-space: nowrap;
        }

        .staging-table td {
            padding: 0.45rem 0.55rem;
            border-bottom: 1px solid rgba(51, 65, 85, 0.5);
            vertical-align: middle;
            color: #e2e8f0;
        }

        .staging-table tr:hover td {
            background-color: rgba(30, 41, 59, 0.45);
        }

        .staging-row-invalid td {
            background-color: rgba(245, 158, 11, 0.05);
        }

        .staging-row-invalid:hover td {
            background-color: rgba(245, 158, 11, 0.1) !important;
        }

        .staging-cell {
            cursor: pointer;
            border-radius: 4px;
            padding: 0.22rem 0.4rem;
            min-height: 1.6rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.3rem;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }

        .staging-cell:hover {
            background: rgba(2, 132, 199, 0.15);
            border-color: rgba(56, 189, 248, 0.45);
        }

        .staging-cell-error {
            border: 1px solid rgba(239, 68, 68, 0.75) !important;
            background: rgba(239, 68, 68, 0.18) !important;
            color: #fca5a5 !important;
            border-radius: 4px;
        }

        .staging-error-badge {
            color: #f87171;
            font-size: 0.8rem;
            font-weight: bold;
            flex-shrink: 0;
            cursor: help;
        }

        .staging-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .staging-status-ready {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        .staging-status-invalid {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.4);
        }

        .btn-action-icon {
            background: none;
            border: 1px solid transparent;
            border-radius: 4px;
            padding: 0.22rem 0.45rem;
            cursor: pointer;
            color: var(--text-muted);
            transition: all 0.15s ease;
            font-size: 0.8rem;
            line-height: 1;
        }

        .btn-action-edit:hover {
            background: rgba(2, 132, 199, 0.25);
            color: #38bdf8;
            border-color: rgba(56, 189, 248, 0.45);
        }

        .btn-action-delete:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.45);
        }

        .staging-footer-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding-top: 0.85rem;
            margin-top: 0.5rem;
            border-top: 1px solid var(--card-border);
        }

        .staging-undo-toast {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 99999;
            background: #1e293b;
            border: 1px solid rgba(56, 189, 248, 0.45);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.85), 0 0 20px rgba(2, 132, 199, 0.3);
            border-radius: 8px;
            padding: 0.65rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #f8fafc;
            font-size: 0.82rem;
            animation: slideUpFade 0.25s ease-out;
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
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
    <div class="stats-grid" id="statsGridWrap">
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
    <div class="card" id="worklistTableWrap">
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
    <div class="modal-content" id="modalOrderContent" style="max-width: 660px; transition: max-width 0.25s ease;">
        <div class="modal-header" style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.15rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--card-border);">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <div style="font-weight: 700; font-size: 1rem; color: #f8fafc; letter-spacing: -0.01em;">➕ Buat Sesi / Order MCU Baru (Hyu)</div>
                
                <!-- Action Buttons: Upload Excel & Unduh Template -->
                <div class="mcu-header-actions" id="mcuHeaderActions">
                    <input type="file" id="mcuExcelFile" accept=".xlsx,.xls,.csv" style="display: none;" onchange="handleMcuExcelUpload(this)">
                    
                    <button type="button" id="mcuUploadBtn" class="mcu-header-btn mcu-header-btn-upload" onclick="document.getElementById('mcuExcelFile').click()" title="Upload file Excel untuk mengisi form otomatis">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span>Upload Excel</span>
                    </button>

                    <a href="{{ route('mcu.import.template', [], false) }}" class="mcu-header-btn mcu-header-btn-template" title="Unduh format template Excel MCU (.xlsx)">
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

        <!-- Banner Notifikasi Upload (Hanya saat proses upload berlangsung atau error file) -->
        <div id="mcuUploadNotification" style="display: none; margin-bottom: 1.15rem;">
            <div id="mcuUploadStatus" style="padding: 0.6rem 0.85rem; border-radius: 8px; font-size: 0.78rem; display: flex; align-items: center; justify-content: space-between; gap: 0.6rem; border: 1px solid transparent;">
                <div id="mcuUploadStatusContent" style="display: flex; align-items: center; gap: 0.5rem; flex: 1; flex-wrap: wrap;"></div>
                <button type="button" onclick="dismissMcuStatus()" style="background: none; border: none; color: inherit; opacity: 0.6; cursor: pointer; font-size: 1.15rem; line-height: 1; padding: 0 0.2rem;" title="Tutup pemberitahuan">&times;</button>
            </div>
        </div>

        <!-- View 1: Form Manual (Default jika tidak ada Excel aktif) -->
        <div id="manualOrderFormWrap">
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
                        <label class="form-label">Modalitas Pemeriksaan *</label>
                        <select name="modality" class="form-control" required>
                            <option value="Thorax PA">Thorax PA (Rontgen Dada)</option>
                            <option value="Thorax AP">Thorax AP</option>
                            <option value="Thorax Lateral">Thorax Lateral</option>
                            <option value="Waters">Waters (Sinus Paranasal / SPN)</option>
                            <option value="USG Abdomen">USG Abdomen</option>
                            <option value="USG Pelvic">USG Pelvic</option>
                        </select>
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

        <!-- View 2: Staging Table CRUD (Aktif setelah file Excel diunggah) -->
        <div id="stagingExcelWrap" style="display: none; flex-direction: column;">
            <!-- Top Summary Bar -->
            <div class="staging-top-bar">
                <div class="staging-summary-info">
                    <span class="staging-file-badge">📄 <span id="stagingFileName">data_mcu.xlsx</span></span>
                    <span id="stagingSummaryText" style="color: #cbd5e1; font-weight: 500;">Ditemukan 0 pasien (0 siap, 0 perlu dicek)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="confirmGantiFile()" title="Ganti dengan file Excel lain" style="font-size: 0.74rem; padding: 0.3rem 0.65rem;">
                        🔄 Ganti File
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="confirmCloseStaging()" title="Tutup Excel dan kembali ke form manual" style="font-size: 0.74rem; padding: 0.3rem 0.65rem;">
                        ✕ Tutup Excel
                    </button>
                </div>
            </div>

            <!-- Toolbar: Filter Status, Search, Tambah & Hapus -->
            <div class="staging-toolbar">
                <div class="staging-tabs">
                    <button type="button" class="staging-tab active" data-filter="all" onclick="setStagingStatusFilter('all')">
                        Semua (<span id="countTabAll">0</span>)
                    </button>
                    <button type="button" class="staging-tab" data-filter="ready" onclick="setStagingStatusFilter('ready')">
                        ✅ Siap (<span id="countTabReady">0</span>)
                    </button>
                    <button type="button" class="staging-tab" data-filter="invalid" onclick="setStagingStatusFilter('invalid')">
                        ⚠️ Perlu dicek (<span id="countTabInvalid">0</span>)
                    </button>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <div class="staging-search-wrap">
                        <input type="text" id="stagingSearchInput" class="staging-search-input" placeholder="🔍 Cari nama / NIK..." oninput="handleStagingSearch(this.value)">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addStagingRow()" title="Tambah baris kosong di paling atas" style="border-color: #38bdf8; color: #38bdf8; background: rgba(2, 132, 199, 0.15); font-size: 0.75rem; padding: 0.35rem 0.7rem;">
                        + Tambah Pasien
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnDeleteSelected" onclick="deleteSelectedStagingRows()" title="Hapus semua baris yang dicentang" disabled style="border-color: rgba(239, 68, 68, 0.4); color: #f87171; background: rgba(239, 68, 68, 0.1); font-size: 0.75rem; padding: 0.35rem 0.7rem; cursor: not-allowed; opacity: 0.5;">
                        🗑️ Hapus Terpilih
                    </button>
                </div>
            </div>

            <!-- Staging Table Container -->
            <div class="staging-table-container">
                <table class="staging-table">
                    <thead>
                        <tr>
                            <th style="width: 38px; text-align: center;">
                                <input type="checkbox" id="stagingMasterCheckbox" onchange="toggleSelectAllStaging(this.checked)" title="Centang semua baris Siap yang tampil">
                            </th>
                            <th style="width: 44px; text-align: center;">No</th>
                            <th style="width: 110px;">Status</th>
                            <th style="min-width: 160px;">Nama Lengkap *</th>
                            <th style="min-width: 140px;">No. RM / NIK</th>
                            <th style="width: 75px;">Usia *</th>
                            <th style="width: 80px;">JK *</th>
                            <th style="min-width: 140px;">Modalitas *</th>
                            <th style="min-width: 150px;">Indikasi Klinis</th>
                            <th style="min-width: 150px;">Catatan</th>
                            <th style="width: 80px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="stagingTableBody"></tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="pagination-wrapper" style="padding: 0.65rem 0; margin-top: 0.45rem; border-top: 1px solid var(--card-border);">
                <div id="stagingPaginationInfo" class="pagination-info" style="font-size: 0.75rem;">
                    Menampilkan 0 dari 0 pasien
                </div>
                <div id="stagingPaginationLinks" class="pagination-links"></div>
            </div>

            <!-- Staging Footer Bar -->
            <div class="staging-footer-bar">
                <div id="stagingSelectedCountInfo" style="font-size: 0.8rem; color: #cbd5e1; font-weight: 600;">
                    0 dipilih dari 0 pasien
                </div>
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <button type="button" class="btn btn-secondary" onclick="confirmCloseStaging()">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSubmitStaging" onclick="submitStagingToWorklist()" disabled>
                        Kirim 0 Pasien ke Worklist
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sub-Dialog: Edit Pasien Staging (Dialog untuk edit nyaman) -->
<div class="modal-backdrop" id="modalEditStagingPatient" style="display: none; z-index: 10000;">
    <div class="modal-content" style="max-width: 580px; border: 1px solid rgba(56, 189, 248, 0.45); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.9), 0 0 35px rgba(2, 132, 199, 0.2);">
        <div class="modal-header" style="margin-bottom: 1.15rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--card-border);">
            <div style="font-weight: 700; font-size: 0.98rem; color: #f8fafc; display: flex; align-items: center; gap: 0.5rem;">
                <span>✏️</span> Edit Data Pasien MCU (Staging)
            </div>
            <button type="button" class="btn-close" onclick="closeEditStagingModal()">&times;</button>
        </div>
        <div>
            <input type="hidden" id="editStagingRowId">
            <div class="form-group">
                <label class="form-label">Nama Lengkap Pasien *</label>
                <input type="text" id="editStagingNama" class="form-control" placeholder="Contoh: Budi Santoso" required>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">No. RM / NIK (16 Digit jika NIK)</label>
                    <input type="text" id="editStagingNik" class="form-control" placeholder="Kosongkan jika otomatis No. RM">
                </div>
                <div class="form-group">
                    <label class="form-label">Usia (Tahun) *</label>
                    <input type="number" id="editStagingUsia" class="form-control" min="0" max="120" placeholder="0 - 120">
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin *</label>
                    <select id="editStagingGender" class="form-control">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Modalitas Pemeriksaan *</label>
                    <select id="editStagingModality" class="form-control">
                        <option value="Thorax PA">Thorax PA (Rontgen Dada)</option>
                        <option value="Thorax AP">Thorax AP</option>
                        <option value="Thorax Lateral">Thorax Lateral</option>
                        <option value="Waters">Waters (Sinus Paranasal / SPN)</option>
                        <option value="USG Abdomen">USG Abdomen</option>
                        <option value="USG Pelvic">USG Pelvic</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Indikasi Klinis / Deskripsi Sesi</label>
                <input type="text" id="editStagingIndikasi" class="form-control" placeholder="Contoh: MCU Karyawan PT Semen Gresik">
            </div>
            <div class="form-group">
                <label class="form-label">Catatan Tambahan untuk Petugas Rontgen</label>
                <textarea id="editStagingCatatan" rows="3" class="form-control" placeholder="Catatan pengantar khusus..."></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.25rem;">
                <button type="button" class="btn btn-secondary" onclick="closeEditStagingModal()">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveEditStagingModal()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<!-- Floating Toast Undo Delete -->
<div id="stagingUndoToast" class="staging-undo-toast" style="display: none;">
    <span id="stagingUndoMessage">🗑️ Pasien dihapus</span>
    <button type="button" onclick="undoStagingDelete()" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.65rem; font-size: 0.75rem;">Urungkan (Undo)</button>
    <button type="button" onclick="dismissStagingUndo()" style="background:none; border:none; color:#94a3b8; font-size:1.15rem; cursor:pointer; line-height:1; padding:0 0.2rem;" title="Tutup">&times;</button>
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
    /* Helper untuk mengambil CSRF Token secara aman dari meta atau form */
    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta && meta.content) return meta.content;
        var input = document.querySelector('input[name="_token"]');
        if (input && input.value) return input.value;
        return '{{ csrf_token() }}';
    }

    /* ═══════════════════════════════════════════════════════════════════════
       STATE MANAGEMENT: STAGING EXCEL TABLE (CRUD)
       ═══════════════════════════════════════════════════════════════════════ */
    var stagingRows = [];
    var stagingCurrentPage = 1;
    var stagingRowsPerPage = 25;
    var stagingSearchQuery = '';
    var stagingStatusFilter = 'all'; // 'all' | 'ready' | 'invalid'
    var stagingFileName = '';
    var recentlyDeletedRows = [];
    var stagingUndoTimeout = null;
    var activeInlineEdit = null;

    var ALLOWED_MODALITIES = [
        'Thorax PA',
        'Thorax AP',
        'Thorax Lateral',
        'Waters',
        'USG Abdomen',
        'USG Pelvic'
    ];

    /* ─── Modal Open & Close Handlers ─── */
    function openOrderModal() {
        document.getElementById('modalOrder').style.display = 'flex';
    }

    function closeOrderModal() {
        document.getElementById('modalOrder').style.display = 'none';
        resetMcuUploadState();
    }

    function confirmCloseOrderModal() {
        if (stagingRows.length > 0) {
            if (!confirm('Data pasien di tabel staging yang belum dikirim akan hilang. Yakin ingin menutup?')) {
                return;
            }
        }
        closeOrderModal();
    }

    function confirmCloseStaging() {
        if (stagingRows.length > 0) {
            if (!confirm('Data hasil edit di tabel staging akan hilang. Yakin ingin kembali ke form manual?')) {
                return;
            }
        }
        resetMcuUploadState();
    }

    function confirmGantiFile() {
        if (stagingRows.length > 0) {
            if (!confirm('Data hasil edit saat ini akan diganti dengan file baru. Lanjutkan?')) {
                return;
            }
        }
        var fileInput = document.getElementById('mcuExcelFile');
        if (fileInput) fileInput.click();
    }

    function resetMcuUploadState() {
        var fileInput = document.getElementById('mcuExcelFile');
        if (fileInput) fileInput.value = '';

        dismissMcuStatus();
        dismissStagingUndo();

        stagingRows = [];
        recentlyDeletedRows = [];
        stagingCurrentPage = 1;
        stagingSearchQuery = '';
        stagingStatusFilter = 'all';
        stagingFileName = '';
        activeInlineEdit = null;

        var modalContent = document.getElementById('modalOrderContent');
        if (modalContent) modalContent.classList.remove('modal-content-wide');

        var stagingWrap = document.getElementById('stagingExcelWrap');
        if (stagingWrap) stagingWrap.style.display = 'none';

        var manualWrap = document.getElementById('manualOrderFormWrap');
        if (manualWrap) manualWrap.style.display = 'block';

        var searchInput = document.getElementById('stagingSearchInput');
        if (searchInput) searchInput.value = '';
    }

    function dismissMcuStatus() {
        var notif = document.getElementById('mcuUploadNotification');
        if (notif) notif.style.display = 'none';
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

    /* ─── Excel File Upload Handler (Tahap 1) ─── */
    function handleMcuExcelUpload(input) {
        if (!input.files || !input.files[0]) return;

        var file = input.files[0];
        var uploadBtn = document.getElementById('mcuUploadBtn');

        var allowedExt = ['xlsx', 'xls', 'csv'];
        var ext = file.name.split('.').pop().toLowerCase();
        if (allowedExt.indexOf(ext) === -1) {
            showMcuStatus('error', '<span>❌ Format file tidak didukung. Gunakan file <strong>.xlsx</strong>, <strong>.xls</strong>, atau <strong>.csv</strong>.</span>');
            input.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            showMcuStatus('error', '<span>❌ Ukuran file terlalu besar (maksimal 5 MB).</span>');
            input.value = '';
            return;
        }

        showMcuStatus('loading', '<span>⏳ Memproses file <strong>' + escapeHtml(file.name) + '</strong>...</span>');
        if (uploadBtn) { uploadBtn.disabled = true; uploadBtn.style.opacity = '0.6'; }

        var formData = new FormData();
        formData.append('file', file);
        formData.append('_token', getCsrfToken());

        fetch("{{ route('worklist.excel.preview', [], false) }}", {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: formData
        })
        .then(function(res) {
            return res.text().then(function(rawText) {
                var data = null;
                try {
                    data = JSON.parse(rawText);
                } catch (e) {
                    data = null;
                }
                return { ok: res.ok, status: res.status, data: data, rawText: rawText };
            });
        })
        .then(function(result) {
            if (!result.ok) {
                if (result.status === 419) {
                    showMcuStatus('error', '<span>⚠️ Sesi Anda telah kedaluwarsa. Silakan <a href="javascript:location.reload()" style="color:#38bdf8; text-decoration:underline; font-weight:600;">muat ulang halaman</a> lalu coba upload kembali.</span>');
                    return;
                }
                if (result.status === 413) {
                    showMcuStatus('error', '<span>❌ Ukuran file terlalu besar untuk diproses server.</span>');
                    return;
                }
                var errMsg = (result.data && result.data.message) ? result.data.message : ('Terjadi kesalahan pada server (HTTP ' + result.status + ').');
                showMcuStatus('error', '<span>❌ ' + escapeHtml(errMsg) + '</span>');
                return;
            }

            if (!result.data) {
                showMcuStatus('error', '<span>❌ Respon server tidak valid. Silakan coba kembali atau muat ulang halaman.</span>');
                return;
            }

            dismissMcuStatus();

            var resp = result.data;
            var pasien = resp.pasien || [];

            if (pasien.length === 0) {
                showMcuStatus('error', '<span>❌ File Excel tidak berisi data pasien.</span>');
                return;
            }

            stagingRows = pasien;
            stagingFileName = file.name;
            stagingCurrentPage = 1;
            stagingStatusFilter = 'all';
            stagingSearchQuery = '';

            document.getElementById('stagingFileName').textContent = file.name;

            // Alihkan tampilan modal dari manual form ke Staging Table
            var modalContent = document.getElementById('modalOrderContent');
            if (modalContent) modalContent.classList.add('modal-content-wide');

            var manualWrap = document.getElementById('manualOrderFormWrap');
            if (manualWrap) manualWrap.style.display = 'none';

            var stagingWrap = document.getElementById('stagingExcelWrap');
            if (stagingWrap) stagingWrap.style.display = 'flex';

            validateAllStagingRows();
            renderStagingTable();
        })
        .catch(function(err) {
            showMcuStatus('error', '<span>❌ Gagal membaca file: ' + escapeHtml(err.message || 'Kesalahan jaringan') + '</span>');
        })
        .finally(function() {
            if (uploadBtn) { uploadBtn.disabled = false; uploadBtn.style.opacity = '1'; }
            input.value = '';
        });
    }

    /* ─── Client-side Validation (Tahap 2 & Bagian 5 Aturan 1–6) ─── */
    function normalizeModality(mod) {
        if (!mod) return '';
        var lower = mod.trim().toLowerCase();
        for (var i = 0; i < ALLOWED_MODALITIES.length; i++) {
            if (ALLOWED_MODALITIES[i].toLowerCase() === lower) {
                return ALLOWED_MODALITIES[i];
            }
        }
        return mod.trim();
    }

    function validateRow(row, allRows) {
        var errors = {};

        // 1. Nama wajib, minimal 2 karakter
        var nama = (row.nama || '').trim();
        if (!nama || nama.length < 2) {
            errors.nama = 'Nama lengkap wajib diisi (minimal 2 karakter).';
        }

        // 2. NIK diisi tetapi bukan 16 digit angka
        var nik = (row.nik || '').toString().trim();
        if (nik !== '') {
            if (!/^[0-9]{16}$/.test(nik)) {
                errors.nik = 'NIK harus 16 digit angka.';
            }
        }

        // 3. Usia wajib, angka antara 0 - 120
        var usia = row.usia;
        if (usia === null || usia === undefined || usia === '' || isNaN(Number(usia))) {
            errors.usia = 'Usia wajib diisi angka antara 0 dan 120 tahun.';
        } else {
            var usiaNum = Number(usia);
            if (usiaNum < 0 || usiaNum > 120) {
                errors.usia = 'Usia harus antara 0 dan 120 tahun.';
            }
        }

        // 4. Jenis kelamin wajib L / P
        var jk = (row.jenis_kelamin || '').toString().trim().toUpperCase();
        if (jk === 'LAKI-LAKI' || jk === 'MALE' || jk === 'M') jk = 'L';
        if (jk === 'PEREMPUAN' || jk === 'FEMALE' || jk === 'F') jk = 'P';
        if (jk !== 'L' && jk !== 'P') {
            errors.jenis_kelamin = 'Jenis kelamin tidak dikenali. Pilih Laki-laki atau Perempuan.';
        } else {
            row.jenis_kelamin = jk;
        }

        // 5. Modalitas wajib terdaftar di protokol
        var mod = normalizeModality(row.modalitas);
        if (!mod) {
            errors.modalitas = 'Modalitas pemeriksaan wajib dipilih.';
        } else if (ALLOWED_MODALITIES.indexOf(mod) === -1) {
            errors.modalitas = 'Modalitas "' + mod + '" tidak dikenali dalam daftar protokol.';
        } else {
            row.modalitas = mod;
        }

        // 6. Duplikat di dalam file: NIK sama dan modalitas sama dengan baris lain
        if (nik !== '' && row.modalitas) {
            var key = nik + '|' + row.modalitas.toLowerCase();
            var count = 0;
            for (var i = 0; i < allRows.length; i++) {
                var otherNik = (allRows[i].nik || '').toString().trim();
                var otherMod = (allRows[i].modalitas || '').toString().trim().toLowerCase();
                if (otherNik !== '' && otherMod !== '') {
                    if (otherNik + '|' + otherMod === key) {
                        count++;
                    }
                }
            }
            if (count > 1) {
                var dupMsg = 'Duplikat NIK dan modalitas di file.';
                errors.nik = errors.nik || dupMsg;
                errors.modalitas = errors.modalitas || dupMsg;
            }
        }

        var isReady = Object.keys(errors).length === 0;
        row.status = isReady ? 'Siap' : 'Perlu dicek';
        row.errors = errors;
        return isReady;
    }

    function validateAllStagingRows() {
        var total = stagingRows.length;
        var ready = 0;
        var invalid = 0;
        var selected = 0;
        var hasInvalidSelected = false;

        for (var i = 0; i < stagingRows.length; i++) {
            var isReady = validateRow(stagingRows[i], stagingRows);
            if (isReady) ready++; else invalid++;
            if (stagingRows[i].dipilih) {
                selected++;
                if (!isReady) hasInvalidSelected = true;
            }
        }

        // Update header summary text
        var summaryTextEl = document.getElementById('stagingSummaryText');
        if (summaryTextEl) {
            summaryTextEl.innerHTML = 'Ditemukan <strong>' + total + ' pasien</strong> (<span style="color: #34d399; font-weight: 700;">' + ready + ' siap</span>, <span style="color: #fbbf24; font-weight: 700;">' + invalid + ' perlu dicek</span>)';
        }

        // Update tab counters
        var elTabAll = document.getElementById('countTabAll');
        var elTabReady = document.getElementById('countTabReady');
        var elTabInvalid = document.getElementById('countTabInvalid');
        if (elTabAll) elTabAll.textContent = total;
        if (elTabReady) elTabReady.textContent = ready;
        if (elTabInvalid) elTabInvalid.textContent = invalid;

        // Update selected info in footer
        var selectedInfoEl = document.getElementById('stagingSelectedCountInfo');
        if (selectedInfoEl) {
            selectedInfoEl.textContent = selected + ' dipilih dari ' + total + ' pasien';
        }

        // Update Kirim button state
        var submitBtn = document.getElementById('btnSubmitStaging');
        if (submitBtn) {
            submitBtn.textContent = 'Kirim ' + selected + ' Pasien ke Worklist';
            submitBtn.disabled = (selected === 0 || hasInvalidSelected);
        }

        // Update delete selected button state
        var btnDelSelected = document.getElementById('btnDeleteSelected');
        if (btnDelSelected) {
            btnDelSelected.disabled = (selected === 0);
            btnDelSelected.style.opacity = selected === 0 ? '0.5' : '1';
            btnDelSelected.style.cursor = selected === 0 ? 'not-allowed' : 'pointer';
        }
    }

    /* ─── Filtering & Search (Tahap 3) ─── */
    function setStagingStatusFilter(filter) {
        stagingStatusFilter = filter;
        stagingCurrentPage = 1;

        var tabs = document.querySelectorAll('.staging-tab');
        tabs.forEach(function(t) {
            t.classList.toggle('active', t.getAttribute('data-filter') === filter);
        });

        renderStagingTable();
    }

    function handleStagingSearch(query) {
        stagingSearchQuery = (query || '').toLowerCase().trim();
        stagingCurrentPage = 1;
        renderStagingTable();
    }

    function getFilteredStagingRows() {
        return stagingRows.filter(function(r) {
            // Filter status tab
            if (stagingStatusFilter === 'ready' && r.status !== 'Siap') return false;
            if (stagingStatusFilter === 'invalid' && r.status !== 'Perlu dicek') return false;

            // Filter search query
            if (stagingSearchQuery) {
                var searchTarget = (
                    (r.nama || '') + ' ' +
                    (r.nik || '') + ' ' +
                    (r.modalitas || '') + ' ' +
                    (r.indikasi || '') + ' ' +
                    (r.catatan || '')
                ).toLowerCase();

                if (searchTarget.indexOf(stagingSearchQuery) === -1) return false;
            }

            return true;
        });
    }

    /* ─── Render Table & Pagination (Tahap 3 & 4) ─── */
    function renderStagingTable() {
        var tbody = document.getElementById('stagingTableBody');
        if (!tbody) return;

        var filtered = getFilteredStagingRows();
        var totalPages = Math.max(1, Math.ceil(filtered.length / stagingRowsPerPage));

        if (stagingCurrentPage > totalPages) stagingCurrentPage = totalPages;
        if (stagingCurrentPage < 1) stagingCurrentPage = 1;

        var startIdx = (stagingCurrentPage - 1) * stagingRowsPerPage;
        var endIdx = Math.min(startIdx + stagingRowsPerPage, filtered.length);
        var pageRows = filtered.slice(startIdx, endIdx);

        tbody.innerHTML = '';

        if (pageRows.length === 0) {
            var emptyTr = document.createElement('tr');
            emptyTr.innerHTML = '<td colspan="11" style="text-align: center; padding: 2.5rem 1rem; color: #94a3b8; font-size: 0.85rem;">' +
                'Tidak ada data pasien yang sesuai kriteria pencarian / filter.</td>';
            tbody.appendChild(emptyTr);
        } else {
            pageRows.forEach(function(row, idx) {
                var globalNum = startIdx + idx + 1;
                var tr = document.createElement('tr');
                if (row.status === 'Perlu dicek') {
                    tr.classList.add('staging-row-invalid');
                }

                // Checkbox
                var tdCheck = document.createElement('td');
                tdCheck.style.textAlign = 'center';
                var chk = document.createElement('input');
                chk.type = 'checkbox';
                chk.checked = !!row.dipilih;
                chk.title = row.status === 'Perlu dicek' ? 'Baris ini masih Perlu Dicek' : 'Centang untuk dikirim';
                chk.addEventListener('change', function(e) {
                    toggleRowSelection(row.id, e.target.checked);
                });
                tdCheck.appendChild(chk);
                tr.appendChild(tdCheck);

                // No
                var tdNo = document.createElement('td');
                tdNo.style.textAlign = 'center';
                tdNo.style.color = '#94a3b8';
                tdNo.textContent = globalNum;
                tr.appendChild(tdNo);

                // Status Badge
                var tdStatus = document.createElement('td');
                var statusBadge = document.createElement('span');
                statusBadge.className = 'staging-status-badge ' + (row.status === 'Siap' ? 'staging-status-ready' : 'staging-status-invalid');
                if (row.status === 'Siap') {
                    statusBadge.innerHTML = '● Siap';
                } else {
                    var errList = Object.values(row.errors || {}).join(' | ');
                    statusBadge.innerHTML = '⚠️ Perlu dicek';
                    statusBadge.title = errList;
                }
                tdStatus.appendChild(statusBadge);
                tr.appendChild(tdStatus);

                // Nama Lengkap *
                tr.appendChild(createCell(row, 'nama', row.nama, true));

                // No. RM / NIK
                tr.appendChild(createCell(row, 'nik', row.nik, false, true));

                // Usia *
                tr.appendChild(createCell(row, 'usia', row.usia !== null ? row.usia : '', true));

                // Jenis Kelamin *
                tr.appendChild(createCell(row, 'jenis_kelamin', row.jenis_kelamin, true));

                // Modalitas *
                tr.appendChild(createCell(row, 'modalitas', row.modalitas, true));

                // Indikasi Klinis
                tr.appendChild(createCell(row, 'indikasi', row.indikasi, false));

                // Catatan
                tr.appendChild(createCell(row, 'catatan', row.catatan, false));

                // Aksi (Edit & Delete)
                var tdAksi = document.createElement('td');
                tdAksi.style.textAlign = 'center';
                tdAksi.style.whiteSpace = 'nowrap';

                var btnEdit = document.createElement('button');
                btnEdit.type = 'button';
                btnEdit.className = 'btn-action-icon btn-action-edit';
                btnEdit.title = 'Ubah data pasien lengkap';
                btnEdit.innerHTML = '✏️';
                btnEdit.onclick = function() { openEditStagingModal(row.id); };

                var btnDel = document.createElement('button');
                btnDel.type = 'button';
                btnDel.className = 'btn-action-icon btn-action-delete';
                btnDel.title = 'Hapus dari staging';
                btnDel.innerHTML = '🗑️';
                btnDel.onclick = function() { deleteStagingRow(row.id); };

                tdAksi.appendChild(btnEdit);
                tdAksi.appendChild(btnDel);
                tr.appendChild(tdAksi);

                tbody.appendChild(tr);
            });
        }

        // Update master checkbox
        updateMasterCheckboxState(pageRows);

        // Update Pagination Info & Links
        renderPagination(filtered.length, totalPages, startIdx, endIdx);
    }

    function createCell(row, field, value, required, isCode) {
        var td = document.createElement('td');
        var err = row.errors && row.errors[field];

        var cellWrap = document.createElement('div');
        cellWrap.className = 'staging-cell' + (err ? ' staging-cell-error' : '');
        cellWrap.setAttribute('data-row-id', row.id);
        cellWrap.setAttribute('data-field', field);
        if (err) cellWrap.title = err;

        var valSpan = document.createElement('span');
        valSpan.style.overflow = 'hidden';
        valSpan.style.textOverflow = 'ellipsis';
        valSpan.style.whiteSpace = 'nowrap';
        if (isCode) {
            valSpan.style.fontFamily = "'JetBrains Mono', monospace";
            valSpan.style.fontSize = "0.74rem";
        }

        var displayVal = (value !== null && value !== undefined && value !== '') ? value : (required ? '<span style="color: #f87171; font-style: italic;">[Kosong]</span>' : '—');
        valSpan.innerHTML = (typeof displayVal === 'string' && displayVal.indexOf('<') !== -1) ? displayVal : escapeHtml(displayVal);
        cellWrap.appendChild(valSpan);

        if (err) {
            var errIcon = document.createElement('span');
            errIcon.className = 'staging-error-badge';
            errIcon.textContent = '⚠';
            cellWrap.appendChild(errIcon);
        }

        cellWrap.addEventListener('click', function(e) {
            if (activeInlineEdit && activeInlineEdit.rowId === row.id && activeInlineEdit.field === field) return;
            startInlineEdit(cellWrap, row.id, field);
        });

        td.appendChild(cellWrap);
        return td;
    }

    /* ─── Inline Cell Editing (Tahap 3 & 4) ─── */
    function startInlineEdit(cellEl, rowId, field) {
        var row = stagingRows.find(function(r) { return r.id === rowId; });
        if (!row) return;

        activeInlineEdit = { rowId: rowId, field: field };
        cellEl.innerHTML = '';

        var currentVal = row[field];
        if (currentVal === null || currentVal === undefined) currentVal = '';

        var inputEl;

        if (field === 'jenis_kelamin') {
            inputEl = document.createElement('select');
            inputEl.className = 'staging-search-input';
            inputEl.style.fontSize = '0.75rem';
            inputEl.style.padding = '0.2rem 0.35rem';
            inputEl.style.height = '26px';

            var optL = document.createElement('option');
            optL.value = 'L'; optL.textContent = 'Laki-laki (L)';
            var optP = document.createElement('option');
            optP.value = 'P'; optP.textContent = 'Perempuan (P)';
            inputEl.appendChild(optL);
            inputEl.appendChild(optP);
            inputEl.value = (row.jenis_kelamin === 'P') ? 'P' : 'L';
        } else if (field === 'modalitas') {
            inputEl = document.createElement('select');
            inputEl.className = 'staging-search-input';
            inputEl.style.fontSize = '0.75rem';
            inputEl.style.padding = '0.2rem 0.35rem';
            inputEl.style.height = '26px';

            ALLOWED_MODALITIES.forEach(function(m) {
                var opt = document.createElement('option');
                opt.value = m;
                opt.textContent = m;
                inputEl.appendChild(opt);
            });
            inputEl.value = normalizeModality(row.modalitas) || ALLOWED_MODALITIES[0];
        } else if (field === 'usia') {
            inputEl = document.createElement('input');
            inputEl.type = 'number';
            inputEl.min = '0';
            inputEl.max = '120';
            inputEl.className = 'staging-search-input';
            inputEl.style.fontSize = '0.75rem';
            inputEl.style.padding = '0.2rem 0.4rem';
            inputEl.style.height = '26px';
            inputEl.value = currentVal;
        } else {
            inputEl = document.createElement('input');
            inputEl.type = 'text';
            inputEl.className = 'staging-search-input';
            inputEl.style.fontSize = '0.75rem';
            inputEl.style.padding = '0.2rem 0.4rem';
            inputEl.style.height = '26px';
            inputEl.value = currentVal;
        }

        cellEl.appendChild(inputEl);
        inputEl.focus();
        if (inputEl.select && typeof inputEl.select === 'function') inputEl.select();

        var committed = false;
        function commitValue() {
            if (committed) return;
            committed = true;
            activeInlineEdit = null;

            var newVal = inputEl.value;
            if (field === 'usia') {
                row.usia = (newVal !== '' && !isNaN(Number(newVal))) ? parseInt(newVal, 10) : null;
            } else if (field === 'nik' || field === 'nama' || field === 'indikasi' || field === 'catatan') {
                row[field] = newVal.trim();
            } else {
                row[field] = newVal;
            }

            validateAllStagingRows();
            renderStagingTable();
        }

        function cancelEdit() {
            if (committed) return;
            committed = true;
            activeInlineEdit = null;
            renderStagingTable();
        }

        inputEl.addEventListener('blur', function() {
            commitValue();
        });

        inputEl.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                commitValue();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelEdit();
            }
        });
    }

    /* ─── Checkbox Selections (Tahap 3) ─── */
    function toggleRowSelection(rowId, checked) {
        var row = stagingRows.find(function(r) { return r.id === rowId; });
        if (row) {
            row.dipilih = checked;
            validateAllStagingRows();
            var filtered = getFilteredStagingRows();
            var startIdx = (stagingCurrentPage - 1) * stagingRowsPerPage;
            var endIdx = Math.min(startIdx + stagingRowsPerPage, filtered.length);
            updateMasterCheckboxState(filtered.slice(startIdx, endIdx));
        }
    }

    function toggleSelectAllStaging(masterChecked) {
        var filtered = getFilteredStagingRows();
        filtered.forEach(function(r) {
            if (masterChecked) {
                // Sesuai Tahap 3: hanya mencentang baris Siap
                if (r.status === 'Siap') {
                    r.dipilih = true;
                }
            } else {
                r.dipilih = false;
            }
        });

        validateAllStagingRows();
        renderStagingTable();
    }

    function updateMasterCheckboxState(pageRows) {
        var masterChk = document.getElementById('stagingMasterCheckbox');
        if (!masterChk) return;

        var readyRows = pageRows.filter(function(r) { return r.status === 'Siap'; });
        if (readyRows.length === 0) {
            masterChk.checked = false;
            masterChk.indeterminate = false;
            return;
        }

        var selectedCount = readyRows.filter(function(r) { return r.dipilih; }).length;
        if (selectedCount === readyRows.length) {
            masterChk.checked = true;
            masterChk.indeterminate = false;
        } else if (selectedCount > 0) {
            masterChk.checked = false;
            masterChk.indeterminate = true;
        } else {
            masterChk.checked = false;
            masterChk.indeterminate = false;
        }
    }

    /* ─── Pagination Renderer (Tahap 3) ─── */
    function renderPagination(totalFiltered, totalPages, startIdx, endIdx) {
        var infoEl = document.getElementById('stagingPaginationInfo');
        var linksEl = document.getElementById('stagingPaginationLinks');
        if (!infoEl || !linksEl) return;

        var showingText = totalFiltered === 0
            ? 'Menampilkan 0 dari 0 pasien'
            : ('Menampilkan ' + (startIdx + 1) + '–' + endIdx + ' dari ' + totalFiltered + ' pasien' +
               (totalFiltered !== stagingRows.length ? ' (difilter dari ' + stagingRows.length + ' total)' : ''));
        infoEl.textContent = showingText;

        linksEl.innerHTML = '';
        if (totalPages <= 1) return;

        // Button: Previous
        var prevBtn = document.createElement(stagingCurrentPage > 1 ? 'a' : 'span');
        prevBtn.innerHTML = '‹';
        if (stagingCurrentPage > 1) {
            prevBtn.href = 'javascript:void(0)';
            prevBtn.onclick = function() { stagingCurrentPage--; renderStagingTable(); };
        } else {
            prevBtn.className = 'disabled';
        }
        linksEl.appendChild(prevBtn);

        // Page numbers (windowing)
        var startPage = Math.max(1, stagingCurrentPage - 2);
        var endPage = Math.min(totalPages, startPage + 4);
        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }

        for (var p = startPage; p <= endPage; p++) {
            (function(pageNum) {
                if (pageNum === stagingCurrentPage) {
                    var cur = document.createElement('span');
                    cur.className = 'current';
                    cur.textContent = pageNum;
                    linksEl.appendChild(cur);
                } else {
                    var a = document.createElement('a');
                    a.href = 'javascript:void(0)';
                    a.textContent = pageNum;
                    a.onclick = function() { stagingCurrentPage = pageNum; renderStagingTable(); };
                    linksEl.appendChild(a);
                }
            })(p);
        }

        // Button: Next
        var nextBtn = document.createElement(stagingCurrentPage < totalPages ? 'a' : 'span');
        nextBtn.innerHTML = '›';
        if (stagingCurrentPage < totalPages) {
            nextBtn.href = 'javascript:void(0)';
            nextBtn.onclick = function() { stagingCurrentPage++; renderStagingTable(); };
        } else {
            nextBtn.className = 'disabled';
        }
        linksEl.appendChild(nextBtn);
    }

    /* ─── CRUD: Create, Edit Submodal, Delete & Undo (Tahap 4) ─── */
    function addStagingRow() {
        var newId = 'row-new-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
        var newRow = {
            id: newId,
            nama: '',
            nik: '',
            usia: null,
            jenis_kelamin: 'L',
            modalitas: '',
            indikasi: '',
            catatan: '',
            tanggal_pemeriksaan: new Date().toISOString().slice(0, 10),
            dipilih: false,
            status: 'Perlu dicek',
            errors: {
                nama: 'Nama lengkap wajib diisi (minimal 2 karakter).',
                modalitas: 'Modalitas pemeriksaan wajib dipilih.'
            }
        };

        // Tambah di baris paling atas halaman 1 (Tahap 4)
        stagingRows.unshift(newRow);
        stagingCurrentPage = 1;
        stagingStatusFilter = 'all';

        validateAllStagingRows();
        renderStagingTable();

        // Masuk mode edit pada kolom nama
        setTimeout(function() {
            var cell = document.querySelector('[data-row-id="' + newId + '"][data-field="nama"]');
            if (cell) startInlineEdit(cell, newId, 'nama');
        }, 60);
    }

    function openEditStagingModal(rowId) {
        var row = stagingRows.find(function(r) { return r.id === rowId; });
        if (!row) return;

        document.getElementById('editStagingRowId').value = row.id;
        document.getElementById('editStagingNama').value = row.nama || '';
        document.getElementById('editStagingNik').value = row.nik || '';
        document.getElementById('editStagingUsia').value = (row.usia !== null && row.usia !== undefined) ? row.usia : '';
        document.getElementById('editStagingGender').value = (row.jenis_kelamin === 'P') ? 'P' : 'L';
        document.getElementById('editStagingModality').value = normalizeModality(row.modalitas) || ALLOWED_MODALITIES[0];
        document.getElementById('editStagingIndikasi').value = row.indikasi || '';
        document.getElementById('editStagingCatatan').value = row.catatan || '';

        document.getElementById('modalEditStagingPatient').style.display = 'flex';
    }

    function closeEditStagingModal() {
        document.getElementById('modalEditStagingPatient').style.display = 'none';
    }

    function saveEditStagingModal() {
        var rowId = document.getElementById('editStagingRowId').value;
        var row = stagingRows.find(function(r) { return r.id === rowId; });
        if (!row) return;

        var nama = document.getElementById('editStagingNama').value.trim();
        var nik = document.getElementById('editStagingNik').value.trim();
        var usiaStr = document.getElementById('editStagingUsia').value;
        var jk = document.getElementById('editStagingGender').value;
        var mod = document.getElementById('editStagingModality').value;
        var indikasi = document.getElementById('editStagingIndikasi').value.trim();
        var catatan = document.getElementById('editStagingCatatan').value.trim();

        row.nama = nama;
        row.nik = nik;
        row.usia = (usiaStr !== '' && !isNaN(Number(usiaStr))) ? parseInt(usiaStr, 10) : null;
        row.jenis_kelamin = jk;
        row.modalitas = mod;
        row.indikasi = indikasi;
        row.catatan = catatan;

        closeEditStagingModal();
        validateAllStagingRows();
        renderStagingTable();
    }

    function deleteStagingRow(rowId) {
        var idx = stagingRows.findIndex(function(r) { return r.id === rowId; });
        if (idx === -1) return;

        var deleted = stagingRows.splice(idx, 1)[0];
        recentlyDeletedRows = [deleted];

        showStagingUndo('🗑️ 1 pasien dihapus dari staging.');
        validateAllStagingRows();
        renderStagingTable();
    }

    function deleteSelectedStagingRows() {
        var selected = stagingRows.filter(function(r) { return r.dipilih; });
        if (selected.length === 0) return;

        if (!confirm('Hapus ' + selected.length + ' pasien yang dicentang dari tabel staging? (Data di database tidak terpengaruh)')) {
            return;
        }

        recentlyDeletedRows = selected;
        stagingRows = stagingRows.filter(function(r) { return !r.dipilih; });

        showStagingUndo('🗑️ ' + selected.length + ' pasien dihapus dari staging.');
        validateAllStagingRows();
        renderStagingTable();
    }

    function showStagingUndo(message) {
        var toast = document.getElementById('stagingUndoToast');
        var msgEl = document.getElementById('stagingUndoMessage');
        if (!toast || !msgEl) return;

        msgEl.textContent = message;
        toast.style.display = 'flex';

        if (stagingUndoTimeout) clearTimeout(stagingUndoTimeout);
        stagingUndoTimeout = setTimeout(function() {
            dismissStagingUndo();
        }, 5000); // 5 detik sesuai aturan Tahap 4
    }

    function dismissStagingUndo() {
        var toast = document.getElementById('stagingUndoToast');
        if (toast) toast.style.display = 'none';
        if (stagingUndoTimeout) {
            clearTimeout(stagingUndoTimeout);
            stagingUndoTimeout = null;
        }
    }

    function undoStagingDelete() {
        if (!recentlyDeletedRows || recentlyDeletedRows.length === 0) return;

        recentlyDeletedRows.forEach(function(r) {
            stagingRows.unshift(r);
        });

        recentlyDeletedRows = [];
        dismissStagingUndo();

        validateAllStagingRows();
        renderStagingTable();
    }

    /* ─── Submit Batch to Worklist (Tahap 5 & 6) ─── */
    function submitStagingToWorklist() {
        var selectedRows = stagingRows.filter(function(r) { return r.dipilih; });
        if (selectedRows.length === 0) {
            alert('Tidak ada pasien yang dipilih untuk dikirim.');
            return;
        }

        var invalidSelected = selectedRows.filter(function(r) { return r.status === 'Perlu dicek'; });
        if (invalidSelected.length > 0) {
            alert('Ada ' + invalidSelected.length + ' pasien yang dicentang masih berstatus "Perlu dicek". Perbaiki atau hilangkan centang terlebih dahulu.');
            return;
        }

        var submitBtn = document.getElementById('btnSubmitStaging');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ Mengirim ' + selectedRows.length + ' Pasien ke Worklist...';
        }

        var payload = selectedRows.map(function(r) {
            return {
                id: r.id,
                nama: r.nama,
                nik: r.nik ? r.nik : null,
                usia: r.usia,
                jenis_kelamin: r.jenis_kelamin,
                modalitas: r.modalitas,
                indikasi: r.indikasi ? r.indikasi : null,
                catatan: r.catatan ? r.catatan : null,
                tanggal_pemeriksaan: r.tanggal_pemeriksaan || new Date().toISOString().slice(0, 10)
            };
        });

        fetch("{{ route('worklist.import', [], false) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                _token: getCsrfToken(),
                pasien: payload
            })
        })
        .then(function(res) {
            return res.text().then(function(rawText) {
                var data = null;
                try {
                    data = JSON.parse(rawText);
                } catch (e) {
                    data = null;
                }
                return { ok: res.ok, status: res.status, data: data };
            });
        })
        .then(function(result) {
            if (result.ok && result.data) {
                // Sukses HTTP 200
                alert(result.data.message || (selectedRows.length + ' pasien berhasil dikirim ke worklist.'));
                closeOrderModal();
                refreshMainWorklist();
            } else if (result.status === 419) {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Kirim ' + selectedRows.length + ' Pasien ke Worklist';
                }
                alert('⚠️ Sesi Anda telah kedaluwarsa. Data Anda tetap aman di browser. Silakan muat ulang halaman atau coba kirim kembali.');
            } else if (result.status === 422 && result.data) {
                // Gagal validasi HTTP 422: Petakan kembali ke baris yang bersangkutan
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Kirim ' + selectedRows.length + ' Pasien ke Worklist';
                }

                var errs = result.data.errors || {};
                var firstErrorRowId = null;

                for (var key in errs) {
                    var match = key.match(/^pasien\.(\d+)\.(.+)$/);
                    if (match) {
                        var pIdx = parseInt(match[1], 10);
                        var pField = match[2];
                        if (selectedRows[pIdx]) {
                            var targetRow = selectedRows[pIdx];
                            targetRow.status = 'Perlu dicek';
                            targetRow.errors = targetRow.errors || {};
                            targetRow.errors[pField] = errs[key][0];
                            if (!firstErrorRowId) firstErrorRowId = targetRow.id;
                        }
                    }
                }

                validateAllStagingRows();

                // Bawa user ke halaman tabel yang berisi baris bermasalah pertama
                if (firstErrorRowId) {
                    var filtered = getFilteredStagingRows();
                    var foundIdx = filtered.findIndex(function(r) { return r.id === firstErrorRowId; });
                    if (foundIdx !== -1) {
                        stagingCurrentPage = Math.floor(foundIdx / stagingRowsPerPage) + 1;
                    }
                }

                renderStagingTable();
                alert('⚠️ Ada data yang ditolak oleh server:\n' + (result.data.message || 'Silakan cek baris bertanda kuning di tabel staging.'));
            } else {
                // Gagal server 500 / respon tak terduga
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Kirim ' + selectedRows.length + ' Pasien ke Worklist';
                }
                var serverMsg = (result.data && result.data.message) ? ('\nPesan: ' + result.data.message) : (' (HTTP ' + result.status + ')');
                alert('❌ Gagal mengirim data ke worklist' + serverMsg + '. Data Anda masih aman di tabel. Silakan coba lagi.');
            }
        })
        .catch(function(err) {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Kirim ' + selectedRows.length + ' Pasien ke Worklist';
            }
            alert('❌ Gagal menghubungi server: ' + (err.message || 'Kesalahan jaringan') + '. Data Anda masih aman di tabel.');
        });
    }

    /* ─── Refresh Main Worklist Table & KPI Stats (Tahap 6) ─── */
    function refreshMainWorklist() {
        fetch(window.location.href)
        .then(function(res) { return res.text(); })
        .then(function(html) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');

            var newStats = doc.getElementById('statsGridWrap');
            var oldStats = document.getElementById('statsGridWrap');
            if (newStats && oldStats) oldStats.innerHTML = newStats.innerHTML;

            var newTable = doc.getElementById('worklistTableWrap');
            var oldTable = document.getElementById('worklistTableWrap');
            if (newTable && oldTable) oldTable.innerHTML = newTable.innerHTML;

            var newTabs = doc.querySelector('.filter-tabs');
            var oldTabs = document.querySelector('.filter-tabs');
            if (newTabs && oldTabs) oldTabs.innerHTML = newTabs.innerHTML;
        })
        .catch(function() {
            window.location.reload();
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    /* ─── Warning Sebelum Keluar Tab Jika Ada Data Staging (Tahap 7) ─── */
    window.addEventListener('beforeunload', function(e) {
        if (stagingRows && stagingRows.length > 0) {
            e.preventDefault();
            e.returnValue = 'Data pasien di tabel staging yang belum dikirim akan hilang.';
        }
    });

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
        const modalOrder = document.getElementById('modalOrder');
        const modalDicom = document.getElementById('modalDicomConfig');
        const modalCstore = document.getElementById('modalCstoreResult');
        const modalDelete = document.getElementById('modalConfirmDelete');
        const modalEditStaging = document.getElementById('modalEditStagingPatient');
        if (event.target === modalOrder) confirmCloseOrderModal();
        if (event.target === modalDicom) closeDicomModal();
        if (event.target === modalCstore) closeCstoreModal();
        if (event.target === modalDelete) closeDeleteModal();
        if (event.target === modalEditStaging) closeEditStagingModal();
    };

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modalEditStaging = document.getElementById('modalEditStagingPatient');
            if (modalEditStaging && modalEditStaging.style.display === 'flex') {
                closeEditStagingModal();
                return;
            }
            confirmCloseOrderModal();
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
</script>

</body>
</html>

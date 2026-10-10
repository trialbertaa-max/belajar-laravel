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
    <div class="modal-content">
        <div class="modal-header">
            <div style="font-weight: 700; font-size: 1.05rem;">➕ Buat Sesi / Order MCU Baru (Hyu)</div>
            <button onclick="closeOrderModal()" style="background: none; border: none; color: var(--text-muted); font-size: 1.25rem; cursor: pointer;">&times;</button>
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
                    <label class="form-label">Modalitas Pemeriksaan *</label>
                    <select name="modality" class="form-control" required>
                        <option value="Thorax PA">Thorax PA (Rontgen Dada)</option>
                        <option value="Thorax AP">Thorax AP</option>
                        <option value="Thorax Lateral">Thorax Lateral</option>
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
        const modalOrder = document.getElementById('modalOrder');
        const modalDicom = document.getElementById('modalDicomConfig');
        const modalCstore = document.getElementById('modalCstoreResult');
        const modalDelete = document.getElementById('modalConfirmDelete');
        if (event.target === modalOrder) closeOrderModal();
        if (event.target === modalDicom) closeDicomModal();
        if (event.target === modalCstore) closeCstoreModal();
        if (event.target === modalDelete) closeDeleteModal();
    };

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
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
</script>

</body>
</html>

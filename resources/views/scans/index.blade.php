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
                            <div style="display: flex; gap: 0.4rem; align-items: center;">
                                <a href="{{ route('scans.show', $scan->id) }}" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.78rem;">
                                    🔍 Viewer
                                </a>
                                <form action="{{ route('scans.destroy', $scan->id) }}" method="POST" onsubmit="return confirm('Hapus data pemeriksaan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary" style="padding: 0.35rem 0.55rem; font-size: 0.78rem; color: #ef4444;">
                                        🗑️
                                    </button>
                                </form>
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

    window.onclick = function(event) {
        const modalOrder = document.getElementById('modalOrder');
        const modalDicom = document.getElementById('modalDicomConfig');
        if (event.target === modalOrder) closeOrderModal();
        if (event.target === modalDicom) closeDicomModal();
    };

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
                alert("✅ " + data.message + "\nAE Title: " + data.ae_title + " | Port: " + data.port);
                closeDicomModal();
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
        .then(res => res.json())
        .then(data => {
            alert("✅ " + data.message);
            window.location.reload();
        })
        .catch(err => {
            alert("❌ Gagal simulasi: " + err.message);
        })
        .finally(() => {
            btn.innerHTML = origText;
            btn.disabled = false;
        });
    }
</script>

</body>
</html>

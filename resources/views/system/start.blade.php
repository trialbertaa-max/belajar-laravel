<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hyu — System Readiness & Control Center</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-base: #090d16;
            --bg-card: #0f172a;
            --bg-card-hover: #131e36;
            --border-card: rgba(56, 189, 248, 0.2);
            --border-card-active: rgba(56, 189, 248, 0.5);
            --primary: #38bdf8;
            --primary-glow: rgba(56, 189, 248, 0.25);
            --accent-green: #10b981;
            --accent-green-glow: rgba(16, 185, 129, 0.25);
            --accent-amber: #f59e0b;
            --accent-red: #ef4444;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-dim: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1.5rem 3rem 1.5rem;
            background-image: 
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(2, 132, 199, 0.15), transparent),
                radial-gradient(circle at 10% 90%, rgba(16, 185, 129, 0.05), transparent);
            background-attachment: fixed;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Top Header & Branding */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            padding-bottom: 1.75rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-badge {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 1.15rem;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            box-shadow: 0 0 16px rgba(2, 132, 199, 0.35);
            letter-spacing: -0.01em;
        }

        .brand-title {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand-subtitle {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-top: 0.2rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-cta-launch {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: linear-gradient(135deg, #0284c7, #0284c7 50%, #0369a1);
            color: #ffffff;
            padding: 0.7rem 1.4rem;
            border-radius: 8px;
            font-size: 0.92rem;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid rgba(56, 189, 248, 0.5);
            box-shadow: 0 4px 15px rgba(2, 132, 199, 0.35), 0 0 25px rgba(56, 189, 248, 0.2);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-cta-launch:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.5), 0 0 35px rgba(56, 189, 248, 0.35);
            background: linear-gradient(135deg, #0369a1, #0284c7);
        }

        .btn-refresh {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: rgba(30, 41, 59, 0.75);
            border: 1px solid rgba(148, 163, 184, 0.25);
            color: #cbd5e1;
            padding: 0.7rem 1.1rem;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-refresh:hover {
            background: rgba(51, 65, 85, 0.9);
            color: #ffffff;
            border-color: rgba(56, 189, 248, 0.4);
        }

        /* Hero Banner / Score */
        .readiness-hero {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 41, 59, 0.85));
            border: 1px solid var(--border-card);
            border-radius: 12px;
            padding: 1.5rem 1.75rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .readiness-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #38bdf8, #10b981, #38bdf8);
        }

        .hero-left h1 {
            font-size: 1.35rem;
            font-weight: 800;
            color: #f8fafc;
            margin-bottom: 0.35rem;
        }

        .hero-left p {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .hero-score-badge {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(2, 6, 23, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
        }

        .score-circle {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.75rem;
            font-weight: 800;
            color: #38bdf8;
        }

        .score-details {
            text-align: right;
        }

        .score-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-dim);
            letter-spacing: 0.05em;
        }

        .score-status-text {
            font-size: 0.9rem;
            font-weight: 700;
            color: #34d399;
        }

        /* Highlighted Star Card: DICOM Server */
        .dicom-server-card {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.98), rgba(17, 30, 56, 0.95));
            border: 1px solid rgba(56, 189, 248, 0.45);
            border-radius: 12px;
            padding: 1.75rem;
            margin-bottom: 2rem;
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.7), 0 0 30px rgba(2, 132, 199, 0.15);
            position: relative;
        }

        .dicom-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .dicom-title-group {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .dicom-icon {
            font-size: 1.75rem;
            background: rgba(2, 132, 199, 0.2);
            border: 1px solid rgba(56, 189, 248, 0.35);
            border-radius: 10px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dicom-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
        }

        .dicom-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }

        /* Animated Pulsing Status Badge */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 0.45rem 0.95rem;
            border-radius: 9999px;
            letter-spacing: 0.02em;
        }

        .status-pill-active {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #34d399;
        }

        .status-pill-inactive {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #f87171;
        }

        .pulse-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background-color: currentColor;
            display: inline-block;
            box-shadow: 0 0 10px currentColor;
            animation: pulseAnim 1.8s infinite;
        }

        @keyframes pulseAnim {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        /* Toggle Switch UI */
        .dicom-toggle-wrap {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            margin: 1.5rem 0;
            background: rgba(2, 6, 23, 0.6);
            padding: 1rem 1.25rem;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            flex-wrap: wrap;
        }

        .toggle-switch-container {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .toggle-btn {
            position: relative;
            width: 72px;
            height: 38px;
            background: #334155;
            border-radius: 9999px;
            border: 2px solid rgba(148, 163, 184, 0.3);
            cursor: pointer;
            transition: all 0.25s ease;
            outline: none;
        }

        .toggle-btn.active {
            background: #0284c7;
            border-color: #38bdf8;
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.4);
        }

        .toggle-slider {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 28px;
            height: 28px;
            background: #ffffff;
            border-radius: 50%;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
        }

        .toggle-btn.active .toggle-slider {
            transform: translateX(34px);
            background: #ffffff;
        }

        .toggle-label {
            font-size: 0.95rem;
            font-weight: 700;
            color: #f1f5f9;
        }

        .dicom-meta-grid {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-left: auto;
        }

        .meta-item {
            font-size: 0.78rem;
        }

        .meta-label {
            color: var(--text-dim);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.04em;
            margin-bottom: 0.15rem;
        }

        .meta-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.88rem;
            font-weight: 700;
            color: #38bdf8;
        }

        /* Action Buttons Row */
        .dicom-actions-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .btn-sub-action {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: rgba(30, 41, 59, 0.65);
            border: 1px solid rgba(148, 163, 184, 0.25);
            color: #cbd5e1;
            padding: 0.5rem 0.95rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-sub-action:hover {
            background: rgba(51, 65, 85, 0.9);
            color: #ffffff;
            border-color: rgba(56, 189, 248, 0.4);
        }

        .btn-sub-action.primary-sub {
            background: rgba(2, 132, 199, 0.2);
            border-color: rgba(56, 189, 248, 0.45);
            color: #38bdf8;
        }

        .btn-sub-action.primary-sub:hover {
            background: rgba(2, 132, 199, 0.35);
        }

        /* Diagnostic Grid (3 Cards) */
        .subsystems-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .sub-card {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 12px;
            padding: 1.35rem;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
        }

        .sub-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sub-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #f8fafc;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sub-card-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
        }

        .badge-ready {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        .badge-warn {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.35);
        }

        .check-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .check-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            padding: 0.35rem 0;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.04);
        }

        .check-item:last-child {
            border-bottom: none;
        }

        .check-name {
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        .check-val {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            color: #f1f5f9;
        }

        .val-ok {
            color: #34d399;
        }

        .val-warn {
            color: #fbbf24;
        }

        .val-err {
            color: #f87171;
        }

        /* Live Activity Console */
        .console-card {
            background: #020617;
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: 10px;
            overflow: hidden;
            font-family: 'JetBrains Mono', monospace;
        }

        .console-header {
            background: rgba(15, 23, 42, 0.9);
            padding: 0.6rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.78rem;
            font-weight: 700;
            color: #94a3b8;
        }

        .console-body {
            padding: 1rem;
            max-height: 180px;
            overflow-y: auto;
            font-size: 0.78rem;
            line-height: 1.6;
            color: #cbd5e1;
            white-space: pre-wrap;
        }

        .log-entry {
            margin-bottom: 0.25rem;
        }

        .log-time {
            color: #64748b;
        }

        .log-success {
            color: #34d399;
        }

        .log-info {
            color: #38bdf8;
        }

        .log-warn {
            color: #fbbf24;
        }

        .log-err {
            color: #f87171;
        }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #1e293b;
            border: 1px solid rgba(56, 189, 248, 0.5);
            color: #ffffff;
            padding: 0.85rem 1.25rem;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            gap: 0.75rem;
            z-index: 9999;
            font-size: 0.88rem;
            animation: slideUp 0.25s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Top Bar -->
    <header class="top-bar">
        <div class="brand-section">
            <div class="brand-badge">Hyu</div>
            <div>
                <div class="brand-title">
                    <span>MCU Central & PACS Radiologi</span>
                </div>
                <div class="brand-subtitle">
                    System Readiness Center &bull; Cahaya Diagnostic Centre (CDC)
                </div>
            </div>
        </div>

        <div class="header-actions">
            <button class="btn-refresh" onclick="fetchSystemStatus()" id="btnRefreshDiagnostics">
                🔄 Refresh Diagnostik
            </button>
            <a href="{{ route('scans.index') }}" class="btn-cta-launch">
                <span>🚀 Buka MCU Central & Viewer</span>
                <span style="background: rgba(255,255,255,0.2); padding: 0.15rem 0.5rem; border-radius: 999px; font-size: 0.78rem;" id="headerOrderBadge">—</span>
            </a>
        </div>
    </header>

    <!-- Overall Readiness Hero Banner -->
    <section class="readiness-hero">
        <div class="hero-left">
            <h1>Pemeriksaan Kesiapan Sistem Radiologi</h1>
            <p>
                Lakukan verifikasi berkala pada port listener DICOM, mesin AI deep learning, direktori citra klinis, dan basis data sebelum operasional ekspos rontgen MCU dimulai.
            </p>
        </div>

        <div class="hero-score-badge">
            <div class="score-circle" id="scorePercent">—%</div>
            <div class="score-details">
                <div class="score-label">Status Kesiapan</div>
                <div class="score-status-text" id="scoreStatusText">Memeriksa...</div>
            </div>
        </div>
    </section>

    <!-- Highlighted Feature Card: Hyu DICOM PACS Server Toggle -->
    <section class="dicom-server-card">
        <div class="dicom-card-header">
            <div class="dicom-title-group">
                <div class="dicom-icon">🏥</div>
                <div>
                    <div class="dicom-title">Hyu Standalone DICOM PACS Server (C-STORE & Modality Worklist)</div>
                    <div class="dicom-desc">Daemon penerima pengiriman citra dari alat rontgen (Fujifilm FDR / Mindray USG) via protokol DICOM 3.0</div>
                </div>
            </div>

            <div id="dicomStatusPill" class="status-pill status-pill-inactive">
                <span class="pulse-dot"></span>
                <span id="dicomStatusText">MEMERIKSA PORT...</span>
            </div>
        </div>

        <!-- Interactive Switch & Parameters -->
        <div class="dicom-toggle-wrap">
            <div class="toggle-switch-container">
                <button class="toggle-btn" id="btnDicomToggle" onclick="handleDicomToggle()">
                    <span class="toggle-slider"></span>
                </button>
                <div class="toggle-label" id="lblDicomToggle">
                    Klik untuk Mengaktifkan Server
                </div>
            </div>

            <div class="dicom-meta-grid">
                <div class="meta-item">
                    <div class="meta-label">Port Listener</div>
                    <div class="meta-val" id="metaPort">{{ $pacsConfig['port'] }} (TCP)</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">AE Title Server</div>
                    <div class="meta-val" id="metaAe">{{ $pacsConfig['ae_title'] }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Process ID (PID)</div>
                    <div class="meta-val" id="metaPid">—</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Target Modality</div>
                    <div class="meta-val" id="metaTarget">{{ $pacsConfig['modality_ae'] }}</div>
                </div>
            </div>
        </div>

        <!-- Quick Server Actions -->
        <div class="dicom-actions-row">
            <button class="btn-sub-action primary-sub" onclick="testEchoPing()" id="btnPingEcho">
                📡 Tes Koneksi C-ECHO (Ping)
            </button>
            <button class="btn-sub-action" onclick="restartDicomServer()" id="btnRestartDicom">
                🔄 Restart Server DICOM
            </button>
            <button class="btn-sub-action" onclick="triggerSimulateFuji()" id="btnSimulateFuji">
                📨 Simulasi Transmisi Fujifilm (C-STORE)
            </button>
            <a href="{{ route('scans.index') }}" class="btn-sub-action" style="text-decoration: none;">
                📋 Lihat Antrian Modality Worklist
            </a>
        </div>
    </section>

    <!-- Subsystems Diagnostic Grid (3 Cards) -->
    <section class="subsystems-grid">

        <!-- Card 1: AI & Python Engine -->
        <div class="sub-card">
            <div class="sub-card-header">
                <div class="sub-card-title">
                    <span>🧠</span>
                    <span>Python & AI Medical Engine</span>
                </div>
                <span class="sub-card-badge badge-ready" id="badgePython">SIAP</span>
            </div>

            <ul class="check-list">
                <li class="check-item">
                    <span class="check-name">Python Runtime</span>
                    <span class="check-val" id="valPyVersion">Memeriksa...</span>
                </li>
                <li class="check-item">
                    <span class="check-name">PyTorch Engine</span>
                    <span class="check-val" id="valTorchVersion">Memeriksa...</span>
                </li>
                <li class="check-item">
                    <span class="check-name">Hardware Acceleration</span>
                    <span class="check-val" id="valCuda">Memeriksa...</span>
                </li>
                <li class="check-item">
                    <span class="check-name">OpenCV Imaging</span>
                    <span class="check-val" id="valOpencv">Memeriksa...</span>
                </li>
                <li class="check-item">
                    <span class="check-name">Pynetdicom & Pydicom</span>
                    <span class="check-val" id="valDicomLibs">Memeriksa...</span>
                </li>
                <li class="check-item">
                    <span class="check-name">Bone Suppression Weights</span>
                    <span class="check-val" id="valBoneWeights">Memeriksa...</span>
                </li>
            </ul>
        </div>

        <!-- Card 2: Database & EMR -->
        <div class="sub-card">
            <div class="sub-card-header">
                <div class="sub-card-title">
                    <span>🗄️</span>
                    <span>Database & Rekam Medis (EMR)</span>
                </div>
                <span class="sub-card-badge badge-ready" id="badgeDb">SIAP</span>
            </div>

            <ul class="check-list">
                <li class="check-item">
                    <span class="check-name">Database Driver</span>
                    <span class="check-val" id="valDbDriver">SQLite</span>
                </li>
                <li class="check-item">
                    <span class="check-name">Koneksi Database</span>
                    <span class="check-val val-ok" id="valDbConn">Terhubung</span>
                </li>
                <li class="check-item">
                    <span class="check-name">Tabel Database</span>
                    <span class="check-val" id="valDbTables">Memeriksa...</span>
                </li>
                <li class="check-item">
                    <span class="check-name">Total Rekam Pasien MCU</span>
                    <span class="check-val val-ok" id="valDbScans">Memeriksa...</span>
                </li>
                <li class="check-item">
                    <span class="check-name">Integritas Transaksi</span>
                    <span class="check-val val-ok">ACID Protected</span>
                </li>
            </ul>
        </div>

        <!-- Card 3: Storage & Assets -->
        <div class="sub-card">
            <div class="sub-card-header">
                <div class="sub-card-title">
                    <span>📁</span>
                    <span>Penyimpanan Citra & Aset DICOM</span>
                </div>
                <span class="sub-card-badge badge-ready" id="badgeStorage">SIAP</span>
            </div>

            <ul class="check-list">
                <li class="check-item">
                    <span class="check-name">Folder scan-assets/</span>
                    <span class="check-val val-ok" id="valAssetsDir">Writable</span>
                </li>
                <li class="check-item">
                    <span class="check-name">Folder storage/scans/</span>
                    <span class="check-val val-ok" id="valScansDir">Tersedia</span>
                </li>
                <li class="check-item">
                    <span class="check-name">Sampel Citra Toraks</span>
                    <span class="check-val" id="valSampleToraks">sample_toraks.dcm</span>
                </li>
                <li class="check-item">
                    <span class="check-name">Sampel Citra Kepala (Sinus)</span>
                    <span class="check-val" id="valSampleKepala">sample_kepala.dcm</span>
                </li>
                <li class="check-item">
                    <span class="check-name">Sampel Ultrasonografi (USG)</span>
                    <span class="check-val" id="valSampleUsg">sample_usg.dcm</span>
                </li>
            </ul>
        </div>

    </section>

    <!-- Live Activity Console -->
    <section class="console-card">
        <div class="console-header">
            <span>📟 KONSOL DIAGNOSTIK & LOG AKTIVITAS SERVER</span>
            <span id="consolePingLatency" style="color: #38bdf8; font-weight: 600;">Response: — ms</span>
        </div>
        <div class="console-body" id="consoleBody">
            <div class="log-entry"><span class="log-time">[SISTEM]</span> Menghubungkan ke API diagnostik internal...</div>
        </div>
    </section>

</div>

<!-- Toast -->
<div class="toast" id="toastBox">
    <span id="toastIcon">ℹ️</span>
    <span id="toastMsg">Pesan notifikasi</span>
</div>

<script>
    let isServerRunning = false;
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function logToConsole(type, msg) {
        const consoleEl = document.getElementById('consoleBody');
        const now = new Date();
        const timeStr = now.toTimeString().split(' ')[0] + '.' + String(now.getMilliseconds()).padStart(3, '0');
        
        const entry = document.createElement('div');
        entry.className = 'log-entry';
        
        let colorClass = 'log-info';
        if (type === 'success') colorClass = 'log-success';
        if (type === 'warn') colorClass = 'log-warn';
        if (type === 'error') colorClass = 'log-err';

        entry.innerHTML = `<span class="log-time">[${timeStr}]</span> <span class="${colorClass}">${msg}</span>`;
        consoleEl.appendChild(entry);
        consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function showToast(icon, message) {
        const toast = document.getElementById('toastBox');
        document.getElementById('toastIcon').innerText = icon;
        document.getElementById('toastMsg').innerText = message;
        toast.style.display = 'flex';
        setTimeout(() => { toast.style.display = 'none'; }, 4000);
    }

    // Fetch Status Diagnostik
    async function fetchSystemStatus() {
        const btnRefresh = document.getElementById('btnRefreshDiagnostics');
        btnRefresh.innerText = '🔄 Memeriksa...';
        btnRefresh.disabled = true;

        try {
            const res = await fetch("{{ route('system.status') }}");
            const data = await res.json();

            if (data.status === 'success') {
                // Readiness Score
                document.getElementById('scorePercent').innerText = `${data.readiness_percent}%`;
                document.getElementById('scoreStatusText').innerText = data.all_ready ? 'SEMUA SISTEM SIAP OPERASIONAL' : 'PERLU PERHATIAN';
                document.getElementById('scoreStatusText').style.color = data.all_ready ? '#34d399' : '#fbbf24';
                document.getElementById('consolePingLatency').innerText = `Diagnostik: ${data.execution_ms} ms`;

                // DICOM Server Status
                updateDicomUi(data.dicom.running, data.dicom.port, data.dicom.pid, data.dicom.ae_title);

                // Python Subsystem
                const py = data.python;
                document.getElementById('valPyVersion').innerText = py.version ? `v${py.version}` : 'Tidak Terdeteksi';
                document.getElementById('valTorchVersion').innerText = py.torch_version ? `v${py.torch_version}` : 'Belum Terpasang';
                document.getElementById('valCuda').innerText = py.cuda_available ? '✅ GPU CUDA Aktif' : 'CPU Mode';
                document.getElementById('valCuda').className = 'check-val ' + (py.cuda_available ? 'val-ok' : '');
                document.getElementById('valOpencv').innerText = py.opencv_version ? `v${py.opencv_version}` : 'Tidak Ada';
                document.getElementById('valDicomLibs').innerText = (py.pydicom && py.pynetdicom) ? '✅ pydicom & pynetdicom' : 'Kurang Komponen';
                document.getElementById('valDicomLibs').className = 'check-val ' + ((py.pydicom && py.pynetdicom) ? 'val-ok' : 'val-warn');
                document.getElementById('valBoneWeights').innerText = py.weights_bone ? '✅ weights/bone_suppression.ts' : '❌ Berkas Bobot Tidak Ditemukan';
                document.getElementById('valBoneWeights').className = 'check-val ' + (py.weights_bone ? 'val-ok' : 'val-err');

                // Database Subsystem
                const db = data.database;
                document.getElementById('valDbDriver').innerText = db.driver.toUpperCase();
                document.getElementById('valDbTables').innerText = `${db.tables_count} Tabel Terverifikasi`;
                document.getElementById('valDbScans').innerText = `${db.scans_count} Pasien Terdaftar`;
                document.getElementById('headerOrderBadge').innerText = `${db.scans_count} Pasien`;

                // Storage Subsystem
                const st = data.storage;
                document.getElementById('valAssetsDir').innerText = st.assets_dir_writable ? '✅ Siap (Writable)' : '❌ Read-Only';
                document.getElementById('valAssetsDir').className = 'check-val ' + (st.assets_dir_writable ? 'val-ok' : 'val-err');
                document.getElementById('valScansDir').innerText = st.scans_dir_exists ? '✅ storage/app/public/scans' : 'Belum Dibuat';
                document.getElementById('valSampleToraks').innerText = st.sample_toraks ? '✅ Ada (CR Toraks)' : '❌ Hilang';
                document.getElementById('valSampleKepala').innerText = st.sample_kepala ? '✅ Ada (Sinus Waters)' : '❌ Hilang';
                document.getElementById('valSampleUsg').innerText = st.sample_usg ? '✅ Ada (USG Abdomen)' : '❌ Hilang';

                logToConsole('success', `Status diperbarui: Kesiapan ${data.readiness_percent}% (${data.dicom.running ? 'Server DICOM Port ' + data.dicom.port + ' Aktif' : 'Server DICOM Offline'})`);
            }
        } catch (err) {
            logToConsole('error', 'Gagal memuat status diagnostik: ' + err.message);
        } finally {
            btnRefresh.innerText = '🔄 Refresh Diagnostik';
            btnRefresh.disabled = false;
        }
    }

    function updateDicomUi(running, port, pid, aeTitle) {
        isServerRunning = running;
        const pill = document.getElementById('dicomStatusPill');
        const text = document.getElementById('dicomStatusText');
        const toggleBtn = document.getElementById('btnDicomToggle');
        const toggleLbl = document.getElementById('lblDicomToggle');
        const metaPid = document.getElementById('metaPid');

        if (running) {
            pill.className = 'status-pill status-pill-active';
            text.innerText = `● SERVER DICOM AKTIF (PORT ${port})`;
            toggleBtn.className = 'toggle-btn active';
            toggleLbl.innerText = 'Server Aktif — Klik untuk Mematikan';
            metaPid.innerText = pid ? `PID ${pid}` : 'Aktif (Socket)';
            metaPid.style.color = '#34d399';
        } else {
            pill.className = 'status-pill status-pill-inactive';
            text.innerText = '○ SERVER OFFLINE';
            toggleBtn.className = 'toggle-btn';
            toggleLbl.innerText = 'Server Mati — Klik untuk Menyalakan';
            metaPid.innerText = 'Offline';
            metaPid.style.color = '#94a3b8';
        }
    }

    // Toggle ON/OFF
    async function handleDicomToggle() {
        const newAction = isServerRunning ? 'stop' : 'start';
        const toggleLbl = document.getElementById('lblDicomToggle');
        toggleLbl.innerText = newAction === 'start' ? 'Sedang menyalakan daemon DICOM...' : 'Sedang menghentikan server...';

        logToConsole('info', `Mengirim perintah ${newAction.toUpperCase()} ke daemon DICOM...`);

        try {
            const res = await fetch("{{ route('system.dicom.toggle') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ action: newAction })
            });
            const data = await res.json();

            logToConsole(data.status === 'success' ? 'success' : 'warn', data.message);
            showToast(data.status === 'success' ? '✅' : '⚠️', data.message);

            updateDicomUi(data.running, data.port, data.pid, '{{ $pacsConfig["ae_title"] }}');
            fetchSystemStatus();
        } catch (err) {
            logToConsole('error', 'Gagal mengubah status server: ' + err.message);
            showToast('❌', 'Terjadi kesalahan jaringan.');
        }
    }

    // Restart Server
    async function restartDicomServer() {
        const btn = document.getElementById('btnRestartDicom');
        btn.disabled = true;
        btn.innerText = '🔄 Mereboot...';
        logToConsole('info', 'Meminta restart daemon DICOM...');

        try {
            const res = await fetch("{{ route('system.dicom.toggle') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ action: 'restart' })
            });
            const data = await res.json();
            logToConsole(data.status === 'success' ? 'success' : 'warn', data.message);
            showToast('🔄', data.message);
            updateDicomUi(data.running, data.port, data.pid, '{{ $pacsConfig["ae_title"] }}');
            fetchSystemStatus();
        } catch (err) {
            logToConsole('error', 'Gagal me-restart server: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerText = '🔄 Restart Server DICOM';
        }
    }

    // Tes C-ECHO Ping
    async function testEchoPing() {
        const btn = document.getElementById('btnPingEcho');
        btn.disabled = true;
        btn.innerText = '📡 Mengirim Ping C-ECHO...';
        logToConsole('info', 'Menginisiasi asosiasi DICOM C-ECHO Verification ke 127.0.0.1:{{ $pacsConfig["port"] }}...');

        try {
            const res = await fetch("{{ route('system.dicom.ping') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await res.json();

            if (res.ok && data.status === 'success') {
                logToConsole('success', `[C-ECHO BERHASIL] Status: ${data.status_code} | Latensi: ${data.latency_ms} ms`);
                showToast('📡', `C-ECHO Sukses (${data.latency_ms} ms)`);
            } else {
                logToConsole('error', `[C-ECHO GAGAL] ${data.message || 'Koneksi ditolak'}`);
                showToast('❌', data.message || 'C-ECHO Ping gagal.');
            }
        } catch (err) {
            logToConsole('error', 'Gagal memicu C-ECHO: ' + err.message);
            showToast('❌', 'Koneksi socket terputus.');
        } finally {
            btn.disabled = false;
            btn.innerText = '📡 Tes Koneksi C-ECHO (Ping)';
        }
    }

    // Simulasi C-STORE Fujifilm
    async function triggerSimulateFuji() {
        const btn = document.getElementById('btnSimulateFuji');
        btn.disabled = true;
        btn.innerText = '📨 Mentransmisikan...';
        logToConsole('info', 'Mengirim citra rontgen via simulasi DICOM C-STORE Fujifilm...');

        try {
            const res = await fetch("{{ route('scans.simulate-fuji') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await res.json();

            if (data.status === 'success') {
                logToConsole('success', `[C-STORE SUKSES] Citra pasien ${data.patient_name} (${data.accession_number}) berhasil diarsipkan via ${data.mode}!`);
                showToast('📨', `C-STORE Sukses: ${data.patient_name}`);
                fetchSystemStatus();
            } else {
                logToConsole('error', `[C-STORE GAGAL] ${data.message}`);
                showToast('❌', data.message);
            }
        } catch (err) {
            logToConsole('error', 'Kesalahan transmisi C-STORE: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerText = '📨 Simulasi Transmisi Fujifilm (C-STORE)';
        }
    }

    // Initial check on page load
    window.addEventListener('DOMContentLoaded', () => {
        fetchSystemStatus();
    });
</script>

</body>
</html>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hyu - Portal Medical Imaging & PACS</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --border-color: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #0284c7;
            --primary-hover: #0369a1;
            --badge-usg: #0d9488;
            --badge-thorax: #8b5cf6;
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
            padding: 2.5rem 1.5rem;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        /* Navbar / Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }

        .brand-title {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-logo {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .pacs-badge {
            background-color: rgba(2, 132, 199, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
            padding: 0.4rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Card & Table */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background-color: rgba(15, 23, 42, 0.6);
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.925rem;
            vertical-align: middle;
        }

        tr:hover td {
            background-color: rgba(255, 255, 255, 0.02);
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.3rem 0.7rem;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .badge-mri, .badge-usg {
            background-color: rgba(13, 148, 136, 0.2);
            color: #2dd4bf;
            border: 1px solid rgba(45, 212, 191, 0.3);
        }

        .badge-thorax, .badge-ct {
            background-color: rgba(139, 92, 246, 0.2);
            color: #c084fc;
            border: 1px solid rgba(192, 132, 252, 0.3);
        }

        /* Buttons */
        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background-color: var(--primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-view:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <header class="header">
        <div>
            <div class="brand-title">
                <span class="brand-logo">Hyu</span>
                <span>Medical Imaging System</span>
            </div>
            <p class="subtitle">Cahaya Diagnostic Centre (CDC) &bull; Modul Multimodal PACS Viewer</p>
        </div>
        <div>
            <span class="pacs-badge">● teraMedik PACS: Terhubung</span>
        </div>
    </header>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Daftar Pemeriksaan Pasien (Studi Radiologi)</div>
            <div style="color: var(--text-muted); font-size: 0.85rem;">
                Total: <strong>{{ $scans->count() }} Pemeriksaan</strong>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID / No. RM</th>
                    <th>Nama Pasien</th>
                    <th>Modalitas</th>
                    <th>Catatan Klinis / Diagnosa</th>
                    <th>Tanggal Periksa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($scans as $scan)
                    <tr>
                        <td style="font-weight: 600; color: #38bdf8;">#{{ str_pad($scan->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td style="font-weight: 600;">{{ $scan->patient_name }}</td>
                        <td>
                            @if(str_contains(strtoupper($scan->modality), 'USG') || str_contains(strtoupper($scan->modality), 'MRI'))
                                <span class="badge badge-usg">{{ $scan->modality }}</span>
                            @else
                                <span class="badge badge-thorax">{{ $scan->modality }}</span>
                            @endif
                        </td>
                        <td style="color: var(--text-muted); max-width: 320px;">
                            {{ $scan->diagnosis_notes ?? 'Menunggu diagnosa radiolog' }}
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.85rem;">
                            {{ $scan->created_at ? $scan->created_at->format('d M Y, H:i') : '-' }}
                        </td>
                        <td>
                            <a href="{{ route('scans.show', $scan->id) }}" class="btn-view">
                                🔍 Buka Viewer
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                            Belum ada data pemeriksaan pasien.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

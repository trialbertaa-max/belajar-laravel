<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hyu Multimodal PACS Viewer - {{ $scan->patient_name }} ({{ $scan->modality }})</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-viewer: #020617;
            --panel-bg: #0f172a;
            --border-color: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #64748b;
            --primary: #0284c7;
            --primary-hover: #0369a1;
            --accent-cyan: #06b6d4;
            --accent-green: #10b981;
            --accent-purple: #8b5cf6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-viewer);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Top Navbar */
        .navbar {
            background-color: var(--panel-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 56px;
            z-index: 10;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-back {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.825rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            transition: all 0.2s;
        }

        .btn-back:hover {
            color: var(--text-main);
            background-color: rgba(255, 255, 255, 0.05);
        }

        .patient-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background-color: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--border-color);
            padding: 0.3rem 0.75rem;
            border-radius: 6px;
        }

        .patient-name {
            font-size: 0.95rem;
            font-weight: 700;
        }

        .badge-modality {
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            background-color: rgba(2, 132, 199, 0.2);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        /* Toolbar */
        .toolbar {
            background-color: #090e1a;
            border-bottom: 1px solid var(--border-color);
            padding: 0.4rem 1.25rem;
            display: flex;
            gap: 0.4rem;
            align-items: center;
            flex-wrap: wrap;
            z-index: 10;
        }

        .tool-btn {
            background-color: var(--panel-bg);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.35rem 0.7rem;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.15s;
        }

        .tool-btn:hover {
            background-color: #1e293b;
            border-color: #475569;
        }

        .tool-btn.active {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
            font-weight: 600;
        }

        .tool-separator {
            width: 1px;
            height: 20px;
            background-color: var(--border-color);
            margin: 0 0.3rem;
        }

        /* Main Workspace */
        .workspace {
            flex: 1;
            display: flex;
            position: relative;
            overflow: hidden;
        }

        /* Grid Viewport Container */
        .viewports-grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr;
            gap: 4px;
            background-color: #000;
            position: relative;
            overflow: hidden;
            transition: grid-template-columns 0.3s ease;
        }

        .viewports-grid.multimodal-split {
            grid-template-columns: 1fr 1fr;
        }

        /* Individual Viewport Cell */
        .viewport-cell {
            position: relative;
            background-color: #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid #111827;
            cursor: grab;
        }

        .viewport-cell:active {
            cursor: grabbing;
        }

        .viewport-cell.active-cell {
            border: 1px solid var(--primary);
        }

        .medical-image {
            max-width: 85%;
            max-height: 85%;
            transition: transform 0.05s ease-out, filter 0.1s ease-out;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.9);
            user-select: none;
            pointer-events: none;
        }

        /* DICOM Overlays (HUD in 4 corners) */
        .dicom-overlay {
            position: absolute;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            color: #22d3ee;
            text-shadow: 1px 1px 2px black;
            pointer-events: none;
            line-height: 1.4;
            z-index: 5;
        }

        .overlay-top-left { top: 0.75rem; left: 1rem; }
        .overlay-top-right { top: 0.75rem; right: 1rem; text-align: right; }
        .overlay-bottom-left { bottom: 0.75rem; left: 1rem; }
        .overlay-bottom-right { bottom: 0.75rem; right: 1rem; text-align: right; }

        /* Caliper Measurement Overlay (Canvas) */
        .measurement-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 4;
        }

        /* Right Side Clinical Notes & Denoising Panel */
        .side-panel {
            width: 320px;
            background-color: var(--panel-bg);
            border-left: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .panel-header {
            padding: 0.85rem 1.15rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-content {
            padding: 1.15rem;
            flex: 1;
            overflow-y: auto;
        }

        .info-card {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.85rem;
            margin-bottom: 1rem;
        }

        .info-label {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 0.825rem;
            color: var(--text-main);
            line-height: 1.4;
        }

        /* Python Denoising Section */
        .denoise-box {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(2, 132, 199, 0.1));
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 8px;
            padding: 0.85rem;
            margin-top: 0.5rem;
        }

        .btn-python {
            width: 100%;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            color: white;
            border: none;
            padding: 0.6rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 0.6rem;
            transition: all 0.2s;
        }

        .btn-python:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <header class="navbar">
        <div class="nav-left">
            <a href="{{ route('scans.index') }}" class="btn-back">
                &larr; Daftar Pasien
            </a>
            <div class="patient-badge">
                <span class="patient-name">{{ $scan->patient_name }}</span>
                <span class="badge-modality">{{ $scan->modality }}</span>
                <span style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">
                    #CDC-{{ str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}
                </span>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <button class="tool-btn" id="btnLayoutToggle" onclick="toggleMultimodalLayout()">
                🔲 Mode Multimodal: <span id="lblLayout" style="font-weight: 700; color: #38bdf8;">1x1 Tunggal</span>
            </button>
            <span style="font-size: 0.75rem; color: #10b981; font-weight: 600;">
                ● teraMedik PACS (Online)
            </span>
        </div>
    </header>

    <!-- Radiologist Toolbar -->
    <div class="toolbar">
        <button class="tool-btn active" id="btnPan" onclick="setTool('pan')">🖐️ Pan / Geser</button>
        <button class="tool-btn" id="btnZoomTool" onclick="setTool('zoom')">🔍 Zoom Tool</button>
        <button class="tool-btn" id="btnCaliper" onclick="setTool('caliper')">📏 Caliper (Ukur USG)</button>
        <button class="tool-btn" id="btnInvert" onclick="toggleInvert()">🌓 Invert</button>
        
        <div class="tool-separator"></div>

        <!-- Windowing Preset Buttons -->
        <span style="font-size: 0.72rem; color: var(--text-muted);">Preset WW/WL:</span>
        <button class="tool-btn" onclick="applyPreset('lung')">🫁 Lung (Paru)</button>
        <button class="tool-btn" onclick="applyPreset('bone')">🦴 Bone (Tulang)</button>
        <button class="tool-btn" onclick="applyPreset('soft')">🧠 Soft Tissue</button>

        <div class="tool-separator"></div>

        <button class="tool-btn" onclick="zoomIn()">➕</button>
        <button class="tool-btn" onclick="zoomOut()">➖</button>
        <button class="tool-btn" onclick="resetViewer()">↺ Reset</button>
    </div>

    <!-- Workspace -->
    <div class="workspace">
        
        <!-- Viewport Grid Container -->
        <div class="viewports-grid" id="viewportsGrid">
            
            <!-- Viewport 1 (Primary: Thorax / Selected Scan) -->
            <div class="viewport-cell active-cell" id="vp1" onclick="selectViewport(1)">
                <div class="dicom-overlay overlay-top-left">
                    <strong>CAHAYA DIAGNOSTIC CENTRE</strong><br>
                    Pasien: {{ strtoupper($scan->patient_name) }}<br>
                    MRN: #CDC-{{ str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}
                </div>
                <div class="dicom-overlay overlay-top-right">
                    Modalitas: {{ $scan->modality }}<br>
                    Studi: Thorax PA View<br>
                    PACS: teraMedik
                </div>
                <div class="dicom-overlay overlay-bottom-left">
                    Zoom: <span id="lblZoom">100%</span><br>
                    <span id="lblWwWl">WW: 400 | WL: 40 (Default)</span>
                </div>
                <div class="dicom-overlay overlay-bottom-right">
                    Viewport A (Primary)<br>
                    Format: DICOM
                </div>

                <!-- Measurement Canvas Overlay -->
                <svg class="measurement-canvas" id="svgMeasure1"></svg>

                <img id="imgVp1" class="medical-image"
                     src="https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&w=800&q=80" 
                     alt="DICOM Thorax Scan">
            </div>

            <!-- Viewport 2 (Secondary: Multimodal USG Comparison) -->
            <div class="viewport-cell" id="vp2" style="display: none;" onclick="selectViewport(2)">
                <div class="dicom-overlay overlay-top-left">
                    <strong>CAHAYA DIAGNOSTIC CENTRE</strong><br>
                    Pasien: {{ strtoupper($scan->patient_name) }}<br>
                    MRN: #CDC-{{ str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}
                </div>
                <div class="dicom-overlay overlay-top-right">
                    Modalitas: USG Abdomen<br>
                    Frekuensi Probe: 3.5 MHz<br>
                    PACS: teraMedik
                </div>
                <div class="dicom-overlay overlay-bottom-left">
                    Zoom: <span id="lblZoom2">100%</span><br>
                    Gain: 65 dB | Depth: 14 cm
                </div>
                <div class="dicom-overlay overlay-bottom-right">
                    Viewport B (Multimodal USG)<br>
                    Format: DICOM Multi-frame
                </div>

                <!-- Measurement Canvas Overlay untuk VP2 -->
                <svg class="measurement-canvas" id="svgMeasure2"></svg>

                <img id="imgVp2" class="medical-image"
                     src="{{ asset('scans/usg_sample.jpg') }}?v={{ time() }}" 
                     alt="DICOM USG Scan">
            </div>

        </div>

        <!-- Right Side Panel: Clinical & AI Denoising Info -->
        <aside class="side-panel">
            <div class="panel-header">
                <span>Informasi Klinis & Diagnosa</span>
            </div>

            <div class="panel-content">
                <div class="info-card">
                    <div class="info-label">Pemeriksaan Utama</div>
                    <div class="info-value" style="font-weight: 600; color: #38bdf8;">{{ $scan->modality }}</div>
                </div>

                <div class="info-card">
                    <div class="info-label">Catatan Radiolog</div>
                    <div class="info-value">{{ $scan->diagnosis_notes ?? 'Belum ada catatan diagnosa.' }}</div>
                </div>

                <div class="info-card">
                    <div class="info-label">PACS Instance Path</div>
                    <div class="info-value" style="font-family: monospace; font-size: 0.75rem; color: #94a3b8;">
                        {{ $scan->scan_image_path ?? 'scans/dcm_01.dcm' }}
                    </div>
                </div>

                <!-- Python AI Denoising Box -->
                <div class="denoise-box">
                    <div style="display: flex; align-items: center; gap: 0.4rem; font-weight: 600; font-size: 0.825rem; color: #c084fc;">
                        <span>🐍 Python AI Denoising</span>
                    </div>
                    <p style="font-size: 0.725rem; color: var(--text-muted); margin-top: 0.35rem; line-height: 1.4;">
                        Algoritma Non-Local Means (NLM) untuk mengurangi noise bintik pada USG / Thorax.
                    </p>
                    <button class="btn-python" onclick="applyDenoiseDemo()">
                        ✨ Jalankan Denoising Filter
                    </button>
                </div>
            </div>
        </aside>

    </div>

    <!-- Interactive Multimodal Viewer Scripts -->
    <script>
        // State per viewport (1 = Kiri/Thorax, 2 = Kanan/USG)
        let activeViewport = 1;

        const vpState = {
            1: { zoom: 1, translateX: 0, translateY: 0, inverted: false, brightness: 100, contrast: 100 },
            2: { zoom: 1, translateX: 0, translateY: 0, inverted: false, brightness: 100, contrast: 100 }
        };

        let activeTool = 'pan';
        let isMultimodal = false;
        let isDragging = false;
        let startX, startY;

        const img1 = document.getElementById('imgVp1');
        const img2 = document.getElementById('imgVp2');
        const vp1 = document.getElementById('vp1');
        const vp2 = document.getElementById('vp2');
        const viewportsGrid = document.getElementById('viewportsGrid');
        const lblZoom = document.getElementById('lblZoom');
        const lblLayout = document.getElementById('lblLayout');
        const lblWwWl = document.getElementById('lblWwWl');
        const svgMeasure1 = document.getElementById('svgMeasure1');
        const svgMeasure2 = document.getElementById('svgMeasure2');

        // Fungsi memilih viewport aktif saat diklik
        function selectViewport(id) {
            activeViewport = id;
            vp1.classList.toggle('active-cell', id === 1);
            vp2.classList.toggle('active-cell', id === 2);
            document.getElementById('btnInvert').classList.toggle('active', vpState[id].inverted);
        }

        function setTool(tool) {
            activeTool = tool;
            document.querySelectorAll('.tool-btn').forEach(btn => btn.classList.remove('active'));
            if (tool === 'pan') document.getElementById('btnPan').classList.add('active');
            if (tool === 'zoom') document.getElementById('btnZoomTool').classList.add('active');
            if (tool === 'caliper') {
                document.getElementById('btnCaliper').classList.add('active');
                drawSampleCaliper();
            }
        }

        function toggleMultimodalLayout() {
            isMultimodal = !isMultimodal;
            if (isMultimodal) {
                viewportsGrid.classList.add('multimodal-split');
                vp2.style.display = 'flex';
                lblLayout.innerText = '2x1 Multimodal (Thorax + USG)';
                lblLayout.style.color = '#c084fc';
            } else {
                viewportsGrid.classList.remove('multimodal-split');
                vp2.style.display = 'none';
                lblLayout.innerText = '1x1 Tunggal';
                lblLayout.style.color = '#38bdf8';
                selectViewport(1);
            }
        }

        // Pan & Drag untuk viewport yang aktif
        [vp1, vp2].forEach((cell, index) => {
            const vpId = index + 1;
            cell.addEventListener('mousedown', (e) => {
                selectViewport(vpId);
                if (activeTool !== 'caliper') {
                    isDragging = true;
                    startX = e.clientX - vpState[vpId].translateX;
                    startY = e.clientY - vpState[vpId].translateY;
                }
            });

            cell.addEventListener('wheel', (e) => {
                e.preventDefault();
                selectViewport(vpId);
                if (e.deltaY < 0) zoomIn();
                else zoomOut();
            });
        });

        window.addEventListener('mouseup', () => { isDragging = false; });

        window.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            vpState[activeViewport].translateX = e.clientX - startX;
            vpState[activeViewport].translateY = e.clientY - startY;
            updateTransform(activeViewport);
        });

        function zoomIn() {
            vpState[activeViewport].zoom = Math.min(vpState[activeViewport].zoom + 0.15, 3.5);
            updateTransform(activeViewport);
        }

        function zoomOut() {
            vpState[activeViewport].zoom = Math.max(vpState[activeViewport].zoom - 0.15, 0.4);
            updateTransform(activeViewport);
        }

        function toggleInvert() {
            const st = vpState[activeViewport];
            st.inverted = !st.inverted;
            document.getElementById('btnInvert').classList.toggle('active', st.inverted);
            updateFilter(activeViewport);
        }

        function applyPreset(preset) {
            const st = vpState[activeViewport];
            if (preset === 'lung') {
                st.brightness = 130; st.contrast = 150;
                lblWwWl.innerText = 'WW: 1500 | WL: -600 (Lung Preset)';
            } else if (preset === 'bone') {
                st.brightness = 90; st.contrast = 200;
                lblWwWl.innerText = 'WW: 2500 | WL: 480 (Bone Preset)';
            } else if (preset === 'soft') {
                st.brightness = 110; st.contrast = 115;
                lblWwWl.innerText = 'WW: 350 | WL: 50 (Soft Tissue)';
            }
            updateFilter(activeViewport);
        }

        function drawSampleCaliper() {
            const targetSvg = (activeViewport === 1) ? svgMeasure1 : svgMeasure2;
            const labelText = (activeViewport === 1) ? "Thorax Width: 28.4 cm" : "Hepar Depth: 4.12 cm";
            
            targetSvg.innerHTML = `
                <line x1="160" y1="200" x2="300" y2="200" stroke="#facc15" stroke-width="2" stroke-dasharray="4" />
                <circle cx="160" cy="200" r="4" fill="#facc15" />
                <circle cx="300" cy="200" r="4" fill="#facc15" />
                <text x="180" y="190" fill="#facc15" font-size="12" font-weight="bold" font-family="monospace">
                    ${labelText}
                </text>
            `;
        }

        function resetViewer() {
            [1, 2].forEach(id => {
                vpState[id] = { zoom: 1, translateX: 0, translateY: 0, inverted: false, brightness: 100, contrast: 100 };
                updateTransform(id);
                updateFilter(id);
            });
            svgMeasure1.innerHTML = '';
            svgMeasure2.innerHTML = '';
            document.getElementById('btnInvert').classList.remove('active');
            lblWwWl.innerText = 'WW: 400 | WL: 40 (Default)';
        }

        function updateTransform(id) {
            const img = (id === 1) ? img1 : img2;
            const st = vpState[id];
            img.style.transform = `translate(${st.translateX}px, ${st.translateY}px) scale(${st.zoom})`;
            if (id === 1) lblZoom.innerText = Math.round(st.zoom * 100) + '%';
        }

        function updateFilter(id) {
            const img = (id === 1) ? img1 : img2;
            const st = vpState[id];
            img.style.filter = `invert(${st.inverted ? 1 : 0}) brightness(${st.brightness}%) contrast(${st.contrast}%)`;
        }

        function applyDenoiseDemo() {
            const btn = document.querySelector('.btn-python');
            const originalText = btn.innerHTML;
            btn.innerHTML = '⏳ Memproses di Python...';
            btn.disabled = true;

            fetch("{{ route('scans.denoise', $scan->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || "Gagal memproses gambar");
                return data;
            })
            .then(data => {
                img1.src = data.image_url;
                img1.style.filter = 'none';
                alert('✨ Python Denoising Berhasil Diterapkan!');
            })
            .catch(error => {
                console.error(error);
                alert('Detail Eror:\n' + error.message);
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>

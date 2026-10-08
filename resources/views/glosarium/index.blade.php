<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glosarium Istilah Teknis — Hyu PACS</title>
    <meta name="description" content="Glosarium lengkap singkatan dan istilah teknis yang digunakan dalam sistem Hyu PACS, mencakup kualitas citra, algoritma denoising, DICOM, dan sistem.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg:           #060911;
            --surface:      #0c121e;
            --surface-el:   #111a2c;
            --border:       #1a2538;
            --border-focus: #0284c7;
            --text-main:    #f1f5f9;
            --text-muted:   #94a3b8;
            --text-faint:   #54657e;
            --blue:         #0284c7;
            --blue-light:   #38bdf8;
            --cyan:         #06b6d4;
            --emerald:      #10b981;
            --amber:        #f59e0b;
            --purple:       #a855f7;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* ── TOP NAV ──────────────────────────────────────────────── */
        .gl-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(6, 9, 17, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .gl-nav-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .gl-brand-logo {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: white;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.9rem;
        }

        .gl-brand-name {
            font-weight: 700;
            font-size: 1rem;
            color: var(--text-main);
        }

        .gl-brand-sub {
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-top: 0.05rem;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.9rem;
            border-radius: 7px;
            border: 1px solid var(--border);
            background: var(--surface-el);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.15s;
        }

        .btn-back:hover {
            border-color: #334155;
            color: var(--text-main);
            background: #1e293b;
        }

        /* ── HERO ─────────────────────────────────────────────────── */
        .gl-hero {
            background: linear-gradient(160deg, #0c121e 0%, #0a1525 60%, #060911 100%);
            border-bottom: 1px solid var(--border);
            padding: 2.5rem 1.5rem 2rem;
            text-align: center;
        }

        .gl-hero h1 {
            font-size: 1.9rem;
            font-weight: 800;
            background: linear-gradient(135deg, #38bdf8, #06b6d4, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .gl-hero p {
            font-size: 0.88rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 1.5rem;
            line-height: 1.6;
        }

        .gl-disclaimer {
            display: inline-flex;
            align-items: flex-start;
            gap: 0.5rem;
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.25);
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-size: 0.78rem;
            color: #fbbf24;
            max-width: 640px;
            text-align: left;
            line-height: 1.5;
        }

        /* ── CONTROLS ─────────────────────────────────────────────── */
        .gl-controls {
            max-width: 960px;
            margin: 1.5rem auto 0;
            padding: 0 1.5rem;
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .gl-search-wrap {
            position: relative;
            flex: 1;
            min-width: 200px;
        }

        .gl-search-wrap svg {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-faint);
            pointer-events: none;
        }

        .gl-search {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 0.55rem 0.85rem 0.55rem 2.4rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.15s;
        }

        .gl-search:focus { border-color: var(--border-focus); }
        .gl-search::placeholder { color: var(--text-faint); }

        .gl-filter-tabs {
            display: flex;
            gap: 0.3rem;
            background: rgba(12, 18, 30, 0.7);
            padding: 0.25rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            flex-wrap: wrap;
        }

        .gl-tab {
            padding: 0.3rem 0.7rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .gl-tab:hover { color: var(--text-main); }

        .gl-tab.active {
            background: var(--blue);
            color: #fff;
        }

        .gl-count {
            font-size: 0.75rem;
            color: var(--text-faint);
            padding: 0.3rem 0.6rem;
            white-space: nowrap;
        }

        /* ── MAIN LIST ────────────────────────────────────────────── */
        .gl-main {
            max-width: 960px;
            margin: 1.5rem auto 4rem;
            padding: 0 1.5rem;
        }

        .gl-category-group { margin-bottom: 2.5rem; }

        .gl-category-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
        }

        .gl-category-badge {
            padding: 0.2rem 0.55rem;
            border-radius: 5px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .cat-image_quality  { background: rgba(6, 182, 212, 0.15); color: #22d3ee; border: 1px solid rgba(6, 182, 212, 0.3); }
        .cat-algorithm      { background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }
        .cat-radiology      { background: rgba(2, 132, 199, 0.15);  color: #38bdf8; border: 1px solid rgba(2, 132, 199, 0.3); }
        .cat-system         { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }

        .gl-category-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .gl-category-count {
            font-size: 0.75rem;
            color: var(--text-faint);
            margin-left: auto;
        }

        /* ── ENTRY CARD ───────────────────────────────────────────── */
        .gl-entry {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1.25rem 1.4rem;
            margin-bottom: 0.75rem;
            transition: border-color 0.2s, background 0.2s;
            scroll-margin-top: 80px; /* offset for sticky nav */
        }

        .gl-entry:hover {
            border-color: #334155;
            background: var(--surface-el);
        }

        /* Highlight when scrolled to via hash */
        .gl-entry.highlighted {
            border-color: var(--blue-light);
            background: rgba(2, 132, 199, 0.07);
            animation: highlightFade 2.5s ease forwards;
        }

        @keyframes highlightFade {
            0%   { border-color: var(--blue-light); background: rgba(2, 132, 199, 0.12); }
            70%  { border-color: var(--blue-light); background: rgba(2, 132, 199, 0.07); }
            100% { border-color: var(--border);     background: var(--surface); }
        }

        .gl-entry-head {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
        }

        .gl-abbrev {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--blue-light);
            font-family: 'JetBrains Mono', monospace;
            white-space: nowrap;
        }

        .gl-fullname {
            font-size: 0.82rem;
            color: var(--text-muted);
            font-style: italic;
            padding-top: 0.15rem;
            flex: 1;
        }

        .gl-entry-body { font-size: 0.84rem; line-height: 1.65; color: var(--text-muted); }

        .gl-section-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--text-faint);
            margin-top: 0.75rem;
            margin-bottom: 0.2rem;
        }

        .gl-formula {
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 0.45rem 0.75rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.78rem;
            color: #a5f3fc;
            margin-top: 0.25rem;
            display: inline-block;
        }

        .gl-how-to-read {
            margin-top: 0.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.4rem;
            font-size: 0.8rem;
            color: #34d399;
        }

        .gl-refs {
            margin-top: 0.6rem;
        }

        .gl-ref-item {
            font-size: 0.73rem;
            color: var(--text-faint);
            font-style: italic;
        }

        .gl-ref-item::before { content: '› '; }

        /* ── EMPTY STATE ──────────────────────────────────────────── */
        .gl-empty {
            text-align: center;
            padding: 4rem 1rem;
            color: var(--text-faint);
        }

        .gl-empty h3 { font-size: 1.1rem; color: var(--text-muted); margin-bottom: 0.5rem; }

        /* ── RESPONSIVE ───────────────────────────────────────────── */
        @media (max-width: 640px) {
            .gl-hero h1 { font-size: 1.4rem; }
            .gl-controls { gap: 0.5rem; }
            .gl-entry { padding: 1rem; }
        }
    </style>
</head>
<body>

    <!-- ── TOP NAV ──────────────────────────────────────────────────── -->
    <nav class="gl-nav">
        <div class="gl-nav-brand">
            <span class="gl-brand-logo">Hyu</span>
            <div>
                <div class="gl-brand-name">Glosarium Istilah Teknis</div>
                <div class="gl-brand-sub">Hyu PACS — Referensi Singkatan & Terminologi</div>
            </div>
        </div>
        <a href="{{ route('scans.index') }}" class="btn-back">
            ← Kembali ke Dashboard
        </a>
    </nav>

    <!-- ── HERO ─────────────────────────────────────────────────────── -->
    <section class="gl-hero">
        <h1>📖 Glosarium Hyu PACS</h1>
        <p>
            Referensi lengkap singkatan, metrik, protokol, dan istilah teknis
            yang digunakan dalam sistem Hyu PACS.
            Gunakan kotak pencarian atau filter kategori di bawah.
        </p>
        <div class="gl-disclaimer">
            ⚠️ <span><strong>Catatan Klinis:</strong> Semua metrik pada glosarium ini (PSNR, SSIM, SNR, dll.)
            adalah ukuran <em>kualitas teknis citra</em> — bukan penilaian diagnostik klinis.
            Interpretasi medis atas citra radiologi harus dilakukan oleh radiolog bersertifikat.</span>
        </div>
    </section>

    <!-- ── CONTROLS ─────────────────────────────────────────────────── -->
    <div class="gl-controls">
        <div class="gl-search-wrap">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
            </svg>
            <input
                type="text"
                id="glSearch"
                class="gl-search"
                placeholder="Cari singkatan atau istilah..."
                aria-label="Cari istilah"
                autocomplete="off"
            >
        </div>

        <div class="gl-filter-tabs" role="group" aria-label="Filter kategori">
            <button class="gl-tab active" data-cat="all" onclick="filterCategory('all')">Semua</button>
            <button class="gl-tab" data-cat="image_quality" onclick="filterCategory('image_quality')">Kualitas Citra</button>
            <button class="gl-tab" data-cat="algorithm"     onclick="filterCategory('algorithm')">Algoritma</button>
            <button class="gl-tab" data-cat="radiology"     onclick="filterCategory('radiology')">Radiologi &amp; DICOM</button>
            <button class="gl-tab" data-cat="system"        onclick="filterCategory('system')">Sistem &amp; Umum</button>
        </div>

        <span class="gl-count" id="glCount" aria-live="polite"></span>
    </div>

    <!-- ── MAIN CONTENT ──────────────────────────────────────────────── -->
    <main class="gl-main" id="glMain" aria-label="Daftar istilah glosarium">
        {{-- Rendered by JS from glossary-data.js --}}
        <div class="gl-empty" id="glLoading">
            <p>Memuat glosarium…</p>
        </div>
    </main>

    <!-- Load centralized data source -->
    <script src="{{ asset('js/glossary-data.js') }}?v={{ time() }}"></script>

    <script>
        /* ──────────────────────────────────────────────────────────────
         * GLOSSARY PAGE — Render, Search, Filter, Highlight
         * ────────────────────────────────────────────────────────────── */

        const CATEGORY_ICONS = {
            image_quality: '📊',
            algorithm:     '🤖',
            radiology:     '🏥',
            system:        '⚙️',
        };

        let currentCategory = 'all';
        let currentSearch   = '';

        /* ── Build & Render ───────────────────────────────────────── */
        function renderGlossary() {
            const query   = currentSearch.toLowerCase().trim();
            const catFilter = currentCategory;

            // Filter entries
            const filtered = GLOSSARY_DATA.filter(entry => {
                const matchCat  = catFilter === 'all' || entry.category === catFilter;
                const matchTerm = !query ||
                    entry.abbreviation.toLowerCase().includes(query) ||
                    entry.fullName.toLowerCase().includes(query) ||
                    entry.summary.toLowerCase().includes(query) ||
                    (entry.detailedExplanation || '').toLowerCase().includes(query);
                return matchCat && matchTerm;
            });

            // Group by category (preserve order)
            const catOrder = ['image_quality', 'algorithm', 'radiology', 'system'];
            const grouped  = {};
            catOrder.forEach(c => { grouped[c] = []; });
            filtered.forEach(e => {
                if (grouped[e.category]) grouped[e.category].push(e);
            });

            const main = document.getElementById('glMain');
            const countEl = document.getElementById('glCount');

            if (filtered.length === 0) {
                main.innerHTML = `
                    <div class="gl-empty">
                        <h3>Tidak ada istilah yang ditemukan</h3>
                        <p>Coba kata kunci yang berbeda atau hapus filter kategori.</p>
                    </div>`;
                countEl.textContent = '0 istilah';
                return;
            }

            countEl.textContent = `${filtered.length} istilah`;

            let html = '';
            catOrder.forEach(cat => {
                const entries = grouped[cat];
                if (!entries || entries.length === 0) return;

                const catName  = GLOSSARY_CATEGORIES[cat];
                const catIcon  = CATEGORY_ICONS[cat];

                html += `
                <div class="gl-category-group" data-cat="${cat}">
                    <div class="gl-category-header">
                        <span class="gl-category-badge cat-${cat}">${catName}</span>
                        <span class="gl-category-title">${catIcon} ${catName}</span>
                        <span class="gl-category-count">${entries.length} entri</span>
                    </div>`;

                entries.forEach(e => {
                    html += buildEntryHTML(e, query);
                });

                html += `</div>`;
            });

            main.innerHTML = html;

            // Scroll to & highlight hash target (if any)
            highlightHashEntry();
        }

        /* ── Build single entry card ──────────────────────────────── */
        function buildEntryHTML(e, query) {
            const highlight = (text) => {
                if (!query) return escHtml(text);
                const re = new RegExp(`(${escRegex(query)})`, 'gi');
                return escHtml(text).replace(re, '<mark style="background:rgba(56,189,248,0.25);color:#f1f5f9;border-radius:2px;">$1</mark>');
            };

            let body = `
                <div class="gl-entry-body">
                    <div>${highlight(e.detailedExplanation || e.summary)}</div>`;

            if (e.formula) {
                body += `
                    <div class="gl-section-label">Rumus / Formula</div>
                    <div class="gl-formula">${escHtml(e.formula)}</div>`;
            }

            if (e.howToRead) {
                body += `
                    <div class="gl-section-label">Cara Membaca Nilai</div>
                    <div class="gl-how-to-read">✅ ${escHtml(e.howToRead)}</div>`;
            }

            if (e.references && e.references.length > 0) {
                body += `<div class="gl-section-label">Referensi</div><div class="gl-refs">`;
                e.references.forEach(r => {
                    body += `<div class="gl-ref-item">${escHtml(r)}</div>`;
                });
                body += `</div>`;
            }

            body += `</div>`;

            return `
            <div class="gl-entry" id="entry-${escHtml(e.id)}" role="article">
                <div class="gl-entry-head">
                    <span class="gl-abbrev">${highlight(e.abbreviation)}</span>
                    <span class="gl-fullname">${highlight(e.fullName)}</span>
                    <span class="gl-category-badge cat-${e.category}" style="font-size:0.68rem;">${GLOSSARY_CATEGORIES[e.category]}</span>
                </div>
                ${body}
            </div>`;
        }

        /* ── Scroll to & highlight entry from URL hash ──────────── */
        function highlightHashEntry() {
            const hash = window.location.hash; // e.g. "#psnr"
            if (!hash) return;

            const termId = hash.replace('#', '');
            const el     = document.getElementById(`entry-${termId}`);
            if (!el) return;

            // Defer so layout is painted first
            requestAnimationFrame(() => {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                el.classList.add('highlighted');
                // Remove class after animation so hover effect works again
                el.addEventListener('animationend', () => el.classList.remove('highlighted'), { once: true });
            });
        }

        /* ── Category filter ─────────────────────────────────────── */
        function filterCategory(cat) {
            currentCategory = cat;
            document.querySelectorAll('.gl-tab').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.cat === cat);
            });
            renderGlossary();
        }

        /* ── Search ──────────────────────────────────────────────── */
        let searchTimer;
        document.getElementById('glSearch').addEventListener('input', function () {
            clearTimeout(searchTimer);
            const val = this.value;
            searchTimer = setTimeout(() => {
                currentSearch = val;
                renderGlossary();
            }, 200);
        });

        /* ── Hash change (browser back/forward) ──────────────────── */
        window.addEventListener('hashchange', highlightHashEntry);

        /* ── Helpers ─────────────────────────────────────────────── */
        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function escRegex(str) {
            return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        /* ── Initial render ──────────────────────────────────────── */
        renderGlossary();
    </script>
</body>
</html>

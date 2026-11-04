import matplotlib.pyplot as plt
import matplotlib.patches as patches

# Set publication-quality typography
plt.rcParams['font.family'] = 'serif'
plt.rcParams['font.serif'] = ['Times New Roman', 'DejaVu Serif']
plt.rcParams['mathtext.fontset'] = 'stix'

fig, ax = plt.subplots(figsize=(17.5, 11.2), dpi=300)
ax.set_xlim(0, 17.5)
ax.set_ylim(0, 11.2)
ax.axis('off')

# Main Header / Title
ax.text(8.75, 10.85, 'DIAGRAM ARSITEKTUR & ALUR KERJA PERANGKAT LUNAK HYU PACS WORKSTATION', 
        ha='center', va='top', fontsize=13.5, fontweight='bold', color='#0f172a')
ax.plot([0.8, 16.7], [10.45, 10.45], color='#cbd5e1', lw=1.5)

# Helper function to create clean rounded cards
def draw_card(ax, x, y, w, h, header_text, bg_color='#ffffff', border_color='#2563eb', 
              header_color='#1e40af', header_h=0.58, radius=0.14):
    card = patches.FancyBboxPatch((x, y), w, h, boxstyle=f"round,pad={radius}",
                                  edgecolor=border_color, facecolor=bg_color, linewidth=1.6, zorder=2)
    ax.add_patch(card)
    hbar = patches.FancyBboxPatch((x, y + h - header_h), w, header_h, boxstyle=f"round,pad={radius*0.6}",
                                  edgecolor='none', facecolor=header_color, zorder=3)
    ax.add_patch(hbar)
    ax.text(x + w/2, y + h - header_h/2, header_text, ha='center', va='center', 
            fontsize=9.2, fontweight='bold', color='white', zorder=4)

# ==============================================================================
# BARIS ATAS (ROW 1): INGESTI & MANAJEMEN DATA MEDIS (y = 6.4 to 9.9, h = 3.5)
# ==============================================================================

# ----------------- 0. MODALITAS RONTGEN (KLINIK CDC SURABAYA) -----------------
draw_card(ax, 0.8, 6.4, 3.5, 3.5, 'MODALITAS RONTGEN DR', 
          bg_color='#f8fafc', border_color='#64748b', header_color='#475569')

ax.text(2.55, 9.15, 'Konsol Radiografi Digital\n(Klinik CDC Surabaya)', 
        ha='center', va='top', fontsize=8.5, fontweight='bold', color='#1e293b')
ax.plot([1.0, 4.1], [8.65, 8.65], color='#cbd5e1', lw=0.9)

modality_text = (
    "• Detektor Flat-Panel DR Medis\n"
    "• Standar Format Citra DICOM 3.0\n"
    "• Transmisi TCP/IP (Kabel LAN Klinik)\n"
    "• SOP Class Terdaftar:\n"
    "  - Computed Radiography (CR)\n"
    "  - Digital X-Ray (DX Image Storage)\n"
    "• Tag Kalibrasi Sensor Fisik:\n"
    "  - ImagerPixelSpacing (0.140 mm/px)\n"
    "  - Photometric: MONOCHROME2\n"
    "• Resolusi Matriks: 2999 × 2999 piksel"
)
ax.text(1.0, 8.48, modality_text, ha='left', va='top', fontsize=7.8, color='#334155', linespacing=1.3)

# ----------------- 1. MODUL 1: DICOM LISTENER (C-STORE SCP) -----------------
draw_card(ax, 5.6, 6.4, 4.1, 3.5, 'MODUL 1: DICOM LISTENER (SCP DAEMON)', 
          bg_color='#f0f9ff', border_color='#0284c7', header_color='#0369a1')

ax.text(7.65, 9.15, 'Daemon Service (pynetdicom)\nPort: 4242 | AE Title: HYU_PACS_SERVER', 
        ha='center', va='top', fontsize=8.4, fontweight='bold', color='#0c4a6e')
ax.plot([5.8, 9.5], [8.65, 8.65], color='#bae6fd', lw=0.9)

m1_text = (
    "• C-STORE SCP Service (NEMA PS 3.4)\n"
    "• Listening Port 4242 (Background Daemon Task)\n"
    "• Validasi SOP Class UID & Transfer Syntax Medis\n"
    "• Ekstraksi Tag Header Metadata (pydicom):\n"
    "  - Tag (0028,0030) Pixel Spacing (mm/px)\n"
    "  - Tag (0028,1052/53) Rescale Intercept / Slope\n"
    "  - Tag (0028,1050/51) Window Center & Width\n"
    "• Pengarsipan Berkas Mentah (.dcm) ke\n"
    "  Direktori Penyimpanan Terproteksi Server"
)
ax.text(5.8, 8.48, m1_text, ha='left', va='top', fontsize=7.8, color='#075985', linespacing=1.3)

# ----------------- 2. MODUL 2: BACKEND & DATABASE (LARAVEL 11) -----------------
draw_card(ax, 11.0, 6.4, 5.5, 3.5, 'MODUL 2: BACKEND & DATABASE (LARAVEL 11)', 
          bg_color='#fef2f2', border_color='#dc2626', header_color='#b91c1c')

ax.text(13.75, 9.15, 'Framework Web & Basis Data Relasional EMR', 
        ha='center', va='top', fontsize=8.5, fontweight='bold', color='#7f1d1d')
ax.plot([11.2, 16.3], [8.65, 8.65], color='#fecaca', lw=0.9)

m2_text = (
    "• Manajemen Basis Data Relasional Pasien MCU Klinik:\n"
    "  - Nama Pasien, NIK, No. Rekam Medis (RM), Usia, Gender\n"
    "  - Study Instance UID, Series Instance UID, Accession Number\n"
    "• Sistem Otentikasi & Otorisasi Pengguna Berbasis Peran (Role-Based)\n"
    "• Sinkronisasi Jadwal Pemeriksaan Modality Worklist (MWL)\n"
    "• RESTful API Endpoint & Layanan DICOM Streaming Service\n"
    "• Controller Logik Manajemen Rekam Medis Elektronik (EMR)\n"
    "• Integrasi Alur Data Ekspertise Diagnostik Dokter Spesialis Radiologi"
)
ax.text(11.25, 8.48, m2_text, ha='left', va='top', fontsize=7.8, color='#991b1b', linespacing=1.3)

# ==============================================================================
# BARIS BAWAH (ROW 2): WORKSTATION VIEWER, AI ENGINE, & REPORTING (y = 0.8 to 4.8, h = 4.0)
# ==============================================================================

# ----------------- 3. MODUL 3: ENGINE RESTORASI CITRA & AI (PYTHON) -----------------
draw_card(ax, 0.8, 0.8, 3.8, 4.0, 'MODUL 3: ENGINE RESTORASI & AI (PYTHON)', 
          bg_color='#f0fdf4', border_color='#059669', header_color='#047857', header_h=0.58)

ax.text(2.7, 4.05, 'Multi-Algorithm Restoration Engine', 
        ha='center', va='top', fontsize=8.4, fontweight='bold', color='#064e3b')
ax.plot([1.0, 4.4], [3.8, 3.8], color='#a7f3d0', lw=0.9)

m3_text = (
    "1. Bilateral Filtering (Tomasi & Manduchi, 1998):\n"
    "   - Domain Spasial (σ_s) & Radiometrik Range (σ_r)\n"
    "   - Preservasi Kontur Batas Toraks & Jantung\n"
    "2. Non-Local Means / NLM (Buades et al., 2005):\n"
    "   - Redundansi Patch Citra (Self-Exemplar)\n"
    "3. Deep Residual CNN / DnCNN (Zhang et al., 2017):\n"
    "   - 17-Lapisan Konvolusi (Conv + BN + ReLU)\n"
    "   - Residual Noise Learning: x = y - R(y)\n"
    "   - Inferensi Model PyTorch Teroptimasi"
)
ax.text(0.95, 3.68, m3_text, ha='left', va='top', fontsize=7.6, color='#065f46', linespacing=1.23)

# Telemetry Box inside Modul 3
telemetry_box = patches.FancyBboxPatch((0.95, 0.95), 3.5, 1.18, boxstyle="round,pad=0.08",
                                       edgecolor='#10b981', facecolor='#ecfdf5', lw=1.2, zorder=3)
ax.add_patch(telemetry_box)
ax.text(2.7, 1.98, 'Telemetri Real-Time (JSON Response):', 
        ha='center', va='top', fontsize=7.7, fontweight='bold', color='#064e3b', zorder=4)
tele_items = (
    "• Peak Signal-to-Noise Ratio (PSNR dalam dB)\n"
    "• Structural Similarity Index Measure (SSIM)\n"
    "• Signal-to-Noise Ratio Gain (SNR Gain dB)\n"
    "• Latensi Waktu Komputasi Filter (Milidetik)"
)
ax.text(1.1, 1.74, tele_items, ha='left', va='top', fontsize=7.4, color='#047857', linespacing=1.2, zorder=4)

# ----------------- 4. MODUL 4: WEB DIAGNOSTIC WORKSTATION (HYU PACS VIEWER) -----------------
draw_card(ax, 5.7, 0.8, 6.4, 4.0, 'MODUL 4: WEB DIAGNOSTIC WORKSTATION (HYU PACS VIEWER)', 
          bg_color='#f8fafc', border_color='#4338ca', header_color='#3730a3', header_h=0.58)

ax.text(8.9, 4.05, 'Zero-Footprint Clinical Viewer (HTML5 + CSS + Custom Modern JavaScript)', 
        ha='center', va='top', fontsize=8.4, fontweight='bold', color='#1e1b4b')

# Sub-panel 4A: SVG Stage Layer
card_4a = patches.FancyBboxPatch((5.85, 0.95), 2.95, 2.85, boxstyle="round,pad=0.08",
                                 edgecolor='#a5b4fc', facecolor='#ffffff', lw=1.3, zorder=3)
ax.add_patch(card_4a)
ax.text(7.32, 3.65, 'A. SVG Stage Layer (.vp-stage-layer)', 
        ha='center', va='top', fontsize=8.0, fontweight='bold', color='#312e81', zorder=4)
ax.plot([6.0, 8.65], [3.42, 3.42], color='#e0e7ff', lw=0.9, zorder=4)

m4a_text = (
    "• Pemetaan CTM Terbalik (CTM⁻¹)\n"
    "• Lockstep Annotation Anchoring:\n"
    "  - Anotasi linier, lingkaran, & ROI\n"
    "    terkunci mutlak saat Zoom & Pan\n"
    "• Manipulasi Transformasi Citra:\n"
    "  - Rotasi Bebas (90°, 180°, 270°)\n"
    "  - Pembalikan Flip (H / V)\n"
    "  - Windowing Kontras/Brightness (W/L)\n"
    "• Interaksi Seleksi Klik & Hapus Instan\n"
    "• Visualisasi Klinis Komparatif:\n"
    "  - Split-Curtain Comparison Slider\n"
    "  - Difference Map (|I_noisy - I_clean|)"
)
ax.text(6.0, 3.32, m4a_text, ha='left', va='top', fontsize=7.4, color='#1e1b4b', linespacing=1.21, zorder=4)

# Sub-panel 4B: Kaliper Biometri CTR
card_4b = patches.FancyBboxPatch((8.95, 0.95), 3.0, 2.85, boxstyle="round,pad=0.08",
                                 edgecolor='#86efac', facecolor='#ffffff', lw=1.3, zorder=3)
ax.add_patch(card_4b)
ax.text(10.45, 3.65, 'B. Kaliper Biometri CTR (Danzer, 1919)', 
        ha='center', va='top', fontsize=8.0, fontweight='bold', color='#065f46', zorder=4)
ax.plot([9.1, 11.8], [3.42, 3.42], color='#dcfce7', lw=0.9, zorder=4)

m4b_text = (
    "• Kalibrasi Fisik Tag (0028,0030)\n"
    "  Pixel Spacing Sensor (mm/px)\n"
    "• Penarikan Garis Dua Langkah:\n"
    "  - Step 1: Garis Jantung A+B (Merah)\n"
    "  - Step 2: Garis Toraks C (Kuning)\n"
    "• Otomasi Formula CTR Danzer:\n"
    "    CTR = ((A + B) / C) × 100%\n"
    "• Klasifikasi Diagnostik Klinis:\n"
    "  - Normal (CTR ≤ 50%)\n"
    "  - Kardiomegali (CTR > 50%)\n"
    "• Visual HUD Badge & Sync ke EMR"
)
ax.text(9.1, 3.32, m4b_text, ha='left', va='top', fontsize=7.4, color='#064e3b', linespacing=1.22, zorder=4)

# ----------------- 5. MODUL 5: PELAPORAN & EKSPOR (NEMA PS 3.3) -----------------
draw_card(ax, 13.3, 0.8, 3.4, 4.0, 'MODUL 5: PELAPORAN & EKSPOR (PS 3.3)', 
          bg_color='#fffbeb', border_color='#d97706', header_color='#b45309', header_h=0.58)

ax.text(15.0, 4.05, 'Sistem Pelaporan & Output Klinis', 
        ha='center', va='top', fontsize=8.4, fontweight='bold', color='#78350f')
ax.plot([13.5, 16.5], [3.8, 3.8], color='#fde68a', lw=0.9)

m5_text = (
    "• Live Insertion Template Ringkasan\n"
    "  Ekspertise Radiologi ke Lembar EMR\n"
    "• Penerbitan Lembar Hasil Pemeriksaan\n"
    "  Format Dokumen Medis Siap Cetak (PDF)\n"
    "• Ekspor Citra Secondary Capture (SC):\n"
    "  - Burn-In Overlay Identitas Pasien & RM\n"
    "  - Burn-In Garis Ukur CTR (A, B, C)\n"
    "  - Burn-In Nilai Rasio (%) & Status\n"
    "• Kepatuhan Standar Medis NEMA PS 3.3"
)
ax.text(13.5, 3.65, m5_text, ha='left', va='top', fontsize=7.8, color='#92400e', linespacing=1.35)

# ==============================================================================
# ALUR KONEKSI & PANAH DATA FLOW (BERSIH, TEGAK LURUS, DAN LAPANG)
# ==============================================================================

# 1. Modality -> Modul 1 (Horizontal Row 1: gap 4.3 to 5.6 = 1.3)
ax.annotate('', xy=(5.6, 8.15), xytext=(4.3, 8.15),
            arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#0284c7', mutation_scale=16), zorder=6)
ax.text(4.95, 8.35, 'DICOM C-STORE\n(Kabel LAN)', ha='center', va='bottom', 
        fontsize=7.8, fontweight='bold', color='#0284c7', zorder=7)

# 2. Modul 1 -> Modul 2 (Horizontal Row 1: gap 9.7 to 11.0 = 1.3)
ax.annotate('', xy=(11.0, 8.15), xytext=(9.7, 8.15),
            arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#0284c7', mutation_scale=16), zorder=6)
ax.text(10.35, 8.35, 'Ekstraksi Metadata\n& File Path .dcm', ha='center', va='bottom', 
        fontsize=7.6, fontweight='bold', color='#0284c7', zorder=7)

# 3. Modul 2 -> Modul 4 (Tegak Lurus Vertikal: x = 11.4 dari y = 6.4 ke y = 4.8)
ax.annotate('', xy=(11.4, 4.8), xytext=(11.4, 6.4),
            arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#4338ca', mutation_scale=16), zorder=6)
ax.text(11.2, 5.6, 'Stream Citra Raw & Tag Kalibrasi\n(Base64 Data URI / REST API)', 
        ha='right', va='center', fontsize=7.6, fontweight='bold', color='#4338ca', zorder=7)

# 4. Modul 2 -> Modul 5 (Tegak Lurus Vertikal: x = 15.0 dari y = 6.4 ke y = 4.8)
ax.annotate('', xy=(15.0, 4.8), xytext=(15.0, 6.4),
            arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#dc2626', mutation_scale=16), zorder=6)
ax.text(15.2, 5.6, 'Demografi Pasien MCU\n& Template EMR', 
        ha='left', va='center', fontsize=7.6, fontweight='bold', color='#b91c1c', zorder=7)

# 5. Modul 4 <-> Modul 3 (Bidirectional Horizontal Row 2: gap 4.6 to 5.7 = 1.1)
ax.annotate('', xy=(4.6, 2.7), xytext=(5.7, 2.7),
            arrowprops=dict(arrowstyle="<|-|>", lw=2.2, color='#059669', mutation_scale=16), zorder=6)
ax.text(5.15, 2.95, 'Request Filter &\nCitra Restorasi\n+ Telemetri', ha='center', va='bottom', 
        fontsize=7.3, fontweight='bold', color='#047857', zorder=7)

# 6. Modul 4 -> Modul 5 (Horizontal Row 2: gap 12.1 to 13.3 = 1.2)
ax.annotate('', xy=(13.3, 2.7), xytext=(12.1, 2.7),
            arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#d97706', mutation_scale=16), zorder=6)
ax.text(12.7, 2.95, 'Hasil Ukur CTR\n& Anotasi Final', ha='center', va='bottom', 
        fontsize=7.3, fontweight='bold', color='#b45309', zorder=7)

plt.tight_layout()
out_file = 'Gambar_3_4_Diagram_Alur_Kerja_Hyu_PACS.png'
plt.savefig(out_file, dpi=300, bbox_inches='tight')
print('Successfully regenerated clean Gambar 3.4:', out_file)

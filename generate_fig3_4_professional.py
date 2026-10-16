import matplotlib.pyplot as plt
import matplotlib.patches as patches

# Publication typography setup
plt.rcParams['font.family'] = 'serif'
plt.rcParams['font.serif'] = ['Times New Roman', 'DejaVu Serif']
plt.rcParams['mathtext.fontset'] = 'stix'

fig, ax = plt.subplots(figsize=(13.0, 8.4), dpi=300)
ax.set_xlim(0, 13.0)
ax.set_ylim(0, 8.4)
ax.axis('off')

# Main Header / Title (Academic Style)
ax.text(6.5, 8.25, 'DIAGRAM ARSITEKTUR & ALUR KERJA SISTEM HYU PACS WORKSTATION', 
        ha='center', va='top', fontsize=12.5, fontweight='bold', color='#0f172a')
ax.text(6.5, 8.00, 'Arsitektur Integrasi Modalitas Radiografi Digital, Core Backend EMR, Engine AI, dan Workstation Diagnostik Klinis', 
        ha='center', va='top', fontsize=8.6, fontstyle='italic', color='#475569')
ax.plot([0.5, 12.5], [7.88, 7.88], color='#64748b', lw=1.2)

# Helper: Draw Academic Tier Header Bar
def draw_tier_bar(ax, y, text_label):
    bar = patches.Rectangle((0.5, y - 0.26), 12.0, 0.26, facecolor='#f8fafc', edgecolor='#94a3b8', lw=1.0, zorder=1)
    ax.add_patch(bar)
    # Accent indicator bar on the left
    ax.plot([0.5, 0.5], [y - 0.26, y], color='#0f172a', lw=4.5, zorder=2)
    ax.text(0.68, y - 0.13, text_label, ha='left', va='center', 
            fontsize=8.4, fontweight='bold', color='#0f172a', zorder=3)

# Helper: Draw Academic Solid Module Card (Unified Engineering Style)
def draw_card(ax, x, y, w, h, header_text, subheader_text, header_bg='#1e293b'):
    card = patches.Rectangle((x, y), w, h, facecolor='#ffffff', edgecolor='#334155', lw=1.2, zorder=2)
    ax.add_patch(card)
    # Header bar
    hbar = patches.Rectangle((x, y + h - 0.36), w, 0.36, facecolor=header_bg, edgecolor='#334155', lw=1.2, zorder=3)
    ax.add_patch(hbar)
    ax.text(x + w/2, y + h - 0.18, header_text, ha='center', va='center', 
            fontsize=9.4, fontweight='bold', color='white', zorder=4)
    # Subheader bar
    if subheader_text:
        sbar = patches.Rectangle((x, y + h - 0.60), w, 0.24, facecolor='#f1f5f9', edgecolor='#cbd5e1', lw=0.8, zorder=3)
        ax.add_patch(sbar)
        ax.text(x + w/2, y + h - 0.48, subheader_text, ha='center', va='center', 
                fontsize=8.2, fontweight='bold', color='#1e293b', zorder=4)

# ==============================================================================
# TIER 1: LAPISAN AKUISISI CITRA & INGESTI MEDIS (y = 6.30 to 7.80)
# ==============================================================================
draw_tier_bar(ax, 7.80, 'TIER 1: LAPISAN AKUISISI CITRA & INGESTI TRANSMISI MEDIS (HARDWARE & DICOM SCP)')

# Card 0: Modalitas Rontgen DR
draw_card(ax, 0.5, 6.32, 5.2, 1.22, 'MODALITAS RADIOGRAFI DIGITAL (DR)', 
          'Konsol Radiografi Digital (Klinik CDC Surabaya)', header_bg='#1e293b')
mod0_items = (
    "• Detektor: Flat-Panel Detector (FPD) Medis | Standar Format: DICOM 3.0\n"
    "• SOP Class Terdaftar: Computed Radiography (CR) / Digital X-Ray (DX)\n"
    "• Resolusi Matriks: 2999 × 2999 Piksel | Kedalaman Citra: 16-bit Grayscale\n"
    "• Tag Kalibrasi Fisik Sensor: (0028,0030) ImagerPixelSpacing = 0.140 mm/px"
)
ax.text(0.65, 6.32 + 1.22 - 0.68, mod0_items, ha='left', va='top', fontsize=8.7, color='#0f172a', linespacing=1.28, zorder=5)

# Card 1: DICOM Listener SCP Daemon
draw_card(ax, 7.3, 6.32, 5.2, 1.22, 'MODUL 1: DICOM LISTENER (C-STORE SCP)', 
          'Daemon Service Latar Belakang (pynetdicom & pydicom)', header_bg='#0f4c81')
mod1_items = (
    "• Layanan: C-STORE SCP Standar Medis (NEMA PS 3.4) | Port Jaringan: 4242\n"
    "• AE Title: HYU_PACS_SERVER | Validasi SOP Class UID & Transfer Syntax\n"
    "• Ekstraksi Tag Kunci: Pixel Spacing, Rescale Intercept/Slope, Window W/L\n"
    "• Repositori Penyimpanan: Pengarsipan Berkas Mentah .dcm ke Direktori Server"
)
ax.text(7.45, 6.32 + 1.22 - 0.68, mod1_items, ha='left', va='top', fontsize=8.7, color='#0f172a', linespacing=1.28, zorder=5)

# Horizontal Arrow Tier 1 (Modul 0 -> Modul 1)
ax.annotate('', xy=(7.3, 6.93), xytext=(5.7, 6.93),
            arrowprops=dict(arrowstyle="-|>", lw=2.0, color='#0f4c81', mutation_scale=15), zorder=6)
b1 = patches.FancyBboxPatch((5.85, 7.03), 1.3, 0.38, boxstyle="round,pad=0.03", 
                            edgecolor='#0f4c81', facecolor='#f0f9ff', lw=1.1, zorder=7)
ax.add_patch(b1)
ax.text(6.5, 7.22, 'DICOM C-STORE\n(Port 4242 LAN)', ha='center', va='center', 
        fontsize=7.6, fontweight='bold', color='#0f4c81', zorder=8)

# Vertical Arrow Tier 1 -> Tier 2 (Modul 1 -> Modul 2)
ax.annotate('', xy=(9.9, 5.82), xytext=(9.9, 6.32),
            arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#0f4c81', mutation_scale=15), zorder=6)
bv1 = patches.FancyBboxPatch((8.6, 5.94), 2.6, 0.24, boxstyle="round,pad=0.03",
                             edgecolor='#0f4c81', facecolor='#ffffff', lw=1.1, zorder=7)
ax.add_patch(bv1)
ax.text(9.9, 6.06, 'Berkas .dcm & Metadata Header Terurai', ha='center', va='center', 
        fontsize=7.6, fontweight='bold', color='#0f4c81', zorder=8)

# ==============================================================================
# TIER 2: PENGELOLAAN DATA EMR & RESTORASI AI (y = 4.10 to 5.82)
# ==============================================================================
draw_tier_bar(ax, 5.82, 'TIER 2: LAPISAN PENGELOLAAN DATA EMR & KOMPUTASI RESTORASI AI')

# Card 3: Engine Restorasi Citra & AI (Python)
draw_card(ax, 0.5, 4.15, 5.4, 1.40, 'MODUL 3: ENGINE RESTORASI CITRA & AI', 
          'Multi-Algorithm Restoration Engine (Python CLI & API)', header_bg='#065f46')
mod3_items = (
    "• Bilateral Filter: Domain Spasial (σ_s) & Range (σ_r) Preservasi Tepi Batas\n"
    "• Non-Local Means (NLM): Redundansi Patch Citra Toraks (Self-Exemplar)\n"
    "• Deep Residual CNN (DnCNN): 17-Lapisan Konvolusi (Conv + BN + ReLU)\n"
    "  Residual Noise Learning: Rekonstruksi Citra Bersih x = y - R(y)\n"
    "• Telemetri Real-Time: PSNR (dB), SSIM, SNR Gain (dB), Latensi Eksekusi (ms)"
)
ax.text(0.65, 4.15 + 1.40 - 0.68, mod3_items, ha='left', va='top', fontsize=8.6, color='#0f172a', linespacing=1.28, zorder=5)

# Card 2: Backend & Database Relasional (Laravel 11)
draw_card(ax, 7.1, 4.15, 5.4, 1.40, 'MODUL 2: BACKEND & BASIS DATA RELASIONAL', 
          'Framework Web EMR & Manajemen Pasien (Laravel 11)', header_bg='#831843')
mod2_items = (
    "• Basis Data Relasional EMR: Pasien Medical Check Up (MCU) CDC Surabaya\n"
    "  Nama Pasien, NIK, No. Rekam Medis (RM), Usia, Gender, Study Instance UID\n"
    "• Keamanan Sistem: Otentikasi & Otorisasi Pengguna Berbasis Peran (Role-Based)\n"
    "• Integrasi Alur Kerja: Sinkronisasi Jadwal Modality Worklist (MWL)\n"
    "• Distribusi Data: RESTful API Endpoint & DICOM Streaming Controller"
)
ax.text(7.25, 4.15 + 1.40 - 0.68, mod2_items, ha='left', va='top', fontsize=8.6, color='#0f172a', linespacing=1.28, zorder=5)

# Horizontal Bidirectional Arrow Tier 2 (Modul 3 <-> Modul 2)
ax.annotate('', xy=(5.9, 4.85), xytext=(7.1, 4.85),
            arrowprops=dict(arrowstyle="<|-|>", lw=2.0, color='#065f46', mutation_scale=15), zorder=6)
b2 = patches.FancyBboxPatch((5.85, 4.96), 1.3, 0.38, boxstyle="round,pad=0.03", 
                            edgecolor='#065f46', facecolor='#ecfdf5', lw=1.1, zorder=7)
ax.add_patch(b2)
ax.text(6.5, 5.15, 'Request Filter &\nCitra + Telemetri', ha='center', va='center', 
        fontsize=7.4, fontweight='bold', color='#065f46', zorder=8)

# Vertical Arrow Tier 2 -> Tier 3: Left side (Modul 3 -> Modul 4)
ax.annotate('', xy=(3.2, 3.45), xytext=(3.2, 4.15),
            arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#065f46', mutation_scale=15), zorder=6)
bv2a = patches.FancyBboxPatch((1.8, 3.65), 2.8, 0.24, boxstyle="round,pad=0.03",
                              edgecolor='#065f46', facecolor='#ffffff', lw=1.1, zorder=7)
ax.add_patch(bv2a)
ax.text(3.2, 3.77, 'Citra Terestorasi & Telemetri PSNR/SSIM', ha='center', va='center', 
        fontsize=7.6, fontweight='bold', color='#065f46', zorder=8)

# Vertical Arrow Tier 2 -> Tier 3: Right side (Modul 2 -> Modul 4 & Modul 5)
ax.annotate('', xy=(8.0, 3.45), xytext=(8.0, 4.15),
            arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#831843', mutation_scale=15), zorder=6)
bv2b = patches.FancyBboxPatch((6.8, 3.65), 2.4, 0.24, boxstyle="round,pad=0.03",
                              edgecolor='#831843', facecolor='#ffffff', lw=1.1, zorder=7)
ax.add_patch(bv2b)
ax.text(8.0, 3.77, 'Stream Citra Raw & Tag Kalibrasi', ha='center', va='center', 
        fontsize=7.6, fontweight='bold', color='#831843', zorder=8)

ax.annotate('', xy=(11.0, 3.45), xytext=(11.0, 4.15),
            arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#831843', mutation_scale=15), zorder=6)
bv2c = patches.FancyBboxPatch((9.8, 3.65), 2.4, 0.24, boxstyle="round,pad=0.03",
                              edgecolor='#831843', facecolor='#ffffff', lw=1.1, zorder=7)
ax.add_patch(bv2c)
ax.text(11.0, 3.77, 'Demografi Pasien MCU & EMR', ha='center', va='center', 
        fontsize=7.6, fontweight='bold', color='#831843', zorder=8)

# ==============================================================================
# TIER 3: WORKSTATION DIAGNOSTIK & PELAPORAN (y = 0.45 to 3.45)
# ==============================================================================
draw_tier_bar(ax, 3.45, 'TIER 3: LAPISAN WORKSTATION DIAGNOSTIK KLINIS & PELAPORAN MEDIS')

# Card 4: Web Diagnostic Workstation (Hyu PACS Viewer) - width 7.4
draw_card(ax, 0.5, 0.45, 7.4, 2.70, 'MODUL 4: WEB DIAGNOSTIC WORKSTATION (HYU PACS VIEWER)', 
          'Zero-Footprint Clinical Viewer (HTML5 + CSS + Custom Modern JavaScript)', header_bg='#1e1b4b')

# Sub-box 4A: SVG Stage Layer
box_4a = patches.Rectangle((0.65, 0.55), 3.50, 1.88, facecolor='#f8fafc', edgecolor='#cbd5e1', lw=1.0, zorder=3)
ax.add_patch(box_4a)
ax.text(2.40, 2.27, 'A. SVG Stage Layer (.vp-stage-layer)', ha='center', va='center', 
        fontsize=8.4, fontweight='bold', color='#312e81', zorder=4)
ax.plot([0.75, 4.05], [2.13, 2.13], color='#cbd5e1', lw=0.8, zorder=4)

m4a_items = (
    "• Pemetaan CTM Terbalik (CTM⁻¹)\n"
    "• Lockstep Anchoring: Koordinat garis ukur\n"
    "  terkunci mutlak saat Zoom-in/out & Pan/drag\n"
    "• Manipulasi Transformasi Citra Klinis:\n"
    "  - Rotasi Bebas (90°, 180°, 270°) & Flip H/V\n"
    "  - Windowing Kontras/Brightness (W/L)\n"
    "• Visualisasi Klinis Komparatif:\n"
    "  - Split-Curtain Comparison Slider\n"
    "  - Difference Map (|I_noisy - I_clean|)"
)
ax.text(0.75, 2.03, m4a_items, ha='left', va='top', fontsize=8.0, color='#0f172a', linespacing=1.24, zorder=5)

# Sub-box 4B: Kaliper CTR Danzer
box_4b = patches.Rectangle((4.25, 0.55), 3.50, 1.88, facecolor='#f8fafc', edgecolor='#cbd5e1', lw=1.0, zorder=3)
ax.add_patch(box_4b)
ax.text(6.00, 2.27, 'B. Kaliper Biometri CTR (Danzer, 1919)', ha='center', va='center', 
        fontsize=8.4, fontweight='bold', color='#065f46', zorder=4)
ax.plot([4.35, 7.65], [2.13, 2.13], color='#cbd5e1', lw=0.8, zorder=4)

m4b_items = (
    "• Kalibrasi Fisik Tag (0028,0030) Pixel Spacing\n"
    "• Penarikan Garis Interaktif Presisi 2 Langkah:\n"
    "  - Step 1: Garis Transversal Jantung A+B (Merah)\n"
    "  - Step 2: Diameter Transversal Toraks C (Kuning)\n"
    "• Otomasi Formula: CTR = ((A+B)/C) × 100%\n"
    "• Klasifikasi Otomatis Status Diagnostik:\n"
    "  - Normal (CTR ≤ 50%) | Kardiomegali (CTR > 50%)\n"
    "• Visual HUD Badge & Sync ke Lembar Ekspertise"
)
ax.text(4.35, 2.03, m4b_items, ha='left', va='top', fontsize=8.0, color='#0f172a', linespacing=1.24, zorder=5)

# Card 5: Pelaporan & Ekspor NEMA PS 3.3 - width 3.6, starts at 8.9 (gap 1.0 from Modul 4)
draw_card(ax, 8.9, 0.45, 3.6, 2.70, 'MODUL 5: PELAPORAN & EKSPOR', 
          'Output Medis (Standar NEMA PS 3.3)', header_bg='#92400e')
mod5_items = (
    "• Live Insertion Template Ringkasan\n"
    "  Ekspertise Radiologi ke Lembar EMR\n"
    "• Penerbitan Lembar Hasil Pemeriksaan\n"
    "  Format Dokumen Medis Siap Cetak (PDF)\n"
    "• Ekspor Citra Secondary Capture (SC):\n"
    "  - Burn-In Overlay Data Identitas Pasien\n"
    "  - Burn-In Garis Ukur CTR (A, B, C)\n"
    "  - Burn-In Nilai Rasio (%) & Status Klinis\n"
    "• Kepatuhan Penuh Standar Medis NEMA PS 3.3"
)
ax.text(9.05, 0.45 + 2.70 - 0.68, mod5_items, ha='left', va='top', fontsize=8.3, color='#0f172a', linespacing=1.35, zorder=5)

# Horizontal Arrow Tier 3 (Modul 4 -> Modul 5) in gap 7.9 to 8.9
ax.annotate('', xy=(8.9, 1.80), xytext=(7.9, 1.80),
            arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#92400e', mutation_scale=16), zorder=6)
b3 = patches.FancyBboxPatch((7.95, 1.92), 0.90, 0.38, boxstyle="round,pad=0.03", 
                            edgecolor='#92400e', facecolor='#fffbeb', lw=1.0, zorder=7)
ax.add_patch(b3)
ax.text(8.40, 2.11, 'Hasil CTR &\nCitra Anotasi', ha='center', va='center', 
        fontsize=7.3, fontweight='bold', color='#92400e', zorder=8)

plt.tight_layout()
out_file = 'Gambar_3_4_Diagram_Alur_Kerja_Hyu_PACS.png'
plt.savefig(out_file, dpi=300, bbox_inches='tight')
print('Successfully generated publication-grade compact Gambar 3.4:', out_file)

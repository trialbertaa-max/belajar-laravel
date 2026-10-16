import matplotlib.pyplot as plt
import matplotlib.patches as patches

# Set Publication Quality (Times New Roman, Academic Standard)
plt.rcParams['font.family'] = 'Times New Roman'
plt.rcParams['mathtext.fontset'] = 'stix'

# Create canvas in A4 Portrait proportion (11 x 14 inches at 300 DPI)
fig, ax = plt.subplots(figsize=(11, 14.5), dpi=300)
ax.set_facecolor('#FFFFFF')
fig.patch.set_facecolor('#FFFFFF')

# Canvas limits
ax.set_xlim(0, 110)
ax.set_ylim(0, 145)
ax.axis('off')

# Title Block
ax.text(55, 141.5, "GAMBAR 3.4. DIAGRAM ALUR KERJA & ARSITEKTUR HYU PACS WORKSTATION",
        fontsize=13, fontweight='bold', ha='center', va='center', color='#0F172A')
ax.text(55, 138.8, "Integrasi Ingesti Transmisi DICOM, Core Backend EMR, Engine AI, Diagnostik CTR, dan Pelaporan Medis",
        fontsize=10, fontstyle='italic', ha='center', va='center', color='#475569')
ax.plot([8, 102], [136.5, 136.5], color='#334155', linewidth=1.2)

# Helper Function: Draw Template Card (Dual-outline rounded card + colored pill header)
def draw_a4_card(ax, x, y, w, h, pill_color, border_color, title, subtitle, bullets):
    # Outer rounded rectangle
    outer_rect = patches.FancyBboxPatch(
        (x, y), w, h,
        boxstyle="round,pad=0.2,rounding_size=1.2",
        linewidth=2.0, edgecolor=border_color, facecolor='#FFFFFF', zorder=2
    )
    ax.add_patch(outer_rect)
    
    # Pill header badge
    pill_w = w - 4.5
    pill_h = 4.0
    pill_x = x + 2.25
    pill_y = y + h - pill_h - 1.6
    pill_rect = patches.FancyBboxPatch(
        (pill_x, pill_y), pill_w, pill_h,
        boxstyle="round,pad=0.2,rounding_size=2.0",
        linewidth=0, facecolor=pill_color, zorder=3
    )
    ax.add_patch(pill_rect)
    
    # Pill header text (Large, bold white)
    ax.text(x + w/2, pill_y + pill_h/2, title,
            fontsize=9.5, fontweight='bold', ha='center', va='center', color='#FFFFFF', zorder=4)
    
    # Subtitle (Italic accent)
    ax.text(x + w/2, y + h - 8.2, subtitle,
            fontsize=8.5, fontstyle='italic', fontweight='bold', ha='center', va='center', color=border_color, zorder=4)
    
    # Bullets (Crisp, dark, highly readable)
    curr_y = y + h - 11.2
    for b in bullets:
        ax.text(x + 3.0, curr_y, b,
                fontsize=8.2, ha='left', va='center', color='#1E293B', zorder=4)
        curr_y -= 2.6

# Helper Function: Draw Dashed Description Pill on connectors
def draw_dashed_pill(ax, cx, cy, text, border_color='#0284C7'):
    pill_w = len(text) * 0.65 + 3.5
    pill_h = 3.2
    rect = patches.FancyBboxPatch(
        (cx - pill_w/2, cy - pill_h/2), pill_w, pill_h,
        boxstyle="round,pad=0.2,rounding_size=1.6",
        linewidth=1.2, linestyle='--', edgecolor=border_color, facecolor='#FFFFFF', zorder=5
    )
    ax.add_patch(rect)
    ax.text(cx, cy, text, fontsize=7.6, ha='center', va='center', color='#334155', zorder=6)

# Colors matching user template
C_TEAL = '#06B6D4'
C_TEAL_DARK = '#0891B2'
C_ROSE = '#F43F5E'
C_ROSE_DARK = '#E11D48'
C_BLUE = '#3B82F6'
C_BLUE_DARK = '#2563EB'
C_SLATE = '#64748B'
C_SLATE_DARK = '#475569'

# Card Dimensions (Very wide and spacious for A4)
CARD_W = 42.0
CARD_H = 21.0
COL1_X = 8.0
COL2_X = 60.0

ROW1_Y = 111.0  # Baris 1: Ingesti & Transmisi
ROW2_Y = 69.0   # Baris 2: Core Backend & Engine AI
ROW3_Y = 27.0   # Baris 3: Workstation & Pelaporan

# ==================== BARIS 1: INGESTI & TRANSMISI ====================
# Box 1: Modalitas DR (Kiri)
draw_a4_card(ax, COL1_X, ROW1_Y, CARD_W, CARD_H,
             pill_color=C_TEAL_DARK, border_color=C_TEAL,
             title="MODALITAS RONTGEN DR",
             subtitle="Konsol Digital Klinik CDC Surabaya",
             bullets=[
                 "• Detektor: Flat-Panel Detector (FPD) 16-bit Grayscale",
                 "• Matriks Citra: 2999 × 2999 Piksel Resolusi Tinggi",
                 "• Protokol: Transmisi DICOM C-STORE SCU via LAN",
                 "• Tag Kalibrasi: (0028,0030) Pixel Spacing = 0.140 mm"
             ])

# Box 2: Modul 1 DICOM Listener (Kanan)
draw_a4_card(ax, COL2_X, ROW1_Y, CARD_W, CARD_H,
             pill_color=C_ROSE_DARK, border_color=C_ROSE,
             title="MODUL 1: DICOM LISTENER (C-STORE SCP)",
             subtitle="Daemon Service Latar Belakang (pynetdicom)",
             bullets=[
                 "• Parameter: Listening Port 4242 | AE: HYU_PACS_SERVER",
                 "• Kepatuhan Medis: Validasi NEMA PS 3.4 & SOP UID",
                 "• Parsing Metadata: Window W/L, Slope, & Intercept",
                 "• Repositori: Simpan Berkas .dcm Mentah ke Direktori"
             ])

# Connector 1 -> 2 (Horizontal)
ax.annotate('', xy=(COL2_X, ROW1_Y + CARD_H/2), xytext=(COL1_X + CARD_W, ROW1_Y + CARD_H/2),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_TEAL, lw=2.0), zorder=1)
draw_dashed_pill(ax, (COL1_X + CARD_W + COL2_X)/2, ROW1_Y + CARD_H/2, "TCP/IP LAN", border_color=C_TEAL)

# Connector Baris 1 -> Baris 2 (S-Curve Turun ke Box 3)
mid_r1_r2 = (ROW1_Y + ROW2_Y + CARD_H) / 2
ax.plot([COL2_X + CARD_W/2, COL2_X + CARD_W/2, COL1_X + CARD_W/2, COL1_X + CARD_W/2],
        [ROW1_Y, mid_r1_r2, mid_r1_r2, ROW2_Y + CARD_H], color=C_ROSE, lw=1.8, zorder=1)
ax.plot(COL2_X + CARD_W/2, ROW1_Y, 'o', color=C_ROSE, markersize=4.5, zorder=3)
ax.annotate('', xy=(COL1_X + CARD_W/2, ROW2_Y + CARD_H), xytext=(COL1_X + CARD_W/2, ROW2_Y + CARD_H + 2),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_ROSE, lw=1.8), zorder=1)
draw_dashed_pill(ax, 55.0, mid_r1_r2, "Ekstraksi Tag Metadata & Berkas Raw .dcm", border_color=C_ROSE)

# ==================== BARIS 2: BACKEND & AI RESTORASI ====================
# Box 3: Modul 2 Core Backend Laravel (Kiri)
draw_a4_card(ax, COL1_X, ROW2_Y, CARD_W, CARD_H,
             pill_color=C_BLUE_DARK, border_color=C_BLUE,
             title="MODUL 2: CORE BACKEND & BASIS DATA",
             subtitle="Web Application Core (Laravel 11 & MySQL)",
             bullets=[
                 "• Database MySQL: Metadata Pasien MCU, RM, Study UID",
                 "• Repositori File: storage/app/dicom/raw/ & restored/",
                 "• Otentikasi Role-Based & Live Form Ekspertise EMR",
                 "• Controller Logik: Dispatch Antrean Restorasi AI"
             ])

# Box 4: Modul 3 Engine Restorasi AI (Kanan)
draw_a4_card(ax, COL2_X, ROW2_Y, CARD_W, CARD_H,
             pill_color=C_TEAL_DARK, border_color=C_TEAL,
             title="MODUL 3: ENGINE RESTORASI CITRA & AI",
             subtitle="Python Multi-Algorithm Engine (CLI & REST API)",
             bullets=[
                 "• Bilateral Filter: Preservasi Tepi Batas Organ (σ_s, σ_r)",
                 "• Non-Local Means (NLM): Redundansi Patch Mandiri",
                 "• Deep Residual DnCNN: 17 Lapisan Konvolusi (x = y - R)",
                 "• Telemetri Real-Time: Nilai PSNR (dB), SSIM, Latensi"
             ])

# Connector 3 <-> 4 (Bi-directional)
ax.annotate('', xy=(COL2_X, ROW2_Y + CARD_H/2 + 2.5), xytext=(COL1_X + CARD_W, ROW2_Y + CARD_H/2 + 2.5),
            arrowprops=dict(arrowstyle='->,head_width=0.32,head_length=0.4', color=C_BLUE, lw=1.8), zorder=1)
ax.annotate('', xy=(COL1_X + CARD_W, ROW2_Y + CARD_H/2 - 2.5), xytext=(COL2_X, ROW2_Y + CARD_H/2 - 2.5),
            arrowprops=dict(arrowstyle='->,head_width=0.32,head_length=0.4', color=C_TEAL, lw=1.8), zorder=1)
draw_dashed_pill(ax, (COL1_X + CARD_W + COL2_X)/2, ROW2_Y + CARD_H/2, "Job AI & Citra Bersih", border_color=C_BLUE)

# Connector Baris 2 -> Baris 3 (Turun Tegak ke Box 5)
mid_r2_r3 = (ROW2_Y + ROW3_Y + CARD_H) / 2
ax.plot([COL1_X + CARD_W/2, COL1_X + CARD_W/2], [ROW2_Y, ROW3_Y + CARD_H], color=C_BLUE, lw=1.8, zorder=1)
ax.plot(COL1_X + CARD_W/2, ROW2_Y, 'o', color=C_BLUE, markersize=4.5, zorder=3)
ax.annotate('', xy=(COL1_X + CARD_W/2, ROW3_Y + CARD_H), xytext=(COL1_X + CARD_W/2, ROW3_Y + CARD_H + 2),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_BLUE, lw=1.8), zorder=1)
draw_dashed_pill(ax, COL1_X + CARD_W/2, mid_r2_r3, "Streaming Citra Medis 16-bit & Tag Kalibrasi", border_color=C_BLUE)

# ==================== BARIS 3: WORKSTATION & PELAPORAN ====================
# Box 5: Modul 4 Hyu PACS Viewer (Kiri)
draw_a4_card(ax, COL1_X, ROW3_Y, CARD_W, CARD_H,
             pill_color=C_TEAL_DARK, border_color=C_TEAL,
             title="MODUL 4: WEB WORKSTATION (HYU PACS)",
             subtitle="Web Clinical Viewer (HTML5 Canvas & SVG Layer)",
             bullets=[
                 "• Lockstep Anchoring: Matriks CTM⁻¹ menjaga posisi ukur",
                 "• Manipulasi Citra: Windowing (W/L), Zoom/Pan, Rotasi/Flip",
                 "• Kaliper Biometri CTR Danzer: ((A+B)/C × 100%)",
                 "• Split-Curtain Slider & Difference Map |Inoisy - Iclean|"
             ])

# Box 6: Modul 5 Pelaporan & Ekspor (Kanan)
draw_a4_card(ax, COL2_X, ROW3_Y, CARD_W, CARD_H,
             pill_color=C_ROSE_DARK, border_color=C_ROSE,
             title="MODUL 5: PELAPORAN & EKSPOR (PS 3.3)",
             subtitle="Output Medis Resmi & Verifikasi Radiolog",
             bullets=[
                 "• Cetak Lembar Hasil Pemeriksaan Radiologi (PDF KOP CDC)",
                 "• Ekspor Secondary Capture (DICOM SC Standar NEMA)",
                 "• Burn-in Anotasi: Identitas Pasien, Garis CTR & Rasio",
                 "• Evaluasi Diagnostik Dokter Spesialis Radiologi"
             ])

# Connector 5 -> 6 (Horizontal)
ax.annotate('', xy=(COL2_X, ROW3_Y + CARD_H/2), xytext=(COL1_X + CARD_W, ROW3_Y + CARD_H/2),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_TEAL, lw=2.0), zorder=1)
draw_dashed_pill(ax, (COL1_X + CARD_W + COL2_X)/2, ROW3_Y + CARD_H/2, "Rasio CTR & Anotasi", border_color=C_TEAL)

# Bottom Feedback Loop: Box 6 -> Box 3 (Sync ke Rekam Medis EMR)
loop_y = 13.0
ax.plot([COL2_X + CARD_W/2, COL2_X + CARD_W/2, COL1_X + CARD_W/4, COL1_X + CARD_W/4],
        [ROW3_Y, loop_y, loop_y, ROW3_Y], color=C_SLATE, lw=1.4, linestyle='--', zorder=1)
ax.plot(COL2_X + CARD_W/2, ROW3_Y, 'o', color=C_SLATE, markersize=4, zorder=3)
ax.annotate('', xy=(COL1_X + CARD_W/4, ROW3_Y), xytext=(COL1_X + CARD_W/4, ROW3_Y - 2),
            arrowprops=dict(arrowstyle='->,head_width=0.3,head_length=0.4', color=C_SLATE, lw=1.4), zorder=1)
draw_dashed_pill(ax, 55.0, loop_y, "Sinkronisasi Status Diagnostik & Ekspertise ke Rekam Medis EMR", border_color=C_SLATE)

plt.tight_layout()
output_img = 'd:/laragon\www/belajar-laravel/Gambar_3_4_A4_Portrait_Template_Hyu_PACS.png'
plt.savefig(output_img, dpi=300, bbox_inches='tight')
plt.close()

print("Successfully generated A4 Portrait diagram at:", output_img)

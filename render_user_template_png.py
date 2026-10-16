import matplotlib.pyplot as plt
import matplotlib.patches as patches

# Set Publication Quality (Times New Roman, Academic Standard)
plt.rcParams['font.family'] = 'Times New Roman'
plt.rcParams['mathtext.fontset'] = 'stix'

fig, ax = plt.subplots(figsize=(16, 10), dpi=300)
ax.set_facecolor('#FFFFFF')
fig.patch.set_facecolor('#FFFFFF')

# Canvas limits
ax.set_xlim(0, 160)
ax.set_ylim(0, 100)
ax.axis('off')

# Title Block
ax.text(80, 96, "GAMBAR 3.4. DIAGRAM ALUR KERJA & ARSITEKTUR HYU PACS WORKSTATION",
        fontsize=14, fontweight='bold', ha='center', va='center', color='#0F172A')
ax.text(80, 93, "Integrasi Alur Transmisi DICOM, Core Backend EMR, Engine AI, Workstation Diagnostik CTR, dan Pelaporan Medis",
        fontsize=10.5, fontstyle='italic', ha='center', va='center', color='#475569')

# Define Helper Function to draw the dual-box Card matching the template
def draw_template_card(ax, x, y, w, h, pill_color, border_color, title, subtitle, bullets):
    # Outer rounded rectangle
    outer_rect = patches.FancyBboxPatch(
        (x, y), w, h,
        boxstyle="round,pad=0.2,rounding_size=1.2",
        linewidth=2.0, edgecolor=border_color, facecolor='#FFFFFF', zorder=2
    )
    ax.add_patch(outer_rect)
    
    # Pill header badge
    pill_w = w - 4
    pill_h = 4.2
    pill_x = x + 2
    pill_y = y + h - pill_h - 1.5
    pill_rect = patches.FancyBboxPatch(
        (pill_x, pill_y), pill_w, pill_h,
        boxstyle="round,pad=0.2,rounding_size=2.0",
        linewidth=0, facecolor=pill_color, zorder=3
    )
    ax.add_patch(pill_rect)
    
    # Pill text
    ax.text(x + w/2, pill_y + pill_h/2, title,
            fontsize=9.5, fontweight='bold', ha='center', va='center', color='#FFFFFF', zorder=4)
    
    # Subtitle
    ax.text(x + w/2, y + h - 8.2, subtitle,
            fontsize=8.0, fontstyle='italic', fontweight='bold', ha='center', va='center', color=border_color, zorder=4)
    
    # Bullet lines
    curr_y = y + h - 11.0
    for b in bullets:
        ax.text(x + w/2, curr_y, b,
                fontsize=7.8, ha='center', va='center', color='#1E293B', zorder=4)
        curr_y -= 2.6

# Function to draw dashed description pills on connector lines
def draw_dashed_pill(ax, cx, cy, text, border_color='#0284C7'):
    pill_w = len(text) * 0.72 + 4.0
    pill_h = 3.4
    rect = patches.FancyBboxPatch(
        (cx - pill_w/2, cy - pill_h/2), pill_w, pill_h,
        boxstyle="round,pad=0.2,rounding_size=1.7",
        linewidth=1.2, linestyle='--', edgecolor=border_color, facecolor='#FFFFFF', zorder=5
    )
    ax.add_patch(rect)
    ax.text(cx, cy, text, fontsize=7.5, ha='center', va='center', color='#334155', zorder=6)

# Colors matching user template
C_TEAL = '#06B6D4'      # Cyan / Teal
C_TEAL_DARK = '#0891B2'
C_ROSE = '#F43F5E'      # Pink / Coral
C_ROSE_DARK = '#E11D48'
C_BLUE = '#3B82F6'      # Royal Blue
C_BLUE_DARK = '#2563EB'
C_SLATE = '#64748B'     # Slate Gray
C_SLATE_DARK = '#475569'

# Card Dimensions
CARD_W = 24.5
CARD_H = 18.0
MAIN_Y = 41.0  # Centerline Y for horizontal pipeline

# 1. DRAW 5 CENTRAL HORIZONTAL CARDS
# Box 1: Modalitas DR (Far Left)
draw_template_card(ax, 6, MAIN_Y, CARD_W, CARD_H,
                   pill_color=C_TEAL_DARK, border_color=C_TEAL,
                   title="MODALITAS RONTGEN DR",
                   subtitle="Konsol Digital Klinik CDC Surabaya",
                   bullets=[
                       "• Detektor FPD (16-bit Grayscale)",
                       "• Matriks Citra: 2999 × 2999 Piksel",
                       "• Layanan DICOM C-STORE SCU"
                   ])

# Box 2: Modul 1 DICOM Listener
draw_template_card(ax, 37.5, MAIN_Y, CARD_W, CARD_H,
                   pill_color=C_ROSE_DARK, border_color=C_ROSE,
                   title="MODUL 1: DICOM LISTENER",
                   subtitle="C-STORE SCP Daemon (pynetdicom)",
                   bullets=[
                       "• Daemon Background (Port 4242)",
                       "• AE Title: HYU_PACS_SERVER",
                       "• Validasi NEMA & Ekstraksi Tag"
                   ])

# Box 3: Modul 2 Core Backend Laravel
draw_template_card(ax, 69, MAIN_Y, CARD_W, CARD_H,
                   pill_color=C_BLUE_DARK, border_color=C_BLUE,
                   title="MODUL 2: CORE BACKEND EMR",
                   subtitle="Web Application Core (Laravel 11)",
                   bullets=[
                       "• Basis Data Pasien MCU & Study UID",
                       "• Routing REST API & Otentikasi Role",
                       "• Dispatch Job Restorasi & Live EMR"
                   ])

# Box 4: Modul 4 Hyu PACS Viewer
draw_template_card(ax, 100.5, MAIN_Y, CARD_W, CARD_H,
                   pill_color=C_TEAL_DARK, border_color=C_TEAL,
                   title="MODUL 4: HYU PACS VIEWER",
                   subtitle="Web Diagnostic Workstation",
                   bullets=[
                       "• Inverse Affine Mapping (CTM⁻¹)",
                       "• Windowing (W/L), Zoom/Pan, Rotasi",
                       "• Kaliper CTR Danzer ((A+B)/C)"
                   ])

# Box 5: Modul 5 Pelaporan & Ekspor (Far Right)
draw_template_card(ax, 131.5, MAIN_Y, CARD_W, CARD_H,
                   pill_color=C_ROSE_DARK, border_color=C_ROSE,
                   title="MODUL 5: PELAPORAN & EKSPOR",
                   subtitle="Output Medis & Secondary Capture",
                   bullets=[
                       "• Cetak Lembar Hasil Radiologi (PDF)",
                       "• Ekspor DICOM SC (NEMA PS 3.3)",
                       "• Verifikasi Dokter Spesialis"
                   ])

# 2. DRAW TOP SATELLITE CARD (MODUL 3: ENGINE RESTORASI AI)
draw_template_card(ax, 37.5, 72.0, CARD_W, CARD_H,
                   pill_color=C_TEAL_DARK, border_color=C_TEAL,
                   title="MODUL 3: ENGINE RESTORASI AI",
                   subtitle="Python Multi-Algorithm Engine",
                   bullets=[
                       "• Bilateral Filter & NLM Denoising",
                       "• Deep Residual CNN (DnCNN 17-Lapisan)",
                       "• Telemetri PSNR (dB), SSIM, Latensi"
                   ])

# 3. DRAW BOTTOM SATELLITE CARD (BASIS DATA & STORAGE ARSIP)
draw_template_card(ax, 37.5, 10.0, CARD_W, CARD_H,
                   pill_color=C_SLATE_DARK, border_color=C_SLATE,
                   title="BASIS DATA & STORAGE ARSIP",
                   subtitle="Penyimpanan Data Terpusat",
                   bullets=[
                       "• Database MySQL: Metadata Pasien MCU",
                       "• Filesystem: storage/app/dicom/raw/",
                       "• Log Telemetri Kinerja & Evaluasi"
                   ])

# 4. DRAW CONNECTORS & DASHED PILLS (EXACTLY MATCHING THE USER'S TEMPLATE)

# Horizontal arrows along central pipeline
# Conn 1 -> 2
ax.annotate('', xy=(37.5, MAIN_Y + CARD_H/2), xytext=(30.5, MAIN_Y + CARD_H/2),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_TEAL, lw=2.0), zorder=1)

# Conn 2 -> 3 (with small circle node at start)
ax.plot(62.0, MAIN_Y + CARD_H/2, 'o', color=C_ROSE, markersize=5, zorder=3)
ax.annotate('', xy=(69.0, MAIN_Y + CARD_H/2), xytext=(62.0, MAIN_Y + CARD_H/2),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_ROSE, lw=2.0), zorder=1)

# Conn 3 -> 4
ax.plot(93.5, MAIN_Y + CARD_H/2, 'o', color=C_BLUE, markersize=5, zorder=3)
ax.annotate('', xy=(100.5, MAIN_Y + CARD_H/2), xytext=(93.5, MAIN_Y + CARD_H/2),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_BLUE, lw=2.0), zorder=1)

# Conn 4 -> 5
ax.plot(125.0, MAIN_Y + CARD_H/2, 'o', color=C_TEAL, markersize=5, zorder=3)
ax.annotate('', xy=(131.5, MAIN_Y + CARD_H/2), xytext=(125.0, MAIN_Y + CARD_H/2),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_TEAL, lw=2.0), zorder=1)

# Overhead arch 1: Box 2 -> Box 3
# Arch from x=56, y=59 -> up to 64.5 -> right to 75 -> down to y=59
ax.plot([56, 56, 75, 75], [MAIN_Y + CARD_H, 64.5, 64.5, MAIN_Y + CARD_H], color=C_TEAL, lw=1.5, zorder=1)
ax.plot(56, MAIN_Y + CARD_H, 'o', color=C_TEAL, markersize=4, zorder=2)
ax.annotate('', xy=(75, MAIN_Y + CARD_H), xytext=(75, 62),
            arrowprops=dict(arrowstyle='->,head_width=0.3,head_length=0.4', color=C_TEAL, lw=1.5), zorder=1)
draw_dashed_pill(ax, 65.5, 64.5, "Ekstraksi Tag & Berkas Raw", border_color=C_TEAL)

# Overhead arch 2: Box 3 -> Box 4
ax.plot([87.5, 87.5, 106.5, 106.5], [MAIN_Y + CARD_H, 64.5, 64.5, MAIN_Y + CARD_H], color=C_TEAL, lw=1.5, zorder=1)
ax.plot(87.5, MAIN_Y + CARD_H, 'o', color=C_TEAL, markersize=4, zorder=2)
ax.annotate('', xy=(106.5, MAIN_Y + CARD_H), xytext=(106.5, 62),
            arrowprops=dict(arrowstyle='->,head_width=0.3,head_length=0.4', color=C_TEAL, lw=1.5), zorder=1)
draw_dashed_pill(ax, 97.0, 64.5, "Stream Citra Medis & Kalibrasi", border_color=C_TEAL)

# Top Box Connectors:
# Downward from Top Box into Box 2
ax.plot([49.75, 49.75], [72.0, MAIN_Y + CARD_H], color=C_TEAL, lw=1.5, zorder=1)
ax.plot(49.75, 72.0, 'o', color=C_TEAL, markersize=4, zorder=2)
ax.annotate('', xy=(49.75, MAIN_Y + CARD_H), xytext=(49.75, MAIN_Y + CARD_H + 2),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_TEAL, lw=1.5), zorder=1)
draw_dashed_pill(ax, 49.75, 65.5, "Dispatch Restorasi AI", border_color=C_TEAL)

# Long Overhead Return from Box 4 -> Top Box
ax.plot([112.75, 112.75, 62.0, 62.0], [MAIN_Y + CARD_H, 81.0, 81.0, 81.0], color=C_TEAL, lw=1.5, zorder=1)
ax.plot(112.75, MAIN_Y + CARD_H, 'o', color=C_TEAL, markersize=4, zorder=2)
ax.annotate('', xy=(62.0, 81.0), xytext=(65.0, 81.0),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_TEAL, lw=1.5), zorder=1)
draw_dashed_pill(ax, 87.5, 81.0, "Evaluasi Citra Tereduksi Derau & Telemetri PSNR/SSIM", border_color=C_TEAL)

# Bottom Box Connectors:
# Downward from Box 2 into Bottom Box (Rose/Pink line)
ax.plot([45.0, 45.0], [MAIN_Y, 28.0], color=C_ROSE, lw=1.5, zorder=1)
ax.annotate('', xy=(45.0, 28.0), xytext=(45.0, 30.0),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_ROSE, lw=1.5), zorder=1)

# Upward from Bottom Box into Box 2 (Slate gray line)
ax.plot([54.5, 54.5], [28.0, MAIN_Y], color=C_SLATE, lw=1.5, zorder=1)
ax.annotate('', xy=(54.5, MAIN_Y), xytext=(54.5, MAIN_Y - 2),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_SLATE, lw=1.5), zorder=1)
draw_dashed_pill(ax, 49.75, 34.5, "Read / Write MySQL & Storage", border_color=C_SLATE)

# Long Return Below from Box 4 -> Bottom Box
ax.plot([112.75, 112.75, 62.0, 62.0], [MAIN_Y, 19.0, 19.0, 19.0], color=C_TEAL, lw=1.5, zorder=1)
ax.plot(112.75, MAIN_Y, 'o', color=C_TEAL, markersize=4, zorder=2)
ax.annotate('', xy=(62.0, 19.0), xytext=(65.0, 19.0),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_TEAL, lw=1.5), zorder=1)
draw_dashed_pill(ax, 87.5, 19.0, "Sinkronisasi Ekspertise Rasio CTR & Anotasi ke EMR", border_color=C_TEAL)

plt.tight_layout()
output_img = 'd:/laragon/www/belajar-laravel/Gambar_3_4_Formasi_Template_Hyu_PACS.png'
plt.savefig(output_img, dpi=300, bbox_inches='tight')
plt.close()

print("Successfully generated exact match template figure at:", output_img)

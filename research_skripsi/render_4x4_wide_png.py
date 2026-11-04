# -*- coding: utf-8 -*-
import matplotlib.pyplot as plt
import matplotlib.patches as patches

# Set Publication Quality (Times New Roman, Academic Standard)
plt.rcParams['font.family'] = 'Times New Roman'
plt.rcParams['mathtext.fontset'] = 'stix'

# Create canvas in Wide/Landscape proportion (16 x 8.8 inches at 300 DPI)
fig, ax = plt.subplots(figsize=(16, 8.8), dpi=300)
ax.set_facecolor('#FFFFFF')
fig.patch.set_facecolor('#FFFFFF')

# Canvas limits
ax.set_xlim(0, 160)
ax.set_ylim(0, 88)
ax.axis('off')

# Title Block
ax.text(80, 84.8, "GAMBAR 3.4. DIAGRAM ALUR KERJA PERANGKAT LUNAK HYU PACS WORKSTATION",
        fontsize=15, fontweight='bold', ha='center', va='center', color='#0F172A')
ax.text(80, 81.6, "Alur Integrasi Ingesti Transmisi DICOM, Core Backend EMR, Engine AI, Workstation Diagnostik CTR, dan Pelaporan Medis",
        fontsize=11, fontstyle='italic', ha='center', va='center', color='#475569')
ax.plot([10, 150], [79.5, 79.5], color='#334155', linewidth=1.4)

# Colors matching user's preferred modern palette
C_TEAL = '#06B6D4'
C_TEAL_DARK = '#0891B2'
C_ROSE = '#F43F5E'
C_ROSE_DARK = '#E11D48'
C_BLUE = '#3B82F6'
C_BLUE_DARK = '#2563EB'
C_SLATE = '#64748B'
C_SLATE_DARK = '#475569'
C_PURPLE = '#8B5CF6'
C_PURPLE_DARK = '#7C3AED'

# Helper Function: Draw 4x4 Card (Dual-outline rounded card + colored pill header)
def draw_card_4x4(ax, x, y, w, h, pill_color, border_color, title, subtitle, bullets):
    outer_rect = patches.FancyBboxPatch(
        (x, y), w, h,
        boxstyle="round,pad=0.2,rounding_size=1.4",
        linewidth=2.2, edgecolor=border_color, facecolor='#FFFFFF', zorder=2
    )
    ax.add_patch(outer_rect)
    
    # Pill header badge (spanning nearly full width)
    pill_w = w - 1.6
    pill_h = 4.4
    pill_x = x + 0.8
    pill_y = y + h - pill_h - 1.2
    pill_rect = patches.FancyBboxPatch(
        (pill_x, pill_y), pill_w, pill_h,
        boxstyle="round,pad=0.2,rounding_size=2.2",
        linewidth=0, facecolor=pill_color, zorder=3
    )
    ax.add_patch(pill_rect)
    
    # Pill header text
    ax.text(x + w/2, pill_y + pill_h/2, title,
            fontsize=9.6, fontweight='bold', ha='center', va='center', color='#FFFFFF', zorder=4)
    
    # Subtitle (BOLD)
    ax.text(x + w/2, y + h - 8.3, subtitle,
            fontsize=9.8, fontweight='bold', ha='center', va='center', color=border_color, zorder=4)
    
    # Bullets (Large, readable font)
    curr_y = y + h - 12.0
    for b in bullets:
        ax.text(x + 2.2, curr_y, b,
                fontsize=9.4, ha='left', va='center', color='#1E293B', zorder=4)
        curr_y -= 3.2

# Helper Function: Draw Dashed Description Pill on connectors
def draw_dashed_pill(ax, cx, cy, text, border_color='#0284C7'):
    pill_w = len(text) * 0.80 + 3.8
    pill_h = 3.6
    rect = patches.FancyBboxPatch(
        (cx - pill_w/2, cy - pill_h/2), pill_w, pill_h,
        boxstyle="round,pad=0.2,rounding_size=1.8",
        linewidth=1.4, linestyle='--', edgecolor=border_color, facecolor='#FFFFFF', zorder=5
    )
    ax.add_patch(rect)
    ax.text(cx, cy, text, fontsize=8.8, fontweight='bold', ha='center', va='center', color='#1E293B', zorder=6)

# Card Dimensions & Grid Coordinates (4 Columns, 2 Rows)
CARD_W = 28.0
CARD_H = 21.0

# 4 Columns X
C1_X = 10.0
C2_X = 46.5
C3_X = 83.0
C4_X = 119.5

# 2 Rows Y
ROW_TOP_Y = 51.5   # Baris Atas
ROW_BOT_Y = 11.5   # Baris Bawah

# ==================== BARIS ATAS (4 SHAPES) ====================
# 1. Modalitas DR (Teal)
draw_card_4x4(ax, C1_X, ROW_TOP_Y, CARD_W, CARD_H,
              pill_color=C_TEAL_DARK, border_color=C_TEAL,
              title="1. MODALITAS RONTGEN DR",
              subtitle="Fujifilm FDR Xair & D-EVO II",
              bullets=[
                  "• Citra Toraks PA (Format DICOM)",
                  "• Transmisi DICOM C-STORE SCU",
                  "• Kalibrasi Pixel Spacing Fisik"
              ])

# 2. Modul 1 DICOM Listener (Rose/Pink)
draw_card_4x4(ax, C2_X, ROW_TOP_Y, CARD_W, CARD_H,
              pill_color=C_ROSE_DARK, border_color=C_ROSE,
              title="2. DICOM LISTENER (MODUL 1)",
              subtitle="Daemon SCP (pynetdicom)",
              bullets=[
                  "• Background Port Jaringan 4242",
                  "• AE Title: HYU_PACS_SERVER",
                  "• Ekstraksi Tag & Matriks Citra"
              ])

# 3. Modul 2 Backend Laravel (Royal Blue)
draw_card_4x4(ax, C3_X, ROW_TOP_Y, CARD_W, CARD_H,
              pill_color=C_BLUE_DARK, border_color=C_BLUE,
              title="3. CORE BACKEND (MODUL 2)",
              subtitle="Framework Laravel 11",
              bullets=[
                  "• Manajemen Pasien MCU & RM",
                  "• Routing API & Otentikasi Role",
                  "• Controller Antrean Dispatch AI"
              ])

# 4. Basis Data MySQL & Storage (Slate Gray)
draw_card_4x4(ax, C4_X, ROW_TOP_Y, CARD_W, CARD_H,
              pill_color=C_SLATE_DARK, border_color=C_SLATE,
              title="4. BASIS DATA & STORAGE",
              subtitle="MySQL & Filesystem",
              bullets=[
                  "• Metadata Pasien & Study UID",
                  "• Penyimpanan Berkas DICOM",
                  "• Log Audit & Riwayat Restorasi"
              ])

# ==================== CONNECTORS BARIS ATAS ====================
# 1 -> 2
ax.annotate('', xy=(C2_X, ROW_TOP_Y + CARD_H/2), xytext=(C1_X + CARD_W, ROW_TOP_Y + CARD_H/2),
            arrowprops=dict(arrowstyle="-|>", color=C_TEAL, lw=2.5, mutation_scale=18), zorder=3)
draw_dashed_pill(ax, (C1_X + CARD_W + C2_X)/2, ROW_TOP_Y + CARD_H/2, "TCP/IP LAN", border_color=C_TEAL)

# 2 -> 3
ax.annotate('', xy=(C3_X, ROW_TOP_Y + CARD_H/2), xytext=(C2_X + CARD_W, ROW_TOP_Y + CARD_H/2),
            arrowprops=dict(arrowstyle="-|>", color=C_ROSE, lw=2.5, mutation_scale=18), zorder=3)
draw_dashed_pill(ax, (C2_X + CARD_W + C3_X)/2, ROW_TOP_Y + CARD_H/2, "File & Tag", border_color=C_ROSE)

# 3 <-> 4 (Bidirectional)
ax.annotate('', xy=(C4_X, ROW_TOP_Y + CARD_H/2), xytext=(C3_X + CARD_W, ROW_TOP_Y + CARD_H/2),
            arrowprops=dict(arrowstyle="<|-|>", color=C_BLUE, lw=2.5, mutation_scale=18), zorder=3)
draw_dashed_pill(ax, (C3_X + CARD_W + C4_X)/2, ROW_TOP_Y + CARD_H/2, "SQL & File", border_color=C_BLUE)

# ==================== TRANSISI DARI ATAS KE BAWAH ====================
# Turun dari Box 3 ke Box 5
top_mid_x = C3_X + CARD_W/2
bot_mid_x = C1_X + CARD_W/2
elbow_y = (ROW_TOP_Y + ROW_BOT_Y + CARD_H) / 2  # ~ 42.0

ax.plot([top_mid_x, top_mid_x], [ROW_TOP_Y, elbow_y], color=C_BLUE_DARK, lw=2.5, zorder=3)
ax.plot([top_mid_x, bot_mid_x], [elbow_y, elbow_y], color=C_BLUE_DARK, lw=2.5, zorder=3)
ax.annotate('', xy=(bot_mid_x, ROW_BOT_Y + CARD_H), xytext=(bot_mid_x, elbow_y),
            arrowprops=dict(arrowstyle="-|>", color=C_BLUE_DARK, lw=2.5, mutation_scale=18), zorder=3)

# Badge pada panah transisi (LARGE & BOLD)
draw_dashed_pill(ax, 68.0, elbow_y, "Dispatch Job Pemrosesan Restorasi AI & Ekstraksi Matriks Citra 16-bit", border_color=C_BLUE_DARK)

# ==================== BARIS BAWAH (4 SHAPES) ====================
# 5. Engine Restorasi AI (Teal)
draw_card_4x4(ax, C1_X, ROW_BOT_Y, CARD_W, CARD_H,
              pill_color=C_TEAL_DARK, border_color=C_TEAL,
              title="5. ENGINE RESTORASI AI (MOD 3)",
              subtitle="Python Denoising Engine",
              bullets=[
                  "• Bilateral Filter (Preservasi Tepi)",
                  "• DnCNN Residual (17-Layer)",
                  "• Telemetri PSNR & SSIM Real-Time"
              ])

# 6. Hyu PACS Viewer (Purple)
draw_card_4x4(ax, C2_X, ROW_BOT_Y, CARD_W, CARD_H,
              pill_color=C_PURPLE_DARK, border_color=C_PURPLE,
              title="6. HYU PACS VIEWER (MODUL 4)",
              subtitle="Web Diagnostic Workstation",
              bullets=[
                  "• SVG Layer & Matriks CTM⁻¹",
                  "• Windowing (W/L), Pan, & Zoom",
                  "• Split-Curtain Comparison Slider"
              ])

# 7. Kaliper Biometri CTR (Rose/Red)
draw_card_4x4(ax, C3_X, ROW_BOT_Y, CARD_W, CARD_H,
              pill_color=C_ROSE_DARK, border_color=C_ROSE,
              title="7. KALIPER BIOMETRI CTR",
              subtitle="Analisis Kardiomegali (Danzer, 1919)",
              bullets=[
                  "• Garis Jantung (A+B) & Toraks (C)",
                  "• Kalkulasi Rasio: ((A+B)/C) × 100%",
                  "• Klasifikasi Otomatis Kardiomegali"
              ])

# 8. Pelaporan & Ekspor (Blue)
draw_card_4x4(ax, C4_X, ROW_BOT_Y, CARD_W, CARD_H,
              pill_color=C_BLUE_DARK, border_color=C_BLUE,
              title="8. PELAPORAN & EKSPOR (MOD 5)",
              subtitle="Output Medis Resmi PS 3.3",
              bullets=[
                  "• Penerbitan Laporan PDF Siap Cetak",
                  "• Ekspor Secondary Capture (DICOM SC)",
                  "• Burn-in Anotasi & Hasil Rasio CTR"
              ])

# ==================== CONNECTORS BARIS BAWAH ====================
# 5 -> 6
ax.annotate('', xy=(C2_X, ROW_BOT_Y + CARD_H/2), xytext=(C1_X + CARD_W, ROW_BOT_Y + CARD_H/2),
            arrowprops=dict(arrowstyle="-|>", color=C_TEAL, lw=2.5, mutation_scale=18), zorder=3)
draw_dashed_pill(ax, (C1_X + CARD_W + C2_X)/2, ROW_BOT_Y + CARD_H/2, "Citra Bersih", border_color=C_TEAL)

# 6 -> 7
ax.annotate('', xy=(C3_X, ROW_BOT_Y + CARD_H/2), xytext=(C2_X + CARD_W, ROW_BOT_Y + CARD_H/2),
            arrowprops=dict(arrowstyle="-|>", color=C_PURPLE, lw=2.5, mutation_scale=18), zorder=3)
draw_dashed_pill(ax, (C2_X + CARD_W + C3_X)/2, ROW_BOT_Y + CARD_H/2, "Anotasi SVG", border_color=C_PURPLE)

# 7 -> 8
ax.annotate('', xy=(C4_X, ROW_BOT_Y + CARD_H/2), xytext=(C3_X + CARD_W, ROW_BOT_Y + CARD_H/2),
            arrowprops=dict(arrowstyle="-|>", color=C_ROSE, lw=2.5, mutation_scale=18), zorder=3)
draw_dashed_pill(ax, (C3_X + CARD_W + C4_X)/2, ROW_BOT_Y + CARD_H/2, "Rasio CTR %", border_color=C_ROSE)

# Feedback Loop dari Box 8 kembali ke Box 4 (MySQL / EMR)
feedback_x = C4_X + CARD_W + 4.5
ax.plot([C4_X + CARD_W, feedback_x], [ROW_BOT_Y + CARD_H/2, ROW_BOT_Y + CARD_H/2], color=C_SLATE_DARK, lw=2.0, linestyle='--', zorder=3)
ax.plot([feedback_x, feedback_x], [ROW_BOT_Y + CARD_H/2, ROW_TOP_Y + CARD_H/2], color=C_SLATE_DARK, lw=2.0, linestyle='--', zorder=3)
ax.annotate('', xy=(C4_X + CARD_W, ROW_TOP_Y + CARD_H/2), xytext=(feedback_x, ROW_TOP_Y + CARD_H/2),
            arrowprops=dict(arrowstyle="-|>", color=C_SLATE_DARK, lw=2.0, linestyle='--', mutation_scale=18), zorder=3)
draw_dashed_pill(ax, feedback_x, (ROW_TOP_Y + ROW_BOT_Y + CARD_H)/2, "Sinkronisasi EMR", border_color=C_SLATE_DARK)

plt.tight_layout()
output_png = 'Gambar_3_4_Lebar_4x4_Hyu_PACS.png'
plt.savefig(output_png, dpi=300, facecolor='#FFFFFF', bbox_inches='tight')
plt.close()

print(f"Berhasil merender gambar resolusi tinggi 300 DPI dengan teks besar & ringkas: {output_png}")

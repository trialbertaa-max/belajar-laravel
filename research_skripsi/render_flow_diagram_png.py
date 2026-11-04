import matplotlib.pyplot as plt
import matplotlib.patches as patches

# Set Publication Quality (Times New Roman, Academic Standard)
plt.rcParams['font.family'] = 'Times New Roman'
plt.rcParams['mathtext.fontset'] = 'stix'

fig, ax = plt.subplots(figsize=(15, 9.5), dpi=300)
ax.set_facecolor('#FFFFFF')
fig.patch.set_facecolor('#FFFFFF')

# Canvas limits
ax.set_xlim(0, 150)
ax.set_ylim(0, 95)
ax.axis('off')

# Title Block matching user's image header
ax.text(12, 88.5, "FLOW DIAGRAM", fontsize=20, fontweight='bold', ha='left', va='center', color='#0F172A')
ax.text(12, 84.5, "ALUR LOGIKA INGESTI DICOM, ENGINE AI, DIAGNOSTIK CTR, DAN PELAPORAN MEDIS",
        fontsize=9.5, fontstyle='italic', ha='left', va='center', color='#64748B')

# Colors matching user's uploaded example
C_PURPLE = '#8B5CF6'       # Circle Start
C_PINK = '#EC4899'         # Vibrant Magenta / Pink
C_DEEP_PURPLE = '#7C3AED'  # Deep Violet
C_LAVENDER = '#A78BFA'     # Lavender / Periwinkle
C_DIAMOND = '#DB2777'      # Darker Pink / Magenta for Diamond
C_END = '#6D28D9'          # Dark Purple for Capsule End
C_LINE = '#CBD5E1'         # Soft Slate Gray for connectors

# Helper to draw rounded process rectangle
def draw_process_rect(ax, x, y, w, h, bg_color, title, desc):
    rect = patches.FancyBboxPatch(
        (x, y), w, h,
        boxstyle="round,pad=0.2,rounding_size=1.2",
        linewidth=0, facecolor=bg_color, zorder=2
    )
    ax.add_patch(rect)
    ax.text(x + w/2, y + h - 3.2, title,
            fontsize=9.2, fontweight='bold', ha='center', va='center', color='#FFFFFF', zorder=3)
    
    # Split description lines
    lines = desc.split('\n')
    y_text = y + h - 6.2
    for line in lines:
        ax.text(x + w/2, y_text, line,
                fontsize=7.6, ha='center', va='center', color='#FFFFFF', zorder=3)
        y_text -= 2.4

# Layout Grid coordinates matching user's image
COL1_X = 12.0
COL2_X = 46.0
COL3_X = 80.0
COL4_X = 114.0

RECT_W = 24.5
RECT_H = 12.0

ROW_TOP_Y = 60.0
ROW_MID_Y = 38.0
ROW_BOT_Y = 16.0

# ==================== 1. COLUMN 1 (LEFT) ====================
# Node 1: START (Circle Purple)
circle_r = 7.2
circle_cx = COL1_X + RECT_W/2
circle_cy = ROW_TOP_Y + RECT_H/2
circle = plt.Circle((circle_cx, circle_cy), circle_r, color=C_PURPLE, zorder=2)
ax.add_patch(circle)
ax.text(circle_cx, circle_cy, "START", fontsize=12, fontweight='bold', ha='center', va='center', color='#FFFFFF', zorder=3)

# Node 2: Input Modalitas (Pink Rect)
draw_process_rect(ax, COL1_X, ROW_MID_Y, RECT_W, RECT_H, C_PINK,
                  "Modalitas Rontgen DR",
                  "Transmisi citra toraks mentah\n16-bit via protokol C-STORE SCU\n(CDC Surabaya - LAN TCP/IP)")

# Node 3: Modul 1 DICOM Listener (Deep Purple Rect)
draw_process_rect(ax, COL1_X, ROW_BOT_Y, RECT_W, RECT_H, C_DEEP_PURPLE,
                  "Modul 1: DICOM Listener",
                  "Daemon SCP Port 4242 Service\nParsing metadata header tag\n& simpan matriks piksel mentah")

# ==================== 2. COLUMN 2 ====================
# Node 4: Modul 2 Backend EMR (Pink Rect)
draw_process_rect(ax, COL2_X, ROW_BOT_Y, RECT_W, RECT_H, C_PINK,
                  "Modul 2: Core Backend EMR",
                  "Manajemen pasien MCU di MySQL\nPengorganisasian berkas .dcm\n& otentikasi role-based access")

# Node 5: Validasi Standar Medis (Lavender Rect)
draw_process_rect(ax, COL2_X, ROW_MID_Y, RECT_W, RECT_H, C_LAVENDER,
                  "Validasi NEMA PS 3.4",
                  "Pemeriksaan integritas header\nVerifikasi SOP Class UID\n& kompatibilitas transfer syntax")

# ==================== 3. COLUMN 3 ====================
# Node 6: Fallback / Log (Magenta Rect)
draw_process_rect(ax, COL3_X, ROW_TOP_Y, RECT_W, RECT_H, '#C026D3',
                  "Bypass / Log Transmisi",
                  "Tampilkan citra asli (Raw) jika\ntanpa derau atau catat log error\nsaat kegagalan transmisi")

# Node 7: Modul 3 Engine AI (Deep Purple Rect)
draw_process_rect(ax, COL3_X, ROW_MID_Y, RECT_W, RECT_H, C_DEEP_PURPLE,
                  "Modul 3: Engine Restorasi AI",
                  "Eksekusi pipeline filter DnCNN\n17-lapisan / NLM / Bilateral &\nkalkulasi metrik PSNR/SSIM")

# Node 9: Diamond Decision CTR (Magenta Diamond)
dia_cx = COL3_X + RECT_W/2
dia_cy = ROW_BOT_Y + RECT_H/2
dia_rx = 9.0
dia_ry = 7.5
diamond = patches.Polygon([
    (dia_cx, dia_cy + dia_ry),
    (dia_cx + dia_rx, dia_cy),
    (dia_cx, dia_cy - dia_ry),
    (dia_cx - dia_rx, dia_cy)
], closed=True, facecolor=C_DIAMOND, edgecolor='none', zorder=2)
ax.add_patch(diamond)
ax.text(dia_cx, dia_cy + 2.0, "Uji Rasio CTR", fontsize=8.5, fontweight='bold', ha='center', va='center', color='#FFFFFF', zorder=3)
ax.text(dia_cx, dia_cy - 1.8, "Apakah CTR\n(A+B)/C > 50%?", fontsize=7.2, ha='center', va='center', color='#FFFFFF', zorder=3)

# ==================== 4. COLUMN 4 (RIGHT) ====================
# Node 8: Modul 4 Workstation Viewer (Lavender Rect)
draw_process_rect(ax, COL4_X, ROW_TOP_Y, RECT_W, RECT_H, C_LAVENDER,
                  "Modul 4: Hyu PACS Viewer",
                  "HTML5 Canvas & SVG Layer\nWindowing (W/L), Zoom/Pan &\nKaliper CTR Danzer interaktif")

# Node 10: Modul 5 Pelaporan Medis (Pink Rect)
draw_process_rect(ax, COL4_X, ROW_MID_Y, RECT_W, RECT_H, C_PINK,
                  "Modul 5: Pelaporan & Ekspor",
                  "Penerbitan Lembar Hasil PDF\nEkspor Secondary Capture SC\n(Burn-in anotasi & rasio CTR)")

# Node 11: END / FINISH (Capsule Deep Purple)
capsule_w = RECT_W + 2.0
capsule_h = 9.5
capsule_x = COL4_X - 1.0
capsule_y = ROW_BOT_Y + (RECT_H - capsule_h)/2
capsule = patches.FancyBboxPatch(
    (capsule_x, capsule_y), capsule_w, capsule_h,
    boxstyle="round,pad=0.2,rounding_size=4.5",
    linewidth=0, facecolor=C_END, zorder=2
)
ax.add_patch(capsule)
ax.text(capsule_x + capsule_w/2, capsule_y + capsule_h/2 + 1.2, "SELESAI (Arsip EMR)",
        fontsize=9.5, fontweight='bold', ha='center', va='center', color='#FFFFFF', zorder=3)
ax.text(capsule_x + capsule_w/2, capsule_y + capsule_h/2 - 1.8, "Verifikasi Dokter Spesialis Radiologi",
        fontsize=7.8, ha='center', va='center', color='#FFFFFF', zorder=3)

# ==================== CONNECTING LINES (EXACTLY MATCHING USER'S IMAGE) ====================
# Conn 1: Start -> Modality (Down line)
ax.plot([circle_cx, circle_cx], [circle_cy - circle_r, ROW_MID_Y + RECT_H], color=C_LINE, lw=1.5, zorder=1)

# Conn 2: Modality -> Listener (Down line)
ax.plot([COL1_X + RECT_W/2, COL1_X + RECT_W/2], [ROW_MID_Y, ROW_BOT_Y + RECT_H], color=C_LINE, lw=1.5, zorder=1)

# Conn 3: Listener -> Backend (Right line)
ax.plot([COL1_X + RECT_W, COL2_X], [ROW_BOT_Y + RECT_H/2, ROW_BOT_Y + RECT_H/2], color=C_LINE, lw=1.5, zorder=1)

# Conn 4: Backend -> Val Dicom (Up line)
ax.plot([COL2_X + RECT_W/2, COL2_X + RECT_W/2], [ROW_BOT_Y + RECT_H, ROW_MID_Y], color=C_LINE, lw=1.5, zorder=1)

# Conn 5: Val Dicom -> Fallback (NO - Up then Right line)
corner_x = COL2_X + RECT_W/2
ax.plot([corner_x, corner_x, COL3_X], [ROW_MID_Y + RECT_H, ROW_TOP_Y + RECT_H/2, ROW_TOP_Y + RECT_H/2],
        color=C_LINE, lw=1.5, zorder=1)
ax.text(corner_x + 2.5, ROW_MID_Y + RECT_H + 2.5, "NO", fontsize=8.0, fontweight='bold', color='#64748B', zorder=4)

# Conn 6: Val Dicom -> AI Engine (YES - Right line)
ax.plot([COL2_X + RECT_W, COL3_X], [ROW_MID_Y + RECT_H/2, ROW_MID_Y + RECT_H/2], color=C_LINE, lw=1.5, zorder=1)
ax.text((COL2_X + RECT_W + COL3_X)/2, ROW_MID_Y + RECT_H/2 + 1.8, "YES", fontsize=8.0, fontweight='bold', color='#64748B', ha='center', zorder=4)

# Conn 7: AI Engine -> Fallback (NO - Up line)
ax.plot([COL3_X + RECT_W/2, COL3_X + RECT_W/2], [ROW_MID_Y + RECT_H, ROW_TOP_Y], color=C_LINE, lw=1.5, zorder=1)
ax.text(COL3_X + RECT_W/2 + 2.5, ROW_MID_Y + RECT_H + 2.5, "NO", fontsize=8.0, fontweight='bold', color='#64748B', zorder=4)

# Conn 8: Fallback -> Viewer (Right line)
ax.plot([COL3_X + RECT_W, COL4_X], [ROW_TOP_Y + RECT_H/2, ROW_TOP_Y + RECT_H/2], color=C_LINE, lw=1.5, zorder=1)

# Conn 9: AI Engine -> Decision CTR (YES - Down line)
ax.plot([COL3_X + RECT_W/2, dia_cx], [ROW_MID_Y, dia_cy + dia_ry], color=C_LINE, lw=1.5, zorder=1)
ax.text(dia_cx + 2.5, ROW_MID_Y - 2.5, "YES", fontsize=8.0, fontweight='bold', color='#64748B', zorder=4)

# Conn 10: Viewer -> Report (Down line)
ax.plot([COL4_X + RECT_W/2, COL4_X + RECT_W/2], [ROW_TOP_Y, ROW_MID_Y + RECT_H], color=C_LINE, lw=1.5, zorder=1)

# Conn 11: Report -> End (Down arrow)
ax.annotate('', xy=(COL4_X + RECT_W/2, capsule_y + capsule_h),
            xytext=(COL4_X + RECT_W/2, ROW_MID_Y),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_LINE, lw=1.5), zorder=1)

# Conn 12: Decision CTR -> End (Right arrow)
ax.annotate('', xy=(capsule_x, dia_cy),
            xytext=(dia_cx + dia_rx, dia_cy),
            arrowprops=dict(arrowstyle='->,head_width=0.35,head_length=0.45', color=C_LINE, lw=1.5), zorder=1)

plt.tight_layout()
output_flow_png = 'd:/laragon/www/belajar-laravel/Gambar_3_4_Flowchart_Hyu_PACS.png'
plt.savefig(output_flow_png, dpi=300, bbox_inches='tight')
plt.close()

print("Successfully generated exact match Flow Diagram at:", output_flow_png)

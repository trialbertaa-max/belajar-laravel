# -*- coding: utf-8 -*-
import matplotlib.pyplot as plt
import matplotlib.patches as patches

# Set Academic Quality (Times New Roman, STIX math)
plt.rcParams['font.family'] = 'Times New Roman'
plt.rcParams['mathtext.fontset'] = 'stix'

# Create Canvas (7.5 x 7.0 inches at 300 DPI - Compact, balanced, NOT overly tall!)
fig, ax = plt.subplots(figsize=(7.5, 7.0), dpi=300)
ax.set_facecolor('#FFFFFF')
fig.patch.set_facecolor('#FFFFFF')

# Canvas Limits
ax.set_xlim(0, 100)
ax.set_ylim(0, 95)
ax.axis('off')

# Styling constants
STROKE_COLOR = '#1E293B'
FILL_CARD = '#FFFFFF'
FILL_OVAL = '#F8FAFC'
LW = 1.8

# Helper: Draw Oval Node
def draw_oval(ax, cx, cy, w, h, text):
    oval = patches.FancyBboxPatch(
        (cx - w/2, cy - h/2), w, h,
        boxstyle="round,pad=0.2,rounding_size=2.0",
        linewidth=LW, edgecolor=STROKE_COLOR, facecolor=FILL_OVAL, zorder=2
    )
    ax.add_patch(oval)
    ax.text(cx, cy, text, fontsize=12.0, fontweight='bold', ha='center', va='center', color='#0F172A', zorder=3)

# Helper: Draw Process Box (Compact 2-line structure)
def draw_box(ax, cx, cy, w, h, title, subtext):
    box = patches.FancyBboxPatch(
        (cx - w/2, cy - h/2), w, h,
        boxstyle="round,pad=0.2,rounding_size=0.8",
        linewidth=LW, edgecolor=STROKE_COLOR, facecolor=FILL_CARD, zorder=2
    )
    ax.add_patch(box)
    
    # Title
    ax.text(cx, cy + 2.0, title,
            fontsize=11.5, fontweight='bold', ha='center', va='center', color='#0F172A', zorder=3)
    
    # Subtext (Single concise line)
    ax.text(cx, cy - 2.2, subtext,
            fontsize=10.0, ha='center', va='center', color='#334155', zorder=3)

MID_X = 50.0
W_MAIN = 84.0
H_BOX = 8.5

# 1. Mulai
draw_oval(ax, MID_X, 90.0, 36.0, 6.0, "Mulai Penelitian")

# 2. Box 1
draw_box(ax, MID_X, 79.5, W_MAIN, H_BOX,
         "1. Persiapan & Kurasi Dataset",
         "100 citra DICOM toraks PA (CDC Surabaya) & de-identifikasi data pasien")

# 3. Box 2
draw_box(ax, MID_X, 67.5, W_MAIN, H_BOX,
         "2. Praproses & Pemodelan Derau Dosis Rendah",
         "Ekstraksi metadata & injeksi derau campuran Poisson-Gaussian dosis rendah")

# 4. Box 3
draw_box(ax, MID_X, 55.5, W_MAIN, H_BOX,
         "3. Implementasi Restorasi Citra Komparatif",
         "Noisy Baseline, Filter (Bilateral, Gaussian, Median) & DnCNN (17-Layer)")

# 5. Box 4
draw_box(ax, MID_X, 43.5, W_MAIN, H_BOX,
         "4. Pengukuran Biometrik CTR Danzer (1919)",
         "Pengukuran diameter jantung ($D_H$) & toraks ($D_T$) vs ground truth radiolog")

# 6. Parallel Boxes 5a & 5b
W_SUB = 40.5
X_5A = 28.5
X_5B = 71.5
Y_5 = 31.5

draw_box(ax, X_5A, Y_5, W_SUB, H_BOX,
         "5a. Evaluasi Kualitas Citra",
         "Metrik: PSNR, SSIM, SNR Gain, & Latensi")

draw_box(ax, X_5B, Y_5, W_SUB, H_BOX,
         "5b. Evaluasi Akurasi CTR",
         "Biometrik: MAE, Bias, Pearson r, & LoA")

# 7. Box 6
draw_box(ax, MID_X, 19.5, W_MAIN, H_BOX,
         "6. Uji Statistik & Integrasi Sistem",
         "Uji ANOVA / Friedman, Tukey HSD, & implementasi ke Web Hyu PACS")

# 8. Selesai
draw_oval(ax, MID_X, 9.0, 36.0, 6.0, "Selesai / Laporan Skripsi")

# ==================== CONNECTORS ====================
def draw_arrow(ax, x1, y1, x2, y2):
    ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                arrowprops=dict(arrowstyle="-|>", color=STROKE_COLOR, lw=1.6, mutation_scale=13), zorder=4)

# Mulai -> 1
draw_arrow(ax, MID_X, 87.0, MID_X, 83.75)
# 1 -> 2
draw_arrow(ax, MID_X, 75.25, MID_X, 71.75)
# 2 -> 3
draw_arrow(ax, MID_X, 63.25, MID_X, 59.75)
# 3 -> 4
draw_arrow(ax, MID_X, 51.25, MID_X, 47.75)

# 4 -> Split to 5a & 5b
SPLIT_Y = 37.5
ax.plot([MID_X, MID_X], [39.25, SPLIT_Y], color=STROKE_COLOR, lw=1.6, zorder=3)
ax.plot([X_5A, X_5B], [SPLIT_Y, SPLIT_Y], color=STROKE_COLOR, lw=1.6, zorder=3)
draw_arrow(ax, X_5A, SPLIT_Y, X_5A, Y_5 + H_BOX/2)
draw_arrow(ax, X_5B, SPLIT_Y, X_5B, Y_5 + H_BOX/2)

# 5a & 5b -> Merge to 6
MERGE_Y = 25.5
ax.plot([X_5A, X_5A], [Y_5 - H_BOX/2, MERGE_Y], color=STROKE_COLOR, lw=1.6, zorder=3)
ax.plot([X_5B, X_5B], [Y_5 - H_BOX/2, MERGE_Y], color=STROKE_COLOR, lw=1.6, zorder=3)
ax.plot([X_5A, X_5B], [MERGE_Y, MERGE_Y], color=STROKE_COLOR, lw=1.6, zorder=3)
draw_arrow(ax, MID_X, MERGE_Y, MID_X, 23.75)

# 6 -> Selesai
draw_arrow(ax, MID_X, 15.25, MID_X, 12.0)

plt.tight_layout()
output_png = 'Gambar_3_1_Diagram_Alir_Penelitian.png'
plt.savefig(output_png, dpi=300, facecolor='#FFFFFF', bbox_inches='tight')
plt.close()

print(f"Berhasil merender gambar ringkas 300 DPI: {output_png}")

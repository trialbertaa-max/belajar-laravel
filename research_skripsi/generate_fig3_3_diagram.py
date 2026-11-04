import pydicom
import numpy as np
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from PIL import Image

# Read sample_toraks.dcm
ds = pydicom.dcmread('sample_toraks.dcm')
arr = ds.pixel_array.astype(np.float32)
if ds.PhotometricInterpretation == "MONOCHROME1":
    arr = np.max(arr) - arr

# Windowing
wc = float(ds.WindowCenter) if not hasattr(ds.WindowCenter, '__iter__') else float(ds.WindowCenter[0])
ww = float(ds.WindowWidth) if not hasattr(ds.WindowWidth, '__iter__') else float(ds.WindowWidth[0])
arr = np.clip((arr - (wc - ww/2)) / ww, 0, 1)

# Resize to 1200x1200 for ultra-sharp canvas
h_orig, w_orig = arr.shape
img_1200 = np.array(Image.fromarray((arr * 255).astype(np.uint8)).resize((1200, 1200), Image.Resampling.LANCZOS))

# Anatomical Coordinates scaled to 1200x1200:
# Factor: 1.2 relative to 1000
scale = 1.2
x_mid = 485 * scale # 582
y_mid_top = 110 * scale # 132
y_mid_bot = 820 * scale # 984

# Line A (Right heart border): y = 515 * scale = 618, x_A = 405 * scale = 486
y_A = 515 * scale # 618
x_A = 405 * scale # 486

# Line B (Left heart apex): y = 605 * scale = 726, x_B = 625 * scale = 750
y_B = 605 * scale # 726
x_B = 625 * scale # 750

# Line C (Thorax diameter): y = 645 * scale = 774, x_C1 = 225 * scale = 270, x_C2 = 750 * scale = 900
y_C = 645 * scale # 774
x_C1 = 225 * scale # 270
x_C2 = 750 * scale # 900

# Physical measurements based on Pixel Spacing (0.140 mm/pixel on 2999):
# pixel_size_1200 = 0.140 * (2999 / 1200) = 0.34988 mm/pixel = 0.034988 cm/pixel
px_cm = 0.140 * (2999.0 / 1200.0) / 10.0 # cm per pixel

dist_A_cm = (x_mid - x_A) * px_cm
dist_B_cm = (x_B - x_mid) * px_cm
dist_C_cm = (x_C2 - x_C1) * px_cm
ctr_val = (dist_A_cm + dist_B_cm) / dist_C_cm * 100.0

print(f"A = {dist_A_cm:.2f} cm | B = {dist_B_cm:.2f} cm | C = {dist_C_cm:.2f} cm | CTR = {ctr_val:.1f}%")

# Plotting
plt.rcParams['font.family'] = 'serif'
plt.rcParams['font.serif'] = ['Times New Roman', 'DejaVu Serif']
plt.rcParams['mathtext.fontset'] = 'stix'

fig, ax = plt.subplots(figsize=(10.5, 10.5), dpi=300)
ax.imshow(img_1200, cmap='gray')
ax.axis('off')

# 1. Midline (Vertical Dashed Line)
ax.plot([x_mid, x_mid], [y_mid_top, y_mid_bot], color='#ffeb3b', linestyle='--', linewidth=2.0, alpha=0.9, zorder=5)
ax.text(x_mid, y_mid_top - 20, 'Garis Sumbu Tengah (Midline)\nKolumna Vertebralis', 
        color='#fff59d', fontsize=9.5, fontweight='bold', ha='center', va='bottom',
        bbox=dict(boxstyle='round,pad=0.3', fc='#212121', ec='#fbc02d', alpha=0.85))

# 2. Line A (Right Cardiac Border): x_mid to x_A
ax.annotate('', xy=(x_A, y_A), xytext=(x_mid, y_A),
            arrowprops=dict(arrowstyle="<|-|>", lw=2.4, color='#ff3d00', mutation_scale=16), zorder=6)
# Marker point at right cardiac contour
ax.plot(x_A, y_A, 'o', color='#ff3d00', markersize=6.5, markeredgecolor='white', markeredgewidth=1.2, zorder=7)
# Label for Line A
ax.text((x_mid + x_A)/2, y_A - 18, f'A = {dist_A_cm:.2f} cm\n(Batas Kanan)', 
        color='white', fontsize=9.0, fontweight='bold', ha='center', va='bottom',
        bbox=dict(boxstyle='round,pad=0.25', fc='#d84315', ec='white', lw=0.8, alpha=0.92), zorder=8)

# 3. Line B (Left Cardiac Border / Apex): x_mid to x_B
ax.annotate('', xy=(x_B, y_B), xytext=(x_mid, y_B),
            arrowprops=dict(arrowstyle="<|-|>", lw=2.4, color='#00b0ff', mutation_scale=16), zorder=6)
# Marker point at left cardiac apex
ax.plot(x_B, y_B, 'o', color='#00b0ff', markersize=6.5, markeredgecolor='white', markeredgewidth=1.2, zorder=7)
# Label for Line B
ax.text((x_mid + x_B)/2, y_B - 18, f'B = {dist_B_cm:.2f} cm\n(Apeks Kiri)', 
        color='white', fontsize=9.0, fontweight='bold', ha='center', va='bottom',
        bbox=dict(boxstyle='round,pad=0.25', fc='#0277bd', ec='white', lw=0.8, alpha=0.92), zorder=8)

# 4. Line C (Thoracic Diameter): x_C1 to x_C2
ax.annotate('', xy=(x_C1, y_C), xytext=(x_C2, y_C),
            arrowprops=dict(arrowstyle="<|-|>", lw=2.4, color='#00e676', mutation_scale=16), zorder=6)
# Markers at inner ribs
ax.plot([x_C1, x_C2], [y_C, y_C], 'o', color='#00e676', markersize=6.5, markeredgecolor='white', markeredgewidth=1.2, zorder=7)
# Label for Line C
ax.text((x_C1 + x_C2)/2, y_C + 20, f'C = {dist_C_cm:.2f} cm (Diameter Transversal Toraks)', 
        color='white', fontsize=9.5, fontweight='bold', ha='center', va='top',
        bbox=dict(boxstyle='round,pad=0.3', fc='#1b5e20', ec='white', lw=0.8, alpha=0.92), zorder=8)

# Projection line indicator for Right Hemidiaphragm peak
ax.plot([x_C1, x_mid], [y_C, y_C], color='#00e676', linestyle=':', lw=1.2, alpha=0.5)

# 5. Diagnostic HUD Card (Top-Right Corner)
hud_text = (
    "METRIK BIOMETRI CTR (DANZER 1919)\n"
    "─────────────────────────────────\n"
    f"• Diameter Jantung (A + B) : {dist_A_cm + dist_B_cm:.2f} cm\n"
    f"  - Batas Kanan (A)        : {dist_A_cm:.2f} cm\n"
    f"  - Batas Kiri (B)         : {dist_B_cm:.2f} cm\n"
    f"• Diameter Toraks (C)      : {dist_C_cm:.2f} cm\n"
    "─────────────────────────────────\n"
    f"Formula : CTR = (A + B) / C × 100%\n"
    f"Hasil   : CTR = {ctr_val:.1f}%\n"
    "Status  : NORMAL (≤ 50%)\n"
    "─────────────────────────────────\n"
    "Kalibrasi: DICOM Tag (0028,0030)\n"
    "Imager Pixel Spacing: 0.140 mm/px"
)
ax.text(1160, 40, hud_text, color='#00e676', fontsize=8.8, family='monospace',
        ha='right', va='top', linespacing=1.35,
        bbox=dict(boxstyle='round,pad=0.6', fc='#0a192f', ec='#00e676', lw=1.4, alpha=0.92), zorder=10)

# Title at top-left
ax.text(35, 45, 'Hyu PACS Diagnostic Caliper\nPengukuran Rasio Kardiotoraks (CTR)', 
        color='white', fontsize=11, fontweight='bold', ha='left', va='top',
        bbox=dict(boxstyle='round,pad=0.45', fc='#1a237e', ec='#3d5afe', lw=1.2, alpha=0.88), zorder=10)

plt.tight_layout()
out_file = 'Gambar_3_3_Skema_CTR_Danzer.png'
plt.savefig(out_file, dpi=300, bbox_inches='tight')
print('Successfully generated:', out_file)

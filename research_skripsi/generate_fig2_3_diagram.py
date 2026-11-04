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

# Normalize to [0, 1]
p_min, p_max = np.percentile(arr, (1, 99))
arr = np.clip((arr - p_min) / (p_max - p_min), 0, 1)

# Crop central region
h, w = arr.shape
center_crop = arr[int(h*0.35):int(h*0.75), int(w*0.25):int(w*0.65)]
clean_img = np.array(Image.fromarray((center_crop * 255).astype(np.uint8)).resize((260, 260), Image.Resampling.LANCZOS)) / 255.0

# Generate simulated noise (v)
np.random.seed(42)
noise = np.random.normal(0, 0.12, clean_img.shape)
noisy_img = np.clip(clean_img + noise, 0, 1)
residual_img = np.clip(np.abs(noise) * 2.8, 0, 1)

# Plotting
plt.rcParams['font.family'] = 'serif'
plt.rcParams['font.serif'] = ['Times New Roman', 'DejaVu Serif']
plt.rcParams['mathtext.fontset'] = 'stix'

fig, ax = plt.subplots(figsize=(15, 8.5), dpi=300)
ax.set_xlim(-0.5, 23.5)
ax.set_ylim(-6.2, 5.2)
ax.axis('off')

# ================= TOP PIPELINE: DnCNN FEATURE EXTRACTION =================
# 1. Noisy Image (Input)
ax.imshow(noisy_img, cmap='gray', extent=[0, 3.2, 0.8, 4.0])
ax.text(1.6, 4.3, 'Citra Toraks Berderau ($y$)\n$y = x + v$', ha='center', va='bottom', fontsize=11, fontweight='bold')
rect_in = patches.Rectangle((0, 0.8), 3.2, 3.2, linewidth=1.3, edgecolor='black', facecolor='none')
ax.add_patch(rect_in)

# Arrow from Input to Layer 1
ax.annotate('', xy=(4.2, 2.4), xytext=(3.3, 2.4),
            arrowprops=dict(arrowstyle="-|>", lw=2.0, color='#1565c0', mutation_scale=16))

# 2. Layer 1: Conv + ReLU
l1 = patches.FancyBboxPatch((4.3, 1.0), 1.5, 2.8, boxstyle="round,pad=0.08", 
                            edgecolor='#7cb342', facecolor='#f1f8e9', linewidth=1.6)
ax.add_patch(l1)
ax.text(5.05, 2.4, 'Conv + ReLU\n\n' + r'$64 \times 3 \times 3 \times 1$', 
        ha='center', va='center', fontsize=9.5, fontweight='bold', color='#33691e', rotation=90)
ax.text(5.05, 0.6, 'Lapisan 1', ha='center', va='top', fontsize=9.5, fontweight='bold', color='#555')

# Arrow L1 to L2
ax.annotate('', xy=(6.6, 2.4), xytext=(5.9, 2.4),
            arrowprops=dict(arrowstyle="-|>", lw=2.0, color='#1565c0', mutation_scale=16))

# 3. Layer 2: Conv + BN + ReLU
l2 = patches.FancyBboxPatch((6.7, 0.8), 1.6, 3.2, boxstyle="round,pad=0.08", 
                            edgecolor='#0288d1', facecolor='#e1f5fe', linewidth=1.6)
ax.add_patch(l2)
ax.text(7.5, 2.4, 'Conv + BN + ReLU\n\n' + r'$64 \times 3 \times 3 \times 64$', 
        ha='center', va='center', fontsize=9.5, fontweight='bold', color='#01579b', rotation=90)
ax.text(7.5, 0.4, 'Lapisan 2', ha='center', va='top', fontsize=9.5, fontweight='bold', color='#555')

# Arrow L2 to dots 1
ax.annotate('', xy=(8.9, 2.4), xytext=(8.4, 2.4),
            arrowprops=dict(arrowstyle="-|>", lw=2.0, color='#1565c0', mutation_scale=16))

# Dots 1
ax.text(9.45, 2.4, r'$\cdots$', ha='center', va='center', fontsize=22, color='#666')

# Arrow dots 1 to middle
ax.annotate('', xy=(10.5, 2.4), xytext=(10.0, 2.4),
            arrowprops=dict(arrowstyle="-|>", lw=2.0, color='#1565c0', mutation_scale=16))

# 4. Middle Layer (Layer i)
l_mid = patches.FancyBboxPatch((10.6, 0.8), 1.6, 3.2, boxstyle="round,pad=0.08", 
                              edgecolor='#0288d1', facecolor='#e1f5fe', linewidth=1.6)
ax.add_patch(l_mid)
ax.text(11.4, 2.4, 'Conv + BN + ReLU\n\n' + r'$64 \times 3 \times 3 \times 64$', 
        ha='center', va='center', fontsize=9.5, fontweight='bold', color='#01579b', rotation=90)
ax.text(11.4, 0.4, 'Lapisan 3 s.d. 15', ha='center', va='top', fontsize=9.5, fontweight='bold', color='#555')

# Arrow middle to dots 2
ax.annotate('', xy=(12.8, 2.4), xytext=(12.3, 2.4),
            arrowprops=dict(arrowstyle="-|>", lw=2.0, color='#1565c0', mutation_scale=16))

# Dots 2
ax.text(13.35, 2.4, r'$\cdots$', ha='center', va='center', fontsize=22, color='#666')

# Arrow dots 2 to L16
ax.annotate('', xy=(14.4, 2.4), xytext=(13.9, 2.4),
            arrowprops=dict(arrowstyle="-|>", lw=2.0, color='#1565c0', mutation_scale=16))

# 5. Layer 16
l16 = patches.FancyBboxPatch((14.5, 0.8), 1.6, 3.2, boxstyle="round,pad=0.08", 
                             edgecolor='#0288d1', facecolor='#e1f5fe', linewidth=1.6)
ax.add_patch(l16)
ax.text(15.3, 2.4, 'Conv + BN + ReLU\n\n' + r'$64 \times 3 \times 3 \times 64$', 
        ha='center', va='center', fontsize=9.5, fontweight='bold', color='#01579b', rotation=90)
ax.text(15.3, 0.4, 'Lapisan 16', ha='center', va='top', fontsize=9.5, fontweight='bold', color='#555')

# Arrow L16 to L17
ax.annotate('', xy=(16.8, 2.4), xytext=(16.2, 2.4),
            arrowprops=dict(arrowstyle="-|>", lw=2.0, color='#1565c0', mutation_scale=16))

# 6. Layer 17: Conv
l17 = patches.FancyBboxPatch((16.9, 1.0), 1.5, 2.8, boxstyle="round,pad=0.08", 
                             edgecolor='#f57c00', facecolor='#fff3e0', linewidth=1.6)
ax.add_patch(l17)
ax.text(17.65, 2.4, 'Conv (Rekonstruksi)\n\n' + r'$1 \times 3 \times 3 \times 64$', 
        ha='center', va='center', fontsize=9.5, fontweight='bold', color='#e65100', rotation=90)
ax.text(17.65, 0.6, 'Lapisan 17', ha='center', va='top', fontsize=9.5, fontweight='bold', color='#555')

# Arrow L17 to Residual Image
ax.annotate('', xy=(19.1, 2.4), xytext=(18.5, 2.4),
            arrowprops=dict(arrowstyle="-|>", lw=2.0, color='#1565c0', mutation_scale=16))

# 7. Residual Image R(y)
ax.imshow(residual_img, cmap='gray', extent=[19.2, 22.4, 0.8, 4.0])
ax.text(20.8, 4.3, r'Prediksi Derau Residual $\mathcal{R}(y)$' + '\n' + r'$\mathcal{R}(y) \approx v$', 
        ha='center', va='bottom', fontsize=11, fontweight='bold')
rect_res = patches.Rectangle((19.2, 0.8), 3.2, 3.2, linewidth=1.3, edgecolor='black', facecolor='none')
ax.add_patch(rect_res)

# ================= RESIDUAL SUBTRACTION & CLEAN OUTPUT =================
# Minus circle at (20.8, -0.6)
circ_sub = patches.Circle((20.8, -0.6), 0.65, edgecolor='#d32f2f', facecolor='#ffebee', linewidth=2.2)
ax.add_patch(circ_sub)
ax.text(20.8, -0.6, r'$-$', ha='center', va='center', fontsize=26, fontweight='bold', color='#b71c1c')

# Text explaining residual subtraction to the left of the circle
ax.text(19.9, -0.6, 'Operasi Residual:\n' + r'$\hat{x} = y - \mathcal{R}(y)$', 
        ha='right', va='center', fontsize=10.5, fontweight='bold', color='#b71c1c')

# Arrow 1: From Residual Image R(y) down into minus circle
ax.annotate('', xy=(20.8, 0.1), xytext=(20.8, 0.75),
            arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#d32f2f', mutation_scale=16))

# Line & Arrow 2: From Input y (Skip connection) down and across to minus circle
ax.plot([1.6, 1.6, 20.1], [0.8, -0.6, -0.6], color='#1565c0', lw=2.2, linestyle='-')
ax.annotate('', xy=(20.1, -0.6), xytext=(19.5, -0.6),
            arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#1565c0', mutation_scale=16))
ax.text(10.0, -0.3, r'Jalur Lewat Input / Skip Connection ($y$)', 
        fontsize=11, fontweight='bold', color='#0d47a1', ha='center')

# Arrow 3: From minus circle down into clean output image
ax.annotate('', xy=(20.8, -1.9), xytext=(20.8, -1.3),
            arrowprops=dict(arrowstyle="-|>", lw=2.4, color='#2e7d32', mutation_scale=18))

# Clean Image Box at [19.2, 22.4], y = [-5.2, -2.0]
ax.imshow(clean_img, cmap='gray', extent=[19.2, 22.4, -5.2, -2.0])
rect_clean = patches.Rectangle((19.2, -5.2), 3.2, 3.2, linewidth=1.6, edgecolor='#2e7d32', facecolor='none')
ax.add_patch(rect_clean)

# Labels for Clean Image to the left
ax.text(18.9, -3.6, r'Citra Toraks Terestorasi ($\hat{x}$)' + '\n' + r'Bebas Derau & Tepi Anatomi Utuh' + '\n' + r'(Edge-Preserved Clean Output)', 
        ha='right', va='center', fontsize=11, fontweight='bold', color='#1b5e20')

plt.tight_layout()
out_file = 'Gambar_2_3_DnCNN_Architecture_Full_Residual.png'
plt.savefig(out_file, dpi=300, bbox_inches='tight')
print('Successfully generated clean diagram:', out_file)

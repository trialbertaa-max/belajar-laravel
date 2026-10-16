import matplotlib.pyplot as plt
import matplotlib.gridspec as gridspec
from PIL import Image

# Load images
im_a = Image.open('step_orig.png')
im_b = Image.open('step_sim.png')
im_c = Image.open('step_filt.png')

plt.rcParams['font.family'] = 'serif'
plt.rcParams['font.serif'] = ['Times New Roman', 'DejaVu Serif']

fig, axes = plt.subplots(1, 3, figsize=(14, 4.2), dpi=300)

axes[0].imshow(im_a, cmap='gray')
axes[0].axis('off')
axes[0].set_title('(a) Profil Tepi Berderau (100 Gray-Level Step)\ndengan Derau Gaussian', fontsize=11, fontweight='bold', pad=10)

axes[1].imshow(im_b, cmap='gray')
axes[1].axis('off')
axes[1].set_title('(b) Bobot Gabungan Spasial & Kemiripan\n(Kernel Bilateral dekat Batas Tepi)', fontsize=11, fontweight='bold', pad=10)

axes[2].imshow(im_c, cmap='gray')
axes[2].axis('off')
axes[2].set_title('(c) Hasil Penyaringan Bilateral Filter\n(Batas Tepi Tetap Tajam & Tegak)', fontsize=11, fontweight='bold', pad=10)

plt.tight_layout()
out_name = 'Gambar_2_2_Tomasi_1998_Original.png'
plt.savefig(out_name, dpi=300, bbox_inches='tight')
print('Saved:', out_name)

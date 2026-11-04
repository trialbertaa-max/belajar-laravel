import numpy as np
import matplotlib.pyplot as plt
import matplotlib.gridspec as gridspec

# Set publication-quality font and styles
plt.rcParams['font.family'] = 'serif'
plt.rcParams['font.serif'] = ['Times New Roman', 'DejaVu Serif']
plt.rcParams['mathtext.fontset'] = 'stix'
plt.rcParams['font.size'] = 11

fig = plt.figure(figsize=(12, 6.5), dpi=300)
gs = gridspec.GridSpec(2, 3, figure=fig, hspace=0.35, wspace=0.28)

# Domain x
x = np.linspace(-10, 10, 400)

# 1. Step edge signal (Input)
edge_pos = 0
signal_clean = np.where(x < edge_pos, 2.0, 7.0)
np.random.seed(42)
noise = np.random.normal(0, 0.45, len(x))
signal_noisy = signal_clean + noise

# Selected center pixel x0 on the right side of the edge (x0 = 1.5)
x0 = 1.8
idx0 = np.argmin(np.abs(x - x0))
I_x0 = signal_noisy[idx0]

# --- (a) Sinyal Tepi Berderau (Input Step Edge) ---
ax1 = fig.add_subplot(gs[0, 0])
ax1.plot(x, signal_clean, 'k--', alpha=0.5, label='Sinyal Ideal')
ax1.plot(x, signal_noisy, color='#1f77b4', lw=1.2, label='Sinyal Berderau')
ax1.axvline(edge_pos, color='gray', linestyle=':', alpha=0.7)
ax1.plot(x0, I_x0, 'ro', markersize=6, label=r'Piksel Pusat $x$')
ax1.set_title('(a) Profil Tepi Berderau & Titik $x$', fontweight='bold', fontsize=11)
ax1.set_xlabel('Posisi Spasial ($p$)')
ax1.set_ylabel('Intensitas ($I$)')
ax1.set_ylim(0, 9)
ax1.legend(loc='upper left', fontsize=8.5, framealpha=0.9)
ax1.grid(True, linestyle='--', alpha=0.3)

# --- (b) Bobot Spasial / Domain Kernel ---
sigma_s = 2.5
w_spatial = np.exp(-((x - x0)**2) / (2 * sigma_s**2))

ax2 = fig.add_subplot(gs[0, 1])
ax2.plot(x, w_spatial, color='#2ca02c', lw=2, label=r'Bobot Spasial $c(\xi, x)$')
ax2.fill_between(x, w_spatial, color='#2ca02c', alpha=0.15)
ax2.axvline(x0, color='r', linestyle='--', alpha=0.6, label=r'Pusat $x$')
ax2.axvline(edge_pos, color='gray', linestyle=':', alpha=0.7, label='Batas Tepi Organ')
ax2.set_title(r'(b) Kernel Spasial / Jarak Kedekatan ($g_s$)', fontweight='bold', fontsize=11)
ax2.set_xlabel('Posisi Spasial ($p$)')
ax2.set_ylabel('Bobot ($[0, 1]$)')
ax2.set_ylim(-0.05, 1.1)
ax2.legend(loc='upper left', fontsize=8.5, framealpha=0.9)
ax2.grid(True, linestyle='--', alpha=0.3)

# --- (c) Bobot Rentang / Range Kernel ---
sigma_r = 1.0
w_range = np.exp(-((signal_noisy - I_x0)**2) / (2 * sigma_r**2))

ax3 = fig.add_subplot(gs[0, 2])
ax3.plot(x, w_range, color='#d62728', lw=1.5, label=r'Bobot Rentang $s(I(\xi), I(x))$')
ax3.fill_between(x, w_range, color='#d62728', alpha=0.15)
ax3.axvline(edge_pos, color='gray', linestyle=':', alpha=0.7, label='Batas Tepi Organ')
ax3.set_title(r'(c) Kernel Radiometrik / Kemiripan ($f_r$)', fontweight='bold', fontsize=11)
ax3.set_xlabel('Posisi Spasial ($p$)')
ax3.set_ylabel('Bobot ($[0, 1]$)')
ax3.set_ylim(-0.05, 1.1)
ax3.text(-6, 0.25, r'Penurunan drastis' + '\n' + r'di seberang batas' + '\n' + r'karena beda intensitas', 
         fontsize=8.5, color='#d62728', bbox=dict(boxstyle='round,pad=0.3', fc='white', ec='#d62728', alpha=0.8))
ax3.legend(loc='upper right', fontsize=8.5, framealpha=0.9)
ax3.grid(True, linestyle='--', alpha=0.3)

# --- (d) Kernel Komposit Bilateral (Spatial x Range) ---
w_bilateral = w_spatial * w_range

ax4 = fig.add_subplot(gs[1, 0])
ax4.plot(x, w_bilateral, color='#9467bd', lw=2.2, label=r'Bilateral = $g_s \times f_r$')
ax4.fill_between(x, w_bilateral, color='#9467bd', alpha=0.2)
ax4.axvline(edge_pos, color='gray', linestyle=':', alpha=0.7, label='Batas Tepi')
ax4.axvline(x0, color='r', linestyle='--', alpha=0.6)
ax4.set_title('(d) Bobot Terpadu Kernel Bilateral', fontweight='bold', fontsize=11)
ax4.set_xlabel('Posisi Spasial ($p$)')
ax4.set_ylabel('Bobot Kombinasi')
ax4.set_ylim(-0.05, 1.1)
ax4.text(-7, 0.6, 'Piksel di sisi kiri\ntepi diabaikan\n(Bobot $\\approx 0$)', 
         fontsize=8.5, color='#7b1fa2', bbox=dict(boxstyle='round,pad=0.3', fc='white', ec='#9467bd', alpha=0.8))
ax4.legend(loc='upper right', fontsize=8.5, framealpha=0.9)
ax4.grid(True, linestyle='--', alpha=0.3)

# Calculate filtered signals across whole range
# Standard Gaussian filter
gauss_filt = np.zeros_like(signal_noisy)
# Bilateral filter
bilat_filt = np.zeros_like(signal_noisy)

for i in range(len(x)):
    xi = x[i]
    Ii = signal_noisy[i]
    
    # spatial weights
    ws = np.exp(-((x - xi)**2) / (2 * sigma_s**2))
    gauss_filt[i] = np.sum(ws * signal_noisy) / np.sum(ws)
    
    # bilateral weights
    wr = np.exp(-((signal_noisy - Ii)**2) / (2 * sigma_r**2))
    wb = ws * wr
    bilat_filt[i] = np.sum(wb * signal_noisy) / np.sum(wb)

# --- (e) Output Filter Standar Gaussian (Pengaburan Tepi) ---
ax5 = fig.add_subplot(gs[1, 1])
ax5.plot(x, signal_clean, 'k--', alpha=0.4, label='Bentuk Asli')
ax5.plot(x, gauss_filt, color='#ff7f0e', lw=2, label='Filter Gaussian Standar')
ax5.axvline(edge_pos, color='gray', linestyle=':', alpha=0.7)
ax5.set_title('(e) Filter Gaussian Biasa (Tepi Kabur)', fontweight='bold', fontsize=11)
ax5.set_xlabel('Posisi Spasial ($p$)')
ax5.set_ylabel('Intensitas ($I$)')
ax5.set_ylim(0, 9)
ax5.text(-2.5, 4.2, r'$\leftarrow$ Lereng Pengaburan' + '\n' + r'   (Blurring Tepi)', 
         fontsize=8.5, color='#d95f02', fontweight='bold')
ax5.legend(loc='upper left', fontsize=8.5, framealpha=0.9)
ax5.grid(True, linestyle='--', alpha=0.3)

# --- (f) Output Bilateral Filter (Edge-Preserving Denoising) ---
ax6 = fig.add_subplot(gs[1, 2])
ax6.plot(x, signal_clean, 'k--', alpha=0.4, label='Bentuk Asli')
ax6.plot(x, bilat_filt, color='#1f77b4', lw=2.2, label='Bilateral Filter')
ax6.axvline(edge_pos, color='gray', linestyle=':', alpha=0.7)
ax6.set_title('(f) Bilateral Filter (Tepi Terjaga Tajam)', fontweight='bold', fontsize=11)
ax6.set_xlabel('Posisi Spasial ($p$)')
ax6.set_ylabel('Intensitas ($I$)')
ax6.set_ylim(0, 9)
ax6.text(0.3, 4.2, r'Batas Tepi Tetap Tegak $\rightarrow$' + '\n' + r'(Edge Preservation)', 
         fontsize=8.5, color='#0d47a1', fontweight='bold')
ax6.legend(loc='upper left', fontsize=8.5, framealpha=0.9)
ax6.grid(True, linestyle='--', alpha=0.3)

plt.tight_layout()
output_path = 'Gambar_2_2_Bilateral_Filter_Scientific.png'
plt.savefig(output_path, dpi=300)
print('Successfully saved:', output_path)

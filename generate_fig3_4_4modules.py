import matplotlib.pyplot as plt
import matplotlib.patches as patches

# Set academic typography
plt.rcParams['font.family'] = 'serif'
plt.rcParams['font.serif'] = ['Times New Roman', 'DejaVu Serif']
plt.rcParams['mathtext.fontset'] = 'stix'

fig, ax = plt.subplots(figsize=(10.5, 9.5), dpi=300)
ax.set_xlim(0, 10.5)
ax.set_ylim(0, 9.5)
ax.axis('off')

# Title Header
ax.text(5.25, 9.15, 'DIAGRAM ALUR KERJA PERANGKAT LUNAK DEMONSTRATOR DICOM WORKSTATION',
        ha='center', va='top', fontsize=11.5, fontweight='bold', color='#0f172a')
ax.text(5.25, 8.85, 'Arsitektur 4 Modul Terintegrasi: Ingesti DICOM, Engine Restorasi, Workstation CTR, dan Penyimpanan Hasil',
        ha='center', va='top', fontsize=8.8, fontstyle='italic', color='#475569')
ax.plot([0.6, 9.9], [8.68, 8.68], color='#334155', lw=1.2)

# Helper function to draw module card
def draw_module_box(ax, y, title_num, title_text, subtitle, items, bg_color='#ffffff', header_bg='#1e293b'):
    w = 8.6
    h = 1.30
    x = 0.95
    
    # Outer box
    box = patches.FancyBboxPatch((x, y), w, h, boxstyle="round,pad=0.04",
                                facecolor=bg_color, edgecolor='#334155', lw=1.4, zorder=2)
    ax.add_patch(box)
    
    # Header bar
    hbar = patches.Rectangle((x, y + h - 0.32), w, 0.32, facecolor=header_bg, edgecolor='#334155', lw=1.2, zorder=3)
    ax.add_patch(hbar)
    
    # Header Title
    full_title = f"{title_num}. {title_text.upper()}"
    ax.text(x + 0.25, y + h - 0.16, full_title, ha='left', va='center',
            fontsize=9.2, fontweight='bold', color='#ffffff', zorder=4)
    ax.text(x + w - 0.25, y + h - 0.16, subtitle, ha='right', va='center',
            fontsize=8.0, fontstyle='italic', color='#93c5fd', zorder=4)
    
    # Bullet Items
    line_y = y + h - 0.52
    for item in items:
        ax.text(x + 0.35, line_y, f"• {item}", ha='left', va='center',
                fontsize=8.2, color='#1e293b', zorder=4)
        line_y -= 0.24

# Helper function to draw connecting arrow
def draw_arrow(ax, y_start, y_end):
    ax.annotate('', xy=(5.25, y_end), xytext=(5.25, y_start),
                arrowprops=dict(arrowstyle="-|>", lw=2.2, color='#0284c7', mutation_scale=15), zorder=5)

# Module 1
draw_module_box(
    ax, y=6.95,
    title_num="MODUL 1", title_text="Ingesti Citra Medis (DICOM Input)",
    subtitle="Layanan Jaringan TCP/IP & Antarmuka Unggah",
    items=[
        "DICOM C-STORE SCP Daemon (pynetdicom): Menangkap transmisi dari modalitas digital (Port 4242, AE: HYU_PACS_SERVER)",
        "Fitur Unggah Mandiri: Menerima berkas mentah biner (.dcm) melalui antarmuka berbasis web",
        "Ekstraksi Metadata: Parsing identitas pasien, Pixel Spacing (0028,0030), Window Width/Center, dan larik piksel mentah"
    ],
    header_bg='#0f172a'
)

draw_arrow(ax, 6.95, 6.55)

# Module 2
draw_module_box(
    ax, y=5.10,
    title_num="MODUL 2", title_text="Pemrosesan Citra & Restorasi (Image Processing API)",
    subtitle="Python Compute Engine & Model Deep Learning",
    items=[
        "Normalisasi Intensitas: Pemetaan nilai piksel mentah 12/16-bit ke rentang skala ternormalisasi [0, 1]",
        "Inferensi Bilateral Filter: Reduksi derau berbasis pembobotan jarak spasial (\u03c3s) dan selisih intensitas (\u03c3r)",
        "Inferensi DnCNN: Model 17-lapisan residual learning (Conv+BN+ReLU) mengekstraksi residual derau \u211B(y)",
        "Telemetri Kinerja: Pencatatan waktu komputasi Inference / Restoration Latency (ms)"
    ],
    header_bg='#0369a1'
)

draw_arrow(ax, 5.10, 4.70)

# Module 3
draw_module_box(
    ax, y=3.25,
    title_num="MODUL 3", title_text="Penampil & Pengukuran (CTR Measurement Viewer)",
    subtitle="Web Diagnostic Workstation (SVG Stage Layer)",
    items=[
        "Visualisasi Web Interaktif: Penampil citra canvas dilengkapi pengaturan Windowing (W/L), Zoom, Pan, Flip, dan Rotasi",
        "Sistem Koordinat Lockstep: Transformasi CTM\u207b\u00b9 menjaga koordinat anotasi tetap menempel presisi pada piksel anatomi",
        "Kaliper CTR Danzer (1919): Penarikan garis sumbu tengah (midline), batas kardiak (MRD+MLD), dan toraks (MTD)",
        "Telemetri Interaksi: Pengukuran kecepatan respons rendering antarmuka Viewer Rendering Time (ms)"
    ],
    header_bg='#047857'
)

draw_arrow(ax, 3.25, 2.85)

# Module 4
draw_module_box(
    ax, y=1.40,
    title_num="MODUL 4", title_text="Penyimpanan & Ekspor Hasil (Result Storage and Export)",
    subtitle="Manajemen Data & Standardisasi DICOM Secondary Capture",
    items=[
        "Penyimpanan Basis Data: Menyimpan rasio CTR, status klasifikasi kardiomegali, citra terproses, dan metadata audit",
        "Ekspor DICOM Secondary Capture (SC): Menghasilkan berkas citra turunan beranotasi sesuai standar NEMA PS 3.3",
        "Ekspor Citra Resolusi Tinggi: Menyediakan berkas PNG 300 DPI dengan overlay garis ukur untuk demonstrasi"
    ],
    header_bg='#b45309'
)

# Bottom Box: Evaluasi Kinerja Sistem Perangkat Lunak
eval_box = patches.FancyBboxPatch((0.95, 0.25), 8.6, 0.85, boxstyle="round,pad=0.03",
                                 facecolor='#f8fafc', edgecolor='#64748b', linestyle='--', lw=1.2, zorder=2)
ax.add_patch(eval_box)
ax.text(5.25, 0.90, 'PARAMETER EVALUASI KELAYAKAN SISTEM DEMONSTRATOR (KUANTITATIF)',
        ha='center', va='center', fontsize=8.4, fontweight='bold', color='#1e293b', zorder=4)
ax.text(1.20, 0.62, '1. Inference / Restoration Latency (ms): Durasi komputasi inferensi model Bilateral Filter vs DnCNN per citra',
        ha='left', va='center', fontsize=7.8, color='#334155', zorder=4)
ax.text(1.20, 0.40, '2. Viewer Rendering Time (ms): Kecepatan render SVG Stage Layer & penyesuaian lockstep CTM\u207b\u00b9 saat zoom/pan',
        ha='left', va='center', fontsize=7.8, color='#334155', zorder=4)

plt.tight_layout()
out_png = 'Gambar_3_4_Demonstrator_DICOM_4_Modul.png'
plt.savefig(out_png, dpi=300, bbox_inches='tight')
print('Successfully generated publication-grade 4-module diagram:', out_png)


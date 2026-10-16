import matplotlib.pyplot as plt
import matplotlib.patches as patches

# Set Publication Quality (Times New Roman, Academic Standard)
plt.rcParams['font.family'] = 'Times New Roman'
plt.rcParams['mathtext.fontset'] = 'stix'

fig, ax = plt.subplots(figsize=(10, 14), dpi=300)
ax.set_facecolor('#FFFFFF')
fig.patch.set_facecolor('#FFFFFF')

# Diagram Canvas Limits
ax.set_xlim(0, 100)
ax.set_ylim(0, 140)
ax.axis('off')

# Title Block
ax.text(50, 137, "GAMBAR 3.4. DIAGRAM ALUR KERJA PERANGKAT LUNAK HYU PACS WORKSTATION",
        fontsize=13, fontweight='bold', ha='center', va='center', color='#0F172A')
ax.text(50, 134.5, "Alur Integrasi Ingesti Transmisi DICOM, Core Backend EMR, Engine AI, Diagnostik CTR, dan Pelaporan Medis",
        fontsize=10, fontstyle='italic', ha='center', va='center', color='#475569')
ax.plot([10, 90], [132.5, 132.5], color='#334155', linewidth=1.2)

# Define Nodes
# Each node: (id, y_top, height, title, subtitle, bullets, bg_color, border_color)
nodes = [
    {
        'id': 'M0', 'y': 118, 'h': 11.5,
        'title': 'MODALITAS RADIOGRAFI DIGITAL (DR)',
        'sub': 'Konsol Radiografi Digital Klinik CDC Surabaya (Standar DICOM 3.0)',
        'bullets': [
            '• Detektor: Flat-Panel Detector (FPD) 16-bit Grayscale (Matriks 2999 × 2999 Piksel)',
            '• Protokol: Transmisi Layanan DICOM C-STORE SCU via Jaringan Lokal (TCP/IP LAN)',
            '• Tag Kalibrasi Fisik: (0028,0030) ImagerPixelSpacing = 0.140 mm/px, Slope, & Intercept'
        ],
        'bg': '#F8FAFC', 'border': '#1E293B', 'sub_color': '#334155'
    },
    {
        'id': 'M1', 'y': 99.5, 'h': 12,
        'title': 'MODUL 1: DICOM LISTENER (C-STORE SCP DAEMON)',
        'sub': 'Layanan Penerima Transmisi Latar Belakang (pynetdicom & pydicom Service)',
        'bullets': [
            '• Parameter Jaringan: Listening Port 4242 | Application Entity Title: HYU_PACS_SERVER',
            '• Kepatuhan NEMA PS 3.4: Validasi SOP Class UID & Negosiasi Transfer Syntax Medis',
            '• Ekstraksi Tag Header: Parsing Metadata Pasien, Window Width/Level, & Matriks Piksel'
        ],
        'bg': '#FFFFFF', 'border': '#0F172A', 'sub_color': '#0369A1'
    },
    {
        'id': 'M2', 'y': 80, 'h': 13,
        'title': 'MODUL 2: CORE BACKEND & INTEGRASI EMR (LARAVEL 11)',
        'sub': 'Manajemen Basis Data Relasional, Repositori Berkas, & Routing API',
        'bullets': [
            '• Basis Data MySQL: Manajemen Rekam Medis (Nama, NIK, No. RM, Study UID, Tanggal)',
            '• Repositori Server: Pengarsipan Berkas Raw .dcm ke Direktori storage/app/dicom/raw/',
            '• Keamanan Sistem: Otentikasi & Otorisasi Pengguna Berbasis Peran (Role-Based Access)',
            '• Integrasi Alur Kerja: Live Insertion Templat Ekspertise & Dispatch Job Restorasi AI'
        ],
        'bg': '#FFFFFF', 'border': '#0F172A', 'sub_color': '#0F172A'
    },
    {
        'id': 'M3', 'y': 60.5, 'h': 13,
        'title': 'MODUL 3: ENGINE RESTORASI CITRA & AI (PYTHON ENVIRONMENT)',
        'sub': 'Multi-Algorithm Denoising Engine & Evaluasi Telemetri Kinerja Kuantitatif',
        'bullets': [
            '• Bilateral Filter: Domain Spasial (σ_s) & Range (σ_r) Preservasi Tepi Batas Organ',
            '• Non-Local Means (NLM): Eksploitasi Redundansi Patch Citra Mandiri (Self-Similarity)',
            '• Deep Residual CNN (DnCNN): 17 Lapisan Konvolusi (Conv+BN+ReLU), Residual x = y - R(y)',
            '• Telemetri Real-Time: Kalkulasi Nilai PSNR (dB), SSIM, Latensi (ms), & Memori'
        ],
        'bg': '#FFFFFF', 'border': '#0F172A', 'sub_color': '#047857'
    },
    {
        'id': 'M4', 'y': 41, 'h': 13,
        'title': 'MODUL 4: WEB DIAGNOSTIC WORKSTATION (HYU PACS VIEWER)',
        'sub': 'Zero-Footprint Clinical Viewer (HTML5 Canvas & Custom SVG Stage Layer)',
        'bullets': [
            '• Manipulasi Citra: Windowing (W/L), Zoom/Pan Sinkron, Rotasi (90°, 180°, 270°), & Flip',
            '• Lockstep Anchoring: Matriks CTM⁻¹ menjaga titik anotasi tetap presisi pada anatomi',
            '• Kaliper Biometri CTR Danzer: Penarikan 2-Langkah ((A+B)/C × 100%) & Status Kardiomegali',
            '• Komparasi Klinis: Split-Curtain Comparison Slider & Difference Map |Inoisy - Iclean|'
        ],
        'bg': '#FFFFFF', 'border': '#0F172A', 'sub_color': '#3730A3'
    },
    {
        'id': 'M5', 'y': 22.5, 'h': 12,
        'title': 'MODUL 5: PELAPORAN MEDIS & EKSPOR CITRA (NEMA PS 3.3)',
        'sub': 'Output Medis Resmi, Penerbitan Lembar Hasil, & Ekspor Secondary Capture',
        'bullets': [
            '• Penerbitan Dokumen PDF: Lembar Hasil Pemeriksaan Radiologi Siap Cetak (KOP CDC)',
            '• Ekspor Secondary Capture (SC): Burn-In Overlay Data Pasien, Garis Ukur CTR & Rasio',
            '• Kepatuhan Standar Medis: Berkas DICOM SC Standar PS 3.3 & Citra Resolusi Tinggi (300 DPI)'
        ],
        'bg': '#FFFFFF', 'border': '#0F172A', 'sub_color': '#B45309'
    },
    {
        'id': 'RAD', 'y': 6, 'h': 10,
        'title': 'RADIOLOG / DOKTER SPESIALIS RADIOLOGI',
        'sub': 'Pengguna Akhir Workstation Klinis (Clinical End-User)',
        'bullets': [
            '• Evaluasi Diagnostik: Interpretasi Citra Toraks, Verifikasi Garis CTR, & Penegakan Diagnosis',
            '• Pengarsipan EMR: Persetujuan Laporan Ekspertise Radiologi ke Rekam Medis Elektronik'
        ],
        'bg': '#F8FAFC', 'border': '#1E293B', 'sub_color': '#334155'
    }
]

# Edge labels
edge_labels = [
    "1. Transmisi Berkas DICOM Mentah (C-STORE SCU via TCP/IP)",
    "2. Ekstraksi Tag Header Metadata & Penyimpanan Berkas .dcm Mentah",
    "3. Ekstraksi Matriks Piksel 16-bit & Trigger Job Restorasi Multi-Algoritma",
    "4. Citra Tereduksi Derau & Telemetri Kinerja Kuantitatif (PSNR / SSIM / Latensi)",
    "5. Nilai Rasio CTR (%), Status Diagnostik Kardiomegali, & Koordinat Anotasi SVG",
    "6. Penerbitan Laporan Ekspertise PDF & Berkas DICOM Secondary Capture"
]

# Draw nodes
box_left = 12
box_width = 76

for n in nodes:
    rect = patches.FancyBboxPatch(
        (box_left, n['y']), box_width, n['h'],
        boxstyle="round,pad=0.2,rounding_size=0.8",
        linewidth=1.4, edgecolor=n['border'], facecolor=n['bg']
    )
    ax.add_patch(rect)
    
    # Title
    ax.text(50, n['y'] + n['h'] - 2.0, n['title'],
            fontsize=10.5, fontweight='bold', ha='center', va='center', color='#0F172A')
    # Subtitle
    ax.text(50, n['y'] + n['h'] - 4.0, n['sub'],
            fontsize=8.5, fontstyle='italic', ha='center', va='center', color=n['sub_color'])
    
    # Divider line
    ax.plot([box_left + 2, box_left + box_width - 2], [n['y'] + n['h'] - 5.2, n['y'] + n['h'] - 5.2],
            color='#E2E8F0', linewidth=0.8)
    
    # Bullets
    y_text = n['y'] + n['h'] - 6.8
    for bullet in n['bullets']:
        ax.text(box_left + 3, y_text, bullet,
                fontsize=8.2, ha='left', va='center', color='#1E293B')
        y_text -= 1.8

# Draw Connectors
for i in range(len(nodes) - 1):
    n_curr = nodes[i]
    n_next = nodes[i+1]
    
    y_start = n_curr['y']
    y_end = n_next['y'] + n_next['h']
    
    # Arrow line
    ax.annotate(
        '', xy=(50, y_end), xytext=(50, y_start),
        arrowprops=dict(arrowstyle='->,head_width=0.3,head_length=0.4',
                        color='#0F172A', lw=1.3)
    )
    
    # Arrow label text box
    y_mid = (y_start + y_end) / 2
    label_text = edge_labels[i]
    ax.text(
        50, y_mid, label_text,
        fontsize=7.8, fontweight='bold', ha='center', va='center', color='#0F172A',
        bbox=dict(boxstyle='square,pad=0.25', facecolor='#FFFFFF', edgecolor='none')
    )

plt.tight_layout()
output_path = 'd:/laragon/www/belajar-laravel/Gambar_3_4_Alur_Kerja_Hyu_PACS_Academic.png'
plt.savefig(output_path, dpi=300, bbox_inches='tight')
plt.close()

print("Successfully generated clean academic figure at:", output_path)

import docx
from docx.shared import Pt, Inches, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml, OxmlElement
from docx.oxml.ns import nsdecls, qn

def set_run_font(r, font_name='Times New Roman', size_pt=12, bold=False, italic=False):
    r.font.name = font_name
    r.font.size = Pt(size_pt)
    r.bold = bold
    r.italic = italic

def format_p(p, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=0, space_before_pt=0, line_spacing=1.5, first_line_indent_pt=28.35):
    p.alignment = align
    p.paragraph_format.line_spacing = line_spacing
    p.paragraph_format.space_after = Pt(space_after_pt)
    p.paragraph_format.space_before = Pt(space_before_pt)
    p.paragraph_format.first_line_indent = Pt(first_line_indent_pt)

def add_styled_run(p, text, bold=False, italic=False, size_pt=12):
    r = p.add_run(text)
    set_run_font(r, bold=bold, italic=italic, size_pt=size_pt)
    return r

def main():
    doc = docx.Document()

    # Standard Skripsi Margins (Left 4cm, Top/Right/Bottom 3cm)
    for section in doc.sections:
        section.top_margin = Inches(1.18)    # 3 cm
        section.bottom_margin = Inches(1.18) # 3 cm
        section.left_margin = Inches(1.57)   # 4 cm
        section.right_margin = Inches(1.18)  # 3 cm

    # --- JUDUL BAB III ---
    p_bab = doc.add_paragraph()
    format_p(p_bab, align=WD_ALIGN_PARAGRAPH.CENTER, space_after_pt=0, space_before_pt=0, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(p_bab, "BAB III", bold=True, size_pt=12)

    p_judul_bab = doc.add_paragraph()
    format_p(p_judul_bab, align=WD_ALIGN_PARAGRAPH.CENTER, space_after_pt=12, space_before_pt=0, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(p_judul_bab, "METODE PENELITIAN", bold=True, size_pt=12)

    # --- SUBBAB 3.1 ---
    h2 = doc.add_paragraph()
    format_p(h2, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=6, space_before_pt=6, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h2, "3.1\tDesain Penelitian", bold=True, size_pt=12)

    # Paragraf 1: Pendekatan Penelitian
    p1 = doc.add_paragraph()
    format_p(p1)
    add_styled_run(p1, "Penelitian ini menggunakan rancangan penelitian eksperimental komparatif laboratoris berbasis komputasi (")
    add_styled_run(p1, "in silico comparative experimental study", italic=True)
    add_styled_run(p1, ") dengan pendekatan kuantitatif. Desain ini diterapkan untuk menguji secara empiris efektivitas metode penapisan derau non-linear spasial (")
    add_styled_run(p1, "Bilateral Filter", italic=True)
    add_styled_run(p1, ") dan model jaringan saraf tiruan konvolusional residual (")
    add_styled_run(p1, "Denoising Convolutional Neural Network", italic=True)
    add_styled_run(p1, " atau DnCNN) dalam merestorasi citra radiografi toraks proyeksi Posteroanterior (PA) yang terdegradasi oleh derau sintetis Poisson-Gaussian dosis rendah. Evaluasi penelitian difokuskan pada paradigma evaluasi berbasis tugas klinis (")
    add_styled_run(p1, "task-based evaluation", italic=True)
    add_styled_run(p1, ") guna menganalisis korelasi antara metrik fidelitas citra global dengan kemampuan pelestarian batas kontur anatomi kardioperikardial serta tingkat akurasi pengukuran biometrik ")
    add_styled_run(p1, "Cardiothoracic Ratio", italic=True)
    add_styled_run(p1, " (CTR) terhadap standar referensi penelitian (")
    add_styled_run(p1, "reference standard", italic=True)
    add_styled_run(p1, ").")

    # Paragraf 2: Variabel Penelitian Pengantar
    p2 = doc.add_paragraph()
    format_p(p2)
    add_styled_run(p2, "Untuk menjamin keterukuran dan validitas ilmiah eksperimen, variabel-variabel dalam penelitian ini diklasifikasikan sebagai berikut:")

    # Poin a: Variabel Bebas
    p_va = doc.add_paragraph()
    format_p(p_va, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_va, "a.\tVariabel Bebas (", bold=True)
    add_styled_run(p_va, "Independent Variables", bold=True, italic=True)
    add_styled_run(p_va, "): Variabel yang dimanipulasi secara terkontrol dalam eksperimen, meliputi: (1) tingkat variansi derau campuran Poisson-Gaussian sintetis yang menyimulasikan paparan dosis radiasi rendah (\u03c3n = 15, 25, dan 50); serta (2) metode perlakuan restorasi citra yang mencakup lima kondisi komparatif, yaitu ")
    add_styled_run(p_va, "Clean Reference", italic=True)
    add_styled_run(p_va, ", ")
    add_styled_run(p_va, "Noisy Baseline", italic=True)
    add_styled_run(p_va, ", ")
    add_styled_run(p_va, "Gaussian Filter Baseline", italic=True)
    add_styled_run(p_va, " (penapis linier tanpa pelestarian tepi), ")
    add_styled_run(p_va, "Bilateral Filter", italic=True)
    add_styled_run(p_va, ", dan DnCNN 17 lapis konvolusi.")

    # Poin b: Variabel Terikat
    p_vb = doc.add_paragraph()
    format_p(p_vb, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_vb, "b.\tVariabel Terikat (", bold=True)
    add_styled_run(p_vb, "Dependent Variables", bold=True, italic=True)
    add_styled_run(p_vb, "): Parameter luaran yang diukur sebagai respons terhadap perlakuan restorasi, mencakup: (1) metrik kualitas citra global, yaitu ")
    add_styled_run(p_vb, "Peak Signal-to-Noise Ratio", italic=True)
    add_styled_run(p_vb, " (PSNR) dan ")
    add_styled_run(p_vb, "Structural Similarity Index Measure", italic=True)
    add_styled_run(p_vb, " (SSIM); (2) metrik pelestarian batas anatomi (")
    add_styled_run(p_vb, "boundary preservation", italic=True)
    add_styled_run(p_vb, "), yaitu ")
    add_styled_run(p_vb, "Dice Similarity Coefficient", italic=True)
    add_styled_run(p_vb, " (DSC), ")
    add_styled_run(p_vb, "Hausdorff Distance", italic=True)
    add_styled_run(p_vb, " persentil ke-95 (HD95), dan ")
    add_styled_run(p_vb, "Mean Surface Distance", italic=True)
    add_styled_run(p_vb, " (MSD); (3) metrik akurasi biometrik CTR, yaitu ")
    add_styled_run(p_vb, "Mean Absolute Error", italic=True)
    add_styled_run(p_vb, " (MAE), ")
    add_styled_run(p_vb, "Mean Error", italic=True)
    add_styled_run(p_vb, " (Bias), korelasi Pearson (")
    add_styled_run(p_vb, "r", italic=True)
    add_styled_run(p_vb, "), dan rentang batas kesepakatan (")
    add_styled_run(p_vb, "Limits of Agreement", italic=True)
    add_styled_run(p_vb, " atau LoA) Bland–Altman; serta (4) metrik performa perangkat lunak demonstrator, yaitu ")
    add_styled_run(p_vb, "Inference Latency", italic=True)
    add_styled_run(p_vb, " (ms) dan ")
    add_styled_run(p_vb, "Viewer Rendering Time", italic=True)
    add_styled_run(p_vb, " (ms).")

    # Poin c: Variabel Terkontrol
    p_vc = doc.add_paragraph()
    format_p(p_vc, space_after_pt=4, first_line_indent_pt=0)
    add_styled_run(p_vc, "c.\tVariabel Terkontrol (", bold=True)
    add_styled_run(p_vc, "Controlled Variables", bold=True, italic=True)
    add_styled_run(p_vc, "): Parameter yang dipertahankan konstan selama pengujian, meliputi: (1) jenis proyeksi radiografi (proyeksi PA posisi tegak pasien dewasa dengan derajat inspirasi optimal); (2) protokol penyeragaman polaritas fotometrik citra DICOM (seluruh citra dikonversi secara konsisten ke MONOCHROME2) dan penormalan rentang intensitas [0, 1]; serta (3) kaidah penentuan garis acuan anatomis (")
    add_styled_run(p_vc, "midline", italic=True)
    add_styled_run(p_vc, " pada prosesus spinosus vertebra torakalis) dan formulasi pengukuran CTR Danzer (1919).")

    # Paragraf 3: Alur Tahapan Penelitian
    p3 = doc.add_paragraph()
    format_p(p3)
    add_styled_run(p3, "Pelaksanaan penelitian dirancang secara bertahap dan terintegrasi melalui enam fase utama:")

    stages = [
        ("Fase 1: Persiapan, Kurasi Dataset, dan Tata Kelola Etik", 
         "Meliputi studi literatur komprehensif, pengajuan persetujuan kaji etik (ethical clearance), serta kurasi dataset radiografi toraks PA dari repositori standar internasional (JSRT dan VinDr-CXR) dan data klinis teranonimisasi dari CDC Surabaya sebagai kelompok validasi eksternal."),
        ("Fase 2: Prapemrosesan Citra DICOM dan Pemodelan Derau", 
         "Meliputi standardisasi citra melalui 7 tahapan prapemrosesan terstruktur (pembacaan array piksel mentah, pemeriksaan inversi Photometric Interpretation, normalisasi skala [0, 1], percentile clipping, penyesuaian ukuran), serta simulasi injeksi derau campuran Poisson-Gaussian terkontrol pada variansi bertingkat (\u03c3n = 15, 25, 50)."),
        ("Fase 3: Optimasi Parameter Bilateral Filter dan Pelatihan DnCNN", 
         "Meliputi optimasi sistematik pasangan parameter (\u03c3s, \u03c3r) Bilateral Filter melalui penelusuran kisi (Grid Search) pada validation set, serta pelatihan dan fine-tuning model DnCNN 17 lapis dengan skema pembagian data patient-wise split (70% latih, 15% validasi, 15% uji), pemotongan patch citra (40\u00d740 piksel), dan optimasi fungsi rugi Mean Squared Error (L2 Loss)."),
        ("Fase 4: Ekstraksi Kontur Batas Anatomi dan Pengukuran CTR", 
         "Meliputi penentuan kontur kardioperikardial dan dinding toraks secara independen pada setiap kondisi citra terestorasi menggunakan model dan kaidah landmark yang seragam, dilanjutkan dengan kalkulasi diameter jantung (DH), diameter toraks (DT), dan nilai rasio CTR."),
        ("Fase 5: Evaluasi Kuantitatif dan Analisis Statistik Inferensial", 
         "Meliputi penghitungan metrik kualitas citra (PSNR, SSIM), evaluasi pelestarian batas anatomi (DSC, HD95, MSD), penghitungan galat biometrik CTR (MAE, Bias, korelasi Pearson), serta uji signifikansi statistik (Shapiro–Wilk, Repeated Measures ANOVA atau Friedman, dan uji post-hoc paired comparisons dengan penyesuaian koreksi Holm) bersama analisis kesepakatan klinis Bland–Altman."),
        ("Fase 6: Integrasi dan Evaluasi Demonstrator Web-Based DICOM Workstation", 
         "Meliputi pengintegrasian model restorasi terpilih dan modul pengukuran CTR ke dalam purwarupa stasiun kerja DICOM berbasis web 4 modul inti (DICOM Input, Processing API, Measurement Viewer, dan Result Storage/Export) serta pengujian latensi komputasi inferensi dan waktu rendering antarmuka.")
    ]

    for title, desc in stages:
        p_st = doc.add_paragraph()
        format_p(p_st, space_after_pt=2, first_line_indent_pt=0)
        add_styled_run(p_st, title + ": ", bold=True)
        add_styled_run(p_st, desc)

    # Paragraf Pengantar Gambar 3.1
    p_fig = doc.add_paragraph()
    format_p(p_fig)
    add_styled_run(p_fig, "Keterkaitan logis dan alur eksekusi komputasi antar-tahapan eksperimen tersebut dirangkum secara sistematis pada diagram alir Gambar 3.1.")

    p_cap = doc.add_paragraph()
    format_p(p_cap, align=WD_ALIGN_PARAGRAPH.CENTER, space_after_pt=6, space_before_pt=4, line_spacing=1.0, first_line_indent_pt=0)
    add_styled_run(p_cap, "Gambar 3.1 Diagram Alir Tahapan Penelitian Komparasi Restorasi Citra dan Evaluasi CTR", bold=True, size_pt=11)

    out_path = r'd:\laragon\www\belajar-laravel\SUBBAB_3_1_DESAIN_PENELITIAN.docx'
    doc.save(out_path)
    print("Successfully generated:", out_path)

if __name__ == '__main__':
    main()


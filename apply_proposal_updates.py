import docx
from docx.shared import Pt, Inches, Twips, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.text.paragraph import Paragraph

def set_run_font(r, font_name='Times New Roman', size_pt=12, bold=False, italic=False):
    r.font.name = font_name
    r.font.size = Pt(size_pt)
    r.bold = bold
    r.italic = italic

def format_p(p, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=0, line_spacing=1.5, left_indent_pt=0, first_line_indent_pt=0):
    p.alignment = align
    p.paragraph_format.line_spacing = line_spacing
    p.paragraph_format.space_after = Pt(space_after_pt)
    if left_indent_pt > 0:
        p.paragraph_format.left_indent = Pt(left_indent_pt)
    if first_line_indent_pt > 0:
        p.paragraph_format.first_line_indent = Pt(first_line_indent_pt)

def insert_p_before(target_p, style_name='Normal'):
    new_p_elm = OxmlElement('w:p')
    target_p._p.addprevious(new_p_elm)
    new_p = Paragraph(new_p_elm, target_p._parent)
    try:
        new_p.style = target_p._parent.part.document.styles[style_name]
    except Exception:
        pass
    return new_p

def remove_p(p):
    p._p.getparent().remove(p._p)

def run():
    doc_path = 'd:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI.docx'
    doc = docx.Document(doc_path)
    print(f"Total initial paragraphs: {len(doc.paragraphs)}")

    # ----------------------------------------------------
    # 1. UPDATE SECTION 2.6.2 (Arsitektur Web Workstation)
    # ----------------------------------------------------
    p201_idx = None
    for i, p in enumerate(doc.paragraphs):
        if 'Penerapan workstation medis modern mengarah pada arsitektur zero-footprint' in p.text:
            p201_idx = i
            break
    
    if p201_idx is not None:
        p_target = doc.paragraphs[p201_idx]
        p_target.text = ""
        format_p(p_target, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=0, line_spacing=1.5, first_line_indent_pt=20)
        
        # Add rich text runs
        r = p_target.add_run("Penerapan stasiun kerja medis (")
        set_run_font(r, italic=False)
        r = p_target.add_run("workstation")
        set_run_font(r, italic=True)
        r = p_target.add_run(") modern saat ini bertransformasi menuju arsitektur ")
        set_run_font(r, italic=False)
        r = p_target.add_run("Zero-Footprint Web-Based Medical DICOM Workstation")
        set_run_font(r, italic=True)
        r = p_target.add_run(" yang dapat diakses secara langsung melalui peramban web (")
        set_run_font(r, italic=False)
        r = p_target.add_run("web browser")
        set_run_font(r, italic=True)
        r = p_target.add_run(") tanpa memerlukan instalasi perangkat lunak tambahan maupun ")
        set_run_font(r, italic=False)
        r = p_target.add_run("plugin")
        set_run_font(r, italic=True)
        r = p_target.add_run(" khusus pada komputer klien. Arsitektur ini menggabungkan ")
        set_run_font(r, italic=False)
        r = p_target.add_run("framework")
        set_run_font(r, italic=True)
        r = p_target.add_run(" Laravel (PHP 8.2+) di sisi ")
        set_run_font(r, italic=False)
        r = p_target.add_run("backend")
        set_run_font(r, italic=True)
        r = p_target.add_run(" untuk manajemen rekam medis elektronik (")
        set_run_font(r, italic=False)
        r = p_target.add_run("Electronic Medical Record")
        set_run_font(r, italic=True)
        r = p_target.add_run(" / EMR) dan autentikasi pengguna, layanan ")
        set_run_font(r, italic=False)
        r = p_target.add_run("daemon")
        set_run_font(r, italic=True)
        r = p_target.add_run(" DICOM C-STORE SCP (berbasis Python ")
        set_run_font(r, italic=False)
        r = p_target.add_run("pynetdicom")
        set_run_font(r, italic=True)
        r = p_target.add_run(") pada ")
        set_run_font(r, italic=False)
        r = p_target.add_run("Port")
        set_run_font(r, italic=True)
        r = p_target.add_run(" 4242 untuk penerimaan citra rontgen langsung dari modalitas secara ")
        set_run_font(r, italic=False)
        r = p_target.add_run("real-time")
        set_run_font(r, italic=True)
        r = p_target.add_run(", serta ")
        set_run_font(r, italic=False)
        r = p_target.add_run("engine")
        set_run_font(r, italic=True)
        r = p_target.add_run(" visualisasi penampil ")
        set_run_font(r, italic=False)
        r = p_target.add_run("frontend")
        set_run_font(r, italic=True)
        r = p_target.add_run(" berbasis SVG ")
        set_run_font(r, italic=False)
        r = p_target.add_run("Stage Layer")
        set_run_font(r, italic=True)
        r = p_target.add_run(" dengan pemetaan transformasi matriks afina CTM (")
        set_run_font(r, italic=False)
        r = p_target.add_run("Current Transformation Matrix")
        set_run_font(r, italic=True)
        r = p_target.add_run("). Arsitektur SVG ")
        set_run_font(r, italic=False)
        r = p_target.add_run("Stage Layer")
        set_run_font(r, italic=True)
        r = p_target.add_run(" tersebut menjamin kelekatan presisi (")
        set_run_font(r, italic=False)
        r = p_target.add_run("lockstep coordinate binding")
        set_run_font(r, italic=True)
        r = p_target.add_run(") dari seluruh garis ukur dan anotasi biometrik klinis terhadap citra saat dilakukan manipulasi tampilan (")
        set_run_font(r, italic=False)
        r = p_target.add_run("zoom")
        set_run_font(r, italic=True)
        r = p_target.add_run(", ")
        set_run_font(r, italic=False)
        r = p_target.add_run("pan")
        set_run_font(r, italic=True)
        r = p_target.add_run(", ")
        set_run_font(r, italic=False)
        r = p_target.add_run("rotate")
        set_run_font(r, italic=True)
        r = p_target.add_run(", dan ")
        set_run_font(r, italic=False)
        r = p_target.add_run("flip")
        set_run_font(r, italic=True)
        r = p_target.add_run("). Selain itu, sistem terhubung secara terpadu dengan ")
        set_run_font(r, italic=False)
        r = p_target.add_run("engine")
        set_run_font(r, italic=True)
        r = p_target.add_run(" komputasi Python (PyTorch dan OpenCV) untuk mengeksekusi algoritma restorasi citra (")
        set_run_font(r, italic=False)
        r = p_target.add_run("Bilateral Filter")
        set_run_font(r, italic=True)
        r = p_target.add_run(", NLM, dan DnCNN), evaluasi kualitas citra kuantitatif (PSNR, SSIM, dan ")
        set_run_font(r, italic=False)
        r = p_target.add_run("SNR Gain")
        set_run_font(r, italic=True)
        r = p_target.add_run("), serta penerbitan laporan diagnostik kardiomegali berstandar ")
        set_run_font(r, italic=False)
        r = p_target.add_run("DICOM Secondary Capture")
        set_run_font(r, italic=True)
        r = p_target.add_run(".")
        set_run_font(r, italic=False)
        print("Updated Section 2.6.2 successfully.")

    # ----------------------------------------------------
    # 2. UPDATE SECTION 3.2 (Alat dan Bahan)
    # ----------------------------------------------------
    # Locate Alat dan Bahan heading and Diagram Alir Penelitian heading
    h_alat_idx = None
    h_diagram_idx = None
    for i, p in enumerate(doc.paragraphs):
        if p.text.strip() == 'Alat dan Bahan' and p.style.name.startswith('Heading'):
            h_alat_idx = i
        if 'Diagram Alir Penelitian' in p.text and p.style.name.startswith('Heading'):
            h_diagram_idx = i
            break
    
    print(f"Alat dan Bahan heading at [{h_alat_idx}], Diagram Alir at [{h_diagram_idx}]")
    
    if h_alat_idx is not None and h_diagram_idx is not None:
        target_diagram_p = doc.paragraphs[h_diagram_idx]
        
        # Collect paragraphs to delete between h_alat_idx+1 and h_diagram_idx-1
        paragraphs_to_remove = [doc.paragraphs[k] for k in range(h_alat_idx + 1, h_diagram_idx)]
        
        # We will insert new paragraphs before target_diagram_p
        # 1. Paragraf Pengantar
        p_new = insert_p_before(target_diagram_p, 'Normal')
        format_p(p_new, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=4, line_spacing=1.5, first_line_indent_pt=20)
        r = p_new.add_run("Dalam melaksanakan penelitian ini, diperlukan seperangkat alat (perangkat keras dan perangkat lunak) serta bahan penelitian sebagai berikut:")
        set_run_font(r)

        # 2. Kategori: Perangkat Keras
        p_new = insert_p_before(target_diagram_p, 'List Paragraph')
        format_p(p_new, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=2, line_spacing=1.5, left_indent_pt=20)
        r = p_new.add_run("1.  Perangkat Keras (")
        set_run_font(r, bold=True)
        r = p_new.add_run("Hardware")
        set_run_font(r, bold=True, italic=True)
        r = p_new.add_run("):")
        set_run_font(r, bold=True)

        # Hardware items
        hw_items = [
            ("a.  Komputer Workstation / Laptop Pengembang: ", "Perangkat komputasi utama dengan spesifikasi prosesor Intel Core i7 / AMD Ryzen 7 (Multi-Core 8-Core/16-Thread), memori utama RAM 16 GB DDR4/DDR5, media penyimpanan berkecepatan tinggi SSD NVMe 512 GB/1 TB untuk membaca dan menulis berkas citra medis DICOM resolusi matriks tinggi (2048×2048 hingga 3000×3000 piksel), serta unit pengolah grafis (GPU) NVIDIA GeForce RTX Series (mendukung akselerasi komputasi paralel CUDA) untuk proses pelatihan serta inferensi model deep learning DnCNN dan eksekusi filter spasial."),
            ("b.  Modalitas Radiografi Digital Klinis: ", "Unit Digital Radiography (DR) klinik tipe Fujifilm FDR D-EVO II / FDR Smart X dan konsol DR Mindray di Instalasi Radiologi PT Cahaya Medika Healthcare (Cahaya Diagnostic Centre / CDC Surabaya) yang terhubung pada jaringan lokal (LAN TCP/IP)."),
            ("c.  Perangkat Layar Tampilan (Monitor) & Input Presisi: ", "Monitor diagnostik medis / panel IPS beresolusi minimal Full HD (1920×1080) hingga QHD dengan rasio kontras tinggi dan cakupan warna sRGB >99% untuk visualisasi akurat citra radiografi toraks, serta perangkat mouse optik presisi tinggi untuk manipulasi penarikan garis ukur anotasi digital caliper dan penentuan titik anatomi kardiotoraks.")
        ]
        for prefix, desc in hw_items:
            p_new = insert_p_before(target_diagram_p, 'List Paragraph')
            format_p(p_new, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=2, line_spacing=1.5, left_indent_pt=40)
            r = p_new.add_run(prefix)
            set_run_font(r, bold=True)
            r = p_new.add_run(desc)
            set_run_font(r)

        # 3. Kategori: Perangkat Lunak
        p_new = insert_p_before(target_diagram_p, 'List Paragraph')
        format_p(p_new, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=2, line_spacing=1.5, left_indent_pt=20)
        r = p_new.add_run("2.  Perangkat Lunak (")
        set_run_font(r, bold=True)
        r = p_new.add_run("Software")
        set_run_font(r, bold=True, italic=True)
        r = p_new.add_run("):")
        set_run_font(r, bold=True)

        # Software items
        sw_items = [
            ("a.  Sistem Operasi: ", "Microsoft Windows 11 Pro 64-bit sebagai lingkungan utama komputasi, pengembangan, dan pengujian sistem stasiun kerja medis."),
            ("b.  Lingkungan Server & Web Backend: ", "Laragon Development Stack mencakup interpreter PHP versi 8.2 ke atas, Web Server Apache/Nginx, dan basis data relasional (MySQL / SQLite), serta Framework Laravel versi 11 sebagai arsitektur backend MVC (Model-View-Controller) yang mengelola autentikasi pengguna, basis data rekam medis pasien MCU, manajemen antrean citra, API routing, dan integrasi formulir Electronic Medical Record (EMR)."),
            ("c.  Layanan Komunikasi & Jaringan DICOM: ", "Modul Python pynetdicom (v2.0+) yang dikonfigurasi sebagai layanan daemon DICOM C-STORE Service Class Provider (SCP) pada Port jaringan 4242 dengan Application Entity Title (AE Title) HYU_PACS_SERVER untuk menangkap dan menerima aliran berkas DICOM rontgen toraks secara real-time langsung dari modalitas rontgen digital klinik."),
            ("d.  Pustaka Pemrosesan Citra & Komputasi Medis (Python 3.10+): ", "Pustaka terpadu mencakup: (1) pydicom (v2.4+) untuk membaca dan mengekstrak metadata standar DICOM PS 3.3/3.6, normalisasi intensitas piksel (Rescale Slope/Intercept dan Window Width/Center), serta ekstraksi resolusi spasial fisik tag Pixel Spacing (0028,0030) atau Imager Pixel Spacing (0018,1164); (2) PyTorch (torch, torchvision) dengan akselerasi GPU CUDA untuk inferensi model deep learning Denoising Convolutional Neural Network (DnCNN 17-lapisan residual learning); (3) OpenCV (opencv-python) untuk implementasi algoritma Bilateral Filter, Non-Local Means (NLM), Gaussian filter, Median filter, CLAHE, dan Unsharp Masking; (4) scikit-image (skimage.metrics) untuk kalkulasi kuantitatif Peak Signal-to-Noise Ratio (PSNR) dan Structural Similarity Index (SSIM); (5) NumPy dan SciPy untuk manipulasi matriks tensor, evaluasi SNR Gain, dan pengukuran latensi komputasi; serta (6) Matplotlib dan Seaborn untuk visualisasi histogram, kurva profil kontur tepi (edge profile), Bland-Altman plots, dan korelasi statistik Pearson."),
            ("e.  Arsitektur Antarmuka Klien (Hyu PACS Medical DICOM Workstation): ", "Antarmuka stasiun kerja medis web mandiri (Zero-Footprint Web-Based Medical DICOM Workstation) berkinerja tinggi tanpa dependensi plugin eksternal. Dibangun menggunakan HTML5, CSS3 Modern (Dark Mode bertema High-Contrast Medical Diagnostic UI dengan efek Glassmorphism), dan Vanilla JavaScript murni. Menggunakan arsitektur penampil khusus SVG Stage Layer (.vp-stage-layer) berbasis pemetaan matriks transformasi CTM terbalik (Current Transformation Matrix Inverse Affine Mapping) yang menjamin seluruh anotasi dan garis ukur biometrik melekat presisi (lockstep) pada koordinat anatomi citra saat operasi Zoom, Pan, Rotate (90°, 180°, 270°), dan Flip. Dilengkapi instrumen Multi-Tool Caliper Biometrik (Linear Caliper, True Circle, Ellipse, Box ROI, Polygon ROI dengan magnetic vertex snap, serta Danzer 1919 CTR Caliper terkalibrasi fisik 0,08 cm/piksel), modul interaktif seleksi klik kursor (click-to-select) dan hapus via tombol Delete, fitur visualisasi Split-Curtain Wipe Comparison Slider, Residual Noise Difference Map (|Inoisy - Iclean|), serta modul ekspor laporan diagnostik berstandar DICOM Secondary Capture."),
            ("f.  Lingkungan Pengembang Kode: ", "Visual Studio Code dan Antigravity IDE.")
        ]
        for prefix, desc in sw_items:
            p_new = insert_p_before(target_diagram_p, 'List Paragraph')
            format_p(p_new, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=2, line_spacing=1.5, left_indent_pt=40)
            r = p_new.add_run(prefix)
            set_run_font(r, bold=True)
            r = p_new.add_run(desc)
            set_run_font(r)

        # 4. Kategori: Bahan Data
        p_new = insert_p_before(target_diagram_p, 'List Paragraph')
        format_p(p_new, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=2, line_spacing=1.5, left_indent_pt=20)
        r = p_new.add_run("3.  Bahan Penelitian (")
        set_run_font(r, bold=True)
        r = p_new.add_run("Dataset")
        set_run_font(r, bold=True, italic=True)
        r = p_new.add_run("):")
        set_run_font(r, bold=True)

        # Data items
        data_items = [
            ("a.  Dataset Klinis Primer (Primary Clinical Dataset): ", "Minimal 100 berkas citra radiografi toraks proyeksi Posteroanterior (PA) berformat DICOM dari pasien dewasa pemeriksaan kesehatan kerja (Medical Check-Up / MCU) di PT Cahaya Medika Healthcare (Cahaya Diagnostic Centre / CDC Surabaya). Seluruh citra telah melalui proses de-identifikasi data pribadi (anonimasi nama dan nomor rekam medis) sesuai prinsip etika kerahasiaan data medis dan divalidasi langsung oleh Dokter Spesialis Radiologi sebagai nilai acuan baku (ground truth CTR)."),
            ("b.  Dataset Benchmark Terstandar: ", "Citra radiografi toraks terstandarisasi internasional dari Japanese Society of Radiological Technology (JSRT) dan National Institutes of Health (NIH ChestX-ray14) untuk validasi silang dan komparasi generalisasi model restorasi.")
        ]
        for prefix, desc in data_items:
            p_new = insert_p_before(target_diagram_p, 'List Paragraph')
            format_p(p_new, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=2, line_spacing=1.5, left_indent_pt=40)
            r = p_new.add_run(prefix)
            set_run_font(r, bold=True)
            r = p_new.add_run(desc)
            set_run_font(r)

        # Now remove old paragraphs
        for p_old in paragraphs_to_remove:
            remove_p(p_old)
        print("Updated Section 3.2 (Alat dan Bahan) successfully.")

    # ----------------------------------------------------
    # 3. UPDATE SECTION 3.3.6 (Tahap Perancangan Demonstrator)
    # ----------------------------------------------------
    h_336_idx = None
    p_gbr34_idx = None
    for i, p in enumerate(doc.paragraphs):
        if '3.3.6 Tahap Perancangan Demonstrator Web-Based DICOM Workstation' in p.text:
            h_336_idx = i
        if 'Gambar 3.4.' in p.text and 'Demonstrator Web-Based DICOM Workstation' in p.text:
            p_gbr34_idx = i
            break
            
    print(f"Heading 3.3.6 at [{h_336_idx}], Gambar 3.4 at [{p_gbr34_idx}]")
    
    if h_336_idx is not None and p_gbr34_idx is not None:
        target_gbr34_p = doc.paragraphs[p_gbr34_idx]
        
        # Collect paragraphs between h_336_idx+1 and p_gbr34_idx-1
        paragraphs_to_remove = [doc.paragraphs[k] for k in range(h_336_idx + 1, p_gbr34_idx)]
        
        # 1. Paragraf Pengantar
        p_new = insert_p_before(target_gbr34_p, 'Normal')
        format_p(p_new, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=4, line_spacing=1.5, first_line_indent_pt=20)
        r = p_new.add_run("Sebagai luaran implementasi praktis di fasilitas klinik radiologi, sistem perangkat lunak dirancang sebagai purwarupa ")
        set_run_font(r)
        r = p_new.add_run("Zero-Footprint Web-Based Medical DICOM Workstation")
        set_run_font(r, italic=True)
        r = p_new.add_run(" (Hyu PACS) yang mandiri, terintegrasi penuh, dan patuh terhadap standar protokol medis melalui 5 modul arsitektur utama:")
        set_run_font(r)

        # 5 Modul Arsitektur
        modules = [
            ("Layanan Penerima Transmisi DICOM (C-STORE SCP Daemon): ", "Dibangun menggunakan modul Python pynetdicom yang berjalan sebagai daemon service di latar belakang (Port jaringan 4242, Application Entity Title: HYU_PACS_SERVER) untuk menangkap aliran transmisi berkas DICOM rontgen toraks secara real-time langsung dari konsol modalitas rontgen digital klinik CDC Surabaya via protokol TCP/IP."),
            ("Manajemen Backend & Integrasi EMR (Laravel 11): ", "Mengelola basis data relasional pasien MCU (Nama Pasien, Usia, Gender, Accession Number, Study Instance UID, tanggal pemeriksaan), penyimpanan dan pengorganisasian berkas citra DICOM, otentikasi radiolog/radiografer, live insertion templat ekspertise radiologi ke formulir EMR, serta routing antarmuka pengguna."),
            ("Engine Pemrosesan Citra & Restorasi Multi-Algoritma (Python API & CLI): ", "Mengintegrasikan skrip inferensi terpadu untuk mengeksekusi Bilateral Filter, Non-Local Means (NLM), dan Denoising Convolutional Neural Network (DnCNN) 17-lapisan konvolusi, serta mengembalikan hasil citra terestorasi beserta telemetri kuantitatif real-time (PSNR, SSIM, SNR Gain, dan waktu latensi komputasi dalam milidetik)."),
            ("Antarmuka Penampil Klinis Berbasis SVG Stage Layer (Hyu PACS Viewer): ", "Menggantikan pustaka pihak ketiga dengan custom-engineered SVG Stage Layer (.vp-stage-layer) berbasis matriks transformasi CTM terbalik (Current Transformation Matrix Inverse Affine Mapping). Memastikan seluruh anotasi (garis ukur linier, lingkaran, elips, ROI poligon, dan garis ukur CTR Danzer A+B/C) tetap melekat secara lockstep pada koordinat anatomi citra saat pengguna melakukan zoom-in/zoom-out, pan/drag, rotasi (90°, 180°, 270°), dan flip (horisontal/vertikal). Mendukung interaksi seleksi klik kursor (click-to-select) dan penghapusan instan melalui tombol Delete keyboard maupun tombol hapus antarmuka. Menyediakan fitur visualisasi Split-Curtain Wipe Comparison Slider dan Residual Noise Difference Map (|Inoisy - Iclean|) untuk inspeksi klinis komparatif."),
            ("Modul Pelaporan Medis & Ekspor Secondary Capture: ", "Menerbitkan lembar ekspertise radiologi kardiomegali otomatis siap cetak, serta mengekspor citra radiografi beranotasi penuh dalam format citra beresolusi tinggi (Secondary Capture PNG / DICOM SC) dengan burn-in overlay data pasien, garis ukur biometrik, rasio CTR, dan interpretasi klinis berstandar NEMA PS 3.3.")
        ]
        
        for prefix, desc in modules:
            p_new = insert_p_before(target_gbr34_p, 'List Paragraph')
            format_p(p_new, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=3, line_spacing=1.5, left_indent_pt=20)
            r = p_new.add_run(prefix)
            set_run_font(r, bold=True)
            r = p_new.add_run(desc)
            set_run_font(r)

        # Remove old paragraphs
        for p_old in paragraphs_to_remove:
            remove_p(p_old)
        print("Updated Section 3.3.6 (Tahap Perancangan Demonstrator) successfully.")

    # Save to original document and updated file
    doc.save('d:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI.docx')
    doc.save('d:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI_UPDATED.docx')
    print("Document saved successfully to both paths!")

if __name__ == '__main__':
    run()

import docx
from docx.shared import Pt, Inches, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import parse_xml, OxmlElement
import lxml.etree as ET

# Load XSLT for MML to OMML
xslt_path = r'C:\Program Files\Microsoft Office\root\Office16\MML2OMML.XSL'
xslt = ET.parse(xslt_path)
transform = ET.XSLT(xslt)

def mml_to_omml_element(mml_str):
    mml_tree = ET.fromstring(mml_str)
    omml_tree = transform(mml_tree)
    root = omml_tree.getroot()
    if root.tag.endswith('oMath'):
        para = ET.Element('{http://schemas.openxmlformats.org/officeDocument/2006/math}oMathPara')
        para_pr = ET.SubElement(para, '{http://schemas.openxmlformats.org/officeDocument/2006/math}oMathParaPr')
        jc = ET.SubElement(para_pr, '{http://schemas.openxmlformats.org/officeDocument/2006/math}jc')
        jc.set('{http://schemas.openxmlformats.org/officeDocument/2006/math}val', 'center')
        para.append(root)
        root = para
    xml_bytes = ET.tostring(root, encoding='utf-8')
    return parse_xml(xml_bytes)

def set_run_font(r, font_name='Times New Roman', size_pt=12, bold=False, italic=False, subscript=False, superscript=False):
    r.font.name = font_name
    r.font.size = Pt(size_pt)
    r.bold = bold
    r.italic = italic
    if subscript:
        r.font.subscript = True
    if superscript:
        r.font.superscript = True

def format_p(p, align=WD_ALIGN_PARAGRAPH.JUSTIFY, space_after_pt=0, space_before_pt=0, line_spacing=1.5, first_line_indent_pt=28.35):
    p.alignment = align
    p.paragraph_format.line_spacing = line_spacing
    p.paragraph_format.space_after = Pt(space_after_pt)
    p.paragraph_format.space_before = Pt(space_before_pt)
    p.paragraph_format.first_line_indent = Pt(first_line_indent_pt)

def add_styled_run(p, text, bold=False, italic=False, subscript=False, superscript=False, size_pt=12):
    r = p.add_run(text)
    set_run_font(r, bold=bold, italic=italic, subscript=subscript, superscript=superscript, size_pt=size_pt)
    return r

def main():
    doc = docx.Document()

    # Set margins to 4 cm (top, left), 3 cm (bottom, right)
    for section in doc.sections:
        section.top_margin = Inches(1.18)    # 3 cm
        section.bottom_margin = Inches(1.18) # 3 cm
        section.left_margin = Inches(1.57)   # 4 cm
        section.right_margin = Inches(1.18)  # 3 cm

    def create_eq_p(mml_str):
        p = doc.add_paragraph()
        format_p(p, align=WD_ALIGN_PARAGRAPH.CENTER, space_after_pt=4, space_before_pt=4, line_spacing=1.0, first_line_indent_pt=0)
        omml_elm = mml_to_omml_element(mml_str)
        p._p.append(omml_elm)
        return p

    # --- MathML Definitions ---
    # 1. Pixel Spacing Calibration
    mml_spacing = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
      <mrow>
        <msub><mi>d</mi><mtext>mm</mtext></msub>
        <mo>=</mo>
        <msub><mi>d</mi><mtext>piksel</mtext></msub>
        <mo>&#x00B7;</mo>
        <mi>&#x0394;</mi><mi>p</mi>
      </mrow>
    </math>'''

    # 2. VOI LUT Windowing Range
    mml_windowing = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
      <mrow>
        <msub><mi>I</mi><mo>min</mo></msub>
        <mo>=</mo>
        <mi>C</mi><mo>-</mo><mfrac><mi>W</mi><mn>2</mn></mfrac>
        <mo>,</mo>
        <mspace width="2em"/>
        <msub><mi>I</mi><mo>max</mo></msub>
        <mo>=</mo>
        <mi>C</mi><mo>+</mo><mfrac><mi>W</mi><mn>2</mn></mfrac>
      </mrow>
    </math>'''

    # --- JUDUL SUBBAB 2.7 ---
    h2 = doc.add_paragraph()
    format_p(h2, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=6, space_before_pt=12, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h2, "2.7\tDICOM dan Web-Based Workstation", bold=True, size_pt=12)

    # Paragraf Pengantar 2.7
    p_intro = doc.add_paragraph()
    format_p(p_intro)
    add_styled_run(p_intro, "Penerapan algoritma restorasi citra dan otomatisasi pengukuran ")
    add_styled_run(p_intro, "Cardiothoracic Ratio", italic=True)
    add_styled_run(p_intro, " (CTR) memerlukan integrasi yang selaras dengan ekosistem teknologi informasi radiologi rumah sakit. Hal ini bertujuan agar luaran pemrosesan tidak hanya berhenti pada ranah komputasi teoretis, melainkan dapat diakses dan ditinjau secara langsung dalam alur kerja klinis. Standar ")
    add_styled_run(p_intro, "Digital Imaging and Communications in Medicine", italic=True)
    add_styled_run(p_intro, " (DICOM) bersama konsep stasiun kerja medis berbasis web (")
    add_styled_run(p_intro, "web-based medical workstation", italic=True)
    add_styled_run(p_intro, ") menjadi fondasi utama dalam menjamin interoperabilitas transmisi data, kalibrasi geometri organ, dan penyajian bukti visual diagnostik.")

    # --- SUBBAB 2.7.1 Standar DICOM dan Metadata Citra Radiografi ---
    h3_1 = doc.add_paragraph()
    format_p(h3_1, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=4, space_before_pt=8, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h3_1, "2.7.1\tStandar DICOM dan Metadata Citra Radiografi", bold=True)

    p_d1 = doc.add_paragraph()
    format_p(p_d1)
    add_styled_run(p_d1, "Standar DICOM yang dikembangkan bersama oleh ")
    add_styled_run(p_d1, "National Electrical Manufacturers Association", italic=True)
    add_styled_run(p_d1, " (NEMA) dan ")
    add_styled_run(p_d1, "American College of Radiology", italic=True)
    add_styled_run(p_d1, " (ACR) merupakan standar internasional universal untuk pertukaran, penyimpanan, dan komunikasi informasi pencitraan medis. Berbeda dengan format berkas grafis digital umum (seperti JPEG atau PNG) yang hanya menyimpan matriks intensitas piksel visual, format berkas DICOM membungkus data piksel mentah (")
    add_styled_run(p_d1, "pixel data", italic=True)
    add_styled_run(p_d1, ") bersama kumpulan informasi metadata komprehensif dalam struktur ")
    add_styled_run(p_d1, "Information Object Definition", italic=True)
    add_styled_run(p_d1, " (IOD). Metadata DICOM tersusun dalam penanda heksadesimal 4 bita (Grup, Elemen) yang mencakup identitas demografi pasien, parameter fisik penyinaran tabung rontgen (kVp, mA, dan waktu eksposisi), serta atribut teknis representasi citra.")

    p_d2 = doc.add_paragraph()
    format_p(p_d2)
    add_styled_run(p_d2, "Dalam pengolahan radiografi toraks digital, beberapa atribut metadata memegang peranan krusial terhadap akurasi interpretasi visual dan kalibrasi ukuran, antara lain:")

    # Poin a: Photometric Interpretation
    p_pa = doc.add_paragraph()
    format_p(p_pa, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_pa, "a.\tInterpretasi Fotometrik (", bold=True)
    add_styled_run(p_pa, "Photometric Interpretation", bold=True, italic=True)
    add_styled_run(p_pa, "): Atribut ini menentukan polaritas hubungan antara nilai intensitas piksel dan luminans tampilan visual. Pada radiografi toraks digital, nilai dapat berupa MONOCHROME1 (nilai piksel terendah merepresentasikan warna putih dan nilai tertinggi merepresentasikan warna hitam) atau MONOCHROME2 (nilai piksel terendah merepresentasikan warna hitam dan nilai tertinggi merepresentasikan warna putih). Penyeragaman polaritas ini wajib dilakukan sebelum citra diproses oleh algoritma restorasi atau model segmentasi guna mencegah pembalikan kontras visual organ anatomi.")

    # Poin b: Bit Depth & Windowing
    p_pb = doc.add_paragraph()
    format_p(p_pb, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_pb, "b.\tKedalaman Bita dan Pemetaan Kontras (", bold=True)
    add_styled_run(p_pb, "Bits Allocated, Window Center, Window Width", bold=True, italic=True)
    add_styled_run(p_pb, "): Citra radiografi digital umumnya disimpan dalam kedalaman rentang dinamis tinggi, yaitu 10 hingga 16 bit (")
    add_styled_run(p_pb, "Bits Stored", italic=True)
    add_styled_run(p_pb, "), yang menghasilkan variasi skala abu-abu jauh lebih kaya dibandingkan format tampilan standar 8 bit. Untuk menyajikan kontras jaringan lunak kardiak dan rongga toraks secara optimal pada monitor penampil, transformasi ")
    add_styled_run(p_pb, "Value of Interest Lookup Table", italic=True)
    add_styled_run(p_pb, " (VOI LUT) diterapkan melalui parameter ")
    add_styled_run(p_pb, "Window Center", italic=True)
    add_styled_run(p_pb, " (")
    add_styled_run(p_pb, "C", italic=True)
    add_styled_run(p_pb, ") dan ")
    add_styled_run(p_pb, "Window Width", italic=True)
    add_styled_run(p_pb, " (")
    add_styled_run(p_pb, "W", italic=True)
    add_styled_run(p_pb, ") yang membatasi rentang intensitas:")

    create_eq_p(mml_windowing)

    # Poin c: Pixel Spacing
    p_pc = doc.add_paragraph()
    format_p(p_pc, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_pc, "c.\tKalibrasi Spasial (", bold=True)
    add_styled_run(p_pc, "Pixel Spacing", bold=True, italic=True)
    add_styled_run(p_pc, "): Metadata tag ")
    add_styled_run(p_pc, "Pixel Spacing", italic=True)
    add_styled_run(p_pc, " (0028, 0030) atau ")
    add_styled_run(p_pc, "Imager Pixel Spacing", italic=True)
    add_styled_run(p_pc, " (0018, 1164) menyatakan jarak fisik antar-pusat piksel yang bertetangga pada detektor dalam satuan milimeter. Nilai ini menjadi faktor konversi jarak fisik:")

    create_eq_p(mml_spacing)

    p_pd = doc.add_paragraph()
    format_p(p_pd)
    add_styled_run(p_pd, "di mana ")
    add_styled_run(p_pd, "d", italic=True)
    add_styled_run(p_pd, "mm", italic=True, subscript=True)
    add_styled_run(p_pd, " adalah jarak metrik nyata dalam milimeter, ")
    add_styled_run(p_pd, "d", italic=True)
    add_styled_run(p_pd, "piksel", italic=True, subscript=True)
    add_styled_run(p_pd, " adalah jarak dalam satuan piksel pada matriks citra, dan ")
    add_styled_run(p_pd, "\u0394p", italic=True)
    add_styled_run(p_pd, " merepresentasikan resolusi spasial fisik detektor. Meskipun indeks CTR diformulasikan sebagai rasio perbandingan transversal relatif tanpa dimensi (D")
    add_styled_run(p_pd, "H", italic=True, subscript=True)
    add_styled_run(p_pd, " / D")
    add_styled_run(p_pd, "T", italic=True, subscript=True)
    add_styled_run(p_pd, "), keberadaan metadata kalibrasi spasial menjamin integritas aspek rasio piksel tetap isotropik sehingga morfologi kardiak tidak mengalami distorsi geometris.")

    # --- SUBBAB 2.7.2 Protokol Transmisi Jaringan Medis (C-STORE dan DICOMweb) ---
    h3_2 = doc.add_paragraph()
    format_p(h3_2, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=4, space_before_pt=8, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h3_2, "2.7.2\tProtokol Transmisi Jaringan Medis (C-STORE dan DICOMweb)", bold=True)

    p_t1 = doc.add_paragraph()
    format_p(p_t1)
    add_styled_run(p_t1, "Pertukaran dan transmisi citra medis antar-perangkat dalam jaringan rumah sakit, seperti antara modalitas pesawat rontgen digital, sistem pengarsipan ")
    add_styled_run(p_t1, "Picture Archiving and Communication System", italic=True)
    add_styled_run(p_t1, " (PACS), dan stasiun kerja diagnostik, diatur oleh bagian PS 3.4 dan PS 3.7 standar DICOM melalui protokol jaringan TCP/IP. Komunikasi jaringan ini beroperasi dengan model arsitektur klien dan peladen berbasis pesan perintah ")
    add_styled_run(p_t1, "DICOM Message Service Element", italic=True)
    add_styled_run(p_t1, " (DIMSE):")

    p_t2 = doc.add_paragraph()
    format_p(p_t2)
    add_styled_run(p_t2, "Layanan inti untuk pengiriman dan penyimpanan objek citra radiografi dijalankan melalui perintah DICOM C-STORE. Dalam transaksi ini, entitas pengirim bertindak sebagai ")
    add_styled_run(p_t2, "Service Class User", italic=True)
    add_styled_run(p_t2, " (SCU), sedangkan entitas penerima bertindak sebagai ")
    add_styled_run(p_t2, "Service Class Provider", italic=True)
    add_styled_run(p_t2, " (SCP) yang beroperasi sebagai layanan latar belakang (")
    add_styled_run(p_t2, "daemon", italic=True)
    add_styled_run(p_t2, ") pada nomor porta komunikasi medis (seperti porta standar 104, 4242, atau 11112). Sebelum pengiriman berkas citra berlangsung, kedua entitas melakukan tahap negosiasi asosiasi (")
    add_styled_run(p_t2, "association negotiation", italic=True)
    add_styled_run(p_t2, ") untuk menyepakati kelas konteks presentasi (")
    add_styled_run(p_t2, "presentation context", italic=True)
    add_styled_run(p_t2, ") dan sintaks transfer (")
    add_styled_run(p_t2, "transfer syntax", italic=True)
    add_styled_run(p_t2, ") yang didukung bersama, seperti pengodean ")
    add_styled_run(p_t2, "Explicit VR Little Endian", italic=True)
    add_styled_run(p_t2, " maupun kompresi nirrugi JPEG.")

    p_t3 = doc.add_paragraph()
    format_p(p_t3)
    add_styled_run(p_t3, "Seiring berkembangnya arsitektur komputasi awan dan aplikasi peramban web modern, standar DICOM PS 3.18 memperkenalkan protokol pertukaran data medis berbasis layanan web yang dikenal sebagai DICOMweb. Protokol ini mengadopsi prinsip arsitektur RESTful API melalui saluran HTTP/HTTPS standar, yang meliputi layanan WADO-RS (")
    add_styled_run(p_t3, "Web Access to DICOM Objects by RESTful Services", italic=True)
    add_styled_run(p_t3, ") untuk pengambilan objek citra maupun metadata terkompresi secara cepat, STOW-RS (")
    add_styled_run(p_t3, "Store Over the Web by RESTful Services", italic=True)
    add_styled_run(p_t3, ") untuk pengunggahan citra langsung melalui peramban, dan QIDO-RS (")
    add_styled_run(p_t3, "Query based on ID for DICOM Objects", italic=True)
    add_styled_run(p_t3, ") untuk pencarian daftar riwayat pemeriksaan pasien. Pemanfaatan DICOMweb memungkinkan sistem penampil citra medis berbasis peramban beroperasi secara ringan dan aman tanpa memerlukan ketergantungan pada pustaka soket jaringan biner tingkat rendah di sisi klien.")

    # --- SUBBAB 2.7.3 Representasi Luaran dan Laporan Medis ---
    h3_3 = doc.add_paragraph()
    format_p(h3_3, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=4, space_before_pt=8, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h3_3, "2.7.3\tRepresentasi Luaran dan Laporan Medis", bold=True)

    p_r1 = doc.add_paragraph()
    format_p(p_r1)
    add_styled_run(p_r1, "Setelah citra radiografi selesai direstorasi dan dianalisis untuk menghasilkan batas kontur organ serta nilai kuantitatif CTR, luaran diagnostik tersebut perlu direpresentasikan kembali ke dalam format standar agar dapat diarsipkan di dalam sistem PACS maupun diakses oleh dokter pengirim. Terdapat tiga format representasi yang didefinisikan dalam standar DICOM:")

    # 1. DICOM Secondary Capture
    p_r_sc = doc.add_paragraph()
    format_p(p_r_sc, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_r_sc, "1.\tDICOM Secondary Capture (SC): ", bold=True)
    add_styled_run(p_r_sc, "DICOM Secondary Capture merupakan kelas objek citra turunan (")
    add_styled_run(p_r_sc, "derived image SOP Class", italic=True)
    add_styled_run(p_r_sc, ") yang merekam tangkapan visual dua dimensi dari hasil olahan citra medis primer. Dalam penelitian ini, format DICOM Secondary Capture difungsikan untuk mengekspor citra radiografi hasil pengukuran beranotasi visual (garis diameter transversal jantung, diameter rongga dada, dan anotasi angka CTR) untuk keperluan demonstrasi visual. Ditinjau dari kaidah radiologi digital, objek Secondary Capture berperan sebagai representasi grafis sekunder dan bukan merupakan format laporan diagnostik terstruktur (")
    add_styled_run(p_r_sc, "structured report", italic=True)
    add_styled_run(p_r_sc, ") resmi secara otomatis, karena informasi numerik telah tertanam secara langsung ke dalam bidang matriks piksel citra.")

    # 2. DICOM Structured Reporting
    p_r_sr = doc.add_paragraph()
    format_p(p_r_sr, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_r_sr, "2.\tDICOM Structured Reporting (DICOM SR): ", bold=True)
    add_styled_run(p_r_sr, "DICOM SR dirancang secara khusus untuk menyimpan data temuan klinis, hasil pengukuran biometrik, dan kesimpulan interpretasi radiologi dalam struktur pohon data hierarkis terstandarisasi. Setiap simpul pada dokumen SR dikaitkan dengan kode terminologi medis baku (seperti RadLex atau SNOMED-CT), sehingga data nilai CTR dan koordinat garis pembatas dapat dipertukarkan dan dianalisis secara otomatis oleh sistem rekam medis elektronik tanpa ambiguitas semantik. Kendati memiliki keunggulan interoperabilitas data murni yang tinggi, DICOM SR membutuhkan perangkat lunak penampil radiologi khusus yang mampu menguraikan sintaks pohon dokumen tersebut.")

    # 3. DICOM Encapsulated PDF
    p_r_pdf = doc.add_paragraph()
    format_p(p_r_pdf, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_r_pdf, "3.\tDICOM Encapsulated PDF: ", bold=True)
    add_styled_run(p_r_pdf, "Sebagai alternatif format laporan resmi yang praktis, standar DICOM menyediakan fasilitas ")
    add_styled_run(p_r_pdf, "Encapsulated PDF", italic=True)
    add_styled_run(p_r_pdf, " yang membungkus dokumen laporan ekspertise berformat PDF ke dalam amplop objek DICOM. Format ini memungkinkan lembar laporan diagnostik lengkap (yang memuat logo fasilitas medis, tabel ringkasan metrik kualitas citra dan CTR, deskripsi ekspertise klinis, serta tanda tangan dokter radiolog) disimpan, diindeks berdasarkan nomor rekam medis pasien, dan ditransmisikan di dalam ekosistem PACS berdampingan dengan berkas citra asli tanpa mengubah tata letak dokumen resminya.")

    # --- SUBBAB 2.7.4 Konsep Stasiun Kerja Medis Berbasis Web ---
    h3_4 = doc.add_paragraph()
    format_p(h3_4, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=4, space_before_pt=8, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h3_4, "2.7.4\tKonsep Stasiun Kerja Medis Berbasis Web", bold=True)

    p_w1 = doc.add_paragraph()
    format_p(p_w1)
    add_styled_run(p_w1, "Secara konvensional, stasiun kerja medis (")
    add_styled_run(p_w1, "medical workstation", italic=True)
    add_styled_run(p_w1, ") dioperasikan melalui aplikasi berbasis desktop (")
    add_styled_run(p_w1, "thick-client", italic=True)
    add_styled_run(p_w1, ") yang memerlukan instalasi perangkat lunak lokal serta konfigurasi perangkat keras khusus pada setiap terminal pembacaan di instalasi radiologi. Ketergantungan ini menimbulkan kendala fleksibilitas akses, pemeliharaan sistem yang rumit, dan biaya implementasi yang tinggi.")

    p_w2 = doc.add_paragraph()
    format_p(p_w2)
    add_styled_run(p_w2, "Paradigma stasiun kerja medis modern bertransformasi menuju konsep penampil berbasis web tanpa instalasi (")
    add_styled_run(p_w2, "zero-footprint web-based workstation", italic=True)
    add_styled_run(p_w2, "). Pendekatan ini memungkinkan dokter spesialis radiologi maupun klinisi lain mengakses dan mengevaluasi citra radiografi resolusi tinggi secara langsung melalui peramban web standar tanpa memerlukan pemasangan perangkat lunak tambahan (")
    add_styled_run(p_w2, "plug-in", italic=True)
    add_styled_run(p_w2, ") pada sisi klien. Dalam penelitian ini, purwarupa stasiun kerja medis berbasis web dirancang sebagai demonstrator pembuktian konsep (")
    add_styled_run(p_w2, "proof-of-concept", italic=True)
    add_styled_run(p_w2, ") guna menguji kelayakan integrasi metode restorasi dan modul pengukuran CTR pada alur kerja klinis nyata. Rincian implementasi perangkat lunak, arsitektur basis data, layanan penerima transmisi jaringan, serta antarmuka penampil interaktif diuraikan secara mendalam pada Bab III.")

    out_path = r'd:\laragon\www\belajar-laravel\SUBBAB_2_7_DICOM.docx'
    doc.save(out_path)
    print("Successfully generated:", out_path)

if __name__ == '__main__':
    main()

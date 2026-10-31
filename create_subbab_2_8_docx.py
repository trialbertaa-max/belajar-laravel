import docx
from docx.shared import Pt, Inches, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
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

def set_cell_background(cell, hex_color="F2F2F2"):
    shading_elm = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{hex_color}"/>')
    cell._tc.get_or_add_tcPr().append(shading_elm)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('w:top', top), ('w:bottom', bottom), ('w:left', left), ('w:right', right)]:
        node = OxmlElement(m)
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def main():
    doc = docx.Document()

    # Set page layout to Landscape for wide 6-column table, or Portrait with adjusted margins
    # Standard skripsi proposal usually Portrait with table auto-fit or Landscape section.
    # Let's create standard Portrait with 1.57" (4cm) left, 1.18" (3cm) right/top/bottom
    # But for Table 2.1, let's ensure cell text is compact and readable (10 pt font).
    for section in doc.sections:
        section.top_margin = Inches(1.18)
        section.bottom_margin = Inches(1.18)
        section.left_margin = Inches(1.57)
        section.right_margin = Inches(1.18)

    # --- JUDUL SUBBAB 2.8 ---
    h2 = doc.add_paragraph()
    format_p(h2, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=6, space_before_pt=12, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h2, "2.8\tPenelitian Terdahulu", bold=True, size_pt=12)

    # Paragraf Pengantar
    p1 = doc.add_paragraph()
    format_p(p1)
    add_styled_run(p1, "Kajian terhadap penelitian terdahulu dilakukan guna memetakan perkembangan metodologi dan mengidentifikasi kesenjangan ilmiah (")
    add_styled_run(p1, "research gap", italic=True)
    add_styled_run(p1, ") dalam bidang penapisan derau citra radiografi toraks, otomatisasi segmentasi organ kardioperikardial, serta dampaknya terhadap presisi klinis pengukuran ")
    add_styled_run(p1, "Cardiothoracic Ratio", italic=True)
    add_styled_run(p1, " (CTR). Berbagai penelitian telah mengembangkan pendekatan restorasi citra mulai dari penapis spasial non-linear deterministik hingga arsitektur jaringan saraf tiruan mendalam (")
    add_styled_run(p1, "deep learning", italic=True)
    add_styled_run(p1, "), serta otomatisasi estimasi CTR menggunakan model deteksi objek dan segmentasi semantik. Ringkasan komparatif penelitian terdahulu yang relevan disajikan pada Tabel 2.1.")

    # Judul Tabel
    p_tlabel = doc.add_paragraph()
    format_p(p_tlabel, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=4, space_before_pt=8, line_spacing=1.0, first_line_indent_pt=0)
    add_styled_run(p_tlabel, "Tabel 2.1  Ringkasan Penelitian Terdahulu", bold=True, size_pt=11)

    # Table Definition: 6 Kolom
    # Studi | Data | Metode | Hasil | Limitasi | Gap untuk penelitian ini
    headers = ["Studi", "Data", "Metode", "Hasil", "Limitasi", "Gap untuk penelitian ini"]
    
    table_data = [
        # 1. Seminal Bilateral Filter
        (
            "Tomasi & Manduchi (1998)\nProc. IEEE ICCV",
            "Citra digital umum berderau sintetis",
            "Bilateral Filtering (kombinasi filter domain spasial geometris dan filter rentang radiometrik)",
            "Efektif mereduksi derau Gaussian sembari mempertahankan ketajaman tepi garis kontur objek tanpa efek pengaburan linier.",
            "Evaluasi murni didasarkan pada persepsi visual citra fotografi umum, belum menguji karakteristik derau campuran radiografi medis serta pengaruhnya terhadap akurasi biometrik klinis.",
            "Penelitian ini menerapkan penapisan bilateral pada domain citra rontgen toraks PA dan menguji pengaruh pelestarian batas organ secara kuantitatif terhadap presisi perhitungan CTR."
        ),
        # 2. Seminal DnCNN
        (
            "Zhang et al. (2017)\nIEEE Trans. Image Process.",
            "Dataset citra natural berukuran kecil (BSD400 dan Set12) dengan derau Gaussian",
            "Denoising Convolutional Neural Network (DnCNN) 17 lapis dengan residual learning dan batch normalization",
            "Mencapai nilai PSNR dan SSIM yang melampaui metode BM3D serta stabil memisahkan residu derau dari struktur citra bersih.",
            "Model dilatih menggunakan citra natural fotografi 8-bit dan derau Gaussian murni, belum disesuaikan dengan rentang dinamis tinggi citra medis DICOM dan derau Poisson-Gaussian.",
            "Penelitian ini menguji dan menyesuaikan arsitektur DnCNN untuk domain radiografi toraks beresolusi tinggi serta mengkaji dampak rekonstruksi residunya terhadap integritas kontur batas kardiak."
        ),
        # 3. Saiviroonporn et al. (2021)
        (
            "Saiviroonporn et al. (2021)\nBMC Med. Imaging",
            "500 radiografi toraks PA klinis dewasa",
            "Sistem pembacaan CTR berbantuan AI berbasis deep learning konvensional dibandingkan dengan pembacaan manual radiolog",
            "Mempercepat waktu pembacaan CTR dari 2 menit menjadi beberapa detik dengan koefisien korelasi intraclass (ICC) 0,89 terhadap radiolog.",
            "Penelitian hanya menguji citra dengan kualitas standar rumah sakit, tanpa mengevaluasi performa model saat citra terdegradasi derau dosis rendah (low-dose CXR).",
            "Penelitian ini melengkapi evaluasi klinis dengan menganalisis ketahanan pengukuran CTR pada berbagai variasi intensitas derau dan membandingkan pemulihan kualitasnya pasca-restorasi."
        ),
        # 4. Jafar et al. (2022)
        (
            "Jafar et al. (2022)\nJ. Pers. Med.",
            "Dataset publik JSRT (247 citra) dan Montgomery County (138 citra)",
            "CardioNet: arsitektur segmentasi semantik end-to-end berbasis encoder-decoder untuk kontur jantung dan paru",
            "Mencapai skor Dice 0,95 untuk segmentasi kardiak dan akurasi klasifikasi kardiomegali sebesar 92% dengan korelasi CTR yang kuat.",
            "Fokus penelitian murni pada arsitektur segmentasi tanpa menganalisis dampak pra-pemrosesan filter restorasi citra terhadap konsistensi garis batas tepi organ.",
            "Penelitian ini secara mendalam meneliti efek penghalusan filter spasial dan restorasi CNN terhadap deviasi garis batas anatomi (DSC, Hausdorff Distance, dan MSD)."
        ),
        # 5. Yoshida et al. (2023) - 2023 Paper
        (
            "Yoshida et al. (2023)\nQuant. Imaging Med. Surg.",
            "1.116 radiografi toraks frontal dari berbagai institusi rumah sakit",
            "Deep learning iterative framework untuk pengukuran CTR otomatis tanpa anotasi mandiri berulang",
            "Menghasilkan korelasi Pearson r = 0,93 terhadap radiolog senior dan menurunkan galat pengukuran CTR hingga MAE 1,6%.",
            "Model mengasumsikan kontur anatomi bebas dari derau transmisi atau artefak kuantisasi, serta tidak dilengkapi analisis batas kesepakatan Bland-Altman yang komprehensif.",
            "Penelitian ini menerapkan uji kesepakatan klinis Bland-Altman (bias dan LoA) serta menguji ketahanan segmentasi kontur saat diberikan injeksi derau dosis rendah."
        ),
        # 6. Kufel et al. (2024) - 2024 Paper
        (
            "Kufel et al. (2024)\nJ. Clin. Med.",
            "Dataset radiografi toraks klinis multi-patologi (850 citra PA)",
            "Kombinasi deteksi batas kardiak berbasis ResNet dan regresi otomatis diameter rongga toraks",
            "Mencapai sensitivitas 91,4% dan spesifisitas 89,2% dalam mendeteksi kardiomegali dengan nilai galat pengukuran CTR yang minimal.",
            "Tidak mengevaluasi fenomena pergeseran tepi batas (boundary shift) akibat proses pemfilteran derau dan tidak menyediakan purwarupa penampil terstandarisasi DICOM.",
            "Penelitian ini mengisi celah tersebut dengan menguji metrik pelestarian batas (HD95 dan MSD) serta mengintegrasikan modul pengukuran ke dalam demonstrator Web DICOM Workstation."
        ),
        # 7. Song et al. (2024) - 2024 Review Paper
        (
            "Song et al. (2024)\nRadiat. Med. Prot.",
            "Kajian literatur komprehensif algoritma peningkatan citra CXR (2018-2023)",
            "Studi komparasi model AI untuk supresi derau, peningkatan kontras, dan super-resolusi pada rontgen toraks",
            "Menunjukkan bahwa model restorasi deep learning secara konsisten memberikan nilai PSNR dan SSIM lebih unggul dibanding penapis spasial konvensional.",
            "Mengidentifikasi limitasi utama literatur saat ini, yaitu dominasi metrik fidelitas piksel global tanpa menguji apakah peningkatan PSNR berbanding lurus dengan ketepatan tugas klinis hilir.",
            "Penelitian ini secara langsung menjawab tantangan tersebut dengan menerapkan paradigma task-based evaluation yang menghubungkan metrik kualitas citra dengan akurasi CTR."
        ),
        # 8. Fan et al. (2024) - 2024 High-Impact Paper
        (
            "Fan et al. (2024)\nNature Commun.",
            "Dataset multi-negara skala besar (>100.000 radiografi toraks)",
            "Model deep learning multi-modal berbasis CNN dan Transformer untuk skrining abnormalitas kardiopulmonal",
            "Mencapai tingkat akurasi diagnosis setara spesialis radiologi dengan nilai AUC > 0,95 pada deteksi kardiomegali.",
            "Arsitektur komputasi sangat berat (memerlukan klaster GPU besar), bersifat black-box, dan sulit diimplementasikan secara langsung pada stasiun kerja klinik mandiri.",
            "Penelitian ini mengkaji metode restorasi berbobot komputasi terjangkau (Bilateral Filter dan DnCNN) yang efisien dioperasikan pada stasiun kerja medis web mandiri."
        )
    ]

    table = doc.add_table(rows=len(table_data) + 1, cols=6)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False

    # Column widths (in inches): Total ~6.5 inches (fits Portrait page with 1" margins)
    # Studi: 1.1", Data: 0.9", Metode: 1.1", Hasil: 1.1", Limitasi: 1.1", Gap: 1.2"
    col_widths = [Inches(1.1), Inches(0.9), Inches(1.1), Inches(1.1), Inches(1.1), Inches(1.2)]

    # Format Header Row
    hdr_cells = table.rows[0].cells
    for i, title in enumerate(headers):
        hdr_cells[i].width = col_widths[i]
        set_cell_background(hdr_cells[i], "D9D9D9")
        set_cell_margins(hdr_cells[i], top=120, bottom=120, left=100, right=100)
        p = hdr_cells[i].paragraphs[0]
        format_p(p, align=WD_ALIGN_PARAGRAPH.CENTER, space_after_pt=2, space_before_pt=2, line_spacing=1.0, first_line_indent_pt=0)
        add_styled_run(p, title, bold=True, size_pt=10)

    # Populate Data Rows
    for row_idx, data_tuple in enumerate(table_data):
        row_cells = table.rows[row_idx + 1].cells
        # Alternate subtle shading for readability
        bg_col = "F9F9F9" if row_idx % 2 == 1 else "FFFFFF"
        for col_idx in range(6):
            cell = row_cells[col_idx]
            cell.width = col_widths[col_idx]
            set_cell_background(cell, bg_col)
            set_cell_margins(cell, top=100, bottom=100, left=100, right=100)
            p = cell.paragraphs[0]
            format_p(p, align=WD_ALIGN_PARAGRAPH.JUSTIFY if col_idx > 1 else WD_ALIGN_PARAGRAPH.LEFT, 
                     space_after_pt=2, space_before_pt=2, line_spacing=1.15, first_line_indent_pt=0)
            text_content = data_tuple[col_idx]
            
            # Format text in cell, handling line breaks and italics if needed
            lines = text_content.split('\n')
            for l_idx, line in enumerate(lines):
                if l_idx > 0:
                    p = cell.add_paragraph()
                    format_p(p, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=2, space_before_pt=0, line_spacing=1.0, first_line_indent_pt=0)
                # Check for italicized words
                words = line.split(' ')
                for w_idx, word in enumerate(words):
                    space = " " if w_idx < len(words) - 1 else ""
                    is_italic = word.strip('(),.') in ['et', 'al.', 'Proc.', 'IEEE', 'ICCV', 'Trans.', 'Image', 'Process.', 
                                                      'BMC', 'Med.', 'Imaging', 'J.', 'Pers.', 'Quant.', 'Surg.', 'Clin.', 
                                                      'Radiat.', 'Prot.', 'Nature', 'Commun.', 'deep', 'learning', 'end-to-end',
                                                      'residual', 'learning', 'batch', 'normalization', 'low-dose', 'CardioNet',
                                                      'black-box', 'task-based', 'evaluation', 'boundary', 'preservation',
                                                      'Cardiothoracic', 'Ratio', 'Bilateral', 'Filtering', 'Denoising']
                    add_styled_run(p, word + space, italic=is_italic, size_pt=9.5)

    # Apply standard table borders (XML manipulation)
    tblPr = table._tbl.tblPr
    borders = parse_xml(
        f'<w:tblBorders {nsdecls("w")}>\n'
        f'  <w:top w:val="single" w:sz="8" w:space="0" w:color="000000"/>\n'
        f'  <w:bottom w:val="single" w:sz="8" w:space="0" w:color="000000"/>\n'
        f'  <w:insideH w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>\n'
        f'  <w:insideV w:val="none"/>\n'
        f'  <w:left w:val="none"/>\n'
        f'  <w:right w:val="none"/>\n'
        f'</w:tblBorders>'
    )
    tblPr.append(borders)

    # Paragraf Sintesis Penelitian Terdahulu
    p_syn1 = doc.add_paragraph()
    format_p(p_syn1, space_before_pt=8)
    add_styled_run(p_syn1, "Berdasarkan telaah komparatif pada Tabel 2.1, tampak jelas bahwa perkembangan penelitian terdahulu terbagi ke dalam dua kelompok utama yang berjalan secara terpisah. Kelompok pertama berfokus mendalam pada domain penapisan derau dan restorasi citra, namun evaluasinya sangat terbatas pada metrik fidelitas piksel global matematis (PSNR dan SSIM) tanpa menguji integritas morfologi kontur anatomis organ. Kelompok kedua berfokus pada otomasi segmentasi kardiak dan perhitungan CTR, namun cenderung mengasumsikan citra masukan selalu dalam kondisi ideal dan bersih, sehingga mengabaikan risiko pergeseran garis batas (")
    add_styled_run(p_syn1, "boundary shift", italic=True)
    add_styled_run(p_syn1, ") akibat efek penghalusan berlebih (")
    add_styled_run(p_syn1, "over-smoothing", italic=True)
    add_styled_run(p_syn1, ") maupun artefak rekonstruksi jaringan saraf tiruan.")

    p_syn2 = doc.add_paragraph()
    format_p(p_syn2)
    add_styled_run(p_syn2, "Penelitian ini hadir menjembatani kesenjangan tersebut melalui pendekatan komprehensif yang mengintegrasikan penapisan derau (Bilateral Filter dan DnCNN) dengan evaluasi berbasis tugas klinis (")
    add_styled_run(p_syn2, "task-based evaluation", italic=True)
    add_styled_run(p_syn2, "). Kebaruan penelitian ini terletak pada pengujian simultan antara pelestarian batas anatomi kardioperikardial (DSC, Hausdorff Distance, dan MSD) dan presisi diagnostik CTR (MAE, Bias, korelasi Pearson, dan batas kesepakatan Bland-Altman) pada citra rontgen toraks PA berformat DICOM, yang diwujudkan ke dalam purwarupa demonstrator ")
    add_styled_run(p_syn2, "Web-Based DICOM Workstation", italic=True)
    add_styled_run(p_syn2, " untuk membuktikan kelayakan penerapannya pada alur kerja radiologi klinis.")

    out_path = r'd:\laragon\www\belajar-laravel\TABEL_2_8_PENELITIAN_TERDAHULU.docx'
    doc.save(out_path)
    print("Successfully generated:", out_path)

if __name__ == '__main__':
    main()

import docx
from docx.shared import Pt, Inches
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import parse_xml, OxmlElement
from docx.text.paragraph import Paragraph
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

    for section in doc.sections:
        section.top_margin = Inches(1.18) # 3 cm
        section.bottom_margin = Inches(1.18)
        section.left_margin = Inches(1.57) # 4 cm
        section.right_margin = Inches(1.18)

    def create_eq_p(mml_str):
        p = doc.add_paragraph()
        format_p(p, align=WD_ALIGN_PARAGRAPH.CENTER, space_after_pt=4, space_before_pt=4, line_spacing=1.0, first_line_indent_pt=0)
        omml_elm = mml_to_omml_element(mml_str)
        p._p.append(omml_elm)
        return p

    # --- MathML Definitions ---
    mml_mse = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
      <mrow>
        <mtext>MSE</mtext>
        <mo>=</mo>
        <mfrac>
          <mn>1</mn>
          <mrow><mi>M</mi><mo>·</mo><mi>N</mi></mrow>
        </mfrac>
        <munderover>
          <mo>∑</mo>
          <mrow><mi>i</mi><mo>=</mo><mn>0</mn></mrow>
          <mrow><mi>M</mi><mo>-</mo><mn>1</mn></mrow>
        </munderover>
        <munderover>
          <mo>∑</mo>
          <mrow><mi>j</mi><mo>=</mo><mn>0</mn></mrow>
          <mrow><mi>N</mi><mo>-</mo><mn>1</mn></mrow>
        </munderover>
        <msup>
          <mrow>
            <mo>[</mo>
            <msub><mi>I</mi><mtext>clean</mtext></msub>
            <mo>(</mo><mi>i</mi><mo>,</mo><mi>j</mi><mo>)</mo>
            <mo>-</mo>
            <msub><mi>I</mi><mtext>restored</mtext></msub>
            <mo>(</mo><mi>i</mi><mo>,</mo><mi>j</mi><mo>)</mo>
            <mo>]</mo>
          </mrow>
          <mn>2</mn>
        </msup>
      </mrow>
    </math>'''

    mml_psnr = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
      <mrow>
        <mtext>PSNR</mtext>
        <mo>=</mo>
        <mn>10</mn>
        <mo>·</mo>
        <msub><mo>log</mo><mn>10</mn></msub>
        <mo>(</mo>
        <mfrac>
          <mrow><msubsup><mtext>MAX</mtext><mi>I</mi><mn>2</mn></msubsup></mrow>
          <mtext>MSE</mtext>
        </mfrac>
        <mo>)</mo>
      </mrow>
    </math>'''

    mml_ssim = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
      <mrow>
        <mtext>SSIM</mtext>
        <mo>(</mo><mi>x</mi><mo>,</mo><mi>y</mi><mo>)</mo>
        <mo>=</mo>
        <mfrac>
          <mrow>
            <mo>(</mo><mn>2</mn><msub><mi>μ</mi><mi>x</mi></msub><msub><mi>μ</mi><mi>y</mi></msub><mo>+</mo><msub><mi>C</mi><mn>1</mn></msub><mo>)</mo>
            <mo>(</mo><mn>2</mn><msub><mi>σ</mi><mrow><mi>x</mi><mi>y</mi></mrow></msub><mo>+</mo><msub><mi>C</mi><mn>2</mn></msub><mo>)</mo>
          </mrow>
          <mrow>
            <mo>(</mo><msubsup><mi>μ</mi><mi>x</mi><mn>2</mn></msubsup><mo>+</mo><msubsup><mi>μ</mi><mi>y</mi><mn>2</mn></msubsup><mo>+</mo><msub><mi>C</mi><mn>1</mn></msub><mo>)</mo>
            <mo>(</mo><msubsup><mi>σ</mi><mi>x</mi><mn>2</mn></msubsup><mo>+</mo><msubsup><mi>σ</mi><mi>y</mi><mn>2</mn></msubsup><mo>+</mo><msub><mi>C</mi><mn>2</mn></msub><mo>)</mo>
          </mrow>
        </mfrac>
      </mrow>
    </math>'''

    mml_dsc = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
      <mrow>
        <mtext>DSC</mtext>
        <mo>(</mo><mi>A</mi><mo>,</mo><mi>B</mi><mo>)</mo>
        <mo>=</mo>
        <mfrac>
          <mrow>
            <mn>2</mn>
            <mo>|</mo><mi>A</mi><mo>∩</mo><mi>B</mi><mo>|</mo>
          </mrow>
          <mrow>
            <mo>|</mo><mi>A</mi><mo>|</mo>
            <mo>+</mo>
            <mo>|</mo><mi>B</mi><mo>|</mo>
          </mrow>
        </mfrac>
      </mrow>
    </math>'''

    mml_hd = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
      <mrow>
        <msub><mi>d</mi><mi>H</mi></msub>
        <mo>(</mo><mi>A</mi><mo>,</mo><mi>B</mi><mo>)</mo>
        <mo>=</mo>
        <mo>max</mo>
        <mo>(</mo>
        <munder>
          <mo>sup</mo>
          <mrow><mi>a</mi><mo>∈</mo><mi>A</mi></mrow>
        </munder>
        <munder>
          <mo>inf</mo>
          <mrow><mi>b</mi><mo>∈</mo><mi>B</mi></mrow>
        </munder>
        <mo>‖</mo><mi>a</mi><mo>-</mo><mi>b</mi><mo>‖</mo>
        <mo>,</mo>
        <munder>
          <mo>sup</mo>
          <mrow><mi>b</mi><mo>∈</mo><mi>B</mi></mrow>
        </munder>
        <munder>
          <mo>inf</mo>
          <mrow><mi>a</mi><mo>∈</mo><mi>A</mi></mrow>
        </munder>
        <mo>‖</mo><mi>b</mi><mo>-</mo><mi>a</mi><mo>‖</mo>
        <mo>)</mo>
      </mrow>
    </math>'''

    mml_msd = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
      <mrow>
        <mtext>MSD</mtext>
        <mo>(</mo><mi>A</mi><mo>,</mo><mi>B</mi><mo>)</mo>
        <mo>=</mo>
        <mfrac>
          <mn>1</mn>
          <mrow><mo>|</mo><mi>A</mi><mo>|</mo><mo>+</mo><mo>|</mo><mi>B</mi><mo>|</mo></mrow>
        </mfrac>
        <mo>(</mo>
        <munder>
          <mo>∑</mo>
          <mrow><mi>a</mi><mo>∈</mo><mi>A</mi></mrow>
        </munder>
        <munder>
          <mo>min</mo>
          <mrow><mi>b</mi><mo>∈</mo><mi>B</mi></mrow>
        </munder>
        <mo>‖</mo><mi>a</mi><mo>-</mo><mi>b</mi><mo>‖</mo>
        <mo>+</mo>
        <munder>
          <mo>∑</mo>
          <mrow><mi>b</mi><mo>∈</mo><mi>B</mi></mrow>
        </munder>
        <munder>
          <mo>min</mo>
          <mrow><mi>a</mi><mo>∈</mo><mi>A</mi></mrow>
        </munder>
        <mo>‖</mo><mi>b</mi><mo>-</mo><mi>a</mi><mo>‖</mo>
        <mo>)</mo>
      </mrow>
    </math>'''

    mml_bias = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
      <mrow>
        <mtext>Bias</mtext>
        <mo>=</mo>
        <mfrac>
          <mn>1</mn>
          <mi>N</mi>
        </mfrac>
        <munderover>
          <mo>∑</mo>
          <mrow><mi>i</mi><mo>=</mo><mn>1</mn></mrow>
          <mi>N</mi>
        </munderover>
        <mo>(</mo>
        <msub><mtext>CTR</mtext><mrow><mtext>restored</mtext><mo>,</mo><mi>i</mi></mrow></msub>
        <mo>-</mo>
        <msub><mtext>CTR</mtext><mrow><mtext>ref</mtext><mo>,</mo><mi>i</mi></mrow></msub>
        <mo>)</mo>
      </mrow>
    </math>'''

    mml_mae = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
      <mrow>
        <mtext>MAE</mtext>
        <mo>=</mo>
        <mfrac>
          <mn>1</mn>
          <mi>N</mi>
        </mfrac>
        <munderover>
          <mo>∑</mo>
          <mrow><mi>i</mi><mo>=</mo><mn>1</mn></mrow>
          <mi>N</mi>
        </munderover>
        <mo>|</mo>
        <msub><mtext>CTR</mtext><mrow><mtext>restored</mtext><mo>,</mo><mi>i</mi></mrow></msub>
        <mo>-</mo>
        <msub><mtext>CTR</mtext><mrow><mtext>ref</mtext><mo>,</mo><mi>i</mi></mrow></msub>
        <mo>|</mo>
      </mrow>
    </math>'''

    mml_pearson = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
      <mrow>
        <mi>r</mi>
        <mo>=</mo>
        <mfrac>
          <mrow>
            <munderover>
              <mo>∑</mo>
              <mrow><mi>i</mi><mo>=</mo><mn>1</mn></mrow>
              <mi>N</mi>
            </munderover>
            <mo>(</mo>
            <msub><mtext>CTR</mtext><mrow><mtext>restored</mtext><mo>,</mo><mi>i</mi></mrow></msub>
            <mo>-</mo>
            <msub><mover><mtext>CTR</mtext><mo>¯</mo></mover><mtext>restored</mtext></msub>
            <mo>)</mo>
            <mo>(</mo>
            <msub><mtext>CTR</mtext><mrow><mtext>ref</mtext><mo>,</mo><mi>i</mi></mrow></msub>
            <mo>-</mo>
            <msub><mover><mtext>CTR</mtext><mo>¯</mo></mover><mtext>ref</mtext></msub>
            <mo>)</mo>
          </mrow>
          <msqrt>
            <munderover>
              <mo>∑</mo>
              <mrow><mi>i</mi><mo>=</mo><mn>1</mn></mrow>
              <mi>N</mi>
            </munderover>
            <msup>
              <mrow>
                <mo>(</mo>
                <msub><mtext>CTR</mtext><mrow><mtext>restored</mtext><mo>,</mo><mi>i</mi></mrow></msub>
                <mo>-</mo>
                <msub><mover><mtext>CTR</mtext><mo>¯</mo></mover><mtext>restored</mtext></msub>
                <mo>)</mo>
              </mrow>
              <mn>2</mn>
            </msup>
            <munderover>
              <mo>∑</mo>
              <mrow><mi>i</mi><mo>=</mo><mn>1</mn></mrow>
              <mi>N</mi>
            </munderover>
            <msup>
              <mrow>
                <mo>(</mo>
                <msub><mtext>CTR</mtext><mrow><mtext>ref</mtext><mo>,</mo><mi>i</mi></mrow></msub>
                <mo>-</mo>
                <msub><mover><mtext>CTR</mtext><mo>¯</mo></mover><mtext>ref</mtext></msub>
                <mo>)</mo>
              </mrow>
              <mn>2</mn>
            </msup>
          </msqrt>
        </mfrac>
      </mrow>
    </math>'''

    mml_loa = '''<math xmlns="http://www.w3.org/1998/Math/MathML">
      <mrow>
        <mtext>LoA</mtext>
        <mo>=</mo>
        <mover><mi>d</mi><mo>¯</mo></mover>
        <mo>±</mo>
        <mn>1,96</mn>
        <mo>·</mo>
        <mtext>SD</mtext>
      </mrow>
    </math>'''

    # =========================================================================
    # BAGIAN 2.5 EVALUASI KUALITAS CITRA
    # =========================================================================
    h2_5 = doc.add_paragraph()
    format_p(h2_5, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=6, space_before_pt=12, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h2_5, "2.5\tEvaluasi Kualitas Citra", bold=True, size_pt=12)

    p_intro5 = doc.add_paragraph()
    format_p(p_intro5)
    add_styled_run(p_intro5, "Evaluasi terhadap efektivitas metode restorasi citra radiografi toraks dilakukan untuk mengukur kemampuan algoritma dalam mereduksi derau sekaligus mempertahankan integritas informasi visual. Pada tingkat fidelitas fisik dan persepsi visual, pengukuran kualitas citra secara objektif umumnya menggunakan dua metrik komputasional standar, yaitu ")
    add_styled_run(p_intro5, "Peak Signal-to-Noise Ratio", italic=True)
    add_styled_run(p_intro5, " (PSNR) dan ")
    add_styled_run(p_intro5, "Structural Similarity Index Measure", italic=True)
    add_styled_run(p_intro5, " (SSIM).")

    # 2.5.1 PSNR
    h3_51 = doc.add_paragraph()
    format_p(h3_51, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=4, space_before_pt=8, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h3_51, "2.5.1\tPeak Signal-to-Noise Ratio (PSNR)", bold=True, size_pt=12)

    p_psnr = doc.add_paragraph()
    format_p(p_psnr)
    add_styled_run(p_psnr, "Peak Signal-to-Noise Ratio", italic=True)
    add_styled_run(p_psnr, " (PSNR) merupakan metrik objektif logaritmik yang mengukur perbandingan antara daya maksimum sinyal citra terhadap daya derau yang mengontaminasinya, dinyatakan dalam satuan desibel (dB). Formulasi PSNR dihitung berdasarkan nilai galat kuadrat rata-rata (")
    add_styled_run(p_psnr, "Mean Squared Error", italic=True)
    add_styled_run(p_psnr, " atau MSE) antara citra acuan bersih dan citra hasil restorasi:")

    create_eq_p(mml_mse)
    create_eq_p(mml_psnr)

    p_psnr_desc = doc.add_paragraph()
    format_p(p_psnr_desc)
    add_styled_run(p_psnr_desc, "di mana ")
    add_styled_run(p_psnr_desc, "M", italic=True)
    add_styled_run(p_psnr_desc, " dan ")
    add_styled_run(p_psnr_desc, "N", italic=True)
    add_styled_run(p_psnr_desc, " adalah dimensi spasial citra dalam baris dan kolom piksel, ")
    add_styled_run(p_psnr_desc, "I", italic=True)
    add_styled_run(p_psnr_desc, "clean", subscript=True)
    add_styled_run(p_psnr_desc, " (")
    add_styled_run(p_psnr_desc, "i", italic=True)
    add_styled_run(p_psnr_desc, ", ")
    add_styled_run(p_psnr_desc, "j", italic=True)
    add_styled_run(p_psnr_desc, ") adalah intensitas piksel citra acuan bersih pada koordinat (")
    add_styled_run(p_psnr_desc, "i", italic=True)
    add_styled_run(p_psnr_desc, ", ")
    add_styled_run(p_psnr_desc, "j", italic=True)
    add_styled_run(p_psnr_desc, "), ")
    add_styled_run(p_psnr_desc, "I", italic=True)
    add_styled_run(p_psnr_desc, "restored", subscript=True)
    add_styled_run(p_psnr_desc, " (")
    add_styled_run(p_psnr_desc, "i", italic=True)
    add_styled_run(p_psnr_desc, ", ")
    add_styled_run(p_psnr_desc, "j", italic=True)
    add_styled_run(p_psnr_desc, ") adalah intensitas piksel citra hasil restorasi, dan MAX")
    add_styled_run(p_psnr_desc, "I", italic=True, subscript=True)
    add_styled_run(p_psnr_desc, " melambangkan nilai intensitas puncak yang dapat dicapai berdasarkan kedalaman bit citra (bernilai 255 pada citra 8-bit, atau 4095 pada citra radiografi digital 12-bit).")

    p_psnr_lim = doc.add_paragraph()
    format_p(p_psnr_lim)
    add_styled_run(p_psnr_lim, "Nilai PSNR yang semakin tinggi merepresentasikan kebersihan citra yang semakin baik dari derau. Kendati demikian, PSNR memiliki keterbatasan mendasar karena menghitung deviasi intensitas secara independen per piksel tanpa memperhitungkan korelasi spasial antar-piksel maupun karakteristik persepsi sistem visual manusia.")

    # 2.5.2 SSIM
    h3_52 = doc.add_paragraph()
    format_p(h3_52, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=4, space_before_pt=8, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h3_52, "2.5.2\tStructural Similarity Index Measure (SSIM)", bold=True, size_pt=12)

    p_ssim = doc.add_paragraph()
    format_p(p_ssim)
    add_styled_run(p_ssim, "Untuk mengatasi keterbatasan metrik berbasis piksel independen, ")
    add_styled_run(p_ssim, "Structural Similarity Index Measure", italic=True)
    add_styled_run(p_ssim, " (SSIM) dikembangkan guna menilai kemiripan struktural antara dua citra berdasarkan pemodelan persepsi sistem visual manusia (")
    add_styled_run(p_ssim, "Human Visual System", italic=True)
    add_styled_run(p_ssim, " atau HVS) (Wang ")
    add_styled_run(p_ssim, "et al.", italic=True)
    add_styled_run(p_ssim, ", 2004). SSIM mengukur kualitas citra dengan mengombinasikan tiga parameter komparasi independen, yaitu luminansi, kontras, dan struktur:")

    create_eq_p(mml_ssim)

    p_ssim_desc = doc.add_paragraph()
    format_p(p_ssim_desc)
    add_styled_run(p_ssim_desc, "di mana ")
    add_styled_run(p_ssim_desc, "μ", italic=True)
    add_styled_run(p_ssim_desc, "x", italic=True, subscript=True)
    add_styled_run(p_ssim_desc, " dan ")
    add_styled_run(p_ssim_desc, "μ", italic=True)
    add_styled_run(p_ssim_desc, "y", italic=True, subscript=True)
    add_styled_run(p_ssim_desc, " adalah rata-rata intensitas lokal pada jendela cuplikan citra acuan ")
    add_styled_run(p_ssim_desc, "x", italic=True)
    add_styled_run(p_ssim_desc, " dan citra hasil restorasi ")
    add_styled_run(p_ssim_desc, "y", italic=True)
    add_styled_run(p_ssim_desc, ", ")
    add_styled_run(p_ssim_desc, "σ", italic=True)
    add_styled_run(p_ssim_desc, "x", italic=True, subscript=True)
    add_styled_run(p_ssim_desc, "2", superscript=True)
    add_styled_run(p_ssim_desc, " dan ")
    add_styled_run(p_ssim_desc, "σ", italic=True)
    add_styled_run(p_ssim_desc, "y", italic=True, subscript=True)
    add_styled_run(p_ssim_desc, "2", superscript=True)
    add_styled_run(p_ssim_desc, " merepresentasikan varians intensitas lokal yang mencerminkan kontras, ")
    add_styled_run(p_ssim_desc, "σ", italic=True)
    add_styled_run(p_ssim_desc, "xy", italic=True, subscript=True)
    add_styled_run(p_ssim_desc, " adalah kovarians intensitas antara ")
    add_styled_run(p_ssim_desc, "x", italic=True)
    add_styled_run(p_ssim_desc, " dan ")
    add_styled_run(p_ssim_desc, "y", italic=True)
    add_styled_run(p_ssim_desc, " yang mengukur korelasi struktur lokal, serta ")
    add_styled_run(p_ssim_desc, "C", italic=True)
    add_styled_run(p_ssim_desc, "1", subscript=True)
    add_styled_run(p_ssim_desc, " = (")
    add_styled_run(p_ssim_desc, "K", italic=True)
    add_styled_run(p_ssim_desc, "1", subscript=True)
    add_styled_run(p_ssim_desc, "L", italic=True)
    add_styled_run(p_ssim_desc, ")")
    add_styled_run(p_ssim_desc, "2", superscript=True)
    add_styled_run(p_ssim_desc, " dan ")
    add_styled_run(p_ssim_desc, "C", italic=True)
    add_styled_run(p_ssim_desc, "2", subscript=True)
    add_styled_run(p_ssim_desc, " = (")
    add_styled_run(p_ssim_desc, "K", italic=True)
    add_styled_run(p_ssim_desc, "2", subscript=True)
    add_styled_run(p_ssim_desc, "L", italic=True)
    add_styled_run(p_ssim_desc, ")")
    add_styled_run(p_ssim_desc, "2", superscript=True)
    add_styled_run(p_ssim_desc, " merupakan konstanta penstabil numerik untuk mencegah pembagian dengan nol saat penyebut bernilai sangat kecil, dengan ")
    add_styled_run(p_ssim_desc, "L", italic=True)
    add_styled_run(p_ssim_desc, " adalah rentang dinamis piksel serta nilai konvensi ")
    add_styled_run(p_ssim_desc, "K", italic=True)
    add_styled_run(p_ssim_desc, "1", subscript=True)
    add_styled_run(p_ssim_desc, " = 0,01 dan ")
    add_styled_run(p_ssim_desc, "K", italic=True)
    add_styled_run(p_ssim_desc, "2", subscript=True)
    add_styled_run(p_ssim_desc, " = 0,03.")

    p_ssim_post = doc.add_paragraph()
    format_p(p_ssim_post)
    add_styled_run(p_ssim_post, "Skor SSIM berada pada rentang 0 hingga 1, dengan nilai 1 menandakan kemiripan struktur geometris yang sempurna terhadap citra acuan. Karena memanfaatkan jendela lokal (")
    add_styled_run(p_ssim_post, "sliding window", italic=True)
    add_styled_run(p_ssim_post, "), SSIM jauh lebih sensitif terhadap penurunan ketajaman tepi dan degradasi tekstur dibandingkan PSNR.")

    # 2.5.3 Task-Based Evaluation
    h3_53 = doc.add_paragraph()
    format_p(h3_53, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=4, space_before_pt=8, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h3_53, "2.5.3\tEvaluasi Berbasis Tugas Klinis (", bold=True)
    add_styled_run(h3_53, "Task-Based Evaluation", bold=True, italic=True)
    add_styled_run(h3_53, ")", bold=True)

    p_tb1 = doc.add_paragraph()
    format_p(p_tb1)
    add_styled_run(p_tb1, "Meskipun PSNR dan SSIM merupakan metrik standar dalam pemrosesan citra, capaian nilai PSNR dan SSIM yang tinggi belum menjamin pelestarian struktur anatomi yang relevan untuk tugas klinis tertentu (Yu ")
    add_styled_run(p_tb1, "et al.", italic=True)
    add_styled_run(p_tb1, ", 2023). Hal ini disebabkan oleh sifat kedua metrik tersebut yang memperlakukan seluruh area citra secara seragam tanpa memberikan bobot khusus pada batas fitur anatomis yang menjadi target diagnostik. Algoritma restorasi yang melakukan penghalusan berlebih (")
    add_styled_run(p_tb1, "over-smoothing", italic=True)
    add_styled_run(p_tb1, ") pada area bidang paru yang luas dapat memperoleh skor PSNR dan SSIM yang sangat tinggi, namun secara bersamaan berisiko mengikis ketajaman garis tepi kardioperikardial.")

    p_tb2 = doc.add_paragraph()
    format_p(p_tb2)
    add_styled_run(p_tb2, "Mengingat tujuan utama dari restorasi citra radiografi toraks pada penelitian ini adalah mendukung ketepatan pengukuran biometri kardiomegali, evaluasi kualitas tidak dapat disandarkan semata pada metrik fidelitas fisik global. Evaluasi kualitas citra perlu diperluas mencakup pendekatan berbasis tugas klinis (")
    add_styled_run(p_tb2, "task-based evaluation", italic=True)
    add_styled_run(p_tb2, "), yaitu dengan menguji pengaruh restorasi secara langsung terhadap pelestarian garis batas anatomi (")
    add_styled_run(p_tb2, "boundary preservation", italic=True)
    add_styled_run(p_tb2, ") dan tingkat galat pengukuran ")
    add_styled_run(p_tb2, "Cardiothoracic Ratio", italic=True)
    add_styled_run(p_tb2, " (CTR).")

    # =========================================================================
    # BAGIAN 2.6 EVALUASI BERBASIS TUGAS KLINIS
    # =========================================================================
    h2_6 = doc.add_paragraph()
    format_p(h2_6, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=6, space_before_pt=14, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h2_6, "2.6\tEvaluasi Berbasis Tugas Klinis", bold=True, size_pt=12)

    p_intro6 = doc.add_paragraph()
    format_p(p_intro6)
    add_styled_run(p_intro6, "Evaluasi kualitas citra konvensional berbasis fidelitas piksel global (PSNR dan SSIM) belum mencerminkan keberhasilan klinis sesungguhnya. Oleh karena itu, penelitian ini menerapkan paradigma evaluasi berbasis tugas (")
    add_styled_run(p_intro6, "task-based evaluation", italic=True)
    add_styled_run(p_intro6, ") yang berfokus langsung pada dua aspek utama, yaitu kemampuan algoritma restorasi dalam melestarikan batas kontur anatomi organ (")
    add_styled_run(p_intro6, "boundary preservation", italic=True)
    add_styled_run(p_intro6, ") serta ketepatan hasil pengukuran biometrik ")
    add_styled_run(p_intro6, "Cardiothoracic Ratio", italic=True)
    add_styled_run(p_intro6, " (CTR).")

    # 2.6.1 Pelestarian Batas Anatomi
    h3_61 = doc.add_paragraph()
    format_p(h3_61, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=4, space_before_pt=8, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h3_61, "2.6.1\tPelestarian Batas Anatomi (", bold=True)
    add_styled_run(h3_61, "Boundary Preservation", bold=True, italic=True)
    add_styled_run(h3_61, ")", bold=True)

    p_bp = doc.add_paragraph()
    format_p(p_bp)
    add_styled_run(p_bp, "Pelestarian batas anatomi mengukur sejauh mana proses penapisan derau mampu mempertahankan ketajaman dan integritas geometris garis tepi organ kardioperikardial serta dinding toraks tanpa menimbulkan distorsi struktural, efek pengaburan (")
    add_styled_run(p_bp, "over-smoothing", italic=True)
    add_styled_run(p_bp, "), maupun artefak halusinasi. Evaluasi pelestarian batas dilakukan melalui perbandingan antara kontur segmentasi hasil restorasi dan kontur acuan bersih menggunakan tiga metrik kuantitatif:")

    # 1. Dice
    p_d1 = doc.add_paragraph()
    format_p(p_d1, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_d1, "1.\tKoefisien Kemiripan Dice (", bold=True)
    add_styled_run(p_d1, "Dice Similarity Coefficient", bold=True, italic=True)
    add_styled_run(p_d1, " atau DSC)", bold=True)
    
    p_d1_desc = doc.add_paragraph()
    format_p(p_d1_desc)
    add_styled_run(p_d1_desc, "DSC mengukur derajat tumpang-tindih spasial (")
    add_styled_run(p_d1_desc, "spatial overlap", italic=True)
    add_styled_run(p_d1_desc, ") antara area biner kontur acuan ")
    add_styled_run(p_d1_desc, "A", italic=True)
    add_styled_run(p_d1_desc, " dan kontur hasil restorasi ")
    add_styled_run(p_d1_desc, "B", italic=True)
    add_styled_run(p_d1_desc, ":")

    create_eq_p(mml_dsc)

    p_d1_post = doc.add_paragraph()
    format_p(p_d1_post)
    add_styled_run(p_d1_post, "Nilai DSC berada pada rentang 0 hingga 1, dengan nilai mendekati 1 merepresentasikan tingkat kesesuaian area anatomi yang semakin sempurna.")

    # 2. Hausdorff Distance
    p_d2 = doc.add_paragraph()
    format_p(p_d2, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_d2, "2.\tJarak Hausdorff (", bold=True)
    add_styled_run(p_d2, "Hausdorff Distance", bold=True, italic=True)
    add_styled_run(p_d2, " atau HD)", bold=True)

    p_d2_desc = doc.add_paragraph()
    format_p(p_d2_desc)
    add_styled_run(p_d2_desc, "HD mengukur jarak pemisahan spasial maksimum terburuk (")
    add_styled_run(p_d2_desc, "worst-case boundary distance", italic=True)
    add_styled_run(p_d2_desc, ") antara titik-titik pada kontur acuan ")
    add_styled_run(p_d2_desc, "A", italic=True)
    add_styled_run(p_d2_desc, " dan kontur restorasi ")
    add_styled_run(p_d2_desc, "B", italic=True)
    add_styled_run(p_d2_desc, ":")

    create_eq_p(mml_hd)

    p_d2_post = doc.add_paragraph()
    format_p(p_d2_post)
    add_styled_run(p_d2_post, "Dalam analisis radiologi digital, persentil ke-95 dari jarak Hausdorff (HD95) digunakan untuk menghindari sensitivitas ekstrem terhadap titik pencilan (")
    add_styled_run(p_d2_post, "outliers", italic=True)
    add_styled_run(p_d2_post, "). Nilai HD95 yang semakin kecil menunjukkan bahwa batas tepi kardiak terestorasi tidak menyimpang jauh dari batas anatomis aslinya.")

    # 3. Mean Surface Distance
    p_d3 = doc.add_paragraph()
    format_p(p_d3, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_d3, "3.\tJarak Permukaan Rata-Rata (", bold=True)
    add_styled_run(p_d3, "Mean Surface Distance", bold=True, italic=True)
    add_styled_run(p_d3, " atau MSD)", bold=True)

    p_d3_desc = doc.add_paragraph()
    format_p(p_d3_desc)
    add_styled_run(p_d3_desc, "MSD mengukur rata-rata jarak euclid terdekat antara seluruh titik pada permukaan kontur ")
    add_styled_run(p_d3_desc, "A", italic=True)
    add_styled_run(p_d3_desc, " dan kontur ")
    add_styled_run(p_d3_desc, "B", italic=True)
    add_styled_run(p_d3_desc, ":")

    create_eq_p(mml_msd)

    p_d3_post = doc.add_paragraph()
    format_p(p_d3_post)
    add_styled_run(p_d3_post, "Metrik MSD melengkapi HD dengan memberikan gambaran deviasi batas anatomi secara menyeluruh, di mana nilai MSD yang rendah mengindikasikan konsistensi kontur organ secara global.")

    # 2.6.2 Galat Pengukuran CTR
    h3_62 = doc.add_paragraph()
    format_p(h3_62, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=4, space_before_pt=8, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h3_62, "2.6.2\tGalat Pengukuran CTR (", bold=True)
    add_styled_run(h3_62, "CTR Measurement Error", bold=True, italic=True)
    add_styled_run(h3_62, ")", bold=True)

    p_ctr = doc.add_paragraph()
    format_p(p_ctr)
    add_styled_run(p_ctr, "Cardiothoracic Ratio", italic=True)
    add_styled_run(p_ctr, " (CTR) merupakan rasio biometrik transversal jantung terhadap rongga dada yang menjadi keluaran klinis utama dalam penelitian ini. Pengaruh degradasi derau dan pemulihan metode restorasi terhadap presisi diagnostik dievaluasi melalui besarnya penyimpangan nilai CTR terestorasi (CTR")
    add_styled_run(p_ctr, "restored", italic=True, subscript=True)
    add_styled_run(p_ctr, ") terhadap nilai CTR acuan baku (CTR")
    add_styled_run(p_ctr, "ref", italic=True, subscript=True)
    add_styled_run(p_ctr, ") menggunakan metrik galat statistik:")

    # 1. Bias
    p_c1 = doc.add_paragraph()
    format_p(p_c1, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_c1, "1.\tGalat Rata-Rata (", bold=True)
    add_styled_run(p_c1, "Mean Error", bold=True, italic=True)
    add_styled_run(p_c1, " atau Bias)", bold=True)

    p_c1_desc = doc.add_paragraph()
    format_p(p_c1_desc)
    add_styled_run(p_c1_desc, "Bias mengukur kecenderungan arah penyimpangan pengukuran secara sistematis:")

    create_eq_p(mml_bias)

    p_c1_post = doc.add_paragraph()
    format_p(p_c1_post)
    add_styled_run(p_c1_post, "Nilai bias positif menunjukkan kecenderungan estimasi berlebih (")
    add_styled_run(p_c1_post, "overestimation", italic=True)
    add_styled_run(p_c1_post, "), sedangkan nilai bias negatif mengindikasikan kecenderungan penyusutan dimensi jantung (")
    add_styled_run(p_c1_post, "underestimation", italic=True)
    add_styled_run(p_c1_post, ") akibat pengikisan batas tepi oleh penapis.")

    # 2. MAE
    p_c2 = doc.add_paragraph()
    format_p(p_c2, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_c2, "2.\tGalat Absolut Rata-Rata (", bold=True)
    add_styled_run(p_c2, "Mean Absolute Error", bold=True, italic=True)
    add_styled_run(p_c2, " atau MAE)", bold=True)

    p_c2_desc = doc.add_paragraph()
    format_p(p_c2_desc)
    add_styled_run(p_c2_desc, "MAE mengukur magnitudo deviasi kesalahan rata-rata tanpa memedulikan arah positif atau negatif:")

    create_eq_p(mml_mae)

    p_c2_post = doc.add_paragraph()
    format_p(p_c2_post)
    add_styled_run(p_c2_post, "Nilai MAE menjadi indikator utama dalam menguji keandalan restorasi, di mana nilai MAE yang mendekati nol merepresentasikan kesalahan klinis yang minimal.")

    # 3. Pearson
    p_c3 = doc.add_paragraph()
    format_p(p_c3, space_after_pt=2, first_line_indent_pt=0)
    add_styled_run(p_c3, "3.\tKorelasi Linear Pearson (", bold=True)
    add_styled_run(p_c3, "r", bold=True, italic=True)
    add_styled_run(p_c3, ")", bold=True)

    p_c3_desc = doc.add_paragraph()
    format_p(p_c3_desc)
    add_styled_run(p_c3_desc, "Korelasi Pearson digunakan sebagai analisis pendukung guna mengukur kekuatan hubungan linear antara nilai CTR hasil restorasi dan acuan standar:")

    create_eq_p(mml_pearson)

    p_c3_post = doc.add_paragraph()
    format_p(p_c3_post)
    add_styled_run(p_c3_post, "Nilai ")
    add_styled_run(p_c3_post, "r", italic=True)
    add_styled_run(p_c3_post, " mendekati 1 menunjukkan bahwa metode restorasi mempertahankan urutan dan proporsi nilai biometri pasien secara konsisten.")

    # 2.6.3 Bland-Altman
    h3_63 = doc.add_paragraph()
    format_p(h3_63, align=WD_ALIGN_PARAGRAPH.LEFT, space_after_pt=4, space_before_pt=8, line_spacing=1.5, first_line_indent_pt=0)
    add_styled_run(h3_63, "2.6.3\tAnalisis Kesepakatan Bland–Altman (", bold=True)
    add_styled_run(h3_63, "Bland–Altman Agreement Analysis", bold=True, italic=True)
    add_styled_run(h3_63, ")", bold=True)

    p_ba1 = doc.add_paragraph()
    format_p(p_ba1)
    add_styled_run(p_ba1, "Korelasi linear yang tinggi tidak serta merta membuktikan adanya kesepakatan (")
    add_styled_run(p_ba1, "agreement", italic=True)
    add_styled_run(p_ba1, ") klinis yang setara antara dua metode pengukuran (Bland & Altman, 1986). Dua metode dapat memiliki korelasi linear mendekati 1 meskipun terdapat perbedaan sistematis yang konstan. Oleh karena itu, analisis grafis dan kuantitatif Bland–Altman diterapkan untuk mengevaluasi derajat kesesuaian klinis antara pengukuran CTR pasca-restorasi dan acuan standar.")

    p_ba2 = doc.add_paragraph()
    format_p(p_ba2)
    add_styled_run(p_ba2, "Plot Bland–Altman memetakan selisih perbedaan pengukuran (")
    add_styled_run(p_ba2, "d", italic=True)
    add_styled_run(p_ba2, "i", italic=True, subscript=True)
    add_styled_run(p_ba2, " = CTR")
    add_styled_run(p_ba2, "restored", italic=True, subscript=True)
    add_styled_run(p_ba2, ",i", italic=True, subscript=True)
    add_styled_run(p_ba2, " \u2212 CTR")
    add_styled_run(p_ba2, "ref", italic=True, subscript=True)
    add_styled_run(p_ba2, ",i", italic=True, subscript=True)
    add_styled_run(p_ba2, ") pada sumbu vertikal terhadap nilai rata-rata dari kedua pengukuran pada sumbu horizontal. Kesepakatan klinis dinilai berdasarkan garis bias rata-rata (")
    add_styled_run(p_ba2, "d̄", italic=True)
    add_styled_run(p_ba2, ") dan rentang batas kesepakatan (")
    add_styled_run(p_ba2, "Limits of Agreement", italic=True)
    add_styled_run(p_ba2, " atau LoA):")

    create_eq_p(mml_loa)

    p_ba3 = doc.add_paragraph()
    format_p(p_ba3)
    add_styled_run(p_ba3, "di mana SD adalah simpangan baku dari selisih perbedaan pengukuran. Secara statistik, sebanyak 95% selisih pengukuran diharapkan berada di dalam rentang LoA. Metode restorasi dinyatakan memiliki kesepakatan klinis yang dapat diterima (")
    add_styled_run(p_ba3, "clinically acceptable", italic=True)
    add_styled_run(p_ba3, ") apabila nilai bias rata-rata mendekati nol dan seluruh rentang LoA berada di dalam batas toleransi variabilitas diagnostik kardiomegali yang diizinkan secara medis (misalnya rentang galat CTR tidak melebihi \u00b1 0,02 atau 2%).")

    out_path = r'd:\laragon\www\belajar-laravel\RUMUS_SUBBAB_2_5_DAN_2_6.docx'
    doc.save(out_path)
    print("Successfully generated:", out_path)

if __name__ == '__main__':
    main()

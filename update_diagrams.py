# 1. Update SVG
svg_content = '''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 870" width="500" height="870" style="background-color: white;">
  <defs>
    <marker id="arrow" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
      <path d="M 0 1 L 10 5 L 0 9 z" fill="#000"/>
    </marker>
  </defs>
  <style>
    text { font-family: 'Times New Roman', serif; text-anchor: middle; fill: #000; }
    .term { fill: #fff; stroke: #000; stroke-width: 1.5; rx: 18; ry: 18; }
    .proc { fill: #fff; stroke: #000; stroke-width: 1.5; }
    .line { stroke: #000; stroke-width: 1.5; fill: none; marker-end: url(#arrow); }
    .title { font-size: 13px; font-weight: bold; }
    .sub { font-size: 11.5px; }
  </style>

  <!-- Start -->
  <rect x="180" y="30" width="140" height="36" class="term"/>
  <text x="250" y="53" class="title">Mulai</text>
  <line x1="250" y1="66" x2="250" y2="95" class="line"/>

  <!-- S1 -->
  <rect x="65" y="95" width="370" height="52" class="proc"/>
  <text x="250" y="116" class="title">Kurasi dan De-identifikasi Dataset PA</text>
  <text x="250" y="134" class="sub">(JSRT, VinDr-CXR, dan CDC Surabaya)</text>
  <line x1="250" y1="147" x2="250" y2="175" class="line"/>

  <!-- S2 -->
  <rect x="65" y="175" width="370" height="52" class="proc"/>
  <text x="250" y="196" class="title">Prapemrosesan Citra DICOM Terstandarisasi</text>
  <text x="250" y="214" class="sub">(Inversi Fotometrik dan Normalisasi Skala)</text>
  <line x1="250" y1="227" x2="250" y2="255" class="line"/>

  <!-- S3 -->
  <rect x="65" y="255" width="370" height="52" class="proc"/>
  <text x="250" y="276" class="title">Injeksi Derau Campuran Poisson-Gaussian</text>
  <text x="250" y="294" class="sub">(Simulasi Dosis Rendah: &#963;n = 15, 25, 50)</text>
  <line x1="250" y1="307" x2="250" y2="335" class="line"/>

  <!-- S4 -->
  <rect x="65" y="335" width="370" height="52" class="proc"/>
  <text x="250" y="356" class="title">Restorasi Citra Komparatif</text>
  <text x="250" y="374" class="sub">(Gaussian, Bilateral Filter, dan DnCNN)</text>
  <line x1="250" y1="387" x2="250" y2="415" class="line"/>

  <!-- S5 -->
  <rect x="65" y="415" width="370" height="52" class="proc"/>
  <text x="250" y="436" class="title">Segmentasi Kontur dan Pengukuran CTR</text>
  <text x="250" y="454" class="sub">(Kaidah Baku Metode Danzer 1919)</text>
  <line x1="250" y1="467" x2="250" y2="495" class="line"/>

  <!-- S6 -->
  <rect x="65" y="495" width="370" height="52" class="proc"/>
  <text x="250" y="516" class="title">Evaluasi Metrik Berbasis Tugas Klinis</text>
  <text x="250" y="534" class="sub">(PSNR, SSIM, DSC, HD95, MSD, dan MAE)</text>
  <line x1="250" y1="547" x2="250" y2="575" class="line"/>

  <!-- S7 -->
  <rect x="65" y="575" width="370" height="52" class="proc"/>
  <text x="250" y="596" class="title">Analisis Statistik Inferensial</text>
  <text x="250" y="614" class="sub">(ANOVA / Friedman dan Koreksi Holm)</text>
  <line x1="250" y1="627" x2="250" y2="655" class="line"/>

  <!-- S8 -->
  <rect x="65" y="655" width="370" height="52" class="proc"/>
  <text x="250" y="676" class="title">Integrasi Demonstrator Web DICOM Workstation</text>
  <text x="250" y="694" class="sub">(Pengujian Interoperabilitas dan Latensi)</text>
  <line x1="250" y1="707" x2="250" y2="735" class="line"/>

  <!-- End -->
  <rect x="180" y="735" width="140" height="36" class="term"/>
  <text x="250" y="758" class="title">Selesai</text>
</svg>'''

with open(r'd:\laragon\www\belajar-laravel\diagram_3_1_aliran_penelitian.svg', 'w', encoding='utf-8') as f:
    f.write(svg_content)
print("SVG updated successfully!")

# 2. Update Drawio
import xml.etree.ElementTree as ET

mxfile = ET.Element('mxfile', host='app.diagrams.net', modified='2026-10-28T10:55:00.000Z', agent='Antigravity', version='24.0.0', type='device')
diagram = ET.SubElement(mxfile, 'diagram', id='Diagram-3-1', name='Gambar 3.1')
model = ET.SubElement(diagram, 'mxGraphModel', dx='1000', dy='1000', grid='1', gridSize='10', guides='1', tooltips='1', connect='1', arrows='1', fold='1', page='1', pageScale='1', pageWidth='827', pageHeight='1169', math='0', shadow='0')
root = ET.SubElement(model, 'root')

ET.SubElement(root, 'mxCell', id='0')
ET.SubElement(root, 'mxCell', id='1', parent='0')

nodes = [
    {'id': 'start', 'val': 'Mulai', 'type': 'term', 'w': 140, 'h': 36, 'x': 230, 'y': 40},
    {'id': 's1', 'val': '<b>Kurasi dan De-identifikasi Dataset PA</b><br>(JSRT, VinDr-CXR, dan CDC Surabaya)', 'type': 'proc', 'w': 360, 'h': 54, 'x': 120, 'y': 110},
    {'id': 's2', 'val': '<b>Prapemrosesan Citra DICOM Terstandarisasi</b><br>(Inversi Fotometrik dan Normalisasi Skala)', 'type': 'proc', 'w': 360, 'h': 54, 'x': 120, 'y': 195},
    {'id': 's3', 'val': '<b>Injeksi Derau Campuran Poisson-Gaussian</b><br>(Simulasi Dosis Rendah: &sigma;n = 15, 25, 50)', 'type': 'proc', 'w': 360, 'h': 54, 'x': 120, 'y': 280},
    {'id': 's4', 'val': '<b>Restorasi Citra Komparatif</b><br>(Gaussian, Bilateral Filter, dan DnCNN)', 'type': 'proc', 'w': 360, 'h': 54, 'x': 120, 'y': 365},
    {'id': 's5', 'val': '<b>Segmentasi Kontur dan Pengukuran CTR</b><br>(Kaidah Baku Metode Danzer 1919)', 'type': 'proc', 'w': 360, 'h': 54, 'x': 120, 'y': 450},
    {'id': 's6', 'val': '<b>Evaluasi Metrik Berbasis Tugas Klinis</b><br>(PSNR, SSIM, DSC, HD95, MSD, dan MAE)', 'type': 'proc', 'w': 360, 'h': 54, 'x': 120, 'y': 535},
    {'id': 's7', 'val': '<b>Analisis Statistik Inferensial</b><br>(ANOVA / Friedman dan Koreksi Holm)', 'type': 'proc', 'w': 360, 'h': 54, 'x': 120, 'y': 620},
    {'id': 's8', 'val': '<b>Integrasi Demonstrator Web DICOM Workstation</b><br>(Pengujian Interoperabilitas dan Latensi)', 'type': 'proc', 'w': 360, 'h': 54, 'x': 120, 'y': 705},
    {'id': 'end', 'val': 'Selesai', 'type': 'term', 'w': 140, 'h': 36, 'x': 230, 'y': 790}
]

style_term = 'rounded=1;arcSize=50;whiteSpace=wrap;html=1;strokeColor=#000000;strokeWidth=1.5;fillColor=#ffffff;fontFamily=Times New Roman;fontSize=12;fontColor=#000000;'
style_proc = 'rounded=0;whiteSpace=wrap;html=1;strokeColor=#000000;strokeWidth=1.5;fillColor=#ffffff;fontFamily=Times New Roman;fontSize=11;fontColor=#000000;align=center;verticalAlign=middle;'
style_edge = 'edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#000000;strokeWidth=1.5;endArrow=classic;endFill=1;'

for n in nodes:
    st = style_term if n['type'] == 'term' else style_proc
    cell = ET.SubElement(root, 'mxCell', id=n['id'], value=n['val'], style=st, vertex='1', parent='1')
    geo = ET.SubElement(cell, 'mxGeometry', x=str(n['x']), y=str(n['y']), width=str(n['w']), height=str(n['h']))
    geo.set('as', 'geometry')

for i in range(len(nodes) - 1):
    src = nodes[i]['id']
    tgt = nodes[i+1]['id']
    edge_id = f'e_{src}_{tgt}'
    edge = ET.SubElement(root, 'mxCell', id=edge_id, style=style_edge, edge='1', parent='1', source=src, target=tgt)
    geo = ET.SubElement(edge, 'mxGeometry', relative='1')
    geo.set('as', 'geometry')

tree = ET.ElementTree(mxfile)
tree.write(r'd:\laragon\www\belajar-laravel\diagram_3_1_aliran_penelitian.drawio', encoding='utf-8', xml_declaration=True)
print("Draw.io file updated successfully!")


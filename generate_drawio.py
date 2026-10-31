import xml.etree.ElementTree as ET
import html

# Create Draw.io XML
mxfile = ET.Element('mxfile', host='app.diagrams.net', modified='2026-10-28T10:45:00.000Z', agent='Antigravity', version='24.0.0', type='device')
diagram = ET.SubElement(mxfile, 'diagram', id='Diagram-3-1', name='Gambar 3.1')
model = ET.SubElement(diagram, 'mxGraphModel', dx='1000', dy='1000', grid='1', gridSize='10', guides='1', tooltips='1', connect='1', arrows='1', fold='1', page='1', pageScale='1', pageWidth='827', pageHeight='1169', math='0', shadow='0')
root = ET.SubElement(model, 'root')

ET.SubElement(root, 'mxCell', id='0')
ET.SubElement(root, 'mxCell', id='1', parent='0')

nodes = [
    {
        'id': 'start',
        'val': 'Mulai',
        'type': 'term',
        'w': 140, 'h': 36, 'x': 230, 'y': 40
    },
    {
        'id': 's1',
        'val': '<b>Kurasi dan De-identifikasi Dataset PA</b><br>(JSRT, VinDr-CXR, dan CDC Surabaya)',
        'type': 'proc',
        'w': 340, 'h': 54, 'x': 130, 'y': 110
    },
    {
        'id': 's2',
        'val': '<b>Prapemrosesan Citra DICOM Terstandarisasi</b><br>(Inversi Fotometrik dan Normalisasi Skala)',
        'type': 'proc',
        'w': 340, 'h': 54, 'x': 130, 'y': 195
    },
    {
        'id': 's3',
        'val': '<b>Injeksi Derau Campuran Poisson-Gaussian</b><br>(Simulasi Dosis Rendah: &sigma;<sub>n</sub> = 15, 25, 50)',
        'type': 'proc',
        'w': 340, 'h': 54, 'x': 130, 'y': 280
    },
    {
        'id': 's4',
        'val': '<b>Restorasi Citra Komparatif</b><br>(Gaussian, Bilateral Filter, dan DnCNN)',
        'type': 'proc',
        'w': 340, 'h': 54, 'x': 130, 'y': 365
    },
    {
        'id': 's5',
        'val': '<b>Segmentasi Kontur dan Pengukuran CTR</b><br>(Kaidah Baku Metode Danzer 1919)',
        'type': 'proc',
        'w': 340, 'h': 54, 'x': 130, 'y': 450
    },
    {
        'id': 's6',
        'val': '<b>Evaluasi Metrik Berbasis Tugas Klinis</b><br>(PSNR, SSIM, DSC, HD95, MSD, dan MAE)',
        'type': 'proc',
        'w': 340, 'h': 54, 'x': 130, 'y': 535
    },
    {
        'id': 's7',
        'val': '<b>Analisis Statistik Inferensial</b><br>(ANOVA / Friedman dan Koreksi Holm)',
        'type': 'proc',
        'w': 340, 'h': 54, 'x': 130, 'y': 620
    },
    {
        'id': 's8',
        'val': '<b>Integrasi Demonstrator Web DICOM Workstation</b><br>(Pengujian Interoperabilitas dan Latensi)',
        'type': 'proc',
        'w': 340, 'h': 54, 'x': 130, 'y': 705
    },
    {
        'id': 'end',
        'val': 'Selesai',
        'type': 'term',
        'w': 140, 'h': 36, 'x': 230, 'y': 790
    }
]

style_term = "rounded=1;arcSize=50;whiteSpace=wrap;html=1;strokeColor=#000000;strokeWidth=1.5;fillColor=#ffffff;fontFamily=Times New Roman;fontSize=12;fontColor=#000000;"
style_proc = "rounded=0;whiteSpace=wrap;html=1;strokeColor=#000000;strokeWidth=1.5;fillColor=#ffffff;fontFamily=Times New Roman;fontSize=11;fontColor=#000000;align=center;verticalAlign=middle;"
style_edge = "edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;strokeColor=#000000;strokeWidth=1.5;endArrow=classic;endFill=1;"

for n in nodes:
    st = style_term if n['type'] == 'term' else style_proc
    cell = ET.SubElement(root, 'mxCell', id=n['id'], value=n['val'], style=st, vertex='1', parent='1')
    geo = ET.SubElement(cell, 'mxGeometry', x=str(n['x']), y=str(n['y']), width=str(n['w']), height=str(n['h']), as_='geometry')

# Edges
for i in range(len(nodes) - 1):
    src = nodes[i]['id']
    tgt = nodes[i+1]['id']
    edge_id = f"e_{src}_{tgt}"
    edge = ET.SubElement(root, 'mxCell', id=edge_id, style=style_edge, edge='1', parent='1', source=src, target=tgt)
    geo = ET.SubElement(edge, 'mxGeometry', relative='1', as_='geometry')

tree = ET.ElementTree(mxfile)
out_path = r'd:\laragon\www\belajar-laravel\diagram_3_1_aliran_penelitian.drawio'
tree.write(out_path, encoding='utf-8', xml_declaration=True)
print("Successfully generated:", out_path)


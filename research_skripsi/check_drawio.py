import xml.etree.ElementTree as ET
tree = ET.parse(r'd:\laragon\www\belajar-laravel\diagram_3_1_aliran_penelitian.drawio')
root = tree.getroot()
for cell in root.iter('mxCell'):
    geo = cell.find('mxGeometry')
    if geo is not None and cell.get('vertex') == '1':
        print(f"{cell.get('id')}: x={geo.get('x')}, y={geo.get('y')}, w={geo.get('width')}, h={geo.get('height')}, as={geo.get('as')}")


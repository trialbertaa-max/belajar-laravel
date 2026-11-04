import docx
from lxml import etree

doc = docx.Document('d:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx')

with open('section24_dump.txt', 'w', encoding='utf-8') as f:
    f.write("=== Paragraphs in Section 2.4 ===\n")
    for i in range(169, 187):
        p = doc.paragraphs[i]
        root = etree.fromstring(p._p.xml.encode('utf-8'))
        math_texts = root.xpath('//m:t/text()', namespaces={'m': 'http://schemas.openxmlformats.org/officeDocument/2006/math'})
        m_str = ''.join(math_texts)
        f.write(f"p[{i}] style={p.style.name} text={p.text!r} math={m_str!r}\n")

print("Dumped to section24_dump.txt")

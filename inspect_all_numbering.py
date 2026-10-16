import docx

doc = docx.Document('DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU.docx')
num_xml = doc.part.numbering_part._element.xml
print("Length of numbering xml:", len(num_xml))

# Let's inspect all paragraphs and their displayed prefix if possible, or print all numbered paragraphs in the document
for i, p in enumerate(doc.paragraphs):
    pPr = p._p.get_or_add_pPr()
    numPr = pPr.find('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}numPr')
    if numPr is not None or p.style.name.startswith('Heading') or any(p.text.strip().startswith(x) for x in ['1.', '2.', '3.', 'a.', 'b.', 'c.', 'A.', 'B.', 'C.', 'Gambar', 'Tabel', 'Persamaan']):
        if len(p.text.strip()) > 0:
            print(f"P[{i}] ({p.style.name}): {p.text[:80]}")

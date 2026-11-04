import docx

doc = docx.Document('DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU.docx')
for i in [105, 111, 158, 173, 174, 221, 239, 244, 290]:
    p = doc.paragraphs[i]
    numPr = p._p.get_or_add_pPr().find('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}numPr')
    print(f'P[{i}]: text="{p.text}", numPr={numPr is not None}')

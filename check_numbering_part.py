import docx
from docx.oxml import parse_xml

doc = docx.Document('DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU.docx')
# Let's inspect numbering part
num_part = doc.part.numbering_part
if num_part is not None:
    print("Numbering part exists!")
    # Let's check numPr for P[105] and P[221]
    for idx in [105, 111, 158, 221, 239, 290]:
        p = doc.paragraphs[idx]
        pPr = p._p.get_or_add_pPr()
        numPr = pPr.find('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}numPr')
        if numPr is not None:
            ilvl = numPr.find('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}ilvl')
            numId = numPr.find('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}numId')
            ilvl_val = ilvl.get('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}val') if ilvl is not None else 'None'
            numId_val = numId.get('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}val') if numId is not None else 'None'
            print(f"P[{idx}]: {p.text} -> numId={numId_val}, ilvl={ilvl_val}")

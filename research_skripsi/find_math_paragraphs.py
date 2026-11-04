import docx

doc = docx.Document('DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU.docx')
for i, p in enumerate(doc.paragraphs):
    if any(c in p.text for c in ['=', '+', '×', 'σ', 'λ', 'μ', '∑', 'log', 'max', 'min', 'exp']):
        if len(p.text.strip()) > 0 and len(p.text.strip()) < 150:
            print(f"P[{i}]: {p.text.strip()}")

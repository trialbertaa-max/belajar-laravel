import docx

doc = docx.Document('d:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx')
with open('inspect_242_eq.txt', 'w', encoding='utf-8') as f:
    for idx in [180, 181, 182, 183]:
        p = doc.paragraphs[idx]
        f.write(f"=== P{idx} ===\n")
        f.write(f"Text: {p.text}\n")
        for om in p._p.xpath('.//*[local-name()="oMath"]'):
            texts = [t.text for t in om.xpath('.//*[local-name()="t"]') if t.text]
            f.write(f"Math: {' '.join(texts)}\n")

print("Saved inspect_242_eq.txt")

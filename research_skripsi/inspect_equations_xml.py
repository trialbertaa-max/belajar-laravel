import docx

doc = docx.Document('d:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx')
with open('eq_xml.txt', 'w', encoding='utf-8') as f:
    for idx in [174, 176]:
        p = doc.paragraphs[idx]
        f.write(f"=== P{idx} ===\n")
        f.write(p._p.xml + "\n\n")

print("Saved eq_xml.txt as UTF-8")

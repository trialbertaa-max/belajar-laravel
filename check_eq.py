import docx

doc = docx.Document('d:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx')
for i in [143, 161, 174, 176, 181, 182, 191, 193, 211, 212, 216]:
    p = doc.paragraphs[i]
    print(f"P{i}: text='{p.text}'")

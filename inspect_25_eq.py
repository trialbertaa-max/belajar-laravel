import docx

doc = docx.Document('d:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx')

with open('inspect_25_eq.txt', 'w', encoding='utf-8') as f:
    for idx in range(216, 226):
        p = doc.paragraphs[idx]
        omaths = p._p.xpath('.//m:oMath')
        f.write(f"p[{idx}] ({p.style.name}): {p.text}\n")
        for om in omaths:
            f.write(f"  omath: {om.xpath('string(.)')}\n")

print("Done writing inspect_25_eq.txt")

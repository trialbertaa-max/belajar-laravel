import docx

doc = docx.Document('d:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx')

with open('inspect_241.txt', 'w', encoding='utf-8') as f:
    for i in range(172, 179):
        p = doc.paragraphs[i]
        f.write(f"=== Paragraph {i} (style: {p.style.name}) ===\n")
        f.write(f"Full text: {p.text}\n")
        runs_info = [(r.text, r.italic, r.bold) for r in p.runs]
        f.write(f"Runs: {runs_info}\n")
        omath = p._p.xpath('.//m:oMath')
        f.write(f"oMath count: {len(omath)}\n\n")

print("Done writing inspect_241.txt")

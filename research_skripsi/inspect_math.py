import sys
sys.stdout.reconfigure(encoding='utf-8')
import docx

doc = docx.Document('d:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx')

for idx in [143, 161, 174, 176]:
    p = doc.paragraphs[idx]
    omath = p._p.xpath('.//*[local-name()="oMath"]')
    print(f"P{idx}: count(oMath)={len(omath)}")
    for om in omath:
        texts = [t.text for t in om.xpath('.//*[local-name()="t"]') if t.text]
        print(f"  math tokens: {' '.join(texts)}")

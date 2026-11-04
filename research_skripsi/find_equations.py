import sys
sys.stdout.reconfigure(encoding='utf-8')
import docx

doc = docx.Document('d:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx')
for i, p in enumerate(doc.paragraphs):
    has_math = len(p._p.xpath('.//m:oMath')) > 0
    if '(2.' in p.text or 'Persamaan' in p.text or has_math:
        print(f"P{i:03d} [math={has_math}]: {p.text[:110]}")

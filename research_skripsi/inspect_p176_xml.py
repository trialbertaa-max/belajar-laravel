import sys
sys.stdout.reconfigure(encoding='utf-8')
import docx

doc = docx.Document('d:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx')
p = doc.paragraphs[176]
print(p._p.xml)

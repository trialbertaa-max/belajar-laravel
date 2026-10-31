import docx
doc = docx.Document('d:/laragon/www/belajar-laravel/test_243_result.docx')
p = doc.paragraphs[210]
for idx, r in enumerate(p.runs):
    print(f'[{idx}] text="{r.text}" italic={r.italic}')

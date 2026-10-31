import sys
sys.stdout.reconfigure(encoding='utf-8')
import docx

doc = docx.Document('d:/laragon/www/belajar-laravel/test_241_result.docx')

print("=== ALL RUNS IN 2.4.1 ===")
for p_idx in range(172, 181):
    p = doc.paragraphs[p_idx]
    print(f"\n--- Paragraph {p_idx} ({p.style.name}) ---")
    for r in p.runs:
        style_flag = []
        if r.bold: style_flag.append('BOLD')
        if r.italic: style_flag.append('ITALIC')
        if r.font.subscript: style_flag.append('SUB')
        flags = '+'.join(style_flag) if style_flag else 'REGULAR'
        print(f"[{flags}] {repr(r.text)}")

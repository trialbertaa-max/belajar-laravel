import docx
import re

doc = docx.Document('d:/laragon/www/belajar-laravel/test_241_result.docx')

# List of known foreign/technical words that should NOT be in regular runs
foreign_words = [
    'gaussian', 'filter', 'kernel', 'spatial', 'domain', 'isotropic',
    'blurring', 'edge', 'baseline', 'averaging', 'neighborhood',
    'window', 'posteroanterior', 'cardiothoracic', 'ratio', 'preserving'
]

print("=== Scanning non-italic runs in 2.4.1 ===")
for p_idx in range(172, 181):
    p = doc.paragraphs[p_idx]
    for r_idx, r in enumerate(p.runs):
        if not r.italic:
            text_lower = r.text.lower()
            for fw in foreign_words:
                # Use regex word boundary to find exact word matches
                matches = re.findall(rf'\b{fw}\b', text_lower)
                if matches:
                    print(f"P{p_idx} Run[{r_idx}] '{r.text}' contains non-italic '{fw}'")

print("=== Scan completed ===")

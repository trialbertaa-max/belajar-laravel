import docx
import re

doc = docx.Document('d:/laragon/www/belajar-laravel/test_242_result.docx')

foreign_words = [
    'bilateral', 'filter', 'filtering', 'gaussian', 'kernel', 'spatial',
    'domain', 'proximity', 'photometric', 'tonal', 'threshold', 'boundary',
    'smoothing', 'preservation', 'preserving', 'mottle', 'staircasing',
    'artifact', 'isotropic', 'posteroanterior', 'cardiopericardial',
    'costophrenic', 'angle', 'border', 'averaging', 'neighborhood', 'window',
    'plateau', 'grid', 'search', 'task-based', 'evaluation', 'gain'
]

print("=== Scanning non-italic runs in 2.4.2 ===")
for p_idx in range(181, 197):
    p = doc.paragraphs[p_idx]
    for r_idx, r in enumerate(p.runs):
        if not r.italic:
            text_lower = r.text.lower()
            for fw in foreign_words:
                matches = re.findall(rf'\b{fw}\b', text_lower)
                if matches:
                    print(f"P{p_idx} Run[{r_idx}] '{r.text}' contains non-italic '{fw}'")

print("=== Scan completed ===")

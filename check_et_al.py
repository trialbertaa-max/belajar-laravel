import docx

doc = docx.Document('d:/laragon/www/belajar-laravel/DRAFT PROPOSAL SKRIPSI_REVISI_TERBARU_BACKUP.docx')

et_al_runs = []
for p_idx, p in enumerate(doc.paragraphs):
    for r_idx, r in enumerate(p.runs):
        if 'et al' in r.text:
            et_al_runs.append((p_idx, r.text, r.italic))

print(f"Total runs containing 'et al': {len(et_al_runs)}")
for p_idx, text, is_italic in et_al_runs[:15]:
    print(f"  P{p_idx} italic={is_italic} : {repr(text)}")

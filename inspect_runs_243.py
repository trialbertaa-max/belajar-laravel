import docx

doc = docx.Document('test_243_result.docx')
with open('dump_runs_243.txt', 'w', encoding='utf-8') as f:
    for p_idx in [197, 198, 199, 200, 201, 202, 203, 204, 205, 206, 207, 208, 209, 210]:
        p = doc.paragraphs[p_idx]
        f.write(f"\n=== Paragraph {p_idx} (style: {p.style.name}) ===\n")
        for r_idx, r in enumerate(p.runs):
            f.write(f"  [{r_idx}] i={r.italic} b={r.bold} sub={r.font.subscript} sup={r.font.superscript} : {repr(r.text)}\n")
print("Done writing dump_runs_243.txt")

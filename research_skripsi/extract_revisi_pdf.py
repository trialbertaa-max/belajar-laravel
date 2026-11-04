import pypdf
import os

pdf_path = os.path.expandvars(r'C:\Users\Guest User\.gemini\antigravity-ide\brain\d5fcbe69-63fa-47ba-8baa-936593c181a2\.tempmediaStorage\media_1791252559411.pdf')
reader = pypdf.PdfReader(pdf_path)
print(f"Total pages: {len(reader.pages)}")

with open('extracted_revisi_berta.txt', 'w', encoding='utf-8') as out:
    for idx, page in enumerate(reader.pages):
        text = page.extract_text() or ''
        out.write(f"\n--- PAGE {idx+1} ---\n{text}\n")
        # Check annotations/comments
        if '/Annots' in page:
            annots = page['/Annots']
            for annot in annots:
                obj = annot.get_object()
                if '/Contents' in obj:
                    out.write(f"[COMMENT Page {idx+1}]: {obj['/Contents']}\n")

print("Saved to extracted_revisi_berta.txt")

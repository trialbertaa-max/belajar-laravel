with open('recovered_$R2V56TF.asd.asd', 'rb') as f:
    data = f.read()

docx_data = data[160244:]
with open('extracted_user_autorecover.docx', 'wb') as f:
    f.write(docx_data)

print("Saved extracted_user_autorecover.docx, size:", len(docx_data))

import docx
doc = docx.Document('extracted_user_autorecover.docx')
print("Successfully opened with python-docx!")
print("Total paragraphs:", len(doc.paragraphs))

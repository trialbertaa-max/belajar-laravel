import zipfile

with zipfile.ZipFile('extracted_user_autorecover.docx', 'r') as z:
    for info in z.infolist():
        print(info.filename, info.file_size)
    doc_xml = z.read('word/document.xml')
    print("word/document.xml length:", len(doc_xml))
    print("Beginning of document.xml:", doc_xml[:300])

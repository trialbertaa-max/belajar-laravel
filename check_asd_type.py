import zipfile

try:
    with zipfile.ZipFile('recovered_$R2V56TF.asd.asd', 'r') as z:
        print("Valid ZIP/DOCX! Namelist:", z.namelist()[:10])
except Exception as e:
    print("Not zip:", e)
    with open('recovered_$R2V56TF.asd.asd', 'rb') as f:
        print("Magic bytes:", f.read(16))

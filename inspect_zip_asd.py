import zipfile

with zipfile.ZipFile('recovered_$R2V56TF.asd.asd', 'r') as z:
    for name in z.namelist():
        print(name, z.getinfo(name).file_size)

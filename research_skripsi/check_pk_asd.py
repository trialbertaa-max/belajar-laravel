with open('recovered_$R2V56TF.asd.asd', 'rb') as f:
    data = f.read()

print("File length:", len(data))
# Check if there is another PK header or OLE compound header inside
import re
pks = [m.start() for m in re.finditer(b'PK\x03\x04', data)]
print("PK headers found at positions:", pks[:10], "total:", len(pks))

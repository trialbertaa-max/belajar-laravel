with open('recovered_$R2V56TF.asd.asd', 'rb') as f:
    data = f.read()

import re
headings = re.findall(rb'(?:BAB\s+[IVX]+|\d+\.\d+(?:\.\d+)?)\s+[^\r\n]{5,60}', data)
print(f"Found {len(headings)} headings in asd:")
for h in headings[:30]:
    try:
        print(" ", h.decode('utf-8'))
    except:
        print(" ", h.decode('latin1'))

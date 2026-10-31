with open('recovered_$R2V56TF.asd.asd', 'rb') as f:
    data = f.read()

import re
matches = [m.start() for m in re.finditer(b'PK\x03\x04', data)]
for pos in matches:
    name_len = int.from_bytes(data[pos+26:pos+28], 'little')
    name = data[pos+30:pos+30+name_len].decode('latin1', errors='replace')
    print(f"Offset {pos}: {name}")

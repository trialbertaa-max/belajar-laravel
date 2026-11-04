with open('recovered_$R2V56TF.asd.asd', 'rb') as f:
    data = f.read()

import re
matches = [m.start() for m in re.finditer(b'word/document.xml', data)]
print("Found 'word/document.xml' at offsets:", matches)

for m in matches:
    # Look back for PK\x03\x04
    pk_pos = data.rfind(b'PK\x03\x04', 0, m)
    print(f"PK pos: {pk_pos}, distance to name: {m - pk_pos}")
    # compression method at pk_pos + 8
    comp_method = int.from_bytes(data[pk_pos+8:pk_pos+10], 'little')
    csize = int.from_bytes(data[pk_pos+18:pk_pos+22], 'little')
    usize = int.from_bytes(data[pk_pos+22:pk_pos+26], 'little')
    extra_len = int.from_bytes(data[pk_pos+28:pk_pos+30], 'little')
    name_len = int.from_bytes(data[pk_pos+26:pk_pos+28], 'little')
    payload_start = pk_pos + 30 + name_len + extra_len
    payload = data[payload_start:payload_start+csize]
    print(f"comp_method: {comp_method}, csize: {csize}, usize: {usize}, payload len: {len(payload)}")
    if comp_method == 8: # deflate
        import zlib
        try:
            decomp = zlib.decompress(payload, -15)
            print("Successfully decompressed! Decompressed length:", len(decomp))
            with open(f'decomp_doc_{pk_pos}.xml', 'wb') as out_f:
                out_f.write(decomp)
            print(f"Saved decomp_doc_{pk_pos}.xml")
        except Exception as e:
            print("Decompress error:", e)
    elif comp_method == 0:
        print("Stored uncompressed")

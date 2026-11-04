with open('recovered_$R2V56TF.asd.asd', 'rb') as f:
    data = f.read()

import re
matches = [m.start() for m in re.finditer(b'Bilateral', data)]
print(f"Found 'Bilateral' {len(matches)} times! Positions: {matches[:5]}")
for m in matches[:3]:
    start = max(0, m - 50)
    end = min(len(data), m + 150)
    print(f"Sample at {m}:", data[start:end])

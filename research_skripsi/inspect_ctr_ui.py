import sys
sys.stdout.reconfigure(encoding='utf-8')

with open('resources/views/scans/show.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

import re
matches = [m.start() for m in re.finditer(r"tool\s*===?\s*'ctr'|currentTool\s*===?\s*'ctr'|'ctr'", text)]
print("Total occurrences of 'ctr':", len(matches))
for idx in matches[:10]:
    print("--- Occur at", idx)
    print(text[idx-50:idx+300])

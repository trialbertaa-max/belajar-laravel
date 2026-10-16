import urllib.request
import re
import os

base_url = "https://homepages.inf.ed.ac.uk/rbf/CVonline/LOCAL_COPIES/MANDUCHI1/"
req = urllib.request.Request(base_url + "Bilateral_Filtering.html", headers={'User-Agent': 'Mozilla/5.0'})
resp = urllib.request.urlopen(req)
html = resp.read().decode('utf-8', errors='ignore')

imgs = re.findall(r'SRC=["\']([^"\']+\.gif)["\']', html, re.I)
print("Found image files:", imgs)

# Download step-orig.gif, step-sim.gif, step-filt.gif
for img_name in ['step-orig.gif', 'step-sim.gif', 'step-filt.gif']:
    img_url = base_url + img_name
    print("Downloading", img_url)
    try:
        r = urllib.request.urlopen(urllib.request.Request(img_url, headers={'User-Agent': 'Mozilla/5.0'}))
        with open(img_name, 'wb') as f:
            f.write(r.read())
        print("Saved", img_name)
    except Exception as e:
        print("Failed to download", img_name, e)

# Also let's print the caption of Figure 1
pos = html.find("Figure 1:")
if pos != -1:
    print("\nCaption of Figure 1:\n", html[pos:pos+600])
else:
    pos2 = html.find("Figure 1")
    print("\nText around Figure 1:\n", html[pos2:pos2+600])

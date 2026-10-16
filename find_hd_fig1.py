import urllib.request
import re

url = 'https://www.ncbi.nlm.nih.gov/pmc/articles/PMC8186194/figure/Fig1/'
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
try:
    with urllib.request.urlopen(req) as resp:
        html = resp.read().decode('utf-8')
        for m in re.finditer(r'href=["\']([^"\']+\.(?:jpg|png|tif|jpeg))["\']', html, re.IGNORECASE):
            print('Found href image:', m.group(1))
        for m in re.finditer(r'src=["\']([^"\']+\.(?:jpg|png|tif|jpeg))["\']', html, re.IGNORECASE):
            print('Found src image:', m.group(1))
except Exception as e:
    print('Failed:', e)

import urllib.request
import re

url = 'https://www.ncbi.nlm.nih.gov/pmc/articles/PMC8186194/'
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
try:
    with urllib.request.urlopen(req) as resp:
        html = resp.read().decode('utf-8')
        img_srcs = re.findall(r'<img[^>]+src=["\']([^"\']+)["\']', html)
        for s in img_srcs:
            if 'Fig1' in s or 'fig1' in s.lower():
                print('Found Fig1 src:', s)
                if s.startswith('//'):
                    full_url = 'https:' + s
                elif s.startswith('/'):
                    full_url = 'https://www.ncbi.nlm.nih.gov' + s
                else:
                    full_url = s
                print('Downloading from:', full_url)
                img_data = urllib.request.urlopen(urllib.request.Request(full_url, headers={'User-Agent': 'Mozilla/5.0'})).read()
                out_path = 'd:/laragon/www/belajar-laravel/Gambar_2_1_Saiviroonporn_2021.jpg'
                with open(out_path, 'wb') as f:
                    f.write(img_data)
                print(f'Successfully downloaded {len(img_data)} bytes to {out_path}!')
                break
except Exception as e:
    print('Error:', e)

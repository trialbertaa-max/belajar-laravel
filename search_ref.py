import urllib.request, json, urllib.parse

queries = [
    'Nazir medical denoising 2024',
    'Yu task-based evaluation Medical Physics 2023',
    'van Ginneken Segmentation anatomical structures chest radiographs 2006'
]

for q in queries:
    url = f"https://api.crossref.org/works?query={urllib.parse.quote(q)}&rows=2"
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        with urllib.request.urlopen(req, timeout=10) as resp:
            data = json.loads(resp.read().decode())
            print(f"=== Query: {q} ===")
            for item in data['message']['items']:
                author = item.get('author', [{}])[0].get('family', 'Unknown')
                year = item.get('issued', {}).get('date-parts', [[0]])[0][0]
                title = item.get('title', [''])[0]
                container = item.get('container-title', [''])[0]
                doi = item.get('DOI', '')
                print(f"{author} ({year}). {title}. {container}. DOI: {doi}")
    except Exception as e:
        print(f"Error for {q}: {e}")


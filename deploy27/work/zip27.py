# Build deploy27 zip and verify every referenced asset is in the zip (or, for product images, already live on the server).
import os,re,json,zipfile,subprocess,urllib.request
L='/workspace/onyx/local/'; D='/workspace/onyx/deploy27/'; T='wp-content/themes/onyx-child/'
files=[T+'functions.php',T+'inc/shop2.php',T+'assets/css/shop2.css',T+'assets/css/onyx-fonts.css',T+'assets/css/home-v2.css',T+'assets/data/shop2-premium.json']
files+=[T+'assets/fonts/'+f for f in os.listdir(L+T+'assets/fonts')]
prem=json.load(open(L+T+'assets/data/shop2-premium.json'))
files+=['wp-content/uploads/'+r for v in prem.values() for r in v.values()]
# home-v2 (deploy26 page) custom assets – fixes the deploy26 miss (pen-bpc-tb.png etc.)
hv=open(D+'../deploy26/work/page-712-server.json').read()
refs=set(re.findall(r'euro-nutra\.com/onyxwp/(wp-content/[^"\'\s)\\,]+)',hv))
custom=[r for r in refs if '/onyx-home-v2/' in r or '/themes/' in r]
files+=custom
# woff2 referenced from onyx-fonts.css
for f in re.findall(r'url\(\.\./fonts/([^)]+)\)',open(L+T+'assets/css/onyx-fonts.css').read()): files.append(T+'assets/fonts/'+f)
files=sorted(set(files))
missing=[f for f in files if not os.path.isfile(L+f)]
assert not missing, missing
z=D+'onyx-peptides-2-deploy27-wp-content.zip'
if os.path.exists(z): os.remove(z)
with zipfile.ZipFile(z,'w',zipfile.ZIP_DEFLATED) as zf:
    for f in files: zf.write(L+f,f)
names=set(zipfile.ZipFile(z).namelist())
# referenced by page code but expected on server already: product featured images (WooCommerce) – verify live
other=[r for r in refs if r not in names]
bad=[]
for r in sorted(other):
    try: c=urllib.request.urlopen(urllib.request.Request('https://euro-nutra.com/onyxwp/'+r,method='HEAD'),timeout=20).status
    except Exception as e: c=getattr(e,'code',str(e))
    if c!=200: bad.append((r,c))
rep=f"zip: {z}\nfiles in zip: {len(names)}\nall custom refs in zip: {all(f in names for f in files)}\nhome-v2 refs not in zip (live-checked on server): {len(other)}, not 200: {bad}\n"
open(D+'work/zip-check.txt','w').write(rep+"\n".join(sorted(names))+"\n"); print(rep)

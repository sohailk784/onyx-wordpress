# deploy28 zip: cumulative (deploy27 file set + deploy28 pages2 files) because functions.php is shared. Verifies every file and every
# url()/asset referenced by pages2.css / onyx-fonts.css is inside the zip.
import os,re,zipfile
L='/workspace/onyx/local/'; D='/workspace/onyx/deploy28/'; T='wp-content/themes/onyx-child/'
base=zipfile.ZipFile('/workspace/onyx-wp-fresh/deploy27/onyx-peptides-2-deploy27-wp-content.zip').namelist()
files=sorted(set([f for f in base if not f.endswith('/')]+[T+'functions.php',T+'inc/pages2.php',T+'assets/css/pages2.css',T+'assets/css/onyx-fonts.css',T+'assets/data/shop2-premium-bg.json',T+'assets/js/home-v2.js',T+'assets/css/home-v2.css',T+'inc/shop2.php',T+'assets/css/shop2.css']+[os.path.relpath(os.path.join(r,x),L) for r,_,fs in os.walk(L+'wp-content/uploads/2026/10/onyx-home-v2/tiles') for x in fs]))
missing=[f for f in files if not os.path.isfile(L+f)]; assert not missing, missing
z=D+'onyx-pages-2-deploy28-wp-content.zip'
if os.path.exists(z): os.remove(z)
with zipfile.ZipFile(z,'w',zipfile.ZIP_DEFLATED) as zf:
    for f in files: zf.write(L+f,f)
names=set(zipfile.ZipFile(z).namelist())
refs=[]
for css in ['pages2.css','onyx-fonts.css','shop2.css']:
    for u in re.findall(r'url\(([^)]+)\)',open(L+T+'assets/css/'+css).read()):
        u=u.strip('\'"')
        if u.startswith('data:'): continue
        refs.append(os.path.normpath(T+'assets/css/'+u))
php=open(L+T+'inc/pages2.php').read()+open(L+T+'functions.php').read()
for u in re.findall(r"'/(assets/[^']+)'",php): refs.append(T+u)
for f in re.findall(r"'([a-z0-9-]+\.woff2)'",php): refs.append(T+'assets/fonts/'+f)
refs=[r for r in refs if not r.endswith('/') and not r.endswith('fonts')]
bad=[r for r in sorted(set(refs)) if r not in names and not os.path.isfile('/workspace/onyx-wp-fresh/'+r)]
onserver=[r for r in sorted(set(refs)) if r not in names and r not in bad]
req=re.findall(r"require_once get_stylesheet_directory\(\) \. '/([^']+)'",open(L+T+'functions.php').read())
badreq=[r for r in req if T+r not in names and not os.path.isfile('/workspace/onyx-wp-fresh/'+T+r)]
import json
prem=json.load(open(L+T+'assets/data/shop2-premium.json'))
imgs=['wp-content/uploads/'+r for v in prem.values() for r in v.values()]
missimg=[i for i in imgs if i not in names]
assert not missimg, missimg
keyfiles=[T+'inc/shop2.php',T+'inc/pages2.php',T+'assets/css/shop2.css',T+'assets/css/pages2.css',T+'assets/data/shop2-premium-bg.json',T+'assets/css/home-v2.css',T+'functions.php']
assert all(k in names for k in keyfiles)
rep=f"premium images referenced by shop2-premium.json: {len(imgs)}, all in zip\ndeploy27+deploy28 key files present: {keyfiles}\nzip: {z}\nfiles in zip: {len(names)}\nreferenced theme assets checked: {len(set(refs))}, missing from zip AND existing theme: {bad}\nexisting (unchanged) theme files referenced, already on server: {len(onserver)}\nfunctions.php require_once targets missing from zip AND repo theme: {badreq}\n"
print(rep); assert not bad and not badreq
open(D+'work/zip-check.txt','w').write(rep+"\n".join(sorted(names))+"\n"); print(rep)

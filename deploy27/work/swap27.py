# Peptides 2 premium images: chatgpt-out (6, reused from deploy25 tiles) + chatgpt-out2/<product_id>.png -> webp 480/800/1200
# in uploads/2026/10/onyx-shop2/. Writes theme data file assets/data/shop2-premium.json (product_id -> upload-relative files).
# Products' own featured images are never modified. Rerun any time; existing webps are reused.
import os,json,glob
from PIL import Image
L='/workspace/onyx/local/wp-content'; UP=L+'/uploads/2026/10/onyx-shop2'; os.makedirs(UP,exist_ok=True)
m={}
for pid in ['270','48','55','62','70','73']:
    m[pid]={str(w):f'2026/10/onyx-home-v2/tiles/onyx-tile-{pid}-{w}.webp' for w in (480,800,1200)}
for p in sorted(glob.glob('/workspace/onyx/chatgpt-out2/*.png')):
    pid=os.path.basename(p)[:-4]
    if not pid.isdigit(): continue
    im=None; files={}
    for w in (480,800,1200):
        fn=f'{UP}/onyx-p2-{pid}-{w}.webp'
        if not os.path.exists(fn) or os.path.getmtime(fn)<os.path.getmtime(p):
            im=im or Image.open(p).convert('RGB')
            if w>im.width: continue
            im.resize((w,round(im.height*w/im.width)),Image.LANCZOS).save(fn,'WEBP',quality=84,method=6)
        files[str(w)]=f'2026/10/onyx-shop2/onyx-p2-{pid}-{w}.webp'
    m[pid]=files
os.makedirs(L+'/themes/onyx-child/assets/data',exist_ok=True)
json.dump(m,open(L+'/themes/onyx-child/assets/data/shop2-premium.json','w'),indent=1,sort_keys=True)
print('premium products:',len(m))

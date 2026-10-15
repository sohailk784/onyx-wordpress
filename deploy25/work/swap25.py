# Premium tile images: /workspace/onyx/chatgpt-out/<id>.png -> webp sizes in uploads/2026/10/onyx-home-v2/tiles/,
# imported as new media (originals untouched), mapping written to deploy25/work/tiles25.json. Rerun build25.py after.
import os,json,subprocess
from PIL import Image
SRC='/workspace/onyx/chatgpt-out';UP='/workspace/onyx/local/wp-content/uploads/2026/10/onyx-home-v2/tiles'
URL='http://localhost:8088/wp-content/uploads/2026/10/onyx-home-v2/tiles';out={}
os.makedirs(UP,exist_ok=True)
for i in ['270','48','55','62','70','73']:
    p=f'{SRC}/{i}.png'
    if not os.path.exists(p): continue
    im=Image.open(p).convert('RGB'); W=min(im.width,1254); ss=[]
    for w in sorted({W,1200,800,480}):
        if w>im.width: continue
        fn=f'onyx-tile-{i}-{w}.webp'; im.resize((w,round(im.height*w/im.width)),Image.LANCZOS).save(f'{UP}/{fn}','WEBP',quality=86,method=6); ss.append(f'{URL}/{fn} {w}w')
    ex=subprocess.run(['wp','post','list','--post_type=attachment','--title=ONYX premium tile '+i,'--format=ids'],cwd='/workspace/onyx/local',capture_output=True,text=True).stdout.strip()
    if not ex: subprocess.run(['wp','media','import',f'{UP}/onyx-tile-{i}-{W}.webp','--title=ONYX premium tile '+i,'--skip-copy','--porcelain'],cwd='/workspace/onyx/local',capture_output=True)
    out[i]={'full':f'{URL}/onyx-tile-{i}-{W}.webp','srcset':', '.join(ss),'premium':True}
json.dump(out,open('/workspace/onyx/deploy25/work/tiles25.json','w'),indent=1); print('premium tiles:',list(out))

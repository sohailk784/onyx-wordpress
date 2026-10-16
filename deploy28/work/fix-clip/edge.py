import json,sys
from PIL import Image
import numpy as np
# flags a card if product pixels touch the media edge. bg = blue gradient; product pixels = strong deviation from row-wise bg estimate
d=json.load(open(sys.argv[1]));out=[]
for r in d:
    a=np.asarray(Image.open(r['f']).convert('RGB')).astype(int);H,W,_=a.shape
    # inset 3px to skip rounded-corner antialias; ignore 12% corners (radius)
    k=4;cy=int(H*.10);cx=int(W*.14)
    strips={'left':a[cy:H-cy,k:k+3],'right':a[cy:H-cy,W-k-3:W-k],'top':a[k:k+3,cx:W-cx],'bottom':a[H-k-3:H-k,cx:W-cx]}
    hits={}
    for s,px in strips.items():
        px=px.reshape(-1,3);med=np.median(px,axis=0)
        dev=np.abs(px-med).sum(1)
        frac=(dev>60).mean()
        if frac>0.02: hits[s]=round(float(frac),3)
    r['hits']=hits;out.append(r)
json.dump(out,open(sys.argv[1].replace('.json','-edges.json'),'w'))
bad=[r for r in out if r['hits']]
print(len(out),'cards',len(bad),'clipped')
for r in bad: print(r['pg'],r['i'],'P' if r['prem'] else 'O',r['name'],r['hits'])

#!/bin/bash
D=/workspace/onyx/deploy25; cd /workspace/onyx/tools && rm -f $D/*seg* && for w in 1440:desktop 390:mobile; do node mshot2xseg.mjs http://localhost:8088/home-v2/ $D/after-full-${w#*:}@2x.png ${w%:*}; done && cd $D && rm -f *part* && python3 -c "
from PIL import Image;import glob
for k in ['desktop','mobile']:
  segs=[Image.open(f) for f in sorted(glob.glob(f'after-full-{k}@2x-seg*.png'),key=lambda f:int(f.split('seg')[1][:-4]))]
  w=segs[0].width;h=sum(s.height for s in segs);im=Image.new('RGB',(w,h));y=0
  for s in segs: im.paste(s,(0,y));y+=s.height
  im.save(f'after-full-{k}@2x.png');step=int(w*(0.75 if k=='desktop' else 2.6));n=0
  for y in range(0,h,step):
    n+=1;im.crop((0,y,w,min(h,y+step))).save(f'after-full-{k}-part{n}@2x.png')
  print(k,h,n)" && rm -f *seg*

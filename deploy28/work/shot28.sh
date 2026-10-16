#!/bin/bash
# usage: shot28.sh URL OUTDIR PREFIX  -> PREFIX-{desktop,mobile}@2x.png + -partN
U=$1; D=$2; P=$3; mkdir -p $D; cd /workspace/onyx/tools
for w in 1440:desktop 390:mobile; do node seg2x.mjs "$U" $D/$P-${w#*:}@2x.png ${w%:*} >/dev/null; done
cd $D && rm -f $P-*-part*@2x.png && python3 - "$P" <<'P'
import sys,glob
from PIL import Image
P=sys.argv[1]
for k in ['desktop','mobile']:
  segs=[Image.open(f) for f in sorted(glob.glob(f'{P}-{k}@2x-seg*.png'),key=lambda f:int(f.split('seg')[1][:-4]))]
  w=segs[0].width;h=sum(s.height for s in segs);im=Image.new('RGB',(w,h));y=0
  for s in segs: im.paste(s,(0,y));y+=s.height
  im.save(f'{P}-{k}@2x.png');step=int(w*(0.75 if k=='desktop' else 2.6));n=0
  for y in range(0,h,step):
    n+=1;im.crop((0,y,w,min(h,y+step))).save(f'{P}-{k}-part{n}@2x.png')
  print(P,k,h,n)
P
rm -f $P-*-seg*.png

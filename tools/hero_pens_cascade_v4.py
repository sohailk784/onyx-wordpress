from PIL import Image, ImageFilter
L=1219; GAP=105; STAG=150; ANG=-38
pens=[Image.open(f'pen3-{n}.png') for n in ['hcg','sema','bpc-tb','ghk','hgh-frag']]
import numpy as np
ND=Image.open('needle.png')
def attach(p):
    a=np.asarray(p)[...,3]; h=a.shape[0]; xs=np.where(a[h-15]>128)[0]; cx=(xs.min()+xs.max())/2
    top=h-44; H2=top+ND.height; W2=max(p.width,int(cx+ND.width/2)+2)
    c=Image.new('RGBA',(W2,H2),(0,0,0,0)); c.alpha_composite(p,(0,0)); c.alpha_composite(ND,(int(round(cx-ND.width/2)),top)); return c,h
out=[]
for p in pens:
    c,h=attach(p); k=L/h; out.append(c.resize((round(c.width*k),round(c.height*k)),Image.LANCZOS))
pens=out; L2=max(p.height for p in pens)
W=sum(0 for _ in pens)+len(pens)*(140+GAP)+200; H=L2+STAG*len(pens)+200
plate=Image.new('RGBA',(W,H),(0,0,0,0))
for i,p in enumerate(pens):
    x=100+i*(140+GAP)+(140-p.width)//2; y=100+(len(pens)-1-i)*STAG   # cascade: each pen to the right sits higher
    plate.alpha_composite(p,(x,y))
R=plate.rotate(ANG,Image.BICUBIC,expand=True)
a=R.split()[3]
# soft contact shadow (two layers: tight + ambient), cast down-right
sh1=a.filter(ImageFilter.GaussianBlur(10)).point(lambda v:int(v*.28)); sh2=a.filter(ImageFilter.GaussianBlur(34)).point(lambda v:int(v*.22))
pad=120; C=Image.new('RGBA',(R.width+pad*2,R.height+pad*2),(0,0,0,0))
for sh,(dx,dy) in [(sh2,(30,46)),(sh1,(12,18))]:
    layer=Image.new('RGBA',R.size,(20,32,80,0)); layer.putalpha(sh); C.alpha_composite(layer,(pad+dx,pad+dy))
C.alpha_composite(R,(pad,pad))
C=C.crop(C.split()[3].point(lambda v:255 if v>2 else 0).getbbox())
TW,TH=1600,1240; k=min((TW-30)/C.width,(TH-30)/C.height); print('scale',k,C.size)
assert k<=1
C=C.resize((round(C.width*k),round(C.height*k)),Image.LANCZOS)
out=Image.new('RGBA',(TW,TH),(0,0,0,0)); out.alpha_composite(C,(TW-C.width-10,(TH-C.height)//2))
out.save('hero-pens-v4.png',optimize=True); out.save('hero-pens-v4.webp',quality=92,method=6)
bg=Image.new('RGBA',out.size,(255,255,255,255)); bg.alpha_composite(out); bg.convert('RGB').resize((800,620),Image.LANCZOS).save('v4prev.png')

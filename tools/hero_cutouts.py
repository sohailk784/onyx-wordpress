import numpy as np
from PIL import Image, ImageFilter
from scipy import ndimage as nd
U='/workspace/onyx/local/wp-content/uploads/2026/10/'
def cut(src,out,thr=22):
    im=Image.open(U+src).convert('RGB'); a=np.asarray(im).astype(int)
    bg=np.median(np.concatenate([a[:8].reshape(-1,3),a[-8:].reshape(-1,3)]),axis=0)
    d=np.abs(a-bg).max(2)
    m=d>thr
    m=nd.binary_closing(m,iterations=6); m=nd.binary_fill_holes(m); m=nd.binary_opening(m,iterations=3)
    lab,n=nd.label(m); sizes=nd.sum(m,lab,range(1,n+1)); keep=[i+1 for i,s in enumerate(sizes) if s>sizes.max()*0.05]
    m=np.isin(lab,keep)
    al=Image.fromarray((m*255).astype('uint8')).filter(ImageFilter.GaussianBlur(1.2))
    rgba=im.convert('RGBA'); rgba.putalpha(al); rgba=rgba.crop(al.getbbox()); rgba.save(out); print(out,rgba.size)
for s,o in [('5a5ae50eca10fb6cf7756296da0f7724.webp','box-ss31-motsc.png'),('50605ac5b85bf9028fe4ab999f2ea4b5.webp','box-glutathione.png'),('ed804679aba17ce55e348a3994aa7434.webp','box-cardiogen.png')]: cut(s,o)
# pens: from the raw product renders, white bg
for s,o in [('3e9b80848e99b120d1d4b34240544d99.png','pen-bpc-tb.png'),('48538a98efe793e71ce0f29d0161e7cc.png','pen-sema.png'),('26ec3b486b36bd89816206ff4d48d7dd.png','pen-ghk.png')]:
    im=Image.open(U+s).convert('RGB'); w,h=im.size; c=im.crop((int(w*.53),int(h*.02),int(w*.74),int(h*.86))); c.save('/tmp/_hv2pen.png')
    import shutil; Image.open('/tmp/_hv2pen.png').save(U+'_tmp_hv2pen.png'); cut('_tmp_hv2pen.png',o,thr=14)
import os; os.remove(U+'_tmp_hv2pen.png')

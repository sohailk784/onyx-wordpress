import numpy as np
from PIL import Image, ImageFilter
from scipy import ndimage as nd
U='/workspace/onyx/local/wp-content/uploads/2026/10/'
SRC=[('f37c5982b57e30b3c8e8ac3221436131.png','hcg'),('48538a98efe793e71ce0f29d0161e7cc.png','sema'),('3e9b80848e99b120d1d4b34240544d99.png','bpc-tb'),('26ec3b486b36bd89816206ff4d48d7dd.png','ghk'),('80ee66a80d2982e0d7648cdb18c3abe6.png','hgh-frag')]
def cut(src):
    im=Image.open(U+src).convert('RGB'); w,h=im.size
    im=im.crop((int(w*.47),0,w,int(h*.86)))
    a=np.asarray(im).astype(np.float32)
    bg=np.median(a[:6].reshape(-1,3),axis=0)
    d=np.abs(a-bg).max(2)
    hard=d>10
    hard=nd.binary_closing(hard,iterations=4); hard=nd.binary_fill_holes(hard)
    lab,n=nd.label(hard); s=nd.sum(hard,lab,range(1,n+1)); hard=lab==(np.argmax(s)+1)
    hard=nd.binary_fill_holes(hard)
    core=nd.binary_erosion(hard,iterations=2)
    soft=np.clip((d-3)/22,0,1)
    alpha=np.where(core,1.0,np.where(hard|nd.binary_dilation(hard,iterations=2),soft,0.0))
    alpha=nd.gaussian_filter(alpha,0.6)*np.where(nd.binary_dilation(hard,iterations=3),1,0)
    alpha=np.clip(np.maximum(alpha,core*1.0),0,1)
    # colour decontamination against the white background
    am=np.maximum(alpha,1e-3)[...,None]
    col=np.clip((a-(1-am)*bg)/am,0,255); col=np.where(alpha[...,None]>0.98,a,col)
    out=np.dstack([col,alpha*255]).astype(np.uint8)
    o=Image.fromarray(out,'RGBA'); o=o.crop(o.split()[3].point(lambda v:255 if v>4 else 0).getbbox())
    return o
for s,n in SRC:
    o=cut(s); o.save(f'pen3-{n}.png'); print(n,o.size)

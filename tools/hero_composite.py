import sys
from PIL import Image, ImageFilter, ImageChops
U='/workspace/onyx/local/wp-content/uploads/2026/10/'
W,H=1800,1400
cv=Image.new('RGB',(W,H),(255,255,255))
def box(src,scale,ang,cx,cy):
    im=Image.open(U+src).convert('RGB')
    bb=ImageChops.difference(im,Image.new('RGB',im.size,im.getpixel((3,3)))).convert('L').point(lambda v:255 if v>9 else 0).getbbox(); im=im.crop(bb)
    im=im.resize((int(im.width*scale),int(im.height*scale)),Image.LANCZOS)
    # whiten near-white background to pure white so multiply blend is clean
    px=im.point(lambda v: 255 if v>246 else v)
    m=Image.new('L',im.size,255)
    r=px.rotate(ang,Image.BICUBIC,expand=True,fillcolor=(255,255,255)); rm=m.rotate(ang,Image.BICUBIC,expand=True)
    cv.paste(r,(int(cx-r.width/2),int(cy-r.height/2)),rm)
def pen(src,scale,ang,cx,cy):
    im=Image.open(src); im=im.resize((int(im.width*scale),int(im.height*scale)),Image.LANCZOS)
    r=im.rotate(ang,Image.BICUBIC,expand=True)
    a=r.split()[3]; sh=Image.new('RGB',r.size,(150,160,185)); sa=a.filter(ImageFilter.GaussianBlur(18)).point(lambda v:int(v*.45))
    x,y=int(cx-r.width/2),int(cy-r.height/2)
    cv.paste(sh,(x+26,y+34),sa); cv.paste(r.convert('RGB'),(x,y),a)
box('5a5ae50eca10fb6cf7756296da0f7724.webp',.46,38,1330,360)
box('ed804679aba17ce55e348a3994aa7434.webp',.9,38,560,1230)
box('50605ac5b85bf9028fe4ab999f2ea4b5.webp',.46,38,1400,1040)
pen('pen-sema.png',.72,-45,500,800)
pen('pen-bpc-tb.png',.78,-45,930,600)
pen('pen-ghk.png',.72,-45,1010,1120)
cv.save('hero-composite.png',optimize=True)
cv.convert('RGB').save('hero-composite.webp',quality=90)
cv.resize((900,700)).save('chk5.png')

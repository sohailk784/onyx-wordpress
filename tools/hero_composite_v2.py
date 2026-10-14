from PIL import Image, ImageFilter, ImageChops
import sys
U='/workspace/onyx/local/wp-content/uploads/2026/10/'
ANG=float(sys.argv[1]) if len(sys.argv)>1 else 40
P=Image.new('RGB',(3000,2400),(255,255,255))
def boxim(src,w):
    im=Image.open(U+src).convert('RGB')
    bb=ImageChops.difference(im,Image.new('RGB',im.size,im.getpixel((3,3)))).convert('L').point(lambda v:255 if v>9 else 0).getbbox()
    im=im.crop(bb); s=w/im.width; assert s<=1.0,(src,s)
    return im.resize((w,int(im.height*s)),Image.LANCZOS).point(lambda v:255 if v>246 else v)
def penim(src,L):
    im=Image.open(src).rotate(-90,expand=True)  # horizontal, cap to the right
    s=L/im.width; assert s<=1.0
    return im.resize((L,int(im.height*s)),Image.LANCZOS)
def paste_pen(im,x,y):
    a=im.split()[3]; sa=a.filter(ImageFilter.GaussianBlur(16)).point(lambda v:int(v*.40))
    P.paste(Image.new('RGB',im.size,(150,160,185)),(x+10,y+30),sa); P.paste(im.convert('RGB'),(x,y),a)
BW=760; gapx=110
boxes=[boxim('5a5ae50eca10fb6cf7756296da0f7724.webp',BW),boxim('50605ac5b85bf9028fe4ab999f2ea4b5.webp',BW),boxim('ed804679aba17ce55e348a3994aa7434.webp',BW)]
pens=[penim('pen-bpc-tb.png',1000),penim('pen-sema.png',1000),penim('pen-ghk.png',1000)]
rowh=max(b.height for b in boxes)+70
for i in range(3):
    y=300+i*rowh
    p=pens[i]; paste_pen(p,300,y+(rowh-70-p.height)//2)
    P.paste(boxes[i],(300+1000+gapx,y))
R=P.rotate(ANG,Image.BICUBIC,expand=True,fillcolor=(255,255,255))
bb=ImageChops.difference(R,Image.new('RGB',R.size,(255,255,255))).convert('L').point(lambda v:255 if v>12 else 0).getbbox()
R=R.crop(bb); k=1400*1.10/R.height; R=R.resize((int(R.width*k),int(R.height*k)),Image.LANCZOS)
out=Image.new('RGB',(1800,1400),(255,255,255)); out.paste(R,(300,int((1400-R.height)/2)))
print(R.size,k)
out.save('hero-composite2.png'); out.save('hero-composite2.webp',quality=90)
out.resize((900,700)).save('y_%d.png'%int(ANG))

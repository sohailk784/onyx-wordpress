import sys
from PIL import Image
Image.MAX_IMAGE_PIXELS=None
a,b,out=sys.argv[1:4]; W=int(sys.argv[4]) if len(sys.argv)>4 else 700; maxh=int(sys.argv[5]) if len(sys.argv)>5 else 100000
A=Image.open(a).convert('RGB');B=Image.open(b).convert('RGB')
def r(i): return i.resize((W,int(i.height*W/i.width)))
A,B=r(A),r(B); h=min(max(A.height,B.height),maxh)
o=Image.new('RGB',(W*2+10,h),'red');o.paste(A,(0,0));o.paste(B,(W+10,0));o.save(out)

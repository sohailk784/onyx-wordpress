import sys
from PIL import Image
Image.MAX_IMAGE_PIXELS=None
n,y0,y1,out=sys.argv[1],int(sys.argv[2]),int(sys.argv[3]),sys.argv[4]
A=Image.open(f'cmp/{n}-390-live.png').convert('RGB').crop((0,y0,390,y1));B=Image.open(f'cmp/{n}-390-local.png').convert('RGB').crop((0,y0,390,y1))
o=Image.new('RGB',(790,y1-y0),'red');o.paste(A,(0,0));o.paste(B,(400,0));o.save(out)

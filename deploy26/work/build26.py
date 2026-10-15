import json,re
W25='/workspace/onyx/deploy25/work/';W='/workspace/onyx/deploy26/work/'
LOGO={'nature.svg':(106,26),'nih.svg':(45,28),'pubchem.svg':(116,34),'sciencedaily.png':(144,24),'sciencedirect.svg':(151,24),'pubmed.svg':(90,32)}
def fix_img(t):
    src=re.search(r'src="([^"]+)"',t).group(1); fn=src.rsplit('/',1)[1]
    t=re.sub(r'\s(width|height)="[^"]*"','',t)
    if fn in LOGO: wh=LOGO[fn]
    elif 'onyx-tile-' in fn: wh=(1254,1254)
    elif fn=='pen-bpc-tb.png': wh=(132,1262)
    else: return t
    t=t.replace('<img ',f'<img width="{wh[0]}" height="{wh[1]}" ',1)
    if 'loading=' not in t: t=t.replace('<img ','<img loading="lazy" ',1)
    if 'decoding=' not in t: t=t.replace('<img ','<img decoding="async" ',1)
    return t
def build(src,out):
    d=json.load(open(src))
    for s in d[1:]:   # everything below the hero
        def walk(e):
            st=e['settings']
            if 'html' in st: st['html']=re.sub(r'<img\b[^>]*>',lambda m:fix_img(m.group(0)),st['html'])
            for c in e['elements']: walk(c)
        walk(s)
    json.dump(d,open(out,'w'),ensure_ascii=False,separators=(',',':'))
build(W25+'base-local.json',W+'base-local.json'); build(W25+'page-712-server.json',W+'page-712-server.json')

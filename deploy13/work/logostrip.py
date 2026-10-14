import json,sys,random
BASE=sys.argv[1]; U=BASE+'/wp-content/uploads/2026/10/onyx-home-v2/logos/'
def rid(): return ''.join(random.choice('0123456789abcdef') for _ in range(7))
random.seed(13013)
L=[('nature.svg','Nature',None),('nih.svg','National Institutes of Health',('National Institutes of Health','Turning Discovery Into Health')),
 ('pubchem.svg','PubChem',None),('nih.svg','National Library of Medicine',('National Library of Medicine','National Center for Biotechnology Information')),
 ('sciencedaily.png','ScienceDaily',None),('sciencedirect.svg','ScienceDirect',None),('pubmed.svg','PubMed',None)]
def tile(f,alt,lab,hidden):
    a='' if not hidden else ''
    img=f'<img src="{U}{f}" alt="{"" if hidden else alt}" loading="lazy" decoding="async" height="34">'
    if lab: return f'<li class="hv2-lg-tile hv2-lg-duo hv2-lg-{f[:-4]}">{img.replace(" height=\"34\"","")}<span><b>{lab[0]}</b><small>{lab[1]}</small></span></li>'
    return f'<li class="hv2-lg-tile hv2-lg-{f[:-4]}">{img}</li>'
def S(h): return ''.join(tile(f,a,l,h) for f,a,l in L)
html=('<div class="hv2-lg"><p class="hv2-lg-cap">Every new formula begins with data derived from reliable scientific sources.</p>'
 '<div class="hv2-lg-vp" role="region" aria-label="Scientific sources"><div class="hv2-lg-track">'
 f'<ul class="hv2-lg-set">{S(False)}</ul><ul class="hv2-lg-set" aria-hidden="true">{S(True)}</ul></div></div></div>')
pad={'unit':'px','top':'0','right':'0','bottom':'0','left':'0','isLinked':True}
sec={'id':'a7c3e01','elType':'container','isInner':False,'settings':{'content_width':'full','flex_direction':'column','_css_classes':'hv2-sec hv2-logos','css_classes':'hv2-sec hv2-logos','padding':pad,'flex_gap':{'unit':'px','size':0,'column':'0','row':'0','isLinked':True}},
 'elements':[{'id':'a7c3e02','elType':'widget','widgetType':'html','settings':{'_css_classes':'hv2-logos-w','html':html},'elements':[]}]}
json.dump(sec,open(sys.argv[2],'w'))

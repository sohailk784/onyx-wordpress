import os
W = os.environ.get('ONYX_WORK', 'build-work')
import json,csv,glob,html,os
L=W + '/live/'
cats={c['slug']:c['name'] for c in json.load(open(L+'cats.json'))}
cats['programs']='Peptide Programs'
plist=json.load(open(L+'products.json'))
plans={os.path.basename(f)[:-5]:json.load(open(f)) for f in glob.glob(L+'plans/*.json')}
E_=html.escape
def lst(items): return '<ul>'+''.join(f'<li>{E_(i)}</li>' for i in items)+'</ul>'
def cards(title,items):
  if not items: return ''
  return f'<h3>{title}</h3><div class="onyx-cards">'+''.join(f'<div class="onyx-card"><h4>{E_(i.get("title",""))}</h4><p>{E_(i.get("body",""))}</p></div>' for i in items)+'</div>'
def desc_for(p):
  d=(json.load(open(L+f'detail/{p["slug"]}.json')).get('detail') or {})
  h=''
  if d.get('summary'): h+=f'<h3>Overview</h3><p>{E_(d["summary"])}</p>'
  if d.get('profile'): h+='<h3>Compound profile</h3><table class="onyx-profile">'+''.join(f'<tr><th>{E_(r["label"])}</th><td>{E_(r["value"])}</td></tr>' for r in d['profile'])+'</table>'
  if d.get('features'): h+='<h3>Key features</h3>'+lst(d['features'])
  h+=cards('Research benefits',d.get('benefits'))
  h+=cards('Research',d.get('research'))
  h+=cards('Research information',d.get('info'))
  if d.get('steps'): h+='<h3>Steps</h3><ol>'+''.join(f'<li>{E_(s if isinstance(s,str) else (s.get("title","")+": "+s.get("body","")))}</li>' for s in d['steps'])+'</ol>'
  for k in ('dosage','sideEffects','conclusion','note'):
    v=d.get(k)
    if v: h+=f'<h3>{ {"dosage":"Dosage","sideEffects":"Side effects","conclusion":"Conclusion","note":"Note"}[k]}</h3><p>{E_(v if isinstance(v,str) else json.dumps(v))}</p>'
  if d.get('faqs'): h+='<h3>FAQ</h3>'+''.join(f'<details class="onyx-faq"><summary>{E_(f["q"])}</summary><p>{E_(f["a"])}</p></details>' for f in d['faqs'])
  h+='<p class="onyx-ruo"><strong>Research use only.</strong> Supplied exclusively for laboratory and scientific research. Not for human or veterinary use.</p>'
  return h
def plan_desc(pl):
  h=f'<p class="lead">{E_(pl.get("intro") or "")}</p><h3>Overview</h3><p>{E_(pl.get("overview") or "")}</p>'
  if pl.get('cycleLength'): h+=f'<p><strong>Cycle length:</strong> {E_(pl["cycleLength"])}</p>'
  if pl.get('howItHelps'): h+='<h3>How it helps</h3>'+lst(pl['howItHelps'])
  if pl.get('included'): h+='<h3>Included peptides</h3><div class="onyx-cards">'+''.join(f'<div class="onyx-card"><h4><a href="/product/{i["slug"]}/">{E_(i["name"])}</a></h4><p>{E_(i["role"])}</p></div>' for i in pl['included'])+'</div>'
  ds=pl.get('dosing') or {}
  if ds:
    h+=f'<h3>Dosing protocol</h3><p>{E_(ds.get("overview",""))}</p>'
    if ds.get('perPeptide'): h+='<table class="onyx-profile">'+''.join(f'<tr><th>{E_(x["name"])}</th><td>{E_(x["schedule"])}</td></tr>' for x in ds['perPeptide'])+'</table>'
    if ds.get('penCounts'): h+='<h4>Pens required</h4><table class="onyx-profile">'+''.join(f'<tr><th>{E_(x["cycle"])}</th><td>{E_(x["pens"])}</td></tr>' for x in ds['penCounts'])+'</table>'
  if pl.get('conclusion'): h+=f'<h3>Conclusion</h3><p>{E_(pl["conclusion"])}</p>'
  return h
H=['ID','Type','SKU','Name','Published','Is featured?','Visibility in catalog','Short description','Description','Tax status','In stock?','Stock','Backorders allowed?','Weight (kg)','Regular price','Sale price','Categories','Tags','Images','Parent','Upsells','Position','Attribute 1 name','Attribute 1 value(s)','Attribute 1 visible','Attribute 1 global','Attribute 2 name','Attribute 2 value(s)','Attribute 2 visible','Attribute 2 global','Meta: _onyx_slug','Meta: _onyx_type','Meta: _onyx_badges','Meta: _onyx_linked']
rows=[]
slugmap={}
for i,p in enumerate(plist):
  vs=p.get('variants') or []
  sku=f'ONYX-{p["id"]}'
  slugmap[p['slug'].strip()]=sku
  cat=cats.get(p['category'] or '', 'Essentials')
  if p['category']=='plans': cat='Essentials'
  desc= plan_desc(plans[p['slug']]) if p['slug'] in plans else desc_for(p)
  instock = 0 if (p.get('outOfStock') or p.get('comingSoon')) else 1
  tags=','.join(b.title() for b in (p.get('badges') or []))+ (',Coming Soon' if p.get('comingSoon') else '')
  imgs=', '.join(p.get('images') or ([p['image']] if p.get('image') else []))
  base={k:'' for k in H}
  base.update({'Type':'simple','SKU':sku,'Name':p['name'].strip(),'Published':1,'Is featured?':1 if (p.get('isFeatured') or 'bestseller' in (p.get('badges') or [])) else 0,'Visibility in catalog':'visible','Short description':E_(p.get('description') or ''),'Description':desc,'Tax status':'taxable','In stock?':instock,'Weight (kg)':(p.get('weightGrams') or 50)/1000 if 'weightGrams' in p else '','Categories':cat,'Tags':tags.strip(','),'Images':imgs,'Position':i,'Meta: _onyx_slug':p['slug'].strip(),'Meta: _onyx_type':p.get('type') or '','Meta: _onyx_badges':','.join(p.get('badges') or []),'Meta: _onyx_linked':(p.get('linkedProduct') or {}).get('slug','')})
  if p.get('type'): base.update({'Attribute 2 name':'Format','Attribute 2 value(s)':{'pen':'Dual Chamber Pen','vial':'Vial'}.get(p['type'],p['type']),'Attribute 2 visible':1,'Attribute 2 global':1})
  if len(vs)<=1:
    base['Regular price']= vs[0]['price'] if vs else p['price']
    if vs: base.update({'Attribute 1 name':'Strength','Attribute 1 value(s)':vs[0]['label'],'Attribute 1 visible':1,'Attribute 1 global':1})
    if p.get('originalPrice'): base['Regular price'],base['Sale price']=p['originalPrice'],base['Regular price']
    rows.append(base)
  else:
    base.update({'Type':'variable','Attribute 1 name':'Strength','Attribute 1 value(s)':', '.join(v['label'] for v in vs),'Attribute 1 visible':1,'Attribute 1 global':1})
    rows.append(base)
    for j,v in enumerate(vs):
      r={k:'' for k in H}
      r.update({'Type':'variation','SKU':f'ONYX-V{v["id"]}','Name':f'{p["name"].strip()} - {v["label"]}','Published':1,'Tax status':'taxable','In stock?':0 if (v.get('outOfStock') or v.get('comingSoon')) else 1,'Regular price':v['price'],'Parent':sku,'Position':j,'Attribute 1 name':'Strength','Attribute 1 value(s)':v['label'],'Attribute 1 global':1,'Stock':v['stockQty'] if v.get('stockQty') is not None else ''})
      rows.append(r)
# upsells for plans
for r in rows:
  s=r['Meta: _onyx_slug']
  if s in plans: r['Upsells']=','.join(slugmap[i['slug']] for i in plans[s].get('included',[]) if i['slug'] in slugmap)
  if r['Meta: _onyx_linked'] in slugmap: r['Upsells']=slugmap[r['Meta: _onyx_linked']]
with open(W + '/import/onyx-products-woocommerce.csv','w',newline='') as f:
  w=csv.DictWriter(f,H); w.writeheader(); w.writerows(rows)
print(len(plist),'products',len(rows),'rows')
json.dump(slugmap,open(W + '/import/slugmap.json','w'))

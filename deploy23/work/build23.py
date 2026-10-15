import json,sys
cats=json.load(open('/workspace/onyx/homev2/cats23.json'))
def build(src,base,out):
    d=json.load(open(src))
    keep={s['settings']['css_classes'].split()[1]:s for s in d}
    L=lambda u:u.replace('http://localhost:8088',base)
    def html(i,h,cls=''): return {'id':i,'elType':'widget','widgetType':'html','isInner':False,'settings':{'html':h,'_css_classes':cls},'elements':[]}
    def sec(i,cls,els): return {'id':i,'elType':'container','isInner':False,'settings':{'css_classes':'hv2-sec '+cls,'_css_classes':'hv2-sec '+cls,'content_width':'full','padding':{'unit':'px','top':'0','right':'0','bottom':'0','left':'0','isLinked':False}},'elements':els}
    tiles=''.join(f'<a class="hv2-cat-tile" href="{L(c["link"])}"><span class="hv2-cat-img"><img src="{L(c["img"])}" alt="{c["pname"]}" loading="lazy"></span><span class="hv2-cat-name">{c["name"]} <i aria-hidden="true">&rarr;</i></span></a>' for c in cats)
    cat=sec('d23c001','hv2-cats',[html('d23c002',f'<div class="hv2-wrap"><div class="hv2-c-head"><p class="hv2-eyebrow">Browse the range</p><h2>Shop by category</h2></div><div class="hv2-cat-grid">{tiles}</div></div>')])
    steps=[('Two separate chambers','The freeze-dried peptide and the diluent are stored apart in one pen, so they stay separate until reconstitution.'),
           ('Integrated mixing','A built-in mechanism combines the two components inside the pen when you are ready.'),
           ('No separate mixing vials','Because everything is in one device, there is no need for separate mixing vials.')]
    acc=''.join(f'<details class="hv2-acc"{" open" if i==0 else ""}><summary><span>{t}</span><i aria-hidden="true"></i></summary><p>{b}</p></details>' for i,(t,b) in enumerate(steps))
    pen=sec('d23p001','hv2-penhow',[html('d23p002',f'<div class="hv2-wrap hv2-ph-grid"><div class="hv2-ph-text"><p class="hv2-eyebrow">Dual chamber pens</p><h2>How the self-mixing pen works</h2>{acc}<div class="hv2-pills"><span>Third-party tested</span><span>COA per batch</span><span>Batch authenticity codes</span></div><a class="hv2-pill-btn" href="{base}/peptides/dual-chamber-peptide-pens/">Shop pens</a></div><div class="hv2-ph-media"><img src="{base}/wp-content/uploads/2026/10/onyx-home-v2/pen-bpc-tb.png" alt="BPC + TB dual chamber pen" loading="lazy"></div></div>')])
    grid=sec('d23g001','hv2-wlgrid',[html('d23g002','<div class="hv2-wrap"><div class="hv2-c-head"><p class="hv2-eyebrow">Collection</p><h2>Weight loss peptides</h2></div></div>'),
        {'id':'d23g003','elType':'widget','widgetType':'shortcode','isInner':False,'settings':{'shortcode':'[products category="weight-loss" limit="8" columns="4"]','_css_classes':'hv2-wrap hv2-products hv2-wl-products'},'elements':[]},
        html('d23g004',f'<div class="hv2-wrap hv2-center"><a class="hv2-pill-btn" href="{base}/peptides/weight-loss/">View all</a></div>')])
    ok='<span class="hv2-yes" aria-label="Yes">&#10003;</span>'; no='<span class="hv2-no" aria-label="No">&#10005;</span>'
    rows=[('Peptide and diluent in one device',ok,no),('Built-in mixing mechanism',ok,no),('No separate mixing vial needed',ok,no)]
    tr=''.join(f'<tr><th scope="row">{a}</th><td>{b}</td><td>{c}</td></tr>' for a,b,c in rows)
    cmp_=sec('d23k001','hv2-compare',[html('d23k002',f'<div class="hv2-wrap"><div class="hv2-c-head"><p class="hv2-eyebrow">Pen or vial</p><h2>Dual chamber pen vs standard vial</h2></div><table class="hv2-cmp"><thead><tr><th></th><th>Dual chamber pen</th><th>Standard vial</th></tr></thead><tbody>{tr}</tbody></table><p class="hv2-cmp-note">Both formats are available in the ONYX range.</p></div>')])
    new=[keep[k] for k in ['hv2-hero2','hv2-logos','hv2-tr','hv2-lab2','hv2-best']]+[cat,pen,grid,cmp_,keep['hv2-faqsec'],keep['hv2-newssec']]
    json.dump(new,open(out,'w'),ensure_ascii=False,separators=(',',':'))
W='/workspace/onyx/deploy23/work/'
build('/workspace/onyx/deploy22/work/base-local.json','http://localhost:8088',W+'base-local.json')
build('/workspace/onyx/deploy22/work/page-712-server.json','https://euro-nutra.com/onyxwp',W+'page-712-server.json')

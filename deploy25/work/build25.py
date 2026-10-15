import json,re,os
W24='/workspace/onyx/deploy24/work/';W='/workspace/onyx/deploy25/work/'
img=json.load(open('/workspace/onyx/homev2/img24.json'))
if os.path.exists(W+'tiles25.json'): img.update(json.load(open(W+'tiles25.json')))  # premium images override
cats=json.load(open('/workspace/onyx/homev2/cats23.json'))
ids=['270','48','55','70','73','62']
ARROW='<span class="hv2-cat-go" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>'
def build(src,out,base):
    d=json.load(open(src)); L=lambda u:u.replace('http://localhost:8088',base)
    for s in d:
        c=s['settings']['css_classes']
        if 'hv2-hero2' in c:
            for w in s['elements'][0]['elements']:
                if w.get('widgetType')=='heading': w['settings']['title']='Research-grade peptides, <br><em>verified batch by batch</em>'
            a=s['elements'][1]['settings']
            if 'hv2-art-badge' not in a['html']:
                a['html']=a['html'].replace('</picture></div>','</picture><span class="hv2-art-orb" aria-hidden="true"></span><span class="hv2-art-badge"><i></i>For laboratory research only</span></div>')
        w=s['elements'][0]; h=w['settings'].get('html','')
        if 'hv2-cats' in c:
            js=h[h.find('<script>'):]
            tiles=''
            for cat,i in zip(cats,ids):
                im=img[i]; ss=f' srcset="{L(im["srcset"])}" sizes="(max-width:767px) 92vw, 400px"' if im.get('srcset') else ''
                tiles+=f'<a class="hv2-cat-tile{" hv2-cat-premium" if im.get("premium") else ""}" href="{L(cat["link"])}"><span class="hv2-cat-img"><img src="{L(im["full"])}"{ss} alt="{cat["pname"].replace("  "," ")}" loading="lazy"{"" if ss else " class=\"hv2-cat-cutout\""}></span><span class="hv2-cat-name"><span>{cat["name"]}</span>{ARROW}</span></a>'
            h=f'<div class="hv2-wrap"><div class="hv2-c-head hv2-c-left"><p class="hv2-eyebrow">Browse the range</p><h2>Shop by category</h2></div><div class="hv2-cat-grid">{tiles}</div></div>'+js
        if 'hv2-penhow' in c:
            steps=re.findall(r'<summary><span>(.*?)</span>.*?<p>(.*?)</p>',h)
            num=['i.','ii.','iii.']
            st=''.join(f'<div class="hv2-pstep"><em class="hv2-pstep-n">{n}</em><h3>{t}</h3><p>{b}</p></div>' for n,(t,b) in zip(num,steps))
            pills=re.search(r'<div class="hv2-pills">.*?</div>',h).group(0); btn=re.search(r'<a class="hv2-pill-btn".*?</a>',h).group(0)
            im=re.search(r'<img [^>]*>',h).group(0)
            h=f'<div class="hv2-wrap"><div class="hv2-ph-head"><div><p class="hv2-eyebrow">Dual chamber pens</p><h2>How the <em>self-mixing</em> pen works</h2></div><div class="hv2-ph-side">{pills}{btn}</div></div><div class="hv2-ph-body"><div class="hv2-ph-media">{im}</div><div class="hv2-psteps">{st}</div></div></div>'
        if 'hv2-wlgrid' in c:
            h=h.replace('<div class="hv2-c-head"><p','<div class="hv2-c-head"><div><p',1).replace('</h2></div>',f'</h2></div><a class="hv2-ulink" href="{base}/peptides/weight-loss/">View all</a></div>',1).replace('hv2-c-head"','hv2-c-head hv2-c-left hv2-c-split"',1)
            s['elements']=[e for e in s['elements'] if e['id']!='d23g004']
        if 'hv2-compare' in c:
            h=h.replace('<div class="hv2-wrap"><div class="hv2-c-head">','<div class="hv2-wrap hv2-cmp-grid"><div class="hv2-c-head hv2-c-left">').replace('<table','<p class="hv2-cmp-note">Both formats are available in the ONYX range.</p></div><div class="hv2-cmp-wrap"><table',1)
            h=h.replace('<thead><tr><th></th><th>Dual chamber pen</th>','<thead><tr><th></th><th><span class="hv2-cmp-hl">Dual chamber pen</span></th>')
            h=re.sub(r'</table><p class="hv2-cmp-note">.*?</p></div>$','</table></div></div>',h)
            h=h.replace('</h2></div><p class="hv2-cmp-note">','</h2><p class="hv2-cmp-note">',1)
        if h: w['settings']['html']=h
    json.dump(d,open(out,'w'),ensure_ascii=False,separators=(',',':'))
build(W24+'base-local.json',W+'base-local.json','http://localhost:8088')
build(W24+'page-712-server.json',W+'page-712-server.json','https://euro-nutra.com/onyxwp')

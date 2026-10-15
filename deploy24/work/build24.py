import json,re
img=json.load(open('/workspace/onyx/homev2/img24.json'))
cats=json.load(open('/workspace/onyx/homev2/cats23.json'))
pid={c['slug']:p for c,p in zip(cats,['270','48','55','70','73','62'])}
JS=('<script>(function(){var d=document.documentElement;if(!("IntersectionObserver" in window)||matchMedia("(prefers-reduced-motion: reduce)").matches)return;'
 'd.classList.add("hv2-anim");var io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add("hv2-in");io.unobserve(e.target);}});},{rootMargin:"0px 0px -8% 0px"});'
 'function go(){document.querySelectorAll(".hv2-sec").forEach(function(s,i){if(i>0)io.observe(s);else s.classList.add("hv2-in");});}'
 'document.readyState!=="loading"?go():document.addEventListener("DOMContentLoaded",go);})();</script>')
def build(src,out,base):
    d=json.load(open(src)); L=lambda u:u.replace('http://localhost:8088',base)
    for s in d:
        cls=s['settings']['css_classes']
        for w in s['elements']:
            if w.get('widgetType')!='html': continue
            h=w['settings']['html']
            if 'hv2-cats' in cls:
                for c in cats:
                    i=img[pid[c['slug']]]
                    h=h.replace(f'src="{L(c["img"])}"',(f'src="{L(i["full"])}" srcset="{L(i["srcset"])}" sizes="(max-width:767px) 46vw, 380px" width="1024" height="1024"' if i["srcset"] else f'src="{L(i["full"])}" class="hv2-cat-cutout"'))
                if 'hv2-anim' not in h: h+=JS
            w['settings']['html']=h
    json.dump(d,open(out,'w'),ensure_ascii=False,separators=(',',':'))
W='/workspace/onyx/deploy2%s/work/'
build(W%3+'base-local.json',W%4+'base-local.json','http://localhost:8088')
build(W%3+'page-712-server.json',W%4+'page-712-server.json','https://euro-nutra.com/onyxwp')

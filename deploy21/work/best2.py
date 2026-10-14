import json,sys,copy
src,out=sys.argv[1],sys.argv[2]
d=json.load(open(src)); b=copy.deepcopy([x for x in d if x['settings'].get('css_classes')=='hv2-sec hv2-best'][0])
b['settings']['css_classes']=b['settings']['_css_classes']='hv2-sec hv2-best hv2-best2'
wrap=b['elements'][0]; kids=wrap['elements']
i=[k for k,e in enumerate(kids) if e.get('widgetType')=='shortcode'][0]
pager='''<nav class="hv2-b2-pager" aria-label="Bestsellers carousel"><button type="button" class="hv2-b2-prev" aria-label="Previous products"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></button><span class="hv2-b2-count" aria-live="polite">1/1</span><button type="button" class="hv2-b2-next" aria-label="Next products"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button></nav>
<script>(function(){var s=document.currentScript,sec=s&&s.closest('.hv2-best2');if(!sec)return;var ul=sec.querySelector('.hv2-products ul.products');if(!ul)return;var nav=sec.querySelector('.hv2-b2-pager'),c=nav.querySelector('.hv2-b2-count'),pv=nav.querySelector('.hv2-b2-prev'),nx=nav.querySelector('.hv2-b2-next');
function step(){var li=ul.querySelector('li.product');if(!li)return ul.clientWidth;var g=parseFloat(getComputedStyle(ul).columnGap)||0;var per=Math.max(1,Math.floor((ul.clientWidth+g)/(li.offsetWidth+g)));return per*(li.offsetWidth+g);}
function upd(){var st=step(),max=ul.scrollWidth-ul.clientWidth,n=max>2?Math.ceil(max/st)+1:1,p=max>2?Math.min(n,Math.round(ul.scrollLeft/st)+1):1;if(ul.scrollLeft>=max-2)p=n;c.textContent=p+'/'+n;pv.disabled=ul.scrollLeft<=2;nx.disabled=ul.scrollLeft>=max-2;nav.hidden=n<2;}
pv.addEventListener('click',function(){ul.scrollBy({left:-step(),behavior:'smooth'});});nx.addEventListener('click',function(){ul.scrollBy({left:step(),behavior:'smooth'});});
ul.addEventListener('scroll',function(){window.requestAnimationFrame(upd);},{passive:true});window.addEventListener('resize',upd);upd();})();</script>'''
kids.insert(i+1,{'id':'d5b2a01','elType':'widget','widgetType':'html','settings':{'_css_classes':'hv2-b2-pagerw','html':pager},'elements':[]})
# drop the 'Research use only' note from this section (research-use line removed per user)
wrap['elements']=[e for e in wrap['elements'] if 'research use only' not in json.dumps(e).lower()]
json.dump(b,open(out,'w'))

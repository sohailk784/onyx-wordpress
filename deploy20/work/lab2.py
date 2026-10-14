import json,sys
BASE=sys.argv[1]
flask='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 2h6M10 2v6.5L5.2 18.6A2.3 2.3 0 0 0 7.3 22h9.4a2.3 2.3 0 0 0 2.1-3.4L14 8.5V2"/><path d="M7.5 15h9"/></svg>'
html=f'''<div class="hv2-l2">
<div class="hv2-l2-grid">
<div class="hv2-l2-vid"><iframe src="https://www.youtube-nocookie.com/embed/W1-7GmGogEg?rel=0&amp;modestbranding=1" title="Inside the ONYX laboratory (video)" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>
<div class="hv2-l2-copy">
<p class="hv2-l2-eyebrow">Inside the ONYX laboratory</p>
<h2 class="hv2-l2-h">The science and process behind every ONYX&nbsp;product.</h2>
<p class="hv2-l2-p">Complete transparency into the technologies, processes, and controls behind every product we create.</p>
<div class="hv2-l2-item"><span class="hv2-l2-ic">{flask}</span><span><strong>Analytical control</strong><small>Verification of key parameters through HPLC and LC-MS analysis.</small></span></div>
<a class="hv2-l2-btn" href="{BASE}/peptides/">View Research Materials <span aria-hidden="true">&rarr;</span></a>
</div></div>
<ul class="hv2-l2-chips"><li>High-tech equipment</li><li>Controlled production</li><li>Strict quality control</li></ul>
<p class="hv2-l2-note">For laboratory standards and research purposes only.</p>
</div>'''
pad={'unit':'px','top':'0','right':'0','bottom':'0','left':'0','isLinked':True}
cls='hv2-sec hv2-lab2'
sec={'id':'c4d2a01','elType':'container','isInner':False,'settings':{'content_width':'full','flex_direction':'column','_css_classes':cls,'css_classes':cls,'padding':pad,'flex_gap':{'unit':'px','size':0,'column':'0','row':'0','isLinked':True}},
'elements':[{'id':'c4d2a02','elType':'widget','widgetType':'html','settings':{'_css_classes':'hv2-lab2-w','html':html},'elements':[]}]}
json.dump(sec,open(sys.argv[2],'w'))

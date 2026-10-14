import json,random,string,sys
BASE=sys.argv[1] if len(sys.argv)>1 else 'http://localhost:8088'
U=BASE+'/wp-content/uploads/2026/10/'
def rid(): return ''.join(random.choice('0123456789abcdef') for _ in range(7))
def C(cls,kids,direction='column',inner=True,**s):
    st={'content_width':'full','flex_direction':direction,'_css_classes':cls,'css_classes':cls,
        'padding':{'unit':'px','top':'0','right':'0','bottom':'0','left':'0','isLinked':True},
        'flex_gap':{'unit':'px','size':0,'column':'0','row':'0','isLinked':True}}
    st.update(s); return {'id':rid(),'elType':'container','isInner':inner,'settings':st,'elements':kids}
def W(t,cls='',**s):
    s['_css_classes']=cls; return {'id':rid(),'elType':'widget','widgetType':t,'settings':s,'elements':[]}
H=lambda t,tag='h2',cls='': W('heading',cls,title=t,header_size=tag)
T=lambda html,cls='': W('text-editor',cls,editor=html)
B=lambda t,url,cls='hv2-btn': W('button',cls,text=t,link={'url':BASE+url,'is_external':'','nofollow':''})
IMG=lambda url,id_,cls='',alt='': W('image',cls,image={'url':url,'id':id_,'alt':alt,'source':'library'},image_size='full')
EB=lambda t,cls='': H(t,'p','hv2-eyebrow '+cls)
def ICONB(icon,title,desc): return W('icon-box','hv2-tb',selected_icon={'value':icon,'library':'fa-solid'},title_text=title,description_text=desc,position='left',title_size='h3')
def LIST(items,cls): return W('icon-list',cls,icon_list=[{'_id':rid(),'text':i,'selected_icon':{'value':'fas fa-check','library':'fa-solid'}} for i in items])
def SEC(cls,kids,**s): return C('hv2-sec '+cls,[C('hv2-wrap',kids)],inner=False,**s)

hero=SEC('hv2-hero',[C('hv2-hero-grid',[
  C('hv2-hero-copy',[
    T('<span class="hv2-pill"><i></i>Every batch HPLC &amp; LC-MS verified</span>'),
    H('Research-grade peptides.<br><span>Proven, batch by batch.</span>','h1','hv2-h1'),
    T('<p>Independently tested for identity and purity, with a certificate of analysis for every batch. Supplied exclusively for laboratory research.</p>','hv2-hero-p'),
    C('hv2-ctas',[B('Shop research peptides →','/peptides/','hv2-btn'),B('Verify a batch code','/check-code/','hv2-btn hv2-btn-ghost')],'row'),
    LIST(['≥99% reported purity','COA per batch','Made in the U.K. &amp; EU'],'hv2-checks')]),
  C('hv2-stage',[
    IMG(U+'3e9b80848e99b120d1d4b34240544d99.png',269,'hv2-stage-img','BPC + TB Dual Chamber Pen'),
    W('html','hv2-floats',html=f'<a class="hv2-float hv2-f1" href="{BASE}/product/bpctbdualpen/"><b>BPC + TB | Dual Chamber Pen</b><small>Bestseller · from £116.00</small></a><div class="hv2-float hv2-f2"><b>Batch report <span class="hv2-ex">EXAMPLE</span></b><small>HPLC purity profile</small><div class="hv2-mini"><i style="--h:20%"></i><i style="--h:35%"></i><i style="--h:95%"></i><i style="--h:30%"></i><i style="--h:12%"></i></div></div>')])],'row')])

trust=SEC('hv2-trust',[C('hv2-trust-grid',[
  ICONB('fas fa-bolt','Fast EU dispatch','Shipped within 24 hours'),
  ICONB('fas fa-check','Verified purity','Third-party HPLC &amp; LC-MS'),
  ICONB('fas fa-hashtag','Authenticity codes','Check every batch online'),
  ICONB('fas fa-file-alt','COA available','Per product line &amp; batch'),
  ICONB('fas fa-industry','U.K. &amp; EU manufacturing','Certified facilities')],'row')])

lab=SEC('hv2-lab',[C('hv2-split hv2-split-lab',[
  C('hv2-vid',[W('video','',video_type='youtube',youtube_url='https://www.youtube.com/watch?v=W1-7GmGogEg',mute='yes',rel='',autoplay='',controls='yes')]),
  C('hv2-lab-copy',[EB('Inside the ONYX laboratory'),H('The science and process behind every <span>ONYX</span> product.'),
    T('<p>Complete transparency into the technologies, processes, and controls behind every product we create.</p>','hv2-lead'),
    W('icon-box','hv2-ac',selected_icon={'value':'fas fa-clipboard-check','library':'fa-solid'},title_text='Analytical control',description_text='Verification of key parameters through HPLC and LC-MS analysis.',position='left',title_size='h3'),
    B('View Research Materials →','/peptides/'),
    LIST(['High-tech equipment','Controlled production','Strict quality control'],'hv2-chips')])],'row')])

best=SEC('hv2-best',[
  C('hv2-head',[C('',[EB('Most ordered'),H('Bestsellers')]),B('View all peptides →','/peptides/','hv2-btn hv2-btn-o')],'row'),
  W('shortcode','hv2-products',shortcode='[products ids="270,272,268,255" columns="4" orderby="post__in"]'),
  T('<p><b>Research use only.</b> All ONYX materials are supplied exclusively for legitimate laboratory and scientific research. Not for human or veterinary use, diagnosis, treatment or therapeutic application.</p>','hv2-ruo')])

coa_html='''<div class="hv2-coa"><div class="hv2-coa-hd"><div><b>Certificate of Analysis</b><br><small>BPC · Batch ONX-XXXX</small></div><span class="hv2-ex">EXAMPLE LAYOUT</span></div>
<div class="hv2-chrom"><svg viewBox="0 0 400 110" width="100%" height="110" preserveAspectRatio="none"><polyline fill="none" stroke="#2f7bf5" stroke-width="2" points="0,100 60,99 90,97 120,99 160,98 185,90 195,12 205,90 230,98 280,96 300,99 400,100"/></svg></div>
<table><tr><td>Method</td><td>HPLC / LC-MS</td></tr><tr><td>Identity</td><td>Conforms</td></tr><tr><td>Purity</td><td>≥ 99% (reported)</td></tr><tr><td>Tested by</td><td>Independent third-party lab</td></tr></table></div>'''
def STEP(n,t,d): return C('hv2-step',[H(n,'p','hv2-step-n'),H(t,'h3'),T(f'<p>{d}</p>')])
testing=SEC('hv2-testing',[
  C('hv2-split',[C('',[EB('How we test'),H('Every batch, independently verified'),
     T('<p>Every ONYX Research batch is independently verified by third-party laboratories using HPLC and mass spectrometry, with a reported purity of at least 99%. A certificate of analysis accompanies each product line.</p>','hv2-lead'),
     C('hv2-ctas',[B('View certificates →','/check-code/'),B('Our quality process','/what-are-peptides/','hv2-btn hv2-btn-o')],'row')]),
   W('html','',html=coa_html)],'row'),
  C('hv2-steps',[STEP('01','Manufactured','Produced in certified U.K. &amp; EU facilities under controlled conditions.'),
    STEP('02','Tested','Third-party HPLC &amp; mass-spec analysis for identity and purity.'),
    STEP('03','Documented','Batch-specific certificate of analysis issued and published.'),
    STEP('04','Coded','Unique authenticity code on every pack — verify it online in seconds.')],'row')])

def REV(t,who): return C('hv2-rev',[T('<div class="hv2-stars">★★★★★</div>'),T(f'<p>“{t}”</p>'),T(f'<p class="hv2-who">{who}</p>')])
reviews=SEC('hv2-reviews',[
  C('hv2-head',[C('',[EB('Researchers on ONYX'),H('What our customers say')]),T('<span class="hv2-ex">PLACEHOLDER — replace with verified reviews (e.g. Trustpilot widget)</span>')],'row'),
  C('hv2-revs',[REV('Example review text: fast dispatch and the COA matched the batch code on the box.','Example name · Verified buyer'),
    REV('Example review text: clear documentation and responsive support team.','Example name · Verified buyer'),
    REV('Example review text: the batch-code check gave us confidence before ordering again.','Example name · Research lab')],'row')])

b2b=SEC('hv2-b2bsec',[C('hv2-b2b',[
  C('hv2-b2b-tx',[EB('Research Supply Program'),H('Wholesale for labs &amp; businesses'),
    T('<p>Supply research materials to qualified laboratories, universities, research organisations and scientific businesses.</p>','hv2-lead'),
    LIST(['Bulk laboratory supply for qualified organisations','Batch documentation for applicable materials','Dedicated business support for approved accounts'],'hv2-b2b-list'),
    C('hv2-ctas',[B('Apply for a trade account →','/wholesale-application/'),B('Trader login','/b2b-login/','hv2-btn hv2-btn-ghost')],'row')]),
  C('hv2-b2b-ph',[],background_background='classic',background_image={'url':U+'onyx-home-v2/wholesale-hero.webp','id':''},background_size='cover',background_position='center center')],'row')])

faq=SEC('hv2-faqsec',[C('hv2-faq',[
  C('',[EB('FAQ'),H('Questions, answered'),T('<p>Ordering, delivery, payment and documentation.</p>','hv2-lead'),B('All FAQs →','/faq/','hv2-btn hv2-btn-o')]),
  W('toggle','hv2-toggle',tabs=[{'_id':rid(),'tab_title':q,'tab_content':a} for q,a in [
   ('How do I verify my product is genuine?','<p>Use the <a href="'+BASE+'/check-code/">Check Code</a> tool with the authenticity code printed on your pack.</p>'),
   ('Where can I find the certificate of analysis?','<p>A certificate of analysis accompanies each product line. Contact us for batch-specific documentation.</p>'),
   ('How fast is dispatch?','<p>Orders are shipped within 24 hours. See <a href="'+BASE+'/payment-delivery/">Payment &amp; Delivery</a>.</p>'),
   ('Which payment methods do you accept?','<p>See <a href="'+BASE+'/payment-delivery/">Payment &amp; Delivery</a> for current payment options.</p>'),
   ('Are products for human use?','<p>No. All ONYX materials are supplied strictly for laboratory research and are not intended for human or veterinary use.</p>')]])],'row')])

news=SEC('hv2-newssec',[C('hv2-news',[
  C('',[EB('Newsletter'),H('Stay informed'),T('<p>Research insights, new product releases and exclusive offers.</p>','hv2-lead')]),
  W('shortcode','hv2-newsform',shortcode='[fluentform id="6" css_classes="onyx-form-newsletter"]')],'row')])

data=[hero,trust,lab,best,testing,reviews,b2b,faq,news]
json.dump(data,open('/workspace/onyx/homev2/elementor-'+('server' if 'euro' in BASE else 'local')+'.json','w'))

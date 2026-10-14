import json,random,string,sys
BASE=sys.argv[1] if len(sys.argv)>1 else 'http://localhost:8088'
U=BASE+'/wp-content/uploads/2026/10/'
def rid(): return ''.join(random.choice('0123456789abcdef') for _ in range(7))
def C(cls,kids,direction='column',inner=True,**s):
    s=dict(s)
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


ANIM=lambda d=0: {'_animation':'fadeInUp','animation':'fadeInUp','animation_duration':'fast','_animation_delay':d,'animation_delay':d}
hero=C('hv2-sec hv2-hero2',[
  C('hv2-hero2-copy',[
    T('<p class="hv2-hero2-trust"><span class="hv2-dots"><i></i><i></i><i></i></span>Third-party tested · HPLC &amp; LC-MS · ≥99% reported purity</p>'),
    H('Research-grade peptides, <br>verified batch by batch','h1','hv2-h1-light'),
    T('<p>Independently tested for identity and purity, with a certificate of analysis for every product line.</p>','hv2-hero2-sub'),
    C('hv2-hero2-ctas',[B('Shop research peptides','/peptides/','hv2-btn hv2-btn-pill hv2-btn-arrow'),B('Verify a batch code','/check-code/','hv2-btn hv2-btn-link')],'row')]),
  W('html','hv2-hero2-art',html=f'<div class="hv2-art"><img loading="eager" fetchpriority="high" src="{U}onyx-home-v2/hero-composite.webp" width="1800" height="1400" alt="ONYX dual chamber peptide pens and boxes: Semaglutide, BPC-157 + TB-500, GHK-Cu, SS31 + MOTS-c, Glutathione, Cardiogen"></div>')
 ],'row',inner=False)

trust=SEC('hv2-trust',[C('hv2-trust-grid',[
  ICONB('fas fa-shipping-fast','Fast EU dispatch','Shipped within 24 hours'),
  ICONB('fas fa-flask','Verified purity','Third-party HPLC &amp; LC-MS'),
  ICONB('fas fa-qrcode','Authenticity codes','Check every batch online'),
  ICONB('fas fa-file-signature','Certificates of analysis','Per product line'),
  ICONB('fas fa-industry','U.K. &amp; EU manufacturing','Certified facilities')],'row')])

lab=SEC('hv2-lab',[C('hv2-split hv2-split-lab',[
  C('hv2-vid',[W('video','',video_type='youtube',youtube_url='https://www.youtube.com/watch?v=W1-7GmGogEg',mute='yes',rel='',controls='yes',
     show_image_overlay='yes',image_overlay={'url':U+'onyx-home-v2/manufacture.webp','id':''},image_overlay_size='full',lightbox='')]),
  C('hv2-lab-copy',[EB('Inside the ONYX laboratory'),H('The science and process behind every <span>ONYX</span> product.'),
    T('<p>Complete transparency into the technologies, processes, and controls behind every product we create.</p>','hv2-lead'),
    W('icon-box','hv2-ac',selected_icon={'value':'fas fa-clipboard-check','library':'fa-solid'},title_text='Analytical control',description_text='Verification of key parameters through HPLC and LC-MS analysis.',position='left',title_size='h3'),
    LIST(['High-tech equipment','Controlled production','Strict quality control'],'hv2-chips'),
    B('View research materials','/peptides/','hv2-btn hv2-btn-arrow')])],'row')])

best=SEC('hv2-best',[
  C('hv2-head',[C('',[EB('Most ordered'),H('Bestsellers')]),B('View all peptides','/peptides/','hv2-btn hv2-btn-o hv2-btn-arrow')],'row'),
  W('shortcode','hv2-products',shortcode='[products ids="270,272,268,255" columns="4" orderby="post__in"]'),
  T('<p><b>Research use only.</b> All ONYX materials are supplied exclusively for legitimate laboratory and scientific research. Not for human or veterinary use, diagnosis, treatment or therapeutic application.</p>','hv2-ruo')])

def STEP(n,icon,t,d): return C('hv2-step',[W('icon','hv2-step-ic',selected_icon={'value':icon,'library':'fa-solid'}),H(n,'p','hv2-step-n'),H(t,'h3'),T(f'<p>{d}</p>')])
testing=SEC('hv2-testing',[
  C('hv2-split hv2-split-test',[C('hv2-test-copy',[EB('How we test'),H('Every batch, independently verified'),
     T('<p>Every ONYX Research batch is independently verified by third-party laboratories using HPLC and mass spectrometry, with a reported purity of at least 99%. A certificate of analysis accompanies each product line, documenting identity, purity and batch-specific testing data.</p>','hv2-lead'),
     C('hv2-ctas',[B('Check a batch code','/check-code/','hv2-btn hv2-btn-arrow'),B('Contact us','/contact/','hv2-btn hv2-btn-o')],'row')]),
   W('html','hv2-coa-wrap',html=f"""<figure class="hv2-coa"><img loading="eager" decoding="async" src="{U}onyx-home-v2/coa-bpc157.webp" width="1819" height="1929" alt="ONYX certificate of analysis – BPC-157 10 mg / 3 mL"><figcaption><span class="hv2-f-ic">✓</span><div><b>BPC-157 10 mg / 3 mL</b><small>Cat. No. ONX-BPC157-010-PEN</small></div></figcaption></figure>""")],'row'),
  C('hv2-steps',[STEP('01','fas fa-industry','Manufactured','Produced in certified U.K. &amp; EU facilities under controlled conditions.'),
    STEP('02','fas fa-vials','Tested','Third-party HPLC &amp; mass-spec analysis for identity and purity.'),
    STEP('03','fas fa-file-alt','Documented','A certificate of analysis accompanies each product line.'),
    STEP('04','fas fa-qrcode','Coded','Unique authenticity code on every pack, verifiable online.')],'row')])

b2b=SEC('hv2-b2bsec',[C('hv2-b2b',[
  C('hv2-b2b-tx',[EB('Research Supply Program'),H('Wholesale for labs &amp; businesses'),
    T('<p>Supply research materials to qualified laboratories, universities, research organisations and scientific businesses.</p>','hv2-lead'),
    LIST(['Bulk laboratory supply for qualified organisations','Batch documentation for applicable materials','Dedicated business support for approved accounts'],'hv2-b2b-list'),
    C('hv2-ctas',[B('Apply for a trade account','/wholesale-application/','hv2-btn hv2-btn-arrow'),B('Trader login','/b2b-login/','hv2-btn hv2-btn-ghost')],'row')]),
  C('hv2-b2b-ph',[],background_background='classic',background_image={'url':U+'onyx-home-v2/wholesale-hero.webp','id':''},background_size='cover',background_position='center center')],'row')])

faq=SEC('hv2-faqsec',[C('hv2-faq',[
  C('hv2-faq-copy',[EB('FAQ'),H('Questions, answered'),T('<p>Ordering, delivery, payment and documentation.</p>','hv2-lead'),B('All FAQs','/faq/','hv2-btn hv2-btn-o hv2-btn-arrow')]),
  W('toggle','hv2-toggle',selected_icon={'value':'fas fa-plus','library':'fa-solid'},selected_active_icon={'value':'fas fa-minus','library':'fa-solid'},icon_align='right',tabs=[{'_id':rid(),'tab_title':q,'tab_content':a} for q,a in [
   ('How do I verify my product is genuine?','<p>Use the <a href="'+BASE+'/check-code/">Check Code</a> tool with the authenticity code printed on your pack.</p>'),
   ('Where can I find the certificate of analysis?','<p>A certificate of analysis accompanies each product line. Contact us for batch-specific documentation.</p>'),
   ('How fast is dispatch?','<p>Orders are shipped within 24 hours. See <a href="'+BASE+'/payment-delivery/">Payment &amp; Delivery</a>.</p>'),
   ('Which payment methods do you accept?','<p>See <a href="'+BASE+'/payment-delivery/">Payment &amp; Delivery</a> for current payment options.</p>'),
   ('Are products for human use?','<p>No. All ONYX materials are supplied strictly for laboratory research and are not intended for human or veterinary use.</p>')]])],'row')])

news=SEC('hv2-newssec',[C('hv2-news',[
  C('hv2-news-copy',[EB('Newsletter'),H('Stay informed'),T('<p>Research insights, new product releases and exclusive offers, straight to your inbox.</p>','hv2-lead')]),
  W('shortcode','hv2-newsform',shortcode='[fluentform id="6" css_classes="onyx-form-newsletter"]')],'row')])
data=[hero,trust,lab,best,testing,b2b,faq,news]
json.dump(data,open('/workspace/onyx/homev2/elementor-'+('server' if 'euro' in BASE else 'local')+'.json','w'))

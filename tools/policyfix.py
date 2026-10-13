import json,re,sys
P=['privacy-policy','terms-conditions','cookies-policy','refund-policy','shipping-policy','accessibility-policy']
def txt(c):
    s=c['settings'];v=s.get('editor') or s.get('title') or ''
    return re.sub(r'<[^>]+>','',v).strip()
def addc(c,cl):
    cur=c['settings'].get('css_classes','').split()
    if cl not in cur: cur.append(cl)
    c['settings']['css_classes']=' '.join(cur)
for slug in P:
    f=f'edata/{slug}.json';d=json.load(open(f));cnt={}
    def w(x):
        for i,c in enumerate(x):
            el=c.get('elements',[])
            if c.get('widgetType')=='heading' and txt(c).lower()=='on this page' and i+1<len(x): addc(x[i+1],'onyx-toc');cnt['toc']=1
            if c['elType']=='container' and len(el)==2 and all(e.get('widgetType') in('text-editor','heading') for e in el):
                t=txt(el[0])
                if re.fullmatch(r'\d+(\.\d+)+',t): addc(c,'onyx-clause');addc(c,'d%d'%(t.count('.')));cnt['cl']=cnt.get('cl',0)+1
                elif re.fullmatch(r'\d{2}',t): addc(c,'onyx-sechead');cnt['sh']=cnt.get('sh',0)+1
            w(el)
    w(d);json.dump(d,open(f,'w'));print(slug,cnt)

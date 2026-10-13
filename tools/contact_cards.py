import json,re
d=json.load(open('edata/contact.json'))
lk={'7c96d50':'https://wa.me/447404932343','080d4e5':'mailto:sales@onyxhealth.net'}
def ic(n):
    s=open(f'local/wp-content/themes/onyx-child/assets/icons/{n}.svg').read()
    s=re.sub(r'<!--.*?-->','',s,flags=re.S); s=re.sub(r'\s+',' ',s).strip()
    return s.replace('class="lucide','class="h-5 w-5 lucide')
def card(url,label,val,icon,col,ext):
    c='[#25d366]' if col=='wa' else 'accent'
    hov='group-hover:text-white' if col=='wa' else 'group-hover:text-accent-foreground'
    a=f'group flex items-center gap-4 rounded-2xl border bg-card p-5 transition hover:border-{c}/50 hover:shadow-md'
    s=f'grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-{c}/10 text-{c} ring-1 ring-inset ring-{c}/20 transition group-hover:bg-{c} {hov}'
    v=f'mt-0.5 block text-sm font-semibold group-hover:text-{c}'
    e=' target="_blank" rel="noopener noreferrer"' if ext else ''
    return f'<div class="onyx-tw"><a href="{url}"{e} class="{a}"><span class="{s}">{ic(icon)}</span><span><span class="block text-xs font-bold uppercase tracking-wider text-muted-foreground">{label}</span><span class="{v}">{val}</span></span></a></div>'
def rep(x):
    for c in x:
        if c['id']=='7c96d50': c.update(widgetType='html',settings={'html':card(lk['7c96d50'],'WhatsApp','Chat with us','phone','wa',True)})
        if c['id']=='080d4e5': c.update(widgetType='html',settings={'html':card(lk['080d4e5'],'Email','sales@onyxhealth.net','mail','ac',False)})
        if c['id']=='7684b2c': c['settings']['flex_gap']={"unit":"px","column":"16","row":"16","isLinked":True,"size":16}
        rep(c.get('elements',[]))
rep(d);json.dump(d,open('edata/contact.json','w'))

import os
W = os.environ.get('ONYX_WORK', 'build-work')
import json, sys, os, re
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from convert import *

FORMS = json.load(open(W + '/import/form-ids.json'))
D = W + '/dump/'
OUT = W + '/elementor/'
os.makedirs(OUT, exist_ok=True)

def ff(key): return f'[fluentform id="{FORMS[key]}" css_classes="onyx-form-{key}"]'

def calc_test(cv, n):
    s = cv.pg.s(n)
    return bool(color(s['backgroundColor'])) and 'Click-Dial Pen' in (n.get('text') or '')

def faq_hook(cv, n, parent_info):
    # list of accordion items: children are bordered boxes each with a <button>
    kids = [k for k in n.get('kids', []) if k['tag'] != '#text']
    if len(kids) >= 3 and all(any(kk['tag'] == 'button' for kk in k.get('kids', [])) for k in kids):
        src = {}
        try:
            t = open(W + '/fe/onyx-peptide/src/pages/Faq.tsx').read()
            for q, a in re.findall(r'q:\s*"([^"]+)",\s*a:\s*"([^"]+)"', t): src[q] = a
        except Exception: pass
        items = []
        for k in kids:
            btn = [kk for kk in k['kids'] if kk['tag'] == 'button'][0]
            q = (btn.get('text') or '').strip().rstrip('+-').strip()
            ans = [kk for kk in k['kids'] if kk['tag'] != 'button' and kk['tag'] != '#text']
            a = (ans[0].get('text') or '').strip() if ans else src.get(q, '')
            items.append({'_id': eid(), 'tab_title': q, 'tab_content': f'<p>{H.escape(a)}</p>'})
        return [W('accordion', {'tabs': items, 'selected_icon': {'value': 'fas fa-plus', 'library': 'fa-solid'}, 'selected_active_icon': {'value': 'fas fa-minus', 'library': 'fa-solid'},
                                 'border_color': '#dce2e9', 'title_background': '#ffffff', 'title_color': '#040d1e', 'tab_active_color': '#2885ef', 'icon_color': '#2885ef', 'icon_active_color': '#2885ef',
                                 'content_background_color': '#ffffff', 'content_color': '#5d646f', 'title_typography_typography': 'custom', 'title_typography_font_size': {'unit': 'px', 'size': 14}, 'title_typography_font_weight': '600',
                                 'content_typography_typography': 'custom', 'content_typography_font_size': {'unit': 'px', 'size': 14}})]
    return None

PAGES = {
    'home': dict(slug='home', title='Home', ov={'form': ff('newsletter')}),
    'what-are-peptides': dict(title='What Are Peptides?'),
    'faq': dict(title='FAQ', ov={'hooks': [faq_hook]}),
    'payment-delivery': dict(title='Payment & Delivery'),
    'product-returns': dict(title='Product Returns'),
    'contact': dict(title='Contact', ov={'form': ff('contact')}),
    'wholesale-application': dict(title='Wholesale Application', ov={'form': ff('wholesale')}),
    'independent-doctor-consultation': dict(title='Independent Doctor Consultation', ov={'form': ff('consultation')}),
    'terms-conditions': dict(title='Terms & Conditions'),
    'privacy-policy': dict(title='Privacy Policy'),
    'refund-policy': dict(title='Refund Policy'),
    'shipping-policy': dict(title='Shipping Policy'),
    'cookies-policy': dict(title='Cookies Policy'),
    'accessibility-policy': dict(title='Accessibility Policy'),
    'product-catalogue': dict(title='Product Catalogue', ov={'no_products': True}),
    'check-code': dict(title='Check Code', ov={'form': '[onyx_check_code]'}),
    'analytical-reference-calculator': dict(title='Analytical Reference Calculator', ov={'form': '[onyx_reference_calculator]', 'form_container_test': calc_test}),
    'peptide-plans': dict(title='Peptide Plans'),
}

if __name__ == '__main__':
    only = sys.argv[1:]
    manifest = {}
    for key, cfg in PAGES.items():
        if only and key not in only: continue
        f = D + ('home' if key == 'home' else key) + '.json'
        pg = Page(f)
        cv = Converter(pg, cfg.get('ov'))
        data = mark_inner(cv.page())
        json.dump(data, open(OUT + key + '.json', 'w'))
        manifest[key] = {'slug': cfg.get('slug', key), 'title': cfg['title'], 'file': OUT + key + '.json', 'notes': cv.notes}
        print(key, len(json.dumps(data)) // 1024, 'KB', cv.notes)
    old = json.load(open(OUT + 'manifest.json')) if os.path.exists(OUT + 'manifest.json') else {}
    old.update(manifest)
    json.dump(old, open(OUT + 'manifest.json', 'w'), indent=1)

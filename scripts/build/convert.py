import os
W = os.environ.get('ONYX_WORK', 'build-work')
"""Convert a rendered-DOM dump (dump.mjs) into Elementor container JSON."""
import json, re, math, sys, random, string, html as H
from bs4 import BeautifulSoup

LOCAL = 'http://localhost:8088'
MEDIA = json.load(open(W + '/import/media-map.json'))
PRODUCTS = json.load(open(W + '/import/product-ids.json'))  # slug -> id

def eid(): return ''.join(random.choice('0123456789abcdef') for _ in range(7))

# ---------- colours ----------
def _srgb(x):
    x = max(0.0, min(1.0, x))
    return round(255 * ((1.055 * x ** (1 / 2.4) - 0.055) if x > 0.0031308 else 12.92 * x))
def oklab_to_rgb(L, a, b):
    l_ = L + 0.3963377774 * a + 0.2158037573 * b; m_ = L - 0.1055613458 * a - 0.0638541728 * b; s_ = L - 0.0894841775 * a - 1.2914855480 * b
    l, m, s = l_ ** 3, m_ ** 3, s_ ** 3
    return (_srgb(4.0767416621 * l - 3.3077115913 * m + 0.2309699292 * s), _srgb(-1.2684380046 * l + 2.6097574011 * m - 0.3413193965 * s), _srgb(-0.0041960863 * l - 0.7034186147 * m + 1.7076147010 * s))
def num(v, pct_scale=1.0):
    v = v.strip()
    if v == 'none': return 0.0
    if v.endswith('%'): return float(v[:-1]) / 100 * pct_scale
    return float(v)
def color(c):
    """Return rgba() string or None if fully transparent."""
    if not c: return None
    c = c.strip()
    m = re.match(r'rgba?\(([^)]+)\)', c)
    if m:
        p = [x.strip() for x in re.split(r'[ ,/]+', m.group(1)) if x.strip()]
        r, g, b = [float(x) for x in p[:3]]; a = float(p[3]) if len(p) > 3 else 1.0
    else:
        m = re.match(r'(oklch|oklab|color|lab|lch)\(([^)]+)\)', c)
        if not m: return None
        fn, body = m.groups(); parts = body.split('/')
        vals = parts[0].split(); a = num(parts[1]) if len(parts) > 1 else 1.0
        if fn == 'oklch':
            L, C, h = num(vals[0]), num(vals[1], 0.4), num(vals[2]) if len(vals) > 2 else 0
            r, g, b = oklab_to_rgb(L, C * math.cos(math.radians(h)), C * math.sin(math.radians(h)))
        elif fn == 'oklab':
            r, g, b = oklab_to_rgb(num(vals[0]), num(vals[1], 0.4), num(vals[2], 0.4))
        elif fn == 'color':
            r, g, b = [_srgb(float(x)) if False else round(float(x) * 255) for x in vals[1:4]]
        else:
            return None
    if a <= 0.001: return None
    if a >= 0.999: return '#%02x%02x%02x' % (int(r), int(g), int(b))
    return 'rgba(%d,%d,%d,%.2f)' % (r, g, b, a)
def colors_in(s): return [color(x) for x in re.findall(r'(?:rgba?|oklch|oklab|color)\([^()]*\)', s)]

def px(v):
    try: return float(str(v).replace('px', ''))
    except Exception: return 0.0
def r1(x): return round(x, 1) if x % 1 else int(x)

# ---------- url mapping ----------
def map_href(h):
    if not h: return ''
    if h.startswith(('mailto:', 'tel:', 'https://wa.me', 'https://api.whatsapp')): return h
    m = re.match(r'https?://(www\.)?onyxhealth\.net(/[^?#]*)?([?#].*)?$', h)
    if not m: return h
    path = (m.group(2) or '/').rstrip('/') or '/'
    rules = [(r'^/product/([^/]+)$', lambda g: f'/product/{g[0].strip()}/'),
             (r'^/peptide-plans/([^/]+)$', lambda g: f'/product/{g[0]}/'),
             (r'^/peptides/([^/]+)$', lambda g: f'/peptides/{g[0]}/'),
             (r'^/(login|register|profile)$', lambda g: '/my-account/'),
             (r'^/(b2b-login|b2b-register)$', lambda g: '/wholesale-application/'),
             (r'^/quick-order$', lambda g: '/peptides/'),
             (r'^/$', lambda g: '/')]
    for pat, fn in rules:
        mm = re.match(pat, path)
        if mm: return LOCAL + fn(mm.groups())
    return LOCAL + path + '/'
def map_media(u):
    if u in MEDIA: return MEDIA[u]['url'], MEDIA[u]['id']
    return u, ''

# ---------- dump helpers ----------
class Page:
    def __init__(self, path):
        d = json.load(open(path)); self.tree = d['tree']; self.st = d['st']; self.url = d['url']
    def s(self, n, bp='d'): return self.st[bp].get(n.get('ix'), None)
    def visible(self, n, bp):
        s = self.s(n, bp)
        if not s: return False
        if s['display'] == 'none' or s['visibility'] == 'hidden': return False
        if str(s.get('opacity')) == '0': return False
        if s['w'] < 1 and s['h'] < 1 and s['position'] not in ('absolute', 'fixed'): return False
        return True
    def vis_any(self, n): return any(self.visible(n, bp) for bp in 'dtm')

INLINE_TAGS = {'span', 'a', 'strong', 'b', 'em', 'i', 'br', 'small', 'sup', 'sub', 'u', 'code', 'mark', 'abbr', 'time', '#text', 'svg', 'path', 'circle', 'line', 'polyline', 'rect', 'g', 'polygon', 'ellipse', 'defs', 'use', 'path'}
def text_of(n):
    if n['tag'] == '#text': return n['text']
    return n.get('text') or ''

def is_inline_tree(pg, n):
    for k in n.get('kids', []):
        if k['tag'] == '#text': continue
        if k['tag'] not in INLINE_TAGS: return False
        s = pg.s(k)
        if s and s['display'] in ('block', 'flex', 'grid', 'list-item') and k['tag'] not in ('svg', 'br'): return False
        if k['tag'] != 'svg' and not is_inline_tree(pg, k): return False
    return True

def has_tag(n, tags):
    if n['tag'] in tags: return True
    return any(has_tag(k, tags) for k in n.get('kids', []))
def count_tag(n, tags):
    return (1 if n['tag'] in tags else 0) + sum(count_tag(k, tags) for k in n.get('kids', []))
def product_links(n, acc=None):
    acc = set() if acc is None else acc
    if n['tag'] == 'a':
        h = (n.get('attrs') or {}).get('href', '')
        m = re.search(r'onyxhealth\.net/(?:product|peptide-plans)/([^/?#]+)', h)
        if m: acc.add(m.group(1).strip().replace('%20', ''))
    for k in n.get('kids', []): product_links(k, acc)
    return acc

# ---------- style -> elementor settings ----------
def box(s, prop):
    keys = {'padding': ('paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft'), 'margin': ('marginTop', 'marginRight', 'marginBottom', 'marginLeft'),
            'border_width': ('borderTopWidth', 'borderRightWidth', 'borderBottomWidth', 'borderLeftWidth')}[prop]
    v = [r1(px(s[k])) for k in keys]
    return {'unit': 'px', 'top': str(v[0]), 'right': str(v[1]), 'bottom': str(v[2]), 'left': str(v[3]), 'isLinked': len(set(v)) == 1}, v
def radius(s):
    parts = s['borderRadius'].split()
    v = [r1(min(px(p), 9999)) for p in parts] if parts else [0]
    while len(v) < 4: v.append(v[-1] if len(v) < 2 else v[len(v) - 2])
    return {'unit': 'px', 'top': str(v[0]), 'right': str(v[1]), 'bottom': str(v[2]), 'left': str(v[3]), 'isLinked': len(set(v)) == 1}, v

def typography(pg, n, out, prefix='typography'):
    for bp, suf in (('d', ''), ('t', '_tablet'), ('m', '_mobile')):
        s = pg.s(n, bp)
        if not s: continue
        if bp == 'd' or s['fontSize'] != pg.s(n, 'd')['fontSize']:
            out[f'{prefix}_font_size{suf}'] = {'unit': 'px', 'size': r1(px(s['fontSize']))}
        if bp == 'd' or s['lineHeight'] != pg.s(n, 'd')['lineHeight']:
            if s['lineHeight'] != 'normal': out[f'{prefix}_line_height{suf}'] = {'unit': 'px', 'size': r1(px(s['lineHeight']))}
    s = pg.s(n)
    out[f'{prefix}_typography'] = 'custom'
    out[f'{prefix}_font_weight'] = str(s['fontWeight'])
    if s['letterSpacing'] not in ('normal', '0px'): out[f'{prefix}_letter_spacing'] = {'unit': 'px', 'size': round(px(s['letterSpacing']), 2)}
    if s['textTransform'] != 'none': out[f'{prefix}_text_transform'] = s['textTransform']
    if s['fontStyle'] == 'italic': out[f'{prefix}_font_style'] = 'italic'

ALIGN = {'start': 'left', 'left': 'left', 'center': 'center', 'right': 'right', 'end': 'right', 'justify': 'justify'}
def text_align(pg, n, out, key='align'):
    for bp, suf in (('d', ''), ('t', '_tablet'), ('m', '_mobile')):
        s = pg.s(n, bp)
        if s and (bp == 'd' or s['textAlign'] != pg.s(n, 'd')['textAlign']):
            out[key + suf] = ALIGN.get(s['textAlign'], 'left')

def visibility_flags(pg, n, out):
    for bp, key in (('d', 'hide_desktop'), ('t', 'hide_tablet'), ('m', 'hide_mobile')):
        if not pg.visible(n, bp): out[key] = 'hidden-' + {'d': 'desktop', 't': 'tablet', 'm': 'mobile'}[bp]

def margins(pg, n, out, key):
    for bp, suf in (('d', ''), ('t', '_tablet'), ('m', '_mobile')):
        s = pg.s(n, bp)
        if not s: continue
        m, v = box(s, 'margin')
        # auto margins come back as px; ignore horizontal centring margins
        v[1] = v[3] = 0 if (v[1] == v[3] and v[1] > 0) else v[1]
        m.update(right=str(v[1]), left=str(v[3]))
        if any(v) or bp != 'd': out[key + suf] = m

def sizing_in_parent(pg, n, out, parent_info, is_widget):
    """Width handling for children of row flex containers / max-width blocks."""
    s = pg.s(n)
    if not s or not parent_info: return
    pdir = parent_info.get('dir')
    pw = s['pw'] or 1
    for bp, suf in (('d', ''), ('t', '_tablet'), ('m', '_mobile')):
        sb = pg.s(n, bp)
        if not sb: continue
        dirb = parent_info.get('dir_' + bp, pdir)
        frac = sb['w'] / (sb['pw'] or 1)
        if dirb == 'row':
            if float(sb['flexGrow'] or 0) >= 1:
                out['_flex_size' + suf] = 'grow'
            elif is_widget:
                out['_element_width' + suf] = 'initial'; out['_element_custom_width' + suf] = {'unit': '%', 'size': round(frac * 100, 2)} if frac > 0.25 else {'unit': 'px', 'size': r1(math.ceil(sb['w']))}
            else:
                out['width' + suf] = {'unit': '%', 'size': round(frac * 100, 2)} if frac > 0.25 else {'unit': 'px', 'size': r1(math.ceil(sb['w']))}
                if frac <= 0.25: out['_flex_size' + suf] = 'none'
        else:
            if frac < 0.97 and sb['maxWidth'] != 'none':
                if is_widget:
                    out['_element_width' + suf] = 'initial'; out['_element_custom_width' + suf] = {'unit': 'px', 'size': r1(math.ceil(sb['w']))}
                else:
                    out['width' + suf] = {'unit': 'px', 'size': r1(math.ceil(sb['w']))}
                ml, mr = px(sb['marginLeft']), px(sb['marginRight'])
                key = '_flex_align_self'
                if ml > 1 and abs(ml - mr) < 2: out[key + suf] = 'center'
            elif is_widget and sb['display'].startswith('inline') and frac < 0.9:
                out['_element_width' + suf] = 'auto'
                if bp == 'd':
                    pai = parent_info.get('align_items', 'stretch')
                    ta = sb['textAlign']
                    out['_flex_align_self'] = {'center': 'center', 'flex-end': 'flex-end'}.get(pai, 'center' if ta == 'center' else 'flex-start')
            elif bp != 'd' and ('width' in out or '_element_width' in out):
                if is_widget: out['_element_width' + suf] = 'inherit'
                else: out['width' + suf] = {'unit': '%', 'size': 100}

def bg_settings(pg, n, out, prefix='background'):
    s = pg.s(n)
    bgi = s['backgroundImage']
    if bgi and 'gradient' in bgi:
        cs = [c for c in colors_in(bgi) if c]
        if len(cs) >= 2:
            ang = 180
            m = re.search(r'(-?[\d.]+)deg', bgi)
            if m: ang = float(m.group(1))
            elif 'to right' in bgi: ang = 90
            elif 'to bottom right' in bgi: ang = 135
            elif 'to top' in bgi: ang = 0
            elif 'to left' in bgi: ang = 270
            out.update({f'{prefix}_background': 'gradient', f'{prefix}_color': cs[0], f'{prefix}_color_b': cs[-1], f'{prefix}_gradient_angle': {'unit': 'deg', 'size': ang},
                        f'{prefix}_color_stop': {'unit': '%', 'size': 0}, f'{prefix}_color_b_stop': {'unit': '%', 'size': 100}})
            return
    m = re.search(r'url\("?([^")]+)"?\)', bgi or '')
    if m:
        u, i = map_media(m.group(1))
        out.update({f'{prefix}_background': 'classic', f'{prefix}_image': {'url': u, 'id': i, 'size': ''}, f'{prefix}_size': 'cover' if 'cover' in s['backgroundSize'] else 'contain' if 'contain' in s['backgroundSize'] else 'auto', f'{prefix}_position': 'center center', f'{prefix}_repeat': 'no-repeat'})
        c = color(s['backgroundColor'])
        if c: out[f'{prefix}_color'] = c
        return
    c = color(s['backgroundColor'])
    if c: out.update({f'{prefix}_background': 'classic', f'{prefix}_color': c})

def border_settings(pg, n, out, prefix='', radius_key='border_radius'):
    s = pg.s(n)
    bw, v = box(s, 'border_width')
    bc = color(s['borderTopColor'])
    if any(v) and s['borderStyle'].split()[0] != 'none' and bc:
        out[prefix + 'border_border'] = 'solid'; out[prefix + 'border_width'] = bw; out[prefix + 'border_color'] = bc
    rr, rv = radius(s)
    if any(rv): out[radius_key] = rr
    sh = s['boxShadow']
    if sh and sh != 'none':
        m = re.search(r'((?:rgba?|oklch|oklab)\([^)]*\))\s+(-?[\d.]+)px\s+(-?[\d.]+)px\s+(-?[\d.]+)px\s*(-?[\d.]+)?px?', sh)
        # pick the most visible shadow layer
        best = None
        for mm in re.finditer(r'((?:rgba?|oklch|oklab)\([^)]*\))\s+(-?[\d.]+)px\s+(-?[\d.]+)px\s+(-?[\d.]+)px(?:\s+(-?[\d.]+)px)?', sh):
            c = color(mm.group(1))
            if c and (best is None or float(mm.group(4)) > float(best.group(4))): best = mm
        if best:
            out[prefix + 'box_shadow_box_shadow_type'] = 'yes'
            out[prefix + 'box_shadow_box_shadow'] = {'horizontal': r1(float(best.group(2))), 'vertical': r1(float(best.group(3))), 'blur': r1(float(best.group(4))), 'spread': r1(float(best.group(5) or 0)), 'color': color(best.group(1))}

# ---------- inline html cleaning ----------
def clean_inline(pg, n):
    """innerHTML of a text leaf with classes replaced by minimal inline styles."""
    soup = BeautifulSoup(n.get('inner') or H.escape(n.get('text') or ''), 'html.parser')
    base = pg.s(n)
    for el in soup.find_all(True):
        ix = el.get('data-ix'); st = pg.st['d'].get(ix) if ix else None
        style = []
        if el.name == 'svg':
            if st: el['width'] = r1(st['w']); el['height'] = r1(st['h'])
            c = color(st['color']) if st else None
            el['style'] = f'display:inline-block;vertical-align:middle;{"color:"+c+";" if c else ""}'
            for a in ('class', 'data-ix'): el.attrs.pop(a, None)
            for sub in el.find_all(True):
                for a in ('class', 'data-ix'): sub.attrs.pop(a, None)
            continue
        if st and base:
            if st['color'] != base['color'] and color(st['color']): style.append('color:' + color(st['color']))
            if st['fontWeight'] != base['fontWeight']: style.append('font-weight:' + st['fontWeight'])
            if st['fontSize'] != base['fontSize']: style.append('font-size:' + st['fontSize'])
            if st['textTransform'] != base['textTransform'] and st['textTransform'] != 'none': style.append('text-transform:' + st['textTransform'])
            if st['display'] in ('block',) and el.name == 'span': style.append('display:block')
            bg = color(st['backgroundColor'])
            if bg: style.append(f'background:{bg};padding:{st["paddingTop"]} {st["paddingRight"]};border-radius:{st["borderRadius"]}')
            if 'underline' in st['textDecorationLine']: style.append('text-decoration:underline')
        for a in list(el.attrs):
            if a not in ('href', 'target', 'rel'): el.attrs.pop(a)
        if el.name == 'a' and el.get('href'): el['href'] = map_href(el['href'] if el['href'].startswith('http') else 'https://onyxhealth.net' + el['href'])
        if el.name == 'a' and st and not any(x.startswith('color:') for x in style) and color(st['color']): style.append('color:' + color(st['color']))
        if style: el['style'] = ';'.join(style)
    return str(soup).strip()

# ---------- widgets ----------
def W(wtype, settings): return {'id': eid(), 'elType': 'widget', 'widgetType': wtype, 'settings': settings, 'elements': []}
def C(settings, kids): return {'id': eid(), 'elType': 'container', 'isInner': False, 'settings': settings, 'elements': kids}

def common_widget(pg, n, st, parent_info):
    margins(pg, n, st, '_margin')
    visibility_flags(pg, n, st)
    sizing_in_parent(pg, n, st, parent_info, True)

def heading_w(pg, n, parent_info, tag=None):
    s = pg.s(n); st = {'title': clean_inline(pg, n), 'header_size': tag or n['tag']}
    c = color(s['color']);  st['title_color'] = c or '#040d1e'
    typography(pg, n, st); text_align(pg, n, st)
    p, v = box(s, 'padding')
    if any(v): st['_padding'] = p
    common_widget(pg, n, st, parent_info)
    return W('heading', st)

def text_w(pg, n, parent_info):
    s = pg.s(n)
    inner = clean_inline(pg, n)
    tag = n['tag'] if n['tag'] in ('p', 'li', 'blockquote') else 'div'
    html = f'<p>{inner}</p>' if tag in ('p', 'div', 'li') else f'<{tag}>{inner}</{tag}>'
    st = {'editor': html}
    c = color(s['color'])
    if c: st['text_color'] = c
    typography(pg, n, st); text_align(pg, n, st)
    p, v = box(s, 'padding')
    if any(v): st['_padding'] = p
    bg = color(s['backgroundColor'])
    if bg: st['_background_background'] = 'classic'; st['_background_color'] = bg
    border_settings(pg, n, st, '_', '_border_radius')
    common_widget(pg, n, st, parent_info)
    return W('text-editor', st)

def is_buttonish(pg, n):
    if n['tag'] not in ('a', 'button'): return False
    s = pg.s(n)
    if has_tag(n, {'img'}): return False
    txt = (n.get('text') or '').strip()
    if not txt or len(txt) > 60: return False
    bg = color(s['backgroundColor']); bw = px(s['borderTopWidth'])
    return bool(bg) or (bw > 0 and px(s['paddingLeft']) >= 8) or 'gradient' in (s['backgroundImage'] or '')

def button_w(pg, n, parent_info):
    s = pg.s(n); a = n.get('attrs') or {}
    href = map_href(a.get('href', '')) if n['tag'] == 'a' else ''
    st = {'text': (n.get('text') or '').strip(), 'link': {'url': href or '#', 'is_external': '', 'nofollow': ''}}
    bg = color(s['backgroundColor'])
    if 'gradient' in (s['backgroundImage'] or ''):
        cs = [c for c in colors_in(s['backgroundImage']) if c]
        if cs: bg = cs[0]
    st['background_color'] = bg or 'rgba(0,0,0,0)'
    st['button_text_color'] = color(s['color']) or '#ffffff'
    typography(pg, n, st)
    p, v = box(s, 'padding'); st['text_padding'] = p
    rr, rv = radius(s); st['border_radius'] = rr
    bw, bv = box(s, 'border_width'); bc = color(s['borderTopColor'])
    if any(bv) and bc: st.update(border_border='solid', border_width=bw, border_color=bc)
    if has_tag(n, {'svg'}):
        kids = [k for k in n.get('kids', []) if not (k['tag'] == '#text' and not k['text'].strip())]
        first_svg = bool(kids) and kids[0]['tag'] == 'svg'
        st['selected_icon'] = {'value': 'fas fa-arrow-right' if not first_svg else 'fas fa-check', 'library': 'fa-solid'}
        st['icon_align'] = 'row' if first_svg else 'row-reverse'
        st['icon_indent'] = {'unit': 'px', 'size': 8}
    # hover: accent -> primary like the React buttons
    st['button_background_hover_color'] = '#001a61' if (bg or '').lower() == '#2885ef' else '#2885ef'
    st['hover_color'] = '#ffffff'
    sb = pg.s(n); frac = sb['w'] / (sb['pw'] or 1)
    st['align'] = 'justify' if frac > 0.95 and parent_info.get('dir') != 'row' else ''
    ps = parent_info.get('align_items', '')
    if not st['align']:
        st['align'] = {'center': 'center', 'flex-end': 'right', 'end': 'right'}.get(ps, 'left')
    common_widget(pg, n, st, parent_info)
    if parent_info.get('dir') == 'row':
        st.pop('_element_custom_width', None); st['_element_width'] = 'auto'
        for suf in ('_tablet', '_mobile'): st.pop('_element_custom_width' + suf, None); st.pop('_element_width' + suf, None)
    return W('button', st)

def image_w(pg, n, parent_info, link=None):
    s = pg.s(n); a = n.get('attrs') or {}
    u, i = map_media(a.get('src', ''))
    st = {'image': {'url': u, 'id': i, 'size': '', 'alt': a.get('alt', '')}, 'image_size': 'full', 'align': ALIGN.get(s['textAlign'], 'left') if s['textAlign'] in ('center', 'right', 'end') else 'left'}
    if parent_info.get('align_items') == 'center': st['align'] = 'center'
    for bp, suf in (('d', ''), ('t', '_tablet'), ('m', '_mobile')):
        sb = pg.s(n, bp)
        if not sb: continue
        frac = sb['w'] / (sb['pw'] or 1)
        st['width' + suf] = {'unit': '%', 'size': 100} if frac > 0.95 else {'unit': 'px', 'size': r1(math.ceil(sb['w']))}
        if sb['objectFit'] in ('cover', 'contain') and sb['h'] > 0:
            st['height' + suf] = {'unit': 'px', 'size': r1(sb['h'])}
            st['object-fit' + suf] = sb['objectFit']
    rr, rv = radius(s)
    if any(rv): st['image_border_radius'] = rr
    if link: st['link_to'] = 'custom'; st['link'] = {'url': link}
    margins(pg, n, st, '_margin'); visibility_flags(pg, n, st)
    if parent_info.get('dir') == 'row':
        st['_element_width'] = 'auto'
    return W('image', st)

def svg_w(pg, n, parent_info, link=None):
    s = pg.s(n)
    html = n.get('html', '')
    soup = BeautifulSoup(html, 'html.parser')
    for el in soup.find_all(True):
        el.attrs.pop('class', None); el.attrs.pop('data-ix', None)
    html = str(soup)
    if link: html = f'<a href="{link}" style="color:inherit;display:inline-flex">{html}</a>'
    st = {'html': html}
    margins(pg, n, st, '_margin'); visibility_flags(pg, n, st)
    st['_element_width'] = 'auto'
    return W('html', st)

def video_w(pg, n, parent_info):
    a = n.get('attrs') or {}
    u, i = map_media(a.get('src', ''))
    s = pg.s(n)
    st = {'video_type': 'hosted', 'hosted_url': {'url': u, 'id': i}, 'autoplay': 'yes', 'mute': 'yes', 'loop': 'yes', 'controls': '', 'play_on_mobile': 'yes'}
    rr, rv = radius(s)
    if any(rv): st['_border_radius'] = rr
    margins(pg, n, st, '_margin'); visibility_flags(pg, n, st)
    return W('video', st)

# ---------- container ----------
def container_settings(pg, n, parent_info, top=False):
    s = pg.s(n); st = {}
    disp = s['display']
    info = {}
    if disp == 'grid' or disp == 'inline-grid':
        st['container_type'] = 'grid'
        for bp, suf in (('d', ''), ('t', '_tablet'), ('m', '_mobile')):
            sb = pg.s(n, bp)
            if not sb: continue
            cols = [px(x) for x in sb['gridTemplateColumns'].split() if x.endswith('px')]
            if not cols: cols = [1]
            if len(set(round(c) for c in cols)) <= 1 or max(cols) - min(cols) < 4:
                st['grid_columns_grid' + suf] = {'unit': 'fr', 'size': len(cols)}
            else:
                tot = sum(cols)
                st['grid_columns_grid' + suf] = {'unit': 'custom', 'size': ' '.join('%.3ffr' % (c / tot * len(cols)) for c in cols)}
            st['grid_gaps' + suf] = {'unit': 'px', 'column': str(r1(px(sb['columnGap']) if sb['columnGap'] != 'normal' else 0)), 'row': str(r1(px(sb['rowGap']) if sb['rowGap'] != 'normal' else 0)), 'isLinked': False}
        st['grid_rows_grid'] = {'unit': 'custom', 'size': 'auto'}
        st['grid_auto_flow'] = 'row'
        if s['alignItems'] in ('center', 'start', 'end', 'stretch'): st['grid_align_items'] = s['alignItems']
        info['dir'] = 'grid'
    else:
        st['container_type'] = 'flex'
        for bp, suf in (('d', ''), ('t', '_tablet'), ('m', '_mobile')):
            sb = pg.s(n, bp)
            if not sb: continue
            if sb['display'] in ('flex', 'inline-flex'):
                d = 'row' if sb['flexDirection'].startswith('row') else 'column'
                if sb['flexDirection'].endswith('reverse'): d += '-reverse'
                gap_c = px(sb['columnGap']) if sb['columnGap'] != 'normal' else 0
                gap_r = px(sb['rowGap']) if sb['rowGap'] != 'normal' else 0
                st['flex_gap' + suf] = {'unit': 'px', 'column': str(r1(gap_c)), 'row': str(r1(gap_r)), 'isLinked': gap_c == gap_r, 'size': r1(gap_c)}
                st['flex_wrap' + suf] = 'wrap' if sb['flexWrap'] == 'wrap' else 'nowrap'
                jc = sb['justifyContent']; ai = sb['alignItems']
                st['flex_justify_content' + suf] = {'normal': 'flex-start', 'start': 'flex-start', 'end': 'flex-end'}.get(jc, jc) if jc in ('normal', 'start', 'end', 'flex-start', 'flex-end', 'center', 'space-between', 'space-around', 'space-evenly') else 'flex-start'
                st['flex_align_items' + suf] = {'normal': 'stretch', 'start': 'flex-start', 'end': 'flex-end', 'baseline': 'center'}.get(ai, ai) if ai in ('normal', 'start', 'end', 'flex-start', 'flex-end', 'center', 'stretch', 'baseline') else 'stretch'
            else:
                d = 'column'
                st['flex_gap' + suf] = {'unit': 'px', 'column': '0', 'row': '0', 'isLinked': True, 'size': 0}
                st['flex_align_items' + suf] = 'stretch'
                st['flex_justify_content' + suf] = 'flex-start'
                st['flex_wrap' + suf] = 'nowrap'
            st['flex_direction' + suf] = d
            info['dir_' + bp] = 'row' if d.startswith('row') else 'column'
        info['dir'] = info.get('dir_d', 'column')
        info['align_items'] = st.get('flex_align_items', '')
    # box model
    for bp, suf in (('d', ''), ('t', '_tablet'), ('m', '_mobile')):
        sb = pg.s(n, bp)
        if not sb: continue
        p, v = box(sb, 'padding'); st['padding' + suf] = p
        if sb['minHeight'] not in ('auto', '0px') and px(sb['minHeight']) > 0:
            st['min_height' + suf] = {'unit': 'px', 'size': r1(px(sb['minHeight']))}
    margins(pg, n, st, 'margin')
    # boxed content (max-width + auto margins)
    mw = s['maxWidth']
    if mw != 'none' and px(mw) > 0 and abs(px(s['marginLeft']) - px(s['marginRight'])) < 2 and px(s['marginLeft']) > 0:
        st['content_width'] = 'boxed' if top else 'full'
        if top: st['boxed_width'] = {'unit': 'px', 'size': r1(px(mw))}
        else:
            st['width'] = {'unit': 'px', 'size': r1(px(mw))}; st['width_mobile'] = {'unit': '%', 'size': 100}; st['width_tablet'] = {'unit': '%', 'size': 100}
            st['_flex_align_self'] = 'center'
    else:
        st['content_width'] = 'full'
    bg_settings(pg, n, st)
    border_settings(pg, n, st)
    if s['overflow'] == 'hidden': st['overflow'] = 'hidden'
    visibility_flags(pg, n, st)
    sizing_in_parent(pg, n, st, parent_info, False)
    return st, info

def has_visual(pg, n):
    s = pg.s(n)
    if color(s['backgroundColor']) or (s['backgroundImage'] and s['backgroundImage'] != 'none'): return True
    if any(px(s[k]) > 0 for k in ('borderTopWidth', 'borderBottomWidth', 'borderLeftWidth', 'borderRightWidth')): return True
    if s['boxShadow'] != 'none': return True
    if any(px(s[k]) > 0 for k in ('paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft')): return True
    if s['maxWidth'] != 'none' or (s['minHeight'] not in ('auto', '0px')): return True
    return False

class Converter:
    def __init__(self, pg, overrides=None):
        self.pg = pg; self.ov = overrides or {}
        self.notes = []
        self.seen_grids = set()

    def node(self, n, parent_info, top=False):
        pg = self.pg
        if n['tag'] == '#text':
            t = n['text'].strip()
            if not t: return []
            return [W('text-editor', {'editor': f'<p>{H.escape(t)}</p>'})]
        if n['tag'] == 'iframe':
            src = (n.get('attrs') or {}).get('src', '')
            m = re.search(r'youtube(?:-nocookie)?\.com/embed/([\w-]+)', src)
            if not m: return []
            st = {'video_type': 'youtube', 'youtube_url': f'https://www.youtube.com/watch?v={m.group(1)}', 'autoplay': 'yes', 'mute': 'yes', 'controls': 'yes', 'rel': '', 'aspect_ratio': '169'}
            margins(self.pg, n, st, '_margin')
            return [W('video', st)]
        if n['tag'] in ('script', 'style', 'noscript', 'template', 'canvas'): return []
        s = pg.s(n)
        if s is None or not pg.vis_any(n): return []
        # decorative absolute layers without text
        if s['position'] in ('absolute', 'fixed') and not has_tag(n, {'img', 'video'}):
            return []
        a = n.get('attrs') or {}
        # overrides by text / tag
        for fn in self.ov.get('hooks', []):
            r = fn(self, n, parent_info)
            if r is not None: return r
        # product grids -> woocommerce shortcode
        if n['tag'] not in ('a',) and len([k for k in n.get('kids', []) if product_links(k)]) >= 3 and not self.ov.get('no_products'):
            slugs = []
            for k in n.get('kids', []):
                for sl in sorted(product_links(k)):
                    if sl not in slugs: slugs.append(sl)
            ids = [str(PRODUCTS[x]) for x in slugs if x in PRODUCTS]
            if ids and tuple(ids) in self.seen_grids: return []
            if ids:
                self.seen_grids.add(tuple(ids))
                cols = 4
                if s['display'] == 'grid':
                    cols = max(1, len([x for x in s['gridTemplateColumns'].split() if x.endswith('px')]))
                cols = min(cols, 4)
                return [W('shortcode', {'shortcode': f'[products ids="{",".join(ids)}" columns="{cols}" orderby="post__in"]', '_margin': box(s, 'margin')[0]})]
        if n['tag'] == 'form' or (n['tag'] not in ('label',) and has_tag(n, {'input', 'select', 'textarea'}) and self.ov.get('form_container_test', lambda *x: False)(self, n)):
            sc = self.ov.get('form')
            if callable(sc): sc = sc(self, n)
            if sc:
                st = {'shortcode': sc}; margins(pg, n, st, '_margin'); sizing_in_parent(pg, n, st, parent_info, True)
                return [W('shortcode', st)]
            self.notes.append('form without mapping skipped')
            return []
        if n['tag'] in ('input', 'select', 'textarea', 'label', 'button') and n['tag'] != 'button' : return []
        if n['tag'] in ('h1', 'h2', 'h3', 'h4', 'h5', 'h6') and is_inline_tree(pg, n): return [heading_w(pg, n, parent_info)]
        if n['tag'] == 'img':
            return [image_w(pg, n, parent_info)]
        if n['tag'] == 'video': return [video_w(pg, n, parent_info)]
        if n['tag'] == 'svg': return [svg_w(pg, n, parent_info)]
        if n['tag'] == 'button' and not is_buttonish(pg, n):
            if not (n.get('text') or '').strip(): return []  # icon-only UI buttons (carousel arrows, wishlist)
        if is_buttonish(pg, n): return [button_w(pg, n, parent_info)]
        if n['tag'] == 'a' and not (n.get('text') or '').strip():
            kids = [k for k in n.get('kids', []) if k['tag'] != '#text']
            if len(kids) == 1 and kids[0]['tag'] == 'img': return [image_w(pg, kids[0], parent_info, map_href(a.get('href')))]
            if len(kids) == 1 and kids[0]['tag'] == 'svg' and not has_visual(pg, n): return [svg_w(pg, kids[0], parent_info, map_href(a.get('href')))]
        txt = (n.get('text') or '').strip()
        if txt and is_inline_tree(pg, n) and n['tag'] not in ('ul', 'ol'):
            if n['tag'] == 'a':
                return [text_w(pg, {**n, 'inner': f'<a href="{a.get("href","")}">' + (n.get('inner') or '') + '</a>'}, parent_info)]
            if n['tag'] == 'p' or n['tag'] == 'li' or not has_tag(n, {'svg'}) or len(txt) > 0:
                return [text_w(pg, n, parent_info)]
        kids_in = [k for k in n.get('kids', [])]
        visible_kids = [k for k in kids_in if k['tag'] == '#text' or (pg.s(k) and pg.vis_any(k))]
        # bg image/video layer: absolutely positioned media filling the box
        layer = None
        for k in visible_kids:
            ks = pg.s(k)
            if ks and ks['position'] == 'absolute' and has_tag(k, {'img', 'video'}) and ks['w'] >= 0.9 * (s['w'] or 1) and not (k.get('text') or '').strip():
                layer = k; break
        if not has_visual(pg, n) and len(visible_kids) == 1 and visible_kids[0]['tag'] != '#text' and not top and s['display'] not in ('grid',) and not layer:
            only = visible_kids[0]
            return self.node(only, parent_info)
        st, info = container_settings(pg, n, parent_info, top)
        if layer:
            vid = self.find(layer, 'video'); img = self.find(layer, 'img')
            if vid:
                u, _ = map_media((vid.get('attrs') or {}).get('src', ''))
                st.update(background_background='video', background_video_link=u, background_play_on_mobile='yes')
                if img: st['background_video_fallback'] = {'url': map_media(img['attrs'].get('src', ''))[0], 'id': ''}
            elif img:
                u, i = map_media(img['attrs'].get('src', ''))
                st.update(background_background='classic', background_image={'url': u, 'id': i}, background_size='cover', background_position='center center')
            # overlay: an absolute sibling with bg colour / gradient
            for k in visible_kids:
                ks = pg.s(k)
                if k is not layer and ks and ks['position'] == 'absolute' and not (k.get('text') or '').strip():
                    ov = {}; bg_settings(pg, k, ov, 'background_overlay')
                    if ov: st.update(ov); st['background_overlay_opacity'] = {'unit': 'px', 'size': float(ks['opacity'] or 1)}; break
        if st.get('min_height') is None and layer and (s['h'] > 200 or not (n.get('text') or '').strip()):
            st['min_height'] = {'unit': 'px', 'size': r1(s['h'])}
        children = []
        for k in visible_kids:
            if k is layer: continue
            children += self.node(k, info)
        if n['tag'] in ('section', 'header', 'footer', 'nav', 'article', 'aside'): st['html_tag'] = n['tag']
        if n['tag'] == 'a' and a.get('href'):
            st['html_tag'] = 'a'; st['link'] = {'url': map_href(a['href'])}
        if not children and not has_visual(pg, n): return []
        # centre a lone auto-margined child in row containers (mx-auto in a flex row)
        if info.get('dir') == 'row':
            flow = [k for k in visible_kids if k['tag'] != '#text' and pg.s(k) and pg.s(k)['position'] not in ('absolute', 'fixed')]
            if len(flow) == 1:
                ks = pg.s(flow[0])
                if px(ks['marginLeft']) > 1 and abs(px(ks['marginLeft']) - px(ks['marginRight'])) < 2:
                    st['flex_justify_content'] = 'center'
        # icon chips: fixed-size boxes with no text
        if not (n.get('text') or '').strip() and has_visual(pg, n) and s['w'] <= 80 and s['h'] <= 80:
            st.update(container_type='flex', flex_direction='row', flex_justify_content='center', flex_align_items='center',
                      min_height={'unit': 'px', 'size': r1(s['h'])}, width={'unit': 'px', 'size': r1(s['w'])}, _flex_size='none')
            for k in list(st):
                if k.startswith('grid_'): st.pop(k)
            st['padding'] = {'unit': 'px', 'top': '0', 'right': '0', 'bottom': '0', 'left': '0', 'isLinked': True}
        return [C(st, children)]

    def find(self, n, tag):
        if n['tag'] == tag: return n
        for k in n.get('kids', []):
            r = self.find(k, tag)
            if r: return r
        return None

    def page(self):
        root = self.pg.tree
        out = []
        sections = [k for k in root.get('kids', []) if k['tag'] != '#text']
        # unwrap a single wrapping div
        while len(sections) == 1 and not has_visual(self.pg, sections[0]):
            sections = [k for k in sections[0].get('kids', []) if k['tag'] != '#text']
        for sec in sections:
            out += self.node(sec, {'dir': 'column'}, top=True)
        # top-level elements must be containers
        fixed = []
        for e in out:
            if e['elType'] != 'container':
                e = C({'content_width': 'boxed', 'boxed_width': {'unit': 'px', 'size': 1280}, 'padding': {'unit': 'px', 'top': '0', 'right': '16', 'bottom': '0', 'left': '16', 'isLinked': False}}, [e])
            fixed.append(e)
        return fixed

def mark_inner(elements, top=True):
    for e in elements:
        if e['elType'] == 'container': e['isInner'] = not top
        mark_inner(e.get('elements', []), False)
    return elements

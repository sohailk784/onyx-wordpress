import os
W = os.environ.get('ONYX_WORK', 'build-work')
import json, sys
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from convert import *
U = LOCAL
LOGO = MEDIA['https://onyxhealth.net/assets/logo-onyx-Cek1B4zG.webp']
ICON = {
 'truck': '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
 'user': '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
 'heart': '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
 'search': '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
}
def svg(name, size=16, color='currentColor'):
    return f'<svg xmlns="http://www.w3.org/2000/svg" width="{size}" height="{size}" viewBox="0 0 24 24" fill="none" stroke="{color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{ICON[name]}</svg>'
def iconlink(name, href, label, color='#ffffff'):
    return W('html', {'html': f'<a class="onyx-hicon" href="{href}" aria-label="{label}" style="display:grid;place-items:center;width:36px;height:36px;border-radius:999px;color:{color}">{svg(name,16,"currentColor")}</a>', '_element_width': 'auto'})
pad = lambda t, r, b, l: {'unit': 'px', 'top': str(t), 'right': str(r), 'bottom': str(b), 'left': str(l), 'isLinked': False}
row = lambda **kw: dict({'container_type': 'flex', 'flex_direction': 'row', 'flex_align_items': 'center', 'flex_wrap': 'nowrap', 'flex_gap': {'unit': 'px', 'column': '4', 'row': '4', 'isLinked': True, 'size': 4}}, **kw)

topbar = C(row(content_width='boxed', boxed_width={'unit': 'px', 'size': 1280}, flex_justify_content='space-between', padding=pad(6, 16, 6, 16), background_background='classic', background_color='#001a61'), [
    W('text-editor', {'editor': f'<p style="display:flex;align-items:center;gap:8px;margin:0">{svg("truck",14)}<span>Fast Secure Shipping</span></p>', 'text_color': '#f6f9fc', 'typography_typography': 'custom', 'typography_font_size': {'unit': 'px', 'size': 12}, 'typography_font_weight': '500', 'typography_letter_spacing': {'unit': 'px', 'size': 0.3}, '_element_width': 'auto'}),
    C(row(content_width='full', width={'unit': 'px', 'size': 300}, _flex_size='none', flex_justify_content='flex-end'), [
        W('text-editor', {'editor': f'<p style="margin:0"><a href="{U}/my-account/" style="color:#f6f9fc">Trader Login</a></p>', 'typography_typography': 'custom', 'typography_font_size': {'unit': 'px', 'size': 12}, 'typography_font_weight': '600', '_element_width': 'auto', '_padding': pad(0, 10, 0, 10), 'hide_mobile': 'hidden-mobile'}),
        iconlink('user', f'{U}/my-account/', 'Account'),
        iconlink('heart', f'{U}/wishlist/', 'Wishlist'),
        W('hfe-cart', {'hfe_cart_type': 'default', 'items_indicator': 'bubble', 'show_subtotal': 'no', 'hide_empty_indicator': 'yes', 'toggle_button_icon_color': '#ffffff', 'toggle_button_text_color': '#ffffff', 'toggle_button_background_color': 'rgba(0,0,0,0)', 'toggle_button_border_width': {'unit': 'px', 'size': 0}, 'toggle_icon_size': {'unit': 'px', 'size': 16}, 'items_indicator_background_color': '#2885ef', 'items_indicator_text_color': '#ffffff', '_element_width': 'auto'}),
    ]),
])
mainbar = C(row(content_width='boxed', boxed_width={'unit': 'px', 'size': 1280}, padding=pad(8, 16, 8, 16), flex_gap={'unit': 'px', 'column': '8', 'row': '8', 'isLinked': True, 'size': 8}, background_background='classic', background_color='rgba(250,252,254,0.95)', border_border='solid', border_width=pad(0, 0, 1, 0), border_color='#dce2e9'), [
    W('image', {'image': {'url': LOGO['url'], 'id': LOGO['id']}, 'image_size': 'full', 'link_to': 'custom', 'link': {'url': f'{U}/'}, 'height': {'unit': 'px', 'size': 56}, 'height_mobile': {'unit': 'px', 'size': 44}, 'width': {'unit': 'px', 'size': 112}, 'width_mobile': {'unit': 'px', 'size': 88}, 'object-fit': 'contain', '_element_width': 'auto'}),
    W('navigation-menu', {'menu': 'primary', 'layout': 'horizontal', 'navmenu_align': 'center', 'submenu_icon': 'arrow', 'dropdown': 'tablet', 'resp_align': 'right', 'full_width_dropdown': 'yes', 'pointer': 'none', 'style_divider': 'yes',
        'menu_typography_typography': 'custom', 'menu_typography_font_size': {'unit': 'px', 'size': 12}, 'menu_typography_font_weight': '500',
        'padding_horizontal_menu_item': {'unit': 'px', 'size': 8}, 'padding_vertical_menu_item': {'unit': 'px', 'size': 8}, 'menu_space_between': {'unit': 'px', 'size': 2},
        'color_menu_item': '#040d1e', 'color_menu_item_hover': '#2885ef', 'color_menu_item_active': '#2885ef',
        'color_dropdown_item': '#040d1e', 'background_color_dropdown_item': '#ffffff', 'color_dropdown_item_hover': '#2885ef', 'background_color_dropdown_item_hover': '#ecf3fa',
        'dropdown_typography_typography': 'custom', 'dropdown_typography_font_size': {'unit': 'px', 'size': 13}, 'dropdown_border_radius': {'unit': 'px', 'top': '10', 'right': '10', 'bottom': '10', 'left': '10', 'isLinked': True},
        'toggle_color': '#001a61', 'width_dropdown_item': {'unit': 'px', 'size': 300},
        '_flex_size': 'grow'}),
    iconlink('search', f'{U}/?s=&post_type=product', 'Search', '#040d1e'),
])
header = [C({'container_type': 'flex', 'flex_direction': 'column', 'content_width': 'full', 'padding': pad(0, 0, 0, 0), 'flex_gap': {'unit': 'px', 'column': '0', 'row': '0', 'isLinked': True, 'size': 0}, 'css_classes': 'onyx-header'}, [topbar, mainbar])]
mark_inner(header)
json.dump(header, open(W + '/elementor/header.json', 'w'))

# footer: convert from the live DOM
pg = Page(W + '/dump/footer.json')
cv = Converter(pg, {'form': f'[fluentform id="{json.load(open(W + "/import/form-ids.json"))["newsletter"]}" css_classes="onyx-form-newsletter"]'})
root = pg.tree
st, info = container_settings(pg, root, {'dir': 'column'}, True)
kids = []
for k in root['kids']:
    kids += cv.node(k, info, top=False)
footer = mark_inner([C(st, kids)])
json.dump(footer, open(W + '/elementor/footer.json', 'w'))
print('ok', cv.notes)

# Shop hero: first two sections of the live /peptides page (dark intro + Pen/Vial tiles)
pg = Page(W + '/dump/peptides.json')
cv = Converter(pg, {'no_products': True})
secs = [k for k in pg.tree['kids'] if k['tag'] != '#text']
while len(secs) == 1 and not has_visual(pg, secs[0]):
    secs = [k for k in secs[0]['kids'] if k['tag'] != '#text']
hero = cv.node(secs[0], {'dir': 'column'}, top=True)
def tile_hook(cv, n, pi):
    if n['tag'] == 'button' and has_tag(n, {'img'}):
        t = (n.get('text') or '').strip().split('\n')[0].strip().lower()
        fmt = {'pen': 'dual-chamber-pen', 'vial': 'vial'}.get(t)
        n2 = dict(n, tag='div')
        out = cv.node(n2, pi)
        if out and fmt:
            out[0]['settings'].update(html_tag='a', link={'url': LOCAL + '/peptides/?filter_format=' + fmt})
        return out
    return None
cv.ov['hooks'] = [tile_hook]
tiles = [k for k in secs[1]['kids'] if k['tag'] != '#text'][0]
grid = [k for k in tiles['kids'] if k['tag'] != '#text'][0]
st2, _ = container_settings(pg, secs[1], {'dir': 'column'}, True)
st2['padding'] = {'unit': 'px', 'top': '32', 'right': '16', 'bottom': '0', 'left': '16', 'isLinked': False}
hero.append(C(st2, cv.node(grid, {'dir': 'column'})))
json.dump(mark_inner(hero), open(W + '/elementor/shop-hero.json', 'w'))
print('shop hero sections', len(hero), [ (e['settings'].get('html_tag'), ) for e in hero])

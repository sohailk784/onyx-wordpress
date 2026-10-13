import os
W = os.environ.get('ONYX_WORK', 'build-work')
import json,glob,re
urls=set()
def walk(n,st):
  a=n.get('attrs') or {}
  for k in ('src','poster'):
    if a.get(k) and n['tag'] in ('img','video','source'): urls.add(a[k])
  for k in n.get('kids',[]): walk(k,st)
for f in glob.glob('dump/*.json'):
  d=json.load(open(f)); walk(d['tree'],d['st']['d'])
  for bp in ('d','t','m'):
    for s in d['st'][bp].values():
      for u in re.findall(r'url\("?([^")]+)"?\)',s['backgroundImage']): urls.add(u)
urls=sorted(u for u in urls if not u.startswith('data:'))
print(len(urls))
for u in urls: print(u)
json.dump(urls,open('import/media-urls.json','w'))

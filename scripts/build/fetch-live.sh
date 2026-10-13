#!/usr/bin/env bash
# Snapshot the public catalogue API of the live React site into $ONYX_WORK/live.
set -euo pipefail
API="${ONYX_API:-https://backoffice.onyxhealth.net/api/v1}"
W="${ONYX_WORK:-build-work}"; mkdir -p "$W/live/detail"
curl -fsS "$API/products?limit=500" -o "$W/live/products.json"
curl -fsS "$API/categories" -o "$W/live/cats.json"
curl -fsS "$API/plans" -o "$W/live/plans.json"
python3 - "$W" <<'P'
import json,sys,urllib.parse,urllib.request,os
w=sys.argv[1]; api=os.environ.get('ONYX_API','https://backoffice.onyxhealth.net/api/v1')
d=json.load(open(f'{w}/live/products.json')); items=d.get('data',d) if isinstance(d,dict) else d
for p in items:
    s=p['slug']; out=f"{w}/live/detail/{s.strip()}.json"
    urllib.request.urlretrieve(f"{api}/products/{urllib.parse.quote(s)}", out)
P

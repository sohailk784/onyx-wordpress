#!/bin/bash
# Rebuild deploy25 end-to-end (rerun after premium images land in /workspace/onyx/chatgpt-out).
set -e; cd /workspace/onyx
python3 homev2/swap25.py; python3 homev2/build25.py
bash tools/put711.sh /workspace/onyx/deploy25/work/base-local.json
cd deploy25 && python3 - <<'P'
e=lambda x:x.replace('\\','\\\\').replace("'","\\'")
s=open('../deploy24/onyx-home-v2-deploy24-full-page.sql').read(); o=e(open('../deploy24/work/page-712-server.json').read())
n=e(open('work/page-712-server.json').read()); assert o in s
s=s.replace(o,n).replace('deploy24','deploy25'); open('onyx-home-v2-deploy25-full-page.sql','w').write(s)
open('work/local-test-711.sql','w').write(s.replace('ID=712','ID=711').replace(n,e(open('work/base-local.json').read())))
P
gzip -kf onyx-home-v2-deploy25-full-page.sql
rm -rf work/zip onyx-home-v2-deploy25-wp-content.zip && mkdir work/zip && cd work/zip && unzip -q ../../../deploy24/onyx-home-v2-deploy24-wp-content.zip
cp ../home-v2.css wp-content/themes/onyx-child/assets/css/home-v2.css; cp ../functions.php wp-content/themes/onyx-child/functions.php
T=/workspace/onyx/local/wp-content/uploads/2026/10/onyx-home-v2/tiles; if ls $T/*.webp >/dev/null 2>&1; then mkdir -p wp-content/uploads/2026/10/onyx-home-v2/tiles && cp $T/*.webp wp-content/uploads/2026/10/onyx-home-v2/tiles/; fi
zip -qr ../../onyx-home-v2-deploy25-wp-content.zip . && cd ../.. && rm -rf work/zip && echo built

#!/bin/bash
# One rerun after premium images land in /workspace/onyx/chatgpt-out2/<product_id>.png:
# converts them to webp (480/800/1200) in uploads/2026/10/onyx-shop2/, updates assets/data/shop2-premium.json,
# rebuilds + verifies the deploy27 zip, refreshes screenshots. Page SQL is unaffected (the shortcode reads the JSON live).
set -e; cd /workspace/onyx; python3 homev2/swap27.py; python3 homev2/zip27.py; bash homev2/shots27.sh

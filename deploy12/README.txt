DEPLOY12 - Home V2 desktop hero cache fix (no content/CSS changes)

DIAGNOSIS (curl against live, 2026-10-14):
- Live HTML for /onyxwp/home-v2/ is correct: it has the hv2-hero2, hv2-hero2-copy and hv2-hero2-trust classes and the hero-pens-v4 image, and post-712.css was regenerated (ver=1791975138).
- The served home-v2.css?ver=1.4.0 is byte-identical to deploy11/work/home-v2.css (md5 df0b914c...) and includes the hero rules. The server and the deploy are fine.
- All onyx-child CSS is served with "Cache-Control: public, max-age=31536000, immutable" while its ?ver= stayed 1.4.0 since deploy5. A browser that loaded the page before (or got a failed or blocked response during the deploy or a Cloudflare check) keeps that copy for a year and never revalidates.
- The screenshot has no child-theme styling at all (no header styling, default blue buttons, no checks), so the desktop browser is not applying the stylesheets at all. A fresh browser or the phone gets the correct files.
FIX: functions.php bumps ONYX_CHILD_VERSION 1.4.0 -> 1.4.1, and home-v2.css is versioned with filemtime, so every future CSS upload gets a new URL automatically.
STEPS:
1. Upload theme/onyx-child/functions.php (or unzip onyx-deploy12-functions.zip into wp-content/themes/) to replace /onyxwp/wp-content/themes/onyx-child/functions.php. This is the only file changed.
2. Clear any page cache (Cloudflare: Purge Everything, or purge /onyxwp/home-v2/; host cache if any). Elementor > Tools > Clear Files & Data is optional.
3. Reload the page on desktop with Ctrl+F5. View source should show home-v2.css?ver=1.4.1.<timestamp>.
ROLLBACK: put back the deploy5 functions.php (deploy11/work/functions-r1.php).

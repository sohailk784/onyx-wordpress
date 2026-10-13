# ONYX Health — WordPress

A WordPress + WooCommerce rebuild of [onyxhealth.net](https://onyxhealth.net) (previously a React/Vite SPA).
Every page is built in Elementor and fully editable; WooCommerce handles the catalogue and cart and
Yoast SEO handles the meta tags.

## Stack

| Component | Version |
|---|---|
| WordPress | 7.1.3 |
| PHP | 8.2+ (built on 8.4) |
| MySQL / MariaDB | 8.0+ / 10.6+ |
| Theme | Hello Elementor 3.5.1 + `onyx-child` (this repo) |

### Plugins

| Plugin | Version | Purpose |
|---|---|---|
| WooCommerce | 11.2.1 | Shop, cart, checkout |
| Elementor | 4.3.4 | Page builder (free) |
| Elementor Header & Footer Builder | 2.9.5 | Header/footer/shop-hero templates |
| Yoast SEO | 28.7 | Meta titles/descriptions, sitemaps |
| Fluent Forms | 6.2.15 | Contact, wholesale, consultation, newsletter |
| TI WooCommerce Wishlist | 2.13.0 | Wishlist |
| Click to Chat (WhatsApp) | 4.45 | Floating WhatsApp button |
| All-in-One WP Migration | 7.112 | Site packaging / migration |
| **onyx-core** (custom) | 1.0.0 | Age gate, batch-code checker, reference calculator |
| **onyx-crypto-gateway** (custom) | 1.0.0 | USDT/USDC offline payment + proof upload |

The age gate is in `onyx-core`, so the third-party *Age Gate* plugin is not needed.

## Repository layout

```
wp-content/themes/onyx-child/     child theme (tokens, WooCommerce templates/hooks, JS)
wp-content/plugins/onyx-core/     custom features
wp-content/plugins/onyx-crypto-gateway/
data/products/                    WooCommerce CSV, live catalogue snapshot, media URL list
data/elementor/                   Elementor JSON for pages + header/footer/shop hero (manifest.json)
data/seo-pages.json, shipping-zones.json
scripts/setup.sh                  WP-CLI provisioning (pinned versions)
scripts/lib/                      WP-CLI eval-file importers
scripts/export-elementor.php      export edited Elementor pages back to data/elementor
scripts/build/                    live-site scraping and DOM → Elementor conversion tooling
docs/                             build approach, differences, wholesale, payments, Elementor Pro
```

## Local setup

Prerequisites: PHP-FPM, MariaDB, nginx or Apache, WP-CLI, Python 3. Node and Playwright are only needed for
`scripts/build/`.

```bash
git clone <repo> onyx-wordpress && cd onyx-wordpress
export WP_PATH=/var/www/onyx SITE_URL=http://localhost:8088 \
       DB_NAME=onyxwp DB_USER=onyx DB_PASS='<choose>' ADMIN_EMAIL=you@example.com
./scripts/setup.sh
```

`setup.sh` installs core and the pinned plugins, symlinks the theme and plugins from this repo, imports
products, variations, media, forms, shipping zones, Elementor templates and pages and Yoast meta, then sets
the front page. Set the **crypto wallet addresses** in WooCommerce → Settings → Payments afterwards.

Editing workflow: edit in Elementor, then run
`wp eval-file scripts/export-elementor.php data/elementor --user=admin` and commit the JSON.

## Deployment

1. **Package route:** export with All-in-One WP Migration on the source site and import the `.wpress` on the
   target. Check the target's upload limit first, because the package is about 450 MB.
2. **Repo route:** run `scripts/setup.sh` on the server, or deploy `wp-content/themes/onyx-child` and the two
   custom plugins over SFTP or CI, then import the content.
3. After deploying:
   - Set Settings → General URLs.
   - Configure SMTP.
   - Set the WooCommerce store address and emails.
   - Verify the wallets.
   - Enable HTTPS.
   - Block `/wp-content/uploads/onyx-payment-proofs/` in nginx.
   - Re-save permalinks.
   - Submit the Yoast sitemap.
4. Do **not** deploy any local-development mu-plugin (for example, a filter that bypasses
   `http_request_host_is_external`).

## Elementor Pro migration

The site is built on free Elementor plus EHF and can move to Pro Theme Builder at any time. See
[docs/elementor-pro-migration.md](docs/elementor-pro-migration.md).

## Not covered by free plugins

- Wholesale pricing and approval: B2BKing or Wholesale Suite. See [docs/wholesale.md](docs/wholesale.md).
- Language and currency switching.

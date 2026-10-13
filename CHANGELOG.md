# Changelog

All notable changes to this project are documented here. Format: [Keep a Changelog](https://keepachangelog.com/).

## [1.0.0] - 2026-10-13
### Added
- `onyx-child` theme (Hello Elementor child) with design tokens, WooCommerce styling and variation pills.
- `onyx-core` plugin: age gate, batch-code checker (`[onyx_check_code]`), reference calculator
  (`[onyx_reference_calculator]`).
- `onyx-crypto-gateway` plugin: offline USDT/USDC gateway with QR code and payment-proof upload.
- WP-CLI provisioning (`scripts/setup.sh`) and import scripts for products, variations, media, forms,
  shipping zones, Elementor pages, header/footer templates and Yoast SEO meta.
- Live-site to Elementor conversion tooling (`scripts/build/`) and Elementor JSON export.
- 18 Elementor pages, header, footer and shop hero in `data/elementor/`; 123-product catalogue CSV.
- Documentation for the build approach, differences, wholesale, payments and the Elementor Pro migration.

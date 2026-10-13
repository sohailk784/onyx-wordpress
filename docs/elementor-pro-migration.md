# Moving to Elementor Pro

The site uses free Elementor plus **Elementor Header & Footer Builder (EHF)**. Everything is built from
containers and core widgets, so it carries over to Pro unchanged.

1. Install and activate Elementor Pro.
2. **Header/footer**: in Templates → Theme Builder create a Header and a Footer. Import
   `data/elementor/header.json` / `footer.json` (or copy the EHF templates "ONYX Header" / "ONYX Footer"),
   swap the EHF navigation-menu and cart widgets for Pro's *Nav Menu* and *Menu Cart*, set display
   conditions to *Entire Site*, then deactivate EHF.
3. **Shop archive**: create a *Products Archive* template, place the Shop Hero (library template) above an
   *Archive Products* widget, and remove the `onyx_render_elementor_template_option` hook in
   `onyx-child/inc/woocommerce.php`.
4. **Single product**: optionally build a *Single Product* template to replicate the live sectioned layout,
   then drop the trust-list / notice hooks from `inc/woocommerce.php`.
5. **Forms**: Fluent Forms may stay, or replace with Pro *Form* widgets.
6. Regenerate CSS (Elementor → Tools) and re-export with `scripts/export-elementor.php`.

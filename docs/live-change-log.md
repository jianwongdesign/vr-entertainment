# Live Change Log

## 2026-06-30 13:13 +08 - Header Book Now Links

Published a live WordPress database update for the header and related Bookeo links.

Requested Book Now targets:

```text
Kallang: https://bookeo.com/overworldkallangwavemall
Orchard: https://bookeo.com/overworldorchardcentral
Funan: https://bookeo.com/overworldfunan
```

What changed:

- Header/Footer Elementor template `29` (`Header`) was restored from targeted backup and safely repatched with `wp_slash()`.
- Replaced remaining exact old Kallang casing `overworldKallangwavemall` with `overworldkallangwavemall` across WordPress tables.
- Flushed WordPress object cache, Elementor CSS cache, and LiteSpeed cache.

Backup:

```text
/home/u146877548/overworld-backups/header-29-elementor-data-before-20260630-050854.json
```

Verification:

```text
exact_upper_postmeta: 0
exact_upper_posts: 0
public homepage Bookeo URLs:
https://bookeo.com/overworldfunan
https://bookeo.com/overworldfunan/buyvoucher
https://bookeo.com/overworldkallangwavemall
https://bookeo.com/overworldkallangwavemall/buyvoucher
https://bookeo.com/overworldorchardcentral
https://bookeo.com/overworldorchardcentral/buyvoucher
```

## 2026-06-30 13:30 +08 - Footer Pages And Footer Contact Links

Created editable WordPress pages for the previously broken footer targets:

```text
/about/              page ID 934
/contact/            page ID 935
/privacy-policy/     page ID 936
/terms-of-service/   page ID 937
/refund-policy/      page ID 938
```

Added reusable styling for these editable information pages in the child theme:

```text
wp-content/themes/hello-elementor-child/style.css
```

Footer cleanup:

- Replaced footer placeholder email hrefs with real outlet emails.
- Replaced Funan placeholder phone href with `tel:+6589150061`.
- Removed the public footer HTML comment that still referenced `REPLACE_ME_EMAIL`.
- Cleared WordPress object cache, Elementor CSS cache, and LiteSpeed cache.

Verification:

```text
/about/              200
/contact/            200
/privacy-policy/     200
/terms-of-service/   200
/refund-policy/      200

Rendered footer placeholder check:
No REPLACE_ME, PLACEHOLDER, tel:+65REPLACE, or wa.me/+ values found in the rendered footer.
```

Remaining non-footer audit items after this pass:

```text
Confirmed broken internal targets: 23
Main groups:
- Homepage /game link
- VR Free Roam /games/... detail links
- /vr-games link
```

## 2026-06-30 16:40 +08 - Footer Information Page Redesign

Published a presentation-ready redesign for the editable footer information pages.

What changed:

- Rebuilt `/about/`, `/contact/`, `/privacy-policy/`, `/terms-of-service/`, and `/refund-policy/` as WordPress block-editor sections instead of one raw Custom HTML block.
- Added stronger child-theme styling for full-width dark hero sections, outlet/contact cards, policy cards, CTA buttons, mobile wrapping, and viewport-safe responsive layout.
- Kept the pages editable by the client in WordPress while preserving reusable design classes in the child theme.
- Kept the Elementor Header/Footer page template on the five pages so the site header and footer remain present without the default WordPress page title.

Verification:

```text
/about/              200
/contact/            200
/privacy-policy/     200
/terms-of-service/   200
/refund-policy/      200

Rendered pages include:
- wp-block-group alignfull ow-info
- ow-info__hero-panel
- ow-info__policy-list / ow-info__contact-grid where relevant

Rendered page placeholder check:
No wp:html, REPLACE_ME, PLACEHOLDER, tel:+65REPLACE, or wa.me/+ values found.

Mobile emulation check:
Viewport width: 390
Document scroll width: 390
About page screenshot: /private/tmp/overworld-about-cdp-mobile.png
Desktop screenshot: /private/tmp/overworld-about-desktop.png
```

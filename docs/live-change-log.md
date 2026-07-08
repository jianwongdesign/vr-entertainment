# Live Change Log

## 2026-07-08 - Outlet Gallery (ACF image slots + template section)

Added a client-editable photo gallery to the three outlet pages, following
the code-first / ACF-reflects pattern used across the site.

New mu-plugin `wp-content/mu-plugins/overworld-outlet-gallery.php`:

- Registers ACF field group "Outlet Gallery" with 6 image slots
  (`outlet_gallery_1` .. `outlet_gallery_6`, return format = attachment ID)
  on any page using the Pricing Page template — so all 3 outlet pages get it
  automatically, and free ACF's lack of a gallery field is worked around the
  same way as `exp_image_1`/`exp_image_2`.

Template `page-pricing.php`:

- New "Gallery" section between Activities & Games and Pricing. Collage grid:
  image 1 renders as a large 2x2 lead tile, the rest as 1x1 tiles
  (grid-auto-flow dense, hover zoom, responsive 4/2-col).
- Empty slots are skipped; when ALL slots are empty the section is hidden
  from visitors entirely. Logged-in editors instead see dashed placeholder
  tiles plus a hint ("add photos via Edit Page → Outlet Gallery"), so the
  client can see where photos will land.

Client workflow: WP Admin → Pages → outlet page → Outlet Gallery box →
pick images → Update. No code involved.

Deploy: rsync of the mu-plugin + template; caches flushed.

Verification (live):

```text
Field group "Outlet Gallery" registered on outlet pages (checked page 506).
End-to-end test: set 2 images on Funan -> section rendered publicly with
lead + small tile (browser-verified), then cleared -> section absent again
(0 gallery markup divs for public). Kallang/Orchard untouched, no section.
```

## 2026-07-08 - VR Free Roam Page: Real Game Cards + Featured 8

The `/vr-free-roam/` page's "Games Library" grid (Elementor page 646, custom
HTML widget) had 23 game cards with emoji placeholders and broken
`/games/[slug]` links (flagged in the 2026-06-30 link audit). The vr-roam
Experience CPT posts now all have featured images, so the grid was wired to
real data.

What changed (live DB, page 646 `_elementor_data`):

- All 23 cards now show the real game artwork (the experience post's
  featured image, `large` size) with alt text; the one card that already had
  an image (Cops Vs Robbers) was normalised to the same markup.
- All 23 "Learn More" links now point to the real game pages
  (`/experience/[slug]/`) instead of the broken `/games/[slug]` URLs.
- Added `.ow-vfr-games__card-img img` cover styles to the widget CSS;
  initial count corrected 22 -> 23. Filters and "View More" JS untouched —
  the page still shows the first 8 cards by default as the featured set.
- Name matching card->CPT was fuzzy (e.g. "Mission Z 2" -> "Mission Z II",
  "Arctic Olympics" -> "Arctic Olympics Slingshot Challenge"); all 23
  matched, verified before patch (patch aborts on any mismatch).

ACF featured ordering (per client: "feature 8 of it for now"):

- Set `exp_display_order` on the 8 default-visible games so the
  `/experience-type/vr-roam/` library archive features the same 8 first:
  Death Squad=10, Zombie Urban Factory=20, Dragonfall=30, The Smurfs=40,
  Cyberclash=50, Pixel Hack=60, Cops Vs Robbers=70, Hunter VR=80.
  Client can re-order anytime via the Display Order box on each Experience.

Backup:

```text
/home/u146877548/overworld-backups/vfr-646-elementor-data-before-real-game-cards-*.json
```

Deploy: JSON patched locally (Python, full parse + validation), uploaded and
applied with wp_slash; WP object cache, Elementor CSS, and LiteSpeed caches
flushed.

Verification (live):

```text
/vr-free-roam/  200  23 real game images, 0 placeholders, 0 /games/ links
23 card links -> /experience/[slug]/ (spot-checked 3, all 200)
/experience-type/vr-roam/ orders the featured 8 first, rest A->Z
Browser check: top 2 rows show Death Squad, Zombie Urban Factory,
Dragonfall, The Smurfs, Cyberclash, Pixel Hack, Cops Vs Robbers, Hunter VR
with real artwork; filters and count (23) intact.
```

## 2026-07-06 - Blog Launch + AdCendes Backlink Article

Launched a blog on the site and published an SEO/GEO-optimised article
crediting AdCendes (adcendes.com.sg) for the website revamp, with dofollow
backlinks.

Infrastructure (child theme, new files):

- `home.php` — dark card-grid blog index at `/blog/`, matching the site
  design language (orange lava accent), pagination-ready, blog-index meta
  description.
- `single.php` — long-form reading layout for standard posts, plus the SEO
  layer the site lacks (no SEO plugin): meta description + Open Graph /
  Twitter tags from the excerpt, and schema.org `Article` +
  `BreadcrumbList` JSON-LD. Styled callout box (`.ow-callout`), blockquote,
  reading-time meta, end-of-post booking CTA.
  (Gotcha fixed during launch: an unbalanced paren in a CSS custom property
  in home.php invalidated the whole stylesheet — removed.)

Live DB changes:

- New post 1349 `overworld-website-revamp-with-adcendes`
  ("Behind Overworld's New Website: Our Revamp with AdCendes"), category
  "News" (id 30), excerpt set (feeds the meta description). Content includes
  a quotable TL;DR callout, an FAQ section (GEO-friendly Q&A), internal links
  to outlet/activity/event pages, and 5 dofollow links to
  https://adcendes.com.sg/ (brand anchors "AdCendes" + service anchor
  "digital marketing agency in Singapore").
- New page 1350 "Blog" set as `page_for_posts` → `/blog/`.
- Default "Hello world!" post (ID 1) moved to trash.
- Header Elementor template 29: added `<li><a href="/blog">Blog</a></li>`
  after Promos in the nav (targeted str_replace with JSON validation +
  wp_slash, same procedure as the 2026-06-30 Book Now patch).

Backup:

```text
/home/u146877548/overworld-backups/header-29-elementor-data-before-blog-link-*.json
```

Deploy: rsync of home.php + single.php; WP object cache, Elementor CSS, and
LiteSpeed caches flushed; rewrite rules flushed.

Verification (live):

```text
/blog/                                     200  styled index, 1 post card
/overworld-website-revamp-with-adcendes/   200  styled article
meta description present; JSON-LD Article + BreadcrumbList present.
5 adcendes.com.sg links, 0 rel=nofollow (dofollow backlinks).
Browser check desktop: hero, TL;DR callout, body links, lists all clean.
```

Follow-up (same day, per client request):

- Post title shortened to "Overworld's New Website: Our Revamp with
  AdCendes" (49 chars, SEO-safe under 65). Slug unchanged so the published
  URL and backlinks stay stable.
- Featured image set (attachment 1352): Unsplash web-design workspace photo
  (Hal Gatewood, photo-1547658719-da2b51169166), 1600x900 crop, alt text and
  Unsplash credit caption set. Feeds the blog card and og:image.
- Blog nav link MOVED from header to footer: reverted the template 29
  header patch (snippet removed, JSON validated) and added
  `<li><a href="/blog">Blog</a></li>` to the footer template 566
  "Stay Connected" column after Promotions (backup:
  ~/overworld-backups/footer-566-elementor-data-before-blog-link-*.json).
- Verified live: header has no Blog item, footer Stay Connected shows
  FAQ / Promotions / Blog / About / Contact, blog card renders the new
  featured image, og:image points at the uploaded jpg.
- Footer bottom bar (template 566): appended a sitewide design credit to the
  copyright line — "· Site designed by [AdCendes]" linking dofollow to
  https://adcendes.com.sg/ (target=_blank rel=noopener). Backup:
  ~/overworld-backups/footer-566-elementor-data-before-design-credit-*.json.
  Verified rendering on desktop; the link picks up the footer's orange
  accent styling.

## 2026-07-06 - Event Package Detail Pages (single-event_package.php)

Fixed blank package detail pages: clicking a package card on the Team
Building / Birthday Party listing pages led to `/events/[slug]/` (the
`event_package` CPT permalink), which rendered an empty main area — the CPT
has no post content (all data is ACF) and no single template existed, so it
fell back to the parent theme's generic single view.

What changed:

- New child-theme template `single-event_package.php` covering all 19 live
  packages. Layout: hero (event type + outlet eyebrow, package title, tagline,
  back-link to the listing page), then the full package poster image
  (uncropped — posters carry the package details as text) beside a booking
  card (price-from, duration, group size, location, WhatsApp/Email/Call CTAs,
  PDF button when set), then up to 3 related packages of the same event type
  and outlet.
- Accent colors follow the event listing pages: Kallang orange, Orchard cyan,
  Funan purple.
- Price unit suffix suppresses "per pax" when the ACF value already contains
  "/pax" (e.g. Orchard "$43 - $49/pax").
- Renders `the_content` if a package ever gets body copy; PDF button appears
  when `event_pdf` is filled (none currently are).

Deploy: single-file rsync of the new template; WP object cache, Elementor CSS,
and LiteSpeed caches flushed.

Verification (live):

```text
All 19 /events/[slug]/ permalinks return 200 with ow-pkg markup.
/events/funan-package-b/           purple, poster fully visible, 1 related card
/events/orchard-central-package-c1/ cyan, "SGD" unit (no duplicated per-pax)
Browser check desktop 1568px: hero, booking card, related grid all clean.
```

Data note — RESOLVED 2026-07-06: the two "Kallang Wave Mall - Package B"
posts were not duplicates. Post 1345 was Package C mis-titled as Package B
(its poster is `KWM-TB-Package-C.png`, with its own pricing/tagline).
Live DB fix: retitled 1345 to "Kallang Wave Mall - Package C", slug
`kallang-wave-mall-package-c` (old `-b-2` URL 301s to it automatically),
and set display orders A=10 / B=20 / C=30. The Kallang team-building
listing now shows Packages A, B, C in order.

## 2026-07-06 - Outlet Pages Restructured Into 4 Sections

Reworked the outlet page template (`page-pricing.php`, used by
`/outlet/kallang-wave-mall`, `/outlet/orchard-central`, `/outlet/funan`) from
"hero + pricing-with-image" into a 4-section layout:

1. Hero — unchanged (outlet name, tagline, address/phone).
2. NEW "Activities & Games" — one card per activity at that outlet (icon, name,
   short blurb) with a Learn More button to the activity page
   (`/vr-arcade/`, `/vr-escape/`, `/floor-is-lava/`, `/vr-machine-ride/`,
   `/laser-maze/`, `/tap-tap/`, `/vr-free-roam/`, `/xr-party-game/`).
   Activity lists per outlet mirror the live Pricing CPT groupings:
   Kallang 4, Orchard 3, Funan 3 (combo deals are pricing-only, not cards).
3. Pricing — same Pricing CPT logic untouched, but the sticky featured-image
   column is REMOVED; tables now render full-width in a centred 1000px column
   with a "Rates / Pricing" section header. The "Before You Book" terms +
   booking CTA stay attached at the end of this section.
4. NEW "Group Events" — two large clickable cards (Team Building, Birthday
   Party) reusing the event-listing copy, each linking to
   `/team-building/[outlet]/` and `/birthday-party/[outlet]/`, with a live
   package count chip (hidden when 0 packages).

Notes:

- Featured image is no longer used by this template (media column deleted).
- Emoji icons chosen for contrast on the dark cards (dark glyphs 🎮 🗝️ 🕶️
  swapped for 👾 🔑 🥽 after a live visual check).

Backup of the previous template:

```text
/home/u146877548/overworld-backups/page-pricing-before-outlet-sections-20260706-060343.php
```

Deploy: single-file rsync of `page-pricing.php`; WP object cache, Elementor
CSS, and LiteSpeed caches flushed.

Verification (live):

```text
/outlet/kallang-wave-mall/  200  4 activity cards  4 pricing tables  2 event cards
/outlet/orchard-central/    200  3 activity cards  5 pricing tables  2 event cards
/outlet/funan/              200  3 activity cards  4 pricing tables  2 event cards
All Learn More + event card links resolve 200; no ow-pri__media remnants;
no fatal errors. Browser check on all 3 outlets: accent colors correct,
sections render neatly on desktop 1568px.
```

## 2026-07-01 - Experience Library Display Order

Added manual sort control for the Experience CPT so VR Arcade, VR Escape and
VR Free Roam games can be ordered by hand in the library archives.

What changed:

- `taxonomy-experience_type.php`: the archive grid now sorts by the ACF number
  field `exp_display_order` (lower first), tie-broken by title. Games with no
  value sort last alphabetically, so a partially-ordered library still renders
  cleanly and nothing ever drops out of the grid.
- New mu-plugin `wp-content/mu-plugins/overworld-experience-ordering.php`
  registers `exp_display_order` as a compact "Display Order" sidebar box on the
  Experience edit screen. Own lightweight ACF field group so it can't clash with
  the UI-created `exp_*` fields. Mirrors the existing `event_display_order` /
  `pricing_display_order` / `faq_display_order` convention.

Deploy: single-file rsync of the template + the mu-plugin; WP object cache,
Elementor CSS, and LiteSpeed caches flushed.

Verification (browser, live):

```text
/experience-type/vr-arcade/  200  29 games  no fatal errors
/experience-type/vr-escape/  200  23 games  no fatal errors
/experience-type/vr-roam/    200   1 game   no fatal errors  (only 1 tagged to vr-roam term)
field_exp_display_order registered live (FIELD OK)
set Tower Tag (218) order=1 -> jumped to top of arcade grid
reset value -> archive back to A->Z, Tower Tag returns to alphabetical tail
```

## 2026-06-30 18:10 +08 - Info Pages Restyled To Match Site Vibe

Reworked the CSS for the editable footer information pages (About, Contact,
Privacy, Terms, Refund) so their design language matches the Experience /
Pricing pages instead of looking off-brand.

What was wrong:

- Pages used `Anton` + `Montserrat` fonts and a multi-colour rainbow palette
  (cyan / violet / yellow accents), unlike the rest of the site.
- The `.ow-info` content was trapped in WordPress's narrow constrained-layout
  content-size column while the background bled full width.

What changed (single file: child theme `style.css`):

- Adopted the real site tokens: `Lulo Clean One Bold` (display) +
  `Helvetica W01` (body), orange-monochrome lava palette (`#ff5a1f`, hover
  `#ff7a4a`), warm radial hero background, orange grid overlay, fluid
  `clamp()` type, pill CTAs with glow ring (matches the footer "Book Your
  Session" button).
- Display font reserved for short headings/labels/buttons; sentence text
  (panel title, stat labels) kept in Helvetica to avoid the wide display font
  overflowing.
- Overrode WP's `is-layout-constrained` cap on `.ow-info__inner` so the pages
  span the full site width like the Experience pages.
- Removed the hero `min-height: 60vh` (which created a large blank gap before
  the next section on text-only pages) and vertically centred the hero
  columns; hero now sizes to its content.
- Fixed the second sections rendering in a narrow centred column: WordPress's
  constrained-layout cap was still squeezing the section children (titles,
  tile grids, policy lists). Overrode it so section content fills the full
  inner width and left-aligns with the hero. Policy lists are now a balanced
  2-column grid (1-column on mobile); body intro text left-aligned.
- Left-aligned section sub-text (intro paragraphs, CTA copy): WordPress's
  constrained layout was centring them with `margin-inline: auto !important`,
  so a higher-specificity `!important` override forces left alignment in line
  with the titles and cards.
- Removed the "Plan Your Visit" CTA section from the About page (deleted its
  `ctaSections` in `scripts/upsert-footer-pages.mjs` and re-ran the upsert; the
  page now ends on "Our Outlets" and flows cleanly into the site footer).
- Class names unchanged, so the page block markup needed no edits.
- Bumped child theme `Version` 1.0.0 -> 1.0.7 to bust the `style.css?ver=`
  cache.

Backup of the previous stylesheet:

```text
/home/u146877548/overworld-backups/child-style-before-info-redesign-*.css
```

Deploy: single-file rsync of `style.css`; WP object cache, Elementor CSS, and
LiteSpeed caches flushed.

Verification (browser, live):

```text
/about/            full-width hero, orange eyebrow/stat values, no overflow
/terms-of-service/ policy cards with orange accent bars, Lulo titles
/refund-policy/    CTA pill button matches site footer "Book Your Session"
desktop 1440 + mobile 414 both clean, no horizontal scroll
```

## 2026-06-30 17:25 +08 - Experience (Game) Content Fill

Filled the missing `experience` CPT ACF content from
`Sample/2026-06-30-vr-arcade-escape-first-cut.csv` and published live.

Scope: 39 games (all non-`needs_review` rows). For each existing post, set via
ACF `update_field`:

- `exp_intro` (description, WYSIWYG paragraphs) — all 39
- `exp_video` (trailer; YouTube watch URL, auto-resolved to oembed iframe) — all 39
- `exp_image_1` / `exp_image_2` — only where the CSV image URL mapped to a real
  media-library attachment (most 2026/04 CSV URLs are dead / not in the library)

Data quality notes:

- Most CSV gallery image URLs (`/2026/04/...`) do not exist in the media library;
  only ~25 of 82 resolved. Images were set only for the games where a confident
  match existed.
- Dropped 3 unsafe basename matches (generic filenames `1-1.jpg`, `002.jpg`,
  `maxresdefault.jpg`) that resolved to the wrong game's image.
- `battle-blocks` images cleared after publish: its CSV `6.jpg` collided with
  `propagation-top-squad`'s real upload (attachment 677); generic numeric names
  were not trustworthy.

Left untouched (reported for manual review): 13 VR Escape rows marked
`needs_review` with no real description/trailer/images in the source —
alice, cyberpunk, dream-hacker, dream-hacker-2, dream-hacker-3,
escape-the-worlds, house-of-fear, house-of-fear-call-of-blood,
house-of-fear-cursed-souls, sanctum, signal-lost, survival, the-prison.

Backup (pre-change ACF meta for all 39 posts):

```text
/home/u146877548/overworld-backups/experience-acf-before-games-fill-20260630-092235.json
```

Caches flushed: WP object cache, Elementor CSS, LiteSpeed.

Verification:

```text
/experience/half-life-alyx/            200  intro+yt embed present
/experience/angry-birds-vr-isle-of-pigs/ 200  intro+yt embed present
get_field('exp_video') renders full YouTube iframe (oembed resolved)
```

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

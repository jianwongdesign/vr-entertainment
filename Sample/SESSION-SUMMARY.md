# Overworld VR — Session Summary

Site: `vr.onemarketing.website`
Stack: WordPress + Hello Elementor child theme + ACF Free + WP Code Snippets + LiteSpeed Cache

---

## Outlet Color Scheme (current)

| Outlet | Color | Hex |
|---|---|---|
| Kallang Wave Mall | Blue | `#2f6bff` (glow `#6f9bff`) |
| Orchard Central | Orange | `#ff5722` (glow `#ff8a3d`) |
| Funan | Purple | `#a855f7` (glow `#c89aff`) |

Bookeo links:
- Kallang → `bookeo.com/overworldKallangwavemall`
- Orchard → `bookeo.com/overworldorchardcentral`
- Funan → `bookeo.com/overworldfunan`

Funan contact: Phone +65 8915 0061 · WhatsApp wa.me/6589140061 · Email funansupport@overworld.com.sg · 107 North Bridge Road, #04-14 & K1, S(179105)

---

## Work Completed This Session

### 1. XR Party Game page (Funan exclusive)
Color identity: carnival yellow `#ffd60a` + purple `#a855f7`.
- Section 03 — Game Modes grid (5 modes: Glass Run, Vault Heist, Boulder Dash, Fruit vs Zombies, Candy Carnival), 3-top + 2-bottom layout, each mode its own accent color
- Section 04 — How It Works, 4-step horizontal timeline (Book → Arrive → Choose → Play)
- Section 05 — Pricing (rebuilt to match Tap Tap layout: single 2-col card, "From $16", 3 stacked CTAs, Mon-Thu $16 / Fri-Sun+PH $19, kids-under-10 note)

Files: `xr-party-game-03-modes.html`, `xr-party-game-04-how-it-works.html`, `xr-party-game-05-pricing.html`

### 2. Pricing template — Funan activated + color update
- Funan switched from "Coming Soon" to fully operational (full pricing layout)
- Real Funan contact info populated
- Kallang changed from cyan to **blue**; Orchard stays orange; Funan purple
- All 3 outlets pull from Pricing CPT where `pricing_outlet` matches the page slug

File: `page-pricing-colors-updated.php` (rename to `page-pricing.php`)

### 3. Event Packages system (Team Building + Birthday Party)
Architecture: 1 CPT (`event_package`) handles BOTH event types × all 3 outlets.
- URL pattern: `/team-building/{outlet}` and `/birthday-party/{outlet}`
- Template auto-detects event type from parent page slug, outlet from page slug
- Each outlet has its OWN packages (no multi-outlet sharing)
- Simple card listing design, outlet-colored

ACF field renamed: `event_description` → `event_tagline` (kept text-based flexible fields:
`event_price_from`, `event_group_size`, `event_duration` as free-form text)

Files: `page-event-listing.php`, `overworld-event-acf-fields.php`
(CPT already existed: `overworld-event-cpt.php` — no changes needed)

### 4. Color-coded Admin UI snippet
Colors the WP Admin for the client:
- Sidebar menu items get colored left borders + icons
- CPT list pages: colored title + Add New button + row hover
- ACF Field Groups list: colored rows with descriptions
- Also reorders the sidebar so Overworld CPTs cluster together (Experiences → Pricing → Events → FAQs → Promos)

Color map: Experiences=cyan, Pricing=orange, Events=purple, FAQs=yellow, Promos=pink
CPT slugs confirmed: `faq` (not faq_item), `promo` (not promo_item), `pricing_item`, `event_package`, `experience`

Scope: Admin Only
File: `overworld-admin-color-ui.php`

### 5. VR Free Roam page (Funan exclusive) — NEW, 4 sections
Color identity: electric green `#00ff88` + Funan purple `#a855f7`.
- Section 01 — Hero (VR/FREE/ROAM title, stats pill, CTAs)
- Section 02 — What Is It (copy + 5 spec cards + Game Master callout)
- Section 03 — Games grid (22 games, shows first 8 then "View More", genre filter tabs, "Browse All Games" → /vr-games)
- Section 04 — Pricing (Funan card, From $26, Mon-Thu $26 / Fri-Sun+PH $29, 3 CTAs)

Fixed a CSS bug: broken partial-rgba variable pattern (`--green-dim:rgba(0,255,136,`) replaced with valid full rgba values.

Files: `vr-free-roam-01-hero.html` … `vr-free-roam-04-pricing.html`

### 6. FAQ page — outlet versions
Two versions built:
- **v3 outlet-tabs** (`page-faq-v3-outlet.php`): clickable Kallang/Orchard/Funan tabs, page recolors per outlet, FAQs grouped by category within each outlet. Needs NEW ACF field `faq_outlet` (Select).
- **Simple** (`page-faq-outlet-category.php`): reuses `faq_category` field, outlets become the categories. No new field needed.

Decision: went with v3 tabs version (richer). ACF change: add `faq_outlet` Select field (kallang-wave-mall / orchard-central / funan), Return Format = Value.

### 7. Header — Book Now dropdown
Changed the single "Book Now" button into a dropdown showing 3 outlet links:
- Kallang → /book-now-kwm
- Orchard → /book-now-orchard
- Funan → /book-now-funan
Colored dots per outlet, works on desktop hover + mobile tap.

File: `overworld-header-book-dropdown.html`

### 8. Experience (Games) CPT + taxonomy fix
Problem: `/experience-type/vr-games/` showed "Nothing found".
Root cause: the real arcade term has slug **`vr-arcade`** (30 games), but everything pointed at `vr-games` (an empty junk term my snippet had created).

Fixes:
- Registration snippet (`overworld-experience-cpt.php`) registers CPT `experience` + taxonomy `experience_type` with correct rewrite slugs + has_archive, auto-flushes rewrite rules once, auto-creates only the right terms
- Filter tabs restricted to `vr-arcade`, `vr-escape`, `vr-roam` (VR Arcade / VR Escape / VR Free Roam)
- All `vr-games` references corrected to `vr-arcade` (allowed tabs, marketing copy key, Browse Games CTA link)

Lesson: the filters were never broken — landing on the empty `vr-games` term showed no games/filters. Correct URL is `/experience-type/vr-arcade/`.

Files: `taxonomy-experience_type.php`, `overworld-experience-cpt.php`, `vr-arcade-03-gallery-browse.html`

### 9. Booking menu page (NEW)
Single page with 3 outlet cards, each opens its Bookeo page in a new tab.
- Outlet colors (Kallang blue / Orchard orange / Funan purple)
- Each card: brand tag, outlet name, address, 3 feature highlights, colored book button
- Hover lift + glow per card
- Footer link to event packages
- Fixed Hello theme injected underlines with scoped `text-decoration:none !important` override

File: `book-now-menu.html`

---

## Key Technical Learnings

- After ANY CPT/taxonomy snippet change → **Settings → Permalinks → Save Changes** (flush rewrite rules)
- UI-created ACF field groups take priority over PHP-registered ones with the same field names — delete UI versions if PHP isn't appearing
- Hello/Elementor injects underlines + magenta hover on all `<a>` — needs scoped `text-decoration:none !important` overrides
- Taxonomy archive template filename must match the taxonomy slug exactly: `taxonomy-experience_type.php`
- Taxonomy term display name and slug are independent — "VR Arcade" can have slug `vr-arcade`
- WP Code Snippets scopes: CPT/ACF registration = "Run Everywhere"; admin-only UI tweaks = "Admin Only"
- Invalid CSS partial-rgba pattern (declaring `rgba(R,G,B,` then appending `.X)` later) breaks the whole variable — use complete rgba values

---

## CPT / Taxonomy Slugs (confirmed)

| Thing | Slug |
|---|---|
| FAQ CPT | `faq` |
| Promo CPT | `promo` |
| Pricing CPT | `pricing_item` |
| Event Package CPT | `event_package` |
| Experience CPT | `experience` |
| Experience taxonomy | `experience_type` |
| Arcade term (30 games) | `vr-arcade` |
| VR Escape term | `vr-escape` |
| VR Free Roam term | `vr-roam` |

---

## Outstanding / To-Do

- Add `faq_outlet` ACF field (Select) for the FAQ tabs version, then tag each FAQ
- Delete junk `vr-games` term in WP Admin (Experience Types) — won't respawn now
- Create the Book Now page and decide whether header points to single `/book-now` menu or the 3 separate pages
- Tag VR Free Roam / XR Party games to the right experience_type terms if needed
- Game card "Learn More" links currently use placeholder slugs — update to real experience permalinks
- Combo Deals system still deferred (don't reinstall until staging tested)

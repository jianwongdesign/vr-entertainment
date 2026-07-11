# Overworld Website — Update Report (11 Jul 2026)

Prepared by AdCendes · overworld.com.sg · all changes live and verified

---

## TL;DR

- **Outlet pages rebuilt**: new flow (Hero → What We Offer → Events →
  Pricing → Gallery → FAQ → CTA). Intro + game cards now editable in WP
  Admin, optional card photos auto-crop safely, and each outlet shows its
  own FAQ with category tabs.
- **Event pages rebuilt**: Intro, photo gallery and star-rated reviews are
  now editable per page. Packages workflow unchanged.
- **Promo countdown is live**: pick ONE promo to feature (enforced), set
  its Valid Until date, and the homepage bar + promo page count down
  automatically. Old bar had been stuck at zeros since May — fixed.
- **Everything is self-service now**: empty fields hide themselves, images
  can't break layouts, admin boxes are organised into tabs/collapsible
  rows. Edit once, reflected everywhere.
- **To do**: set the featured promo's date, fill event galleries/reviews,
  add Kallang & Orchard card photos, delete the "Test" FAQ.

---

## 1. What Shipped

### Outlet pages (Kallang / Orchard / Funan) — restructured & editable

New section order on all three outlet pages:

```
Hero  →  What We Offer (intro + game cards)  →  Group Events  →  Pricing  →  Gallery  →  FAQ  →  Booking CTA
```

- **"What We Offer"** now opens with an intro paragraph, followed by the
  activity/game cards. Both are **editable from WP Admin** — including an
  optional photo per card that auto-crops to 16:9, so any upload is safe on
  mobile, tablet and desktop.
- **Per-outlet FAQ** section added: shows only that outlet's questions
  (plus the shared ones), with category tabs — one category at a time.
  Powered by the same FAQ entries as the main /faq/ page, zero extra upkeep.
- Spacing polish: consistent breathing room between sections; gallery moved
  above the booking CTA so every page ends on a call to action.

### Event pages (Team Building / Birthday Party × 3 outlets)

New structure, with the new parts editable per page:

```
Hero  →  Intro  →  Photo Gallery  →  Packages  →  Reviews  →  Enquiry CTA
```

- **Intro**, **Gallery** (6 photo slots, same collage as outlets) and
  **Reviews** (up to 4, with star ratings) are edited on the page itself.
- **Packages are untouched** — still managed under WP Admin → Events,
  exactly as before.
- Empty gallery/review sections hide themselves from visitors, so pages
  never look unfinished while content is being prepared.

### Promo countdown system (new)

- Each promo now has a **"Feature on homepage countdown"** toggle —
  **only one promo can be featured at a time** (turning it on automatically
  turns it off everywhere else).
- The homepage countdown bar is now **dynamic**: it pulls the featured
  promo's title, tagline, booking link and a live timer from the promo's
  **Valid Until** date (ends 23:59 SGT that day).
- The promo's own page also shows an **"Offer ends in"** timer.
- Smart states: no date → bar shows without timer · date passed → bar hides
  itself. (The old bar was hardcoded to 31 May and had been showing zeros.)

### Fixes & polish

- All 8 game pages: section padding reduced to a tighter, consistent rhythm.
- FAQ page mobile: the Kallang/Orchard/Funan filter now stays in one
  straight row instead of stacking.
- Admin editing screens reorganised (see below).

---

## 2. The New Editing Setup — Guide for the Team

Everything below is edited in WP Admin. **No code, no page builder.**
Changes appear on the site immediately (hard-refresh if cached).

### Where to edit what

| Content | Where in WP Admin |
|---|---|
| Outlet intro + game cards | Pages → *outlet page* → **What We Offer** box |
| Outlet photo gallery | Pages → *outlet page* → **Outlet Gallery** box |
| Event page intro / photos / reviews | Pages → *TB or BP outlet page* → **Event Page** box (tabs) |
| Event packages (pricing, posters, PDFs) | **Events** menu (unchanged) |
| FAQs (site-wide + outlet pages) | **FAQs** menu → Outlet + Category dropdowns |
| Promo + homepage countdown | **Promos** → Valid Until date + **Feature on homepage** toggle |
| Game library ordering | **Experiences** → Display Order box (lower = first) |

### The metaboxes are now structured — key things to know

The editing boxes were reorganised so they read top-to-bottom without
clutter:

- **Cards and reviews are collapsible rows** — "Card 1 … Card 6",
  "Review 1 … Review 4". Click a row to expand only that item; everything
  inside is on an aligned grid (Title / Icon / Link on one line, then
  Description and Image full-width).
- **The Event Page box uses tabs** — Intro | Gallery | Reviews — so each
  concern has its own clean panel.
- **Galleries are uniform grids** of 6 image slots, 3 per row, with the
  guidance note at the top ("Image 1 is the large lead tile").

### Conventions that apply everywhere

1. **Empty = hidden.** Leave a card title, review text or all gallery slots
   empty and that item/section simply doesn't render for visitors. Nothing
   ever looks broken or half-filled.
2. **Images can't break layouts.** Every image slot crops to a fixed ratio
   (cards 16:9, gallery tiles) — upload any size/shape safely.
3. **One source of truth.** FAQs feed both /faq/ and the outlet pages;
   packages feed the event pages and their detail pages; the featured promo
   feeds the homepage bar. Edit once, reflected everywhere.
4. **Only-one rules are enforced by the system**, not by memory: one
   featured promo, one FAQ outlet per entry (or blank = all outlets).

### Suggested next steps for the team

- [ ] Set the **Valid Until** date on the featured promo so the homepage
      countdown starts ticking (Promos → Unlock a FREE Private Room →
      Valid Until).
- [ ] Add **event photos** to the 6 Team Building / Birthday Party pages
      (Gallery tab) — sections are ready and hidden until filled.
- [ ] Collect and add 2–3 **customer reviews** per event page (Reviews tab).
- [ ] Review the **card images** on the outlet "What We Offer" sections —
      Funan is done; Kallang and Orchard can take one photo per card.
- [ ] Delete the "Test" FAQ entry if it's still in the FAQs list.

---

## 3. Technical Notes (for reference)

- All fields are registered in code (mu-plugins), so they're versioned in
  Git and can't drift; content lives in the database via ACF.
- Every live change was deployed with a timestamped server-side backup and
  is logged in `docs/live-change-log.md` (11 Jul entries).
- Repo: github.com/jianwongdesign/vr-entertainment — commits `0db7ba3` →
  `81cd343` cover this report.

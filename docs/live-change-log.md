# Live Change Log

## 2026-09-22 - Event Rental: Body Copy And FAQ Span The Full Content Width

The "Make Your Event More Interactive" block and the FAQ list sat in an
860px column while every section above ran to 1300px; the client asked for
one width. Dropped the narrow container so both match. The boxed enquiry
panel keeps its 900px width, as on the hub pages. Template re-deployed
(backup at `/tmp/page-event-rental.php.bak-<stamp>` on the server), caches
purged, checked live.

## 2026-09-22 - Event Rental Page LIVE at /event-rental/, Events › Equipment Rental

**Root cause of the "rejected deploy key" (28 Aug → today):** the site had
moved to a new Hostinger server. sshd at the old IP `145.79.25.17` still
answered and refused every login — key and password alike — because the
account was no longer there. The client supplied the new IP
`72.60.238.181`; `.env` updated, key accepted first try. The key was never
removed from hPanel.

**Live now:** https://overworld.com.sg/event-rental/ — page 1863, published,
template `page-event-rental.php`, mu-plugin `overworld-event-rental.php`
loaded, ACF group `group_ow_event_rental` registered, template guarded.
Header: Events ▾ gained an "At Your Venue" sub-head with "Equipment Rental"
(post 29, widget 36ab9a3). Footer: Plan & Visit list gained "Equipment
Rental" (post 566, widget e37a512). Elementor data of both templates backed
up locally before the patch. All caches purged, incl. Elementor CSS.

Verified on the live URL: HTTP 200, title from the SEO map, meta
description, indexable, FAQPage + Service schema, nav link present, three
activity cards with real photos (borrowed from the outlet pages), in the
sitemap. Checked in Chrome at desktop with the site header/footer around it.

**Two script bugs found on the first real run, both fixed:**
- macOS ships openrsync, which ignores `--relative` with a `/./` anchor and
  recreated the full local path on the server (`wp-content/Users/jianwong/…`).
  The stray tree held only our two files and was removed. Both deploy
  scripts now rsync each file plainly to its explicit destination directory.
- `scp` takes `-P` for the port; the scripts passed `-p` (preserve times).
  `env.sh` now exports `SCP_OPTS` alongside `SSH_OPTS`. Also added an
  optional shared-session helper (`ssh-session-open.sh`, control socket) for
  password login when a key is unavailable — not needed in the end.
- `wp post list --name=` is ignored; the runbook now filters on `post_name`.

**Plugin / update audit (reported, not applied):**

```text
WordPress core        7.0.5   → 7.1.1 (major)
advanced-custom-fields 6.8.7  → 6.8.10
elementor             4.1.4   → 4.2.4
elementor-pro         4.0.4   → 4.2.3
header-footer-elementor 2.9.2 → 2.9.4
google-site-kit       1.187.0 → 1.188.0
insert-headers-and-footers 2.3.8 → 2.3.9
duracelltomi-google-tag-manager 1.22.5 → 2.0.2 (major)
hello-elementor (parent theme) 3.4.9 → update available
twentytwentyfour / twentytwentythree (inactive) → updates available
litespeed-cache, all-in-one-wp-migration, soro-seo: current
```

Nothing was updated. Elementor + Elementor Pro + HFE should be updated
together, by hand, after `./scripts/backup-remote.sh`, given the 6 Aug
incident. Core 7.1.1 and GTM 2.0.2 are majors — read the changelogs first.

## 2026-09-22 - Event Rental: SEO Copy, Nav Placement, Go-Live Runbook (STILL NOT LIVE)

Asked to take the page live under Events › Equipment Rental with richer SEO
wording, then check plugins and updates. SSH is still refused
(`Permission denied (publickey,password)`, key fingerprint
`SHA256:5hEDCS3Wzj2iRP65+nnWKHC6c9gk0jRtBN7RrBLsWq0` offered and rejected),
so nothing reached the server. Everything that does not need the server is
done and committed:

**SEO copy.** The built-in defaults now carry the search phrases the page
should own — interactive game rental, VR rental for events, event equipment
rental, Floor Is Lava rental, XR party game rental, corporate D&D, family
day, school carnival, roadshow, community event, Singapore island-wide.
Title tag 59 chars, meta description 161 (the SEO plugin trims at 158 on a
word boundary). The body block grew from one paragraph to three (what we
rent, who it is for, what is included), the activity blurbs name the format
and the audience, and the FAQ grew from four to six with fuller answers
(space, capacity, staff, customisation, delivery area, lead time and
pricing). No prices or capacities are stated anywhere — the answers say
"send us your headcount and venue" rather than invent a number.

**Navigation.** `scripts/nav-event-rental-link.php` adds "Equipment Rental"
→ `/event-rental/` under Events ▾ in the header (new "At Your Venue"
sub-head after Birthday Party) and to the footer's Events list. It walks
every `elementor-hf` template and patches the HTML widget containing the
anchor markup, so it does not hard-code post 29 / widget 36ab9a3. Tested
against the nav markup pulled from the live homepage on 22 Sep: both
patches land, a second run is a no-op.

**Go-live runbook.** `scripts/go-live-event-rental.sh --apply` does the
whole thing in one go: deploy + draft, nav links, publish, purge, then
verifies the live page (200, title, description, not noindex, FAQPage +
Service schema, nav link present, cards have photos, in the sitemap,
mu-plugin loaded, ACF group registered, template guarded) and lists active
plugins and any pending core / plugin / theme updates. Updates are reported,
not applied — the 6 Aug incident is why.

Blocked on the deploy key. Re-add the public key in hPanel (Advanced → SSH
Access), then run the runbook.

## 2026-09-18 - Event Rental Page: Interactive Game Rental For Events (NOT LIVE)

A new service page for taking the games to the client's venue. The content
and section order come from a reference design the client shared (hero,
three activity cards over three "coming soon" cards, "what's included",
"suitable for", four FAQs, a call to action). The layout is the
`/team-building/` and `/birthday-party/` hub template's, as asked — centred
hero with the eyebrow pill and gradient H1, section heads with the mono
counter, accent-lined cards with the full-width pill button, the "why" grid,
a body-copy block, FAQ accordion and the boxed enquiry panel — and the
accent is the reference's green (`#c3fb33`) rather than the hubs' lava
orange. A first cut that copied the reference's own layout in orange was
replaced the same day after review.

**Everything on it is client-editable from WP Admin**, the same way the
`/team-building/` and `/birthday-party/` hubs are: an ACF field group
("Event Rental Page Content") on the page, every field optional, built-in
copy filling any field left empty. Lists are numbered slots (6 activities, 6
included items, 6 audiences, 6 FAQs) because the site runs ACF free, which
has no repeater; filling any slot of a list replaces that list's built-in
rows entirely. Icons for the strip and the row are picked from a select of
17 built-in SVGs, so the client never pastes markup. A hero photo the client
uploads also becomes the featured image, so `overworld-seo.php` picks it up
as the sharing image without a second upload. Until the client uploads
activity photos, the three default cards borrow the pictures the outlet
pages already use for VR Free Roam, Floor Is Lava and XR Party Game.

Four new files, nothing deployed:

```text
wordpress/wp-content/mu-plugins/overworld-event-rental.php            fields, defaults, SEO
wordpress/wp-content/themes/hello-elementor-child/page-event-rental.php  the template
scripts/event-rental-page.php                                          creates /event-rental/ as a DRAFT
scripts/deploy-event-rental.sh                                         sends the two files, runs the script
```

SEO goes through the existing plugin's filters: `ow_seo_page_map` for the
title and description (the page's own SEO box still wins), `ow_seo_page_keywords`,
and `ow_seo_schema_graph` for a FAQPage built from the rendered questions plus
a Service node listing the activities as an offer catalog. The page is
indexable and in the sitemap — it is a real service page, unlike the ads
landing page. It also registers itself with the template guard so opening
it in Elementor cannot reset the template.

The "View All Activities" link defaults to hidden: the site has no page that
lists every activity, and a link to a 404 is worse than no link.

Verified by rendering the template against a stubbed WordPress, once with
no fields filled and once with client overrides (a hero photo, four
activities with one photo, placeholders switched off, a single FAQ with
HTML in the question, an unknown icon key): no PHP notices, 93 fields with
no duplicate keys, every meta key the code reads is a registered field,
FAQPage + Service emitted, the featured image synced on save, HTML escaped,
the unknown icon falling back to the controller. Checked in Chrome at
desktop and 390px.

Not live. Deploy with `CONFIRM_PUSH=overworld.com.sg ./scripts/deploy-event-rental.sh --apply`
once the Hostinger deploy key is re-added (see 2026-08-28), then publish
from the go-live block in `scripts/event-rental-page.php`.

## 2026-08-29 - Ads Landing Page: The Offer, Read From John's Packages (NOT LIVE)

Feedback on the draft landing page: it never said what you actually get or
what it costs. Asked to work from what John wrote on the packages.

**The packages are read live from the `event_package` CPT**, with the same
query the `/team-building/[outlet]/` and `/birthday-party/[outlet]/` pages
use. Not copied into the template. John writes and prices the packages in WP
Admin, so a price edited there has to reach the page an ad points at on the
next request, without a deploy. His tagline, duration, group size, price and
PDF render as written; only the `"<Outlet> - "` title prefix is trimmed for
display, because a chip beside it already names the outlet.

25 packages across the two types and three outlets:

```text
team-building    Kallang 3   Orchard 7   Funan 4
birthday-party   Kallang 3   Orchard 3   Funan 5
```

**Price anchors in the hero.** `event_price_from` is free text — "$33 -
$39/pax", "$384 - $887.30" — because team building is priced per head and
birthdays are priced per package. Normalising the two into one number would
mean labelling it, and the label would be wrong for one of them. Instead
`ow_ads_from_price()` reads the leading amount off each package, takes the
smallest, and carries the unit across from that same package's own string:

```text
Team Building From $33/pax
Birthday Party From $269
```

It returns '' when nothing parses, and the chip is then omitted. A landing
page stating a price the packages do not back up is worse than one stating
none. The per-block notes carry the distinction in words as well — "Priced per
person" against "Priced per package, not per head".

**Bookeo stays the main call to action**, as asked. The hero button and the
sticky bar still go to the calendar. The packages sit between the event-type
cards and the games, with "What's Included →" to the package's own page and
its PDF beside it. Worth keeping in view: Bookeo sells standard sessions, not
these packages, so a visitor who wants Package C cannot self-serve — they go
through the package page or WhatsApp.

`?e=tb` and `?e=bp` now reorder the offer as well as the hero, so a birthday
ad leads with the birthday price anchor and the birthday packages.

Section banding was reshuffled — games became `--alt` and outlets plain — so
the light/dark alternation survives the inserted section. Package names moved
onto the display font by adding `h4` to the heading rule; they were the only
card titles rendering in the body font.

Verified by rendering the template against a stubbed WordPress carrying the 25
real packages: no PHP notices, 25 cards, both anchors correct, `?e=bp`
reordering both the chips and the blocks, and the grid checked at 390 / 1024px
and desktop.

Still not live, and still blocked on SSH — see the entry below.

## 2026-08-28 - Deploy Blocked: The Hostinger Deploy Key Is Rejected

`./scripts/ssh-hostinger.sh` fails against the live host:

```text
debug1: Offering public key: ~/.ssh/hostinger_deploy ED25519 SHA256:5hEDCS3Wzj2iRP65+nnWKHC6c9gk0jRtBN7RrBLsWq0
debug1: Authentications that can continue: publickey,password
u146877548@145.79.25.17: Permission denied (publickey,password).
```

Host, port and user are right — the connection reaches sshd, which then
refuses the key. `hostinger_deploy.pub` is no longer in `authorized_keys` on
the server. Not the old leading-space-in-`.env` problem: that value is clean
now, and the key is being offered rather than skipped.

To restore: re-add the public key in hPanel → SSH Access, or
`ssh-copy-id -p 65002 -i ~/.ssh/hostinger_deploy.pub u146877548@145.79.25.17`.

Added `scripts/deploy-ads-landing.sh` so the deploy is one command once access
returns. It sends **only the two files** the feature needs rather than the
~900 `push-wp-content.sh` would sync, uploads `ads-landing-page.php` to `/tmp`
(not under the docroot, where it would be web-reachable), runs it through
wp-cli to create the draft, deletes it, and purges the caches. Dry run by
default; `--apply` needs `CONFIRM_PUSH=overworld.com.sg`.


## 2026-08-27 - Google Ads Landing Page For Team Building + Birthday (NOT LIVE)

Asked for a single paid-traffic landing page covering team building and
birthday parties, showing every keyword and every outlet's games, explicitly
**not** built as a client-editable page, and converting into **Bookeo**.

Built as three files, nothing published:

```text
wp-content/themes/hello-elementor-child/page-ads-events.php   the page itself
wp-content/mu-plugins/overworld-ads-landing.php               title/desc/keywords/noindex
scripts/ads-landing-page.php                                  creates it as a draft
```

**Why a theme template and not an Elementor document.** An ad points at a
fixed URL for the length of a campaign. Anything editable in WP Admin can be
changed underneath it without the person running the ads knowing. Everything a
visitor sees is in the template file, so the page can only change through a
deploy.

**The conversion is the Bookeo widget, embedded on the page.** Not a link to
`/book-now-*/`, not a form — an ad click reaches live availability with no
second click.

The constraint that shaped the section: `bookeo.com/widget.js` refuses to run
twice on one document. `bookeo_start()` checks `axiomct_project` and, finding
it already set, fires

```text
alert("Multiple copies of the Bookeo booking widget are present on the page.")
```

and bails. So three tabbed calendars on one page is not available. Exactly one
widget renders, and the outlet switcher is three plain links that reload the
page with `?outlet=<slug>`. Confirmed working against all three live widget
keys — Kallang and Orchard render their white document, Funan its dark one,
the same as the Book Now pages.

**Query parameters are carried across the switch.** `ow_ads_url()` rebuilds
the current URL with `outlet` replaced and every other parameter kept, so
`gclid` and `utm_*` survive. Dropping them would break attribution for exactly
the visitors furthest down the funnel.

**Two ad groups, one URL.** `?e=tb` and `?e=bp` swap the eyebrow, H1 and
tagline so a "team building singapore" ad and a "birthday party venue" ad each
land on a page that reads as a match for the search term:

```text
(none)  Team Building & Birthday Parties in Singapore
?e=tb   Team Building Activities in Singapore
?e=bp   Birthday Party Venues in Singapore
```

**Keyword coverage** is carried by real sections rather than a keyword block:
all eight games with the outlets that have them, all three outlets with MRT
and address, an eight-question FAQ written for paid traffic, and four
paragraphs of prose. Meta keywords, title and description come from the
mu-plugin through the `ow_seo_page_map` and `ow_seo_page_keywords` filters,
matched on **page template** rather than page ID, so the page can be renamed,
re-slugged or duplicated for a second campaign without touching that file.

**noindex, follow, and dropped from wp-sitemap.xml.** The page restates what
`/team-building/` (521) and `/birthday-party/` (522) already say. Left
indexable it would be a third page competing with those two for the same
queries, and they are the ones with the internal links and the package data.
Google Ads does not read the robots directive. To reverse it, set the SEO
box's noindex field to 0 on the page — the per-post field wins.

**Conversion signals.** Every CTA carries `data-ow-cta`, and one delegated
listener pushes `ow_cta_click` with the cta name, outlet and focus to
`dataLayer`, falling back to `gtag`, silent when neither exists. Nothing has
to change on the page when Ads conversion tracking is set up.

Verified by rendering the template against a stubbed WordPress and loading it
in Chrome: no PHP notices, one widget per document, live calendars loading,
`gclid` preserved through a switch, and the layout checked at 390 / 600 / 768
/ 1024px and full desktop width.

Nothing is live. `scripts/ads-landing-page.php` creates
`/group-events-singapore/` as a **draft** and refuses to run if the template
is not deployed. The go-live steps are at the bottom of that script.


## 2026-08-11 - The Next Section Rode Up Over The Video On Phones

Reported with a phone screenshot of `/vr-machine-ride/`: the "WHAT IS VR
MACHINE RIDE / STRAP IN. TAKE OFF." heading was painted across the bottom of
the video, while that section's body text sat correctly below it.

Reproduced by loading the live page into a 390px same-origin iframe, since the
window itself would not resize small enough. The two sections genuinely
overlapped by **101px** — `.ow-vid` ran to y=1199 and `.ow-vrm-what` started at
y=1098.

**Nothing was pulling the next section up.** `.ow-vrm-what` has no negative
margin at any breakpoint and is not positioned; measured `margin-top: 0px`.
The video section was the one lying about its size:

```text
section.ow-vid            h=364   ← correct
div.elementor-shortcode   h=364   ← correct
div.elementor-element-8a84b42 (widget)   h=263  scrollHeight=364   ✗
div.elementor-element-e2f8767 (container, display:flex column) h=263 ✗
```

A block widget cannot be shorter than its block child, so something was sizing
it from outside. Narrowed by experiment, restoring each style in between:

```text
container display:block       → 364, overlap 0   ✓
widget    width:100%          → 364, overlap 0   ✓
widget    min-width:100%      → 364, overlap 0   ✓
widget    align-self:stretch  → 263, overlap 101 ✗
container align-items:stretch → 263, overlap 101 ✗
.ow-vid   width:100%          → 263, overlap 101 ✗
frame     height:195px fixed  → 364, overlap 0   ✓
frame     padding-top:56.25%  → 265, overlap 101 ✗
```

**Elementor's flex-column container resolves the widget's height against its
min-content width, not its real one.** The frame is 16/9, so its height is
derived from its width: measured against roughly 167px instead of 347px it
comes out 94px tall instead of 195px, and the container lands 101px short. The
next section then starts inside the video. Swapping `aspect-ratio` for the old
`padding-top:56.25%` trick does not help — a percentage padding is width-derived
too, and fails in exactly the same place. Only a *definite* width fixes it.

This never showed on desktop because `.ow-vid__inner{max-width:1200px}` pins the
frame to a fixed number above 1200px, so the width is definite before the height
is asked for. It read as a phone bug; it was a below-1200px bug.

One rule, scoped with `:has()` so it reaches this widget and nothing else:

```css
.elementor-widget:has(> .elementor-shortcode > .ow-vid){width:100%;}
```

The outlet pages render the section from `page-pricing.php`, outside any
Elementor widget, so the selector never matches there and nothing changes.

```text
Live, after deploy + purge, measured in-page at 6 widths:
  360 / 390 / 430 / 768 / 1024 / 1280
  overlap 0 at every width · container height == section height at every width
  frame ratio 1.778 at every width (16/9)
  vidBottom == whatTop == 1201 at 390px — the sections now meet exactly
php -l clean both ends · sha1 identical · LiteSpeed purged
backup: ~/overworld-backups/video-flex-fix-20260811/
```

Only `/vr-machine-ride/` carries a video link today, so it was the only page
showing this — but every game page would have hit it as links are added. The
outlet pages could not be checked live for the same reason: no link set, no
section rendered. Worth one look once a video goes onto an outlet page.

## 2026-08-11 - Video Section: The Cropped Frame Was YouTube's Own Poster

Reported as the video looking heavily cut off once the embed loads. First look
at it in a real browser (the Chrome extension is connected again), on
`/vr-machine-ride/` — the first page with a video in it, `sAerBP-DFIk`.

**Our frame was never the problem.** Measured live: the frame is 1200×675, the
iframe fills it at 1198×673, `aspect-ratio` computes to 16/9, and the video is
16:9 (oEmbed reports 200×113). Our own cover is a 16:9 image in a 16:9 box, so
`object-fit:cover` crops nothing. At full width YouTube's poster is not cropped
either — screenshotted to be sure.

**What is cropped is YouTube's own pre-play screen at phone widths.** Injecting
the embed at 324×182 and 324×210 — a 16:9 frame on a 360px phone — YouTube
zooms its poster hard and truncates the title to "VR Machine Fantasy Starship
P…". That screen is only reached when autoplay does not start, which on iOS is
always: Safari refuses autoplay with sound, full stop. So the sequence on a
phone was: tap our play button → YouTube's cropped poster and red button →
tap again. That is the "cut off" being reported.

Three changes:

1. **Start muted.** `mute=1` is the only autoplay every browser allows, so the
   player goes straight into playback and its poster is never reached.
2. **Sound comes back on `PLAYING`, not on `onReady`.** Unmuting before
   playback has begun can trip the autoplay gate and leave the video paused.
   The visitor pressed our button, so the page carries the activation the
   unmute needs. Where the browser still refuses — iOS — a small accent
   "Tap for sound" pill appears over the player; a direct tap is its own
   gesture, which iOS does accept.
3. **Our cover stays on top until the video is genuinely playing**, with a
   spinner in place of the play triangle, and a 2.5s fallback so nobody is
   ever trapped behind it if the API never answers. Whatever YouTube paints
   while it spins up is now behind our own still.

Also `padding:54px 18px` → `54px 12px` at ≤600px. YouTube's chrome crowds and
crops below roughly 340px of player width, so the extra 12px of frame width is
worth more than the symmetry.

```text
Live, in-browser, after deploy:
  at click   children order [IFRAME, BUTTON] — iframe behind the cover
             cover visible, is-loading (spinner) ✓
  +2.6s      cover hidden, one iframe playing ✓
  unmute on PLAYING ✓   no unMute in onReady ✓
php -l clean both ends · sha1 identical · LiteSpeed purged
```

Not verifiable from here: whether sound actually returns on desktop and whether
the pill appears on iOS — YouTube will not stream inside this automated
browser, so playback itself stalls at 0:00 regardless of the code. Worth one
check on a real phone and one on a desktop.

## 2026-08-07 - Video Section Under The Hero On All 11 Game + Outlet Pages

Asked for a YouTube placeholder as the second section on the game pages and
the individual outlet pages, matching the content width, with the YouTube look
played down to a play button and no "watch next" at the end.

New mu-plugin `overworld-video-section.php`. One renderer, two ways in — the
game pages are Elementor documents in the database and the outlet pages are a
theme template, so the plugin exposes both a `[ow_video]` shortcode and an
`ow_video_section()` function.

**It is a facade, not an embed.** On page load there is no iframe, no request
to youtube.com and no cookie — just our own cover image, scrim and accent play
button, so there is no YouTube chrome to play down. The player is built only
when someone presses play, from `youtube-nocookie.com` with `rel=0`,
`modestbranding=1`, `iv_load_policy=3`, `playsinline=1`.

**The "watch next" grid is genuinely gone.** `rel=0` narrows suggestions to the
same channel, but YouTube still paints a grid over the last frame when a video
finishes. So the section attaches the IFrame API, watches for `ENDED`, removes
the iframe and puts the cover back. If the API fails to load the video still
plays — only the reset is lost. What no embed parameter can remove: end screens
and cards baked into the last seconds of a video. Those are a per-video setting
in YouTube Studio and have to be switched off there.

Fields on each page (ACF group "Video", first on the edit screen): YouTube Link
(watch / youtu.be / Shorts / bare ID all parse), optional Cover Image — falls
back to the video's own thumbnail — optional Heading (default "See It In
Action") and one optional line under it.

**Nothing is visible yet, by design.** With no link set the section renders
nothing at all — for visitors *and* for editors. The first cut showed a dashed
"Video slot" placeholder to anyone who could edit the page, following the
outlet gallery convention; that was dropped on request, because a logged-in
client browsing the site sees the same page a visitor does and a placeholder
box reads as something visitors can see too. The ACF field group carries the
explanation instead. Verified as an administrator: all eight game pages return
an empty string with no link set, and the cover comes back the moment one is.

```text
Game pages   container + shortcode widget spliced in at index 1 of _elementor_data
             (hero stays first) — the shape the existing [ow_experience_grid]
             block already uses on these pages:
  326 vr-arcade #ff5722   420 vr-escape #a855f7   294 floor-is-lava #ff5722
  312 laser-maze #22e3ff  364 tap-tap #ff2db8     338 vr-machine-ride #00d4ff
  646 vr-free-roam #00ff88  577 xr-party-game #ffd60a
Outlet pages page-pricing.php calls ow_video_section() after the hero; the
             section inherits --accent from .ow-pri, so it takes the outlet
             colour without being told.
```

Each page passes its own hero accent, so the play button and eyebrow match the
page. Inner width is 1200px — the same measure as every other section on both
page types — with the video in a 16:9 frame across it.

One trap worth recording: an empty Elementor container would leave a band
between hero and section 2 if its zero padding never reached the generated
stylesheet. `_elementor_css` was dropped on all eight pages and each was warmed
so Elementor rewrote it; the padding-0 rule for the new container is now
present in all eight `post-<id>.css` files, so the empty section has no height.

```text
All 11 pages 200 · 0 PHP warnings · 0 ow-vid markup for logged-out visitors
             · 0 raw [ow_video] leaking into the HTML
Outlet pages still render the store introduction (unchanged)
Render test (fake link injected via filter, nothing written to the DB):
  7429 bytes · cover button · data-ow-video · ytimg poster · youtube-nocookie
  · rel=0 · modestbranding=1 · iv_load_policy=3 · PlayerState.ENDED · accent
ID parsing: watch?v= / youtu.be / shorts / embed / bare ID all resolve; junk -> ''
Elementor: padding-0 rule present for the new container on all 8 game pages
```

Not seen in a browser — the Chrome extension is not connected. Paste a link
into any one page's Video field to check the cover, the play behaviour and the
end-of-video reset.

**Order note for the outlet pages:** the video is now section 2 and the store
introduction section 3, which is what was asked for literally. If the intro
should come first, it is a two-line swap.

## 2026-08-07 - Game Page Heroes: Stats Pill Is 2×2 On Phones, Not A Ladder

Reported as the middle text of the hero listing one line at a time on mobile,
asking for 2 by 2.

The pill under the hero copy ("30+ Games · 1-17 Players · 8+ Years Old ·
3 Session Lengths") is a single row on desktop. Seven of the eight game pages
switched it to `flex-direction:column` below 560px, so four stats became four
stacked lines — a tall ladder pushing the buttons down the screen. Every page
carries exactly four stats, so a two-column grid gives a clean 2×2.

```diff
- .ow-<pre>-hero__stats{flex-direction:column;border-radius:24px;padding:16px 24px;gap:12px;}
+ .ow-<pre>-hero__stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));
+   border-radius:24px;padding:16px 18px;gap:14px 16px;
+   width:100%;max-width:420px;margin-left:auto;margin-right:auto;}
- .ow-<pre>-hero__stat{padding:0;}
+ .ow-<pre>-hero__stat{padding:0;white-space:normal;justify-content:center;}
```

Two details that keep it from overflowing a small phone. The base rule sets
`max-width:max-content`, which on a two-column row is exactly what would push
the pill past the viewport — hence the explicit `width:100%;max-width:420px`.
And the base sets `white-space:nowrap`; released at this tier, so a long label
wraps inside its own cell instead of widening the grid. Worst case measured
from the served CSS: the widest cell ("3 Session Lengths", 10px mono at .16em)
is ~132px, so two columns plus gap and padding come to ~315px against ~320px
available on a 360px screen — and if a narrower phone appears, the label wraps
rather than overflows.

Applied to vr-arcade (326), vr-escape (420), floor-is-lava (294), laser-maze
(312), tap-tap (364), vr-machine-ride (338), xr-party-game (577) at ≤560px.

**VR Free Roam (646) is built differently** — it never stacked, it wraps, in a
single ≤760 tier — so it gets the same grid at its own breakpoint. It could not
fit one row below 760px anyway (that is what the 2026-08-05 separator fix was
about), so a deliberate 2×2 replaces ragged wrapping.

Nothing above 560px changed on the seven; desktop keeps its single row with
separators. `scripts/hero-stats-2x2.php`, guarded `str_replace` on both
`post_content` and `_elementor_data`, originals in
`~/overworld-backups/hero-stats-2x2-20260807/`, element caches dropped,
LiteSpeed purged.

```text
Served CSS, all 8 pages: grid rule present 1× · old column rule 0× · HTTP 200 · 0 PHP warnings
Cascade order on vr-arcade: base(61406) < ≤900(64405) < ≤560 grid(65018) < ≤560 cell(65216)
Both new rules confirmed inside @media (max-width:560px).
```

Verified as served CSS and cascade order, not in a browser — the Chrome
extension is not connected, so the 2×2 has not been seen rendered.

## 2026-08-06 - Gift Vouchers: Booking-Style Pages With The Bookeo Embed (NOT LIVE)

Asked for the gift voucher section to work like the booking pages — refer back
to the outlet and embed Bookeo — and explicitly asked for it to be prepared
without going live.

Today vouchers are three nav links straight out to
`bookeo.com/<account>/buyvoucher`; the visitor leaves the site. Booking used to
work that way too until `scripts/booking-redesign.php` gave each outlet its own
page with the Bookeo widget embedded, behind a `/booking/` outlet chooser. This
mirrors that set exactly.

**The key finding: no Bookeo back-office work is needed.** Bookeo's `widget.js`
forwards unknown query parameters into the frame it builds — `widget.js?a=KEY`
plus `&buyvoucher=true` produces
`b_<KEY>_start.html?inwidget=true&a=<KEY>&buyvoucher=true`, the voucher flow,
using the **same three widget keys** the booking pages already use. Verified by
diffing the served `widget.js` with and without the parameter. (Framing the
short `bookeo.com/.../buyvoucher` URL directly is not an option — it answers
`X-Frame-Options: SAMEORIGIN`.)

`scripts/voucher-pages.php` builds four pages, all as **drafts**:

```text
1765  /voucher-kwm/            Kallang  key 231YALUW419DA4CE7973
1766  /voucher-orchard/        Orchard  key 231T6UX7U19D0A676CD2
1767  /voucher-funan/          Funan    key 231RYKULN19D91C736C8
1768  /gift-voucher-preview/   outlet chooser, mirrors /booking/
```

Layout is the Book Now layout with voucher wording: per-outlet accent hero,
venue and address, activity pills, "Choose a different outlet" pointing at the
final `/gift-voucher/` URL, then the black embed section and the phone/WhatsApp
help strip. No claims about validity, denominations or terms — nothing is
asserted about vouchers that the site does not already say.

Nothing is live: the pages are drafts (anonymous request to `/voucher-kwm/`
returns 404), the published `/gift-voucher/` stub (523, still
`<!-- placeholder -->`) was not touched, and the nav was not touched. The
script refuses to write to any page that is already published, and is
idempotent — re-running updates the same four pages.

**Not verified: whether the voucher widget renders inside the frame.** Bookeo
answers a bot check to `curl`, and the Chrome extension is not connected, so
this needs one human look at a preview link while logged in. That is the
approval gate for the go-live steps, which are written out at the bottom of the
script and deliberately not automated: publish the three pages, move the hub
onto 523, repoint the three nav links, purge.

## 2026-08-06 - VR Arcade / VR Escape: Hero "Browse The Games" Goes To The Library

Reported as the hero button scrolling down the page instead of opening the game
library. Both pages had `href="#games"` on the ghost button next to Book Now,
which jumped to the on-page grid; the same pages already link out to the real
library twice lower down.

| Page | Was | Now |
|---|---|---|
| /vr-arcade/ (326) | `#games` | `/experience-type/vr-arcade/` |
| /vr-escape/ (420) | `#games` | `/experience-type/vr-escape/` |

Both are Elementor pages, so the edit is a guarded `str_replace` on the DB, on
an anchor unique enough to hit one button and nothing else
(`ow-vra-hero__btn--ghost\" href=\"#games\"`). vr-arcade needed only
`_elementor_data` — its `post_content` copy already pointed at the library,
having drifted out of step at some earlier edit. vr-escape needed both fields.
`_elementor_element_cache` deleted on both, LiteSpeed purged (see
[[elementor-element-cache-blocks-db-edits]] — without that the change is
invisible). Originals in `~/overworld-backups/hero-browse-links-20260806/`.

The `#games` section itself is untouched — the grid is still there, it just
isn't what the hero button points at now. The other six activity pages were
checked for the same pattern: none of them have it.

```text
Served HTML after purge:
  vr-arcade  hero btn -> /experience-type/vr-arcade/  "Browse The Games" · 0 "#games" left · id="games" still present
  vr-escape  hero btn -> /experience-type/vr-escape/  "Browse The Games" · 0 "#games" left · id="games" still present
  both library archives 200 · 0 PHP warnings on either page
```

## 2026-08-06 - Outlet Pages: Client-Written Store Introduction

Asked for a text holder on all three outlet pages so the client can introduce
the store in their own words and the page carries more content.

New mu-plugin `overworld-outlet-about.php` adds a **Store Introduction** field
group to anything on the Pricing Page template — heading plus a WYSIWYG body.
It sits first on the edit screen (`menu_order` 2, above What We Offer). The
body is a WYSIWYG rather than the plain textarea used by the other outlet
fields: an introduction wants paragraphs, a bold phrase and the odd link.
Output goes through `wpautop()` then `wp_kses_post()`.

`page-pricing.php` renders it as section 2, directly under the hero and above
Activities & Games, using the shared `.ow-pri__section-head` so it reads like
the rest of the page. Body copy is capped at 820px — the reading measure, not
the 1200px grid — with paragraph, list and link styling in the outlet accent.

**It is never blank.** Empty fields fall back to a per-outlet default, the same
pattern `outlet_intro` already uses. The defaults were written only from what
the page already asserts — the activity line-up in `$outlet_config` and the
street address — plus the MRT each outlet sits beside. No opening hours, no
walk-in policy, no claims that are not already on the page. **They are
placeholder copy and should be read and rewritten by the client**; anything
typed into the field replaces them outright.

| Outlet | Default opens with |
|---|---|
| Kallang | flagship arena, four ways to play under one roof |
| Orchard | the headset-free one — lava floor, laser maze, light wall |
| Funan | built for groups who want to move |

Heading default is "Welcome To [outlet name]". Backup of the previous template
in `~/overworld-backups/outlet-about-20260806/`.

```text
php -l clean local and remote; sha1 identical on both files.
ACF: group_ow_outlet_about registered, location page_template==page-pricing.php.
Field group count 16, all previously existing groups still present.

Served HTML, all three outlets, cache-busted:
  kallang   .ow-pri__about-body present · "Welcome To Kallang Wave Mall" · 2 <p>
  orchard   .ow-pri__about-body present · "Welcome To Orchard Central"   · 2 <p>
  funan     .ow-pri__about-body present · "Welcome To Funan"             · 2 <p>
  pricing rows 17 / 17 / 15 · FAQ blocks present · 0 PHP warnings each
```

Checked as source HTML, not in a browser — the Chrome extension is not
connected, so the visual spacing above Activities & Games is unconfirmed.

## 2026-08-06 - Incident: WPCode Cache Delete Took 5 CPTs Off The Site

Reported as "you removed some of the ACF items". Nothing was deleted — but for
roughly 35 minutes FAQs, Pricing, Events, Promos and Experiences were not
registered at all, so their admin menus, their ACF field groups and their
content were missing from wp-admin and from the front end.

**Cause.** Those five post types are registered by WPCode snippets, not by the
repo. WPCode decides what to run from the `wpcode_snippets` option, a cache of
active snippets by location. While deactivating snippet #617 (the admin colour
CSS, being replaced by a mu-plugin) that option was deleted to force the change
through. It never came back: WPCode rebuilds it in exactly two places —
`WPCode_Snippet::save()` and a visit to the Code Snippets admin page — and
neither a front-end request nor wp-cli triggers either. With the option gone,
every snippet stopped executing, including the ones that register the CPTs.

Deleting the option was unnecessary in the first place: setting the snippet's
`post_status` to `draft` already deactivates it.

**Fix.** `wp eval 'wpcode()->cache->cache_all_loaded_snippets();'`

```text
wpcode_snippets rebuilt: everywhere => 906,604,603,473,435,380,280,13
                         site_wide_header => 15,14      (admin_only now empty
                                                         — #617 is draft)
Post types back:  faq 47 · pricing_item 17 · event_package 26 · promo 4 ·
                  experience 83  (published counts — no content was lost)
ACF field groups: 16 registered, every group present.
```

Never `delete_option( 'wpcode_snippets' )`. WPCode's own `delete_cache()` is no
safer — it writes an empty array and is equally inert until something rebuilds.

**Unrelated, same window:** the first of the stalled Elementor auto-updates —
see the entry below.

## 2026-08-06 - Stalled Elementor Auto-Update Is Taking The Site Down Hourly

Reported as "why is the system in maintenance".

Something tries to update Elementor 4.1.4 → 4.2.1, unpacks all 2,566 files into
`wp-content/upgrade/`, then dies before swapping them in. WordPress writes
`.maintenance` at the start of any plugin install and deletes it at the end;
the process never reaches the end, so the flag is orphaned and every visitor
gets a 503 until WordPress treats the flag as stale 10 minutes later.

```text
08:12:25 UTC  flag written · ACF updated OK · Elementor unpacked, not installed · 503 until 08:22:25
09:32:15 UTC  identical stall, same files                                       · 503 until 09:42:15
Elementor still 4.1.4 · no PHP error logged · retries roughly hourly
```

**Not WordPress doing it.** `hostinger-auto-updates.php` disables WP's own
updater (`automatic_updater_disabled`, `auto_update_plugin` → false), the
`auto_update_plugins` option is `false`, every plugin reports auto-update
"off", and there is no user crontab. That leaves Hostinger's platform-level
auto-update in hPanel. Dying mid-copy with no PHP error, in a run where the
much smaller ACF update completed, reads as the process being killed on a host
resource limit while copying ~3,000 files.

**The complication:** `elementor_pro_license_key` is empty. Both Elementor and
Elementor Pro have 4.2.1 available, but without a licence Pro cannot download —
so any successful run moves core only and leaves Pro at 4.0.4. The homepage and
all eight activity pages are Elementor-built, so a core/Pro split is not
something to walk into casually.

**Decision: stop the auto-updates rather than force the update through.**
Versions stay matched at core 4.1.4 / Pro 4.0.4, the outages stop, and the
update gets planned properly once the Pro licence is in place. The switch is in
hPanel (Websites → overworld.com.sg → WordPress → Auto updates) — panel-side,
so it is the client's to flip; nothing in WordPress needed changing, it was
already off there.

Left in place: the stale `.maintenance` flag (WordPress already ignores it —
anything older than 10 minutes is treated as expired) and the abandoned
`wp-content/upgrade/elementor.4.2.1/` unpack. Both are inert leftovers; removing
them was blocked by the local safety policy on remote deletes and is not
required for the site to run.

## 2026-08-06 - Admin Sidebar: Two Zones, Popups Gets Its Colour

Asked for the new Popups section to carry a colour like the other client
sections, and for the left menu to be arranged so the coloured (client) items
sit together and everything else drops to the bottom as admin tooling.

**The colour code was living in the database.** It was WPCode snippet #617
("Overworld :: Color-Coded Admin UI", v3) — never in Git, so it was invisible
to the repo and easy to lose. Ported verbatim into
`wp-content/mu-plugins/overworld-admin-ui.php`, snippet set to draft
(WPCode treats `post_status !== publish` as inactive — verified in
`class-wpcode-snippet.php:423`), and its cache option `wpcode_snippets`
deleted so the deactivation took effect. Original snippet body and post row
saved to `~/overworld-backups/admin-ui-20260806/`. Deactivate-then-upload was
the required order: both copies declare `ow_admin_color_map()`, so any overlap
would have been a redeclare fatal on every admin page.

**Colours.** The five existing ones are unchanged. Added: Popups green
`#22c55e` 📢, Pages blue `#3b82f6` 📄, Media and Posts slate `#94a3b8`. Each
item gets the same treatment as before — 4px accent on the sidebar item,
coloured icon, tinted current state, accented page title, Add New button,
Publish button and row hover on its own screens.

**Two zones.** `custom_menu_order` + `menu_order` at priority 9999, with the
stock separators removed and two labelled ones put in their place:

```text
Dashboard
── WEBSITE CONTENT ──
Pages · Experiences · Events · Pricing · FAQs · Promos · Popups · Media · Posts
── WEB ADMIN TOOLS ──
Site Kit · Elementor · Comments · Templates · Hello · Appearance · Plugins ·
Users · Tools · All-in-One WP Migration · Settings · (ACF, LiteSpeed, WPCode…)
```

Only the client list is enumerated; everything else keeps its default relative
order and lands below the divider on its own, so a plugin installed next month
needs no change here.

Two things that would have broken it quietly:

- Core deletes the second of any two adjacent separators, so the stock
  separators are unset first — otherwise ours could vanish depending on where
  they landed.
- Site Kit filters `menu_order` too, to hoist itself next to the Dashboard. At
  the default priority it ran after us and sat inside the client zone. Priority
  9999 puts us last and the zones hold.

The labels are drawn with `::after` on the separator `<li>`, and blanked when
the sidebar is collapsed (`.folded` / `.auto-fold`) where there is no room.

Verification — the admin menu was rebuilt under wp-cli against the live
database (globals bound first, otherwise `wp-admin/menu.php` writes core's
items into a local scope and the result is nonsense; the four WPCode-registered
CPTs were stood in, as WPCode snippets do not execute under wp-cli):

```text
 1 index.php                        Dashboard
 2 separator-ow-client
 3 edit.php?post_type=page          Pages
 4 …experience / event_package / pricing_item / faq / promo / ow_popup
10 upload.php                       Media
11 edit.php                         Posts
12 separator-ow-admin
13 googlesitekit-dashboard … 24 options-general.php

CSS emitted on a dashboard load: 6434 bytes, all 9 menu ids present,
#22c55e present, both zone labels present.
php -l clean local and remote, sha1 identical, homepage + wp-login 200.
```

Not verified in a browser — the Chrome extension was not connected. Hard-refresh
wp-admin (Cmd/Ctrl + Shift + R) to see it.

## 2026-08-05 - Site-Wide Sweep For Stray Separators On Wrapped Rows

Asked to confirm the VR Free Roam divider problem does not exist anywhere else.
All 179 sitemap URLs were fetched and scanned statically for the bug class: a
separator drawn on adjacent siblings inside a flex container that is allowed to
wrap. Detection is viewport-independent — a container that CAN wrap at any
breakpoint plus children that CARRY a separator is latent whether or not it
happens to be wrapping at the width you are looking at. Parent/child
relationships came from parsing the real DOM, not from guessing at BEM names.

**Correction to the previous entry.** It claimed "none of the seven sibling
pages use dividers at all". That was wrong. It was based on searching only for
`border-left` on an adjacent-sibling selector. The seven pages do draw
separators — as `:not(:last-child)::after` pseudo-elements — which the first
scan did not look for. A second pass covering pseudo-element separators,
`border-top`/`bottom`, and `display:none` neutralisation found them.

**Seven pages had a real, reachable defect.** They hide the separator and switch
to a column at `<=560px`, but the pill starts wrapping while still in row
direction above that. Measured on vr-arcade: under the `<=900px` tier the pill
needs 542px of content against 513px available at a 561px viewport. So between
roughly 561px and 664px (varies with stat text length, 43-57 chars across the
seven) it wraps with separators still painted, and every non-last item leaves a
line hanging off the end of its row. Narrow window, but reachable on small
tablets, foldables and large phones in landscape.

Fix: hide the separator across the whole `<=900px` tier rather than only
`<=560px`. Above 900px the pill cannot wrap — worst-case content is ~776px
against 853px available — so separators remain where they are actually wanted.

```diff
  .ow-<pre>-hero__stat{padding:0 14px;font-size:10px;}
+ .ow-<pre>-hero__stat:not(:last-child)::after{display:none;}
```

Applied to vr-arcade (326), laser-maze (312), floor-is-lava (294), vr-escape
(420), xr-party-game (577), vr-machine-ride (338), tap-tap (364). Database edits
to both `post_content` and `_elementor_data`, guarded on a unique anchor,
verified by counting the hide-rule (1 before, 2 after), element caches cleared,
originals in `~/overworld-backups/stat-separators-20260805/`.

**One remaining candidate, deliberately not changed.** `/faq/` has
`.ow-faq__tab + .ow-faq__tab { border-left }` inside `.ow-faq__tabs`, which
carries `flex-wrap:wrap` at base. It cannot currently wrap: measured 3 tabs
totalling 406px inside 867px of available width, and below 760px the container
is `flex-wrap:nowrap` with `flex:1` tabs. The wrap branch is unreachable, so
there is nothing to fix today. It would become real if a fourth or fifth outlet
tab were added — 5 tabs would be ~676px against ~681px available at 761px, right
at the edge. Worth remembering when an outlet is added.

Verification:

```text
Scan pass 1 (border-left/right only)      2 candidates
Scan pass 2 (+ pseudo separators,
             + display:none neutralised)  9 candidates
After fix:  SAFE 8  /  UNGUARDED 1 (faq, unreachable)

All 7 pages: hide-rule present twice in served CSS, HTTP 200, 0 PHP errors.
Rendered offline at 570px from live CSS, before and after: 3 stray lines gone.
```

## 2026-08-05 - Activity Page Hero Padding + VR Free Roam Stat Dividers

Reported as the mobile header-to-hero gap still being big ("reduce 30 px"), and
the VR Free Roam 25/50 section still showing a line that looks weird.

**Why the earlier padding change missed these.** The 2026-08-04 entry reduced
nine heroes, all of them child-theme templates. The eight activity pages are
Elementor pages whose hero CSS lives in `post_content` / `_elementor_data`, not
in the repo, so nothing in that change reached them. Measured on live
`/vr-free-roam/`: the DOM chain from `#masthead` to the hero contributes
**zero** margin or padding at every level — `#page`, `#content`, `.page-content`,
`.elementor`, and two Elementor wrappers all sat at top 87px with 0/0. The whole
gap was the hero's own `padding-top`, which was still 140px on desktop and
120px on a phone.

| Page | tablet | phone |
|---|---|---|
| vr-arcade, laser-maze, floor-is-lava, vr-escape, xr-party-game, vr-machine-ride, tap-tap | 100 → **70px** (≤900) | 80 → **50px** (≤560) |
| vr-free-roam | 120 → **50px** (≤760, single tier) | same |

The seven siblings got exactly the −30px asked for. VR Free Roam went 120 → 50
rather than 120 → 90: it has only one breakpoint and was sitting 40px above its
siblings on a phone, so a literal −30 would have left it at 90px and still
visibly the odd one out. Matching the siblings was the point of the request.

Desktop base padding (140px) was left alone — the report was about mobile.

**The stray line.** The stats pill draws its dividers with
`.ow-vfr-hero__stat + .ow-vfr-hero__stat { border-left }`. Once centring made it
wrap (previous entry), the first item of every new row drew a border against
nothing — a vertical line hanging at the left of row 2. None of the seven
sibling pages use dividers at all; they separate stats with padding alone. So
the divider is now switched off at the wrap breakpoint and kept on desktop,
where the pill is a single row and the dividers are intentional.

```diff
  .ow-vfr-hero__stats{flex-wrap:wrap;border-radius:16px;}
+ .ow-vfr-hero__stat+.ow-vfr-hero__stat{border-left:0;}
```

All eight are database edits — guarded `str_replace` on fragments containing no
JSON-special characters, applied to both `post_content` and `_elementor_data`,
with each page's originals backed up to
`~/overworld-backups/activity-hero-pad-20260805/`. `_elementor_element_cache`
deleted per page (see the previous entry — without it the edits are invisible).

Verification (served CSS, cache-busted):

```text
vr-free-roam     50px 24px 80px    (both tiers)   divider fix: yes
vr-arcade        70px 24px 140px   50px 18px 120px
laser-maze       70px 24px 140px   50px 18px 120px
floor-is-lava    70px 24px 140px   50px 18px 120px
vr-escape        70px 24px 140px   50px 18px 120px
xr-party-game    70px 24px 140px   50px 18px 120px
vr-machine-ride  70px 24px 140px   50px 18px 120px
tap-tap          70px 24px 140px   50px 18px 120px

All 8: HTTP 200, 0 PHP errors, JSON valid, fragments unique before replace.
```

The pill was rendered offline at 390px from the live CSS, before and after: the
stray row-2 line is gone and both rows centre. The script's own verify flagged
vr-free-roam as failed — a bug in the check, not the edit: the divider
replacement string contains the search string as a prefix, so `strpos` still
finds it afterwards. Confirmed correct by direct read-back (old padding absent,
new rule present exactly once, JSON valid).

Note: the nine template heroes sit at 40px on mobile, these eight now at 50px.
Different design weight, but worth aligning if it reads inconsistent.

## 2026-08-05 - VR Free Roam Hero Stats Pill Centred

Reported as the hero text on the "25 / 50" section not being centre-justified
like the other activity pages.

The stats pill (`25 / 50 Min Session · 20+ Games · 2–6 Players · Age 7+`) picks
up `flex-wrap:wrap` at `<=760px` but never had `justify-content:center`, so once
it wrapped onto a second row the items packed left while the rest of the hero
stayed centred. Above 760px it looked fine — it is `inline-flex` inside a
`text-align:center` parent — which is why this only showed on mobile.

All seven sibling activity pages already carry `justify-content:center` on their
own `__stats` rule (`ow-vra-`, `ow-lm-`, `ow-fil-`, `ow-vre-`, `ow-xr-`,
`ow-vmr-`, `ow-tt-`). VR Free Roam was the only one without it.

```diff
- .ow-vfr-hero__stats{ display:inline-flex;align-items:center;gap:0;
+ .ow-vfr-hero__stats{ display:inline-flex;justify-content:center;flex-wrap:wrap;align-items:center;gap:0;
```

`inline-flex` was kept deliberately — the pill hugs its content on this page,
where the siblings use a full-width bar. The brief was to centre it, not to
restyle it. `flex-wrap:wrap` was added at base to match the siblings so it can
never overflow between 760px and the width where it stops fitting.

This page is Elementor-built, so the change is a database edit, not a file edit.
Page 646, applied to both `post_content` and `_elementor_data` via a scripted
`str_replace` on a fragment containing no JSON-special characters, so the
Elementor JSON stayed valid. Originals backed up to
`~/overworld-backups/vfr-hero-stats-20260805/`.

**Gotcha worth remembering:** the edit appeared to do nothing at first. Elementor
keeps a rendered copy of the page in the `_elementor_element_cache` post meta and
serves that; a LiteSpeed purge does not touch it, and a cache-busting query
string returned the same stale HTML. Deleting that meta key made the change
appear immediately.

Verification:

```text
DB read-back:  post_content YES, _elementor_data YES, JSON valid, old fragment gone
               _elementor_data 28488 -> 28526 bytes
Served page:   justify-content:center  present
               flex-wrap:wrap          present
               4 stat items rendered, 0 PHP errors, HTTP 200
```

NOT visually verified at mobile width — the same viewport limitation as the
padding change applies, and this defect only manifests below 760px. The CSS now
matches the seven sibling pages exactly, but worth a glance on a phone.

## 2026-08-05 - Homepage Popups (new mu-plugin)

New `overworld-popups.php`: a **Popups** section in wp-admin where the client
prepares homepage popups ahead of time — poster image, optional heading and
text, a button with their own label and link, a start/end validity window, an
on/off switch, a display order and a per-visitor frequency.

**Why a post type, not a repeater.** ACF here is the free tier (6.8.4) — no
Repeater, no Flexible Content, no Options page. The rest of the codebase works
around that with flat numbered fields (`outlet_act_1_title`, `_2_`, …), which
caps the list at whatever was hardcoded. Popups are open-ended and each needs
its own dates and button, so each popup is a post. `public => false`, so it has
no URL, no archive, and never enters the sitemap; it is also absent from
`ow_seo_post_types()`, so overworld-seo.php ignores it.

Fields: `ow_popup_enabled`, `_image`, `_heading`, `_text`, `_btn_label`,
`_btn_url`, `_btn_blank`, `_start`, `_end`, `_order`, `_frequency`.

**Validity** is inclusive and read in Asia/Singapore, matching
overworld-promo-countdown.php: a start date opens at 00:00:00, an end date runs
to 23:59:59 that day. Either may be blank (no start = live once switched on, no
end = runs until switched off).

**The date check runs twice, deliberately.** LiteSpeed caches the homepage, so a
popup filtered out server-side is only filtered at the moment the page was
generated — a cached copy could keep serving a popup hours after it expired.
Every slide therefore also carries `data-start`/`data-end` unix timestamps and
the script re-checks them before opening. Cache purges on save/trash/delete
cover the common case; the JS check covers a window that rolls over while a
cached page is still being served.

**Presentation.** Renders via `wp_footer` (the homepage is Elementor-built, so
there is no template to hook). One image modal; when more than one popup is
live it becomes a carousel with arrows, dots, a keyboard path (Esc, ←/→), a
focus trap, `inert` on off-screen slides, backdrop dismiss and
`prefers-reduced-motion` support. Slides stack in one grid cell so the dialog
takes the height of the tallest and never jumps between slides. Poster frame is
locked to `aspect-ratio: 1200/630` with `object-fit: cover`, so an off-ratio
upload is centre-cropped rather than letterboxed. Palette and type match the
info/pricing templates.

**Frequency** is per-popup but they share one modal, so the *most frequent*
setting among the live set wins — suppressing an "every visit" campaign because
an unrelated popup said "once a day" would be the wrong call. Changing the live
set (adding, switching on, expiring) resets every visitor's dismissal.

**Preview.** `?ow_popup_preview=1` on the homepage while logged in with
`edit_posts` shows every published popup ignoring the switch and the dates, with
a "Preview" flag and no dismissal written. Lets a popup be checked before it
goes live, and made the verification below possible without exposing anything.

Verification (draft popups only, created and deleted inside one `wp eval-file`,
so nothing was ever publicly visible):

```text
STATUS      live / scheduled / expired / off        5/5 PASS
WINDOW      start 2026-08-03 00:00:00, end 2026-08-10 23:59:59 (inclusive)
ORDERING    order=1 leads order=5 despite later creation    PASS
FILTERING   scheduled + expired + off + no-image excluded   PASS
FREQUENCY   day + always -> always                          PASS
MARKUP      slide class, is-active, data-start/end, <img>,
            button label + href, nl2br, no target on internal link  PASS
PREVIEW     returns all 5 with an image, ignoring state     PASS
CLEANUP     6 test popups deleted, 0 remaining in DB

Image size: ow_popup => 1200x630 crop=true (registered)
Post-deploy: homepage HTTP 200, 0 popup markup (none exist yet), 0 PHP errors
             /outlet/funan/ 200, /blog/ 200
```

The plugin is inert until the first popup is created and switched on — nothing
on the site changes yet.

NOT visually verified. The modal's rendered appearance and its carousel/keyboard
behaviour have not been seen in a browser; the checks above are server-side
markup and logic assertions. Worth opening the homepage with
`?ow_popup_preview=1` once a real poster is in.

## 2026-08-04 - Mobile/Tablet Hero Top Padding Reduced Site-Wide

Reported as "in mobile view, most of the pages padding is huge" — the gap
between the sticky header nav and the start of hero content.

Root cause: `#masthead` is `position: sticky`, not `fixed`. A sticky header
occupies normal flow space, so it never overlaps the content beneath it.
Measured on live `/outlet/funan/`: the hero's `top` equals the header height
exactly (87px). Every pixel of the hero's own top padding was therefore
additive whitespace *below* the header, not clearance for it. The comment at
`style.css:780` ("heroes keep their own padding — they sit under the sticky
header") describes fixed-header behaviour that does not apply here, which is
the likely reason the values drifted as high as 80–96px.

Ten hero blocks each carried their own padding across nine files. All now
follow one ladder — **56px tablet / 40px mobile / 36px small phone** —
horizontal and bottom padding left untouched.

| File | Class | Tablet | Mobile |
|---|---|---|---|
| `page-pricing.php` | `.ow-pri__hero` | 90 → 56 | 70 → 40 |
| `page-event-hub.php` | `.ow-hub__hero` | 90 → 56 | 70 → 40 |
| `page-event-listing.php` | `.ow-evt__hero` | 90 → 56 | 70 → 40 |
| `single-event_package.php` | `.ow-pkg__hero` | 90 → 56 | 70 → 40 |
| `single-experience.php` | `.ow-single__hero` | 80 → 56 | 56 → 40 (≤380: 48 → 36) |
| `single.php` | `.ow-post__hero` | *(none)* → 56 | 80 → 40 |
| `home.php` | `.ow-blog__hero` | 90 → 56 | 70 → 40 |
| `page-faq.php` | `.ow-faq__hero` | 90 → 56 | 70 → 40 |
| `style.css` | `.ow-info__hero` | 96 → 56 | 76 → 40 |

`single.php` had no tablet tier at all — it jumped from 110px straight to the
mobile value, leaving blog posts the lone outlier between 641–1000px. A
`max-width:1000px` block was added so it matches the rest.

The hero `::before` glow is anchored `bottom:0; height:80%` in every template
and hero content sits in a normal-flow `-grid`/`-inner` wrapper, so no
decoration or absolutely-positioned element depended on the old top padding.

Deploy: all nine files verified byte-identical to live *before* editing (no
server-side drift), backed up to `~/overworld-backups/hero-padding-20260804/`,
pushed by targeted rsync, LiteSpeed purged.

Verification:

```text
Post-deploy md5 local vs live: 9/9 MATCH
php -l on all 8 templates:     no syntax errors

Values confirmed served:
  /outlet/funan/          .ow-pri__hero  56px tablet / 40px mobile
  /blog/                  .ow-blog__hero 56px / 40px
  /faq/                   .ow-faq__hero  56px / 40px
  /team-building/         .ow-hub__hero  56px / 40px
  /events/funan-package-a/ .ow-pkg__hero 56px / 40px
  style.css               .ow-info__hero 100px base / 56px / 40px
```

NOT visually verified. `resize_window` reported success but `innerWidth` stayed
pinned at 947px and `outerWidth` read 0, so no real mobile viewport was
obtained in this environment. Values above are read from the served CSS, which
is exact, but the rendered result was never seen — worth an eyeball on a phone.

## 2026-07-29 - Every Page Now Shares A Real Photograph (SEO v1.2.0)

v1.1.0 left 27 URLs falling back to the logo when shared, and flagged setting a
Share Image on each as a manual job. Asked to cover them too, so the fallback
chain now derives an image from content the site already has rather than
waiting for someone to pick one by hand.

**Result: 167 of 167 URLs share a real photograph. None falls back to the logo.**

Every source is read live, so replacing a photo in the CMS updates the share
image with it — nothing is hardcoded to an attachment ID except the one brand
default:

- **The 8 activity pages** take the photo from the matching activity card on
  the outlet pages. Matched on the card's `outlet_act_N_link` — its own pointer
  at the activity page — with the title as a secondary key, because a link is
  an exact identifier where two strings merely being equal is a coincidence
  waiting to break. (First attempt matched on `outlet_act_N_name`; the field is
  actually `_title`, which is why the first deploy still showed the logo there.)
- **Book Now and gift voucher pages** take their outlet's photo, so
  /book-now-funan/ shares the Funan shop front.
- **The 6 team building / birthday party listing pages** take the first package
  card shown on that page, matched on `event_type` + `event_outlet`.
- **Activity pages and experience_type archives** without a card fall back to
  the first game's artwork from the experience CPT.
- **Everything genuinely generic** — homepage, /about/, /blog/, /booking/,
  /outlet/, /contact/, /faq/, the three legal pages, /gift-voucher/ and the
  category archives, 11 in all — shares one brand image: attachment 707, the
  wide VR arena shot at 1512x864, which is close to the 1.91:1 that WhatsApp and
  Facebook crop to. On these pages no specific photograph is more correct than
  another, and a bare logo reads as a broken preview. Change it with the
  `ow_seo_default_share_image` filter.

The outlet LocalBusiness nodes pick up the same images, so the structured data
and the share preview agree.

Backup: `~/overworld-backups/overworld-seo-v1.1.0-20260729.bak`. Re-swept all
169 URLs: still 169/169 clean, and no PHP errors.

Two content items still need a person, both unchanged from the entries below:
the five empty stub pages (`/outlet/`, `/gift-voucher/` and its three children)
have share images and metadata now but still no page content, so they stay
noindexed; and Dream Hacker 2 and 3 still share one blurb.

## 2026-07-29 - Structured Data Built From Real Site Data, Plus Keywords (SEO v1.1.0)

Follow-up to the entry below, on request: deepen the schema and add the
keywords tag that v1.0.0 deliberately left out.

**Keywords.** Now emitted, and every URL has one — 34 hand-written sets in
`ow_seo_page_keywords()`, generated sets for the CPTs, plus a "Focus Keywords"
field in the SEO box. Written as an honest description of each page rather than
a term list: Google has ignored the tag since 2009 and Bing treats a stuffed one
as a spam signal, so a clean tag is the only version worth shipping. The field's
instructions say so plainly, so nobody later mistakes it for a ranking lever.
Blog posts have no tags (0 of 17), so those fall back to the post's own subject
rather than shipping an empty tag.

**Schema, all from data already in the CMS — nothing invented:**

- `FAQPage` on /faq/ and the three outlet pages, built from the 47-item FAQ CPT.
  The query mirrors page-faq.php and page-pricing.php exactly, including the
  rule that an FAQ with an empty `faq_outlet` applies to every outlet, because
  the markup has to describe what is actually rendered. Site-wide questions
  repeat per outlet, so /faq/ is deduplicated: 38 unique questions from 47 rows,
  17 on Funan, 21 on Orchard, 23 on Kallang. Verified every question in the
  markup appears verbatim in the page body.
- `priceRange` on each outlet, computed from the `pricing_item` rows using the
  same peak-price rule as the pricing tables ($15-$84 Kallang, $10-$27 Orchard,
  $16-$49 Funan), so it cannot drift from the published prices.
- `Service` + `AggregateOffer` on the 8 activity pages, with real low/high
  prices in SGD keyed off `pricing_activity`. Combo rows excluded — they belong
  to more than one activity page.
- `VideoGame` on all 81 experiences, with `genre` from experience_type and
  `numberOfPlayers` / `typicalAgeRange` parsed from `exp_players` ("1-5") and
  `exp_age` ("8+").
- `Organization` gains `sameAs` (the Facebook and Instagram profiles linked in
  the footer — verified profiles only, since a wrong entry is worse than none)
  and a postal address; `WebSite` gains a `SearchAction`; outlets gain
  `currenciesAccepted`, `isAccessibleForFree` and an image; and a `WebPage` node
  ties each URL into the graph.

Node counts across 169 URLs: 169 Organization / WebSite / BreadcrumbList, 167
WebPage, 81 VideoGame, 17 Article, 9 EntertainmentBusiness, 8 Service, 6
FAQPage.

**The two event-hub pages are no longer a gap.** `overworld-event-hub-content.php`
owns their title, description, social tags and FAQ schema, so this plugin used
to skip them entirely — leaving them without robots, keywords or the site-wide
entity. It now fills in exactly those three and nothing else, as a second
JSON-LD block containing only Organization and WebSite. Deliberately not a
second BreadcrumbList or FAQPage: a duplicate of those would be a contradiction
rather than extra detail.

**Share images.** No page on this site sets a featured image, so every page was
falling back to the logo when shared on WhatsApp or Facebook. `_elementor_data`
turned out to hold no upload URLs at all — the visuals are ACF image fields
holding attachment IDs. The resolver now scans post meta for those, ranks them
(hero/poster > gallery > activity > rest), and requires at least 800px wide so
it cannot pick an icon. Activity pages and experience_type archives have no
images of their own, so they borrow the first game's artwork from the
experience CPT. Result: **140 of 167 URLs now share a real photograph, up from
about 98.** Cached in `_ow_seo_og_image`, cleared on `save_post`.

The 27 still on the logo are the legal pages, the booking pages, the noindexed
gift-voucher stubs, the category archives and the five physical-activity pages
(Floor Is Lava, Laser Maze, Tap Tap, XR Party Game, VR Machine Ride) that have
no image field anywhere. **Setting a Share Image on those in the SEO box is a
one-minute job per page** and is the intended use of that field — it needs
someone to choose the photo.

Backup: `~/overworld-backups/overworld-seo-v1.0.0-20260729.bak`. Re-swept all
169 URLs after each deploy: still 169/169 clean, zero duplicate titles, every
URL carrying title, description, keywords, robots, canonical and valid JSON-LD.

## 2026-07-29 - Site-Wide SEO Metadata For Every URL

Client reported that pages were missing titles, descriptions and "those".
Correct: there is no SEO plugin on this site. `soro-seo` is installed but is an
AI article-publishing connector, not a metadata plugin — it emits no tags.

Before this change, of 169 indexable URLs only 20 had a meta description. The
rest shipped the WordPress fallback title (`VR Arcade – Overworld`) and nothing
else, so Google wrote its own snippets from whatever text it hit first. Only
three places had any metadata at all: `single.php` (blog posts), `home.php`
(blog index) and `overworld-event-hub-content.php` (the two event landing
pages).

New mu-plugin `wp-content/mu-plugins/overworld-seo.php` is now the single
source of truth:

- Title tag, meta description, robots, canonical, Open Graph, Twitter card and
  schema.org JSON-LD for pages, posts, experiences, event packages, promos and
  taxonomy archives.
- ACF "SEO" box on every one of those post types: Search Title, Search
  Description, Share Image, Hide From Search Engines. Every field optional —
  empty falls back to the built-in text, same convention as the outlet and
  event-hub pages.
- Structured data: `Organization` + `WebSite` everywhere;
  `EntertainmentBusiness` for each outlet (real address, phone, email, 11:00–
  22:00 daily) on the homepage, `/outlet/` and each outlet page; `Article` on
  blog posts; `BreadcrumbList` built from real ancestors, so
  `/team-building/funan/` reads Home > Team Building > Funan.

**Defaults are keyed by page ID, not slug.** Twelve pages share three slugs —
`kallang-wave-mall` is page 504 (outlet), 524 (team building), 527 (birthday
party) and 530 (gift voucher). Slug matching would have given four different
pages identical metadata. Same reasoning as the template guard.

34 page titles and descriptions were hand-written from copy already on the live
pages — no invented prices, group sizes or inclusions. Prices were deliberately
left out of descriptions so they cannot go stale. The remaining 128 CPT URLs
generate from their own editorial fields (`exp_intro`, `exp_tagline`,
`event_tagline`, `promo_tagline`) rather than a template.

Meta keywords deliberately not emitted: Google dropped it as a ranking signal
in 2009 and Bing treats it as a spam signal.

Four problems found and fixed while verifying:

1. **Duplicate description tags on every post with an excerpt.** The Hello
   Elementor *parent* theme has its own `hello_elementor_add_description_meta_tag`
   that prints one from `post_excerpt`. Disabled via its
   `hello_elementor_description_meta_tag` filter.
2. **Seven pairs of byte-identical titles and descriptions.** Legitimately
   distinct content that happens to share a name: "Pixel Hack" exists as both a
   VR Arcade and a VR Free Roam title, and every event package name appears once
   for team building and once for birthday parties. Titles now carry the
   qualifier (`Pixel Hack | VR Free Roam | Overworld`) and descriptions come
   from each item's own copy.
3. **Beat Saber's description was 8 characters.** Its `post_excerpt` is the
   category label "VR Games", which was winning over the real 400-word
   `exp_intro`. Excerpts under 60 characters are now skipped in favour of the
   custom fields.
4. **Two titles ran past 60 characters** where a long post name met a brand
   suffix. Title parts are now dropped from the end until the whole fits.

Crawl hygiene: `elementor-hf` (the header and footer builder templates) and the
author archives were listed in `wp-sitemap.xml` — both removed, and the header/
footer fragments now carry `noindex, nofollow`. Canonical added for the blog
index and taxonomy archives, which core only emits on singular views.

Also trimmed the two event-hub meta descriptions (169 and 161 chars) under 160,
and gave those two pages the robots directive they lacked.

**Five pages are noindexed because they are empty stubs** — `/outlet/`,
`/gift-voucher/` and its three outlet children have 20 bytes of post_content,
no H1 and no Elementor data, but were sitting in the sitemap. They have titles
and descriptions ready; remove the `noindex` flag in `ow_seo_page_map()` once
they have real content. **This needs the client's attention** — `/outlet/` in
particular is a natural landing page.

Backups: `~/overworld-backups/{single,home}.php-before-seo-20260729.bak`,
`event-hub-content-before-seo-20260729.bak`. The mu-plugin was staged in `/tmp`
and `php -l`-checked on the server (PHP 8.3) before being moved into
`mu-plugins/`, since anything in that directory loads automatically and a fatal
would take the site down.

Verified live by sweeping all 169 sitemap URLs: every one returns 200 with a
unique title, a description, a robots directive, a canonical and valid JSON-LD.
165 of 169 descriptions land in the 120–165 character range and none is under
80. Zero duplicate titles. Editability tested end to end on /vr-arcade/: wrote
an override via meta, saw it live, reverted, confirmed the default came back.

Remaining content issue for the client: **Dream Hacker 2 and Dream Hacker 3
share the same intro copy**, so those two URLs have identical descriptions.
Their titles differ. Needs distinct copy written for one of them.

## 2026-07-29 - Team Building & Birthday Party Turned Into Search Landing Pages

Client asked for `/team-building/` and `/birthday-party/` to work as Google
landing pages and to be editable. Both pages already existed on
`page-event-hub.php`; the problem was that every word was hardcoded in the
template and the pages had no search plumbing at all — no meta description,
no Open Graph, no structured data, and an H1 of just "Team Building".

New mu-plugin `wp-content/mu-plugins/overworld-event-hub-content.php`:

- ACF group "Landing Page Content" on any page using the Event Hub template,
  so both pages get it. Grouped into accordions: Google & Sharing, Top Of
  Page, About This Page, Why Book With Us (4 points), Outlet Card Blurbs,
  FAQ (6 slots), Bottom Call To Action.
- Every field optional. Defaults per event type live in this plugin (not the
  template) because the title tag and meta description need them too, so an
  untouched page renders exactly as before.
- `document_title_parts` filter for the title tag; `wp_head` at priority 2
  for meta description, Open Graph, Twitter card, and JSON-LD
  (BreadcrumbList + FAQPage built from the FAQs actually rendered).
- Clears `_elementor_element_cache` on ACF save.

`page-event-hub.php`: reads those fields through `ow_hub_val()` /
`ow_hub_points()` / `ow_hub_faqs()`, and gained three sections between the
outlet cards and the enquiry CTA — "Why Book With Us" (4 points), a body
copy block, and an FAQ accordion (`<details>`/`<summary>`, no JS).

Copy defaults were also strengthened for search: H1 "Team Building
Activities in Singapore" / "Birthday Party Venues in Singapore" (was just
"Team Building" / "Birthday Party"), plus title tags, meta descriptions,
4 selling points, ~90 words of body copy and 3 FAQs per page. FAQ answers
were kept to facts already on the site (outlets, activities, how to book) —
no invented prices, group sizes or inclusions.

Backup: `~/overworld-backups/page-event-hub-before-landing.php`.

Verified live on both pages: title tag, meta description, og:*, H1, 3 outlet
cards, 4 points, body copy, 3 FAQs, and valid JSON-LD parsing to
`['BreadcrumbList','FAQPage']`. Page text roughly doubled (~390 → ~780
words). Editability tested end to end: wrote a test tagline via meta, saw it
on the live page, reverted, and confirmed the default came back.

Follow-up: **the landing pages were unreachable from the menu.** In the
header nav, Events → "Team Building" and "Birthday Party" were both
`href="#"` — they existed only to open their submenu, so on desktop clicking
them did nothing and the only way in was via an outlet child page. Nobody
would have found the new landing pages, and Google follows internal links.

Fixed via `scripts/nav-event-hub-links.php` (header-footer-elementor
template post 29, HTML widget `36ab9a3`, idempotent):

- Each parent now points at its landing page, so a desktop click navigates
  while hover still opens the submenu.
- Added an explicit "All Outlets →" item as the first entry in each submenu.
  Necessary, not decorative: the nav JS calls `preventDefault()` on
  `.has-submenu > a` for touch/≤1300px so the tap opens the submenu, which
  means the parent link alone would never work on a phone.

Backup: `~/overworld-backups/header29-elementor-before-nav-links.json`.
Verified by clicking the live menu — lands on /team-building/ with the right
title and H1; all four event URLs return 200.

Two follow-ups to yesterday's work.

**1. Guard extended to every template-driven page** (v1.1.0 of
`overworld-outlet-template-guard.php`, renamed "Page Template Guard"):
3 outlet pages, FAQ, 2 event hubs, 6 event listings — 12 in all.

Switched from slug matching to an explicit ID => template map. This was a
latent bug in v1.0.0, not a style preference: the event listing pages reuse
the outlet slugs (`kallang-wave-mall` is page 504 *and* page 524), so the
slug-based guard would have forced `page-pricing.php` onto the event
listings the next time one was saved. No page was saved in that window, so
nothing was damaged. IDs also mean a page recreated under a new ID falls
outside the guard rather than getting the wrong template — the safe way to
fail. Filter `ow_guarded_page_templates` to amend the map.

Tested live across all 12: `update_post_meta(..., 'default')` and
`delete_post_meta` both leave every page on its own template. All 12 URLs
verified 200 with full-size responses afterwards.

**2. Combo badge repositioned** (`page-pricing.php`): the badge
("Save $5 each player", "Most Popular") moved from an overlay on the photo
to inside the card body, directly under the combo name — the client asked
for the highlight to sit where "Best Seller" reads, below the title. Order
is now name → badge → description → included pills → prices → button.
Dropped the white ring (it existed to survive a busy photo behind it) and
kept the accent gradient, dot and glow. The old no-image inline fallback and
its inline styles are gone — one code path now.

Verified on all three outlets; Kallang shows it against the client's new
"Double Thrill Combo Deal" artwork.

**3. "Label Next To Name" field for combos.** The client had "Best Seller"
sitting in the Description field, where it printed as grey body text under
the badge — redundant once the badge moved there. Added
`outlet_combo_{i}_label` to the Combo Deals field group (text, "Small tag
beside the combo name, e.g. Best Seller") rendered as an inline pill inside
the `<h3>`, so it sits on the title line and wraps with it. Styled quiet on
purpose — 9.5px mono, accent outline, transparent fill — so it labels the
combo without fighting the loud savings badge below.

Migrated Kallang combo 1 on the live DB: `outlet_combo_1_desc`
"Best Seller" → `outlet_combo_1_label`, description cleared. Field key
references written alongside both values so the ACF boxes show them.

Verified: label renders on the title line, all three outlet pages 200,
`field_outlet_combo_1_label` registered.

Client reported `/outlet/kallang-wave-mall/` blank after their edit.

Symptom: page returned 200 but 77KB instead of ~145KB, with the body class
`page-template-default` and none of the `ow-pri__*` sections. No PHP error —
the HTML was complete, just empty of page content.

Root cause: page 504's `_wp_page_template` had been reset from
`page-pricing.php` to `default` (post_modified 18:03), and
`_elementor_edit_mode` was set to `builder` — the page had been opened/saved
in Elementor, which dropped the custom template. With the Pricing Page
template no longer assigned, none of the outlet markup ran, and
`_elementor_data` was empty (0 bytes), so nothing rendered in its place.

Nothing was lost: all ACF content survived (4 activity cards, 5 gallery
images, and a new combo the client had just added).

Fix: `update_post_meta(504, '_wp_page_template', 'page-pricing.php')`, then
cleared `_elementor_element_cache` and purged LiteSpeed. Meta backed up
first to `~/overworld-backups/page504-meta-before-template-restore.json`.

Verified after: 145KB, all 7 sections present, 4 pricing tables, 10 gallery
images, 23 FAQs, no PHP notices. `_elementor_edit_mode = builder` was left
alone — Funan carries the same flag and renders correctly, so builder mode
alone is not the trigger; the template assignment is.

Client's own Kallang combo now renders: "Floor Is Lava + VR Machine Ride",
badge "Save $5 each player", $26/$29 entered manually (Floor Is Lava $16 +
VR Machine Ride $15 = $31, less $5). No image uploaded, so the badge falls
back to sitting inline above the title — that path works.

Guard added same day — `wp-content/mu-plugins/overworld-outlet-template-guard.php`:

- Blocks `_wp_page_template` writes on the three outlet pages (matched by
  slug, not ID) unless the value is `page-pricing.php`, via the
  `update_post_metadata` / `add_post_metadata` filters. Returns `true` so the
  caller believes it succeeded and carries on instead of erroring.
- Also blocks deletion of that meta — an empty value falls back to the
  default template just as surely as writing "default".
- Belt and braces: `save_post_page` (priority 99) repairs the value with a
  direct `$wpdb` write, covering anything that bypasses the meta API, and
  clears `_elementor_element_cache`.
- Admin notice on those pages' edit screens: locked to the Pricing Page
  template, edit via the ACF boxes, do not use "Edit with Elementor".
- Escape hatch: `define( 'OW_DISABLE_TEMPLATE_GUARD', true )` in wp-config,
  or the `ow_outlet_template_guard_enabled` filter.

Tested live on all three pages: `update_post_meta(..., 'default')` and
`delete_post_meta` both leave the template at `page-pricing.php`; a direct
SQL write followed by `wp_update_post` is repaired on save; an unguarded
page (16) still accepts template changes normally.

## 2026-07-28 - Outlet Pages: Client-Editable Combo Deals Section

New section on the outlet pages, directly under "Activities & Games":
combo/bundle cards the client fills in themselves, with image, copy and
optional prices. Section hides entirely when no combo has a name, so the
other outlets are unchanged until someone fills them in.

New mu-plugin `wp-content/mu-plugins/overworld-outlet-combos.php`:

- Field group "Combo Deals" on any page using the Pricing Page template
  (so all 3 outlet pages get it), following the outlet_act_* convention:
  free ACF has no repeater, so 4 fixed slots in collapsible accordions.
- Per combo: `outlet_combo_{i}_title` / `_badge` / `_desc` / `_includes`
  (comma-separated → pills) / `_weekday` / `_weekend` / `_link` / `_image`,
  plus a section-level `outlet_combos_intro`.
- Empty title hides that combo. Empty prices fall back to the Pricing CPT
  (see below). Empty link hides the button. Empty image drops the photo and
  moves the badge inline.
- Clears `_elementor_element_cache` on ACF save.

`page-pricing.php`:

- Loads the slots into `$outlet_combos` and renders a "Better Together /
  Combo Deals" section between Activities & Games and Group Events.
- Price fallback: exact normalised name match against the per-activity price
  map, then the first priced activity whose name *contains* the combo name —
  so a combo titled "Floor Is Lava + Laser Maze" resolves to the pricing row
  "Combo A - Floor is Lava & Laser Maze". A weekday price with no weekend
  price means the same price all week.
- Reuses the `.ow-pri__act-from` price row from the activity cards (comment
  added at its definition noting both sections use it).

Sample data written to Orchard Central (page 505) via
`scripts/sample-outlet-combos.php`: two combos — "Floor Is Lava + Laser
Maze" (badge "Save $9") and "Floor Is Lava + Tap Tap" (badge "Most
Popular"), images reused from that page's activity cards (1425, 1536),
button → `/book-now-orchard/`. Prices deliberately left blank so the page
demonstrates the Pricing CPT fallback — both render $23 weekday / $27
weekend from the existing Combo A / Combo Deal B rows.

Verified live: 2 combo cards on Orchard, 0 on Kallang and Funan (section
correctly absent), prices and badges rendering, field group registered.

Client workflow: WP Admin → Pages → outlet page → Combo Deals box → expand
"Combo 1" → fill Combo Name, description, what's included, upload an image,
Update.

Follow-up (same day): rolled the section out to Funan (page 506) — one
combo, "Floor Is Lava + XR Party Game", badge "Save $9", image 1236,
button → `/book-now-funan/`, prices again resolved from Pricing ($23/$27
via the "Combo Deal - Floor Is Lava & XR Party Game" row).

**Kallang (504) left empty on purpose:** it has no combo rows in the
Pricing CPT at all, so there is no real bundle to publish. Needs the client
to say which combos that outlet sells and at what price — inventing one
would put a product on the site that does not exist. The section stays
hidden there until then.

Also capped `.ow-pri__combo-card` at 520px: with a single combo the grid
track was the full 1200px, which blew the 16:9 image up to 1200×675. Cards
now cap at 520 and left-align; two or more still fill the row normally.

Follow-up (same day): the badge was too quiet against the busy game photos.
Rebuilt it as the card's hook — 13px (was 10px), accent-glow → accent
gradient fill, 2px near-white ring so it reads over any image, a dot marker,
outer glow in the outlet accent, and a scale-up on card hover. Sized down to
11.5px under 600px. Nothing else in the card changed.

## 2026-07-28 - Outlet Pages, Section 2: "From" Prices On Activity Cards

Follow-up to the homepage entry below — the client meant the **outlet
detail** pages, not the homepage. Each activity card in "Activities & Games"
(section 2 of `page-pricing.php`) now shows the cheapest weekday and
cheapest weekend & PH price for that activity at that outlet, between the
description and the Learn More button.

`page-pricing.php` only (theme file, no DB change):

- Builds `$activity_from_prices` from the `$pricing_items` already queried
  for the page — no extra queries. Keyed on a normalised activity name
  (lowercase, alphanumerics only) so an ACF card titled "Floor is Lava"
  still matches the "Floor Is Lava" pricing rows. No match = no price line,
  so combo-deal rows and unpriced cards degrade quietly.
- Same peak rule as the tables below: rows with `pricing_has_peak != '1'`
  repeat the weekday price on the weekend side. Zero/empty weekday rows are
  skipped.
- `.ow-pri__act-from` sits after `.ow-pri__act-desc`, which has `flex:1`, so
  the price rows and buttons stay aligned across cards of different text
  lengths. Weekend figure uses `--accent-glow` (per-outlet colour).
- Label sized 8.5px/.07em: "Weekend & PH from" measures 97px and the
  narrowest column the grid produces (4-card row) is 110px. At the original
  9.5px/.14em it was 120px and overflowed into the card padding.

Verified live on all three outlets — every card matched a pricing group:
Kallang VR Arcade $23/$26, VR Escape $39/$44, Floor Is Lava $16/$19,
VR Machine Ride $15/$15; Orchard Floor Is Lava $16/$19, Laser Maze $16/$19,
Tap Tap $10/$12; Funan VR Free Roam $26/$29, Floor Is Lava $16/$19,
XR Party Game $16/$19. Rows and buttons measured pixel-aligned.

Backup: `~/overworld-backups/page-pricing-before-act-from-price.php`
(local repo copy was byte-identical to live before the edit).

Note: these cards have only a Learn More button — there is no per-card
Book Now on the outlet pages (booking lives in the page's final CTA).

## 2026-07-28 - Homepage Outlet Cards: "From" Prices (live from Pricing CPT)

Added a two-column price row to the three cards in the homepage OUTLETS
section (the second section, page 16, Elementor HTML widget `b63910e`):
cheapest **weekday** and cheapest **weekend & PH** price for that outlet.
It sits below the game pills and directly above the Book / Learn More
buttons, exactly where the client asked for it.

The figures are **not hardcoded** — they are read live from the
`pricing_item` CPT, so whatever the client edits under Pricing is what the
homepage shows.

New mu-plugin `wp-content/mu-plugins/overworld-outlet-from-price.php`:

- `ow_outlet_from_prices()` scans published `pricing_item` posts, groups by
  `pricing_outlet`, and takes the min of `pricing_weekday_price` and of the
  weekend price. Rows with `pricing_has_peak != '1'` reuse the weekday price
  on the weekend side, matching how `page-pricing.php` renders the tables.
  Rows with no/zero weekday price are ignored.
- Shortcode `[ow_outlet_from outlet="<slug>"]` prints the row (self-contained
  `<style>`, emitted once per request). Renders nothing if the outlet has no
  usable pricing rows, so the card just closes up.
- Elementor's HTML widget prints markup raw, so a scoped
  `elementor/widget/render_content` filter runs `do_shortcode()` only on
  widgets that actually contain `[ow_outlet_from`.
- Result cached in the `ow_outlet_from_prices` transient; saving/trashing/
  deleting a `pricing_item` flushes it **plus** all `_elementor_element_cache`
  postmeta and LiteSpeed. Verified: pricing_item save flushes, page save does
  not.

Widget edit via `scripts/homepage-outlet-from-price.php`
(`wp eval-file`, idempotent): inserts one shortcode per card anchored on its
unique `/book-now-*/` link, and appends CSS to the widget's `<style>` —
per-outlet accent for the weekend figure (Kallang `#6f9bff`, Orchard
`#ff8a3d`, Funan `#c89aff`) plus `margin-top:auto` on `.ow-fp` (and
`margin-top:0` on the following `.actions`) so all three price rows line up
even though the cards have different numbers of game pills.

Gotcha worth remembering: after editing the mu-plugin's CSS the homepage kept
serving the old style block — `_elementor_element_cache` postmeta caches the
*rendered* widget HTML, so a LiteSpeed purge alone is not enough. Clear that
meta too (the plugin now does it automatically on price edits).

Backups: `~/overworld-backups/page16-elementor-before-outlet-from-price.json`
(remote) and the same JSON locally in the session scratchpad.

Live values at time of change (per pax): Kallang $15 / $15 (VR Machine Ride),
Orchard $10 / $12 (Tap Tap 15 Min), Funan $16 / $19 (Floor Is Lava &
XR Party Game). Browser-verified on the live homepage: three rows rendered,
aligned, no raw shortcode text, correct accent colours; long label
"WEEKEND & PH FROM" measured at 120px and shrunk to 98px under 400px viewport
so it fits the single-column mobile card.

## 2026-07-21 - Pricing Hero: Address Overflowed Off-Screen On Mobile

Client report (with screenshot): on mobile the outlet pricing page hero
address/phone pill was not centered — the address ran off the right edge.
Reported as "booking site" but the screenshot was the **pricing** page
(`/outlet/<slug>/`, template `page-pricing.php`), eyebrow "… · PRICING".

Root cause: `.ow-pri__hero-meta-item` had `white-space:nowrap` and the pill
`.ow-pri__hero-meta` had no width cap. Kallang's short address fit; Funan's
long one ("107 North Bridge Road, #04-14 & K1, Funan, Singapore 179105")
could not wrap, so the inline-flex pill grew wider than the phone viewport
and overflowed/clipped on the right — looking off-centre. Desktop was fine
(wide enough to fit on one line).

Fix (`page-pricing.php`, theme file — pushed to live, single file):

- `@media (max-width:1000px)`: `.ow-pri__hero-meta{max-width:100%}` and
  `.ow-pri__hero-meta-item{white-space:normal}` so the address wraps and the
  pill can never exceed the viewport.
- `@media (max-width:600px)`: stack the pill into a centred column
  (`flex-direction:column;align-items:center;gap:8px;border-radius:20px;
  padding:14px 18px`) with `.ow-pri__hero-meta-item{justify-content:center;
  text-align:center}` so address + phone read as centred lines.

Deploy: local repo copy was byte-identical to live before the edit; pushed
just this file via scp (backup `~/overworld-backups/page-pricing-before-
mobilemeta.php`), then `wp litespeed-purge all`.

Verified: new CSS served on all 3 pricing pages (kallang-wave-mall /
orchard-central / funan). Rendering check was done by measurement — the
browser env renders at a fixed 2560px viewport so the mobile media query
can't be triggered live; simulating 354px phone width + applying the mobile
rules gives pill width 354px, 0px overflow, centred (0/0 side gaps).
Applies to all three outlets (same template).

## 2026-07-20 - Book Now Funan: White Card Slivers Fixed

Client report: white border on the right and bottom of the Funan booking box.

Root cause: Funan's Bookeo widget is *dark-themed* (black bg, purple text),
unlike Kallang/Orchard which are white. The 2026-07-20 change gave
`.ow-bk__widget` a `background:#fff` rounded card (safe for the white widgets).
On Funan the dark iframe sat on that white card, so white showed at the edges:
a 7px strip at the bottom (iframe defaults to `display:inline`, leaving a
baseline gap) and a thin sliver at the right.

Fix (`scripts/booking-redesign.php`, re-run on all 3 pages):

- `.ow-bk__widget` background `#fff` -> `transparent` so the card adapts to
  whatever the iframe paints (white for KWM/Orchard, dark for Funan) instead
  of forcing white behind it. `border-radius:16px;overflow:hidden` kept.
- Added `.ow-bk__widget iframe{display:block;width:100%;border:0;}` to remove
  the inline baseline gap and guarantee full-width coverage.

Verified live (Funan, browser): `.ow-bk__widget` computed bg
`rgba(0,0,0,0)`, iframe `display:block`; bottom-right corner is pure black,
no white sliver. (The faint gray rounded line still visible is Bookeo's own
internal card border inside the cross-origin iframe — not ours.) Kallang/
Orchard unchanged — their white iframes still read as rounded white cards.

## 2026-07-20 - Book Now Embed Section Switched To Black

Client request: make the Bookeo embed section on the outlet Book Now pages
black instead of the white it was redesigned to on 2026-07-17.

Key finding (browser-verified): the Bookeo widget is a cross-origin iframe
(bookeo.com) whose *internal document paints its own white background* — the
iframe element itself is transparent, but the form always renders on white and
its text stays readable regardless of our section color. So a black section
does NOT black out the form (my first assumption); it only fills the margins
around it. Confirmed by flipping `.ow-bk__embed` to black in-browser first.

Changes (`scripts/booking-redesign.php`, re-run on all 3 pages 898/886/893):

- `.ow-bk__embed` background `#fff` -> `#000` (matches the hero + help strip).
- `.ow-bk__widget`: added `background:#fff;border-radius:16px;overflow:hidden`
  so the white Bookeo iframe reads as a deliberate rounded card on black.
- Header contrast fixed for the dark bg: `.ow-bk__embed-head` border
  `#eceef2` -> `rgba(255,255,255,.1)`; `.ow-bk__embed-tag` `#7b8291` ->
  `rgba(255,255,255,.72)`; `.ow-bk__embed-note` `#aab0bc` ->
  `rgba(255,255,255,.42)`. Accent status dot unchanged.
- Ran via `wp eval-file -` over SSH (stdin); each page's Bookeo key kept.

Cache: `wp cache flush` alone was NOT enough — the served HTML came from the
LiteSpeed full-page cache. Had to `wp litespeed-purge all` before the change
showed. (Object cache flush only matters for ACF-style field/meta changes.)

Backup: `~/overworld-backups/booking-{898,886,893}-before-blackbg.json`
(each page's `_elementor_data` before the change).

Verification (live, browser, Orchard): `.ow-bk__embed` computed bg
`rgb(0,0,0)`, `.ow-bk__widget` `rgb(255,255,255)` radius `16px`, tag color
`rgba(255,255,255,.72)`. Form fully readable inside the white card; black is
seamless with nav above and help strip below.

## 2026-07-18 - FAQ Outlet Field Made Optional (couldn't save blank)

Client report: leaving an FAQ's Outlet blank is supposed to show it at every
outlet, but wp-admin refused to save with Outlet empty.

Root cause: the `faq_outlet` ACF select (key `field_69f4a95df434f`, post 384)
had BOTH `required=1` and `allow_null=1`. `allow_null` renders the blank
"— Select —" option, but `required=1` blocks saving it empty — so the
intended "empty = all outlets" state was unreachable in the editor.

Changes (live DB only, no theme files touched):

- `acf_update_field`: set `required` to 0 on `faq_outlet`. `allow_null`
  left at 1. Template behavior (`page-faq.php` / `page-pricing.php`:
  empty `faq_outlet` = shown at every outlet) already matches this.
- Flushed the object cache (LiteSpeed drop-in) — ACF caches the field
  config persistently.

Backup: `~/overworld-backups/faq_outlet-field-before.json` (field JSON
before the change).

Verification: `acf_get_field("faq_outlet")` now reports `required=0`,
`allow_null=1`.

## 2026-07-17 - Book Now Pages Redesigned Around The Bookeo Embed

Context: the client-created pages /book-now-kwm/ (898), /book-now-orchard/
(886) and /book-now-funan/ (893) each held a bare Bookeo widget embed and
nothing else. All three embeds currently show Bookeo's "Web site
integration is not enabled" error — that toggle must be enabled by the
client in EACH outlet's Bookeo account (Settings → Theme and layout →
Web site integration); until then the calendar cannot render.

Redesign (live DB, each page's single Elementor HTML widget replaced;
per-outlet Bookeo widget keys kept unchanged):

- Dark site-style hero with the outlet's accent (Kallang blue / Orchard
  orange / Funan purple): brand eyebrow, "Book <Outlet>" Anton title,
  venue + address, activity pills, "Choose a different outlet" →
  /booking/.
- White full-bleed middle section purpose-built for the white-bg Bookeo
  embed: "Live availability · <venue>" tag + "Secure booking powered by
  Bookeo" note above the widget, max-width 1080px.
- Dark help strip: call + WhatsApp links per outlet.
- Note: Funan's Bookeo account uses a DARK theme, so its embed renders
  as a dark block inside the white section — acceptable, but switching
  that Bookeo account's theme to light would match the other two.

Still pending (after the client enables integration in Bookeo):
verify calendars render, then repoint the /booking/ chooser buttons from
bookeo.com to these internal pages, and fix the dead
order.overworldvr.com/booking links on the three outlet pages
(that legacy system renders an empty shell).

Backups: ~/overworld-backups/page-{898,886,893}-before-booking-redesign-
20260717-130238.json. Patch script: scratchpad booking-redesign.php run
via wp eval-file; caches flushed (object, elementor css, litespeed).

Verification (live, browser): all 3 pages render hero/white-embed/help
strip in the right accents; Bookeo error shows inside the white section
(expected until integration enabled); mobile 390px: no horizontal
scroll, hero scales, hamburger present.

## 2026-07-17 - Game Cards Get Details On All 3 Game Pages; Arcade + Escape Grids Now Live

Client report: game cards on the game pages lack the small details the
library archives show (genre e.g. "Shooter", how many pax). Request: add
them to VR Arcade, VR Escape and VR Free Roam.

mu-plugin `overworld-experience-grid.php` v1.1.0:

- Cards now carry the library-archive details: difficulty badge on the
  image (easy/medium/hard/extreme, same colours as the archive), players
  + age row with icons, and genre chips from `exp_vibes` (label map
  matches the archive; unmapped free-form vibes like "co-op" render
  ucwords'd). All 81 games already have this data filled, so every card
  shows details with no content work.
- New config flag `count_in_title` (false for vr-escape) — the count
  prefix produced "23 Pick Your Escape"; heading is back to the original
  "Pick Your Escape" (the "23 Rooms Available" line still shows the count).

Pages 326 (VR Arcade) and 420 (VR Escape) flipped from static snapshot
grids to the live shortcode (`[ow_experience_grid term="vr-arcade" /
"vr-escape"]`), same structure as page 646 — new games published to a
term now appear on all three pages automatically, ending the
regen-needed caveat from the Jul-10 entry.

Deploy notes / gotchas:

- `push-wp-content.sh` dry-run wanted to sync 7.5k files (remote drift) —
  deployed the single plugin file via ssh cat instead.
- After a direct `_elementor_data` edit, the swapped container rendered
  with Elementor's default 10px padding (white strip between sections):
  the page's generated CSS file predates the new element. Fix:
  `wp elementor flush-css` (+ litespeed purge, + delete
  `_elementor_element_cache` meta per the Jul-16 gotcha).

Backups (before change):

```text
~/overworld-backups/page-326-before-live-grid-20260717-054628.json
~/overworld-backups/page-420-before-live-grid-20260717-054628.json
~/overworld-backups/page-646-before-card-details-20260717-054628.json
~/overworld-backups/db-20260717.sql
```

Verification (live, browser + curl):

```text
/vr-free-roam/: 200, 29 cards, title "29 Worlds To Explore"
/vr-arcade/:    200, 29 cards, title "29 Worlds On Tap"
/vr-escape/:    200, 23 cards, title "Pick Your Escape"
Every card: meta row + diff badge; genre chips render (19x Shooter,
11x Co-op, 9x PvP on arcade). View More clicked on arcade: 21 hidden
cards revealed, button hides itself. Section junctions flush after
flush-css (screenshots on arcade + escape + free roam).
```

## 2026-07-17 - FAQ Category Field Added To wp-admin

Client report: no way to set an FAQ's category in wp-admin. The FAQ page
groups FAQs into sections via `faq_category` post meta, but the ACF field
group "FAQ Details" (381) never had a Category field — existing values
were written directly to postmeta at import time, so they were invisible
and uneditable in the editor. New FAQs silently fell into "General".

Changes (live DB only, no theme files touched):

- New ACF select field "Category" (`faq_category`, key
  `field_6a59b7269d87a`, post 1575) in group 381, positioned between
  Answer and Outlet (Outlet/Display Order bumped to menu_order 3/4).
- Choices: General (default), Booking, VR Arcade, VR Free Roam,
  XR Party Game, Floor Is Lava, Laser Maze, Tap Tap — covers all six
  values in live use plus the two extra sections page-faq.php already
  orders. Required, no free text (prevents typo'd stray sections).
- Backfilled `_faq_category` key-reference meta on all 31 FAQ posts so
  the dropdown pre-selects each post's existing category.
- Flushed the object cache (LiteSpeed drop-in) — ACF's field list is
  cached persistently, so the new field didn't appear until flushed.

Backup: `~/overworld-backups/db-20260717.sql` (35M, taken before the
change). Note: `scripts/backup-remote.sh` currently fails silently —
`wp db export` dies on the host (ssh exit 255); direct `mysqldump` with
creds from `wp config get` works and is what was used.

Verification:

```text
acf_get_fields(group 381) after cache flush:
0. Question => faq_question (text)
1. Answer => faq_answer (wysiwyg)
2. Category => faq_category (select, key field_6a59b7269d87a)
3. Outlet => faq_outlet (select)
4. Display Order => faq_display_order (number)

Sample FAQ #417 "How many players and how long?" -> get_field: "Tap Tap"
31 FAQs, 31 key refs set, 0 needed backfill to General
```

## 2026-07-17 - TB/BP Hub Pages For All 3 Outlets + Footer Links Fixed

Client report: the footer's "Team Building" and "Birthday Party" links went
to the Kallang page only (/team-building/kallang-wave-mall and
/birthday-party/kallang-wave-mall). Request: a Team Building page and a
Birthday Party page that host all 3 outlets, linked from the footer.

The parent pages /team-building (521) and /birthday-party (522) already
existed but were near-blank (default template, ~20 chars of content).

New child-theme template `page-event-hub.php` ("Event Hub (TB / BP — All
Outlets)"), assigned to 521 + 522:

- Auto-detects event type from the page slug; lava-orange hero matching
  the site's event-listing design language (.ow-hub, same fonts/CSS
  conventions as page-event-listing.php).
- One card per outlet in its own accent colour (Kallang orange, Orchard
  cyan, Funan purple): brand, address, activity pills, live package count
  (same event_active meta filter as the listing pages), "View Packages"
  link into /[type]/[outlet], and a WhatsApp button.
- Bottom CTA: Contact Us + cross-link to the other event type.

Footer (Elementor template 566, HTML widget): the two links repointed from
the Kallang URLs to /team-building/ and /birthday-party/ (2 replacements).
Element cache deleted; object/Elementor CSS/LiteSpeed purged.
Backup: ~/overworld-backups/event-hub-footer-before-20260717-031107.json
(page templates + footer _elementor_data before change).

Header untouched — its dropdowns already list all 3 outlets per type.

Verification (live HTML sweep; browser extension unavailable): both hub
pages render hero + 3 cards with correct per-outlet links and live counts
(TB 3/7/4, BP 3/3/5 — counts match the listing pages' active filter),
cross-links + contact CTA present, no PHP warnings/fatals; footer on inner
pages (FAQ checked) now links /team-building/ and /birthday-party/.

## 2026-07-17 - XR Modes Image Flow Verified + ACF/Element-Cache Guard

Client asked whether the XR Party Game mode images can be inserted from
wp-admin. Verified end-to-end on live: the "XR Party — Game Modes" field
group is attached to page 577 with all seeded content; setting an image on
Mode 2 rendered it in the correct card with alt text; cleared after test.

Hardening added (overworld-xr-modes.php v1.0.1): an `acf/save_post` hook
now deletes `_elementor_element_cache` on any post whose ACF fields are
saved — guarantees client edits to ANY of our ACF-driven sections (XR
modes, outlet cards/gallery/intro, event pages) are never masked by
Elementor's element cache (the Jul-16 gotcha).

## 2026-07-16 - VR Free Roam: 2-Line Hero + Live Games Grid + Full-Fit Images

Three client requests on /vr-free-roam/:

1. Hero title locked to exactly two lines ("VR" / "FREE ROAM"). The Jul-8
   inline fix had regressed to 3 lines; the FREE/ROAM spans are now wrapped
   in a `.ow-vfr-hero__title-line2` block (white-space:nowrap) with child
   display forced inline-block at equal size — self-enforcing regardless of
   the base span rules.
2. Games grid is now LIVE: new mu-plugin `overworld-experience-grid.php`
   provides `[ow_experience_grid term="..."]` which queries the Experience
   CPT at request time (exp_display_order then A-Z, matching the archives).
   Page 646's static 23-card grid (client had added 6 newer games that
   never appeared, e.g. Mansion of Death) swapped to the shortcode —
   29 games now render, heading/count are dynamic, first 8 visible with
   in-page "View More" + "Browse All" to the archive. Any new game
   published to vr-roam appears automatically. NOTE: the old genre filter
   tabs were part of the static HTML and are not carried over (genre data
   does not exist in the CPT); grid matches the arcade/escape page style.
   Config also ships vr-arcade / vr-escape palettes so those pages can be
   flipped to the same live grid on request.
3. Card images: fixed 16:9 cover-crop fills the card border edge-to-edge —
   small client uploads scale up to fit perfectly, no letterboxing.

Also: child style.css v1.1.5 hover-fill exception for the new View More
button. Backup: ~/overworld-backups/vfr-646-before-dynamic-grid-*.json

Gotcha for future edits: Elementor's `_elementor_element_cache` post meta
served stale section HTML even after LiteSpeed/object/CSS purges — delete
that meta when a DB-patched page won't update.

Verification (live, browser): hero renders 2 lines; grid shows 29 with new
games; View More expands in-page; images cover-fit; no fatals.

## 2026-07-16 - XR Party Game Modes: Client-Editable Cards With Images

Client request: upgrade the XR Party Game page's "6 Modes" middle section so
the client can add game images per mode and edit the cards easily, like the
outlet "What We Offer" section.

New mu-plugin `overworld-xr-modes.php`:

- ACF group "XR Party — Game Modes" on page 577: 6 accordion mode rows
  (Name / Icon / Hook / Description / optional Image). Empty name hides a
  card; all empty falls back to the built-in defaults (the previous
  hardcoded content). Images crop to 16:9 full-bleed at the card top —
  mixed image/no-image cards stay aligned; per-mode accent colors kept.
- Shortcode `[ow_xr_modes]` renders the section: the original section CSS
  ported verbatim + image styles; heading count is dynamic ("N Modes. One
  Carnival.").

Live changes:

- ACF slots seeded with the existing 6 modes (Glass Run Challenge, Vault
  Heist, Boulder Dash, Fruit vs Zombies, Candy Carnival, Dragon Clash) so
  the client edits current content immediately.
- Page 577 section-3 widget swapped from hardcoded html to the shortcode.
  Backup: ~/overworld-backups/xr-577-before-dynamic-modes-*.json

Client workflow: WP Admin -> Pages -> XR Party Game -> "XR Party — Game
Modes" box -> expand a Mode row -> edit / add image -> Update.

Verification (live, browser): section renders identically from ACF (6 cards,
correct names/hooks/colors, dynamic count); temp image on Mode 01 rendered
full-bleed 16:9 alongside imageless cards without misalignment, then
cleared (client to add real per-mode screenshots); no fatals.

## 2026-07-15 - Funan Phone Number Corrected Site-Wide (8914 0061)

Client report: Funan's phone number was wrong in places. Correct number:
+65 8914 0061. Audit found TWO flavours of the bug:

- Display text "+65 8915 0061" in 4 theme templates (outlet, FAQ, event
  listing, event package pages) and on the Contact page content.
- Worse: the footer's VISIBLE number was already correct, but its tel:
  link dialled +6589150061 — tapping it called the wrong number. Same
  wrong tel: href on the Contact page. (WhatsApp links were fine.)

Fixes:

- Theme templates (page-pricing / page-faq / page-event-listing /
  single-event_package): phone + phone_raw corrected; deployed.
- scripts/upsert-footer-pages.mjs seed data corrected (both directions of
  the old placeholder mapping were wrong).
- Live DB: wp search-replace "8915 0061"->"8914 0061" (2 rows) and
  "89150061"->"89140061" (15 rows) across all tables — covers the footer
  Elementor template (566) + revisions and the Contact page (935) +
  revision. Row-level backup first:
  ~/overworld-backups/funan-phone-rows-before-fix-*.json
  (wp db export unavailable on this host — mysqldump missing; targeted
  row backup used instead.)

Verification (live): 10 page types swept (home, outlet, FAQ, TB/BP, contact,
event package, blog, game page, about) — 0 occurrences of 8915 0061 in any
form, correct 8914 0061 present on all; DB search returns no residual
matches in any table.

## 2026-07-14 - Blog: 9 Posts Per Page + Pagination Verified

Prep for a growing blog: the /blog/ index now shows 9 posts per page
(clean 3x3 card grid) with numbered page-by-page navigation.

- `functions.php`: `pre_get_posts` sets posts_per_page=9 for the main blog
  query only (archives/search/custom queries unaffected).
- The pagination UI (numbered pills, current state, dots, prev/next
  arrows) already existed in home.php but had never rendered (only 1 post
  at launch). Verified end-to-end by temporarily setting 1-per-page with
  2 throwaway posts: /blog/page/2..5/ resolved, numbers + active state +
  dots rendered correctly (browser-checked), then restored to 9 and
  trashed the test posts.

Noted while testing: the client has published 2 new SEO posts (Free Roam
groups, VR arcade group play) — blog is at 3 posts; pagination will appear
automatically from post #10.

## 2026-07-11 - FAQ Page: Mobile Outlet Filter In One Row

Client report: the Kallang/Orchard/Funan filter on /faq/ looked off on
mobile (tabs stacked into a vertical column). The <=760px rules now keep
the three tabs in ONE straight pill row: flex row, each tab flex:1 with
compact padding/font, dots shrunk. Verified at 390px viewport (iframe
test): all three tabs on the same row, equal widths, active state intact.
Single-file deploy of page-faq.php; cache purged.

## 2026-07-11 - Promo Countdown System (featured promo + live timers)

Client request: let the client set a promo end date and get a live countdown
on the promo page, plus choose ONE promo to feature in the homepage
countdown bar. The old homepage bar was hardcoded HTML whose target date
(2026-05-31) had expired weeks ago — it was showing zeros.

New mu-plugin `overworld-promo-countdown.php`:

- ACF side box "Homepage Feature" on Promos: `promo_featured` toggle.
  Enforced limit of one: switching it on programmatically switches it off
  on every other promo (acf/save_post hook).
- Countdown target = the existing `promo_valid_until` date the client
  already fills (ends 23:59:59 Asia/Singapore that day) — no new date field
  to learn.
- Shortcode `[ow_promo_countdown]` renders the homepage bar (same
  Variation-A design as before, now dynamic): featured promo's tagline as
  label, title linking to the promo page, live D/H/M/S timer, CTA from
  promo_cta_url/label. Future date -> timer; no date -> bar without timer;
  expired or nothing featured -> bar hidden entirely.
- Helper `ow_promo_timer_html()` shared with the promo detail page.

Other changes:

- Homepage (Elementor page 16): countdown widget swapped from static html
  widget to a shortcode widget (`[ow_promo_countdown]`). Backup:
  `~/overworld-backups/home-16-before-dynamic-countdown-*.json`.
- `single-promo.php`: "Offer ends in" countdown block under the title/
  tagline whenever the promo has a future valid-until date.
- Seeded `promo_featured=1` on the live promo (1369, Funan private room).
  Its valid_until is empty, so the bar currently shows without a timer —
  the client sets the real deadline in Promos -> Valid Until.

Client workflow: WP Admin -> Promos -> edit a promo -> set "Valid Until"
(countdown date) and flip "Feature on homepage countdown" -> Update.

Verification (live): homepage bar renders featured promo (browser-checked);
temp-date test produced correct target 2026-08-01T23:59:59+08:00 on the
homepage timer and an "Offer ends in" timer on the promo page (then the
temp date was cleared — real deadline is the client's to set); old
hardcoded 2026-05-31 gone; only-one-featured enforcement in place.

## 2026-07-11 - Admin Metaboxes Tidied (tabs + accordions)

Client feedback: the ACF metaboxes added this week rendered as a jagged
mixed-width field soup, hard to navigate. Reorganised all three groups
(field NAMES/keys unchanged — existing content untouched):

- "What We Offer — Intro & Activity Cards" (outlet pages): each card is now
  a collapsible accordion row ("Card 1".."Card 6"); inside: Title 50% /
  Icon 15% / Link 35% on one row, then Description and Image full-width.
- "Outlet Gallery": per-field instructions (which misaligned the grid)
  replaced by a single message field on top; 6 image slots in clean rows
  of three, labels "Image 1 — lead tile" etc.
- "Event Page — Intro, Gallery & Reviews" (TB/BP pages): split into three
  top tabs (Intro / Gallery / Reviews); gallery same 3-per-row grid;
  each review is an accordion row (Text full-width, Name 40 / Detail 40 /
  Stars 20).

Deploy: rsync of the 3 mu-plugins; object cache flushed. Verified live:
groups register with the new tab/accordion structure (acf_get_fields) and
front-end output unchanged (Funan cards + event intro still render).

## 2026-07-11 - Event Pages: Intro + Gallery + Reviews (client-editable)

Restructured the 6 Team Building / Birthday Party pages (template
page-event-listing.php) per client: Hero -> Intro -> Photo Gallery ->
Packages -> Reviews -> Enquiry CTA. Packages remain driven by the Events
CPT/ACF exactly as before; the new sections are page-editable.

New mu-plugin `overworld-event-page-content.php` — ACF group "Event Page —
Intro, Gallery & Reviews" on the Event Listing template:

- `event_page_intro` (textarea) — intro under the hero; empty = built-in
  default copy per event type (TB / BP).
- `event_gallery_1..6` (image slots) — same collage as the outlet pages
  (2x2 lead + tiles, 16:9-safe cover crop, 4-photo no-hole rule); hidden
  from visitors when empty, editors see placeholder tiles + hint.
- `event_review_1..4` (text / name / detail / stars 1-5) — "What Groups
  Say" cards with star ratings; hidden from visitors when empty, editors
  see a hint. Empty review text hides that slot.

Client workflow: WP Admin -> Pages -> the TB/BP outlet page -> "Event Page —
Intro, Gallery & Reviews" box. Packages continue to be managed under
Events as before.

Verification (live): 3 of 6 pages spot-checked, all 200 with section order
HERO -> INTRO -> GALLERY -> PACKAGES -> REVIEWS -> ENQUIRY; default intro
renders; empty gallery/reviews emit no markup for public visitors;
browser-checked /team-building/orchard-central/ (intro + 7 packages).

## 2026-07-11 - Outlet Pages: Per-Outlet FAQ Section (category tabs)

New FAQ section on the 3 outlet pages, between the Gallery and the
Terms/booking CTA. Same data as /faq/ (FAQ CPT):

- Filters to the outlet: entries whose `faq_outlet` matches the page slug,
  plus entries with empty/unknown outlet (= all outlets), mirroring
  page-faq.php.
- Grouped by `faq_category` with the same category ordering convention;
  category pill tabs on top show ONE category at a time (first active);
  questions are <details> accordions; answers rendered with
  wp_kses_post(wpautop()) like the FAQ page.
- Outlet accent styling; active pill hover protected from the button
  neutralizer via child style.css exception (v1.1.4).

Verification (live, browser): all 3 outlets render GALLERY -> FAQ -> CTA;
tabs per outlet are correct (Kallang: General/Booking/VR Arcade/Floor Is
Lava; Orchard: + Laser Maze/Tap Tap; Funan: General/Booking/Floor Is Lava);
clicked Laser Maze tab on Orchard -> only its questions shown, accordion
expands with answer text. Content edits in WP Admin -> FAQs reflect on both
/faq/ and the outlet pages automatically.

## 2026-07-11 - Outlet Pages: Acts Bottom Spacing + Gallery Before CTA

Two follow-ups to the outlet restructure (template page-pricing.php):

- "What We Offer" section bottom padding was near-zero (20px desktop /
  10px / 6px, from when the gallery followed it directly). Now 80px / 60px
  / 50px so the cards breathe before the events section.
- Gallery moved up: final order is now Hero -> What We Offer -> Events ->
  Pricing -> Gallery -> Terms + booking CTA (page ends on the CTA).

Deploy: single-file rsync; caches purged. Verified on all 3 outlets:
order HERO -> OFFER -> EVENTS -> PRICING -> GALLERY -> CTA and new padding
present. Noted while verifying: the client has already used the new card
fields — Funan cards 2 & 3 now carry client-added images (attachments 450,
1236), rendering aligned alongside card 1.

## 2026-07-11 - Outlet Pages: "What We Offer" Section + Editable Cards + Reorder

Client requests for all 3 outlet pages (template page-pricing.php):

1. Second section now opens with an intro ("what we offer") before the
   activity cards. Eyebrow renamed "What's Inside" -> "What We Offer";
   new intro paragraph under the section head.
2. Activity cards are now client-editable from WP admin, with an optional
   image per card.
3. Section order changed: Hero -> What We Offer -> Events (TB/BP) ->
   Pricing (+terms/CTA) -> Gallery (last).
   (Was: Hero -> Activities -> Gallery -> Pricing -> Events.)

Implementation:

- New mu-plugin `overworld-outlet-activities.php`: ACF group "What We Offer
  — Intro & Activity Cards" on the Pricing Page template. Fields:
  `outlet_intro` (textarea) + 6 card slots `outlet_act_1..6` (title,
  description, link, emoji icon, optional image; empty title hides the
  slot; all empty -> template falls back to the built-in activity library).
- Template: cards restructured (optional 16:9 cover-cropped image block on
  top, body below with icon/name/desc/button pinned bottom) so any upload
  renders undistorted on mobile/tablet/desktop, mixed image/no-image cards
  stay aligned. Per-outlet default intro texts added.
- Seeded the ACF slots on pages 504/505/506 with the previous hardcoded
  card content so the client edits live values immediately. Funan card 1
  given a demo image (attachment 1252) to show the optional-image layout.

Client workflow: WP Admin -> Pages -> outlet -> "What We Offer" box ->
edit intro/cards, optionally add a card image -> Update.

Verification (live): all 3 outlets 200; rendered section order
HERO -> OFFER -> EVENTS -> PRICING -> TERMS -> GALLERY on all 3; Funan
shows intro + mixed cards (1 image, 2 icon-only) aligned; 360px container
test: single column, image 358x201 = exact 16:9, no distortion.

## 2026-07-11 - Game Pages: Tighter Section Padding

Client request: section padding on the activity pages was too large — keep
it low. The 8 game pages' sections shipped with 120px desktop / 90px tablet
/ 70px mobile vertical padding (heroes up to 140px top / 180px bottom).

Fix (child `style.css` v1.1.3 — one scoped override instead of patching
40+ inline section styles across 8 Elementor pages):

- All non-hero `<section class="ow-...">` elements on pages 294 / 312 /
  326 / 338 / 364 / 420 / 577 / 646 flattened to 72px desktop / 56px
  tablet / 44px mobile vertical padding. Selector
  `body.page-id-X section[class*="ow-"]:not([class*="hero"])` at (0,2,1)
  outranks the sections' own (0,1,0) rules including their media queries.
- Heroes keep their tall top padding (sticky-header clearance) but the
  oversized bottom gap drops 180px -> 80px (56px mobile).
- Horizontal padding untouched.

Verification: computed styles on /vr-arcade/ confirm hero 140/80 and all
other sections 72/72; stylesheet with both rules confirmed deployed
(ver=1.1.3); all 8 pages 200. (Gotcha: first hero-fix deploy reused
ver=1.1.2 and was cache-masked — version bump required per CSS change.)

## 2026-07-10 - Games Grids Added To VR Arcade + VR Escape Pages

Client request: like VR Free Roam, the VR Arcade and VR Escape pages should
list their games on-page with "view more" in the same page, plus a link to
the full library page.

What changed (live DB, new section inserted at position 3 of each page —
hero, what-is-it, GAMES GRID, pricing, gallery):

- VR Arcade (326): "30+ Worlds On Tap" — all 29 vr-arcade games as cards
  (real thumbnails from the Experience CPT, ordered by exp_display_order
  then A-Z), first 8 visible, "View More Games ↓" reveals the rest in-page,
  "Browse All Games →" links to /experience-type/vr-arcade/. Orange palette.
- VR Escape (420): "Pick Your Escape" — all 23 rooms, same behaviour,
  "Browse All Rooms →" links to /experience-type/vr-escape/. Purple palette.
- Cards link to each game's /experience/[slug]/ page.
- Hero "Browse The Games/Rooms" ghost buttons retargeted from the archive
  URL to the in-page #games anchor (matches VR Free Roam's hero behaviour);
  the grid's Browse All button carries the archive link instead.
- Child style.css v1.1.1: hover-fill exceptions for the two new
  `__btn--more` <button>s (the !important button neutralizer would have
  stripped their accent fill on hover).

Backups:

```text
~/overworld-backups/page-326-before-games-grid-*.json
~/overworld-backups/page-420-before-games-grid-*.json
```

Note: grids are generated snapshots of the CPT (like the VR Free Roam page).
New games added to the library appear on /experience-type/ archives
automatically but need a regen of this section to appear on the page grid.

Verification (live, browser): both pages 200; arcade 29 cards (21 hidden),
escape 23 cards (15 hidden); View More reveals rows in-page (clicked, rows
appeared); real artwork renders; hero anchor + archive links correct.

## 2026-07-10 - "What Is It" Info Section Rolled Out To VR Arcade + VR Escape

Client request: the VR Free Roam page's second section (intro copy + spec
cards: session, games, players, age, what-to-wear) should exist on all game
pages — some had it, some didn't.

Audit of all 8 activity pages:

```text
HAS one:  vr-free-roam (What Is It), xr-party-game (What's The Game),
          floor-is-lava / laser-maze / tap-tap (How To Play),
          vr-machine-ride (specs inside Pricing + How It Works)
MISSING:  vr-arcade (326), vr-escape (420) — both jumped hero -> pricing
```

What changed (live DB, Elementor `_elementor_data`, new container inserted
between hero and pricing on each page):

- VR Arcade: "Pay For Time. / Play Everything." — orange palette, 3 intro
  paragraphs, first-timer callout, 5 spec cards (Session 30/60/120 min,
  30+ titles, 1-17 stations, Age 8+, Glasses OK). Facts sourced from the
  page hero, live pricing CPT and FAQ content.
- VR Escape: "Escape Rooms. / Without Limits." — purple palette, 3 intro
  paragraphs, new-to-VR callout, 5 spec cards (60-min mission, 23 rooms,
  2-8 players, Age 8+ / horror 13+, All Levels).
- Markup/CSS is a parameterized port of the VR Free Roam section
  (`.ow-vra-what` / `.ow-vre-what` class prefixes, unique Elementor IDs,
  section anchor `#what-is-it`).

Backups:

```text
~/overworld-backups/page-326-before-what-section-*.json
~/overworld-backups/page-420-before-what-section-*.json
```

Deploy: JSON built locally (python, anchor asserts + validation), applied
via guarded wp eval with wp_slash; WP object, Elementor CSS and LiteSpeed
caches flushed.

Verification (live, browser): both pages 200, section renders between hero
and pricing in the correct palette, 5 spec cards each, no fatals.

Follow-up (same day, per client): VR Machine Ride (338) added too —
"Strap In. / Take Off." in the page's electric-blue palette, inserted
between hero and pricing. Spec cards: 8-minute ride, 360° motion seat,
1-2 riders, $15 flat, walk-in only. Backup:
`~/overworld-backups/page-338-before-what-section-*.json`. Browser-verified.
All 8 activity pages now carry an intro/specs section.

## 2026-07-08 - Footer: Social Icons Centered + AdCendes Credit Hover

Two footer polish items (live DB patch to Elementor footer template 566):

- Social icons (Instagram/Facebook) sat off-center inside their circular
  buttons: the "Stay Connected" column's generic link rule adds an arrow
  `::before` pseudo-element (opacity 0 but still occupying flex space) and a
  `padding-left:6px` hover shift. Both now disabled for `.ow-foot-a__social`
  anchors in all states — icons are dead-center, hover included.
- "Site designed by AdCendes" credit link: explicit orange styling
  (`--ow-lava` base, `--ow-lava-glow` on hover, !important) — the earlier
  global link neutralizer had reverted it to the muted inherit color and
  white hover.

Backup: `~/overworld-backups/footer-566-before-social-center-*.json`.
Caches flushed; browser-verified (zoomed icon centering + hover state).

## 2026-07-08 - FAQ Outlet Tabs: pink hover killed with !important

Follow-up to the reset-neutralizer work: the FAQ outlet tabs still hovered
pink. Cause: they are `<button type="button">`, and the parent reset also
ships `[type=button]:hover{background:#c36}` at (0,2,0) specificity — higher
than the child theme's `:where()`-based neutralizer (0,1,1).

Fix (child `style.css`, v1.1.0, per client request "set important"):

- `button[class*="ow-"]:hover/:focus { background-color:transparent
  !important; color:#fff !important; }` — !important guarantees the reset's
  pink can never surface on any custom-section button.
- Exceptions (higher specificity + !important) keep intended fills:
  `.ow-faq__tab.is-active` keeps its outlet-color fill on hover,
  `.ow-vfr-games__btn--more` keeps its green fill, and
  `.ow-vfr-games__filter` keeps its green-glow hover text.

Verified live (browser + CSSOM): FAQ tab hover = white text/transparent bg,
active tab keeps orange fill; child rule confirmed loaded with !important
priority on background-color.

## 2026-07-08 - FAQ Outlet Filter Fixed (field mismatch)

Client report: FAQs assigned to Funan / Orchard "in the category" did not
appear under those outlet tabs on /faq/ — everything showed under Kallang.

Root cause: the ACF group "FAQ Details" never had a `faq_outlet` field (which
the template reads); instead its `faq_category` select's CHOICES were the
three outlet slugs. So the admin "Category" dropdown was actually an outlet
picker writing into the wrong meta key, `faq_outlet` was always empty, and
the template's empty-outlet fallback dumped all 31 FAQs under Kallang.

Fixes:

- ACF (live DB, via acf_update_field): repurposed the outlet-choices select
  as `faq_outlet` (label "Outlet", allow_null, instructions: leave empty =
  ALL outlets) and added a proper `faq_category` select
  (key `field_ow_faq_category`) with the real category choices
  (General/Booking/VR Arcade/.../Tap Tap).
- Data migration (31 posts): honored the client's assignments — 387 ->
  Funan/General, 400 -> Orchard/Booking, 410 -> Orchard/Laser Maze — and
  mapped activity categories to outlets (VR Arcade -> Kallang; Laser Maze +
  Tap Tap -> Orchard). General/Booking/Floor Is Lava left outlet-empty =
  shown at all outlets. ACF key references (`_faq_outlet`/`_faq_category`)
  set so the admin UI binds correctly.
- `page-faq.php`: empty/unknown `faq_outlet` now means "show under EVERY
  outlet tab" instead of silently defaulting to Kallang.

Verification (live, browser):

```text
/faq/ 200
Kallang tab: 21 questions (General/Booking/VR Arcade/Floor Is Lava)
Orchard tab: 25 questions (+ Laser Maze, Tap Tap, and Orchard-specific
             "How many games can I play in one session?")
Funan tab:   17 questions (leads with Funan-assigned "Where are your
             outlets located?")
Tab switching, accent colors, and contact cards all correct.
```

## 2026-07-08 - VR Free Roam Hero Title On Two Lines

Per client: the hero title on `/vr-free-roam/` stacked "VR / FREE / ROAM" on
three lines; "Free Roam" should sit on one line. Live DB patch to page 646
hero widget CSS: `.ow-vfr-hero__title--free` and `--roam` changed from
`display:block` to `inline-block`, and ROAM's smaller font-size override
removed so both words render at the full title size. Title now reads
"VR" / "FREE ROAM". Gradients per word unchanged (white VR, green FREE,
faded-white ROAM).

Backup: `~/overworld-backups/vfr-646-before-inline-title-*.json`.
Caches flushed; browser-verified on desktop (fits comfortably; mobile clamp
14vw keeps the line within a 390px viewport).

## 2026-07-08 - Hover Color Fixes (pink/navy leak) + Sticky Header

Two client-reported hover bugs and one UX request, all rooted in theme-level
CSS rather than the page templates.

Root cause of the hover bugs — `hello-elementor/assets/css/reset.css`:

```css
a { color:#c36 }                        /* pink links */
a:active, a:hover { color:#336 }        /* navy hover  */
button:hover { background-color:#c36; color:#fff }  /* pink buttons */
```

These leak into any custom section that doesn't re-declare the exact
property: the VR Free Roam page's "View More Games" + genre filter pills
(`<button>`s) hovered pink, and the VR Arcade hero "Browse the Games" link
hovered navy-on-dark (invisible). Pill buttons also showed a stray underline.

Fixes (child theme):

- `style.css` — "Hello Elementor reset neutralizer" block:
  - `a{color:inherit}`, `a:hover/:active{color:#fff}` (dark site default;
    any component's own hover color still wins by cascade order).
  - `a[class*="--primary"]:where(:hover,:active){color:#0a0a14}` so solid
    orange/green primaries keep dark labels (`:where` keeps specificity at
    the same (0,1,1) so page-inline rules stay in control).
  - Pill/CTA links: `text-decoration:none !important`.
  - `button[class*="ow-"]:where(:hover,:focus){background:transparent;
    color:#fff}` + explicit green fill for `.ow-vfr-games__btn--more`.
- `functions.php` — the child stylesheet previously loaded BEFORE the
  parent's reset.css, so equal-specificity overrides lost the cascade.
  Added `hello-elementor` (reset.css) and `hello-elementor-theme-style`
  (theme.css) as dependencies; child CSS now loads last.

Sticky header (client request "header always fixed when scroll"):

- `style.css`: `#masthead, .ehf-header #masthead { position:sticky; top:0;
  z-index:9999 }` — the `.ehf-header` variant is needed because the
  Header Footer Elementor plugin ships
  `.ehf-header #masthead{position:relative;z-index:99}` at (1,1,0)
  specificity. Admin-bar offsets included.
- Child theme version bumped 1.0.8 -> 1.0.9 for cache busting.

Verification (browser, live, hard-reloaded):

```text
/vr-arcade/    hero "Browse the Games" hover -> white text, no underline
/vr-free-roam/ "View More Games" hover -> green fill + dark label, no pink
/vr-free-roam/ scrolled mid-page -> header stays pinned at top (sticky)
Book Now (header), card Learn More, price CTAs unchanged (own hover rules)
```

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

Follow-up (same day, per client): populated all 3 galleries with 5 existing
media-library photos each so the 4-col grid aligns exactly (2x2 lead + four
1x1 tiles):

```text
Kallang (504): 418 VR room photo (lead), 419 escape room, 448 arcade titles
               collage, 707 arena art, 803 VR Machine Fantasy Starship
Orchard (505): 445 shop front (lead), 450 Floor Is Lava, 451 Laser Maze,
               449 Tap Tap, 310 Laser Maze photo
Funan   (506): 1263 shop front (lead), 447 3D interior render, 1252 VR Free
               Roam, 1236 XR Party Game, 1217 Party Playland
```

Also added a template CSS rule: with exactly 4 photos the last tile spans 2
columns so the grid never shows a hole. Client can swap any photo via
Edit Page -> Outlet Gallery. Browser-verified Funan (5 aligned tiles);
Kallang/Orchard render 5 photos each.

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

<?php
/**
 * Template Name: Pricing Page
 *
 * INSTALL: Upload to your child theme:
 *   /wp-content/themes/hello-elementor-child/page-pricing.php
 *
 * USAGE:
 *   - Assign this template to ALL 3 outlet pages:
 *     /outlet/kallang-wave-mall, /outlet/orchard-central, /outlet/funan
 *   - The template auto-detects the outlet from the page slug
 *
 * PAGE STRUCTURE (top to bottom):
 *   1. Hero            — outlet name, tagline, address/phone
 *   2. What We Offer   — intro text + activity cards (client-editable via ACF
 *                        slots outlet_intro / outlet_act_1..6 with optional
 *                        image; falls back to the built-in library below)
 *   3. Events          — Team Building & Birthday Party cards linking to
 *                        /team-building/[slug] and /birthday-party/[slug]
 *   4. Pricing         — full-width pricing tables from the Pricing CPT
 *                        (pricing_outlet = slug)
 *   5. Gallery         — ACF outlet_gallery_1..6 photo collage
 *   6. FAQ             — this outlet's FAQs from the FAQ CPT, category tabs,
 *                        one category at a time (empty faq_outlet = shown at
 *                        every outlet, same rule as page-faq.php)
 *   7. Terms + CTA     — "Before You Book" list and booking buttons (last)
 *
 * COLOR UPDATE: Kallang = Blue, Orchard = Orange, Funan = Purple.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

// ===== Detect outlet from page slug =====
global $post;
$slug = $post->post_name;

$outlet_config = array(
    'kallang-wave-mall' => array(
        'slug'        => 'kallang-wave-mall',
        'brand'       => 'Overworld VR',
        'name'        => 'Kallang Wave Mall',
        'short_name'  => 'Kallang',
        'address'     => '1 Stadium Place #01-63/64, Singapore 397628',
        'phone'       => '+65 6513 0561',
        'whatsapp'    => 'https://wa.me/+6596101682',
        'email'       => 'support@overworldvr.com',
        'maps'        => 'https://maps.app.goo.gl/WeJiJJuwmm2CirPj6',
        'bookeo'      => 'https://bookeo.com/overworldKallangwavemall',
        'accent'      => '#2f6bff',
        'accent_glow' => '#6f9bff',
        'accent_dim'  => 'rgba(47,107,255,',
        'bg_gradient' => 'radial-gradient(ellipse at 50% 110%,#050d24 0%,#040814 50%,#03060f 100%)',
        'tagline'     => 'Where headsets meet hot floors.',
        'description' => 'Singapore\'s premier VR arcade, plus the city\'s most-loved interactive lava floor.',
        'activities'  => array( 'vr-arcade', 'vr-escape', 'floor-is-lava', 'vr-machine-ride' ),
    ),
    'orchard-central' => array(
        'slug'        => 'orchard-central',
        'brand'       => 'Overworld Lava',
        'name'        => 'Orchard Central',
        'short_name'  => 'Orchard',
        'address'     => '181 Orchard Road, #05-30/K1/K3, Singapore 238896',
        'phone'       => '+65 8801 4303',
        'whatsapp'    => 'https://wa.me/message/WJ7MGRFFVGHAF1',
        'email'       => 'ocsupport@overworld.com.sg',
        'maps'        => 'https://maps.app.goo.gl/orchardcentral',
        'bookeo'      => 'https://bookeo.com/overworldorchardcentral',
        'accent'      => '#ff5722',
        'accent_glow' => '#ff8a3d',
        'accent_dim'  => 'rgba(255,87,34,',
        'bg_gradient' => 'radial-gradient(ellipse at 50% 110%,#1a0a05 0%,#0d0608 50%,#0a0606 100%)',
        'tagline'     => 'Pure physical chaos in the heart of Orchard.',
        'description' => 'Lava floors, laser mazes, and light walls — three immersive games that test your speed, reflex, and grit.',
        'activities'  => array( 'floor-is-lava', 'laser-maze', 'tap-tap' ),
    ),
    'funan' => array(
        'slug'        => 'funan',
        'brand'       => 'Overworld Funan',
        'name'        => 'Funan',
        'short_name'  => 'Funan',
        'address'     => '107 North Bridge Road, #04-14 & K1, Funan, Singapore 179105',
        'phone'       => '+65 8914 0061',
        'whatsapp'    => 'https://wa.me/6589140061',
        'email'       => 'funansupport@overworld.com.sg',
        'maps'        => 'https://maps.app.goo.gl/3yd7X5HqY8s7qDr38',
        'bookeo'      => 'https://bookeo.com/overworldfunan',
        'accent'      => '#a855f7',
        'accent_glow' => '#c89aff',
        'accent_dim'  => 'rgba(168,85,247,',
        'bg_gradient' => 'radial-gradient(ellipse at 50% 110%,#1f0a3a 0%,#10081e 50%,#0a081a 100%)',
        'tagline'     => 'The party arena in the heart of the CBD.',
        'description' => 'Home of XR Party Game, VR Free Roam, and Floor Is Lava — Funan brings carnival energy to every visit.',
        'activities'  => array( 'vr-free-roam', 'floor-is-lava', 'xr-party-game' ),
    ),
);

// Default fallback if slug doesn't match
$outlet = isset( $outlet_config[ $slug ] ) ? $outlet_config[ $slug ] : $outlet_config['kallang-wave-mall'];

// ===== Activity library (shared across outlets) =====
// Each outlet's 'activities' list picks from here, in display order.
$activity_library = array(
    'vr-arcade' => array(
        'name' => 'VR Arcade',
        'icon' => '👾',
        'desc' => 'Pick-up-and-play VR stations loaded with dozens of titles — shooters, rhythm, horror, sports and more.',
        'url'  => '/vr-arcade/',
    ),
    'vr-escape' => array(
        'name' => 'VR Escape',
        'icon' => '🔑',
        'desc' => 'Team up and solve your way out of fully immersive VR escape rooms before the clock runs out.',
        'url'  => '/vr-escape/',
    ),
    'floor-is-lava' => array(
        'name' => 'Floor Is Lava',
        'icon' => '🌋',
        'desc' => 'The interactive floor that turns dodging, jumping and scrambling into a full-body game.',
        'url'  => '/floor-is-lava/',
    ),
    'vr-machine-ride' => array(
        'name' => 'VR Machine Ride',
        'icon' => '🚀',
        'desc' => 'Strap in for a motion-simulator thrill ride — coasters, flights and free-falls in full VR.',
        'url'  => '/vr-machine-ride/',
    ),
    'laser-maze' => array(
        'name' => 'Laser Maze',
        'icon' => '🔦',
        'desc' => 'Duck, weave and slide through a web of lasers — clear the maze with the fewest touches to win.',
        'url'  => '/laser-maze/',
    ),
    'tap-tap' => array(
        'name' => 'Tap Tap',
        'icon' => '⚡',
        'desc' => 'A wall of light pads and a ticking clock — smash as many as your reflexes allow.',
        'url'  => '/tap-tap/',
    ),
    'vr-free-roam' => array(
        'name' => 'VR Free Roam',
        'icon' => '🥽',
        'desc' => 'Untethered, full-space VR — walk, dodge and battle through shared arenas with your whole crew.',
        'url'  => '/vr-free-roam/',
    ),
    'xr-party-game' => array(
        'name' => 'XR Party Game',
        'icon' => '🎉',
        'desc' => 'Mixed-reality party games on the big screen — everyone joins in, no headset experience needed.',
        'url'  => '/xr-party-game/',
    ),
);

// Client-editable cards (ACF slots outlet_act_1..6, see mu-plugin
// overworld-outlet-activities.php). Fallback: built-in activity library.
$outlet_activities = array();
for ( $ai = 1; $ai <= 6; $ai++ ) {
    $act_title = trim( (string) get_post_meta( get_the_ID(), 'outlet_act_' . $ai . '_title', true ) );
    if ( '' === $act_title ) continue;
    $act_img_id  = get_post_meta( get_the_ID(), 'outlet_act_' . $ai . '_image', true );
    $act_img_src = ( $act_img_id && is_numeric( $act_img_id ) ) ? wp_get_attachment_image_url( (int) $act_img_id, 'large' ) : '';
    $outlet_activities[] = array(
        'name' => $act_title,
        'desc' => (string) get_post_meta( get_the_ID(), 'outlet_act_' . $ai . '_desc', true ),
        'url'  => (string) get_post_meta( get_the_ID(), 'outlet_act_' . $ai . '_link', true ),
        'icon' => (string) get_post_meta( get_the_ID(), 'outlet_act_' . $ai . '_icon', true ),
        'img'  => $act_img_src ?: '',
    );
}
if ( empty( $outlet_activities ) ) {
    foreach ( (array) ( $outlet['activities'] ?? array() ) as $activity_key ) {
        if ( isset( $activity_library[ $activity_key ] ) ) {
            $outlet_activities[] = $activity_library[ $activity_key ] + array( 'img' => '' );
        }
    }
}

// Section intro (ACF outlet_intro, fallback to a per-outlet default)
$default_intros = array(
    'kallang-wave-mall' => 'Our flagship VR playground: strap on a headset for 30+ arcade titles, lock your team into a VR escape room, brave the lava floor, or take a spin on Singapore\'s first VR motion ride — all under one roof at Kallang Wave Mall.',
    'orchard-central'   => 'No headsets here — just pure physical play. Dodge a floor of digital lava, weave through a web of lasers, and race the clock on our light wall. Three fast, sweaty, ridiculously fun games in the heart of Orchard.',
    'funan'             => 'Free-roam VR arenas, a lava floor, and big-screen XR party games — Funan is built for groups who want to move. Pick one, or bounce between them all in a single visit.',
);
$acts_intro = trim( (string) get_post_meta( get_the_ID(), 'outlet_intro', true ) );
if ( '' === $acts_intro ) {
    $acts_intro = $default_intros[ $slug ] ?? $outlet['description'];
}

// ===== Event sections (Team Building / Birthday Party) for this outlet =====
$event_sections = array(
    array(
        'type'    => 'team-building',
        'eyebrow' => 'Team Building Packages',
        'title'   => 'Team Building',
        'tagline' => 'Stronger squads. Sharper teams.',
        'desc'    => 'Bring your team out of the office and into the action. From quick icebreakers to full-day adventures — pick a package that fits.',
        'icon'    => '🤝',
    ),
    array(
        'type'    => 'birthday-party',
        'eyebrow' => 'Birthday Party Packages',
        'title'   => 'Birthday Party',
        'tagline' => 'A birthday they\'ll actually remember.',
        'desc'    => 'Forget cake-and-balloons. Throw a birthday party that\'s loud, active, and full of bragging rights.',
        'icon'    => '🎂',
    ),
);

foreach ( $event_sections as $i => $event_section ) {
    $event_sections[ $i ]['url']   = home_url( '/' . $event_section['type'] . '/' . $slug . '/' );
    $event_sections[ $i ]['count'] = count( get_posts( array(
        'post_type'      => 'event_package',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'meta_query'     => array(
            'relation' => 'AND',
            array( 'key' => 'event_type',   'value' => $event_section['type'] ),
            array( 'key' => 'event_outlet', 'value' => $slug ),
            array(
                'relation' => 'OR',
                array( 'key' => 'event_active', 'value' => '1' ),
                array( 'key' => 'event_active', 'compare' => 'NOT EXISTS' ),
            ),
        ),
    ) ) );
}

// ===== Outlet gallery (ACF image slots outlet_gallery_1..6, see mu-plugin overworld-outlet-gallery.php) =====
$gallery_images = array();
for ( $gi = 1; $gi <= 6; $gi++ ) {
    $att_id = get_post_meta( get_the_ID(), 'outlet_gallery_' . $gi, true );
    if ( ! $att_id || ! is_numeric( $att_id ) ) continue;
    $src = wp_get_attachment_image_url( (int) $att_id, 'large' );
    if ( ! $src ) continue;
    $alt = get_post_meta( (int) $att_id, '_wp_attachment_image_alt', true );
    $gallery_images[] = array(
        'src' => $src,
        'alt' => $alt ? $alt : $outlet['name'] . ' — Overworld outlet photo',
    );
}
$gallery_editor_hint = empty( $gallery_images ) && is_user_logged_in() && current_user_can( 'edit_post', get_the_ID() );

// ===== FAQs for this outlet (FAQ CPT — same data as /faq/) =====
// An FAQ with empty/unknown faq_outlet applies to every outlet (mirrors
// page-faq.php). Grouped by category; one category shown at a time.
$faq_by_cat = array();
$faq_posts  = get_posts( array(
    'post_type'      => 'faq',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'meta_key'       => 'faq_display_order',
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
) );
foreach ( $faq_posts as $faq_post ) {
    $faq_outlet = get_post_meta( $faq_post->ID, 'faq_outlet', true );
    if ( $faq_outlet && isset( $outlet_config[ $faq_outlet ] ) && $faq_outlet !== $slug ) continue;
    $faq_cat = get_post_meta( $faq_post->ID, 'faq_category', true );
    if ( ! $faq_cat ) $faq_cat = 'General';
    $faq_by_cat[ $faq_cat ][] = $faq_post;
}
// Category display order (same convention as page-faq.php)
$faq_cat_order = array( 'General', 'Booking', 'VR Arcade', 'VR Escape', 'VR Machine Ride', 'VR Free Roam', 'XR Party Game', 'Floor Is Lava', 'Laser Maze', 'Tap Tap' );
$faq_cats_sorted = array();
foreach ( $faq_cat_order as $faq_cat_name ) {
    if ( isset( $faq_by_cat[ $faq_cat_name ] ) ) $faq_cats_sorted[ $faq_cat_name ] = $faq_by_cat[ $faq_cat_name ];
}
foreach ( $faq_by_cat as $faq_cat_name => $faq_items ) {
    if ( ! isset( $faq_cats_sorted[ $faq_cat_name ] ) ) $faq_cats_sorted[ $faq_cat_name ] = $faq_items;
}

// ===== Query pricing items for this outlet =====
$pricing_items = array();
$pricing_query = get_posts( array(
    'post_type'      => 'pricing_item',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'meta_query'     => array(
        array(
            'key'   => 'pricing_outlet',
            'value' => $slug,
        ),
    ),
    'meta_key'       => 'pricing_display_order',
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
));

// Group by activity
foreach ( $pricing_query as $item ) {
    $activity       = get_post_meta( $item->ID, 'pricing_activity', true );
    $activity_order = (int) get_post_meta( $item->ID, 'pricing_activity_order', true );
    if ( ! $activity ) $activity = 'Other';

    if ( ! isset( $pricing_items[ $activity ] ) ) {
        $pricing_items[ $activity ] = array(
            'order' => $activity_order ?: 999,
            'rows'  => array(),
        );
    }
    $pricing_items[ $activity ]['rows'][] = $item;
}

// Sort activities by their activity_order
uasort( $pricing_items, function( $a, $b ) {
    return $a['order'] - $b['order'];
});
?>

<!-- ============== PRICING PAGE STYLES ============== -->
<style>
  .ow-pri{
    --accent: <?php echo esc_attr( $outlet['accent'] ); ?>;
    --accent-glow: <?php echo esc_attr( $outlet['accent_glow'] ); ?>;
    --bg: #0a0a14;
    --bg-2: #13131f;
    --fg: #fff;
    --dim: rgba(220,225,240,.65);
    --line: rgba(255,255,255,.08);
    background: var(--bg);
    color: var(--fg);
    font-family: 'Space Grotesk','Inter',system-ui,sans-serif;
  }
  .ow-pri *{box-sizing:border-box;}

  /* ===== HERO ===== */
  .ow-pri__hero{
    position:relative;
    background: <?php echo $outlet['bg_gradient']; ?>;
    padding:120px 40px 80px;
    overflow:hidden;
  }
  .ow-pri__hero::before{
    content:"";position:absolute;left:0;right:0;bottom:0;height:80%;
    background:radial-gradient(ellipse at center,
      <?php echo $outlet['accent_dim']; ?>.2) 0%,
      transparent 70%);
    filter:blur(60px);pointer-events:none;
  }
  .ow-pri__hero-grid{
    position:absolute;inset:0;pointer-events:none;
    background-image:
      linear-gradient(<?php echo $outlet['accent_dim']; ?>.05) 1px,transparent 1px),
      linear-gradient(90deg,<?php echo $outlet['accent_dim']; ?>.05) 1px,transparent 1px);
    background-size:60px 60px;
    mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
    -webkit-mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
  }
  .ow-pri__hero-inner{
    max-width:1200px;margin:0 auto;
    position:relative;z-index:2;text-align:center;
  }
  .ow-pri__hero-eyebrow{
    display:inline-flex;align-items:center;gap:12px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.24em;text-transform:uppercase;
    color:var(--accent-glow);
    padding:9px 18px;
    border:1px solid <?php echo $outlet['accent_dim']; ?>.4);
    border-radius:999px;
    background:<?php echo $outlet['accent_dim']; ?>.08);
    margin-bottom:28px;
    backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);
  }
  .ow-pri__hero-eyebrow::before{
    content:"";width:8px;height:8px;border-radius:50%;
    background:var(--accent);
    box-shadow:0 0 12px var(--accent-glow);
  }
  .ow-pri__hero-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(48px,7vw,108px);
    line-height:1;letter-spacing:-.025em;
    font-weight:400;text-transform:uppercase;
    margin:0 0 18px;
    background:linear-gradient(180deg,#fff 0%,#fff 45%,var(--accent-glow) 100%);
    -webkit-background-clip:text;background-clip:text;
    -webkit-text-fill-color:transparent;
    text-shadow:0 0 60px <?php echo $outlet['accent_dim']; ?>.3);
  }
  .ow-pri__hero-tag{
    font-size:clamp(16px,1.7vw,19px);
    color:var(--fg);font-weight:400;line-height:1.4;
    margin:0 0 14px;
  }
  .ow-pri__hero-desc{
    font-size:15px;color:var(--dim);line-height:1.6;
    margin:0 auto 36px;max-width:580px;
  }
  .ow-pri__hero-meta{
    display:inline-flex;align-items:center;gap:14px;flex-wrap:wrap;justify-content:center;
    padding:14px 24px;
    background:rgba(0,0,0,.35);
    border:1px solid var(--line);
    border-radius:999px;
    backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
    margin-bottom:16px;
  }
  .ow-pri__hero-meta-item{
    display:flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--dim);white-space:nowrap;
  }
  .ow-pri__hero-meta-item a{color:#fff;text-decoration:none;transition:color .2s ease;}
  .ow-pri__hero-meta-item a:hover{color:var(--accent-glow);}
  .ow-pri__hero-meta-icon{color:var(--accent);}

  /* ===== SHARED SECTION HEADER ===== */
  .ow-pri__section-head{
    display:flex;align-items:flex-end;justify-content:space-between;
    margin-bottom:40px;padding-bottom:22px;
    border-bottom:1px solid var(--line);
    gap:24px;flex-wrap:wrap;
  }
  .ow-pri__section-eyebrow{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--accent-glow);margin-bottom:12px;
    display:flex;align-items:center;gap:10px;
  }
  .ow-pri__section-eyebrow::before{
    content:"";width:24px;height:1px;background:var(--accent);
  }
  .ow-pri__section-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(28px,3.5vw,42px);
    line-height:1;letter-spacing:-.02em;font-weight:400;
    text-transform:uppercase;margin:0;color:#fff;
  }
  .ow-pri__section-count{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.18em;text-transform:uppercase;
    color:var(--dim);white-space:nowrap;
  }
  .ow-pri__section-count strong{color:var(--accent-glow);font-weight:700;}

  /* ===== ACTIVITIES & GAMES ===== */
  .ow-pri__acts{
    padding:80px 40px 80px;
    background:var(--bg);
  }
  .ow-pri__acts-inner{
    max-width:1200px;margin:0 auto;
  }
  .ow-pri__acts-intro{
    max-width:720px;
    font-size:15.5px;line-height:1.7;color:var(--dim);
    margin:-14px 0 36px;
  }
  .ow-pri__acts-grid{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:22px;
  }
  .ow-pri__act-card{
    display:flex;flex-direction:column;
    background:var(--bg-2);
    border:1px solid var(--line);
    border-radius:20px;
    position:relative;overflow:hidden;
    transition:transform .35s ease, border-color .25s ease, box-shadow .35s ease;
  }
  /* Optional card image: fixed aspect ratio + cover-crop, so any upload
     renders cleanly on mobile / tablet / desktop without distortion */
  .ow-pri__act-img{
    position:relative;aspect-ratio:16/9;
    background:rgba(255,255,255,.02);overflow:hidden;
    border-bottom:1px solid var(--line);
  }
  .ow-pri__act-img img{
    width:100% !important;height:100% !important;
    object-fit:cover !important;object-position:center !important;
    display:block !important;
    position:absolute !important;top:0 !important;left:0 !important;
    max-width:none !important;max-height:none !important;
    transition:transform .4s ease;
  }
  .ow-pri__act-card:hover .ow-pri__act-img img{transform:scale(1.05);}
  .ow-pri__act-body{
    display:flex;flex-direction:column;flex:1;
    padding:28px 26px 26px;
  }
  .ow-pri__act-card::before{
    content:"";position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(to right,transparent,var(--accent),transparent);
    opacity:.5;z-index:1;
  }
  .ow-pri__act-card:hover{
    transform:translateY(-6px);
    border-color:var(--accent);
    box-shadow:0 20px 60px -20px var(--accent);
  }
  .ow-pri__act-icon{
    width:52px;height:52px;border-radius:14px;
    background:<?php echo $outlet['accent_dim']; ?>.12);
    border:1px solid <?php echo $outlet['accent_dim']; ?>.35);
    display:flex;align-items:center;justify-content:center;
    font-size:24px;line-height:1;
    margin-bottom:18px;
  }
  .ow-pri__act-name{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:24px;line-height:1.1;font-weight:400;
    text-transform:uppercase;letter-spacing:-.005em;
    margin:0 0 10px;color:#fff;
  }
  .ow-pri__act-desc{
    font-size:13.5px;line-height:1.55;color:var(--dim);
    margin:0 0 22px;flex:1;
  }
  .ow-pri__act-btn{
    display:inline-flex;align-items:center;justify-content:center;gap:8px;
    padding:12px 18px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    text-decoration:none;font-weight:700;
    background:rgba(255,255,255,.04);color:#fff;
    border:1px solid var(--line);
    transition:transform .25s ease, gap .25s ease, background .25s ease, border-color .25s ease;
  }
  .ow-pri__act-btn:hover{
    background:rgba(255,255,255,.08);
    border-color:var(--accent);
    transform:translateY(-2px);gap:12px;
  }

  /* ===== GALLERY ===== */
  .ow-pri__gallery{
    padding:60px 40px 20px;
    background:var(--bg);
  }
  .ow-pri__gallery-inner{
    max-width:1200px;margin:0 auto;
  }
  .ow-pri__gallery-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    grid-auto-rows:190px;
    grid-auto-flow:dense;
    gap:14px;
  }
  .ow-pri__gallery-item{
    position:relative;overflow:hidden;
    border-radius:18px;
    border:1px solid var(--line);
    background:var(--bg-2);
  }
  .ow-pri__gallery-item--lead{
    grid-column:span 2;grid-row:span 2;
  }
  /* with exactly 4 photos, widen the last tile so the grid has no hole */
  .ow-pri__gallery-item:nth-child(4):last-child{grid-column:span 2;}
  .ow-pri__gallery-item img{
    width:100% !important;height:100% !important;
    object-fit:cover !important;object-position:center !important;
    display:block !important;
    position:absolute !important;top:0 !important;left:0 !important;
    max-width:none !important;max-height:none !important;
    transition:transform .5s ease;
  }
  .ow-pri__gallery-item:hover img{transform:scale(1.05);}
  .ow-pri__gallery-item::after{
    content:"";position:absolute;inset:0;pointer-events:none;
    box-shadow:inset 0 -40px 60px -30px rgba(0,0,0,.55);
  }
  .ow-pri__gallery-empty{
    display:flex;align-items:center;justify-content:center;
    flex-direction:column;gap:8px;
    color:var(--dim);
    background:radial-gradient(ellipse at center,<?php echo $outlet['accent_dim']; ?>.08) 0%,var(--bg-2) 70%);
    border:1px dashed <?php echo $outlet['accent_dim']; ?>.35);
  }
  .ow-pri__gallery-empty-icon{font-size:26px;opacity:.5;}
  .ow-pri__gallery-empty-text{
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.14em;text-transform:uppercase;
    text-align:center;padding:0 14px;
  }
  .ow-pri__gallery-hint{
    margin:18px 0 0;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.1em;
    color:var(--dim);text-align:center;
  }
  .ow-pri__gallery-hint strong{color:var(--accent-glow);font-weight:700;}

  /* ===== MAIN: Pricing tables ===== */
  .ow-pri__main{
    padding:80px 40px 100px;
    background:var(--bg);
  }
  .ow-pri__main-inner{
    max-width:1000px;margin:0 auto;
  }

  /* Pricing tables column */
  .ow-pri__tables{
    display:flex;flex-direction:column;gap:28px;
  }
  .ow-pri__activity{
    background:var(--bg-2);
    border:1px solid var(--line);
    border-radius:20px;
    overflow:hidden;
    position:relative;
  }
  .ow-pri__activity::before{
    content:"";position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(to right,transparent,var(--accent),transparent);
    opacity:.5;
  }
  .ow-pri__activity-head{
    padding:24px 28px 16px;
    display:flex;align-items:center;gap:14px;
    border-bottom:1px solid var(--line);
  }
  .ow-pri__activity-icon{
    width:32px;height:32px;border-radius:8px;
    background:<?php echo $outlet['accent_dim']; ?>.15);
    border:1px solid <?php echo $outlet['accent_dim']; ?>.4);
    display:flex;align-items:center;justify-content:center;
    color:var(--accent-glow);
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:16px;line-height:1;flex-shrink:0;
  }
  .ow-pri__activity-name{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:24px;line-height:1;font-weight:400;
    text-transform:uppercase;letter-spacing:-.005em;
    margin:0;color:#fff;
  }

  /* Table */
  .ow-pri__table{
    width:100%;
    border-collapse:collapse;
  }
  .ow-pri__table thead{
    background:rgba(255,255,255,.02);
  }
  .ow-pri__table th{
    padding:14px 28px;
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.18em;text-transform:uppercase;
    color:var(--dim);font-weight:500;text-align:left;
  }
  .ow-pri__table th.is-price{text-align:right;}
  .ow-pri__table th.is-peak{color:var(--accent-glow);position:relative;}
  .ow-pri__table td{
    padding:18px 28px;
    border-top:1px solid var(--line);
    vertical-align:middle;
  }
  .ow-pri__table tbody tr:hover{background:rgba(255,255,255,.02);}

  .ow-pri__variant{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:22px;line-height:1.05;color:#fff;
    margin:0 0 2px;font-weight:400;text-transform:uppercase;
  }
  .ow-pri__variant-sub{
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.12em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-pri__price{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:28px;line-height:1;font-weight:400;
    color:#fff;text-align:right;
    display:flex;align-items:baseline;justify-content:flex-end;gap:3px;
  }
  .ow-pri__price span{font-size:14px;color:var(--dim);}
  .ow-pri__price.is-peak{color:var(--accent-glow);text-shadow:0 0 16px <?php echo $outlet['accent_dim']; ?>.3);}
  .ow-pri__price-empty{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;color:var(--dim);text-align:right;
  }

  /* ===== FAQ (per-outlet, one category at a time) ===== */
  .ow-pri__faq{
    padding:80px 40px;
    background:var(--bg);
  }
  .ow-pri__faq-inner{
    max-width:1000px;margin:0 auto;
  }
  .ow-pri__faq-tabs{
    display:flex;gap:8px;flex-wrap:wrap;
    margin-bottom:34px;
  }
  .ow-pri__faq-tab{
    padding:10px 20px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    font-weight:600;cursor:pointer;
    background:rgba(255,255,255,.03);color:var(--dim);
    border:1px solid var(--line);
    transition:color .2s ease, border-color .2s ease, background .2s ease;
  }
  .ow-pri__faq-tab:hover{
    border-color:<?php echo $outlet['accent_dim']; ?>.5);
  }
  .ow-pri__faq-tab.is-active{
    background:var(--accent);color:#0a0a14;
    border-color:var(--accent);
    box-shadow:0 8px 24px -8px var(--accent);
  }
  .ow-pri__faq-panel{display:none;}
  .ow-pri__faq-panel.is-active{display:block;}
  .ow-pri__faq-item{
    border-bottom:1px solid var(--line);
  }
  .ow-pri__faq-item summary{
    list-style:none;cursor:pointer;
    display:flex;align-items:center;justify-content:space-between;gap:18px;
    padding:20px 4px;
    font-size:15.5px;font-weight:600;color:var(--fg);
    transition:color .2s ease;
  }
  .ow-pri__faq-item summary::-webkit-details-marker{display:none;}
  .ow-pri__faq-item summary:hover{color:var(--accent-glow);}
  .ow-pri__faq-chev{
    flex-shrink:0;width:10px;height:10px;
    border-right:2px solid var(--accent);
    border-bottom:2px solid var(--accent);
    transform:rotate(45deg);
    transition:transform .25s ease;
  }
  .ow-pri__faq-item[open] .ow-pri__faq-chev{transform:rotate(225deg);}
  .ow-pri__faq-a{
    padding:0 4px 22px;
    font-size:14.5px;line-height:1.7;color:var(--dim);
    max-width:820px;
  }
  .ow-pri__faq-a p{margin:0 0 12px;}
  .ow-pri__faq-a p:last-child{margin-bottom:0;}
  .ow-pri__faq-a a{color:var(--accent-glow);}

  /* ===== TERMS + CTA ===== */
  .ow-pri__terms{
    background:linear-gradient(180deg,var(--bg) 0%,var(--bg-2) 100%);
    padding:80px 40px 120px;
    border-top:1px solid var(--line);
  }
  .ow-pri__terms-inner{
    max-width:1100px;margin:0 auto;
    display:grid;grid-template-columns:1.2fr 1fr;gap:48px;
    align-items:center;
  }
  .ow-pri__terms-head{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--accent-glow);margin-bottom:18px;
    display:flex;align-items:center;gap:10px;
  }
  .ow-pri__terms-head::before{
    content:"";width:24px;height:1px;background:var(--accent);
  }
  .ow-pri__terms-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(28px,3.5vw,42px);
    line-height:1;letter-spacing:-.02em;
    text-transform:uppercase;
    margin:0 0 24px;font-weight:400;color:#fff;
  }
  .ow-pri__terms-list{
    list-style:none;padding:0;margin:0;
    display:flex;flex-direction:column;gap:10px;
  }
  .ow-pri__terms-list li{
    font-size:14px;line-height:1.55;color:var(--dim);
    padding-left:24px;position:relative;
  }
  .ow-pri__terms-list li::before{
    content:"";position:absolute;left:0;top:7px;
    width:14px;height:7px;
    border-left:2px solid var(--accent);
    border-bottom:2px solid var(--accent);
    transform:rotate(-45deg);
  }
  .ow-pri__terms-list strong{color:var(--fg);font-weight:600;}

  /* CTA card */
  .ow-pri__cta{
    padding:40px 32px;
    background:radial-gradient(ellipse at center,<?php echo $outlet['accent_dim']; ?>.15) 0%,var(--bg-2) 70%);
    border:1px solid <?php echo $outlet['accent_dim']; ?>.3);
    border-radius:24px;
    text-align:center;
  }
  .ow-pri__cta-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:30px;line-height:1.05;
    text-transform:uppercase;font-weight:400;
    margin:0 0 14px;color:#fff;
  }
  .ow-pri__cta-sub{
    font-size:14px;color:var(--dim);line-height:1.5;
    margin:0 0 26px;
  }
  .ow-pri__cta-buttons{
    display:flex;flex-direction:column;gap:10px;
  }
  .ow-pri__cta-btn{
    display:inline-flex;align-items:center;justify-content:center;gap:10px;
    padding:15px 24px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.14em;text-transform:uppercase;
    text-decoration:none;font-weight:700;
    transition:transform .25s ease, gap .25s ease, box-shadow .35s ease;
  }
  .ow-pri__cta-btn--primary{
    background:var(--accent);
    color:#0a0a14;
    box-shadow:0 12px 30px -10px var(--accent);
  }
  .ow-pri__cta-btn--primary:hover{transform:translateY(-2px);gap:14px;}
  .ow-pri__cta-btn--ghost{
    background:rgba(255,255,255,.04);color:#fff;
    border:1px solid var(--line);
  }
  .ow-pri__cta-btn--ghost:hover{
    background:rgba(255,255,255,.08);
    border-color:var(--accent);
    transform:translateY(-2px);gap:14px;
  }

  /* ===== EVENTS: Team Building & Birthday Party ===== */
  .ow-pri__events{
    background:var(--bg-2);
    padding:80px 40px 110px;
    border-top:1px solid var(--line);
  }
  .ow-pri__events-inner{
    max-width:1200px;margin:0 auto;
  }
  .ow-pri__events-grid{
    display:grid;grid-template-columns:repeat(2,1fr);gap:24px;
  }
  .ow-pri__event-card{
    display:flex;flex-direction:column;
    padding:38px 34px 34px;
    background:radial-gradient(ellipse at 20% 0%,<?php echo $outlet['accent_dim']; ?>.14) 0%,var(--bg) 65%);
    border:1px solid var(--line);
    border-radius:24px;
    position:relative;overflow:hidden;
    text-decoration:none;
    transition:transform .35s ease, border-color .25s ease, box-shadow .35s ease;
  }
  .ow-pri__event-card::before{
    content:"";position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(to right,transparent,var(--accent),transparent);
    opacity:.6;
  }
  .ow-pri__event-card:hover{
    transform:translateY(-6px);
    border-color:var(--accent);
    box-shadow:0 20px 60px -20px var(--accent);
  }
  .ow-pri__event-top{
    display:flex;align-items:center;justify-content:space-between;
    gap:16px;margin-bottom:20px;
  }
  .ow-pri__event-icon{
    width:56px;height:56px;border-radius:16px;
    background:<?php echo $outlet['accent_dim']; ?>.12);
    border:1px solid <?php echo $outlet['accent_dim']; ?>.35);
    display:flex;align-items:center;justify-content:center;
    font-size:26px;line-height:1;
  }
  .ow-pri__event-count{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.16em;text-transform:uppercase;
    color:var(--dim);
    padding:8px 14px;
    border:1px solid var(--line);
    border-radius:999px;
    background:rgba(0,0,0,.3);
  }
  .ow-pri__event-count strong{color:var(--accent-glow);font-weight:700;}
  .ow-pri__event-eyebrow{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--accent-glow);margin-bottom:10px;
  }
  .ow-pri__event-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(28px,3vw,38px);
    line-height:1.05;font-weight:400;
    text-transform:uppercase;letter-spacing:-.01em;
    margin:0 0 10px;color:#fff;
  }
  .ow-pri__event-tagline{
    font-size:15px;color:var(--fg);line-height:1.4;
    margin:0 0 10px;
  }
  .ow-pri__event-desc{
    font-size:13.5px;line-height:1.6;color:var(--dim);
    margin:0 0 26px;flex:1;
  }
  .ow-pri__event-link{
    display:inline-flex;align-items:center;gap:10px;
    align-self:flex-start;
    padding:13px 22px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    font-weight:700;
    background:var(--accent);color:#0a0a14;
    box-shadow:0 12px 30px -10px var(--accent);
    transition:gap .25s ease;
  }
  .ow-pri__event-card:hover .ow-pri__event-link{gap:14px;}

  /* Responsive */
  @media (max-width:1000px){
    .ow-pri__hero{padding:90px 28px 60px;}
    .ow-pri__acts{padding:60px 28px 60px;}
    .ow-pri__gallery{padding:45px 28px 10px;}
    .ow-pri__gallery-grid{grid-template-columns:repeat(2,1fr);grid-auto-rows:160px;}
    .ow-pri__main{padding:60px 28px 80px;}
    .ow-pri__terms-inner{grid-template-columns:1fr;gap:32px;}
    .ow-pri__cta-buttons{flex-direction:row;flex-wrap:wrap;}
    .ow-pri__cta-btn{flex:1;min-width:160px;}
    .ow-pri__events{padding:60px 28px 90px;}
    .ow-pri__events-grid{grid-template-columns:1fr;}
    .ow-pri__faq{padding:60px 28px;}
  }
  @media (max-width:600px){
    .ow-pri__hero{padding:70px 18px 50px;}
    .ow-pri__acts{padding:50px 18px 50px;}
    .ow-pri__gallery{padding:40px 18px 6px;}
    .ow-pri__gallery-grid{grid-auto-rows:130px;gap:10px;}
    .ow-pri__main{padding:50px 18px 70px;}
    .ow-pri__hero-title{font-size:54px;}
    .ow-pri__table th,
    .ow-pri__table td{padding:14px 18px;}
    .ow-pri__variant{font-size:18px;}
    .ow-pri__price{font-size:24px;}
    .ow-pri__activity-head{padding:18px 22px 14px;}
    .ow-pri__activity-name{font-size:20px;}
    .ow-pri__terms{padding:60px 18px 90px;}
    .ow-pri__events{padding:50px 18px 80px;}
    .ow-pri__event-card{padding:30px 24px 28px;}
    .ow-pri__faq{padding:50px 18px;}
  }
</style>

<section class="ow-pri">

  <!-- ===== HERO ===== -->
  <div class="ow-pri__hero">
    <div class="ow-pri__hero-grid" aria-hidden="true"></div>
    <div class="ow-pri__hero-inner">
      <div class="ow-pri__hero-eyebrow"><?php echo esc_html( $outlet['brand'] ); ?> · Pricing</div>
      <h1 class="ow-pri__hero-title"><?php echo esc_html( $outlet['name'] ); ?></h1>
      <p class="ow-pri__hero-tag"><?php echo esc_html( $outlet['tagline'] ); ?></p>
      <p class="ow-pri__hero-desc"><?php echo esc_html( $outlet['description'] ); ?></p>

      <div class="ow-pri__hero-meta">
        <?php if ( $outlet['address'] ) : ?>
          <div class="ow-pri__hero-meta-item">
            <span class="ow-pri__hero-meta-icon">📍</span>
            <span><?php echo esc_html( $outlet['address'] ); ?></span>
          </div>
        <?php endif; ?>
        <?php if ( $outlet['phone'] ) : ?>
          <div class="ow-pri__hero-meta-item">
            <span class="ow-pri__hero-meta-icon">📞</span>
            <a href="tel:<?php echo esc_attr( str_replace( ' ', '', $outlet['phone'] ) ); ?>"><?php echo esc_html( $outlet['phone'] ); ?></a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ===== ACTIVITIES & GAMES ===== -->
  <?php if ( ! empty( $outlet_activities ) ) : ?>
  <div class="ow-pri__acts">
    <div class="ow-pri__acts-inner">

      <div class="ow-pri__section-head">
        <div>
          <div class="ow-pri__section-eyebrow">What We Offer</div>
          <h2 class="ow-pri__section-title">Activities &amp; Games</h2>
        </div>
        <div class="ow-pri__section-count">
          <strong><?php echo count( $outlet_activities ); ?></strong> Experience<?php echo count( $outlet_activities ) === 1 ? '' : 's'; ?> at <?php echo esc_html( $outlet['short_name'] ); ?>
        </div>
      </div>

      <p class="ow-pri__acts-intro"><?php echo esc_html( $acts_intro ); ?></p>

      <div class="ow-pri__acts-grid">
        <?php foreach ( $outlet_activities as $activity ) : ?>
          <article class="ow-pri__act-card">
            <?php if ( ! empty( $activity['img'] ) ) : ?>
              <div class="ow-pri__act-img">
                <img src="<?php echo esc_url( $activity['img'] ); ?>" alt="<?php echo esc_attr( $activity['name'] . ' at ' . $outlet['name'] ); ?>" loading="lazy" />
              </div>
            <?php endif; ?>
            <div class="ow-pri__act-body">
              <?php if ( ! empty( $activity['icon'] ) ) : ?>
                <div class="ow-pri__act-icon"><?php echo $activity['icon']; ?></div>
              <?php endif; ?>
              <h3 class="ow-pri__act-name"><?php echo esc_html( $activity['name'] ); ?></h3>
              <p class="ow-pri__act-desc"><?php echo esc_html( $activity['desc'] ); ?></p>
              <?php if ( ! empty( $activity['url'] ) ) : ?>
                <a class="ow-pri__act-btn" href="<?php echo esc_url( $activity['url'] ); ?>">Learn More →</a>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
  <?php endif; ?>

  <!-- ===== EVENTS: Team Building & Birthday Party ===== -->
  <div class="ow-pri__events">
    <div class="ow-pri__events-inner">

      <div class="ow-pri__section-head">
        <div>
          <div class="ow-pri__section-eyebrow">Plan Something Bigger</div>
          <h2 class="ow-pri__section-title">Group Events at <?php echo esc_html( $outlet['short_name'] ); ?></h2>
        </div>
      </div>

      <div class="ow-pri__events-grid">
        <?php foreach ( $event_sections as $event_section ) : ?>
          <a class="ow-pri__event-card" href="<?php echo esc_url( $event_section['url'] ); ?>">
            <div class="ow-pri__event-top">
              <div class="ow-pri__event-icon"><?php echo $event_section['icon']; ?></div>
              <?php if ( $event_section['count'] > 0 ) : ?>
                <div class="ow-pri__event-count">
                  <strong><?php echo (int) $event_section['count']; ?></strong> Package<?php echo $event_section['count'] === 1 ? '' : 's'; ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="ow-pri__event-eyebrow"><?php echo esc_html( $event_section['eyebrow'] ); ?></div>
            <h3 class="ow-pri__event-title"><?php echo esc_html( $event_section['title'] ); ?></h3>
            <p class="ow-pri__event-tagline"><?php echo esc_html( $event_section['tagline'] ); ?></p>
            <p class="ow-pri__event-desc"><?php echo esc_html( $event_section['desc'] ); ?></p>
            <span class="ow-pri__event-link">View Packages →</span>
          </a>
        <?php endforeach; ?>
      </div>

    </div>
  </div>

  <!-- ===== PRICING ===== -->
  <div class="ow-pri__main">
    <div class="ow-pri__main-inner">

      <div class="ow-pri__section-head">
        <div>
          <div class="ow-pri__section-eyebrow">Rates</div>
          <h2 class="ow-pri__section-title">Pricing</h2>
        </div>
        <div class="ow-pri__section-count">All prices per pax · SGD</div>
      </div>

      <!-- Pricing tables -->
      <div class="ow-pri__tables">
        <?php
        if ( empty( $pricing_items ) ) :
        ?>
          <div class="ow-pri__activity">
            <div class="ow-pri__activity-head">
              <div class="ow-pri__activity-name">No pricing yet</div>
            </div>
            <div style="padding:24px 28px;color:var(--dim);">
              Add pricing items in <strong>WP Admin → Pricing → Add New Price</strong>, set outlet to <strong><?php echo esc_html( $outlet['name'] ); ?></strong>.
            </div>
          </div>
        <?php
        else :
          $activity_num = 1;
          foreach ( $pricing_items as $activity_name => $activity_data ) :
            $rows = $activity_data['rows'];
            $has_any_peak = false;
            foreach ( $rows as $row ) {
              if ( get_post_meta( $row->ID, 'pricing_has_peak', true ) === '1' ) {
                $has_any_peak = true;
                break;
              }
            }
        ?>
          <div class="ow-pri__activity">
            <div class="ow-pri__activity-head">
              <div class="ow-pri__activity-icon"><?php echo str_pad( $activity_num, 2, '0', STR_PAD_LEFT ); ?></div>
              <h3 class="ow-pri__activity-name"><?php echo esc_html( $activity_name ); ?></h3>
            </div>
            <table class="ow-pri__table">
              <thead>
                <tr>
                  <th>Variant</th>
                  <th class="is-price">Mon — Thu</th>
                  <?php if ( $has_any_peak ) : ?>
                    <th class="is-price is-peak">Fri — Sun &amp; PH</th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ( $rows as $row ) :
                  $variant   = get_post_meta( $row->ID, 'pricing_variant', true ) ?: $row->post_title;
                  $subtitle  = get_post_meta( $row->ID, 'pricing_subtitle', true );
                  $weekday   = get_post_meta( $row->ID, 'pricing_weekday_price', true );
                  $weekend   = get_post_meta( $row->ID, 'pricing_weekend_price', true );
                  $has_peak  = get_post_meta( $row->ID, 'pricing_has_peak', true ) === '1';
                ?>
                  <tr>
                    <td>
                      <div class="ow-pri__variant"><?php echo esc_html( $variant ); ?></div>
                      <?php if ( $subtitle ) : ?>
                        <div class="ow-pri__variant-sub"><?php echo esc_html( $subtitle ); ?></div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ( $weekday !== '' && $weekday > 0 ) : ?>
                        <div class="ow-pri__price"><span>$</span><?php echo esc_html( $weekday ); ?></div>
                      <?php else : ?>
                        <div class="ow-pri__price-empty">—</div>
                      <?php endif; ?>
                    </td>
                    <?php if ( $has_any_peak ) : ?>
                      <td>
                        <?php if ( $has_peak && $weekend !== '' && $weekend > 0 ) : ?>
                          <div class="ow-pri__price is-peak"><span>$</span><?php echo esc_html( $weekend ); ?></div>
                        <?php elseif ( $weekday !== '' && $weekday > 0 ) : ?>
                          <div class="ow-pri__price"><span>$</span><?php echo esc_html( $weekday ); ?></div>
                        <?php else : ?>
                          <div class="ow-pri__price-empty">—</div>
                        <?php endif; ?>
                      </td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php
            $activity_num++;
          endforeach;
        endif;
        ?>
      </div>

    </div>
  </div>

  <!-- ===== GALLERY (ACF outlet_gallery_1..6 — hidden from visitors when empty) ===== -->
  <?php if ( ! empty( $gallery_images ) || $gallery_editor_hint ) : ?>
  <div class="ow-pri__gallery">
    <div class="ow-pri__gallery-inner">

      <div class="ow-pri__section-head">
        <div>
          <div class="ow-pri__section-eyebrow">Inside <?php echo esc_html( $outlet['short_name'] ); ?></div>
          <h2 class="ow-pri__section-title">Gallery</h2>
        </div>
        <?php if ( ! empty( $gallery_images ) ) : ?>
          <div class="ow-pri__section-count"><strong><?php echo count( $gallery_images ); ?></strong> Photo<?php echo count( $gallery_images ) === 1 ? '' : 's'; ?></div>
        <?php endif; ?>
      </div>

      <div class="ow-pri__gallery-grid">
        <?php if ( ! empty( $gallery_images ) ) : ?>
          <?php foreach ( $gallery_images as $g_index => $g_img ) : ?>
            <div class="ow-pri__gallery-item<?php echo 0 === $g_index ? ' ow-pri__gallery-item--lead' : ''; ?>">
              <img src="<?php echo esc_url( $g_img['src'] ); ?>" alt="<?php echo esc_attr( $g_img['alt'] ); ?>" loading="lazy" />
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <?php for ( $g_index = 0; $g_index < 4; $g_index++ ) : ?>
            <div class="ow-pri__gallery-item ow-pri__gallery-empty<?php echo 0 === $g_index ? ' ow-pri__gallery-item--lead' : ''; ?>">
              <div class="ow-pri__gallery-empty-icon">🖼</div>
              <div class="ow-pri__gallery-empty-text">Gallery slot</div>
            </div>
          <?php endfor; ?>
        <?php endif; ?>
      </div>

      <?php if ( $gallery_editor_hint ) : ?>
        <p class="ow-pri__gallery-hint">Only editors see this placeholder — add photos via <strong>Edit Page → Outlet Gallery</strong> and they will appear here for visitors.</p>
      <?php endif; ?>

    </div>
  </div>
  <?php endif; ?>

  <!-- ===== FAQ (per-outlet, one category at a time) ===== -->
  <?php if ( ! empty( $faq_cats_sorted ) ) : ?>
  <div class="ow-pri__faq">
    <div class="ow-pri__faq-inner">

      <div class="ow-pri__section-head">
        <div>
          <div class="ow-pri__section-eyebrow">Good To Know</div>
          <h2 class="ow-pri__section-title">FAQ</h2>
        </div>
        <div class="ow-pri__section-count">
          Answers for <strong><?php echo esc_html( $outlet['short_name'] ); ?></strong>
        </div>
      </div>

      <div class="ow-pri__faq-tabs" role="tablist">
        <?php $faq_i = 0; foreach ( $faq_cats_sorted as $faq_cat_name => $faq_items ) : ?>
          <button type="button" class="ow-pri__faq-tab<?php echo 0 === $faq_i ? ' is-active' : ''; ?>" data-faq-tab="<?php echo esc_attr( sanitize_title( $faq_cat_name ) ); ?>">
            <?php echo esc_html( $faq_cat_name ); ?>
          </button>
        <?php $faq_i++; endforeach; ?>
      </div>

      <?php $faq_i = 0; foreach ( $faq_cats_sorted as $faq_cat_name => $faq_items ) : ?>
        <div class="ow-pri__faq-panel<?php echo 0 === $faq_i ? ' is-active' : ''; ?>" data-faq-panel="<?php echo esc_attr( sanitize_title( $faq_cat_name ) ); ?>">
          <?php foreach ( $faq_items as $faq_post ) :
            $faq_q = get_post_meta( $faq_post->ID, 'faq_question', true ) ?: $faq_post->post_title;
            $faq_a = get_post_meta( $faq_post->ID, 'faq_answer', true );
          ?>
            <details class="ow-pri__faq-item">
              <summary>
                <span><?php echo esc_html( $faq_q ); ?></span>
                <span class="ow-pri__faq-chev" aria-hidden="true"></span>
              </summary>
              <div class="ow-pri__faq-a"><?php echo wp_kses_post( wpautop( $faq_a ) ); ?></div>
            </details>
          <?php endforeach; ?>
        </div>
      <?php $faq_i++; endforeach; ?>

    </div>
  </div>
  <script>
  (function(){
    var tabs = document.querySelectorAll('.ow-pri__faq-tab');
    tabs.forEach(function(tab){
      tab.addEventListener('click', function(){
        tabs.forEach(function(t){ t.classList.remove('is-active'); });
        tab.classList.add('is-active');
        document.querySelectorAll('.ow-pri__faq-panel').forEach(function(p){
          p.classList.toggle('is-active', p.dataset.faqPanel === tab.dataset.faqTab);
        });
      });
    });
  })();
  </script>
  <?php endif; ?>

  <!-- ===== TERMS + CTA ===== -->
  <div class="ow-pri__terms">
    <div class="ow-pri__terms-inner">

      <div>
        <div class="ow-pri__terms-head">Good To Know</div>
        <h3 class="ow-pri__terms-title">Before You Book</h3>
        <ul class="ow-pri__terms-list">
          <li>All prices are <strong>per pax</strong>, in Singapore Dollars (SGD)</li>
          <li><strong>Peak hours:</strong> Fridays, Saturdays, Sundays, and Public Holidays</li>
          <li><strong>Booking:</strong> Reserve a time slot online — choose your game on arrival</li>
          <li>Please arrive at least <strong>10 minutes before</strong> your session</li>
          <li>Comfortable clothing and <strong>closed-toe shoes</strong> recommended for physical games</li>
          <li>Children under 10 must be <strong>accompanied by an adult</strong> for certain games</li>
          <li>Hosting a group? Ask us about <a href="/team-building" style="color:var(--accent-glow);text-decoration:none;border-bottom:1px solid var(--accent);">team-building packages</a></li>
        </ul>
      </div>

      <div class="ow-pri__cta">
        <h4 class="ow-pri__cta-title">Ready to Book?</h4>
        <p class="ow-pri__cta-sub">Reserve your slot now — pick your activity on arrival.</p>
        <div class="ow-pri__cta-buttons">
          <?php if ( $outlet['bookeo'] ) : ?>
            <a class="ow-pri__cta-btn ow-pri__cta-btn--primary" href="<?php echo esc_url( $outlet['bookeo'] ); ?>" target="_blank" rel="noopener">
              Book <?php echo esc_html( $outlet['short_name'] ); ?> →
            </a>
          <?php endif; ?>
          <?php if ( $outlet['whatsapp'] ) : ?>
            <a class="ow-pri__cta-btn ow-pri__cta-btn--ghost" href="<?php echo esc_url( $outlet['whatsapp'] ); ?>" target="_blank" rel="noopener">
              WhatsApp Us
            </a>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>

</section>

<?php get_footer(); ?>
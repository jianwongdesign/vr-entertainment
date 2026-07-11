<?php
/**
 * Template Name: Event Listing Page (TB / BP × Outlet)
 *
 * Handles BOTH event types × all 3 outlets:
 *   /team-building/kallang-wave-mall
 *   /team-building/orchard-central
 *   /team-building/funan
 *   /birthday-party/kallang-wave-mall
 *   /birthday-party/orchard-central
 *   /birthday-party/funan
 *
 * AUTO-DETECTS:
 *   - Event Type from page PARENT slug ('team-building' or 'birthday-party')
 *   - Outlet from page slug ('kallang-wave-mall' / 'orchard-central' / 'funan')
 *
 * INSTALL:
 *   Upload to your child theme:
 *   /wp-content/themes/hello-elementor-child/page-event-listing.php
 *
 * USAGE:
 *   Assign this template to ALL 6 pages (Page Attributes → Template → "Event Listing Page")
 *   It auto-figures out which event type + outlet from the URL.
 *
 * CONTENT WORKFLOW:
 *   Client adds/edits packages via WP Admin → Events → Add New Package
 *   - Pick Event Type (TB or BP)
 *   - Pick Outlet (which one this package is for)
 *   - Fill in details, price, PDF
 *   - Auto-appears on the matching /[event-type]/[outlet] page
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

// ===== DETECT EVENT TYPE FROM PARENT, OUTLET FROM SLUG =====
global $post;
$outlet_slug = $post->post_name;

$parent_slug = '';
if ( $post->post_parent ) {
    $parent = get_post( $post->post_parent );
    $parent_slug = $parent ? $parent->post_name : '';
}

// ===== EVENT TYPE CONFIG =====
$event_type_config = array(
    'team-building' => array(
        'label'       => 'Team Building',
        'eyebrow'     => 'Team Building Packages',
        'title'       => 'Team Building',
        'tagline'     => 'Stronger squads. Sharper teams.',
        'description' => 'Bring your team out of the office and into the action. From quick icebreakers to full-day adventures — pick a package that fits.',
        'cta_label'   => 'Plan Your Team Event',
    ),
    'birthday-party' => array(
        'label'       => 'Birthday Party',
        'eyebrow'     => 'Birthday Party Packages',
        'title'       => 'Birthday Party',
        'tagline'     => 'A birthday they\'ll actually remember.',
        'description' => 'Forget cake-and-balloons. Throw a birthday party that\'s loud, active, and full of bragging rights. Pick a package below.',
        'cta_label'   => 'Plan Your Birthday',
    ),
);

// Fallback if parent slug detection failed - try URL parsing
if ( ! isset( $event_type_config[ $parent_slug ] ) ) {
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
    if ( strpos( $request_uri, '/team-building/' ) !== false ) {
        $parent_slug = 'team-building';
    } elseif ( strpos( $request_uri, '/birthday-party/' ) !== false ) {
        $parent_slug = 'birthday-party';
    } else {
        $parent_slug = 'team-building'; // safe default
    }
}

$event_type = $event_type_config[ $parent_slug ];

// ===== OUTLET CONFIG (same pattern as pricing page) =====
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
        'bookeo'      => 'https://bookeo.com/overworldKallangwavemall',
        'accent'      => '#ff5722',
        'accent_glow' => '#ff8a3d',
        'accent_dim'  => 'rgba(255,87,34,',
        'bg_gradient' => 'radial-gradient(ellipse at 50% 110%,#1a0a05 0%,#0d0608 50%,#0a0606 100%)',
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
        'bookeo'      => 'https://bookeo.com/overworldorchardcentral',
        'accent'      => '#22e3ff',
        'accent_glow' => '#5ff0ff',
        'accent_dim'  => 'rgba(34,227,255,',
        'bg_gradient' => 'radial-gradient(ellipse at 50% 110%,#001a2e 0%,#02050d 50%,#040810 100%)',
    ),
    'funan' => array(
        'slug'        => 'funan',
        'brand'       => 'Overworld Funan',
        'name'        => 'Funan',
        'short_name'  => 'Funan',
        'address'     => '107 North Bridge Road, #04-14 & K1, Funan, Singapore 179105',
        'phone'       => '+65 8915 0061',
        'whatsapp'    => 'https://wa.me/6589140061',
        'email'       => 'funansupport@overworld.com.sg',
        'bookeo'      => 'https://bookeo.com/overworldfunan',
        'accent'      => '#a855f7',
        'accent_glow' => '#c89aff',
        'accent_dim'  => 'rgba(168,85,247,',
        'bg_gradient' => 'radial-gradient(ellipse at 50% 110%,#1f0a3a 0%,#10081e 50%,#0a081a 100%)',
    ),
);

$outlet = isset( $outlet_config[ $outlet_slug ] ) ? $outlet_config[ $outlet_slug ] : $outlet_config['kallang-wave-mall'];

// ===== Editable page content (ACF, see mu-plugin overworld-event-page-content.php) =====
$default_intros = array(
    'team-building'  => 'Every session is run end-to-end by our Game Masters — briefing, rotations, scoreboards and prizes — so you can play alongside your team instead of managing the day. Packages below can be tailored to your group size, timing and budget.',
    'birthday-party' => 'We handle the games, the gear and the energy — you bring the cake. Every party is hosted by our Game Masters from first briefing to final scoreboard, with a private room and time for food between rounds.',
);
$page_intro = trim( (string) get_post_meta( get_the_ID(), 'event_page_intro', true ) );
if ( '' === $page_intro ) {
    $page_intro = $default_intros[ $parent_slug ] ?? '';
}

$gallery_images = array();
for ( $gi = 1; $gi <= 6; $gi++ ) {
    $att_id = get_post_meta( get_the_ID(), 'event_gallery_' . $gi, true );
    if ( ! $att_id || ! is_numeric( $att_id ) ) continue;
    $src = wp_get_attachment_image_url( (int) $att_id, 'large' );
    if ( ! $src ) continue;
    $alt = get_post_meta( (int) $att_id, '_wp_attachment_image_alt', true );
    $gallery_images[] = array(
        'src' => $src,
        'alt' => $alt ? $alt : $event_type['label'] . ' at Overworld ' . $outlet['name'],
    );
}

$reviews = array();
for ( $ri = 1; $ri <= 4; $ri++ ) {
    $rev_text = trim( (string) get_post_meta( get_the_ID(), 'event_review_' . $ri . '_text', true ) );
    if ( '' === $rev_text ) continue;
    $rev_stars = (int) get_post_meta( get_the_ID(), 'event_review_' . $ri . '_stars', true );
    $reviews[] = array(
        'text'  => $rev_text,
        'name'  => (string) get_post_meta( get_the_ID(), 'event_review_' . $ri . '_name', true ),
        'meta'  => (string) get_post_meta( get_the_ID(), 'event_review_' . $ri . '_meta', true ),
        'stars' => ( $rev_stars >= 1 && $rev_stars <= 5 ) ? $rev_stars : 5,
    );
}

$can_edit_page = is_user_logged_in() && current_user_can( 'edit_post', get_the_ID() );

// ===== QUERY EVENT PACKAGES (unchanged — managed under Events in WP Admin) =====
$packages = get_posts( array(
    'post_type'      => 'event_package',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'meta_query'     => array(
        'relation' => 'AND',
        array(
            'key'   => 'event_type',
            'value' => $parent_slug,
        ),
        array(
            'key'   => 'event_outlet',
            'value' => $outlet_slug,
        ),
        array(
            'relation' => 'OR',
            array(
                'key'   => 'event_active',
                'value' => '1',
            ),
            array(
                'key'     => 'event_active',
                'compare' => 'NOT EXISTS',
            ),
        ),
    ),
    'meta_key'       => 'event_display_order',
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
));
?>

<style>
  .ow-evt{
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
  .ow-evt *{box-sizing:border-box;}

  /* ===== HERO ===== */
  .ow-evt__hero{
    position:relative;
    background: <?php echo $outlet['bg_gradient']; ?>;
    padding:120px 40px 80px;
    overflow:hidden;
  }
  .ow-evt__hero::before{
    content:"";position:absolute;left:0;right:0;bottom:0;height:80%;
    background:radial-gradient(ellipse at center,
      <?php echo $outlet['accent_dim']; ?>.2) 0%,
      transparent 70%);
    filter:blur(60px);pointer-events:none;
  }
  .ow-evt__hero-grid{
    position:absolute;inset:0;pointer-events:none;
    background-image:
      linear-gradient(<?php echo $outlet['accent_dim']; ?>.05) 1px,transparent 1px),
      linear-gradient(90deg,<?php echo $outlet['accent_dim']; ?>.05) 1px,transparent 1px);
    background-size:60px 60px;
    mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
    -webkit-mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
  }
  .ow-evt__hero-inner{
    max-width:1100px;margin:0 auto;
    position:relative;z-index:2;text-align:center;
  }
  .ow-evt__eyebrow{
    display:inline-flex;align-items:center;gap:12px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.24em;text-transform:uppercase;
    color:var(--accent-glow);
    padding:9px 18px;
    border:1px solid <?php echo $outlet['accent_dim']; ?>.4);
    border-radius:999px;
    background:<?php echo $outlet['accent_dim']; ?>.08);
    margin-bottom:28px;
  }
  .ow-evt__eyebrow::before{
    content:"";width:8px;height:8px;border-radius:50%;
    background:var(--accent);
    box-shadow:0 0 12px var(--accent-glow);
  }
  .ow-evt__title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(48px,7vw,108px);
    line-height:1;letter-spacing:-.025em;
    font-weight:400;text-transform:uppercase;
    margin:0 0 18px;
    background:linear-gradient(180deg,#fff 0%,#fff 45%,var(--accent-glow) 100%);
    -webkit-background-clip:text;background-clip:text;
    -webkit-text-fill-color:transparent;
  }
  .ow-evt__tag{
    font-size:clamp(16px,1.7vw,19px);
    color:var(--fg);font-weight:400;line-height:1.4;
    margin:0 0 14px;
  }
  .ow-evt__desc{
    font-size:15px;color:var(--dim);line-height:1.6;
    margin:0 auto 24px;max-width:580px;
  }
  .ow-evt__loc{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-evt__loc strong{color:var(--accent-glow);font-weight:600;}

  /* ===== INTRO ===== */
  .ow-evt__intro{
    padding:64px 40px 0;
    background:var(--bg);
  }
  .ow-evt__intro-inner{
    max-width:820px;margin:0 auto;text-align:center;
  }
  .ow-evt__intro-head{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--accent-glow);margin-bottom:14px;
    display:inline-flex;align-items:center;gap:10px;
  }
  .ow-evt__intro-head::before,
  .ow-evt__intro-head::after{content:"";width:24px;height:1px;background:var(--accent);}
  .ow-evt__intro-text{
    font-size:16px;line-height:1.75;color:var(--dim);margin:0;
  }

  /* ===== GALLERY (same collage as outlet pages) ===== */
  .ow-evt__gallery{
    padding:64px 40px 0;
    background:var(--bg);
  }
  .ow-evt__gallery-inner{max-width:1300px;margin:0 auto;}
  .ow-evt__gallery-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    grid-auto-rows:190px;
    grid-auto-flow:dense;
    gap:14px;
  }
  .ow-evt__gallery-item{
    position:relative;overflow:hidden;
    border-radius:18px;
    border:1px solid var(--line);
    background:var(--bg-2);
  }
  .ow-evt__gallery-item--lead{grid-column:span 2;grid-row:span 2;}
  .ow-evt__gallery-item:nth-child(4):last-child{grid-column:span 2;}
  .ow-evt__gallery-item img{
    width:100% !important;height:100% !important;
    object-fit:cover !important;object-position:center !important;
    display:block !important;
    position:absolute !important;top:0 !important;left:0 !important;
    max-width:none !important;max-height:none !important;
    transition:transform .5s ease;
  }
  .ow-evt__gallery-item:hover img{transform:scale(1.05);}
  .ow-evt__gallery-empty{
    display:flex;align-items:center;justify-content:center;
    flex-direction:column;gap:8px;color:var(--dim);
    background:radial-gradient(ellipse at center,<?php echo $outlet['accent_dim']; ?>.08) 0%,var(--bg-2) 70%);
    border:1px dashed <?php echo $outlet['accent_dim']; ?>.35);
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.14em;text-transform:uppercase;
  }
  .ow-evt__gallery-hint{
    margin:16px 0 0;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.1em;
    color:var(--dim);text-align:center;
  }
  .ow-evt__gallery-hint strong{color:var(--accent-glow);font-weight:700;}

  /* ===== REVIEWS ===== */
  .ow-evt__reviews{
    padding:80px 40px 80px;
    background:var(--bg);
  }
  .ow-evt__reviews-inner{max-width:1300px;margin:0 auto;}
  .ow-evt__reviews-head{
    display:flex;align-items:baseline;justify-content:space-between;
    margin-bottom:36px;padding-bottom:20px;
    border-bottom:1px solid var(--line);
    gap:24px;flex-wrap:wrap;
  }
  .ow-evt__reviews-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(24px,3vw,34px);line-height:1;font-weight:400;
    text-transform:uppercase;margin:0;color:#fff;
  }
  .ow-evt__reviews-grid{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px;
  }
  .ow-evt__review{
    display:flex;flex-direction:column;
    padding:26px 26px 24px;
    background:var(--bg-2);
    border:1px solid var(--line);
    border-radius:20px;
    position:relative;overflow:hidden;
  }
  .ow-evt__review::before{
    content:"";position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(to right,transparent,var(--accent),transparent);
    opacity:.5;
  }
  .ow-evt__review-stars{
    color:var(--accent-glow);
    font-size:15px;letter-spacing:3px;
    margin-bottom:14px;
    text-shadow:0 0 14px <?php echo $outlet['accent_dim']; ?>.4);
  }
  .ow-evt__review-text{
    font-size:14px;line-height:1.65;color:var(--dim);
    margin:0 0 18px;flex:1;font-style:italic;
  }
  .ow-evt__review-name{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:16px;font-weight:400;text-transform:uppercase;
    color:#fff;letter-spacing:.02em;
  }
  .ow-evt__review-meta{
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--dim);margin-top:4px;
  }

  /* ===== PACKAGES GRID ===== */
  .ow-evt__main{
    padding:80px 40px 100px;
    background:var(--bg);
  }
  .ow-evt__main-inner{
    max-width:1300px;margin:0 auto;
  }

  .ow-evt__section-head{
    display:flex;align-items:baseline;justify-content:space-between;
    margin-bottom:48px;padding-bottom:24px;
    border-bottom:1px solid var(--line);
    gap:24px;flex-wrap:wrap;
  }
  .ow-evt__section-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:32px;line-height:1;font-weight:400;
    text-transform:uppercase;margin:0;color:#fff;
  }
  .ow-evt__section-count{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.18em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-evt__section-count strong{color:var(--accent-glow);font-weight:700;}

  /* Card grid */
  .ow-evt__grid{
    display:grid;grid-template-columns:repeat(3,1fr);gap:24px;
  }

  .ow-evt__card{
    background:var(--bg-2);
    border:1px solid var(--line);
    border-radius:20px;
    overflow:hidden;
    transition:transform .35s ease, border-color .25s ease, box-shadow .35s ease;
    display:flex;flex-direction:column;
    position:relative;
  }
  .ow-evt__card::before{
    content:"";position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(to right,transparent,var(--accent),transparent);
    opacity:.6;z-index:1;
  }
  .ow-evt__card:hover{
    transform:translateY(-6px);
    border-color:var(--accent);
    box-shadow:0 20px 60px -20px var(--accent);
  }

  .ow-evt__card-img{
    position:relative;
    aspect-ratio:16/10;
    background:rgba(255,255,255,.02);
    overflow:hidden;
  }
  .ow-evt__card-img img{
    width:100% !important;height:100% !important;
    object-fit:cover !important;
    display:block !important;
    position:absolute !important;top:0 !important;left:0 !important;
    max-width:none !important;max-height:none !important;
  }
  .ow-evt__card-img-empty{
    width:100%;height:100%;
    display:flex;align-items:center;justify-content:center;
    color:var(--dim);
    background:radial-gradient(ellipse at center,<?php echo $outlet['accent_dim']; ?>.1) 0%,transparent 60%);
    font-size:40px;opacity:.4;
  }

  .ow-evt__card-body{
    padding:24px 26px 26px;
    flex:1;display:flex;flex-direction:column;
  }
  .ow-evt__card-name{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:24px;line-height:1.1;font-weight:400;
    text-transform:uppercase;color:#fff;
    margin:0 0 8px;letter-spacing:-.005em;
  }
  .ow-evt__card-meta{
    display:flex;align-items:center;gap:16px;flex-wrap:wrap;
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--dim);
    margin-bottom:14px;
  }
  .ow-evt__card-meta-item{display:flex;align-items:center;gap:6px;}
  .ow-evt__card-meta-item span{color:var(--accent-glow);}
  .ow-evt__card-tagline{
    font-size:13.5px;line-height:1.5;color:var(--dim);
    margin:0 0 18px;flex:1;
  }
  .ow-evt__card-price{
    display:flex;align-items:baseline;gap:6px;
    padding:14px 0;
    border-top:1px solid var(--line);
    border-bottom:1px solid var(--line);
    margin-bottom:18px;
  }
  .ow-evt__card-price-from{
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.16em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-evt__card-price-num{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:28px;line-height:1;color:#fff;font-weight:400;
    letter-spacing:-.01em;
  }
  .ow-evt__card-price-num span{color:var(--accent);font-size:18px;}
  .ow-evt__card-price-unit{
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--dim);margin-left:auto;
  }
  .ow-evt__card-ctas{
    display:flex;gap:8px;
  }
  .ow-evt__card-btn{
    flex:1;
    display:inline-flex;align-items:center;justify-content:center;gap:8px;
    padding:12px 18px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    text-decoration:none;font-weight:700;
    transition:transform .25s ease, gap .25s ease;
  }
  .ow-evt__card-btn--primary{
    background:var(--accent);color:#0a0a14;
  }
  .ow-evt__card-btn--primary:hover{transform:translateY(-2px);gap:12px;}
  .ow-evt__card-btn--ghost{
    background:rgba(255,255,255,.04);color:#fff;
    border:1px solid var(--line);
  }
  .ow-evt__card-btn--ghost:hover{
    background:rgba(255,255,255,.08);
    border-color:var(--accent);
    transform:translateY(-2px);gap:12px;
  }

  /* Empty state */
  .ow-evt__empty{
    text-align:center;padding:80px 30px;
    border:1px dashed var(--line);
    border-radius:24px;
    color:var(--dim);
  }
  .ow-evt__empty h3{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:32px;color:#fff;margin:0 0 14px;
    font-weight:400;text-transform:uppercase;
  }
  .ow-evt__empty p{
    font-size:15px;line-height:1.6;margin:0 0 24px;
  }
  .ow-evt__empty a{
    display:inline-flex;align-items:center;gap:8px;
    padding:14px 24px;border-radius:999px;
    background:var(--accent);color:#0a0a14;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.14em;text-transform:uppercase;
    text-decoration:none;font-weight:700;
    transition:transform .25s ease;
  }
  .ow-evt__empty a:hover{transform:translateY(-2px);}

  /* ===== ENQUIRY CTA ===== */
  .ow-evt__enquiry{
    background:linear-gradient(180deg,var(--bg) 0%,var(--bg-2) 100%);
    padding:80px 40px 100px;
    border-top:1px solid var(--line);
  }
  .ow-evt__enquiry-inner{
    max-width:900px;margin:0 auto;
    padding:48px 40px;
    background:radial-gradient(ellipse at center,<?php echo $outlet['accent_dim']; ?>.15) 0%,var(--bg-2) 70%);
    border:1px solid <?php echo $outlet['accent_dim']; ?>.3);
    border-radius:24px;
    text-align:center;
  }
  .ow-evt__enquiry-eyebrow{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--accent-glow);margin-bottom:14px;
  }
  .ow-evt__enquiry-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(28px,3.5vw,42px);
    line-height:1.05;text-transform:uppercase;font-weight:400;
    margin:0 0 14px;color:#fff;
  }
  .ow-evt__enquiry-sub{
    font-size:15px;color:var(--dim);line-height:1.55;
    margin:0 auto 28px;max-width:520px;
  }
  .ow-evt__enquiry-buttons{
    display:flex;gap:12px;justify-content:center;flex-wrap:wrap;
  }
  .ow-evt__enquiry-btn{
    display:inline-flex;align-items:center;gap:10px;
    padding:14px 24px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.14em;text-transform:uppercase;
    text-decoration:none;font-weight:700;
    transition:transform .25s ease, gap .25s ease;
  }
  .ow-evt__enquiry-btn--primary{
    background:var(--accent);color:#0a0a14;
    box-shadow:0 12px 30px -10px var(--accent);
  }
  .ow-evt__enquiry-btn--primary:hover{transform:translateY(-2px);gap:14px;}
  .ow-evt__enquiry-btn--ghost{
    background:rgba(255,255,255,.04);color:#fff;
    border:1px solid var(--line);
  }
  .ow-evt__enquiry-btn--ghost:hover{
    background:rgba(255,255,255,.08);
    border-color:var(--accent);
    transform:translateY(-2px);gap:14px;
  }

  /* Responsive */
  @media (max-width:1000px){
    .ow-evt__hero{padding:90px 28px 60px;}
    .ow-evt__intro{padding:50px 28px 0;}
    .ow-evt__gallery{padding:50px 28px 0;}
    .ow-evt__gallery-grid{grid-template-columns:repeat(2,1fr);grid-auto-rows:160px;}
    .ow-evt__reviews{padding:60px 28px 60px;}
    .ow-evt__main{padding:60px 28px 80px;}
    .ow-evt__grid{grid-template-columns:repeat(2,1fr);gap:18px;}
  }
  @media (max-width:680px){
    .ow-evt__hero{padding:70px 18px 50px;}
    .ow-evt__intro{padding:40px 18px 0;}
    .ow-evt__gallery{padding:40px 18px 0;}
    .ow-evt__gallery-grid{grid-auto-rows:130px;gap:10px;}
    .ow-evt__reviews{padding:50px 18px 50px;}
    .ow-evt__main{padding:50px 18px 70px;}
    .ow-evt__title{font-size:54px;}
    .ow-evt__grid{grid-template-columns:1fr;}
    .ow-evt__section-title{font-size:26px;}
    .ow-evt__enquiry-inner{padding:36px 24px;}
    .ow-evt__enquiry-buttons{flex-direction:column;}
    .ow-evt__enquiry-btn{justify-content:center;width:100%;}
  }
</style>

<section class="ow-evt">

  <!-- ===== HERO ===== -->
  <div class="ow-evt__hero">
    <div class="ow-evt__hero-grid" aria-hidden="true"></div>
    <div class="ow-evt__hero-inner">
      <div class="ow-evt__eyebrow"><?php echo esc_html( $event_type['eyebrow'] ); ?> · <?php echo esc_html( $outlet['short_name'] ); ?></div>
      <h1 class="ow-evt__title"><?php echo esc_html( $event_type['title'] ); ?></h1>
      <p class="ow-evt__tag"><?php echo esc_html( $event_type['tagline'] ); ?></p>
      <p class="ow-evt__desc"><?php echo esc_html( $event_type['description'] ); ?></p>
      <p class="ow-evt__loc">📍 At <strong><?php echo esc_html( $outlet['name'] ); ?></strong></p>
    </div>
  </div>

  <!-- ===== INTRO (ACF event_page_intro) ===== -->
  <?php if ( $page_intro ) : ?>
  <div class="ow-evt__intro">
    <div class="ow-evt__intro-inner">
      <div class="ow-evt__intro-head">How It Works</div>
      <p class="ow-evt__intro-text"><?php echo esc_html( $page_intro ); ?></p>
    </div>
  </div>
  <?php endif; ?>

  <!-- ===== GALLERY (ACF event_gallery_1..6 — hidden from visitors when empty) ===== -->
  <?php if ( ! empty( $gallery_images ) || $can_edit_page ) : ?>
  <div class="ow-evt__gallery">
    <div class="ow-evt__gallery-inner">
      <div class="ow-evt__gallery-grid">
        <?php if ( ! empty( $gallery_images ) ) : ?>
          <?php foreach ( $gallery_images as $g_index => $g_img ) : ?>
            <div class="ow-evt__gallery-item<?php echo 0 === $g_index ? ' ow-evt__gallery-item--lead' : ''; ?>">
              <img src="<?php echo esc_url( $g_img['src'] ); ?>" alt="<?php echo esc_attr( $g_img['alt'] ); ?>" loading="lazy" />
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <?php for ( $g_index = 0; $g_index < 4; $g_index++ ) : ?>
            <div class="ow-evt__gallery-item ow-evt__gallery-empty<?php echo 0 === $g_index ? ' ow-evt__gallery-item--lead' : ''; ?>">
              <div>🖼</div>
              <div>Gallery slot</div>
            </div>
          <?php endfor; ?>
        <?php endif; ?>
      </div>
      <?php if ( empty( $gallery_images ) && $can_edit_page ) : ?>
        <p class="ow-evt__gallery-hint">Only editors see this placeholder — add photos via <strong>Edit Page → Event Page — Intro, Gallery &amp; Reviews</strong>.</p>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- ===== PACKAGES ===== -->
  <div class="ow-evt__main">
    <div class="ow-evt__main-inner">

      <div class="ow-evt__section-head">
        <h2 class="ow-evt__section-title">Available Packages</h2>
        <div class="ow-evt__section-count">
          <strong><?php echo count( $packages ); ?></strong> Package<?php echo count( $packages ) === 1 ? '' : 's'; ?> at <?php echo esc_html( $outlet['short_name'] ); ?>
        </div>
      </div>

      <?php if ( empty( $packages ) ) : ?>

        <div class="ow-evt__empty">
          <h3>No packages yet for <?php echo esc_html( $outlet['short_name'] ); ?></h3>
          <p>
            <?php echo esc_html( $event_type['label'] ); ?> packages for this outlet haven't been published yet.<br>
            Reach out and we'll customise something just for you.
          </p>
          <?php if ( $outlet['whatsapp'] ) : ?>
            <a href="<?php echo esc_url( $outlet['whatsapp'] ); ?>" target="_blank" rel="noopener">
              WhatsApp Us →
            </a>
          <?php endif; ?>
        </div>

      <?php else : ?>

        <div class="ow-evt__grid">
          <?php foreach ( $packages as $pkg ) :
            // Pull text-based ACF fields (flexible free-form text)
            $tagline    = get_post_meta( $pkg->ID, 'event_tagline', true );
            $price_from = get_post_meta( $pkg->ID, 'event_price_from', true );
            $duration   = get_post_meta( $pkg->ID, 'event_duration', true );
            $group_size = get_post_meta( $pkg->ID, 'event_group_size', true );
            $pdf        = get_post_meta( $pkg->ID, 'event_pdf', true );
            $pdf_url    = '';
            if ( $pdf ) {
                $pdf_url = is_numeric( $pdf ) ? wp_get_attachment_url( $pdf ) : $pdf;
            }
            $img = get_the_post_thumbnail_url( $pkg->ID, 'large' );
            $permalink = get_permalink( $pkg->ID );
          ?>
            <article class="ow-evt__card">

              <div class="ow-evt__card-img">
                <?php if ( $img ) : ?>
                  <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $pkg->post_title ); ?>" loading="lazy" />
                <?php else : ?>
                  <div class="ow-evt__card-img-empty">🎉</div>
                <?php endif; ?>
              </div>

              <div class="ow-evt__card-body">
                <h3 class="ow-evt__card-name"><?php echo esc_html( $pkg->post_title ); ?></h3>

                <div class="ow-evt__card-meta">
                  <?php if ( $duration ) : ?>
                    <div class="ow-evt__card-meta-item">
                      <span>⏱</span><?php echo esc_html( $duration ); ?>
                    </div>
                  <?php endif; ?>
                  <?php if ( $group_size ) : ?>
                    <div class="ow-evt__card-meta-item">
                      <span>👥</span><?php echo esc_html( $group_size ); ?>
                    </div>
                  <?php endif; ?>
                </div>

                <?php if ( $tagline ) : ?>
                  <p class="ow-evt__card-tagline"><?php echo esc_html( $tagline ); ?></p>
                <?php endif; ?>

                <?php if ( $price_from ) : ?>
                  <div class="ow-evt__card-price">
                    <span class="ow-evt__card-price-from">From</span>
                    <span class="ow-evt__card-price-num"><?php echo esc_html( $price_from ); ?></span>
                  </div>
                <?php endif; ?>

                <div class="ow-evt__card-ctas">
                  <a class="ow-evt__card-btn ow-evt__card-btn--primary" href="<?php echo esc_url( $permalink ); ?>">
                    Details →
                  </a>
                  <?php if ( $pdf_url ) : ?>
                    <a class="ow-evt__card-btn ow-evt__card-btn--ghost" href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener">
                      📄 PDF
                    </a>
                  <?php endif; ?>
                </div>
              </div>

            </article>
          <?php endforeach; ?>
        </div>

      <?php endif; ?>

    </div>
  </div>

  <!-- ===== REVIEWS (ACF event_review_1..4 — hidden from visitors when empty) ===== -->
  <?php if ( ! empty( $reviews ) || $can_edit_page ) : ?>
  <div class="ow-evt__reviews">
    <div class="ow-evt__reviews-inner">

      <div class="ow-evt__reviews-head">
        <h2 class="ow-evt__reviews-title">What Groups Say</h2>
        <div class="ow-evt__section-count">
          <strong><?php echo count( $reviews ); ?></strong> Review<?php echo count( $reviews ) === 1 ? '' : 's'; ?>
        </div>
      </div>

      <?php if ( ! empty( $reviews ) ) : ?>
        <div class="ow-evt__reviews-grid">
          <?php foreach ( $reviews as $review ) : ?>
            <article class="ow-evt__review">
              <div class="ow-evt__review-stars" aria-label="<?php echo esc_attr( $review['stars'] ); ?> out of 5 stars"><?php echo str_repeat( '★', $review['stars'] ) . str_repeat( '☆', 5 - $review['stars'] ); ?></div>
              <p class="ow-evt__review-text">“<?php echo esc_html( $review['text'] ); ?>”</p>
              <?php if ( $review['name'] ) : ?>
                <div class="ow-evt__review-name"><?php echo esc_html( $review['name'] ); ?></div>
              <?php endif; ?>
              <?php if ( $review['meta'] ) : ?>
                <div class="ow-evt__review-meta"><?php echo esc_html( $review['meta'] ); ?></div>
              <?php endif; ?>
            </article>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <p class="ow-evt__gallery-hint">Only editors see this — add reviews via <strong>Edit Page → Event Page — Intro, Gallery &amp; Reviews</strong> and they will appear here for visitors.</p>
      <?php endif; ?>

    </div>
  </div>
  <?php endif; ?>

  <!-- ===== ENQUIRY CTA ===== -->
  <div class="ow-evt__enquiry">
    <div class="ow-evt__enquiry-inner">
      <div class="ow-evt__enquiry-eyebrow">Need Something Custom?</div>
      <h3 class="ow-evt__enquiry-title"><?php echo esc_html( $event_type['cta_label'] ); ?></h3>
      <p class="ow-evt__enquiry-sub">
        Don't see what you need? We'll customise a package for your group size, budget, and vibe — just reach out at <?php echo esc_html( $outlet['short_name'] ); ?>.
      </p>
      <div class="ow-evt__enquiry-buttons">
        <?php if ( $outlet['whatsapp'] ) : ?>
          <a class="ow-evt__enquiry-btn ow-evt__enquiry-btn--primary" href="<?php echo esc_url( $outlet['whatsapp'] ); ?>" target="_blank" rel="noopener">
            WhatsApp <?php echo esc_html( $outlet['short_name'] ); ?> →
          </a>
        <?php endif; ?>
        <?php if ( $outlet['email'] ) : ?>
          <a class="ow-evt__enquiry-btn ow-evt__enquiry-btn--ghost" href="mailto:<?php echo esc_attr( $outlet['email'] ); ?>?subject=<?php echo esc_attr( $event_type['label'] . ' enquiry - ' . $outlet['short_name'] ); ?>">
            Email Us
          </a>
        <?php endif; ?>
        <?php if ( $outlet['phone'] ) : ?>
          <a class="ow-evt__enquiry-btn ow-evt__enquiry-btn--ghost" href="tel:<?php echo esc_attr( str_replace( ' ', '', $outlet['phone'] ) ); ?>">
            Call <?php echo esc_html( $outlet['phone'] ); ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

</section>

<?php get_footer(); ?>
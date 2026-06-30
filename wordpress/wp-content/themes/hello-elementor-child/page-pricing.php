<?php
/**
 * Template Name: Pricing Page
 *
 * INSTALL: Upload to your child theme:
 *   /wp-content/themes/hello-elementor-child/page-pricing.php
 *
 * USAGE:
 *   - Assign this template to ALL 3 pricing pages:
 *     /pricing/kallang-wave-mall, /pricing/orchard-central, /pricing/funan
 *   - The template auto-detects the outlet from the page slug
 *
 * To set the hero image:
 *   - Edit the page in WP admin
 *   - Set the FEATURED IMAGE (right sidebar) — that becomes the hero/feature image
 *   - Recommended dimensions: 1600x900px (16:9), .webp format, under 300KB
 *
 * UPDATE: Funan is now OPERATIONAL — uses the same full pricing layout as other outlets.
 * All 3 outlets pull pricing data from the Pricing CPT (ACF) where pricing_outlet matches the slug.
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
        'maps'        => 'https://maps.app.goo.gl/3yd7X5HqY8s7qDr38',
        'bookeo'      => 'https://bookeo.com/overworldfunan',
        'accent'      => '#a855f7',
        'accent_glow' => '#c89aff',
        'accent_dim'  => 'rgba(168,85,247,',
        'bg_gradient' => 'radial-gradient(ellipse at 50% 110%,#1f0a3a 0%,#10081e 50%,#0a081a 100%)',
        'tagline'     => 'The party arena in the heart of the CBD.',
        'description' => 'Home of XR Party Game, VR Free Roam, and Floor Is Lava — Funan brings carnival energy to every visit.',
    ),
);

// Default fallback if slug doesn't match
$outlet = isset( $outlet_config[ $slug ] ) ? $outlet_config[ $slug ] : $outlet_config['kallang-wave-mall'];

// Hero image: use featured image if set, otherwise empty
$hero_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );

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

  /* ===== MAIN: Pricing tables + Image ===== */
  .ow-pri__main{
    padding:80px 40px 100px;
    background:var(--bg);
  }
  .ow-pri__main-inner{
    max-width:1300px;margin:0 auto;
    display:grid;grid-template-columns:1.4fr 1fr;gap:48px;
    align-items:start;
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

  /* Image column */
  .ow-pri__media{
    position:sticky;top:100px;
    border-radius:24px;overflow:hidden;
    border:1px solid var(--line);
    aspect-ratio:16/9;
    background:var(--bg-2);
  }
  .ow-pri__media-no-img{
    width:100%;height:100%;
    display:flex;align-items:center;justify-content:center;
    flex-direction:column;gap:14px;
    color:var(--dim);
    background:radial-gradient(ellipse at center,<?php echo $outlet['accent_dim']; ?>.08) 0%,transparent 60%);
    padding:40px;text-align:center;
  }
  .ow-pri__media-no-img-icon{
    font-size:40px;opacity:.5;
  }
  .ow-pri__media-no-img-text{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.16em;text-transform:uppercase;
  }
  .ow-pri__media img{
    width:100% !important;height:100% !important;
    object-fit:cover !important;object-position:center !important;
    display:block !important;
    position:absolute !important;top:0 !important;left:0 !important;
    max-width:none !important;max-height:none !important;
  }
  .ow-pri__media-overlay{
    position:absolute;left:24px;bottom:24px;right:24px;z-index:2;
  }
  .ow-pri__media-tag{
    display:inline-flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.18em;text-transform:uppercase;
    color:#fff;
    padding:8px 14px;
    background:rgba(0,0,0,.55);
    backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
    border:1px solid <?php echo $outlet['accent_dim']; ?>.4);
    border-radius:999px;
  }
  .ow-pri__media-tag::before{
    content:"";width:6px;height:6px;border-radius:50%;background:var(--accent);
    box-shadow:0 0 10px var(--accent-glow);
  }

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

  /* Responsive */
  @media (max-width:1000px){
    .ow-pri__hero{padding:90px 28px 60px;}
    .ow-pri__main{padding:60px 28px 80px;}
    .ow-pri__main-inner{grid-template-columns:1fr;gap:32px;}
    .ow-pri__media{position:static;}
    .ow-pri__terms-inner{grid-template-columns:1fr;gap:32px;}
    .ow-pri__cta-buttons{flex-direction:row;flex-wrap:wrap;}
    .ow-pri__cta-btn{flex:1;min-width:160px;}
  }
  @media (max-width:600px){
    .ow-pri__hero{padding:70px 18px 50px;}
    .ow-pri__main{padding:50px 18px 70px;}
    .ow-pri__hero-title{font-size:54px;}
    .ow-pri__table th,
    .ow-pri__table td{padding:14px 18px;}
    .ow-pri__variant{font-size:18px;}
    .ow-pri__price{font-size:24px;}
    .ow-pri__activity-head{padding:18px 22px 14px;}
    .ow-pri__activity-name{font-size:20px;}
    .ow-pri__terms{padding:60px 18px 90px;}
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

  <!-- ===== MAIN: Tables + Image ===== -->
  <div class="ow-pri__main">
    <div class="ow-pri__main-inner">

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

      <!-- Image column -->
      <div class="ow-pri__media">
        <?php if ( $hero_image ) : ?>
          <img src="<?php echo esc_url( $hero_image ); ?>" alt="<?php echo esc_attr( $outlet['name'] ); ?>" loading="lazy" />
          <div class="ow-pri__media-overlay">
            <span class="ow-pri__media-tag"><?php echo esc_html( $outlet['short_name'] ); ?></span>
          </div>
        <?php else : ?>
          <div class="ow-pri__media-no-img">
            <div class="ow-pri__media-no-img-icon">🖼</div>
            <div class="ow-pri__media-no-img-text">Set a featured image<br>for this page<br>(1600×900 px recommended)</div>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>

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
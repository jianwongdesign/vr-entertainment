<?php
/**
 * Template Name: Event Hub (TB / BP — All Outlets)
 *
 * Landing page for an event type across ALL THREE outlets:
 *   /team-building
 *   /birthday-party
 *
 * AUTO-DETECTS the event type from the page's own slug
 * ('team-building' or 'birthday-party').
 *
 * Shows one card per outlet (Kallang / Orchard / Funan) with that
 * outlet's accent colour, activity mix, live package count, and a link
 * into the matching /[event-type]/[outlet] listing page.
 *
 * USAGE:
 *   Assign this template to the two PARENT pages
 *   (Page Attributes → Template → "Event Hub (TB / BP — All Outlets)").
 *   The per-outlet child pages keep the "Event Listing Page" template.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

global $post;
$type_slug = $post->post_name;

// ===== EVENT TYPE CONFIG =====
$event_type_config = array(
    'team-building' => array(
        'label'       => 'Team Building',
        'eyebrow'     => 'Team Building At Overworld',
        'title'       => 'Team Building',
        'tagline'     => 'Stronger squads. Sharper teams.',
        'description' => 'Bring your team out of the office and into the action. Three outlets across Singapore, each with its own mix of VR and physical challenges — pick the one that fits your crew.',
        'other_slug'  => 'birthday-party',
        'other_label' => 'Planning a birthday instead?',
    ),
    'birthday-party' => array(
        'label'       => 'Birthday Party',
        'eyebrow'     => 'Birthday Parties At Overworld',
        'title'       => 'Birthday Party',
        'tagline'     => 'A birthday they\'ll actually remember.',
        'description' => 'Forget cake-and-balloons. Throw a birthday that\'s loud, active, and full of bragging rights — at any of our three outlets across Singapore.',
        'other_slug'  => 'team-building',
        'other_label' => 'Planning a team event instead?',
    ),
);

if ( ! isset( $event_type_config[ $type_slug ] ) ) {
    $type_slug = 'team-building'; // safe default
}
$event_type = $event_type_config[ $type_slug ];

// ===== OUTLET CONFIG (same data as the event listing / pricing pages) =====
$outlets = array(
    array(
        'slug'        => 'kallang-wave-mall',
        'brand'       => 'Overworld VR',
        'name'        => 'Kallang Wave Mall',
        'short_name'  => 'Kallang',
        'address'     => '1 Stadium Place #01-63/64, Singapore 397628',
        'phone'       => '+65 6513 0561',
        'whatsapp'    => 'https://wa.me/6596101682',
        'activities'  => array( 'VR Arcade', 'VR Escape', 'VR Machine Ride', 'Floor Is Lava' ),
        'blurb'       => 'The flagship VR-heavy outlet by the National Stadium — headsets, escape rooms, rides and lava.',
        'accent'      => '#ff5722',
        'accent_glow' => '#ff8a3d',
        'accent_dim'  => 'rgba(255,87,34,',
    ),
    array(
        'slug'        => 'orchard-central',
        'brand'       => 'Overworld Lava',
        'name'        => 'Orchard Central',
        'short_name'  => 'Orchard',
        'address'     => '181 Orchard Road, #05-30/K1/K3, Singapore 238896',
        'phone'       => '+65 8801 4303',
        'whatsapp'    => 'https://wa.me/message/WJ7MGRFFVGHAF1',
        'activities'  => array( 'Floor Is Lava', 'Laser Maze', 'Tap Tap' ),
        'blurb'       => 'All-physical, all-energy play in the heart of town — built for head-to-head team showdowns.',
        'accent'      => '#22e3ff',
        'accent_glow' => '#5ff0ff',
        'accent_dim'  => 'rgba(34,227,255,',
    ),
    array(
        'slug'        => 'funan',
        'brand'       => 'Overworld Funan',
        'name'        => 'Funan',
        'short_name'  => 'Funan',
        'address'     => '107 North Bridge Road, #04-14 & K1, Singapore 179105',
        'phone'       => '+65 8914 0061',
        'whatsapp'    => 'https://wa.me/6589140061',
        'activities'  => array( 'Floor Is Lava', 'VR Free Roam', 'XR Party Game' ),
        'blurb'       => 'City Hall\'s mixed-reality playground — free-roam VR adventures plus lava and XR party games.',
        'accent'      => '#a855f7',
        'accent_glow' => '#c89aff',
        'accent_dim'  => 'rgba(168,85,247,',
    ),
);

// Live package count per outlet (same filters as the listing template)
foreach ( $outlets as $i => $o ) {
    $packages = get_posts( array(
        'post_type'      => 'event_package',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'meta_query'     => array(
            'relation' => 'AND',
            array( 'key' => 'event_type', 'value' => $type_slug ),
            array( 'key' => 'event_outlet', 'value' => $o['slug'] ),
            array(
                'relation' => 'OR',
                array( 'key' => 'event_active', 'value' => '1' ),
                array( 'key' => 'event_active', 'compare' => 'NOT EXISTS' ),
            ),
        ),
    ) );
    $outlets[ $i ]['package_count'] = count( $packages );
}
?>

<style>
  .ow-hub{
    --lava:#ff5722;
    --lava-glow:#ff8a3d;
    --bg:#0a0a14;
    --bg-2:#13131f;
    --fg:#fff;
    --dim:rgba(220,225,240,.65);
    --line:rgba(255,255,255,.08);
    background:var(--bg);
    color:var(--fg);
    font-family:'Space Grotesk','Inter',system-ui,sans-serif;
  }
  .ow-hub *{box-sizing:border-box;}

  /* ===== HERO ===== */
  .ow-hub__hero{
    position:relative;
    background:radial-gradient(ellipse at 50% 110%,#1a0a05 0%,#0d0608 50%,#0a0606 100%);
    padding:120px 40px 80px;
    overflow:hidden;
  }
  .ow-hub__hero::before{
    content:"";position:absolute;left:0;right:0;bottom:0;height:80%;
    background:radial-gradient(ellipse at center,rgba(255,87,34,.2) 0%,transparent 70%);
    filter:blur(60px);pointer-events:none;
  }
  .ow-hub__hero-grid{
    position:absolute;inset:0;pointer-events:none;
    background-image:
      linear-gradient(rgba(255,87,34,.05) 1px,transparent 1px),
      linear-gradient(90deg,rgba(255,87,34,.05) 1px,transparent 1px);
    background-size:60px 60px;
    mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
    -webkit-mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
  }
  .ow-hub__hero-inner{
    max-width:1100px;margin:0 auto;
    position:relative;z-index:2;text-align:center;
  }
  .ow-hub__eyebrow{
    display:inline-flex;align-items:center;gap:12px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.24em;text-transform:uppercase;
    color:var(--lava-glow);
    padding:9px 18px;
    border:1px solid rgba(255,87,34,.4);
    border-radius:999px;
    background:rgba(255,87,34,.08);
    margin-bottom:28px;
  }
  .ow-hub__eyebrow::before{
    content:"";width:8px;height:8px;border-radius:50%;
    background:var(--lava);
    box-shadow:0 0 12px var(--lava-glow);
  }
  .ow-hub__title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(48px,7vw,108px);
    line-height:1;letter-spacing:-.025em;
    font-weight:400;text-transform:uppercase;
    margin:0 0 18px;
    background:linear-gradient(180deg,#fff 0%,#fff 45%,var(--lava-glow) 100%);
    -webkit-background-clip:text;background-clip:text;
    -webkit-text-fill-color:transparent;
  }
  .ow-hub__tag{
    font-size:clamp(16px,1.7vw,19px);
    color:var(--fg);font-weight:400;line-height:1.4;
    margin:0 0 14px;
  }
  .ow-hub__desc{
    font-size:15px;color:var(--dim);line-height:1.6;
    margin:0 auto 24px;max-width:620px;
  }
  .ow-hub__loc{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-hub__loc strong{color:var(--lava-glow);font-weight:600;}

  /* ===== OUTLET GRID ===== */
  .ow-hub__main{
    padding:80px 40px 100px;
    background:var(--bg);
  }
  .ow-hub__main-inner{max-width:1300px;margin:0 auto;}
  .ow-hub__section-head{
    display:flex;align-items:baseline;justify-content:space-between;
    margin-bottom:48px;padding-bottom:24px;
    border-bottom:1px solid var(--line);
    gap:24px;flex-wrap:wrap;
  }
  .ow-hub__section-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:32px;line-height:1;font-weight:400;
    text-transform:uppercase;margin:0;color:#fff;
  }
  .ow-hub__section-count{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.18em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-hub__section-count strong{color:var(--lava-glow);font-weight:700;}

  .ow-hub__grid{
    display:grid;grid-template-columns:repeat(3,1fr);gap:24px;
  }

  /* Each card carries its outlet accent via --accent vars */
  .ow-hub__card{
    background:var(--bg-2);
    border:1px solid var(--line);
    border-radius:20px;
    overflow:hidden;
    transition:transform .35s ease, border-color .25s ease, box-shadow .35s ease;
    display:flex;flex-direction:column;
    position:relative;
  }
  .ow-hub__card::before{
    content:"";position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(to right,transparent,var(--accent),transparent);
    opacity:.6;z-index:1;
  }
  .ow-hub__card:hover{
    transform:translateY(-6px);
    border-color:var(--accent);
    box-shadow:0 20px 60px -20px var(--accent);
  }
  .ow-hub__card-top{
    padding:28px 26px 0;
  }
  .ow-hub__card-brand{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--accent-glow);
    margin-bottom:10px;
    display:flex;align-items:center;gap:8px;
  }
  .ow-hub__card-brand::before{
    content:"";width:7px;height:7px;border-radius:50%;
    background:var(--accent);
    box-shadow:0 0 10px var(--accent-glow);
  }
  .ow-hub__card-name{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:26px;line-height:1.1;font-weight:400;
    text-transform:uppercase;color:#fff;
    margin:0 0 6px;letter-spacing:-.005em;
  }
  .ow-hub__card-addr{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.08em;
    color:var(--dim);line-height:1.5;
    margin:0 0 16px;
  }
  .ow-hub__card-body{
    padding:0 26px 26px;
    flex:1;display:flex;flex-direction:column;
  }
  .ow-hub__card-blurb{
    font-size:13.5px;line-height:1.55;color:var(--dim);
    margin:0 0 16px;
  }
  .ow-hub__card-acts{
    display:flex;flex-wrap:wrap;gap:8px;
    margin:0 0 18px;padding:0;list-style:none;
  }
  .ow-hub__card-acts li{
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.1em;text-transform:uppercase;
    color:var(--fg);
    padding:6px 12px;border-radius:999px;
    border:1px solid var(--line);
    background:rgba(255,255,255,.03);
  }
  .ow-hub__card-count{
    display:flex;align-items:baseline;gap:8px;
    padding:14px 0;
    border-top:1px solid var(--line);
    margin-top:auto;margin-bottom:18px;
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-hub__card-count strong{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:26px;line-height:1;color:var(--accent-glow);
    font-weight:400;letter-spacing:0;
  }
  .ow-hub__card-ctas{display:flex;gap:8px;}
  .ow-hub__card-btn{
    flex:1;
    display:inline-flex;align-items:center;justify-content:center;gap:8px;
    padding:12px 18px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    text-decoration:none;font-weight:700;
    transition:transform .25s ease, gap .25s ease;
  }
  .ow-hub__card-btn--primary{
    background:var(--accent);color:#0a0a14;
  }
  .ow-hub__card-btn--primary:hover{transform:translateY(-2px);gap:12px;}
  .ow-hub__card-btn--ghost{
    background:rgba(255,255,255,.04);color:#fff;
    border:1px solid var(--line);
    flex:0 0 auto;
  }
  .ow-hub__card-btn--ghost:hover{
    background:rgba(255,255,255,.08);
    border-color:var(--accent);
    transform:translateY(-2px);
  }

  /* ===== ENQUIRY CTA ===== */
  .ow-hub__enquiry{
    background:linear-gradient(180deg,var(--bg) 0%,var(--bg-2) 100%);
    padding:80px 40px 100px;
    border-top:1px solid var(--line);
  }
  .ow-hub__enquiry-inner{
    max-width:900px;margin:0 auto;
    padding:48px 40px;
    background:radial-gradient(ellipse at center,rgba(255,87,34,.15) 0%,var(--bg-2) 70%);
    border:1px solid rgba(255,87,34,.3);
    border-radius:24px;
    text-align:center;
  }
  .ow-hub__enquiry-eyebrow{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--lava-glow);margin-bottom:14px;
  }
  .ow-hub__enquiry-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(28px,3.5vw,42px);
    line-height:1.05;text-transform:uppercase;font-weight:400;
    margin:0 0 14px;color:#fff;
  }
  .ow-hub__enquiry-sub{
    font-size:15px;color:var(--dim);line-height:1.55;
    margin:0 auto 28px;max-width:540px;
  }
  .ow-hub__enquiry-buttons{
    display:flex;gap:12px;justify-content:center;flex-wrap:wrap;
  }
  .ow-hub__enquiry-btn{
    display:inline-flex;align-items:center;gap:10px;
    padding:14px 24px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.14em;text-transform:uppercase;
    text-decoration:none;font-weight:700;
    transition:transform .25s ease, gap .25s ease;
  }
  .ow-hub__enquiry-btn--primary{
    background:var(--lava);color:#0a0a14;
    box-shadow:0 12px 30px -10px var(--lava);
  }
  .ow-hub__enquiry-btn--primary:hover{transform:translateY(-2px);gap:14px;}
  .ow-hub__enquiry-btn--ghost{
    background:rgba(255,255,255,.04);color:#fff;
    border:1px solid var(--line);
  }
  .ow-hub__enquiry-btn--ghost:hover{
    background:rgba(255,255,255,.08);
    border-color:var(--lava);
    transform:translateY(-2px);gap:14px;
  }

  /* Responsive */
  @media (max-width:1000px){
    .ow-hub__hero{padding:90px 28px 60px;}
    .ow-hub__main{padding:60px 28px 80px;}
    .ow-hub__grid{grid-template-columns:1fr;gap:18px;max-width:560px;margin:0 auto;}
  }
  @media (max-width:680px){
    .ow-hub__hero{padding:70px 18px 50px;}
    .ow-hub__main{padding:50px 18px 70px;}
    .ow-hub__title{font-size:54px;}
    .ow-hub__section-title{font-size:26px;}
    .ow-hub__enquiry-inner{padding:36px 24px;}
    .ow-hub__enquiry-buttons{flex-direction:column;}
    .ow-hub__enquiry-btn{justify-content:center;width:100%;}
  }
</style>

<section class="ow-hub">

  <!-- HERO -->
  <div class="ow-hub__hero">
    <div class="ow-hub__hero-grid"></div>
    <div class="ow-hub__hero-inner">
      <div class="ow-hub__eyebrow"><?php echo esc_html( $event_type['eyebrow'] ); ?></div>
      <h1 class="ow-hub__title"><?php echo esc_html( $event_type['title'] ); ?></h1>
      <p class="ow-hub__tag"><?php echo esc_html( $event_type['tagline'] ); ?></p>
      <p class="ow-hub__desc"><?php echo esc_html( $event_type['description'] ); ?></p>
      <div class="ow-hub__loc"><strong>3 Outlets</strong> · Kallang · Orchard · Funan</div>
    </div>
  </div>

  <!-- OUTLET CARDS -->
  <div class="ow-hub__main">
    <div class="ow-hub__main-inner">
      <div class="ow-hub__section-head">
        <h2 class="ow-hub__section-title">Choose Your Outlet</h2>
        <div class="ow-hub__section-count"><strong>3</strong> Locations Across Singapore</div>
      </div>

      <div class="ow-hub__grid">
        <?php foreach ( $outlets as $o ) : ?>
          <article class="ow-hub__card" style="--accent:<?php echo esc_attr( $o['accent'] ); ?>;--accent-glow:<?php echo esc_attr( $o['accent_glow'] ); ?>;">
            <div class="ow-hub__card-top">
              <div class="ow-hub__card-brand"><?php echo esc_html( $o['brand'] ); ?></div>
              <h3 class="ow-hub__card-name"><?php echo esc_html( $o['name'] ); ?></h3>
              <p class="ow-hub__card-addr"><?php echo esc_html( $o['address'] ); ?></p>
            </div>
            <div class="ow-hub__card-body">
              <p class="ow-hub__card-blurb"><?php echo esc_html( $o['blurb'] ); ?></p>
              <ul class="ow-hub__card-acts">
                <?php foreach ( $o['activities'] as $act ) : ?>
                  <li><?php echo esc_html( $act ); ?></li>
                <?php endforeach; ?>
              </ul>
              <?php if ( $o['package_count'] > 0 ) : ?>
                <div class="ow-hub__card-count">
                  <strong><?php echo (int) $o['package_count']; ?></strong>
                  <?php echo esc_html( $event_type['label'] ); ?> Package<?php echo $o['package_count'] === 1 ? '' : 's'; ?>
                </div>
              <?php endif; ?>
              <div class="ow-hub__card-ctas">
                <a class="ow-hub__card-btn ow-hub__card-btn--primary" href="<?php echo esc_url( home_url( '/' . $type_slug . '/' . $o['slug'] . '/' ) ); ?>">
                  View Packages →
                </a>
                <?php if ( $o['whatsapp'] ) : ?>
                  <a class="ow-hub__card-btn ow-hub__card-btn--ghost" href="<?php echo esc_url( $o['whatsapp'] ); ?>" target="_blank" rel="noopener" aria-label="WhatsApp <?php echo esc_attr( $o['short_name'] ); ?>">
                    WhatsApp
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- ENQUIRY CTA -->
  <div class="ow-hub__enquiry">
    <div class="ow-hub__enquiry-inner">
      <div class="ow-hub__enquiry-eyebrow">Not Sure Which Outlet?</div>
      <h3 class="ow-hub__enquiry-title">We'll Point You To The Right One</h3>
      <p class="ow-hub__enquiry-sub">
        Tell us your group size, preferred date, and the vibe you're after — we'll recommend the outlet and package that fit best.
      </p>
      <div class="ow-hub__enquiry-buttons">
        <a class="ow-hub__enquiry-btn ow-hub__enquiry-btn--primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
          Contact Us →
        </a>
        <a class="ow-hub__enquiry-btn ow-hub__enquiry-btn--ghost" href="<?php echo esc_url( home_url( '/' . $event_type['other_slug'] . '/' ) ); ?>">
          <?php echo esc_html( $event_type['other_label'] ); ?>
        </a>
      </div>
    </div>
  </div>

</section>

<?php get_footer(); ?>

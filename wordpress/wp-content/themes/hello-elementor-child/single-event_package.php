<?php
/**
 * Single template for the Events CPT (event_package) — /events/[package-slug]/
 *
 * INSTALL: Upload to your child theme:
 *   /wp-content/themes/hello-elementor-child/single-event_package.php
 *
 * Renders a package detail page from the ACF "Event Package Details" fields:
 *   event_type, event_outlet, event_tagline, event_price_from,
 *   event_group_size, event_duration, event_pdf
 * plus the featured image. Without this template WordPress fell back to the
 * parent theme's generic single view, which showed nothing (packages have no
 * post content — everything lives in ACF).
 *
 * Accent colors follow the event listing pages (page-event-listing.php):
 * Kallang = Orange, Orchard = Cyan, Funan = Purple.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

global $post;

$event_type_slug = get_post_meta( $post->ID, 'event_type', true );
$outlet_slug     = get_post_meta( $post->ID, 'event_outlet', true );

// ===== EVENT TYPE CONFIG (mirrors page-event-listing.php) =====
$event_type_config = array(
    'team-building' => array(
        'label'     => 'Team Building',
        'blurb'     => 'Bring your team out of the office and into the action — an event they\'ll still be talking about next sprint.',
        'cta_label' => 'Book This Package',
    ),
    'birthday-party' => array(
        'label'     => 'Birthday Party',
        'blurb'     => 'Loud, active, and full of bragging rights — a birthday they\'ll actually remember.',
        'cta_label' => 'Book This Package',
    ),
);
$event_type = isset( $event_type_config[ $event_type_slug ] ) ? $event_type_config[ $event_type_slug ] : $event_type_config['team-building'];
if ( ! isset( $event_type_config[ $event_type_slug ] ) ) $event_type_slug = 'team-building';

// ===== OUTLET CONFIG (mirrors page-event-listing.php) =====
$outlet_config = array(
    'kallang-wave-mall' => array(
        'name'        => 'Kallang Wave Mall',
        'short_name'  => 'Kallang',
        'address'     => '1 Stadium Place #01-63/64, Singapore 397628',
        'phone'       => '+65 6513 0561',
        'whatsapp'    => 'https://wa.me/+6596101682',
        'email'       => 'support@overworldvr.com',
        'accent'      => '#ff5722',
        'accent_glow' => '#ff8a3d',
        'accent_dim'  => 'rgba(255,87,34,',
        'bg_gradient' => 'radial-gradient(ellipse at 50% 110%,#1a0a05 0%,#0d0608 50%,#0a0606 100%)',
    ),
    'orchard-central' => array(
        'name'        => 'Orchard Central',
        'short_name'  => 'Orchard',
        'address'     => '181 Orchard Road, #05-30/K1/K3, Singapore 238896',
        'phone'       => '+65 8801 4303',
        'whatsapp'    => 'https://wa.me/message/WJ7MGRFFVGHAF1',
        'email'       => 'ocsupport@overworld.com.sg',
        'accent'      => '#22e3ff',
        'accent_glow' => '#5ff0ff',
        'accent_dim'  => 'rgba(34,227,255,',
        'bg_gradient' => 'radial-gradient(ellipse at 50% 110%,#001a2e 0%,#02050d 50%,#040810 100%)',
    ),
    'funan' => array(
        'name'        => 'Funan',
        'short_name'  => 'Funan',
        'address'     => '107 North Bridge Road, #04-14 & K1, Funan, Singapore 179105',
        'phone'       => '+65 8915 0061',
        'whatsapp'    => 'https://wa.me/6589140061',
        'email'       => 'funansupport@overworld.com.sg',
        'accent'      => '#a855f7',
        'accent_glow' => '#c89aff',
        'accent_dim'  => 'rgba(168,85,247,',
        'bg_gradient' => 'radial-gradient(ellipse at 50% 110%,#1f0a3a 0%,#10081e 50%,#0a081a 100%)',
    ),
);
$outlet = isset( $outlet_config[ $outlet_slug ] ) ? $outlet_config[ $outlet_slug ] : $outlet_config['kallang-wave-mall'];
if ( ! isset( $outlet_config[ $outlet_slug ] ) ) $outlet_slug = 'kallang-wave-mall';

// ===== PACKAGE FIELDS =====
$tagline    = get_post_meta( $post->ID, 'event_tagline', true );
$price_from = get_post_meta( $post->ID, 'event_price_from', true );
$duration   = get_post_meta( $post->ID, 'event_duration', true );
$group_size = get_post_meta( $post->ID, 'event_group_size', true );
$pdf        = get_post_meta( $post->ID, 'event_pdf', true );
$pdf_url    = $pdf ? ( is_numeric( $pdf ) ? wp_get_attachment_url( $pdf ) : $pdf ) : '';
$img        = get_the_post_thumbnail_url( $post->ID, 'full' );
$content    = trim( apply_filters( 'the_content', $post->post_content ) );

$listing_url = home_url( '/' . $event_type_slug . '/' . $outlet_slug . '/' );

// Other packages of the same type at the same outlet
$related = get_posts( array(
    'post_type'      => 'event_package',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
    'post__not_in'   => array( $post->ID ),
    'meta_query'     => array(
        'relation' => 'AND',
        array( 'key' => 'event_type',   'value' => $event_type_slug ),
        array( 'key' => 'event_outlet', 'value' => $outlet_slug ),
        array(
            'relation' => 'OR',
            array( 'key' => 'event_active', 'value' => '1' ),
            array( 'key' => 'event_active', 'compare' => 'NOT EXISTS' ),
        ),
    ),
    'meta_key'       => 'event_display_order',
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
));
?>

<style>
  .ow-pkg{
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
  .ow-pkg *{box-sizing:border-box;}

  /* ===== HERO ===== */
  .ow-pkg__hero{
    position:relative;
    background: <?php echo $outlet['bg_gradient']; ?>;
    padding:110px 40px 70px;
    overflow:hidden;
  }
  .ow-pkg__hero::before{
    content:"";position:absolute;left:0;right:0;bottom:0;height:80%;
    background:radial-gradient(ellipse at center,
      <?php echo $outlet['accent_dim']; ?>.2) 0%,
      transparent 70%);
    filter:blur(60px);pointer-events:none;
  }
  .ow-pkg__hero-grid{
    position:absolute;inset:0;pointer-events:none;
    background-image:
      linear-gradient(<?php echo $outlet['accent_dim']; ?>.05) 1px,transparent 1px),
      linear-gradient(90deg,<?php echo $outlet['accent_dim']; ?>.05) 1px,transparent 1px);
    background-size:60px 60px;
    mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
    -webkit-mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
  }
  .ow-pkg__hero-inner{
    max-width:1100px;margin:0 auto;
    position:relative;z-index:2;text-align:center;
  }
  .ow-pkg__back{
    display:inline-flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.16em;text-transform:uppercase;
    color:var(--dim);text-decoration:none;
    margin-bottom:26px;
    transition:color .2s ease;
  }
  .ow-pkg__back:hover{color:var(--accent-glow);}
  .ow-pkg__eyebrow{
    display:inline-flex;align-items:center;gap:12px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.24em;text-transform:uppercase;
    color:var(--accent-glow);
    padding:9px 18px;
    border:1px solid <?php echo $outlet['accent_dim']; ?>.4);
    border-radius:999px;
    background:<?php echo $outlet['accent_dim']; ?>.08);
    margin-bottom:24px;
  }
  .ow-pkg__eyebrow::before{
    content:"";width:8px;height:8px;border-radius:50%;
    background:var(--accent);
    box-shadow:0 0 12px var(--accent-glow);
  }
  .ow-pkg__title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(40px,6vw,84px);
    line-height:1;letter-spacing:-.02em;
    font-weight:400;text-transform:uppercase;
    margin:0 0 18px;
    background:linear-gradient(180deg,#fff 0%,#fff 45%,var(--accent-glow) 100%);
    -webkit-background-clip:text;background-clip:text;
    -webkit-text-fill-color:transparent;
  }
  .ow-pkg__tagline{
    font-size:clamp(15px,1.6vw,18px);
    color:var(--fg);font-weight:400;line-height:1.5;
    margin:0 auto 10px;max-width:640px;
  }
  .ow-pkg__loc{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-pkg__loc strong{color:var(--accent-glow);font-weight:600;}

  /* ===== MAIN: image + details ===== */
  .ow-pkg__main{
    padding:70px 40px 100px;
    background:var(--bg);
  }
  .ow-pkg__main-inner{
    max-width:1200px;margin:0 auto;
    display:grid;grid-template-columns:1.3fr 1fr;gap:40px;
    align-items:start;
  }
  /* Package posters carry text — never crop them */
  .ow-pkg__media{
    border-radius:24px;overflow:hidden;
    border:1px solid var(--line);
    background:var(--bg-2);
  }
  .ow-pkg__media img{
    width:100% !important;height:auto !important;
    display:block !important;
    max-width:100% !important;
  }
  .ow-pkg__media-empty{
    width:100%;aspect-ratio:4/3;
    display:flex;align-items:center;justify-content:center;
    font-size:48px;opacity:.4;color:var(--dim);
    background:radial-gradient(ellipse at center,<?php echo $outlet['accent_dim']; ?>.1) 0%,transparent 60%);
  }

  /* Details card */
  .ow-pkg__card{
    background:var(--bg-2);
    border:1px solid var(--line);
    border-radius:24px;
    padding:34px 32px 32px;
    position:relative;overflow:hidden;
  }
  .ow-pkg__card::before{
    content:"";position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(to right,transparent,var(--accent),transparent);
    opacity:.6;
  }
  .ow-pkg__card-head{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--accent-glow);margin-bottom:20px;
    display:flex;align-items:center;gap:10px;
  }
  .ow-pkg__card-head::before{
    content:"";width:24px;height:1px;background:var(--accent);
  }
  .ow-pkg__price{
    display:flex;align-items:baseline;gap:10px;flex-wrap:wrap;
    padding-bottom:22px;margin-bottom:22px;
    border-bottom:1px solid var(--line);
  }
  .ow-pkg__price-from{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.16em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-pkg__price-num{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:40px;line-height:1;color:#fff;font-weight:400;
    letter-spacing:-.01em;
  }
  .ow-pkg__price-unit{
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-pkg__specs{
    display:flex;flex-direction:column;gap:14px;
    margin-bottom:26px;
  }
  .ow-pkg__spec{
    display:flex;align-items:center;gap:14px;
  }
  .ow-pkg__spec-icon{
    width:40px;height:40px;border-radius:12px;flex-shrink:0;
    background:<?php echo $outlet['accent_dim']; ?>.12);
    border:1px solid <?php echo $outlet['accent_dim']; ?>.35);
    display:flex;align-items:center;justify-content:center;
    font-size:18px;line-height:1;
  }
  .ow-pkg__spec-label{
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.16em;text-transform:uppercase;
    color:var(--dim);display:block;margin-bottom:2px;
  }
  .ow-pkg__spec-value{
    font-size:15px;color:#fff;font-weight:600;
  }
  .ow-pkg__ctas{
    display:flex;flex-direction:column;gap:10px;
  }
  .ow-pkg__btn{
    display:inline-flex;align-items:center;justify-content:center;gap:10px;
    padding:15px 24px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.14em;text-transform:uppercase;
    text-decoration:none;font-weight:700;
    transition:transform .25s ease, gap .25s ease;
  }
  .ow-pkg__btn--primary{
    background:var(--accent);color:#0a0a14;
    box-shadow:0 12px 30px -10px var(--accent);
  }
  .ow-pkg__btn--primary:hover{transform:translateY(-2px);gap:14px;}
  .ow-pkg__btn--ghost{
    background:rgba(255,255,255,.04);color:#fff;
    border:1px solid var(--line);
  }
  .ow-pkg__btn--ghost:hover{
    background:rgba(255,255,255,.08);
    border-color:var(--accent);
    transform:translateY(-2px);gap:14px;
  }
  .ow-pkg__note{
    margin:20px 0 0;
    font-size:12.5px;line-height:1.55;color:var(--dim);
    text-align:center;
  }

  /* Optional long description */
  .ow-pkg__content{
    grid-column:1 / -1;
    font-size:15px;line-height:1.7;color:var(--dim);
    max-width:820px;
  }
  .ow-pkg__content h2,.ow-pkg__content h3{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-weight:400;text-transform:uppercase;color:#fff;
    letter-spacing:-.01em;
  }
  .ow-pkg__content a{color:var(--accent-glow);}

  /* ===== RELATED PACKAGES ===== */
  .ow-pkg__related{
    background:var(--bg-2);
    padding:70px 40px 100px;
    border-top:1px solid var(--line);
  }
  .ow-pkg__related-inner{
    max-width:1200px;margin:0 auto;
  }
  .ow-pkg__related-head{
    display:flex;align-items:baseline;justify-content:space-between;
    margin-bottom:36px;padding-bottom:20px;
    border-bottom:1px solid var(--line);
    gap:24px;flex-wrap:wrap;
  }
  .ow-pkg__related-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(24px,3vw,34px);line-height:1;font-weight:400;
    text-transform:uppercase;margin:0;color:#fff;
  }
  .ow-pkg__related-all{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.16em;text-transform:uppercase;
    color:var(--accent-glow);text-decoration:none;white-space:nowrap;
  }
  .ow-pkg__related-all:hover{text-decoration:underline;}
  .ow-pkg__related-grid{
    display:grid;grid-template-columns:repeat(3,1fr);gap:22px;
  }
  .ow-pkg__rel-card{
    display:flex;flex-direction:column;
    background:var(--bg);
    border:1px solid var(--line);
    border-radius:20px;
    overflow:hidden;text-decoration:none;
    transition:transform .35s ease, border-color .25s ease, box-shadow .35s ease;
  }
  .ow-pkg__rel-card:hover{
    transform:translateY(-6px);
    border-color:var(--accent);
    box-shadow:0 20px 60px -20px var(--accent);
  }
  .ow-pkg__rel-img{
    position:relative;aspect-ratio:16/10;
    background:rgba(255,255,255,.02);overflow:hidden;
  }
  .ow-pkg__rel-img img{
    width:100% !important;height:100% !important;
    object-fit:cover !important;display:block !important;
    position:absolute !important;top:0 !important;left:0 !important;
    max-width:none !important;max-height:none !important;
  }
  .ow-pkg__rel-img-empty{
    width:100%;height:100%;
    display:flex;align-items:center;justify-content:center;
    font-size:36px;opacity:.4;color:var(--dim);
  }
  .ow-pkg__rel-body{padding:20px 22px 22px;}
  .ow-pkg__rel-name{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:20px;line-height:1.15;font-weight:400;
    text-transform:uppercase;color:#fff;margin:0 0 8px;
  }
  .ow-pkg__rel-price{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-pkg__rel-price strong{color:var(--accent-glow);font-weight:700;}

  /* Responsive */
  @media (max-width:1000px){
    .ow-pkg__hero{padding:90px 28px 55px;}
    .ow-pkg__main{padding:55px 28px 80px;}
    .ow-pkg__main-inner{grid-template-columns:1fr;gap:28px;}
    .ow-pkg__related{padding:55px 28px 80px;}
    .ow-pkg__related-grid{grid-template-columns:repeat(2,1fr);gap:18px;}
  }
  @media (max-width:640px){
    .ow-pkg__hero{padding:70px 18px 45px;}
    .ow-pkg__main{padding:45px 18px 70px;}
    .ow-pkg__title{font-size:44px;}
    .ow-pkg__card{padding:28px 24px 26px;}
    .ow-pkg__related{padding:45px 18px 70px;}
    .ow-pkg__related-grid{grid-template-columns:1fr;}
  }
</style>

<section class="ow-pkg">

  <!-- ===== HERO ===== -->
  <div class="ow-pkg__hero">
    <div class="ow-pkg__hero-grid" aria-hidden="true"></div>
    <div class="ow-pkg__hero-inner">
      <a class="ow-pkg__back" href="<?php echo esc_url( $listing_url ); ?>">← All <?php echo esc_html( $event_type['label'] ); ?> Packages</a>
      <div>
        <div class="ow-pkg__eyebrow"><?php echo esc_html( $event_type['label'] ); ?> · <?php echo esc_html( $outlet['short_name'] ); ?></div>
      </div>
      <h1 class="ow-pkg__title"><?php echo esc_html( $post->post_title ); ?></h1>
      <?php if ( $tagline ) : ?>
        <p class="ow-pkg__tagline"><?php echo esc_html( $tagline ); ?></p>
      <?php endif; ?>
      <p class="ow-pkg__loc">📍 At <strong><?php echo esc_html( $outlet['name'] ); ?></strong></p>
    </div>
  </div>

  <!-- ===== MAIN: image + booking card ===== -->
  <div class="ow-pkg__main">
    <div class="ow-pkg__main-inner">

      <div class="ow-pkg__media">
        <?php if ( $img ) : ?>
          <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $post->post_title ); ?>" />
        <?php else : ?>
          <div class="ow-pkg__media-empty">🎉</div>
        <?php endif; ?>
      </div>

      <div class="ow-pkg__card">
        <div class="ow-pkg__card-head">Package Details</div>

        <?php if ( $price_from ) : ?>
          <div class="ow-pkg__price">
            <span class="ow-pkg__price-from">From</span>
            <span class="ow-pkg__price-num"><?php echo esc_html( $price_from ); ?></span>
            <span class="ow-pkg__price-unit"><?php echo ( stripos( $price_from, 'pax' ) === false ) ? 'per pax · SGD' : 'SGD'; ?></span>
          </div>
        <?php endif; ?>

        <div class="ow-pkg__specs">
          <?php if ( $duration ) : ?>
            <div class="ow-pkg__spec">
              <div class="ow-pkg__spec-icon">⏱</div>
              <div>
                <span class="ow-pkg__spec-label">Duration</span>
                <span class="ow-pkg__spec-value"><?php echo esc_html( $duration ); ?></span>
              </div>
            </div>
          <?php endif; ?>
          <?php if ( $group_size ) : ?>
            <div class="ow-pkg__spec">
              <div class="ow-pkg__spec-icon">👥</div>
              <div>
                <span class="ow-pkg__spec-label">Group Size</span>
                <span class="ow-pkg__spec-value"><?php echo esc_html( $group_size ); ?></span>
              </div>
            </div>
          <?php endif; ?>
          <div class="ow-pkg__spec">
            <div class="ow-pkg__spec-icon">📍</div>
            <div>
              <span class="ow-pkg__spec-label">Location</span>
              <span class="ow-pkg__spec-value"><?php echo esc_html( $outlet['name'] ); ?></span>
            </div>
          </div>
        </div>

        <div class="ow-pkg__ctas">
          <?php if ( $outlet['whatsapp'] ) : ?>
            <a class="ow-pkg__btn ow-pkg__btn--primary" href="<?php echo esc_url( $outlet['whatsapp'] ); ?>" target="_blank" rel="noopener">
              WhatsApp to Book →
            </a>
          <?php endif; ?>
          <?php if ( $pdf_url ) : ?>
            <a class="ow-pkg__btn ow-pkg__btn--ghost" href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener">
              📄 Download Brochure
            </a>
          <?php endif; ?>
          <?php if ( $outlet['email'] ) : ?>
            <a class="ow-pkg__btn ow-pkg__btn--ghost" href="mailto:<?php echo esc_attr( $outlet['email'] ); ?>?subject=<?php echo esc_attr( $event_type['label'] . ' enquiry - ' . $post->post_title ); ?>">
              Email Us
            </a>
          <?php endif; ?>
          <?php if ( $outlet['phone'] ) : ?>
            <a class="ow-pkg__btn ow-pkg__btn--ghost" href="tel:<?php echo esc_attr( str_replace( ' ', '', $outlet['phone'] ) ); ?>">
              Call <?php echo esc_html( $outlet['phone'] ); ?>
            </a>
          <?php endif; ?>
        </div>

        <p class="ow-pkg__note"><?php echo esc_html( $event_type['blurb'] ); ?></p>
      </div>

      <?php if ( $content ) : ?>
        <div class="ow-pkg__content"><?php echo $content; ?></div>
      <?php endif; ?>

    </div>
  </div>

  <!-- ===== RELATED PACKAGES ===== -->
  <?php if ( ! empty( $related ) ) : ?>
  <div class="ow-pkg__related">
    <div class="ow-pkg__related-inner">

      <div class="ow-pkg__related-head">
        <h2 class="ow-pkg__related-title">More <?php echo esc_html( $event_type['label'] ); ?> Packages at <?php echo esc_html( $outlet['short_name'] ); ?></h2>
        <a class="ow-pkg__related-all" href="<?php echo esc_url( $listing_url ); ?>">View All →</a>
      </div>

      <div class="ow-pkg__related-grid">
        <?php foreach ( $related as $rel ) :
          $rel_img   = get_the_post_thumbnail_url( $rel->ID, 'large' );
          $rel_price = get_post_meta( $rel->ID, 'event_price_from', true );
        ?>
          <a class="ow-pkg__rel-card" href="<?php echo esc_url( get_permalink( $rel->ID ) ); ?>">
            <div class="ow-pkg__rel-img">
              <?php if ( $rel_img ) : ?>
                <img src="<?php echo esc_url( $rel_img ); ?>" alt="<?php echo esc_attr( $rel->post_title ); ?>" loading="lazy" />
              <?php else : ?>
                <div class="ow-pkg__rel-img-empty">🎉</div>
              <?php endif; ?>
            </div>
            <div class="ow-pkg__rel-body">
              <h3 class="ow-pkg__rel-name"><?php echo esc_html( $rel->post_title ); ?></h3>
              <?php if ( $rel_price ) : ?>
                <div class="ow-pkg__rel-price">From <strong><?php echo esc_html( $rel_price ); ?></strong></div>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
  <?php endif; ?>

</section>

<?php get_footer(); ?>

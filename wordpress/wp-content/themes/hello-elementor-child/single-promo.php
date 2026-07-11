<?php
/**
 * Template — single-promo.php
 *
 * Displays a single promo with full details: large poster, full description,
 * outlets, validity, and CTA.
 *
 * INSTALLATION:
 * =============
 * Save this file to your child theme:
 *   /wp-content/themes/hello-elementor-child/single-promo.php
 *
 * @package OverworldChild
 */

get_header();
?>

<style>
  /* ============================================================
     OVERWORLD :: SINGLE PROMO
     ============================================================ */
  .ow-promo{
    --ow-bg:#0a0a0f;
    --ow-bg-2:#141419;
    --ow-lava:#ff5722;
    --ow-cyan:#22e3ff;
    --ow-fg:#fff;
    --ow-dim:rgba(255,255,255,.55);
    --ow-line:rgba(255,255,255,.1);
    --ow-line-strong:rgba(255,255,255,.2);
    background:var(--ow-bg);
    color:var(--ow-fg);
    font-family:'Space Grotesk','Inter',system-ui,sans-serif;
    min-height:100vh;
    padding:60px 40px 100px;
  }
  .ow-promo *{box-sizing:border-box;}
  .ow-promo__inner{max-width:1200px;margin:0 auto;}

  /* ====== Breadcrumb ====== */
  .ow-promo__crumb{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.18em;text-transform:uppercase;
    color:var(--ow-dim);
    margin-bottom:40px;
    display:flex;align-items:center;gap:10px;flex-wrap:wrap;
  }
  .ow-promo__crumb a{color:var(--ow-dim);text-decoration:none;transition:color .2s;}
  .ow-promo__crumb a:hover{color:var(--ow-lava);}
  .ow-promo__crumb-sep{color:rgba(255,255,255,.2);}
  .ow-promo__crumb-current{color:var(--ow-fg);}

  /* ====== Layout ====== */
  .ow-promo__layout{
    display:grid;
    grid-template-columns:minmax(0,1fr) minmax(0,1.1fr);
    gap:60px;
    align-items:start;
  }

  /* Left: poster (sticky on desktop) */
  .ow-promo__poster-wrap{
    position:sticky;top:40px;
  }
  .ow-promo__poster{
    border:1px solid var(--ow-line);
    border-radius:20px;
    overflow:hidden;
    background:#0a0a0f;
    aspect-ratio:1/1;
  }
  .ow-promo__poster img{
    width:100%;height:100%;
    object-fit:cover;display:block;
  }
  .ow-promo__poster--empty{
    display:flex;align-items:center;justify-content:center;
    background:repeating-linear-gradient(45deg,#1c1c24,#1c1c24 8px,#22222c 8px,#22222c 16px);
    color:rgba(255,255,255,.25);
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.15em;text-transform:uppercase;
  }

  /* Right: body */
  .ow-promo__body{
    display:flex;flex-direction:column;gap:24px;
  }

  .ow-promo__meta-row{
    display:flex;align-items:center;gap:14px;
    flex-wrap:wrap;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.18em;text-transform:uppercase;
  }
  .ow-promo__date{color:var(--ow-lava);}
  .ow-promo__valid{
    color:var(--ow-dim);
    display:inline-flex;align-items:center;gap:6px;
  }
  .ow-promo__valid::before{
    content:"";width:4px;height:4px;border-radius:50%;
    background:var(--ow-dim);
  }
  .ow-promo__valid--expiring{color:#f5c84b;}
  .ow-promo__valid--expiring::before{background:#f5c84b;}

  .ow-promo__title{
    font-size:clamp(36px,4.5vw,56px);
    line-height:1;letter-spacing:-.025em;
    font-weight:700;margin:0;
    text-transform:uppercase;
  }

  .ow-promo__tagline{
    font-size:18px;color:var(--ow-cyan);
    font-weight:500;line-height:1.4;
    margin:0;letter-spacing:.005em;
  }

  .ow-promo__outlets-section{
    display:flex;flex-direction:column;gap:10px;
  }
  .ow-promo__label{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.18em;text-transform:uppercase;
    color:var(--ow-dim);
  }
  .ow-promo__outlets{
    display:flex;flex-wrap:wrap;gap:8px;
  }
  .ow-promo__outlet{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    padding:8px 14px;border:1px solid var(--ow-line-strong);
    border-radius:999px;color:rgba(255,255,255,.85);
  }
  .ow-promo__outlet--all{
    border-color:var(--ow-lava);color:var(--ow-lava);
  }

  /* ====== Description (WYSIWYG content) ====== */
  .ow-promo__desc{
    border-top:1px solid var(--ow-line);
    padding-top:28px;margin-top:8px;
    font-size:16px;line-height:1.7;color:rgba(255,255,255,.85);
  }
  .ow-promo__desc h2,
  .ow-promo__desc h3,
  .ow-promo__desc h4{
    color:var(--ow-fg);font-weight:700;
    text-transform:uppercase;letter-spacing:-.01em;
    margin:32px 0 14px;line-height:1.2;
  }
  .ow-promo__desc h2{font-size:24px;}
  .ow-promo__desc h3{font-size:20px;}
  .ow-promo__desc h4{font-size:17px;}
  .ow-promo__desc p{margin:0 0 16px;}
  .ow-promo__desc ul,
  .ow-promo__desc ol{margin:0 0 16px;padding-left:22px;}
  .ow-promo__desc li{margin-bottom:8px;}
  .ow-promo__desc strong{color:var(--ow-fg);}
  .ow-promo__desc a{color:var(--ow-lava);text-decoration:underline;}
  .ow-promo__desc a:hover{color:var(--ow-cyan);}
  .ow-promo__desc img{max-width:100%;height:auto;border-radius:8px;margin:16px 0;}

  /* ====== CTA section ====== */
  .ow-promo__cta-section{
    margin-top:32px;padding-top:28px;
    border-top:1px solid var(--ow-line);
    display:flex;flex-direction:column;gap:16px;
  }
  .ow-promo__cta{
    font-family:'JetBrains Mono',monospace;
    font-size:13px;letter-spacing:.14em;text-transform:uppercase;
    padding:18px 28px;border-radius:999px;
    background:var(--ow-lava);color:#0a0a0f;font-weight:700;
    text-decoration:none;display:inline-flex;align-items:center;gap:10px;
    border:1px solid var(--ow-lava);
    transition:transform .2s ease, gap .25s ease, box-shadow .35s ease;
    align-self:flex-start;
    box-shadow:0 0 0 1px rgba(255,138,61,.4),0 16px 50px -16px var(--ow-lava);
  }
  .ow-promo__cta:hover{transform:translateY(-2px);gap:14px;}

  /* ====== Back link ====== */
  .ow-promo__back{
    margin-top:60px;
    display:inline-flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',monospace;
    font-size:11.5px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--ow-dim);text-decoration:none;
    padding:12px 18px;border:1px solid var(--ow-line);
    border-radius:999px;transition:.2s;
  }
  .ow-promo__back:hover{
    color:var(--ow-fg);border-color:var(--ow-lava);gap:12px;
  }

  /* ====== Responsive ====== */
  @media (max-width:980px){
    .ow-promo{padding:50px 28px 80px;}
    .ow-promo__layout{grid-template-columns:1fr;gap:40px;}
    .ow-promo__poster-wrap{position:static;}
    .ow-promo__poster{aspect-ratio:4/5;max-width:480px;margin:0 auto;}
  }
  @media (max-width:560px){
    .ow-promo{padding:40px 18px 70px;}
    .ow-promo__crumb{font-size:10px;letter-spacing:.14em;margin-bottom:30px;}
    .ow-promo__poster{aspect-ratio:1/1;}
    .ow-promo__title{font-size:32px;}
    .ow-promo__tagline{font-size:16px;}
    .ow-promo__desc{font-size:15px;}
  }
</style>

<?php while ( have_posts() ) : the_post();
  // Pull ACF fields.
  $tagline      = function_exists( 'get_field' ) ? get_field( 'promo_tagline' )      : '';
  $description  = function_exists( 'get_field' ) ? get_field( 'promo_description' )  : '';
  $valid_until  = function_exists( 'get_field' ) ? get_field( 'promo_valid_until' )  : '';
  $outlets      = function_exists( 'get_field' ) ? get_field( 'promo_outlets' )      : array();
  $cta_label    = function_exists( 'get_field' ) ? get_field( 'promo_cta_label' )    : '';
  $cta_url      = function_exists( 'get_field' ) ? get_field( 'promo_cta_url' )      : '';
  $poster       = function_exists( 'get_field' ) ? get_field( 'promo_poster' )       : null;

  // Poster fallback to Featured Image.
  $poster_url = '';
  $poster_alt = get_the_title();
  if ( is_array( $poster ) && ! empty( $poster['url'] ) ) {
      $poster_url = $poster['url'];
      if ( ! empty( $poster['alt'] ) ) $poster_alt = $poster['alt'];
  } elseif ( has_post_thumbnail() ) {
      $poster_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
  }

  // Format valid-until date.
  $valid_label = '';
  $valid_class = '';
  if ( $valid_until ) {
      $end_ts = strtotime( $valid_until );
      if ( $end_ts ) {
          $days_left = floor( ( $end_ts - time() ) / DAY_IN_SECONDS );
          $valid_label = 'Valid until ' . date_i18n( 'd M Y', $end_ts );
          if ( $days_left <= 7 && $days_left >= 0 ) {
              $valid_class = 'ow-promo__valid--expiring';
              $valid_label .= ' · ' . ( $days_left === 0 ? 'Last day!' : $days_left . ' days left' );
          }
      }
  }

  // Description fallback to post content if ACF field empty.
  if ( ! $description ) {
      $description = apply_filters( 'the_content', get_the_content() );
  }
?>

<section class="ow-promo">
  <div class="ow-promo__inner">

    <nav class="ow-promo__crumb">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
      <span class="ow-promo__crumb-sep">/</span>
      <a href="<?php echo esc_url( get_post_type_archive_link( 'promo' ) ); ?>">Promos</a>
      <span class="ow-promo__crumb-sep">/</span>
      <span class="ow-promo__crumb-current"><?php echo esc_html( get_the_title() ); ?></span>
    </nav>

    <div class="ow-promo__layout">

      <div class="ow-promo__poster-wrap">
        <?php if ( $poster_url ) : ?>
          <div class="ow-promo__poster">
            <img src="<?php echo esc_url( $poster_url ); ?>" alt="<?php echo esc_attr( $poster_alt ); ?>" />
          </div>
        <?php else : ?>
          <div class="ow-promo__poster ow-promo__poster--empty">[ Add Poster Image ]</div>
        <?php endif; ?>
      </div>

      <div class="ow-promo__body">

        <div class="ow-promo__meta-row">
          <span class="ow-promo__date"><?php echo esc_html( get_the_date( 'd M Y' ) ); ?></span>
          <?php if ( $valid_label ) : ?>
            <span class="ow-promo__valid <?php echo esc_attr( $valid_class ); ?>"><?php echo esc_html( $valid_label ); ?></span>
          <?php endif; ?>
        </div>

        <h1 class="ow-promo__title"><?php the_title(); ?></h1>

        <?php if ( $tagline ) : ?>
          <p class="ow-promo__tagline"><?php echo esc_html( $tagline ); ?></p>
        <?php endif; ?>

        <?php if ( function_exists( 'ow_promo_timer_html' ) && ( $promo_timer = ow_promo_timer_html( get_the_ID() ) ) ) : ?>
          <div class="ow-promo__countdown">
            <div class="ow-promo__countdown-label">Offer ends in</div>
            <?php echo $promo_timer; ?>
          </div>
          <style>
            .ow-promo__countdown{margin:22px 0 4px;}
            .ow-promo__countdown-label{
              font-family:'JetBrains Mono',monospace;
              font-size:11px;letter-spacing:.2em;text-transform:uppercase;
              color:var(--ow-lava);margin-bottom:10px;
            }
            .ow-promo__countdown .ow-promo-timer{display:flex;gap:10px;font-variant-numeric:tabular-nums;}
            .ow-promo__countdown .unit{
              min-width:60px;padding:12px 8px;text-align:center;
              border:1px solid rgba(255,255,255,.12);border-radius:12px;
              background:rgba(255,255,255,.04);
            }
            .ow-promo__countdown .unit .v{font-size:24px;font-weight:700;line-height:1;letter-spacing:-.02em;}
            .ow-promo__countdown .unit .u{
              font-family:'JetBrains Mono',monospace;
              font-size:9.5px;letter-spacing:.18em;text-transform:uppercase;
              color:rgba(255,255,255,.5);margin-top:5px;
            }
          </style>
        <?php endif; ?>

        <?php if ( ! empty( $outlets ) && is_array( $outlets ) ) : ?>
          <div class="ow-promo__outlets-section">
            <span class="ow-promo__label">Available at</span>
            <div class="ow-promo__outlets">
              <?php foreach ( $outlets as $outlet ) :
                $is_all = ( strtolower( $outlet ) === 'all' || strtolower( $outlet ) === 'all outlets' );
                $cls = $is_all ? 'ow-promo__outlet ow-promo__outlet--all' : 'ow-promo__outlet';
              ?>
                <span class="<?php echo esc_attr( $cls ); ?>"><?php echo esc_html( $outlet ); ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if ( $description ) : ?>
          <div class="ow-promo__desc">
            <?php echo wp_kses_post( $description ); ?>
          </div>
        <?php endif; ?>

        <?php if ( $cta_url && $cta_label ) : ?>
          <div class="ow-promo__cta-section">
            <a class="ow-promo__cta" href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener">
              <?php echo esc_html( $cta_label ); ?> →
            </a>
          </div>
        <?php endif; ?>

      </div>

    </div>

    <a class="ow-promo__back" href="<?php echo esc_url( get_post_type_archive_link( 'promo' ) ); ?>">
      ← All Promos
    </a>

  </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>

<?php
/**
 * Template Name: Event Rental (Interactive Game Rental for Events)
 *
 * Service page for bringing Overworld's games to the client's own venue:
 *   /event-rental/
 *
 * Every word and picture on this page is client-editable through the
 * "Event Rental Page Content" box in WP Admin (mu-plugin
 * overworld-event-rental.php). Each field falls back to that plugin's
 * built-in copy, so an untouched page still looks finished.
 *
 * Sections, top to bottom:
 *   hero → intro → activity cards → what's included → suitable for → FAQ → CTA
 *
 * USAGE:
 *   Page Attributes → Template → "Event Rental (Interactive Game Rental for
 *   Events)". Created by scripts/event-rental-page.php.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$page_id = get_the_ID();

// ===== Client-editable copy (mu-plugin overworld-event-rental.php) =====
// The template also runs without the plugin — every helper is guarded and
// the page then renders a plain version of the built-in copy — but the plugin
// is where the real defaults and the ACF box live.
$rental_get = function ( $name ) use ( $page_id ) {
	return function_exists( 'ow_rental_val' ) ? ow_rental_val( $page_id, $name ) : '';
};
$rental_url = function ( $url ) {
	return function_exists( 'ow_rental_url' ) ? ow_rental_url( $url ) : $url;
};
$rental_icon = function ( $key ) {
	return function_exists( 'ow_rental_icon' ) ? ow_rental_icon( $key ) : '';
};

$rental_h1            = $rental_get( 'h1' );
$rental_h1_accent     = $rental_get( 'h1_accent' );
$rental_tagline       = $rental_get( 'tagline' );
$rental_primary_label = $rental_get( 'primary_label' );
$rental_primary_url   = $rental_url( $rental_get( 'primary_url' ) );
$rental_ghost_label   = $rental_get( 'ghost_label' );
$rental_ghost_url     = $rental_url( $rental_get( 'ghost_url' ) );

$rental_hero_image_id = function_exists( 'ow_rental_hero_image_id' ) ? ow_rental_hero_image_id( $page_id ) : 0;

$rental_intro_title = $rental_get( 'intro_title' );
$rental_intro_text  = $rental_get( 'intro_text' );

$rental_activities_title     = $rental_get( 'activities_title' );
$rental_activities_all_label = $rental_get( 'activities_all_label' );
$rental_activities_all_url   = $rental_url( $rental_get( 'activities_all_url' ) );
$rental_activity_link_label  = $rental_get( 'activity_link_label' );
$rental_coming_soon_label    = $rental_get( 'coming_soon_label' );
$rental_activities           = function_exists( 'ow_rental_activities' ) ? ow_rental_activities( $page_id ) : array();
$rental_placeholders         = function_exists( 'ow_rental_show_placeholders' ) ? ow_rental_show_placeholders( $page_id ) : true;
$rental_slots                = defined( 'OW_RENTAL_ACTIVITY_SLOTS' ) ? OW_RENTAL_ACTIVITY_SLOTS : 6;

// Pad to a full grid of placeholders, but never leave a lone row of them:
// three real cards → three placeholders; four real → two; six → none.
$rental_placeholder_count = 0;
if ( $rental_placeholders && count( $rental_activities ) < $rental_slots ) {
	$rental_placeholder_count = $rental_slots - count( $rental_activities );
}

$rental_defaults = function_exists( 'ow_rental_defaults' ) ? ow_rental_defaults() : array();

$rental_included_title = $rental_get( 'included_title' );
$rental_included       = function_exists( 'ow_rental_icon_list' )
	? ow_rental_icon_list( $page_id, 'inc', defined( 'OW_RENTAL_INCLUDED_SLOTS' ) ? OW_RENTAL_INCLUDED_SLOTS : 6, isset( $rental_defaults['included'] ) ? $rental_defaults['included'] : array() )
	: array();

$rental_audience_title = $rental_get( 'audience_title' );
$rental_audience       = function_exists( 'ow_rental_icon_list' )
	? ow_rental_icon_list( $page_id, 'aud', defined( 'OW_RENTAL_AUDIENCE_SLOTS' ) ? OW_RENTAL_AUDIENCE_SLOTS : 6, isset( $rental_defaults['audience'] ) ? $rental_defaults['audience'] : array() )
	: array();

$rental_faq_title     = $rental_get( 'faq_title' );
$rental_faq_all_label = $rental_get( 'faq_all_label' );
$rental_faq_all_url   = $rental_url( $rental_get( 'faq_all_url' ) );
$rental_faqs          = function_exists( 'ow_rental_faqs' ) ? ow_rental_faqs( $page_id ) : array();

$rental_cta_title = $rental_get( 'cta_title' );
$rental_cta_text  = $rental_get( 'cta_text' );
$rental_cta_label = $rental_get( 'cta_label' );
$rental_cta_url   = $rental_url( $rental_get( 'cta_url' ) );

// Arrow used on every button, so the SVG is written once.
$rental_arrow = '<svg class="ow-rental__arrow" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10h13M11 5l5 5-5 5"/></svg>';
?>

<style>
  .ow-rental{
    --lava:#ff5722;
    --lava-glow:#ff8a3d;
    --bg:#0a0a14;
    --bg-2:#13131f;
    --bg-3:#1a1a28;
    --fg:#fff;
    --dim:rgba(220,225,240,.68);
    --line:rgba(255,255,255,.08);
    --line-strong:rgba(255,255,255,.16);
    --font-display:'Anton','Bebas Neue',sans-serif;
    --font-body:'Space Grotesk','Inter',system-ui,sans-serif;
    --font-mono:'JetBrains Mono',monospace;
    background:var(--bg);
    color:var(--fg);
    font-family:var(--font-body);
    overflow-x:hidden;
  }
  .ow-rental *{box-sizing:border-box;}
  .ow-rental a{text-decoration:none;}

  .ow-rental__wrap{max-width:1200px;margin:0 auto;}
  .ow-rental__section{padding:72px 40px;}
  .ow-rental__section--tight{padding-top:56px;padding-bottom:56px;}
  .ow-rental__section + .ow-rental__section{border-top:1px solid var(--line);}

  .ow-rental__h2{
    font-family:var(--font-display);
    font-size:clamp(28px,3.4vw,40px);
    line-height:1;letter-spacing:.005em;
    font-weight:400;text-transform:uppercase;
    margin:0;color:var(--fg);
  }
  .ow-rental__head{
    display:flex;align-items:baseline;justify-content:space-between;
    gap:20px;flex-wrap:wrap;margin-bottom:28px;
  }
  .ow-rental__more{
    display:inline-flex;align-items:center;gap:8px;
    font-family:var(--font-mono);
    font-size:11px;letter-spacing:.16em;text-transform:uppercase;
    font-weight:700;color:var(--lava-glow);
    transition:gap .25s ease,color .2s ease;
  }
  .ow-rental__more:hover{gap:12px;color:#fff;}
  .ow-rental__arrow{width:16px;height:16px;flex:0 0 auto;}

  /* ===== Buttons ===== */
  .ow-rental__btn{
    display:inline-flex;align-items:center;justify-content:center;gap:10px;
    padding:14px 24px;border-radius:999px;
    font-family:var(--font-mono);
    font-size:12px;letter-spacing:.14em;text-transform:uppercase;
    font-weight:700;line-height:1;
    border:1px solid transparent;
    transition:transform .25s ease,gap .25s ease,background .2s ease,border-color .2s ease;
  }
  .ow-rental__btn:hover{transform:translateY(-2px);gap:14px;}
  .ow-rental__btn--primary{
    background:var(--lava);color:#0a0a14;
    box-shadow:0 12px 30px -12px var(--lava);
  }
  .ow-rental__btn--ghost{
    background:rgba(255,255,255,.04);color:#fff;
    border-color:var(--line-strong);
  }
  .ow-rental__btn--ghost:hover{border-color:var(--lava);background:rgba(255,255,255,.08);}
  .ow-rental__btn--outline{
    background:transparent;color:var(--lava-glow);
    border-color:rgba(255,87,34,.55);
    padding:12px 20px;font-size:11px;
  }
  .ow-rental__btn--outline:hover{background:rgba(255,87,34,.1);border-color:var(--lava);}
  .ow-rental__btn--dark{
    background:#0a0a14;color:#fff;
    box-shadow:0 14px 34px -14px rgba(0,0,0,.8);
  }

  /* ===== HERO ===== */
  .ow-rental__hero{
    position:relative;overflow:hidden;
    background:radial-gradient(ellipse at 20% 100%,#1a0a05 0%,#0d0608 45%,var(--bg) 100%);
    min-height:520px;
    display:flex;align-items:center;
  }
  .ow-rental__hero-grid{
    position:absolute;inset:0;pointer-events:none;
    background-image:
      linear-gradient(rgba(255,87,34,.05) 1px,transparent 1px),
      linear-gradient(90deg,rgba(255,87,34,.05) 1px,transparent 1px);
    background-size:60px 60px;
    mask-image:radial-gradient(ellipse at 30% 50%,black 0%,transparent 70%);
    -webkit-mask-image:radial-gradient(ellipse at 30% 50%,black 0%,transparent 70%);
  }
  .ow-rental__hero-media{
    position:absolute;top:0;right:0;bottom:0;width:58%;
    pointer-events:none;
  }
  .ow-rental__hero-media img{
    width:100%;height:100%;object-fit:cover;object-position:center;
    display:block;
  }
  .ow-rental__hero-media::after{
    content:"";position:absolute;inset:0;
    background:
      linear-gradient(90deg,var(--bg) 0%,rgba(10,10,20,.85) 18%,rgba(10,10,20,.15) 55%,rgba(10,10,20,0) 100%),
      linear-gradient(180deg,rgba(10,10,20,.35) 0%,rgba(10,10,20,0) 30%,rgba(10,10,20,0) 70%,var(--bg) 100%);
  }
  .ow-rental__hero-media--empty{
    background:
      radial-gradient(ellipse at 70% 40%,rgba(255,87,34,.35) 0%,transparent 55%),
      linear-gradient(135deg,var(--bg-3) 0%,var(--bg) 100%);
  }
  .ow-rental__hero-media--empty::before{
    content:"";position:absolute;inset:0;
    background-image:
      linear-gradient(rgba(255,87,34,.12) 1px,transparent 1px),
      linear-gradient(90deg,rgba(255,87,34,.12) 1px,transparent 1px);
    background-size:40px 40px;
    mask-image:radial-gradient(ellipse at 65% 45%,black 0%,transparent 65%);
    -webkit-mask-image:radial-gradient(ellipse at 65% 45%,black 0%,transparent 65%);
  }
  .ow-rental__hero-inner{
    position:relative;z-index:2;
    width:100%;max-width:1200px;margin:0 auto;
    padding:96px 40px 88px;
  }
  .ow-rental__hero-copy{max-width:640px;}
  .ow-rental__h1{
    font-family:var(--font-display);
    font-size:clamp(42px,5.4vw,70px);
    line-height:.98;letter-spacing:.005em;
    font-weight:400;text-transform:uppercase;
    margin:0 0 20px;color:#fff;
  }
  .ow-rental__h1 span{
    display:block;color:var(--lava-glow);
    text-shadow:0 0 40px rgba(255,87,34,.35);
  }
  .ow-rental__tagline{
    font-size:clamp(16px,1.6vw,19px);line-height:1.5;
    color:var(--fg);margin:0 0 30px;max-width:460px;
  }
  .ow-rental__hero-ctas{display:flex;gap:12px;flex-wrap:wrap;}

  /* ===== INTRO ===== */
  .ow-rental__intro{text-align:center;}
  .ow-rental__intro .ow-rental__h2{margin-bottom:14px;}
  .ow-rental__intro p{
    font-size:clamp(15px,1.4vw,17px);line-height:1.65;color:var(--dim);
    margin:0 auto;max-width:640px;
  }

  /* ===== ACTIVITIES ===== */
  .ow-rental__grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;}
  .ow-rental__card{
    background:var(--bg-2);border:1px solid var(--line);border-radius:18px;
    overflow:hidden;display:flex;flex-direction:column;
    transition:transform .35s ease,border-color .25s ease,box-shadow .35s ease;
  }
  .ow-rental__card:hover{
    transform:translateY(-6px);border-color:var(--lava);
    box-shadow:0 24px 60px -24px var(--lava);
  }
  .ow-rental__card-media{
    position:relative;aspect-ratio:16/10;overflow:hidden;
    background:linear-gradient(135deg,var(--bg-3),var(--bg));
  }
  .ow-rental__card-media img{
    width:100%;height:100%;object-fit:cover;display:block;
    transition:transform .6s ease;
  }
  .ow-rental__card:hover .ow-rental__card-media img{transform:scale(1.05);}
  .ow-rental__card-media--empty{display:flex;align-items:center;justify-content:center;}
  .ow-rental__card-media--empty .ow-rental__icon{width:48px;height:48px;color:rgba(255,87,34,.6);}
  .ow-rental__card-body{padding:22px 22px 24px;display:flex;flex-direction:column;flex:1;}
  .ow-rental__card-title{
    font-family:var(--font-display);
    font-size:24px;line-height:1.05;font-weight:400;
    text-transform:uppercase;margin:0 0 8px;color:#fff;
  }
  .ow-rental__card-text{font-size:14px;line-height:1.55;color:var(--dim);margin:0 0 18px;}
  .ow-rental__card-body .ow-rental__btn{margin-top:auto;align-self:flex-start;}

  .ow-rental__card--soon{
    border-style:dashed;border-color:var(--line-strong);
    background:rgba(255,255,255,.015);
    align-items:center;justify-content:center;
    min-height:220px;text-align:center;padding:32px 20px;
  }
  .ow-rental__card--soon:hover{transform:none;border-color:var(--line-strong);box-shadow:none;}
  .ow-rental__card--soon .ow-rental__icon{width:52px;height:52px;color:rgba(255,255,255,.28);margin-bottom:16px;}
  .ow-rental__card--soon span{
    font-family:var(--font-display);
    font-size:20px;line-height:1.15;text-transform:uppercase;
    color:rgba(255,255,255,.38);max-width:200px;
  }

  /* ===== WHAT'S INCLUDED ===== */
  .ow-rental__included{
    display:flex;flex-wrap:wrap;
    border:1px solid var(--line);border-radius:18px;
    background:var(--bg-2);overflow:hidden;
    margin-top:28px;
  }
  .ow-rental__included li{
    list-style:none;flex:1 1 0;min-width:150px;
    display:flex;flex-direction:column;align-items:center;gap:12px;
    padding:26px 16px;text-align:center;
    border-right:1px solid var(--line);
  }
  .ow-rental__included li:last-child{border-right:0;}
  .ow-rental__included .ow-rental__icon{width:36px;height:36px;color:var(--lava-glow);}
  .ow-rental__included span{font-size:14px;font-weight:500;color:var(--fg);line-height:1.3;}

  /* ===== SUITABLE FOR ===== */
  .ow-rental__audience{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;
    margin:28px 0 0;padding:0;list-style:none;
  }
  .ow-rental__audience li{
    background:var(--bg-2);border:1px solid var(--line);border-radius:14px;
    padding:24px 16px;text-align:center;
    display:flex;flex-direction:column;align-items:center;gap:12px;
    transition:border-color .25s ease,transform .25s ease;
  }
  .ow-rental__audience li:hover{border-color:rgba(255,87,34,.5);transform:translateY(-3px);}
  .ow-rental__audience .ow-rental__icon{width:32px;height:32px;color:#fff;}
  .ow-rental__audience span{font-size:14px;font-weight:500;color:var(--fg);line-height:1.3;}

  /* ===== FAQ ===== */
  .ow-rental__faq-item{
    border:1px solid var(--line);border-radius:14px;
    background:var(--bg-2);margin-top:12px;overflow:hidden;
  }
  .ow-rental__faq-item[open]{border-color:rgba(255,87,34,.45);}
  .ow-rental__faq-q{
    cursor:pointer;list-style:none;
    padding:18px 56px 18px 22px;position:relative;
    font-size:15.5px;font-weight:600;line-height:1.45;color:#fff;
  }
  .ow-rental__faq-q::-webkit-details-marker{display:none;}
  .ow-rental__faq-q::after{
    content:"+";position:absolute;right:22px;top:50%;transform:translateY(-50%);
    font-family:var(--font-mono);font-size:20px;line-height:1;color:var(--lava-glow);
  }
  .ow-rental__faq-item[open] .ow-rental__faq-q::after{content:"–";}
  .ow-rental__faq-q:hover{color:var(--lava-glow);}
  .ow-rental__faq-a{padding:0 22px 20px;font-size:14.5px;line-height:1.7;color:var(--dim);}

  /* ===== CTA BAND ===== */
  .ow-rental__cta{
    background:
      linear-gradient(100deg,transparent 0%,transparent 62%,rgba(255,255,255,.12) 62.2%,rgba(255,255,255,.12) 74%,transparent 74.2%),
      linear-gradient(135deg,var(--lava-glow) 0%,var(--lava) 100%);
    color:#0a0a14;
    padding:56px 40px;
  }
  .ow-rental__cta-inner{
    max-width:1200px;margin:0 auto;
    display:flex;align-items:center;justify-content:space-between;
    gap:28px;flex-wrap:wrap;
  }
  .ow-rental__cta-title{
    font-family:var(--font-display);
    font-size:clamp(30px,3.6vw,44px);line-height:1;letter-spacing:.005em;
    font-weight:400;text-transform:uppercase;
    margin:0 0 10px;color:#0a0a14;
  }
  .ow-rental__cta-text{font-size:16px;line-height:1.5;margin:0;color:rgba(10,10,20,.82);max-width:560px;}

  /* ===== Responsive ===== */
  @media (max-width:1000px){
    .ow-rental__section{padding:56px 28px;}
    .ow-rental__hero-inner{padding:72px 28px 64px;}
    .ow-rental__hero-media{width:100%;opacity:.55;}
    .ow-rental__hero-media::after{
      background:
        linear-gradient(90deg,var(--bg) 0%,rgba(10,10,20,.7) 40%,rgba(10,10,20,.35) 100%),
        linear-gradient(180deg,rgba(10,10,20,.4) 0%,rgba(10,10,20,0) 40%,var(--bg) 100%);
    }
    .ow-rental__grid{grid-template-columns:repeat(2,1fr);gap:18px;}
    .ow-rental__included li{flex-basis:33.333%;border-bottom:1px solid var(--line);}
    .ow-rental__included li:nth-child(3n){border-right:0;}
    .ow-rental__cta{padding:48px 28px;}
  }
  @media (max-width:640px){
    .ow-rental__section{padding:48px 18px;}
    .ow-rental__hero{min-height:0;}
    .ow-rental__hero-inner{padding:56px 18px 52px;}
    .ow-rental__hero-ctas .ow-rental__btn{width:100%;}
    .ow-rental__grid{grid-template-columns:1fr;}
    .ow-rental__card--soon{min-height:150px;padding:24px 16px;}
    .ow-rental__included li{flex-basis:50%;}
    .ow-rental__included li:nth-child(3n){border-right:1px solid var(--line);}
    .ow-rental__included li:nth-child(2n){border-right:0;}
    .ow-rental__audience{grid-template-columns:repeat(2,1fr);}
    .ow-rental__cta{padding:44px 18px;}
    .ow-rental__cta-inner{flex-direction:column;align-items:flex-start;}
    .ow-rental__cta-inner .ow-rental__btn{width:100%;}
  }
</style>

<section class="ow-rental">

  <!-- HERO -->
  <div class="ow-rental__hero">
    <div class="ow-rental__hero-grid"></div>
    <?php if ( $rental_hero_image_id ) : ?>
      <div class="ow-rental__hero-media">
        <?php echo wp_get_attachment_image( $rental_hero_image_id, 'full', false, array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
      </div>
    <?php else : ?>
      <div class="ow-rental__hero-media ow-rental__hero-media--empty"></div>
    <?php endif; ?>
    <div class="ow-rental__hero-inner">
      <div class="ow-rental__hero-copy">
        <h1 class="ow-rental__h1">
          <?php echo esc_html( $rental_h1 ); ?>
          <?php if ( '' !== $rental_h1_accent ) : ?>
            <span><?php echo esc_html( $rental_h1_accent ); ?></span>
          <?php endif; ?>
        </h1>
        <?php if ( '' !== $rental_tagline ) : ?>
          <p class="ow-rental__tagline"><?php echo esc_html( $rental_tagline ); ?></p>
        <?php endif; ?>
        <div class="ow-rental__hero-ctas">
          <?php if ( '' !== $rental_primary_label && '' !== $rental_primary_url ) : ?>
            <a class="ow-rental__btn ow-rental__btn--primary" href="<?php echo esc_url( $rental_primary_url ); ?>">
              <?php echo esc_html( $rental_primary_label ); ?> <?php echo $rental_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>
          <?php endif; ?>
          <?php if ( '' !== $rental_ghost_label && '' !== $rental_ghost_url ) : ?>
            <a class="ow-rental__btn ow-rental__btn--ghost" href="<?php echo esc_url( $rental_ghost_url ); ?>">
              <?php echo esc_html( $rental_ghost_label ); ?>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- INTRO -->
  <?php if ( '' !== $rental_intro_title || '' !== $rental_intro_text ) : ?>
  <div class="ow-rental__section ow-rental__section--tight ow-rental__intro">
    <div class="ow-rental__wrap">
      <?php if ( '' !== $rental_intro_title ) : ?>
        <h2 class="ow-rental__h2"><?php echo esc_html( $rental_intro_title ); ?></h2>
      <?php endif; ?>
      <?php if ( '' !== $rental_intro_text ) : ?>
        <p><?php echo esc_html( $rental_intro_text ); ?></p>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- ACTIVITIES -->
  <div class="ow-rental__section" id="activities">
    <div class="ow-rental__wrap">
      <div class="ow-rental__head">
        <h2 class="ow-rental__h2"><?php echo esc_html( $rental_activities_title ); ?></h2>
        <?php if ( '' !== $rental_activities_all_url && '' !== $rental_activities_all_label ) : ?>
          <a class="ow-rental__more" href="<?php echo esc_url( $rental_activities_all_url ); ?>">
            <?php echo esc_html( $rental_activities_all_label ); ?> <?php echo $rental_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          </a>
        <?php endif; ?>
      </div>

      <div class="ow-rental__grid">
        <?php foreach ( $rental_activities as $act ) : ?>
          <article class="ow-rental__card">
            <?php if ( '' !== $act['image_url'] ) : ?>
              <div class="ow-rental__card-media">
                <?php if ( $act['image_id'] ) : ?>
                  <?php echo wp_get_attachment_image( $act['image_id'], 'large', false, array( 'loading' => 'lazy' ) ); ?>
                <?php else : ?>
                  <img src="<?php echo esc_url( $act['image_url'] ); ?>" alt="<?php echo esc_attr( $act['title'] ); ?>" loading="lazy" />
                <?php endif; ?>
              </div>
            <?php else : ?>
              <div class="ow-rental__card-media ow-rental__card-media--empty">
                <?php echo $rental_icon( 'gamepad' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
              </div>
            <?php endif; ?>
            <div class="ow-rental__card-body">
              <h3 class="ow-rental__card-title"><?php echo esc_html( $act['title'] ); ?></h3>
              <?php if ( '' !== $act['text'] ) : ?>
                <p class="ow-rental__card-text"><?php echo esc_html( $act['text'] ); ?></p>
              <?php endif; ?>
              <?php if ( '' !== $act['url'] && '' !== $rental_activity_link_label ) : ?>
                <a class="ow-rental__btn ow-rental__btn--outline" href="<?php echo esc_url( $act['url'] ); ?>">
                  <?php echo esc_html( $rental_activity_link_label ); ?> <?php echo $rental_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>

        <?php for ( $i = 0; $i < $rental_placeholder_count; $i++ ) : ?>
          <div class="ow-rental__card ow-rental__card--soon" aria-hidden="<?php echo $i > 0 ? 'true' : 'false'; ?>">
            <?php echo $rental_icon( 'gamepad' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <span><?php echo esc_html( $rental_coming_soon_label ); ?></span>
          </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>

  <!-- WHAT'S INCLUDED -->
  <?php if ( ! empty( $rental_included ) ) : ?>
  <div class="ow-rental__section">
    <div class="ow-rental__wrap">
      <h2 class="ow-rental__h2"><?php echo esc_html( $rental_included_title ); ?></h2>
      <ul class="ow-rental__included">
        <?php foreach ( $rental_included as $item ) : ?>
          <li>
            <?php echo $rental_icon( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <span><?php echo esc_html( $item['label'] ); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <?php endif; ?>

  <!-- SUITABLE FOR -->
  <?php if ( ! empty( $rental_audience ) ) : ?>
  <div class="ow-rental__section">
    <div class="ow-rental__wrap">
      <h2 class="ow-rental__h2"><?php echo esc_html( $rental_audience_title ); ?></h2>
      <ul class="ow-rental__audience">
        <?php foreach ( $rental_audience as $item ) : ?>
          <li>
            <?php echo $rental_icon( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <span><?php echo esc_html( $item['label'] ); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <?php endif; ?>

  <!-- FAQ -->
  <?php if ( ! empty( $rental_faqs ) ) : ?>
  <div class="ow-rental__section">
    <div class="ow-rental__wrap">
      <div class="ow-rental__head">
        <h2 class="ow-rental__h2"><?php echo esc_html( $rental_faq_title ); ?></h2>
        <?php if ( '' !== $rental_faq_all_url && '' !== $rental_faq_all_label ) : ?>
          <a class="ow-rental__more" href="<?php echo esc_url( $rental_faq_all_url ); ?>">
            <?php echo esc_html( $rental_faq_all_label ); ?> <?php echo $rental_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          </a>
        <?php endif; ?>
      </div>
      <?php foreach ( $rental_faqs as $faq ) : ?>
        <details class="ow-rental__faq-item">
          <summary class="ow-rental__faq-q"><?php echo esc_html( $faq['q'] ); ?></summary>
          <div class="ow-rental__faq-a"><?php echo nl2br( esc_html( $faq['a'] ) ); ?></div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- CTA BAND -->
  <?php if ( '' !== $rental_cta_title ) : ?>
  <div class="ow-rental__cta">
    <div class="ow-rental__cta-inner">
      <div>
        <h2 class="ow-rental__cta-title"><?php echo esc_html( $rental_cta_title ); ?></h2>
        <?php if ( '' !== $rental_cta_text ) : ?>
          <p class="ow-rental__cta-text"><?php echo esc_html( $rental_cta_text ); ?></p>
        <?php endif; ?>
      </div>
      <?php if ( '' !== $rental_cta_label && '' !== $rental_cta_url ) : ?>
        <a class="ow-rental__btn ow-rental__btn--dark" href="<?php echo esc_url( $rental_cta_url ); ?>">
          <?php echo esc_html( $rental_cta_label ); ?> <?php echo $rental_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

</section>

<?php get_footer(); ?>

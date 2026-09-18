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
 * Layout mirrors the team building / birthday party hubs (page-event-hub.php):
 * centred hero with eyebrow pill and gradient H1, section heads with a mono
 * counter, accent-lined cards with pill buttons, the "why" grid, a body copy
 * block, FAQ accordion and the boxed enquiry panel. Accent is the green from
 * the client's reference design rather than the hubs' lava orange.
 *
 * Sections, top to bottom:
 *   hero → activity cards → what's included → suitable for → intro copy → FAQ → CTA
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

$rental_eyebrow       = $rental_get( 'eyebrow' );
$rental_hero_line     = $rental_get( 'hero_line' );
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

$rental_cta_eyebrow     = $rental_get( 'cta_eyebrow' );
$rental_cta_ghost_label = $rental_get( 'cta_ghost_label' );
$rental_cta_ghost_url   = $rental_url( $rental_get( 'cta_ghost_url' ) );
$rental_cta_title = $rental_get( 'cta_title' );
$rental_cta_text  = $rental_get( 'cta_text' );
$rental_cta_label = $rental_get( 'cta_label' );
$rental_cta_url   = $rental_url( $rental_get( 'cta_url' ) );

// Arrow used on every button, so the SVG is written once.
$rental_arrow = '<svg class="ow-rental__arrow" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10h13M11 5l5 5-5 5"/></svg>';

$rental_activity_count = count( $rental_activities );
?>

<style>
  .ow-rental{
    --accent:#c3fb33;
    --accent-glow:#dcff6e;
    --bg:#0a0a14;
    --bg-2:#13131f;
    --fg:#fff;
    --dim:rgba(220,225,240,.65);
    --line:rgba(255,255,255,.08);
    background:var(--bg);
    color:var(--fg);
    font-family:'Space Grotesk','Inter',system-ui,sans-serif;
  }
  .ow-rental *{box-sizing:border-box;}
  .ow-rental a{text-decoration:none;}

  /* ===== HERO ===== */
  .ow-rental__hero{
    position:relative;
    background:radial-gradient(ellipse at 50% 110%,#111a05 0%,#0a0f08 50%,#0a0a14 100%);
    padding:120px 40px 80px;
    overflow:hidden;
  }
  .ow-rental__hero::before{
    content:"";position:absolute;left:0;right:0;bottom:0;height:80%;
    background:radial-gradient(ellipse at center,rgba(195,251,51,.18) 0%,transparent 70%);
    filter:blur(60px);pointer-events:none;
  }
  .ow-rental__hero-photo{
    position:absolute;inset:0;pointer-events:none;
  }
  .ow-rental__hero-photo img{
    width:100%;height:100%;object-fit:cover;object-position:center;display:block;
    opacity:.28;
  }
  .ow-rental__hero-photo::after{
    content:"";position:absolute;inset:0;
    background:
      linear-gradient(180deg,rgba(10,10,20,.55) 0%,rgba(10,10,20,.35) 50%,#0a0a14 100%),
      radial-gradient(ellipse at center,transparent 30%,rgba(10,10,20,.7) 100%);
  }
  .ow-rental__hero-grid{
    position:absolute;inset:0;pointer-events:none;
    background-image:
      linear-gradient(rgba(195,251,51,.05) 1px,transparent 1px),
      linear-gradient(90deg,rgba(195,251,51,.05) 1px,transparent 1px);
    background-size:60px 60px;
    mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
    -webkit-mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
  }
  .ow-rental__hero-inner{
    max-width:1100px;margin:0 auto;
    position:relative;z-index:2;text-align:center;
  }
  .ow-rental__eyebrow{
    display:inline-flex;align-items:center;gap:12px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.24em;text-transform:uppercase;
    color:var(--accent);
    padding:9px 18px;
    border:1px solid rgba(195,251,51,.4);
    border-radius:999px;
    background:rgba(195,251,51,.08);
    margin-bottom:28px;
  }
  .ow-rental__eyebrow::before{
    content:"";width:8px;height:8px;border-radius:50%;
    background:var(--accent);
    box-shadow:0 0 12px var(--accent-glow);
  }
  .ow-rental__title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(48px,7vw,108px);
    line-height:1;letter-spacing:-.025em;
    font-weight:400;text-transform:uppercase;
    margin:0 0 18px;
    background:linear-gradient(180deg,#fff 0%,#fff 45%,var(--accent) 100%);
    -webkit-background-clip:text;background-clip:text;
    -webkit-text-fill-color:transparent;
  }
  .ow-rental__title span{display:block;}
  .ow-rental__tag{
    font-size:clamp(16px,1.7vw,19px);
    color:var(--fg);font-weight:400;line-height:1.4;
    margin:0 0 14px;
  }
  .ow-rental__hero-ctas{
    display:flex;gap:12px;justify-content:center;flex-wrap:wrap;
    margin-bottom:28px;
  }
  .ow-rental__loc{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-rental__loc strong{color:var(--accent);font-weight:600;}

  /* ===== Buttons (hub pill style) ===== */
  .ow-rental__btn{
    display:inline-flex;align-items:center;justify-content:center;gap:10px;
    padding:14px 24px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.14em;text-transform:uppercase;
    text-decoration:none;font-weight:700;line-height:1;
    border:1px solid transparent;
    transition:transform .25s ease,gap .25s ease,background .2s ease,border-color .2s ease;
  }
  .ow-rental__btn:hover{transform:translateY(-2px);gap:14px;}
  .ow-rental__btn--primary{
    background:var(--accent);color:#0a0a14;
    box-shadow:0 12px 30px -10px var(--accent);
  }
  .ow-rental__btn--ghost{
    background:rgba(255,255,255,.04);color:#fff;
    border-color:var(--line);
  }
  .ow-rental__btn--ghost:hover{background:rgba(255,255,255,.08);border-color:var(--accent);}
  .ow-rental__arrow{width:16px;height:16px;flex:0 0 auto;}

  /* ===== Sections & heads ===== */
  .ow-rental__section{padding:80px 40px;border-top:1px solid var(--line);}
  .ow-rental__section--alt{background:var(--bg-2);}
  .ow-rental__inner{max-width:1300px;margin:0 auto;}
  .ow-rental__inner--narrow{max-width:860px;}
  .ow-rental__section-head{
    display:flex;align-items:baseline;justify-content:space-between;
    margin-bottom:48px;padding-bottom:24px;
    border-bottom:1px solid var(--line);
    gap:24px;flex-wrap:wrap;
  }
  .ow-rental__section-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:32px;line-height:1;font-weight:400;
    text-transform:uppercase;margin:0;color:#fff;
  }
  .ow-rental__section-count{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.18em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-rental__section-count strong{color:var(--accent);font-weight:700;}
  a.ow-rental__section-count{
    display:inline-flex;align-items:center;gap:8px;color:var(--accent);font-weight:700;
    transition:gap .25s ease,color .2s ease;
  }
  a.ow-rental__section-count:hover{gap:12px;color:#fff;}

  /* ===== ACTIVITY CARDS (hub outlet-card style) ===== */
  .ow-rental__grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
  .ow-rental__card{
    background:var(--bg-2);
    border:1px solid var(--line);
    border-radius:20px;
    overflow:hidden;
    transition:transform .35s ease,border-color .25s ease,box-shadow .35s ease;
    display:flex;flex-direction:column;
    position:relative;
  }
  .ow-rental__card::before{
    content:"";position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(to right,transparent,var(--accent),transparent);
    opacity:.6;z-index:1;
  }
  .ow-rental__card:hover{
    transform:translateY(-6px);
    border-color:var(--accent);
    box-shadow:0 20px 60px -20px var(--accent);
  }
  .ow-rental__card-media{
    position:relative;aspect-ratio:16/10;overflow:hidden;
    background:linear-gradient(135deg,#1a1a28,var(--bg));
  }
  .ow-rental__card-media img{
    width:100%;height:100%;object-fit:cover;display:block;
    transition:transform .6s ease;
  }
  .ow-rental__card:hover .ow-rental__card-media img{transform:scale(1.05);}
  .ow-rental__card-media--empty{display:flex;align-items:center;justify-content:center;}
  .ow-rental__card-media--empty .ow-rental__icon{width:48px;height:48px;color:rgba(195,251,51,.5);}
  .ow-rental__card-body{
    padding:24px 26px 26px;
    flex:1;display:flex;flex-direction:column;
  }
  .ow-rental__card-brand{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--accent);
    margin-bottom:10px;
    display:flex;align-items:center;gap:8px;
  }
  .ow-rental__card-brand::before{
    content:"";width:7px;height:7px;border-radius:50%;
    background:var(--accent);
    box-shadow:0 0 10px var(--accent-glow);
  }
  .ow-rental__card-name{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:26px;line-height:1.1;font-weight:400;
    text-transform:uppercase;color:#fff;
    margin:0 0 10px;letter-spacing:-.005em;
  }
  .ow-rental__card-blurb{
    font-size:13.5px;line-height:1.55;color:var(--dim);
    margin:0 0 20px;
  }
  .ow-rental__card-ctas{display:flex;gap:8px;margin-top:auto;}
  .ow-rental__card-ctas .ow-rental__btn{flex:1;padding:12px 18px;font-size:11px;}

  .ow-rental__card--soon{
    border-style:dashed;border-color:rgba(255,255,255,.16);
    background:rgba(255,255,255,.015);
    align-items:center;justify-content:center;
    min-height:240px;text-align:center;padding:32px 20px;
  }
  .ow-rental__card--soon::before{display:none;}
  .ow-rental__card--soon:hover{transform:none;border-color:rgba(255,255,255,.16);box-shadow:none;}
  .ow-rental__card--soon .ow-rental__icon{width:52px;height:52px;color:rgba(255,255,255,.28);margin-bottom:16px;}
  .ow-rental__card--soon span{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:20px;line-height:1.15;text-transform:uppercase;
    color:rgba(255,255,255,.38);max-width:200px;
  }

  /* ===== WHAT'S INCLUDED (icon strip, per the reference design) ===== */
  .ow-rental__included{
    display:flex;flex-wrap:wrap;
    border:1px solid var(--line);border-radius:18px;
    background:rgba(255,255,255,.02);overflow:hidden;
    margin:32px 0 0;padding:0;list-style:none;
  }
  .ow-rental__included li{
    flex:1 1 0;min-width:150px;
    display:flex;flex-direction:column;align-items:center;gap:14px;
    padding:30px 16px;text-align:center;
    border-right:1px solid var(--line);
  }
  .ow-rental__included li:last-child{border-right:0;}
  .ow-rental__included .ow-rental__icon{width:38px;height:38px;color:var(--accent);}
  .ow-rental__included span{font-size:14.5px;font-weight:500;color:var(--fg);line-height:1.3;}

  /* ===== SUITABLE FOR (row of boxes, per the reference design) ===== */
  .ow-rental__audience{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;
    margin:32px 0 0;padding:0;list-style:none;
  }
  .ow-rental__audience li{
    background:rgba(255,255,255,.02);border:1px solid var(--line);border-radius:14px;
    padding:26px 16px;text-align:center;
    display:flex;flex-direction:column;align-items:center;gap:14px;
    transition:border-color .25s ease,transform .25s ease;
  }
  .ow-rental__audience li:hover{border-color:rgba(195,251,51,.45);transform:translateY(-3px);}
  .ow-rental__audience .ow-rental__icon{width:32px;height:32px;color:#fff;}
  .ow-rental__audience span{font-size:14.5px;font-weight:500;color:var(--fg);line-height:1.3;}

  /* ===== BODY COPY ===== */
  .ow-rental__body-inner h2{margin-bottom:18px;}
  .ow-rental__body-inner p{
    font-size:16px;line-height:1.75;color:var(--dim);margin:0 0 18px;
  }
  .ow-rental__body-inner p:last-child{margin-bottom:0;}

  /* ===== FAQ ===== */
  .ow-rental__faq-item{
    border:1px solid var(--line);border-radius:14px;
    background:rgba(255,255,255,.02);
    margin-top:12px;overflow:hidden;
  }
  .ow-rental__faq-item[open]{border-color:rgba(195,251,51,.4);}
  .ow-rental__faq-q{
    cursor:pointer;list-style:none;
    padding:18px 52px 18px 22px;position:relative;
    font-size:15.5px;font-weight:600;line-height:1.45;color:#fff;
  }
  .ow-rental__faq-q::-webkit-details-marker{display:none;}
  .ow-rental__faq-q::after{
    content:"+";position:absolute;right:22px;top:50%;transform:translateY(-50%);
    font-family:'JetBrains Mono',monospace;font-size:19px;color:var(--accent);
  }
  .ow-rental__faq-item[open] .ow-rental__faq-q::after{content:"–";}
  .ow-rental__faq-q:hover{color:var(--accent);}
  .ow-rental__faq-a{
    padding:0 22px 20px;
    font-size:14.5px;line-height:1.7;color:var(--dim);
  }

  /* ===== ENQUIRY CTA (hub boxed panel) ===== */
  .ow-rental__enquiry{
    background:linear-gradient(180deg,var(--bg) 0%,var(--bg-2) 100%);
    padding:80px 40px 100px;
    border-top:1px solid var(--line);
  }
  .ow-rental__enquiry-inner{
    max-width:900px;margin:0 auto;
    padding:48px 40px;
    background:radial-gradient(ellipse at center,rgba(195,251,51,.13) 0%,var(--bg-2) 70%);
    border:1px solid rgba(195,251,51,.3);
    border-radius:24px;
    text-align:center;
  }
  .ow-rental__enquiry-eyebrow{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--accent);margin-bottom:14px;
  }
  .ow-rental__enquiry-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(28px,3.5vw,42px);
    line-height:1.05;text-transform:uppercase;font-weight:400;
    margin:0 0 14px;color:#fff;
  }
  .ow-rental__enquiry-sub{
    font-size:15px;color:var(--dim);line-height:1.55;
    margin:0 auto 28px;max-width:540px;
  }
  .ow-rental__enquiry-buttons{
    display:flex;gap:12px;justify-content:center;flex-wrap:wrap;
  }

  /* Responsive (same breakpoints as the hubs) */
  @media (max-width:1000px){
    .ow-rental__included li{flex-basis:33.333%;border-bottom:1px solid var(--line);}
    .ow-rental__included li:nth-child(3n){border-right:0;}
    .ow-rental__hero{padding:56px 28px 60px;}
    .ow-rental__section{padding:60px 28px;}
    .ow-rental__grid{grid-template-columns:1fr;gap:18px;max-width:560px;margin:0 auto;}
    .ow-rental__enquiry{padding:60px 28px 80px;}
  }
  @media (max-width:680px){
    .ow-rental__hero{padding:40px 18px 50px;}
    .ow-rental__title{font-size:54px;}
    .ow-rental__hero-ctas{flex-direction:column;}
    .ow-rental__hero-ctas .ow-rental__btn{width:100%;}
    .ow-rental__section{padding:45px 18px;}
    .ow-rental__section-title{font-size:26px;}
    .ow-rental__included li{flex-basis:50%;padding:22px 12px;}
    .ow-rental__included li:nth-child(3n){border-right:1px solid var(--line);}
    .ow-rental__included li:nth-child(2n){border-right:0;}
    .ow-rental__audience{grid-template-columns:repeat(2,1fr);gap:10px;}
    .ow-rental__audience li{padding:20px 12px;}
    .ow-rental__body-inner p{font-size:15px;}
    .ow-rental__enquiry{padding:45px 18px 60px;}
    .ow-rental__enquiry-inner{padding:36px 24px;}
    .ow-rental__enquiry-buttons{flex-direction:column;}
    .ow-rental__enquiry-buttons .ow-rental__btn{justify-content:center;width:100%;}
  }
</style>

<section class="ow-rental">

  <!-- HERO -->
  <div class="ow-rental__hero">
    <?php if ( $rental_hero_image_id ) : ?>
      <div class="ow-rental__hero-photo">
        <?php echo wp_get_attachment_image( $rental_hero_image_id, 'full', false, array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
      </div>
    <?php endif; ?>
    <div class="ow-rental__hero-grid"></div>
    <div class="ow-rental__hero-inner">
      <?php if ( '' !== $rental_eyebrow ) : ?>
        <div class="ow-rental__eyebrow"><?php echo esc_html( $rental_eyebrow ); ?></div>
      <?php endif; ?>
      <h1 class="ow-rental__title">
        <?php echo esc_html( $rental_h1 ); ?>
        <?php if ( '' !== $rental_h1_accent ) : ?>
          <span><?php echo esc_html( $rental_h1_accent ); ?></span>
        <?php endif; ?>
      </h1>
      <?php if ( '' !== $rental_tagline ) : ?>
        <p class="ow-rental__tag"><?php echo esc_html( $rental_tagline ); ?></p>
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
      <?php if ( '' !== $rental_hero_line ) :
        // "A · B · C" — the first item gets the accent, like "3 Outlets" on the hubs.
        $line_parts = array_map( 'trim', explode( '·', $rental_hero_line ) );
        $line_first = array_shift( $line_parts );
      ?>
        <div class="ow-rental__loc">
          <strong><?php echo esc_html( $line_first ); ?></strong>
          <?php foreach ( $line_parts as $part ) : if ( '' === $part ) continue; ?>
            &middot; <?php echo esc_html( $part ); ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ACTIVITY CARDS -->
  <div class="ow-rental__section" id="activities">
    <div class="ow-rental__inner">
      <div class="ow-rental__section-head">
        <h2 class="ow-rental__section-title"><?php echo esc_html( $rental_activities_title ); ?></h2>
        <?php if ( '' !== $rental_activities_all_url && '' !== $rental_activities_all_label ) : ?>
          <a class="ow-rental__section-count" href="<?php echo esc_url( $rental_activities_all_url ); ?>">
            <?php echo esc_html( $rental_activities_all_label ); ?> <?php echo $rental_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          </a>
        <?php else : ?>
          <div class="ow-rental__section-count">
            <strong><?php echo (int) $rental_activity_count; ?></strong>
            <?php echo 1 === $rental_activity_count ? 'Activity' : 'Activities'; ?> Available For Rental
            <?php if ( $rental_placeholder_count > 0 ) : ?> &middot; More Coming Soon<?php endif; ?>
          </div>
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
              <div class="ow-rental__card-brand">Event Rental</div>
              <h3 class="ow-rental__card-name"><?php echo esc_html( $act['title'] ); ?></h3>
              <?php if ( '' !== $act['text'] ) : ?>
                <p class="ow-rental__card-blurb"><?php echo esc_html( $act['text'] ); ?></p>
              <?php endif; ?>
              <div class="ow-rental__card-ctas">
                <?php if ( '' !== $act['url'] && '' !== $rental_activity_link_label ) : ?>
                  <a class="ow-rental__btn ow-rental__btn--primary" href="<?php echo esc_url( $act['url'] ); ?>">
                    <?php echo esc_html( $rental_activity_link_label ); ?> <?php echo $rental_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                  </a>
                <?php endif; ?>
                <?php if ( '' !== $rental_ghost_url ) : ?>
                  <a class="ow-rental__btn ow-rental__btn--ghost" href="<?php echo esc_url( $rental_ghost_url ); ?>" style="flex:0 0 auto;">
                    <?php echo esc_html( $rental_ghost_label ); ?>
                  </a>
                <?php endif; ?>
              </div>
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
  <div class="ow-rental__section ow-rental__section--alt">
    <div class="ow-rental__inner">
      <h2 class="ow-rental__section-title"><?php echo esc_html( $rental_included_title ); ?></h2>
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
    <div class="ow-rental__inner">
      <h2 class="ow-rental__section-title"><?php echo esc_html( $rental_audience_title ); ?></h2>
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

  <!-- BODY COPY (what search engines read) -->
  <?php if ( '' !== $rental_intro_title || '' !== $rental_intro_text ) : ?>
  <div class="ow-rental__section ow-rental__section--alt">
    <div class="ow-rental__inner ow-rental__inner--narrow ow-rental__body-inner">
      <?php if ( '' !== $rental_intro_title ) : ?>
        <h2 class="ow-rental__section-title"><?php echo esc_html( $rental_intro_title ); ?></h2>
      <?php endif; ?>
      <?php foreach ( preg_split( '/\n\s*\n/', $rental_intro_text ) as $paragraph ) :
        $paragraph = trim( $paragraph );
        if ( '' === $paragraph ) continue; ?>
        <p><?php echo nl2br( esc_html( $paragraph ) ); ?></p>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- FAQ -->
  <?php if ( ! empty( $rental_faqs ) ) : ?>
  <div class="ow-rental__section">
    <div class="ow-rental__inner ow-rental__inner--narrow">
      <div class="ow-rental__section-head">
        <h2 class="ow-rental__section-title"><?php echo esc_html( $rental_faq_title ); ?></h2>
        <?php if ( '' !== $rental_faq_all_url && '' !== $rental_faq_all_label ) : ?>
          <a class="ow-rental__section-count" href="<?php echo esc_url( $rental_faq_all_url ); ?>">
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

  <!-- ENQUIRY CTA -->
  <?php if ( '' !== $rental_cta_title ) : ?>
  <div class="ow-rental__enquiry">
    <div class="ow-rental__enquiry-inner">
      <?php if ( '' !== $rental_cta_eyebrow ) : ?>
        <div class="ow-rental__enquiry-eyebrow"><?php echo esc_html( $rental_cta_eyebrow ); ?></div>
      <?php endif; ?>
      <h3 class="ow-rental__enquiry-title"><?php echo esc_html( $rental_cta_title ); ?></h3>
      <?php if ( '' !== $rental_cta_text ) : ?>
        <p class="ow-rental__enquiry-sub"><?php echo esc_html( $rental_cta_text ); ?></p>
      <?php endif; ?>
      <div class="ow-rental__enquiry-buttons">
        <?php if ( '' !== $rental_cta_label && '' !== $rental_cta_url ) : ?>
          <a class="ow-rental__btn ow-rental__btn--primary" href="<?php echo esc_url( $rental_cta_url ); ?>">
            <?php echo esc_html( $rental_cta_label ); ?> <?php echo $rental_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          </a>
        <?php endif; ?>
        <?php if ( '' !== $rental_cta_ghost_label && '' !== $rental_cta_ghost_url ) : ?>
          <a class="ow-rental__btn ow-rental__btn--ghost" href="<?php echo esc_url( $rental_cta_ghost_url ); ?>">
            <?php echo esc_html( $rental_cta_ghost_label ); ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

</section>

<?php get_footer(); ?>

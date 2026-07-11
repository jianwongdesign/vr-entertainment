<?php
/**
 * Plugin Name: Overworld — Promo Countdown & Homepage Feature
 * Description: Adds a "Feature on homepage" toggle to Promos (only one can be featured — turning it on turns it off everywhere else) and renders a live countdown to the promo's "Valid Until" date. Shortcode [ow_promo_countdown] powers the homepage bar; single-promo.php shows a timer via ow_promo_timer_html().
 * Author: Overworld
 * Version: 1.0.0
 *
 * Behaviour:
 * - Countdown target = promo_valid_until (the date the client already fills)
 *   at 23:59:59 Singapore time.
 * - Featured promo with a future date  -> homepage bar with live countdown.
 * - Featured promo with no date        -> homepage bar without the timer.
 * - Featured promo expired / no promo  -> homepage bar hidden entirely.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ===== 1. "Feature on homepage" toggle (ACF, side box on Promos) =====
add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group( array(
		'key'      => 'group_ow_promo_feature',
		'title'    => 'Homepage Feature',
		'fields'   => array(
			array(
				'key'          => 'field_promo_featured',
				'label'        => 'Feature on homepage countdown',
				'name'         => 'promo_featured',
				'type'         => 'true_false',
				'ui'           => 1,
				'instructions' => 'Shows this promo in the countdown bar on the homepage. Only ONE promo can be featured — switching this on switches it off on every other promo. The countdown uses this promo\'s "Valid Until" date (ends 23:59 that day); without a date the bar shows with no timer; after the date the bar hides itself.',
				'required'     => 0,
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'promo',
				),
			),
		),
		'menu_order'      => 0,
		'position'        => 'side',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
	) );
} );

// ===== 2. Enforce "only one featured" =====
add_action( 'acf/save_post', function ( $post_id ) {
	if ( 'promo' !== get_post_type( $post_id ) ) {
		return;
	}
	if ( '1' !== (string) get_post_meta( $post_id, 'promo_featured', true ) ) {
		return;
	}
	$others = get_posts( array(
		'post_type'      => 'promo',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'exclude'        => array( $post_id ),
		'meta_query'     => array(
			array( 'key' => 'promo_featured', 'value' => '1' ),
		),
		'fields'         => 'ids',
	) );
	foreach ( $others as $other_id ) {
		update_post_meta( $other_id, 'promo_featured', '0' );
	}
}, 20 );

// ===== 3. Shared helpers =====

/**
 * End-of-day DateTime for a promo's valid-until date, or null.
 */
function ow_promo_end_datetime( $post_id ) {
	$raw = get_post_meta( $post_id, 'promo_valid_until', true ); // ACF date_picker: Ymd
	if ( ! $raw || ! preg_match( '/^\d{8}$/', $raw ) ) {
		return null;
	}
	$dt = DateTime::createFromFormat( 'Ymd', $raw, new DateTimeZone( 'Asia/Singapore' ) );
	if ( ! $dt ) {
		return null;
	}
	$dt->setTime( 23, 59, 59 );
	return $dt;
}

/**
 * The countdown timer units + shared ticking script (scoped, idempotent).
 */
function ow_promo_timer_html( $post_id, $extra_class = '' ) {
	$end = ow_promo_end_datetime( $post_id );
	if ( ! $end || $end->getTimestamp() < time() ) {
		return '';
	}
	$target = esc_attr( $end->format( 'c' ) );
	ob_start();
	?>
	<div class="ow-promo-timer <?php echo esc_attr( $extra_class ); ?>" data-ow-countdown data-target="<?php echo $target; ?>">
		<div class="unit"><div class="v" data-v="d">00</div><div class="u">Days</div></div>
		<div class="unit"><div class="v" data-v="h">00</div><div class="u">Hrs</div></div>
		<div class="unit"><div class="v" data-v="m">00</div><div class="u">Min</div></div>
		<div class="unit"><div class="v" data-v="s">00</div><div class="u">Sec</div></div>
	</div>
	<script>
	(function(){
		if (window.owCountdownInit) { window.owCountdownInit(); return; }
		window.owCountdownInit = function(){
			document.querySelectorAll('[data-ow-countdown]').forEach(function(el){
				if (el.dataset.init) return; el.dataset.init = '1';
				var target = new Date(el.dataset.target).getTime();
				function tick(){
					var d = Math.max(0, target - Date.now());
					var pad = function(n){ return n < 10 ? '0' + n : n; };
					el.querySelector('[data-v="d"]').textContent = pad(Math.floor(d / 864e5));
					el.querySelector('[data-v="h"]').textContent = pad(Math.floor(d % 864e5 / 36e5));
					el.querySelector('[data-v="m"]').textContent = pad(Math.floor(d % 36e5 / 6e4));
					el.querySelector('[data-v="s"]').textContent = pad(Math.floor(d % 6e4 / 1e3));
				}
				tick(); setInterval(tick, 1000);
			});
		};
		window.owCountdownInit();
	})();
	</script>
	<?php
	return ob_get_clean();
}

// ===== 4. Homepage bar shortcode =====
add_shortcode( 'ow_promo_countdown', function () {

	$featured = get_posts( array(
		'post_type'      => 'promo',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'meta_query'     => array(
			array( 'key' => 'promo_featured', 'value' => '1' ),
		),
	) );
	if ( empty( $featured ) ) {
		return '';
	}
	$promo = $featured[0];
	$end   = ow_promo_end_datetime( $promo->ID );

	// Expired -> hide the bar entirely.
	if ( $end && $end->getTimestamp() < time() ) {
		return '';
	}

	$title     = get_the_title( $promo->ID );
	$tagline   = get_post_meta( $promo->ID, 'promo_tagline', true );
	$cta_url   = get_post_meta( $promo->ID, 'promo_cta_url', true ) ?: get_permalink( $promo->ID );
	$cta_label = get_post_meta( $promo->ID, 'promo_cta_label', true ) ?: 'View Promo';
	$timer     = ow_promo_timer_html( $promo->ID );

	ob_start();
	?>
	<!-- OVERWORLD :: COUNTDOWN WIDGET :: dynamic, featured promo -->
	<style>
	  .ow-count-a{--ow-lava:#ff5722;--ow-cyan:#22e3ff;background:linear-gradient(90deg,#1a0a05 0%,#0a0a0f 50%,#051014 100%);color:#fff;padding:28px 40px;font-family:'Space Grotesk','Inter',system-ui,sans-serif;position:relative;overflow:hidden;border-top:1px solid rgba(255,87,34,.3);border-bottom:1px solid rgba(34,227,255,.2);}
	  .ow-count-a *{box-sizing:border-box;}
	  .ow-count-a .inner{max-width:1400px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:40px;flex-wrap:wrap;}
	  .ow-count-a .lhs{display:flex;align-items:center;gap:20px;}
	  .ow-count-a .pulse{width:12px;height:12px;border-radius:50%;background:var(--ow-lava);box-shadow:0 0 0 0 var(--ow-lava);animation:ow-pulse 1.6s infinite;}
	  @keyframes ow-pulse{0%{box-shadow:0 0 0 0 rgba(255,87,34,.6);}70%{box-shadow:0 0 0 12px rgba(255,87,34,0);}100%{box-shadow:0 0 0 0 rgba(255,87,34,0);}}
	  .ow-count-a .label{font-family:'JetBrains Mono',monospace;font-size:12px;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.6);}
	  .ow-count-a .headline{font-size:18px;font-weight:600;letter-spacing:-.01em;}
	  .ow-count-a .headline a{color:inherit;text-decoration:none;}
	  .ow-count-a .headline a:hover{color:var(--ow-lava);}
	  .ow-count-a .ow-promo-timer{display:flex;align-items:center;gap:10px;font-variant-numeric:tabular-nums;}
	  .ow-count-a .unit{min-width:56px;padding:10px 8px;border:1px solid rgba(255,255,255,.12);border-radius:10px;background:rgba(255,255,255,.04);text-align:center;}
	  .ow-count-a .unit .v{font-size:22px;font-weight:700;line-height:1;letter-spacing:-.02em;}
	  .ow-count-a .unit .u{font-family:'JetBrains Mono',monospace;font-size:9.5px;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-top:4px;}
	  .ow-count-a .cta{font-family:'JetBrains Mono',monospace;font-size:12px;letter-spacing:.12em;text-transform:uppercase;padding:12px 20px;border-radius:999px;background:var(--ow-lava);color:#0a0a0f;font-weight:700;text-decoration:none;white-space:nowrap;}
	  @media(max-width:780px){.ow-count-a{padding:24px 20px;}.ow-count-a .inner{justify-content:center;text-align:center;}.ow-count-a .lhs{flex-direction:column;gap:8px;}}
	</style>
	<section class="ow-count-a">
	  <div class="inner">
	    <div class="lhs">
	      <div class="pulse"></div>
	      <div>
	        <div class="label"><?php echo esc_html( $tagline ?: 'Limited-Time Promo' ); ?></div>
	        <div class="headline"><a href="<?php echo esc_url( get_permalink( $promo->ID ) ); ?>"><?php echo esc_html( $title ); ?></a><?php echo $timer ? ' <span style="color:rgba(255,255,255,.55);">— ends in</span>' : ''; ?></div>
	      </div>
	    </div>
	    <?php echo $timer; ?>
	    <a class="cta" href="<?php echo esc_url( $cta_url ); ?>"<?php echo 0 === strpos( $cta_url, 'http' ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $cta_label ); ?> →</a>
	  </div>
	</section>
	<?php
	return ob_get_clean();
} );

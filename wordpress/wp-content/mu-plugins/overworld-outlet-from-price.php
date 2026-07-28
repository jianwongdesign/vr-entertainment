<?php
/**
 * Plugin Name: Overworld — Outlet "From" Prices
 * Description: Shortcode [ow_outlet_from outlet="kallang-wave-mall"] printing the cheapest weekday and weekend/PH price for an outlet, read live from the pricing_item CPT. Powers the price row on the homepage "Our Outlets" cards, so the figures follow whatever the client edits under Pricing.
 * Author: Overworld
 * Version: 1.0.0
 *
 * Data source: pricing_item posts filtered by the pricing_outlet meta, same
 * fields the outlet pages use (pricing_weekday_price / pricing_weekend_price /
 * pricing_has_peak). Rows without a peak price repeat the weekday price on the
 * weekend side, matching page-pricing.php's table.
 *
 * Prices are cached in a transient and flushed whenever a pricing_item is
 * saved, trashed or deleted (LiteSpeed and the Elementor element cache get
 * flushed too, otherwise the homepage would keep serving the old figures).
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_FROM_PRICE_TRANSIENT = 'ow_outlet_from_prices';

/**
 * Cheapest weekday / weekend price for every outlet.
 *
 * @return array slug => array( 'weekday' => float|null, 'weekend' => float|null )
 */
function ow_outlet_from_prices() {

	$cached = get_transient( OW_FROM_PRICE_TRANSIENT );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$prices = array();

	$items = get_posts( array(
		'post_type'        => 'pricing_item',
		'post_status'      => 'publish',
		'numberposts'      => -1,
		'fields'           => 'ids',
		'suppress_filters' => false,
	) );

	foreach ( $items as $id ) {
		$slug = get_post_meta( $id, 'pricing_outlet', true );
		if ( ! $slug ) {
			continue;
		}

		$weekday = (float) get_post_meta( $id, 'pricing_weekday_price', true );
		if ( $weekday <= 0 ) {
			continue;
		}

		// Same rule as the outlet pricing table: no peak flag means the
		// weekday price applies on weekends too.
		$has_peak = get_post_meta( $id, 'pricing_has_peak', true ) === '1';
		$weekend  = (float) get_post_meta( $id, 'pricing_weekend_price', true );
		if ( ! $has_peak || $weekend <= 0 ) {
			$weekend = $weekday;
		}

		if ( ! isset( $prices[ $slug ] ) ) {
			$prices[ $slug ] = array(
				'weekday' => $weekday,
				'weekend' => $weekend,
			);
			continue;
		}

		$prices[ $slug ]['weekday'] = min( $prices[ $slug ]['weekday'], $weekday );
		$prices[ $slug ]['weekend'] = min( $prices[ $slug ]['weekend'], $weekend );
	}

	set_transient( OW_FROM_PRICE_TRANSIENT, $prices, DAY_IN_SECONDS );

	return $prices;
}

/**
 * Format a price for display: $16, $9.50 — never a trailing .00.
 *
 * @param float $value Price.
 * @return string
 */
function ow_outlet_from_price_format( $value ) {
	$formatted = number_format( (float) $value, 2, '.', ',' );
	$formatted = rtrim( rtrim( $formatted, '0' ), '.' );

	return '$' . $formatted;
}

/**
 * Shortcode: [ow_outlet_from outlet="funan"]
 *
 * Prints nothing at all when the outlet has no usable pricing rows, so the
 * card simply closes up instead of showing an empty strip.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function ow_outlet_from_shortcode( $atts ) {

	$atts = shortcode_atts( array( 'outlet' => '' ), $atts, 'ow_outlet_from' );
	$slug = sanitize_title( $atts['outlet'] );
	if ( ! $slug ) {
		return '';
	}

	$prices = ow_outlet_from_prices();
	if ( empty( $prices[ $slug ]['weekday'] ) ) {
		return '';
	}

	$weekday = ow_outlet_from_price_format( $prices[ $slug ]['weekday'] );
	$weekend = ow_outlet_from_price_format( $prices[ $slug ]['weekend'] );

	static $printed_css = false;
	$css = '';
	if ( ! $printed_css ) {
		$printed_css = true;
		$css         = '<style>'
			. '.ow-fp{display:flex;gap:10px;margin:0 0 22px;padding-top:18px;border-top:1px solid rgba(255,255,255,.1);}'
			. '.ow-fp__item{flex:1;min-width:0;}'
			. '.ow-fp__lbl{display:block;font-family:\'JetBrains Mono\',monospace;font-size:9.5px;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:6px;white-space:nowrap;}'
			. '.ow-fp__val{display:block;font-family:\'Space Grotesk\',\'Inter\',system-ui,sans-serif;font-size:24px;font-weight:700;line-height:1;letter-spacing:-.02em;color:#fff;}'
			. '.ow-fp__item--peak .ow-fp__val{color:var(--ow-fp-accent,#ff5722);}'
			. '.ow-fp__unit{font-size:11px;font-weight:500;letter-spacing:0;color:rgba(255,255,255,.5);margin-left:3px;}'
			// "Weekend & PH from" is the longest label; below ~400px the card
			// column is too narrow for it at full size.
			. '@media(max-width:400px){.ow-fp__lbl{font-size:8.5px;letter-spacing:.08em;}.ow-fp__val{font-size:21px;}}'
			. '</style>';
	}

	return $css
		. '<div class="ow-fp">'
		. '<div class="ow-fp__item">'
		. '<span class="ow-fp__lbl">Weekday from</span>'
		. '<span class="ow-fp__val">' . esc_html( $weekday ) . '<span class="ow-fp__unit">/pax</span></span>'
		. '</div>'
		. '<div class="ow-fp__item ow-fp__item--peak">'
		. '<span class="ow-fp__lbl">Weekend &amp; PH from</span>'
		. '<span class="ow-fp__val">' . esc_html( $weekend ) . '<span class="ow-fp__unit">/pax</span></span>'
		. '</div>'
		. '</div>';
}
add_shortcode( 'ow_outlet_from', 'ow_outlet_from_shortcode' );

/**
 * Elementor's HTML widget prints its markup raw, so run shortcodes for the
 * widgets that actually contain ours and leave every other widget untouched.
 */
add_filter( 'elementor/widget/render_content', function ( $content, $widget ) {
	if ( is_string( $content ) && false !== strpos( $content, '[ow_outlet_from' ) ) {
		return do_shortcode( $content );
	}

	return $content;
}, 10, 2 );

/**
 * Flush the cached figures (and the caches in front of them) on price edits.
 */
function ow_outlet_from_price_flush( $post_id ) {

	if ( 'pricing_item' !== get_post_type( $post_id ) ) {
		return;
	}

	delete_transient( OW_FROM_PRICE_TRANSIENT );

	// Elementor stores the rendered widget HTML per page.
	global $wpdb;
	$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_elementor_element_cache' ) );

	if ( defined( 'LSCWP_V' ) ) {
		do_action( 'litespeed_purge_all' );
	}
}
add_action( 'save_post', 'ow_outlet_from_price_flush' );
add_action( 'trashed_post', 'ow_outlet_from_price_flush' );
add_action( 'untrashed_post', 'ow_outlet_from_price_flush' );
add_action( 'before_delete_post', 'ow_outlet_from_price_flush' );
add_action( 'acf/save_post', 'ow_outlet_from_price_flush', 20 );

<?php
/**
 * Plugin Name: Overworld — Outlet Page Template Guard
 * Description: Locks the three outlet pages to the "Pricing Page" template. Opening one in Elementor used to reset its template to Default, which rendered the page blank (28 Jul 2026 incident on Kallang). Blocks the meta write, repairs the value on save, and warns in the editor.
 * Author: Overworld
 * Version: 1.0.0
 *
 * The outlet pages' entire layout lives in page-pricing.php. Without that
 * template assigned the pages have no content of their own — Elementor holds
 * nothing for them — so the site serves an empty shell.
 *
 * Escape hatch: define( 'OW_DISABLE_TEMPLATE_GUARD', true ) in wp-config.php,
 * or return false from the 'ow_outlet_template_guard_enabled' filter, if a
 * page ever needs to move to a different template on purpose.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_OUTLET_TEMPLATE = 'page-pricing.php';

/**
 * Slugs of the pages that must keep the Pricing Page template.
 *
 * @return string[]
 */
function ow_outlet_guarded_slugs() {
	return array( 'kallang-wave-mall', 'orchard-central', 'funan' );
}

/**
 * Is the guard switched on?
 *
 * @return bool
 */
function ow_outlet_template_guard_enabled() {
	if ( defined( 'OW_DISABLE_TEMPLATE_GUARD' ) && OW_DISABLE_TEMPLATE_GUARD ) {
		return false;
	}

	return (bool) apply_filters( 'ow_outlet_template_guard_enabled', true );
}

/**
 * Is this post one of the guarded outlet pages?
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function ow_is_guarded_outlet_page( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || 'page' !== get_post_type( $post_id ) ) {
		return false;
	}

	return in_array( get_post_field( 'post_name', $post_id ), ow_outlet_guarded_slugs(), true );
}

/**
 * Block any attempt to move a guarded page off the Pricing Page template.
 * Returning non-null short-circuits the meta write; `true` makes the caller
 * (Elementor, the editor, whatever) believe it succeeded and carry on.
 */
function ow_outlet_template_guard_meta( $check, $object_id, $meta_key, $meta_value ) {
	if ( '_wp_page_template' !== $meta_key || ! ow_outlet_template_guard_enabled() ) {
		return $check;
	}
	if ( OW_OUTLET_TEMPLATE === $meta_value || ! ow_is_guarded_outlet_page( $object_id ) ) {
		return $check;
	}

	return true;
}
add_filter( 'update_post_metadata', 'ow_outlet_template_guard_meta', 10, 4 );
add_filter( 'add_post_metadata', 'ow_outlet_template_guard_meta', 10, 4 );

/**
 * Block deletion of the template meta on a guarded page — an empty value
 * falls back to the default template just as surely as writing "default".
 */
add_filter( 'delete_post_metadata', function ( $check, $object_id, $meta_key ) {
	if ( '_wp_page_template' !== $meta_key || ! ow_outlet_template_guard_enabled() ) {
		return $check;
	}
	if ( ! ow_is_guarded_outlet_page( $object_id ) ) {
		return $check;
	}

	return true;
}, 10, 3 );

/**
 * Belt and braces: repair the value after any save, in case something writes
 * the meta straight to the database and skips the filters above.
 */
add_action( 'save_post_page', function ( $post_id ) {
	if ( ! ow_outlet_template_guard_enabled() || ! ow_is_guarded_outlet_page( $post_id ) ) {
		return;
	}
	if ( OW_OUTLET_TEMPLATE === get_post_meta( $post_id, '_wp_page_template', true ) ) {
		return;
	}

	// The filters above would block update_post_meta, so write it directly.
	global $wpdb;
	$wpdb->query( $wpdb->prepare(
		"UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE post_id = %d AND meta_key = '_wp_page_template'",
		OW_OUTLET_TEMPLATE,
		$post_id
	) );
	if ( ! metadata_exists( 'post', $post_id, '_wp_page_template' ) ) {
		$wpdb->insert( $wpdb->postmeta, array(
			'post_id'    => $post_id,
			'meta_key'   => '_wp_page_template',
			'meta_value' => OW_OUTLET_TEMPLATE,
		) );
	}
	wp_cache_delete( $post_id, 'post_meta' );
	delete_post_meta( $post_id, '_elementor_element_cache' );
}, 99 );

/**
 * Tell whoever is editing the page why the template dropdown will not stick.
 */
add_action( 'admin_notices', function () {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'post' !== $screen->base || 'page' !== $screen->post_type ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
	if ( ! ow_is_guarded_outlet_page( $post_id ) ) {
		return;
	}

	echo '<div class="notice notice-info"><p><strong>Outlet page:</strong> this page is locked to the '
		. '<em>Pricing Page</em> template — its whole layout comes from there. Edit the content using the '
		. 'boxes below (What We Offer, Combo Deals, Outlet Gallery). Please do <strong>not</strong> use '
		. '"Edit with Elementor" on this page.</p></div>';
} );

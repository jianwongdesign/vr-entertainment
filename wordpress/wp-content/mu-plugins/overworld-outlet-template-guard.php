<?php
/**
 * Plugin Name: Overworld — Page Template Guard
 * Description: Locks the template-driven pages (outlets, FAQ, event hubs, event listings) to their page templates. Opening one in Elementor used to reset its template to Default, which rendered the page blank (28 Jul 2026 incident on Kallang). Blocks the meta write, repairs the value on save, and warns in the editor.
 * Author: Overworld
 * Version: 1.1.0
 *
 * These pages hold almost no content of their own — the whole layout comes
 * from the theme template, filled from ACF fields and the pricing_item /
 * event_package CPTs. Drop the template and WordPress renders an empty shell:
 * header, footer, nothing in between, and no error to notice it by.
 *
 * v1.1.0: extended beyond the outlet pages to FAQ, event hub and event
 * listing pages, and switched from slug matching to an explicit ID map —
 * the event listing pages share slugs with the outlet pages
 * (e.g. "kallang-wave-mall" is both page 504 and page 524), so matching on
 * slug alone would have forced the wrong template onto them.
 *
 * Escape hatch: define( 'OW_DISABLE_TEMPLATE_GUARD', true ) in wp-config.php,
 * or return false from the 'ow_page_template_guard_enabled' filter, if a page
 * ever needs to move to a different template on purpose.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Page ID => template it must keep.
 *
 * IDs rather than slugs, deliberately — see the version note above. A page
 * recreated under a new ID simply falls outside the guard, which is the safe
 * way to fail.
 *
 * @return array<int,string>
 */
function ow_guarded_page_templates() {
	return apply_filters( 'ow_guarded_page_templates', array(
		// Outlet pages
		504 => 'page-pricing.php',      // kallang-wave-mall
		505 => 'page-pricing.php',      // orchard-central
		506 => 'page-pricing.php',      // funan
		// FAQ
		372 => 'page-faq.php',
		// Event hubs
		521 => 'page-event-hub.php',    // team-building
		522 => 'page-event-hub.php',    // birthday-party
		// Event listings (team building + birthday party, per outlet)
		524 => 'page-event-listing.php',
		525 => 'page-event-listing.php',
		526 => 'page-event-listing.php',
		527 => 'page-event-listing.php',
		528 => 'page-event-listing.php',
		529 => 'page-event-listing.php',
	) );
}

/**
 * Is the guard switched on?
 *
 * @return bool
 */
function ow_page_template_guard_enabled() {
	if ( defined( 'OW_DISABLE_TEMPLATE_GUARD' ) && OW_DISABLE_TEMPLATE_GUARD ) {
		return false;
	}

	return (bool) apply_filters( 'ow_page_template_guard_enabled', true );
}

/**
 * The template this page is locked to, or '' when it is not guarded.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function ow_guarded_page_template( $post_id ) {
	$map     = ow_guarded_page_templates();
	$post_id = (int) $post_id;

	return isset( $map[ $post_id ] ) ? $map[ $post_id ] : '';
}

/**
 * Block any attempt to move a guarded page off its template. Returning
 * non-null short-circuits the meta write; `true` makes the caller (Elementor,
 * the editor, whatever) believe it succeeded and carry on.
 */
function ow_page_template_guard_meta( $check, $object_id, $meta_key, $meta_value ) {
	if ( '_wp_page_template' !== $meta_key || ! ow_page_template_guard_enabled() ) {
		return $check;
	}
	$locked = ow_guarded_page_template( $object_id );
	if ( '' === $locked || $locked === $meta_value ) {
		return $check;
	}

	return true;
}
add_filter( 'update_post_metadata', 'ow_page_template_guard_meta', 10, 4 );
add_filter( 'add_post_metadata', 'ow_page_template_guard_meta', 10, 4 );

/**
 * Block deletion of the template meta on a guarded page — an empty value
 * falls back to the default template just as surely as writing "default".
 */
add_filter( 'delete_post_metadata', function ( $check, $object_id, $meta_key ) {
	if ( '_wp_page_template' !== $meta_key || ! ow_page_template_guard_enabled() ) {
		return $check;
	}
	if ( '' === ow_guarded_page_template( $object_id ) ) {
		return $check;
	}

	return true;
}, 10, 3 );

/**
 * Belt and braces: repair the value after any save, in case something writes
 * the meta straight to the database and skips the filters above.
 */
add_action( 'save_post_page', function ( $post_id ) {
	if ( ! ow_page_template_guard_enabled() ) {
		return;
	}
	$locked = ow_guarded_page_template( $post_id );
	if ( '' === $locked || $locked === get_post_meta( $post_id, '_wp_page_template', true ) ) {
		return;
	}

	// The filters above would block update_post_meta, so write it directly.
	global $wpdb;
	if ( metadata_exists( 'post', $post_id, '_wp_page_template' ) ) {
		$wpdb->query( $wpdb->prepare(
			"UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE post_id = %d AND meta_key = '_wp_page_template'",
			$locked,
			$post_id
		) );
	} else {
		$wpdb->insert( $wpdb->postmeta, array(
			'post_id'    => $post_id,
			'meta_key'   => '_wp_page_template',
			'meta_value' => $locked,
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
	$locked  = ow_guarded_page_template( $post_id );
	if ( '' === $locked ) {
		return;
	}

	echo '<div class="notice notice-info"><p><strong>Template-driven page:</strong> this page is locked to the '
		. '<code>' . esc_html( $locked ) . '</code> template — its whole layout comes from there, so the page '
		. 'itself is nearly empty. Edit the content using the boxes below. Please do <strong>not</strong> use '
		. '"Edit with Elementor" on this page.</p></div>';
} );

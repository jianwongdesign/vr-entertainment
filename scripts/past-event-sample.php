<?php
/**
 * Creates one example Past Event as a DRAFT, so the client opens a filled-in
 * entry rather than an empty form the first time they look.
 *
 * Idempotent and conservative: does nothing at all if any past event already
 * exists, in any status. A draft is invisible to visitors — the gallery and
 * the listing only ever show published entries — so this changes nothing on
 * the live site.
 *
 * Run through wp-cli:
 *   wp eval-file past-event-sample.php
 *
 * @package Overworld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! post_type_exists( 'past_event' ) ) {
	WP_CLI::warning( 'The past_event content type is not registered — is overworld-event-gallery.php deployed?' );
	return;
}

$existing = get_posts(
	array(
		'post_type'   => 'past_event',
		'post_status' => 'any',
		'numberposts' => 1,
		'fields'      => 'ids',
	)
);

if ( $existing ) {
	WP_CLI::log( sprintf( 'Past events already exist (%d…) — leaving them alone.', (int) $existing[0] ) );
	return;
}

$id = wp_insert_post(
	array(
		'post_type'    => 'past_event',
		'post_status'  => 'draft',
		'post_title'   => 'Example — Corporate Family Day (delete or edit me)',
		'post_name'    => 'example-corporate-family-day',
		'post_content' => "This is the write-up. It uses the normal editor, so you can add headings, paragraphs, lists and pictures the same way you would anywhere else on the site.\n\nTell the story of the day: what the client wanted, which games we brought, how the space was laid out, how many people played and what worked well. Two or three short paragraphs is plenty — the photos do the rest.\n\nWhen you are happy with it, fill in the boxes below, add your photos, and hit Publish. The event then appears in the gallery on the Equipment Rental page and on the Past Events listing, with its own page like this one.",
	),
	true
);

if ( is_wp_error( $id ) ) {
	WP_CLI::warning( 'Could not create the example: ' . $id->get_error_message() );
	return;
}

$meta = array(
	'pe_tag'          => 'Corporate Family Day',
	'pe_venue'        => 'Client venue, Singapore',
	'pe_date_label'   => 'Month Year',
	'pe_summary'      => 'One or two lines about the event. This is what people read on the tile, and what Google shows under the page in search results.',
	'pe_intro'        => 'A short lead paragraph that sits under the title at the top of the page.',
	'pe_featured'     => '1',
	'pe_stat_1_value' => '000',
	'pe_stat_1_label' => 'Guests through the games',
	'pe_stat_2_value' => '0',
	'pe_stat_2_label' => 'Games on site',
	'pe_stat_3_value' => '0h',
	'pe_stat_3_label' => 'Setup to pack-down',
	'pe_game_1_name'  => 'VR Free Roam',
	'pe_game_1_text'  => 'One line about what it was like on the day.',
	'pe_game_1_url'   => '/vr-free-roam/',
);

foreach ( $meta as $key => $value ) {
	update_post_meta( $id, $key, $value );
	// ACF stores a companion _key row so the edit screen knows which field a
	// value belongs to; without it the boxes look empty even though the meta
	// is there.
	update_post_meta( $id, '_' . $key, 'field_' . $key );
}

// admin_url, not get_edit_post_link: wp-cli has no current user, and that
// function returns nothing without one.
WP_CLI::success( sprintf( 'Example past event created as a DRAFT (id %d): %s', $id, admin_url( 'post.php?post=' . $id . '&action=edit' ) ) );

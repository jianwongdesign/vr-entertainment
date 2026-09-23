<?php
/**
 * Creates three PUBLISHED sample past events so the gallery section on
 * /event-rental/ can be seen with real pictures in it.
 *
 * These are illustrations, not real case studies: every title starts with
 * "Sample —" so nobody mistakes them for events Overworld actually ran, and
 * the write-ups say so in the first line. Replace them with real events, or
 * delete them:
 *
 *   wp post delete <ids> --force
 *
 * Photos come from attachments already in the media library (the outlet
 * gallery pictures), so nothing is uploaded and nothing is invented.
 *
 * Idempotent: an entry whose slug already exists is left untouched.
 *
 * Run through wp-cli:
 *   wp eval-file past-event-samples.php
 *
 * @package Overworld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! post_type_exists( 'past_event' ) ) {
	WP_CLI::error( 'The past_event content type is not registered — deploy overworld-event-gallery.php first.' );
}

$note = "This is sample content, written so the page can be seen with something in it. Replace it with a real event, or delete this entry.\n\n";

$samples = array(
	array(
		'slug'    => 'sample-corporate-family-day',
		'title'   => 'Sample — Corporate Family Day',
		'content' => $note . "The brief was a full afternoon of activity for staff and their families in one ballroom, with a queue that never stopped moving.\n\nWe brought three games in, laid them out along one wall so the floor stayed open, and ran short rounds back to back. Two crew stayed on the games for the whole session — briefing every player, resetting between rounds and keeping the queue fed — while a third handled the kit.\n\nSetup started three hours before doors and pack-down was done within the hour after.",
		'meta'    => array(
			'pe_tag'          => 'Corporate Family Day',
			'pe_venue'        => 'Hotel ballroom, Marina Bay',
			'pe_date_label'   => 'June 2026',
			'pe_summary'      => 'A full afternoon of VR Free Roam, Floor Is Lava and an XR party game for staff and their families, run end to end by our crew.',
			'pe_intro'        => 'One ballroom, three games, one afternoon — and a queue that kept moving from doors to close.',
			'pe_cover'        => 707,
			'pe_stat_1_value' => '600',
			'pe_stat_1_label' => 'Guests through the games',
			'pe_stat_2_value' => '3',
			'pe_stat_2_label' => 'Games on site',
			'pe_stat_3_value' => '6h',
			'pe_stat_3_label' => 'Doors to pack-down',
			'pe_game_1_name'  => 'VR Free Roam',
			'pe_game_1_text'  => 'Four players at a time, untethered.',
			'pe_game_1_url'   => '/vr-free-roam/',
			'pe_game_1_image' => 1252,
			'pe_game_2_name'  => 'Floor Is Lava',
			'pe_game_2_text'  => 'Modular LED floor, scaled to the room.',
			'pe_game_2_url'   => '/floor-is-lava/',
			'pe_game_2_image' => 450,
			'pe_game_3_name'  => 'Laser Maze',
			'pe_game_3_text'  => 'Quick rounds, big spectator crowd.',
			'pe_game_3_url'   => '/laser-maze/',
			'pe_game_3_image' => 451,
			'pe_photo_1'      => 707,
			'pe_photo_2'      => 1252,
			'pe_photo_3'      => 450,
			'pe_photo_4'      => 1597,
			'pe_photo_5'      => 448,
			'pe_photo_6'      => 803,
			'pe_featured'     => '1',
		),
	),
	array(
		'slug'    => 'sample-school-carnival',
		'title'   => 'Sample — School Carnival',
		'content' => $note . "Two days in a school hall, with classes rotating through in twenty-minute blocks.\n\nThe games had to be quick to explain, safe in a crowd and playable in school shoes, so we brought the two that fit that best and set them at opposite ends of the hall to split the queue.\n\nTeachers ran the rotation; our crew ran the games.",
		'meta'    => array(
			'pe_tag'          => 'School Carnival',
			'pe_venue'        => 'School hall, Singapore',
			'pe_date_label'   => 'May 2026',
			'pe_summary'      => 'Two days of Laser Maze and Tap Tap in a school hall, with classes rotating through in twenty-minute blocks.',
			'pe_intro'        => 'Two games, two days, and a whole cohort through the hall without a single bottleneck.',
			'pe_cover'        => 451,
			'pe_stat_1_value' => '420',
			'pe_stat_1_label' => 'Students played',
			'pe_stat_2_value' => '2',
			'pe_stat_2_label' => 'Games on site',
			'pe_stat_3_value' => '2',
			'pe_stat_3_label' => 'Days on campus',
			'pe_game_1_name'  => 'Laser Maze',
			'pe_game_1_text'  => 'Short rounds, easy to brief.',
			'pe_game_1_url'   => '/laser-maze/',
			'pe_game_1_image' => 451,
			'pe_game_2_name'  => 'Tap Tap',
			'pe_game_2_text'  => 'Head-to-head, no headset needed.',
			'pe_game_2_url'   => '/tap-tap/',
			'pe_game_2_image' => 449,
			'pe_photo_1'      => 451,
			'pe_photo_2'      => 449,
			'pe_photo_3'      => 1236,
			'pe_photo_4'      => 419,
			'pe_featured'     => '1',
		),
	),
	array(
		'slug'    => 'sample-mall-roadshow',
		'title'   => 'Sample — Mall Atrium Roadshow',
		'content' => $note . "A three-day activation in a mall atrium, where the job of the games was to stop people walking past.\n\nFloor Is Lava did the pulling — it is loud, bright and readable from the escalator — and the escape game gave the crowd something to do while they waited. Both were set up inside the tenancy line with cabling run flat and taped down.\n\nThe crew worked mall hours across all three days.",
		'meta'    => array(
			'pe_tag'          => 'Mall Roadshow',
			'pe_venue'        => 'Mall atrium, Singapore',
			'pe_date_label'   => 'March 2026',
			'pe_summary'      => 'Three days in a mall atrium, with the games doing the work of stopping shoppers mid-walk.',
			'pe_intro'        => 'Bright, loud and readable from the escalator — an atrium activation built to interrupt a walk-past.',
			'pe_cover'        => 445,
			'pe_stat_1_value' => '1,200',
			'pe_stat_1_label' => 'Players over three days',
			'pe_stat_2_value' => '2',
			'pe_stat_2_label' => 'Games on site',
			'pe_stat_3_value' => '3',
			'pe_stat_3_label' => 'Days of mall hours',
			'pe_game_1_name'  => 'Floor Is Lava',
			'pe_game_1_text'  => 'The crowd-puller.',
			'pe_game_1_url'   => '/floor-is-lava/',
			'pe_game_1_image' => 450,
			'pe_game_2_name'  => 'VR Escape',
			'pe_game_2_text'  => 'Small groups, longer sessions.',
			'pe_game_2_url'   => '/vr-escape-room/',
			'pe_game_2_image' => 418,
			'pe_photo_1'      => 445,
			'pe_photo_2'      => 1263,
			'pe_photo_3'      => 418,
			'pe_photo_4'      => 448,
			'pe_featured'     => '1',
		),
	),
);

$created = array();

foreach ( $samples as $sample ) {
	$existing = get_page_by_path( $sample['slug'], OBJECT, 'past_event' );
	if ( $existing ) {
		WP_CLI::log( sprintf( 'Already there: %s (id %d) — left alone.', $sample['slug'], $existing->ID ) );
		continue;
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'past_event',
			'post_status'  => 'publish',
			'post_title'   => $sample['title'],
			'post_name'    => $sample['slug'],
			'post_content' => $sample['content'],
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( 'Could not create ' . $sample['slug'] . ': ' . $id->get_error_message() );
		continue;
	}

	foreach ( $sample['meta'] as $key => $value ) {
		// Skip an image slot whose attachment is gone, rather than render a
		// broken tile.
		if ( is_int( $value ) && 'attachment' !== get_post_type( $value ) ) {
			WP_CLI::warning( sprintf( '%s: attachment %d missing, slot %s left empty.', $sample['slug'], $value, $key ) );
			continue;
		}
		update_post_meta( $id, $key, $value );
		// ACF's companion _key row, so the edit screen knows which field the
		// value belongs to.
		update_post_meta( $id, '_' . $key, 'field_' . $key );
	}

	// What the plugin's own save hook would do: cover photo becomes the
	// featured image, tile text becomes the excerpt.
	if ( isset( $sample['meta']['pe_cover'] ) ) {
		set_post_thumbnail( $id, (int) $sample['meta']['pe_cover'] );
	}
	wp_update_post(
		array(
			'ID'           => $id,
			'post_excerpt' => $sample['meta']['pe_summary'],
		)
	);

	$created[] = $id;
	WP_CLI::log( sprintf( 'Created %s (id %d): %s', $sample['slug'], $id, get_permalink( $id ) ) );
}

if ( $created ) {
	WP_CLI::success( sprintf( 'Published %d sample events. Delete them with: wp post delete %s --force', count( $created ), implode( ' ', $created ) ) );
} else {
	WP_CLI::log( 'Nothing to do.' );
}

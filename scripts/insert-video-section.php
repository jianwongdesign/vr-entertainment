<?php
/**
 * Drop the video section into the 8 game pages, directly under the hero.
 *
 * The pages are Elementor documents in the database, so the section is a
 * container holding one shortcode widget — the same shape the existing
 * [ow_experience_grid] block already uses on these pages. The section itself
 * is rendered by mu-plugin overworld-video-section.php and stays invisible to
 * visitors until someone pastes a YouTube link into the page's Video field.
 *
 * Inserted at index 1: hero stays first, everything else shifts down one.
 * Idempotent — a page that already carries an [ow_video] block is skipped.
 *
 * Run: wp eval-file insert-video-section.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ow_video_game_pages' ) ) {
	echo "overworld-video-section.php is not loaded — nothing done.\n";
	return;
}

$backup_dir = getenv( 'HOME' ) . '/overworld-backups/video-section-20260807';
if ( ! is_dir( $backup_dir ) ) {
	mkdir( $backup_dir, 0755, true );
}

$done = array();

foreach ( ow_video_game_pages() as $id => $accent ) {
	$post = get_post( $id );
	if ( ! $post ) {
		printf( "SKIP  %d does not exist\n", $id );
		continue;
	}

	$raw = (string) get_post_meta( $id, '_elementor_data', true );
	$data = json_decode( $raw, true );

	if ( ! is_array( $data ) || ! isset( $data[0]['elType'] ) ) {
		printf( "SKIP  %d %-16s unexpected Elementor structure\n", $id, $post->post_name );
		continue;
	}

	if ( false !== strpos( $raw, '[ow_video' ) ) {
		printf( "SKIP  %d %-16s already has an [ow_video] block\n", $id, $post->post_name );
		continue;
	}

	file_put_contents( "$backup_dir/{$post->post_name}-{$id}-elementor_data.json", $raw );

	$section = array(
		'id'       => substr( md5( 'ow_video_container_' . $id ), 0, 7 ),
		'elType'   => 'container',
		'settings' => array(
			'content_width' => 'full',
			'padding'       => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ),
		),
		'elements' => array(
			array(
				'id'         => substr( md5( 'ow_video_widget_' . $id ), 0, 7 ),
				'elType'     => 'widget',
				'settings'   => array( 'shortcode' => '[ow_video accent="' . $accent . '"]' ),
				'elements'   => array(),
				'widgetType' => 'shortcode',
			),
		),
		'isInner'  => false,
	);

	// Straight after the hero.
	array_splice( $data, 1, 0, array( $section ) );

	update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) );
	delete_post_meta( $id, '_elementor_element_cache' );

	$done[] = $id;
	printf( "OK    %d %-16s video section inserted at index 1 (accent %s), sections now %d\n", $id, $post->post_name, $accent, count( $data ) );
}

echo "\ndone: " . implode( ', ', $done ) . "\nbackups in: $backup_dir\n";

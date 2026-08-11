<?php
/**
 * Activity page heroes: stats pill goes 2×2 on phones instead of a stack.
 *
 * The pill under the hero copy ("30+ Games · 1-17 Players · 8+ Years Old ·
 * 3 Session Lengths") is one row on desktop. Below 560px seven of the eight
 * pages switch it to `flex-direction:column`, so the four stats read as four
 * separate lines — a tall ladder in the middle of the hero. This turns that
 * tier into a two-column grid: 2×2, one card, half the height.
 *
 * VR Free Roam is built differently — it wraps instead of stacking, in a
 * single ≤760 tier — so it gets the same grid at its own breakpoint.
 *
 * Every replacement is guarded: the anchor must appear exactly once in the
 * field or that edit is skipped. Both `post_content` and `_elementor_data`
 * are patched, and `_elementor_element_cache` is dropped afterwards (without
 * that the change never reaches the browser).
 *
 * Run: wp eval-file hero-stats-2x2.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------
   Seven pages share the same stacking rule; only the BEM prefix differs.
   --------------------------------------------------------------------- */

$stacking = array(
	326 => 'vra', // vr-arcade
	420 => 'vre', // vr-escape
	294 => 'fil', // floor-is-lava
	312 => 'lm',  // laser-maze
	364 => 'tt',  // tap-tap
	338 => 'vmr', // vr-machine-ride
	577 => 'xr',  // xr-party-game
);

$edits = array();

foreach ( $stacking as $id => $p ) {
	// The pill itself: column -> two equal columns. width/max-width replace
	// the base `max-width:max-content`, which is what would otherwise let a
	// two-column row overflow a 360px screen.
	$edits[] = array(
		'id'   => $id,
		'from' => ".ow-{$p}-hero__stats{flex-direction:column;border-radius:24px;padding:16px 24px;gap:12px;}",
		'to'   => ".ow-{$p}-hero__stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));border-radius:24px;padding:16px 18px;gap:14px 16px;width:100%;max-width:420px;margin-left:auto;margin-right:auto;}",
	);
	// Each cell: centre it, and let a long label wrap inside its own cell
	// rather than push the grid wider (the base rule is white-space:nowrap).
	$edits[] = array(
		'id'   => $id,
		'from' => ".ow-{$p}-hero__stat{padding:0;}",
		'to'   => ".ow-{$p}-hero__stat{padding:0;white-space:normal;justify-content:center;}",
	);
}

// VR Free Roam: no column rule to swap — it wraps. Same grid, its own tier.
$edits[] = array(
	'id'   => 646,
	'from' => '.ow-vfr-hero__stats{flex-wrap:wrap;border-radius:16px;}.ow-vfr-hero__stat+.ow-vfr-hero__stat{border-left:0;}',
	'to'   => '.ow-vfr-hero__stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));border-radius:16px;width:100%;max-width:420px;margin-left:auto;margin-right:auto;}.ow-vfr-hero__stat+.ow-vfr-hero__stat{border-left:0;}.ow-vfr-hero__stat{white-space:normal;justify-content:center;}',
);

/* ------------------------------------------------------------------- */

global $wpdb;

$backup_dir = getenv( 'HOME' ) . '/overworld-backups/hero-stats-2x2-20260807';
if ( ! is_dir( $backup_dir ) ) {
	mkdir( $backup_dir, 0755, true );
}

$touched = array();

foreach ( $edits as $e ) {
	$id = $e['id'];

	if ( ! isset( $touched[ $id ] ) ) {
		$post = get_post( $id );
		file_put_contents( "$backup_dir/{$post->post_name}-{$id}-post_content.txt", $post->post_content );
		file_put_contents( "$backup_dir/{$post->post_name}-{$id}-elementor_data.json", (string) get_post_meta( $id, '_elementor_data', true ) );
		$touched[ $id ] = get_post( $id )->post_name;
	}

	foreach ( array( 'post_content', '_elementor_data' ) as $field ) {
		$blob = ( 'post_content' === $field )
			? get_post( $id )->post_content
			: (string) get_post_meta( $id, '_elementor_data', true );

		$n = substr_count( $blob, $e['from'] );
		if ( 1 !== $n ) {
			printf( "SKIP  %d %-16s anchor x%d (expected 1): %.48s...\n", $id, $field, $n, $e['from'] );
			continue;
		}

		$new = str_replace( $e['from'], $e['to'], $blob );

		if ( 'post_content' === $field ) {
			$wpdb->update( $wpdb->posts, array( 'post_content' => $new ), array( 'ID' => $id ) );
			clean_post_cache( $id );
		} else {
			update_post_meta( $id, '_elementor_data', wp_slash( $new ) );
		}

		printf( "OK    %d %-16s patched: %.48s...\n", $id, $field, $e['from'] );
	}
}

foreach ( array_keys( $touched ) as $id ) {
	delete_post_meta( $id, '_elementor_element_cache' );
}

echo "\ncache cleared on: " . implode( ', ', array_keys( $touched ) ) . "\n";
echo "backups in: $backup_dir\n";

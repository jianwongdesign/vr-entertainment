<?php
/**
 * Make Events → Team Building / Birthday Party clickable in the header nav.
 *
 * Both were `href="#"`, so they only opened their submenu and there was no way
 * to reach the /team-building/ and /birthday-party/ landing pages from the
 * menu. This points each parent at its landing page, and — because the nav JS
 * swallows the tap on touch devices to open the submenu instead — also adds an
 * explicit "All Outlets" link as the first item inside each submenu.
 *
 * Header lives in the header-footer-elementor template (post 29), HTML widget
 * 36ab9a3. Idempotent. Run: wp eval-file nav-event-hub-links.php
 */

$post_id   = 29;
$widget_id = '36ab9a3';

$data = json_decode( get_post_meta( $post_id, '_elementor_data', true ), true );
if ( ! is_array( $data ) ) {
	echo "post {$post_id}: no _elementor_data, aborted\n";
	return;
}

$find = function ( $elements ) use ( &$find, $widget_id ) {
	foreach ( $elements as $element ) {
		if ( ( isset( $element['id'] ) ? $element['id'] : '' ) === $widget_id ) {
			return isset( $element['settings']['html'] ) ? $element['settings']['html'] : null;
		}
		if ( ! empty( $element['elements'] ) ) {
			$found = $find( $element['elements'] );
			if ( null !== $found ) {
				return $found;
			}
		}
	}
	return null;
};

$html = $find( $data );
if ( null === $html ) {
	echo "widget {$widget_id}: not found, aborted\n";
	return;
}

if ( false !== strpos( $html, 'href="/team-building/"' ) ) {
	echo "nav already links to the landing pages, nothing to do\n";
	return;
}

$types = array(
	'Team Building'  => 'team-building',
	'Birthday Party' => 'birthday-party',
);

foreach ( $types as $label => $slug ) {

	// 1. Parent item becomes a real link.
	$from = '<a href="#" aria-haspopup="true" aria-expanded="false">' . $label . '</a>';
	$to   = '<a href="/' . $slug . '/" aria-haspopup="true" aria-expanded="false">' . $label . '</a>';
	if ( false === strpos( $html, $from ) ) {
		echo "{$label}: parent anchor not found, aborted without saving\n";
		return;
	}
	$html = str_replace( $from, $to, $html );

	// 2. Explicit overview link inside the submenu, for touch users.
	$anchor = '<li><a href="/' . $slug . '/kallang-wave-mall">Kallang Wave Mall</a></li>';
	if ( false === strpos( $html, $anchor ) ) {
		echo "{$label}: submenu anchor not found, aborted without saving\n";
		return;
	}
	$html = str_replace(
		$anchor,
		'<li><a href="/' . $slug . '/">All Outlets &rarr;</a></li>' . "\n              " . $anchor,
		$html
	);

	echo "{$label}: parent linked to /{$slug}/ + All Outlets item added\n";
}

$done = false;
$set  = function ( $elements ) use ( &$set, $widget_id, $html, &$done ) {
	foreach ( $elements as $i => $element ) {
		if ( ( isset( $element['id'] ) ? $element['id'] : '' ) === $widget_id ) {
			$elements[ $i ]['settings']['html'] = $html;
			$done = true;
			return $elements;
		}
		if ( ! empty( $element['elements'] ) ) {
			$elements[ $i ]['elements'] = $set( $element['elements'] );
			if ( $done ) {
				return $elements;
			}
		}
	}
	return $elements;
};

$data = $set( $data );
if ( ! $done ) {
	echo "write-back failed, aborted\n";
	return;
}

update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) );
delete_post_meta( $post_id, '_elementor_element_cache' );

echo "header nav updated\n";

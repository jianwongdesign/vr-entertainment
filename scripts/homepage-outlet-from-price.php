<?php
/**
 * Add a "from" price row to the three cards in the homepage OUTLETS widget
 * (page 16, widget b63910e). The row sits below the game pills and above the
 * Book / Learn More buttons, and pulls the cheapest weekday and weekend price
 * for that outlet live from the pricing_item CPT via [ow_outlet_from].
 *
 * Idempotent: re-running does nothing once the shortcodes are in place.
 * Run: wp eval-file homepage-outlet-from-price.php
 */

$page_id   = 16;
$widget_id = 'b63910e';

$data = json_decode( get_post_meta( $page_id, '_elementor_data', true ), true );
if ( ! is_array( $data ) ) {
	echo "page {$page_id}: no _elementor_data, aborted\n";
	return;
}

// ===== Read the widget's current HTML =====
$find_html = function ( $elements ) use ( &$find_html, $widget_id ) {
	foreach ( $elements as $element ) {
		$id = isset( $element['id'] ) ? $element['id'] : '';
		if ( $id === $widget_id ) {
			return isset( $element['settings']['html'] ) ? $element['settings']['html'] : null;
		}
		if ( ! empty( $element['elements'] ) ) {
			$found = $find_html( $element['elements'] );
			if ( null !== $found ) {
				return $found;
			}
		}
	}
	return null;
};

$html = $find_html( $data );
if ( null === $html ) {
	echo "widget {$widget_id}: not found on page {$page_id}, aborted\n";
	return;
}

if ( false !== strpos( $html, '[ow_outlet_from' ) ) {
	echo "widget {$widget_id}: price row already present, nothing to do\n";
	return;
}

// ===== Per-card accent for the weekend figure, matching the brand colour =====
$accent_css = "  .ow-outlets-a .card.vr{--ow-fp-accent:#6f9bff;}\n"
	. "  .ow-outlets-a .card.lava{--ow-fp-accent:#ff8a3d;}\n"
	. "  .ow-outlets-a .card.soon{--ow-fp-accent:#c89aff;}\n"
	// margin-top:auto pins the price row to the bottom of the card so all
	// three line up regardless of how many game pills sit above it; the
	// buttons then follow directly underneath.
	. "  .ow-outlets-a .ow-fp{margin-top:auto;margin-bottom:22px;}\n"
	. "  .ow-outlets-a .ow-fp + .actions{margin-top:0;}\n";

$style_end = strrpos( $html, '</style>' );
if ( false === $style_end ) {
	echo "widget {$widget_id}: no </style> block, aborted\n";
	return;
}
$html = substr( $html, 0, $style_end ) . $accent_css . substr( $html, $style_end );

// ===== One shortcode per card, anchored on its unique booking link =====
$cards = array(
	'/book-now-kwm/'     => 'kallang-wave-mall',
	'/book-now-orchard/' => 'orchard-central',
	'/book-now-funan/'   => 'funan',
);

$inserted = array();
foreach ( $cards as $book_url => $slug ) {
	$pattern     = '~(<div class="actions">\s*<a href="' . preg_quote( $book_url, '~' ) . '")~';
	$replacement = '[ow_outlet_from outlet="' . $slug . '"]' . "\n          " . '$1';
	$count       = 0;
	$updated     = preg_replace( $pattern, $replacement, $html, -1, $count );

	if ( null === $updated || 1 !== $count ) {
		echo "card {$slug}: expected 1 match, got {$count} — aborted without saving\n";
		return;
	}
	$html       = $updated;
	$inserted[] = $slug;
}

// ===== Write the widget back =====
$done     = false;
$set_html = function ( $elements ) use ( &$set_html, $widget_id, $html, &$done ) {
	foreach ( $elements as $i => $element ) {
		$id = isset( $element['id'] ) ? $element['id'] : '';
		if ( $id === $widget_id ) {
			$elements[ $i ]['settings']['html'] = $html;
			$done                              = true;
			return $elements;
		}
		if ( ! empty( $element['elements'] ) ) {
			$elements[ $i ]['elements'] = $set_html( $element['elements'] );
			if ( $done ) {
				return $elements;
			}
		}
	}
	return $elements;
};

$data = $set_html( $data );
if ( ! $done ) {
	echo "widget {$widget_id}: write-back failed, aborted\n";
	return;
}

update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) );
delete_post_meta( $page_id, '_elementor_element_cache' );
delete_transient( 'ow_outlet_from_prices' );

echo "page {$page_id} widget {$widget_id}: price row added for " . implode( ', ', $inserted ) . "\n";

<?php
/**
 * Fill in two sample Combo Deals on the Orchard Central outlet page (505) so
 * the client can see the new section and edit it from WP Admin → Pages →
 * Orchard Central → Combo Deals.
 *
 * Prices are deliberately left empty: the template then pulls them from the
 * matching combo rows in the Pricing CPT ($23 weekday / $27 weekend), which
 * demonstrates that the section stays in step with Pricing on its own.
 *
 * Images reuse attachments already on that page's activity cards.
 * Run: wp eval-file sample-outlet-combos.php
 */

$page_id = 505;

$intro = 'Two games, one visit, one price — our Orchard bundles cost less than booking each game on its own.';

$combos = array(
	1 => array(
		'title'    => 'Floor Is Lava + Laser Maze',
		'badge'    => 'Save $9',
		'desc'     => 'Dodge the lava floor, then weave the laser grid. Back-to-back sessions with a breather in between.',
		'includes' => '25 min Floor Is Lava, 25 min Laser Maze',
		'link'     => '/book-now-orchard/',
		'image'    => 1425,
	),
	2 => array(
		'title'    => 'Floor Is Lava + Tap Tap',
		'badge'    => 'Most Popular',
		'desc'     => 'Our fastest, sweatiest pairing — full-body lava dodging followed by a race against the light wall.',
		'includes' => '25 min Floor Is Lava, 25 min Tap Tap',
		'link'     => '/book-now-orchard/',
		'image'    => 1536,
	),
);

// ACF needs the value AND the field-key reference, otherwise the admin boxes
// render empty even though the meta is there.
update_post_meta( $page_id, 'outlet_combos_intro', $intro );
update_post_meta( $page_id, '_outlet_combos_intro', 'field_outlet_combos_intro' );

foreach ( $combos as $i => $combo ) {
	$map = array(
		'title'    => $combo['title'],
		'badge'    => $combo['badge'],
		'desc'     => $combo['desc'],
		'includes' => $combo['includes'],
		'link'     => $combo['link'],
		'image'    => $combo['image'],
		'weekday'  => '',
		'weekend'  => '',
	);
	foreach ( $map as $field => $value ) {
		update_post_meta( $page_id, "outlet_combo_{$i}_{$field}", $value );
		update_post_meta( $page_id, "_outlet_combo_{$i}_{$field}", "field_outlet_combo_{$i}_{$field}" );
	}
	echo "combo {$i}: {$combo['title']}\n";
}

delete_post_meta( $page_id, '_elementor_element_cache' );

echo "page {$page_id}: sample combos written\n";

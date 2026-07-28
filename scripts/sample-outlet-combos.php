<?php
/**
 * Fill in the Combo Deals section on the outlet pages so the client can see
 * it and edit it from WP Admin → Pages → <outlet> → Combo Deals.
 *
 * Only combos that genuinely exist in the Pricing CPT are written here.
 * Prices are deliberately left empty: the template resolves them from the
 * matching combo rows in Pricing, which keeps this section in step with the
 * pricing tables lower down the page.
 *
 * Kallang (504) is intentionally absent — that outlet has no combo rows in
 * Pricing, so there is nothing truthful to publish until the client says
 * which bundles they sell and at what price.
 *
 * Images reuse attachments already on each page's activity cards.
 * Idempotent. Run: wp eval-file sample-outlet-combos.php
 */

$pages = array(

	// Orchard Central
	505 => array(
		'intro'  => 'Two games, one visit, one price — our Orchard bundles cost less than booking each game on its own.',
		'combos' => array(
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
		),
	),

	// Funan
	506 => array(
		'intro'  => 'Pair up two Funan experiences in one visit and pay less than booking them separately.',
		'combos' => array(
			1 => array(
				'title'    => 'Floor Is Lava + XR Party Game',
				'badge'    => 'Save $9',
				'desc'     => 'Burn off the energy on the lava floor, then take the whole group onto the big screen for XR party rounds.',
				'includes' => '25 min Floor Is Lava, 25 min XR Party Game',
				'link'     => '/book-now-funan/',
				'image'    => 1236,
			),
		),
	),
);

foreach ( $pages as $page_id => $page ) {

	// ACF needs the value AND the field-key reference, otherwise the admin
	// boxes render empty even though the meta is there.
	update_post_meta( $page_id, 'outlet_combos_intro', $page['intro'] );
	update_post_meta( $page_id, '_outlet_combos_intro', 'field_outlet_combos_intro' );

	foreach ( $page['combos'] as $i => $combo ) {
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
		echo "page {$page_id} combo {$i}: {$combo['title']}\n";
	}

	delete_post_meta( $page_id, '_elementor_element_cache' );
}

echo "done\n";

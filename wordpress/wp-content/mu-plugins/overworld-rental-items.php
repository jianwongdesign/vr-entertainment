<?php
/**
 * Plugin Name: Overworld — Rental Items
 * Description: Adds the "Rental Items" content type behind the activity cards on /event-rental/. Each item the client adds gets its own page (/event-rental/<name>/) showing what is in the package, what the rental covers, the space it needs, photos and FAQs, and a card on the Equipment Rental page with a tag ("Crowd-Puller") and highlight chips ("2–6 Players", "Ages 7+").
 * Author: Overworld
 * Version: 1.0.0
 *
 * Must-use plugin: auto-loads, no activation needed.
 *
 * WHY A CONTENT TYPE AND NOT MORE SLOTS ON THE RENTAL PAGE:
 * the client asked for each rental card to open its own page listing what is
 * in the package and what is covered, the way packages and promos do.
 * Numbered slots on the rental page could show a card but never give it a
 * page, so rental items are posts of their own — "Rental Items" in the WP
 * Admin sidebar, Add New, fill in, publish, and the card appears on the
 * rental page by itself. Same reasoning as overworld-event-gallery.php.
 *
 * HOW THE CARDS ON THE RENTAL PAGE ARE CHOSEN (ow_rental_activities()):
 *   1. published Rental Items with "Show on the Equipment Rental page" on;
 *   2. else the old numbered Activity slots on the rental page;
 *   3. else the three built-in cards.
 * So the page never goes empty while the client is between states.
 *
 * Everything is optional. A title, a cover photo and a few highlights make a
 * finished page. "What's Covered" falls back to the rental page's own
 * "What's Included In Every Rental" list, so the client fills it in only when
 * one item differs. Lists are numbered slots, not repeaters: the site runs
 * ACF free.
 *
 * The page template (single-rental_item.php) reuses the Past Event page's
 * stylesheet (ow_pe_styles) for the hero, sections, photo grid, lightbox and
 * call to action, and adds its own rules (ow_ri_styles) for the parts only a
 * rental item has. Both mu-plugins always load together.
 *
 * NOTE: registering a post type adds rewrite rules. After deploying this file
 * for the first time, flush them once — scripts/deploy-rental-items.sh does
 * it — or /event-rental/<name>/ URLs 404.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_RI_POST_TYPE       = 'rental_item';
const OW_RI_SLUG            = 'event-rental';
const OW_RI_HIGHLIGHT_SLOTS = 4;
const OW_RI_PACK_SLOTS      = 8;
const OW_RI_INC_SLOTS       = 6;
const OW_RI_SPEC_SLOTS      = 6;
const OW_RI_PHOTO_SLOTS     = 8;
const OW_RI_FAQ_SLOTS       = 4;

/**
 * Built-in copy for a rental item page.
 *
 * @return array
 */
function ow_ri_defaults() {
	return array(
		'eyebrow'     => 'Event Rental · Singapore',
		'back_label'  => 'All Rental Activities',
		'about_title' => 'About This Rental',
		'pack_title'  => "What's In The Package",
		'inc_title'   => "What's Covered",
		'spec_title'  => 'Space & Setup',
		'photos_title' => 'Photos',
		'faq_title'   => 'Good To Know',
		'more_title'  => 'Pair It With',
		'cta_eyebrow' => 'Plan Your Event With Us',
		'cta_title'   => '', // Built from the item's name: "Bring VR Free Roam To Your Event".
		'cta_text'    => 'Tell us your venue, date and headcount. We will come back with the setup that fits your space and a quote — delivery, setup, crew and pack-down included.',
		'cta_label'   => 'Get A Quote',
		'cta_url'     => '/contact/',
	);
}

/**
 * A field on a rental item, falling back to the built-in copy.
 *
 * @param int    $post_id Rental item ID.
 * @param string $name    Meta key without the 'ri_' prefix.
 * @return string
 */
function ow_ri_val( $post_id, $name ) {
	$value = trim( (string) get_post_meta( $post_id, 'ri_' . $name, true ) );
	if ( '' !== $value ) {
		return $value;
	}

	if ( 'cta_title' === $name ) {
		return sprintf( 'Bring %s To Your Event', get_the_title( $post_id ) );
	}

	$defaults = ow_ri_defaults();

	return isset( $defaults[ $name ] ) ? (string) $defaults[ $name ] : '';
}

/**
 * A relative URL from the client made absolute; anchors and absolute URLs are
 * left alone. Same rules as ow_rental_url() and ow_pe_url().
 *
 * @param string $url URL as typed.
 * @return string
 */
function ow_ri_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( '#' === $url[0] || preg_match( '#^[a-z][a-z0-9+.-]*:#i', $url ) ) {
		return $url;
	}

	return home_url( '/' . ltrim( $url, '/' ) );
}

/**
 * An attachment ID, or 0 when it no longer points at one.
 *
 * @param mixed $id Stored value.
 * @return int
 */
function ow_ri_attachment( $id ) {
	$id = (int) $id;

	return ( $id && 'attachment' === get_post_type( $id ) ) ? $id : 0;
}

/**
 * The rental page's URL, for the "back" link. Looked up by template so the
 * page can be re-slugged.
 *
 * @return string
 */
function ow_ri_rental_page_url() {
	if ( function_exists( 'ow_rental_page_ids' ) ) {
		foreach ( ow_rental_page_ids() as $id ) {
			if ( 'publish' === get_post_status( $id ) ) {
				return get_permalink( $id );
			}
		}
	}

	return home_url( '/' . OW_RI_SLUG . '/' );
}

/**
 * The cover: the Cover Photo field, else the featured image, else the first
 * gallery photo.
 *
 * @param int $post_id Rental item ID.
 * @return int Attachment ID, or 0.
 */
function ow_ri_cover_id( $post_id ) {
	$id = ow_ri_attachment( get_post_meta( $post_id, 'ri_cover', true ) );
	if ( $id ) {
		return $id;
	}

	$id = ow_ri_attachment( get_post_thumbnail_id( $post_id ) );
	if ( $id ) {
		return $id;
	}

	$photos = ow_ri_photos( $post_id );

	return $photos ? $photos[0] : 0;
}

/**
 * Gallery photos in slot order.
 *
 * @param int $post_id Rental item ID.
 * @return int[]
 */
function ow_ri_photos( $post_id ) {
	$ids = array();
	for ( $i = 1; $i <= OW_RI_PHOTO_SLOTS; $i++ ) {
		$id = ow_ri_attachment( get_post_meta( $post_id, "ri_photo_{$i}", true ) );
		if ( $id ) {
			$ids[] = $id;
		}
	}

	return $ids;
}

/**
 * The highlight chips — "2–6 Players", "Ages 7+", "20+ Games". Shown on the
 * card and big in the page's hero, the same facts the game pages lead with.
 *
 * @param int $post_id Rental item ID.
 * @return array<int, array{icon: string, text: string}>
 */
function ow_ri_highlights( $post_id ) {
	$rows = array();
	for ( $i = 1; $i <= OW_RI_HIGHLIGHT_SLOTS; $i++ ) {
		$text = trim( (string) get_post_meta( $post_id, "ri_hl_{$i}_text", true ) );
		if ( '' === $text ) {
			continue;
		}
		$rows[] = array(
			'icon' => sanitize_key( (string) get_post_meta( $post_id, "ri_hl_{$i}_icon", true ) ),
			'text' => $text,
		);
	}

	return $rows;
}

/**
 * "What's In The Package" — the equipment and content the client gets.
 *
 * @param int $post_id Rental item ID.
 * @return array<int, array{label: string, note: string}>
 */
function ow_ri_pack( $post_id ) {
	$rows = array();
	for ( $i = 1; $i <= OW_RI_PACK_SLOTS; $i++ ) {
		$label = trim( (string) get_post_meta( $post_id, "ri_pack_{$i}_label", true ) );
		if ( '' === $label ) {
			continue;
		}
		$rows[] = array(
			'label' => $label,
			'note'  => trim( (string) get_post_meta( $post_id, "ri_pack_{$i}_note", true ) ),
		);
	}

	return $rows;
}

/**
 * "What's Covered" — the service side. The item's own list when any slot is
 * filled, otherwise the rental page's "What's Included In Every Rental", so
 * the two pages can never disagree unless the client means them to.
 *
 * @param int $post_id Rental item ID.
 * @return array<int, array{icon: string, label: string}>
 */
function ow_ri_included( $post_id ) {
	$rows = array();
	for ( $i = 1; $i <= OW_RI_INC_SLOTS; $i++ ) {
		$label = trim( (string) get_post_meta( $post_id, "ri_inc_{$i}_label", true ) );
		if ( '' === $label ) {
			continue;
		}
		$rows[] = array(
			'icon'  => sanitize_key( (string) get_post_meta( $post_id, "ri_inc_{$i}_icon", true ) ),
			'label' => $label,
		);
	}
	if ( $rows ) {
		return $rows;
	}

	if ( function_exists( 'ow_rental_icon_list' ) && function_exists( 'ow_rental_defaults' ) ) {
		$ids      = function_exists( 'ow_rental_page_ids' ) ? ow_rental_page_ids() : array();
		$defaults = ow_rental_defaults();

		return ow_rental_icon_list(
			$ids ? (int) $ids[0] : 0,
			'inc',
			defined( 'OW_RENTAL_INCLUDED_SLOTS' ) ? OW_RENTAL_INCLUDED_SLOTS : 6,
			isset( $defaults['included'] ) ? $defaults['included'] : array()
		);
	}

	return array();
}

/**
 * "Space & Setup" rows — label / value pairs.
 *
 * @param int $post_id Rental item ID.
 * @return array<int, array{label: string, value: string}>
 */
function ow_ri_specs( $post_id ) {
	$rows = array();
	for ( $i = 1; $i <= OW_RI_SPEC_SLOTS; $i++ ) {
		$label = trim( (string) get_post_meta( $post_id, "ri_spec_{$i}_label", true ) );
		$value = trim( (string) get_post_meta( $post_id, "ri_spec_{$i}_value", true ) );
		if ( '' === $label || '' === $value ) {
			continue;
		}
		$rows[] = array( 'label' => $label, 'value' => $value );
	}

	return $rows;
}

/**
 * The item's own questions.
 *
 * @param int $post_id Rental item ID.
 * @return array<int, array{q: string, a: string}>
 */
function ow_ri_faqs( $post_id ) {
	$rows = array();
	for ( $i = 1; $i <= OW_RI_FAQ_SLOTS; $i++ ) {
		$q = trim( (string) get_post_meta( $post_id, "ri_faq_{$i}_q", true ) );
		$a = trim( (string) get_post_meta( $post_id, "ri_faq_{$i}_a", true ) );
		if ( '' === $q || '' === $a ) {
			continue;
		}
		$rows[] = array( 'q' => $q, 'a' => $a );
	}

	return $rows;
}

/**
 * One card's worth of data. The keys match the rows ow_rental_activities()
 * has always returned (title, text, url, image_id, image_url) plus the new
 * badge, highlights and price, so the rental page renders either kind.
 *
 * @param WP_Post|int $post Rental item.
 * @return array
 */
function ow_ri_card( $post ) {
	$post = get_post( $post );
	if ( ! $post instanceof WP_Post ) {
		return array();
	}

	$cover = ow_ri_cover_id( $post->ID );

	return array(
		'id'         => $post->ID,
		'title'      => get_the_title( $post ),
		'text'       => trim( (string) get_post_meta( $post->ID, 'ri_tagline', true ) ),
		'url'        => get_permalink( $post ),
		'image_id'   => $cover,
		'image_url'  => $cover ? (string) wp_get_attachment_image_url( $cover, 'large' ) : '',
		'badge'      => trim( (string) get_post_meta( $post->ID, 'ri_badge', true ) ),
		'highlights' => ow_ri_highlights( $post->ID ),
		'price'      => trim( (string) get_post_meta( $post->ID, 'ri_price', true ) ),
	);
}

/**
 * Published rental items for a card grid, in the Order set on each item
 * (Page Attributes → Order), then A–Z.
 *
 * @param int  $limit         How many (-1 for all).
 * @param bool $featured_only Only the ones switched on for the rental page.
 * @param int  $exclude       A post ID to leave out (the one being viewed).
 * @return array<int, array>
 */
function ow_ri_cards( $limit = -1, $featured_only = true, $exclude = 0 ) {
	$args = array(
		'post_type'           => OW_RI_POST_TYPE,
		'post_status'         => 'publish',
		'posts_per_page'      => (int) $limit,
		'orderby'             => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	);

	if ( $exclude ) {
		$args['post__not_in'] = array( (int) $exclude );
	}

	if ( $featured_only ) {
		// NOT EXISTS covers items saved before the field existed.
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key'     => 'ri_featured',
				'value'   => '1',
				'compare' => '=',
			),
			array(
				'key'     => 'ri_featured',
				'compare' => 'NOT EXISTS',
			),
		);
	}

	$cards = array();
	foreach ( get_posts( $args ) as $post ) {
		$cards[] = ow_ri_card( $post );
	}

	return $cards;
}

// ===== The content type =====

add_action( 'init', function () {
	register_post_type(
		OW_RI_POST_TYPE,
		array(
			'labels'        => array(
				'name'                  => 'Rental Items',
				'singular_name'         => 'Rental Item',
				'menu_name'             => 'Rental Items',
				'add_new'               => 'Add Rental Item',
				'add_new_item'          => 'Add Rental Item',
				'edit_item'             => 'Edit Rental Item',
				'new_item'              => 'New Rental Item',
				'view_item'             => 'View Rental Item',
				'view_items'            => 'View Rental Items',
				'search_items'          => 'Search Rental Items',
				'not_found'             => 'No rental items yet. Add your first one.',
				'not_found_in_trash'    => 'Nothing in the bin.',
				'all_items'             => 'All Rental Items',
				'featured_image'        => 'Cover Photo',
				'set_featured_image'    => 'Set cover photo',
				'remove_featured_image' => 'Remove cover photo',
				'use_featured_image'    => 'Use as cover photo',
			),
			'description'   => 'Activities rented out for events. Each one gets its own page and a card on the Equipment Rental page.',
			'public'        => true,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => true,
			'menu_position' => 20,
			'menu_icon'     => 'dashicons-tickets-alt',
			// The rental page itself is the listing; no second archive.
			'has_archive'   => false,
			'rewrite'       => array(
				'slug'       => OW_RI_SLUG,
				'with_front' => false,
			),
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			'map_meta_cap'  => true,
		)
	);
} );

add_filter( 'enter_title_here', function ( $text, $post ) {
	if ( $post instanceof WP_Post && OW_RI_POST_TYPE === $post->post_type ) {
		return 'Activity name — e.g. "VR Free Roam"';
	}

	return $text;
}, 10, 2 );

/**
 * A pointer at the top of the Rental Items list.
 */
add_action( 'admin_notices', function () {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'edit-' . OW_RI_POST_TYPE !== $screen->id ) {
		return;
	}

	echo '<div class="notice notice-info"><p>';
	echo 'Each entry here is one activity you rent out for events. It gets its own page, and a card on the <a href="'
		. esc_url( ow_ri_rental_page_url() ) . '">Equipment Rental page</a>. ';
	echo 'To change the order of the cards, open an item and set <strong>Order</strong> in the right-hand panel (lower numbers come first).';
	echo '</p></div>';
} );

// ===== The edit screen (ACF) =====

add_action( 'acf/init', function () {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$d = ow_ri_defaults();

	$icon_choices = array( '' => '— pick an icon —' );
	if ( function_exists( 'ow_rental_icons' ) ) {
		foreach ( ow_rental_icons() as $key => $icon ) {
			$icon_choices[ $key ] = $icon['label'];
		}
	}

	$fields = array();

	$fields[] = array(
		'key'       => 'field_ri_howto',
		'label'     => 'Adding a rental item, in 4 steps',
		'name'      => '',
		'type'      => 'message',
		'message'   => '<ol style="margin:0 0 0 1.2em">'
			. '<li>Type the activity name in the title at the top.</li>'
			. '<li>Describe it in the writing area under the title — what it is and why it works at events.</li>'
			. '<li>Below, under <strong>The Card</strong>, add a Cover Photo, a tag and the highlights. Then list <strong>What\'s In The Package</strong>.</li>'
			. '<li>Click <strong>Publish</strong>. The item gets its own page and a card on the Equipment Rental page.</li>'
			. '</ol>'
			. '<p style="margin:.6em 0 0">Everything else is optional — empty sections are simply left off the page.</p>',
		'new_lines' => '',
		'esc_html'  => 0,
	);

	// --- The card ---
	$fields[] = array(
		'key'          => 'field_ri_card_tab',
		'label'        => 'The Card (how this item looks on the Equipment Rental page)',
		'type'         => 'accordion',
		'open'         => 1,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'           => 'field_ri_cover',
		'label'         => 'Cover Photo',
		'name'          => 'ri_cover',
		'type'          => 'image',
		'instructions'  => 'On the card and across the top of the item\'s page. Landscape, at least 1600px wide.',
		'return_format' => 'id',
		'preview_size'  => 'medium',
		'required'      => 0,
		'wrapper'       => array( 'width' => '30' ),
	);
	$fields[] = array(
		'key'          => 'field_ri_badge',
		'label'        => 'Tag',
		'name'         => 'ri_badge',
		'type'         => 'text',
		'instructions' => 'The bright label on the photo, like the combo deals — "Crowd-Puller", "Best Seller", "New". Two or three words. Leave empty for no tag.',
		'required'     => 0,
		'wrapper'      => array( 'width' => '35' ),
	);
	$fields[] = array(
		'key'          => 'field_ri_price',
		'label'        => 'Price Line (optional)',
		'name'         => 'ri_price',
		'type'         => 'text',
		'instructions' => 'e.g. "From $1,200 / 4 hours" or "Price on request". Leave empty to show no price.',
		'required'     => 0,
		'wrapper'      => array( 'width' => '35' ),
	);
	$fields[] = array(
		'key'          => 'field_ri_tagline',
		'label'        => 'One Or Two Lines For The Card',
		'name'         => 'ri_tagline',
		'type'         => 'textarea',
		'rows'         => 2,
		'instructions' => 'Under the name on the card, and the description Google shows. Write it for someone who has never heard of the game.',
		'required'     => 0,
	);
	for ( $i = 1; $i <= OW_RI_HIGHLIGHT_SLOTS; $i++ ) {
		$fields[] = array(
			'key'          => "field_ri_hl_{$i}_icon",
			'label'        => "Highlight {$i} — Icon",
			'name'         => "ri_hl_{$i}_icon",
			'type'         => 'select',
			'choices'      => $icon_choices,
			'instructions' => 1 === $i ? 'The quick facts, like on the game pages — "2–6 Players", "Ages 7+", "20+ Games", "No Headset". Shown on the card and big at the top of the page.' : '',
			'allow_null'   => 0,
			'ui'           => 0,
			'required'     => 0,
			'wrapper'      => array( 'width' => '20' ),
		);
		$fields[] = array(
			'key'      => "field_ri_hl_{$i}_text",
			'label'    => "Highlight {$i} — Text",
			'name'     => "ri_hl_{$i}_text",
			'type'     => 'text',
			'required' => 0,
			'wrapper'  => array( 'width' => '30' ),
		);
	}
	$fields[] = array(
		'key'           => 'field_ri_featured',
		'label'         => 'Show On The Equipment Rental Page',
		'name'          => 'ri_featured',
		'type'          => 'true_false',
		'instructions'  => 'Off: the item keeps its own page but has no card on the rental page. Useful while an item is being prepared.',
		'ui'            => 1,
		'default_value' => 1,
		'required'      => 0,
	);

	// --- Top of the page ---
	$fields[] = array(
		'key'          => 'field_ri_top_tab',
		'label'        => 'Top Of The Item Page',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_ri_eyebrow',
		'label'        => 'Small Line Above The Title',
		'name'         => 'ri_eyebrow',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['eyebrow'] . '"',
		'required'     => 0,
	);
	$fields[] = array(
		'key'          => 'field_ri_intro',
		'label'        => 'Text Under The Title',
		'name'         => 'ri_intro',
		'type'         => 'textarea',
		'rows'         => 3,
		'instructions' => 'A short lead paragraph. Leave empty and the card text is used.',
		'required'     => 0,
	);
	$fields[] = array(
		'key'          => 'field_ri_about_title',
		'label'        => 'Heading Above The Description',
		'name'         => 'ri_about_title',
		'type'         => 'text',
		'instructions' => 'The description itself is the normal editor above this box. Default heading: "' . $d['about_title'] . '"',
		'required'     => 0,
	);

	// --- What's in the package ---
	$fields[] = array(
		'key'          => 'field_ri_pack_tab',
		'label'        => "What's In The Package (up to " . OW_RI_PACK_SLOTS . ')',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_ri_pack_title',
		'label'        => 'Section Heading',
		'name'         => 'ri_pack_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['pack_title'] . '"',
		'required'     => 0,
	);
	for ( $i = 1; $i <= OW_RI_PACK_SLOTS; $i++ ) {
		$fields[] = array(
			'key'          => "field_ri_pack_{$i}_label",
			'label'        => "Item {$i}",
			'name'         => "ri_pack_{$i}_label",
			'type'         => 'text',
			'instructions' => 1 === $i ? 'What the client gets — equipment, games, set-pieces. "6 VR headsets", "20+ games to choose from". Empty slots are skipped.' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '45' ),
		);
		$fields[] = array(
			'key'          => "field_ri_pack_{$i}_note",
			'label'        => "Item {$i} — Small Detail (optional)",
			'name'         => "ri_pack_{$i}_note",
			'type'         => 'text',
			'instructions' => 1 === $i ? 'A grey line under the item.' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '55' ),
		);
	}

	// --- What's covered ---
	$fields[] = array(
		'key'          => 'field_ri_inc_tab',
		'label'        => "What's Covered (up to " . OW_RI_INC_SLOTS . ')',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'       => 'field_ri_inc_msg',
		'label'     => '',
		'name'      => '',
		'type'      => 'message',
		'message'   => 'Leave these empty and the page shows the <strong>What\'s Included In Every Rental</strong> list from the Equipment Rental page (delivery, setup, operators, support, briefing, pack-down). Fill them in only when this item covers something different — filling in any slot replaces that list for this item.',
		'new_lines' => '',
		'esc_html'  => 0,
	);
	$fields[] = array(
		'key'          => 'field_ri_inc_title',
		'label'        => 'Section Heading',
		'name'         => 'ri_inc_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['inc_title'] . '"',
		'required'     => 0,
	);
	for ( $i = 1; $i <= OW_RI_INC_SLOTS; $i++ ) {
		$fields[] = array(
			'key'        => "field_ri_inc_{$i}_icon",
			'label'      => "Covered {$i} — Icon",
			'name'       => "ri_inc_{$i}_icon",
			'type'       => 'select',
			'choices'    => $icon_choices,
			'allow_null' => 0,
			'ui'         => 0,
			'required'   => 0,
			'wrapper'    => array( 'width' => '20' ),
		);
		$fields[] = array(
			'key'      => "field_ri_inc_{$i}_label",
			'label'    => "Covered {$i} — Text",
			'name'     => "ri_inc_{$i}_label",
			'type'     => 'text',
			'required' => 0,
			'wrapper'  => array( 'width' => '30' ),
		);
	}

	// --- Space & setup ---
	$fields[] = array(
		'key'          => 'field_ri_spec_tab',
		'label'        => 'Space & Setup (up to ' . OW_RI_SPEC_SLOTS . ' rows)',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_ri_spec_title',
		'label'        => 'Section Heading',
		'name'         => 'ri_spec_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['spec_title'] . '"',
		'required'     => 0,
	);
	for ( $i = 1; $i <= OW_RI_SPEC_SLOTS; $i++ ) {
		$fields[] = array(
			'key'          => "field_ri_spec_{$i}_label",
			'label'        => "Row {$i} — Label",
			'name'         => "ri_spec_{$i}_label",
			'type'         => 'text',
			'instructions' => 1 === $i ? 'What a venue needs to know — "Play area", "Power", "Setup time", "Players per round". Both boxes must be filled for the row to show.' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '30' ),
		);
		$fields[] = array(
			'key'      => "field_ri_spec_{$i}_value",
			'label'    => "Row {$i} — Value",
			'name'     => "ri_spec_{$i}_value",
			'type'     => 'text',
			'required' => 0,
			'wrapper'  => array( 'width' => '70' ),
		);
	}

	// --- Photos ---
	$fields[] = array(
		'key'          => 'field_ri_photos_tab',
		'label'        => 'Photos (up to ' . OW_RI_PHOTO_SLOTS . ')',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_ri_photos_title',
		'label'        => 'Section Heading',
		'name'         => 'ri_photos_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['photos_title'] . '". Photo 1 is the large tile; clicking any photo opens it full size.',
		'required'     => 0,
	);
	for ( $i = 1; $i <= OW_RI_PHOTO_SLOTS; $i++ ) {
		$fields[] = array(
			'key'           => "field_ri_photo_{$i}",
			'label'         => 'Photo ' . $i . ( 1 === $i ? ' — large tile' : '' ),
			'name'          => "ri_photo_{$i}",
			'type'          => 'image',
			'return_format' => 'id',
			'preview_size'  => 'thumbnail',
			'required'      => 0,
			'wrapper'       => array( 'width' => '25' ),
		);
	}

	// --- FAQ ---
	$fields[] = array(
		'key'          => 'field_ri_faq_tab',
		'label'        => 'Questions (up to ' . OW_RI_FAQ_SLOTS . ')',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_ri_faq_title',
		'label'        => 'Section Heading',
		'name'         => 'ri_faq_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['faq_title'] . '"',
		'required'     => 0,
	);
	for ( $i = 1; $i <= OW_RI_FAQ_SLOTS; $i++ ) {
		$fields[] = array(
			'key'          => "field_ri_faq_{$i}_q",
			'label'        => "Question {$i}",
			'name'         => "ri_faq_{$i}_q",
			'type'         => 'text',
			'instructions' => 1 === $i ? 'Questions about this activity only — general rental questions are already on the Equipment Rental page.' : '',
			'required'     => 0,
		);
		$fields[] = array(
			'key'      => "field_ri_faq_{$i}_a",
			'label'    => "Answer {$i}",
			'name'     => "ri_faq_{$i}_a",
			'type'     => 'textarea',
			'rows'     => 3,
			'required' => 0,
		);
	}

	// --- CTA ---
	$fields[] = array(
		'key'          => 'field_ri_cta_tab',
		'label'        => 'Bottom Call To Action',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_ri_cta_title',
		'label'        => 'Heading',
		'name'         => 'ri_cta_title',
		'type'         => 'text',
		'instructions' => 'Default: "Bring [item name] To Your Event"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key'          => 'field_ri_cta_text',
		'label'        => 'Text',
		'name'         => 'ri_cta_text',
		'type'         => 'textarea',
		'rows'         => 2,
		'instructions' => 'Default: the standard quote wording.',
		'required'     => 0,
		'wrapper'      => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key'          => 'field_ri_cta_label',
		'label'        => 'Button — Text',
		'name'         => 'ri_cta_label',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['cta_label'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key'          => 'field_ri_cta_url',
		'label'        => 'Button — Link',
		'name'         => 'ri_cta_url',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['cta_url'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key'      => 'field_ri_end',
		'label'    => '',
		'type'     => 'accordion',
		'endpoint' => 1,
	);

	acf_add_local_field_group( array(
		'key'             => 'group_ow_rental_item',
		'title'           => 'Rental Item Details',
		'fields'          => $fields,
		'location'        => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => OW_RI_POST_TYPE,
				),
			),
		),
		'menu_order'      => 2,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
		'description'     => 'Everything about this rental item. All optional — a title, a cover photo and a few highlights already make a finished page.',
	) );
} );

/**
 * After the client saves an item: the cover photo becomes the featured image
 * (sharing previews), the card text becomes the excerpt when there is none
 * (meta description), and Elementor's render cache is cleared.
 */
add_action( 'acf/save_post', function ( $post_id ) {
	if ( ! is_numeric( $post_id ) || OW_RI_POST_TYPE !== get_post_type( $post_id ) ) {
		return;
	}
	$post_id = (int) $post_id;

	$cover = ow_ri_attachment( get_post_meta( $post_id, 'ri_cover', true ) );
	if ( $cover && (int) get_post_thumbnail_id( $post_id ) !== $cover ) {
		set_post_thumbnail( $post_id, $cover );
	}

	$tagline = trim( (string) get_post_meta( $post_id, 'ri_tagline', true ) );
	$post    = get_post( $post_id );
	if ( '' !== $tagline && $post instanceof WP_Post && '' === trim( (string) $post->post_excerpt ) ) {
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_excerpt' => wp_strip_all_tags( $tagline ),
			)
		);
	}

	delete_post_meta( $post_id, '_elementor_element_cache' );
}, 20 );

/**
 * A rental item shows on the Equipment Rental page, which LiteSpeed knows
 * nothing about when the item is saved. Purge it so the card updates.
 *
 * @return void
 */
function ow_ri_purge_related_pages() {
	if ( function_exists( 'ow_rental_page_ids' ) ) {
		foreach ( ow_rental_page_ids() as $page_id ) {
			do_action( 'litespeed_purge_post', $page_id );
			delete_post_meta( $page_id, '_elementor_element_cache' );
		}
	}

	// Each item's page shows the others in "Pair It With".
	do_action( 'litespeed_purge_posttype', OW_RI_POST_TYPE );
}

add_action(
	'save_post_' . OW_RI_POST_TYPE,
	function ( $post_id ) {
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		ow_ri_purge_related_pages();
	},
	20
);

foreach ( array( 'trashed_post', 'untrashed_post', 'deleted_post' ) as $ow_ri_event ) {
	add_action(
		$ow_ri_event,
		function ( $post_id ) {
			if ( OW_RI_POST_TYPE === get_post_type( $post_id ) ) {
				ow_ri_purge_related_pages();
			}
		},
		20
	);
}

// ===== Search-engine plumbing (through overworld-seo.php) =====

add_filter( 'ow_seo_post_types', function ( $types ) {
	if ( ! in_array( OW_RI_POST_TYPE, $types, true ) ) {
		$types[] = OW_RI_POST_TYPE;
	}

	return $types;
} );

/**
 * "VR Free Roam Rental For Events In Singapore | Overworld". The per-post
 * SEO box still wins.
 */
add_filter( 'ow_seo_generated', function ( $seo, $post ) {
	if ( ! $post instanceof WP_Post || OW_RI_POST_TYPE !== $post->post_type ) {
		return $seo;
	}

	$name = get_the_title( $post );

	$seo['title'] = function_exists( 'ow_seo_title_join' )
		? ow_seo_title_join( array( $name . ' Rental For Events In Singapore', 'Overworld' ) )
		: $name . ' Rental For Events In Singapore | Overworld';

	$desc = trim( (string) get_post_meta( $post->ID, 'ri_tagline', true ) );
	if ( '' === $desc && function_exists( 'ow_seo_post_description' ) ) {
		$desc = ow_seo_post_description( $post );
	}

	$context = sprintf( 'Rent %s for corporate events, family days, schools and roadshows in Singapore — delivered, set up and run by the Overworld crew.', $name );

	if ( function_exists( 'ow_seo_pad' ) ) {
		$desc = ow_seo_pad( $desc, $context );
	} elseif ( '' === $desc ) {
		$desc = $context;
	}

	$seo['desc'] = function_exists( 'ow_seo_trim' ) ? ow_seo_trim( $desc ) : $desc;

	return $seo;
}, 10, 2 );

/**
 * Service + FAQPage nodes — only what the page actually renders.
 */
add_filter( 'ow_seo_schema_graph', function ( $graph, $seo ) {
	$post = isset( $seo['post'] ) ? $seo['post'] : null;
	if ( ! $post instanceof WP_Post || OW_RI_POST_TYPE !== $post->post_type ) {
		return $graph;
	}

	$service = array(
		'@type'       => 'Service',
		'@id'         => $seo['url'] . '#service',
		'name'        => get_the_title( $post ) . ' rental',
		'serviceType' => 'Interactive game rental for events',
		'description' => $seo['desc'],
		'provider'    => array( '@id' => home_url( '/' ) . '#organization' ),
		'areaServed'  => array(
			'@type' => 'Country',
			'name'  => 'Singapore',
		),
	);
	$cover = ow_ri_cover_id( $post->ID );
	if ( $cover ) {
		$url = wp_get_attachment_image_url( $cover, 'large' );
		if ( $url ) {
			$service['image'] = $url;
		}
	}
	$graph[] = $service;

	$faqs = ow_ri_faqs( $post->ID );
	if ( $faqs ) {
		$entities = array();
		foreach ( $faqs as $faq ) {
			$entities[] = array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $faq['q'] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $faq['a'] ),
				),
			);
		}
		$graph[] = array(
			'@type'      => 'FAQPage',
			'@id'        => $seo['url'] . '#faq',
			'mainEntity' => $entities,
		);
	}

	return $graph;
}, 10, 2 );

/**
 * The tag and highlight chips, shared by the rental page cards and the
 * "Pair It With" cards. Returned as CSS so each page can print it inside its
 * own <style> block.
 *
 * The tag is the combo deals badge (page-pricing.php .ow-pri__combo-badge)
 * in the rental page's green, sitting on the photo.
 *
 * @return string
 */
function ow_ri_card_styles() {
	return <<<'CSS'
  .ow-ri-tag{
    position:absolute;left:14px;top:14px;z-index:2;
    display:inline-flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:11.5px;letter-spacing:.16em;text-transform:uppercase;font-weight:700;
    padding:8px 14px;border-radius:999px;
    background:linear-gradient(135deg,#dcff6e,#c3fb33);color:#0a0a14;
    box-shadow:0 10px 28px -12px #c3fb33,0 0 18px -6px #c3fb33;
    transition:transform .3s ease,box-shadow .3s ease;
  }
  .ow-ri-tag::before{content:"";width:7px;height:7px;border-radius:50%;background:#0a0a14;opacity:.55;flex:none;}
  .ow-ri-tag--inline{position:static;margin-bottom:18px;}
  a:hover .ow-ri-tag,article:hover .ow-ri-tag{transform:translateY(-1px) scale(1.03);}

  .ow-ri-chips{display:flex;flex-wrap:wrap;gap:6px;margin:0 0 14px;}
  .ow-ri-chip{
    display:inline-flex;align-items:center;gap:6px;
    background:rgba(195,251,51,.08);border:1px solid rgba(195,251,51,.28);
    color:#fff;border-radius:999px;padding:5px 11px;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:600;
    white-space:nowrap;
  }
  .ow-ri-chip svg{width:14px;height:14px;color:#c3fb33;flex:0 0 auto;}
  .ow-ri-price{
    font-family:'JetBrains Mono',ui-monospace,monospace;font-size:12px;
    letter-spacing:.1em;text-transform:uppercase;color:#c3fb33;margin:0 0 16px;
  }
CSS;
}

/**
 * CSS only the rental item page needs, on top of ow_pe_styles().
 *
 * @return string
 */
function ow_ri_styles() {
	return ow_ri_card_styles() . <<<'CSS'
  /* Hero highlights: the game pages' big stat row, sized for 1–4 facts. */
  .ow-ri__highlights{
    display:grid;grid-template-columns:repeat(var(--n,4),minmax(0,1fr));
    border:1px solid rgba(195,251,51,.28);border-radius:20px;overflow:hidden;
    background:rgba(19,19,31,.78);margin:30px 0 0;max-width:calc(var(--n,4) * 210px);
  }
  .ow-ri__hl{padding:20px 22px;border-right:1px solid var(--line);display:flex;flex-direction:column;gap:10px;}
  .ow-ri__hl:last-child{border-right:0;}
  .ow-ri__hl svg{width:26px;height:26px;color:var(--accent);}
  .ow-ri__hl strong{
    font-family:'Anton','Bebas Neue',sans-serif;font-weight:400;
    font-size:clamp(20px,2vw,26px);line-height:1.05;text-transform:uppercase;color:#fff;
  }
  .ow-pe__hero .ow-ri-price{font-size:13px;margin:0 0 22px;}

  /* Package + covered side by side. */
  .ow-ri__split{display:grid;grid-template-columns:1.15fr 1fr;gap:28px;align-items:start;}
  .ow-ri__panel{
    background:var(--bg);border:1px solid var(--line);border-radius:20px;padding:30px 30px 26px;
  }
  .ow-ri__panel .ow-pe__section-title{font-size:clamp(22px,2.4vw,30px);margin-bottom:22px;}
  .ow-ri__pack{list-style:none;margin:0;padding:0;display:grid;gap:14px;}
  .ow-ri__pack li{display:grid;grid-template-columns:26px 1fr;gap:12px;align-items:start;}
  .ow-ri__pack svg{width:22px;height:22px;color:var(--accent);margin-top:1px;}
  .ow-ri__pack strong{display:block;font-size:16px;font-weight:600;color:#fff;line-height:1.35;}
  .ow-ri__pack span{display:block;font-size:13.5px;color:var(--dim);line-height:1.45;margin-top:3px;}
  .ow-ri__inc{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
  .ow-ri__inc-item{
    display:flex;align-items:center;gap:12px;padding:14px 16px;border-radius:14px;
    background:rgba(255,255,255,.03);border:1px solid var(--line);font-size:14px;line-height:1.3;
  }
  .ow-ri__inc-item svg{width:24px;height:24px;color:var(--accent);flex:0 0 auto;}

  /* Space & setup. */
  .ow-ri__specs{
    display:grid;grid-template-columns:repeat(2,minmax(0,1fr));
    border:1px solid var(--line);border-radius:18px;overflow:hidden;max-width:1000px;
  }
  .ow-ri__spec{padding:18px 22px;border-bottom:1px solid var(--line);border-right:1px solid var(--line);}
  .ow-ri__spec:nth-child(2n){border-right:0;}
  .ow-ri__spec dt{
    font-family:'JetBrains Mono',ui-monospace,monospace;font-size:10.5px;letter-spacing:1.6px;
    text-transform:uppercase;color:var(--accent);margin:0 0 6px;
  }
  .ow-ri__spec dd{margin:0;font-size:15px;line-height:1.5;color:#fff;}

  /* FAQ. */
  .ow-ri__faqs{max-width:900px;display:grid;gap:12px;}
  .ow-ri__faq{background:var(--bg-2);border:1px solid var(--line);border-radius:16px;}
  .ow-ri__faq summary{
    list-style:none;cursor:pointer;padding:20px 56px 20px 22px;position:relative;
    font-size:16.5px;font-weight:600;color:#fff;
  }
  .ow-ri__faq summary::-webkit-details-marker{display:none;}
  .ow-ri__faq summary::after{
    content:"+";position:absolute;right:22px;top:50%;transform:translateY(-50%);
    font-size:24px;color:var(--accent);transition:transform .2s ease;
  }
  .ow-ri__faq[open] summary::after{transform:translateY(-50%) rotate(45deg);}
  .ow-ri__faq[open]{border-color:rgba(195,251,51,.35);}
  .ow-ri__faq p{margin:0;padding:0 22px 20px;color:var(--dim);font-size:15px;line-height:1.65;}

  /* "Pair it with" cards. */
  .ow-ri__card .ow-pe__card-media{position:relative;}
  .ow-ri__card .ow-ri-chips{margin:2px 0 4px;}

  @media(max-width:1024px){
    .ow-ri__split{grid-template-columns:1fr;}
  }
  @media(max-width:640px){
    .ow-ri__highlights{grid-template-columns:repeat(2,minmax(0,1fr));max-width:none;}
    .ow-ri__hl:nth-child(2n){border-right:0;}
    .ow-ri__hl:nth-child(-n+2){border-bottom:1px solid var(--line);}
    .ow-ri__hl{padding:16px 18px;}
    .ow-ri__panel{padding:24px 20px 20px;}
    .ow-ri__inc{grid-template-columns:1fr;}
    .ow-ri__specs{grid-template-columns:1fr;}
    .ow-ri__spec{border-right:0;}
  }
CSS;
}

<?php
/**
 * Plugin Name: Overworld — Past Event Gallery
 * Description: Adds the "Past Events" content type behind the gallery on /event-rental/. Each entry the client adds gets its own page (/past-events/<name>/) with a write-up, the games brought along, a photo gallery and a call to action, and the six newest entries appear on the Equipment Rental page above the FAQ.
 * Author: Overworld
 * Version: 1.2.0
 *
 * Must-use plugin: auto-loads, no activation needed.
 *
 * WHY A CONTENT TYPE AND NOT SIX MORE ACF SLOTS ON THE PAGE:
 * the client asked for each gallery tile to open its own page with more
 * information, the way the game pages do. Numbered slots on the rental page
 * could show six tiles but could never give them pages, so past events are
 * posts of their own — "Past Events" in the WP Admin sidebar, Add New, fill
 * in, publish, and the tile appears on the rental page by itself.
 *
 * Everything is optional. An entry with nothing but a title, a cover photo
 * and a few pictures already renders a complete page; the built-in copy below
 * fills the rest. Lists (photos, games, highlights) are numbered slots rather
 * than repeaters because the site runs ACF free — same convention as
 * overworld-event-rental.php and overworld-outlet-gallery.php.
 *
 * The CSS for both templates lives here (ow_pe_styles) because
 * single-past_event.php and archive-past_event.php render the same cards and
 * would otherwise carry two copies of it that drift apart.
 *
 * NOTE: registering a post type adds rewrite rules. After deploying this file
 * for the first time, flush them once — scripts/deploy-event-gallery.sh does
 * it — or /past-events/ URLs 404.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_PE_POST_TYPE   = 'past_event';
const OW_PE_ARCHIVE     = 'past-events';
const OW_PE_PHOTO_SLOTS = 12;
const OW_PE_GAME_SLOTS  = 4;
const OW_PE_STAT_SLOTS  = 3;

/** How many tiles the Equipment Rental page shows. */
const OW_PE_RENTAL_TILES = 6;

/**
 * Built-in copy for a past event page and for the listing.
 *
 * @return array
 */
function ow_pe_defaults() {
	return array(
		// Single event page.
		'eyebrow'         => 'Past Event · Singapore',
		'story_title'     => 'How The Day Went',
		'games_title'     => 'What We Brought Along',
		'photos_title'    => 'Photos From The Day',
		'back_label'      => 'All Past Events',
		'cta_eyebrow'     => 'Plan Your Event With Us',
		'cta_title'       => 'Want This At Your Venue?',
		'cta_text'        => 'Tell us your venue, date and headcount. We will come back with the games that fit the space, what each one needs and a quote — delivery, setup, crew and pack-down included.',
		'cta_label'       => 'Get A Quote',
		'cta_url'         => '/contact/',
		'cta_ghost_label' => 'See What We Rent',
		'cta_ghost_url'   => '/event-rental/',

		// Listing at /past-events/.
		'archive_eyebrow' => 'Past Events · Singapore',
		'archive_title'   => 'Events We Have Powered',
		'archive_text'    => 'Corporate D&Ds, family days, school carnivals, roadshows and mall activations across Singapore — every one of them run on site by the Overworld crew. Have a look at what these days actually looked like.',
		'archive_empty'   => 'The first past events are being written up. In the meantime, see what we rent and what each activity needs.',
	);
}

/**
 * A field on a past event, falling back to the built-in copy.
 *
 * @param int    $post_id Past event ID.
 * @param string $name    Meta key without the 'pe_' prefix.
 * @param mixed  $default Fallback; when null, the built-in default for $name.
 * @return string
 */
function ow_pe_val( $post_id, $name, $default = null ) {
	if ( null === $default ) {
		$defaults = ow_pe_defaults();
		$default  = isset( $defaults[ $name ] ) ? $defaults[ $name ] : '';
	}

	$value = trim( (string) get_post_meta( $post_id, 'pe_' . $name, true ) );

	return '' !== $value ? $value : (string) $default;
}

/**
 * A relative URL from the client made absolute; anchors and absolute URLs are
 * left alone. Mirrors ow_rental_url() so both boxes behave the same.
 *
 * @param string $url URL as typed.
 * @return string
 */
function ow_pe_url( $url ) {
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
 * The listing URL, /past-events/.
 *
 * @return string
 */
function ow_pe_archive_url() {
	$url = get_post_type_archive_link( OW_PE_POST_TYPE );

	return $url ? $url : home_url( '/' . OW_PE_ARCHIVE . '/' );
}

/**
 * An attachment ID, or 0 when the ID no longer points at an attachment
 * (the client deleted the image from the media library).
 *
 * @param mixed $id Stored value.
 * @return int
 */
function ow_pe_attachment( $id ) {
	$id = (int) $id;

	return ( $id && 'attachment' === get_post_type( $id ) ) ? $id : 0;
}

/**
 * The picture that represents this event: the cover photo, else the featured
 * image, else the first gallery photo.
 *
 * @param int $post_id Past event ID.
 * @return int Attachment ID, or 0.
 */
function ow_pe_cover_id( $post_id ) {
	$id = ow_pe_attachment( get_post_meta( $post_id, 'pe_cover', true ) );
	if ( $id ) {
		return $id;
	}

	$id = ow_pe_attachment( get_post_thumbnail_id( $post_id ) );
	if ( $id ) {
		return $id;
	}

	$photos = ow_pe_photos( $post_id );

	return $photos ? $photos[0] : 0;
}

/**
 * The gallery photos, in slot order, skipping empty and deleted ones.
 *
 * @param int $post_id Past event ID.
 * @return int[] Attachment IDs.
 */
function ow_pe_photos( $post_id ) {
	$ids = array();
	for ( $i = 1; $i <= OW_PE_PHOTO_SLOTS; $i++ ) {
		$id = ow_pe_attachment( get_post_meta( $post_id, "pe_photo_{$i}", true ) );
		if ( $id ) {
			$ids[] = $id;
		}
	}

	return $ids;
}

/**
 * The three highlight figures shown under the title ("120 guests", "3 games").
 *
 * @param int $post_id Past event ID.
 * @return array<int, array{value: string, label: string}>
 */
function ow_pe_stats( $post_id ) {
	$rows = array();
	for ( $i = 1; $i <= OW_PE_STAT_SLOTS; $i++ ) {
		$value = trim( (string) get_post_meta( $post_id, "pe_stat_{$i}_value", true ) );
		$label = trim( (string) get_post_meta( $post_id, "pe_stat_{$i}_label", true ) );
		if ( '' === $value && '' === $label ) {
			continue;
		}
		$rows[] = array( 'value' => $value, 'label' => $label );
	}

	return $rows;
}

/**
 * The games brought to this event. Each row can link to the activity's own
 * page, which is what ties a past event back into the rest of the site.
 *
 * @param int $post_id Past event ID.
 * @return array<int, array{name: string, text: string, url: string, image_id: int}>
 */
function ow_pe_games( $post_id ) {
	$rows = array();
	for ( $i = 1; $i <= OW_PE_GAME_SLOTS; $i++ ) {
		$name = trim( (string) get_post_meta( $post_id, "pe_game_{$i}_name", true ) );
		if ( '' === $name ) {
			continue;
		}
		$rows[] = array(
			'name'     => $name,
			'text'     => trim( (string) get_post_meta( $post_id, "pe_game_{$i}_text", true ) ),
			'url'      => ow_pe_url( get_post_meta( $post_id, "pe_game_{$i}_url", true ) ),
			'image_id' => ow_pe_attachment( get_post_meta( $post_id, "pe_game_{$i}_image", true ) ),
		);
	}

	return $rows;
}

/**
 * The small line under a card and under the title: venue and when.
 *
 * @param int $post_id Past event ID.
 * @return string[] Parts, already trimmed and non-empty.
 */
function ow_pe_meta_parts( $post_id ) {
	$parts = array();
	foreach ( array( 'venue', 'date_label', 'client' ) as $key ) {
		$value = trim( (string) get_post_meta( $post_id, 'pe_' . $key, true ) );
		if ( '' !== $value ) {
			$parts[] = $value;
		}
	}

	return $parts;
}

/**
 * One card's worth of data, used by the rental page, the listing and the
 * "more past events" strip at the bottom of a single event.
 *
 * @param WP_Post|int $post Past event.
 * @return array
 */
function ow_pe_card( $post ) {
	$post = get_post( $post );
	if ( ! $post instanceof WP_Post ) {
		return array();
	}

	$cover = ow_pe_cover_id( $post->ID );

	return array(
		'id'        => $post->ID,
		'title'     => get_the_title( $post ),
		'url'       => get_permalink( $post ),
		'tag'       => trim( (string) get_post_meta( $post->ID, 'pe_tag', true ) ),
		'summary'   => trim( (string) get_post_meta( $post->ID, 'pe_summary', true ) ),
		'meta'      => ow_pe_meta_parts( $post->ID ),
		'image_id'  => $cover,
		'image_url' => $cover ? (string) wp_get_attachment_image_url( $cover, 'large' ) : '',
		'photos'    => count( ow_pe_photos( $post->ID ) ),
	);
}

/**
 * Published past events, newest first, for a card grid.
 *
 * Order is the Order field on the post (Page Attributes) and then the
 * published date, so the client can pin a favourite to the front without
 * republishing it.
 *
 * Entries with "Show on the Equipment Rental page" unticked are skipped when
 * $featured_only is true; they still have their own page and still appear on
 * /past-events/.
 *
 * @param int  $limit         How many.
 * @param bool $featured_only Only the ones ticked for the rental page.
 * @param int  $exclude       A post ID to leave out (the one being viewed).
 * @return array<int, array>
 */
function ow_pe_cards( $limit = OW_PE_RENTAL_TILES, $featured_only = true, $exclude = 0 ) {
	$args = array(
		'post_type'           => OW_PE_POST_TYPE,
		'post_status'         => 'publish',
		'posts_per_page'      => (int) $limit,
		'orderby'             => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	);

	if ( $exclude ) {
		$args['post__not_in'] = array( (int) $exclude );
	}

	if ( $featured_only ) {
		// NOT EXISTS covers entries saved before this field existed, so an
		// unticked box is the only thing that hides a tile.
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key'     => 'pe_featured',
				'value'   => '1',
				'compare' => '=',
			),
			array(
				'key'     => 'pe_featured',
				'compare' => 'NOT EXISTS',
			),
		);
	}

	$cards = array();
	foreach ( get_posts( $args ) as $post ) {
		$cards[] = ow_pe_card( $post );
	}

	return $cards;
}

/**
 * Copy for the /past-events/ listing.
 *
 * ACF free has no options page, so these three fields live on the Equipment
 * Rental page's box — the page the listing hangs off — rather than in a
 * settings screen the client would have to be taught separately.
 *
 * @return array{eyebrow: string, title: string, text: string}
 */
function ow_pe_archive_copy() {
	$defaults = ow_pe_defaults();
	$page_id  = 0;

	if ( function_exists( 'ow_rental_page_ids' ) ) {
		$ids     = ow_rental_page_ids();
		$page_id = $ids ? (int) $ids[0] : 0;
	}

	$get = function ( $key ) use ( $page_id, $defaults ) {
		$value = $page_id ? trim( (string) get_post_meta( $page_id, 'rental_' . $key, true ) ) : '';

		return '' !== $value ? $value : $defaults[ $key ];
	};

	return array(
		'eyebrow' => $get( 'archive_eyebrow' ),
		'title'   => $get( 'archive_title' ),
		'text'    => $get( 'archive_text' ),
	);
}

// ===== The content type =====

add_action( 'init', function () {
	register_post_type(
		OW_PE_POST_TYPE,
		array(
			'labels'        => array(
				'name'                  => 'Past Events',
				'singular_name'         => 'Past Event',
				'menu_name'             => 'Past Events',
				'add_new'               => 'Add Past Event',
				'add_new_item'          => 'Add Past Event',
				'edit_item'             => 'Edit Past Event',
				'new_item'              => 'New Past Event',
				'view_item'             => 'View Past Event',
				'view_items'            => 'View Past Events',
				'search_items'          => 'Search Past Events',
				'not_found'             => 'No past events yet. Add your first one.',
				'not_found_in_trash'    => 'Nothing in the bin.',
				'all_items'             => 'All Past Events',
				'archives'              => 'Past Events',
				'featured_image'        => 'Cover Photo',
				'set_featured_image'    => 'Set cover photo',
				'remove_featured_image' => 'Remove cover photo',
				'use_featured_image'    => 'Use as cover photo',
			),
			'description'   => 'Events Overworld has already run on a client site. Each one gets its own page and a tile in the gallery on the Equipment Rental page.',
			'public'        => true,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => true,
			'menu_position' => 21,
			'menu_icon'     => 'dashicons-format-gallery',
			'has_archive'   => OW_PE_ARCHIVE,
			'rewrite'       => array(
				'slug'       => OW_PE_ARCHIVE,
				'with_front' => false,
			),
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			'map_meta_cap'  => true,
		)
	);
} );

/**
 * "Cover Photo" rather than "Featured image" in the sidebar, and a nudge in
 * the title field, so the client is never guessing what a box is for.
 */
add_filter( 'enter_title_here', function ( $text, $post ) {
	if ( $post instanceof WP_Post && OW_PE_POST_TYPE === $post->post_type ) {
		return 'Event name — e.g. "Tech Co. Family Day 2026"';
	}

	return $text;
}, 10, 2 );

/**
 * A pointer at the top of the Past Events list, so the client knows what the
 * screen is for — and, while the "Sample —" placeholders are still published,
 * that they should go once real events are in.
 */
add_action( 'admin_notices', function () {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'edit-' . OW_PE_POST_TYPE !== $screen->id ) {
		return;
	}

	$samples = array_filter(
		get_posts( array(
			'post_type'      => OW_PE_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'fields'         => 'all',
		) ),
		function ( $p ) {
			return 0 === strpos( $p->post_title, 'Sample' );
		}
	);

	echo '<div class="notice notice-info"><p>';
	echo 'Each entry here is one event you have run. Click <strong>Add Past Event</strong> to add one. It gets its own page, and the '
		. (int) OW_PE_RENTAL_TILES . ' newest appear as tiles on the <a href="' . esc_url( home_url( '/event-rental/' ) ) . '">Equipment Rental page</a>. ';
	echo 'The "Example" draft shows a filled-in entry you can copy from.';
	echo '</p>';
	if ( $samples ) {
		echo '<p><strong>' . count( $samples ) . ' placeholder events</strong> (titles starting "Sample —") are showing on the live site so the gallery is not empty. '
			. 'Once your own events are published, hover over each one and click <strong>Bin</strong>.</p>';
	}
	echo '</div>';
} );

// ===== The edit screen (ACF) =====

add_action( 'acf/init', function () {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$d      = ow_pe_defaults();
	$fields = array();

	// --- How to use this box (shown above the first section) ---
	$fields[] = array(
		'key'       => 'field_pe_howto',
		'label'     => 'Adding an event, in 4 steps',
		'name'      => '',
		'type'      => 'message',
		'message'   => '<ol style="margin:0 0 0 1.2em">'
			. '<li>Type the event name in the title at the top of the page.</li>'
			. '<li>Write what happened in the writing area under the title.</li>'
			. '<li>Below, under <strong>The Tile</strong>, add a Cover Photo and one or two lines. Then add pictures under <strong>Photos</strong>.</li>'
			. '<li>Click <strong>Publish</strong> (top right). The event gets its own page and appears on the Equipment Rental page within a few seconds.</li>'
			. '</ol>'
			. '<p style="margin:.6em 0 0">Everything else in this box is optional. Only name the client if they are happy to be named. '
			. 'Photos: landscape, at least 1600px wide, ideally under 400KB each.</p>',
		'new_lines' => '',
		'esc_html'  => 0,
	);

	// --- The tile ---
	$fields[] = array(
		'key'          => 'field_pe_card_tab',
		'label'        => 'The Tile (how this event looks in the gallery)',
		'type'         => 'accordion',
		'open'         => 1,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'           => 'field_pe_cover',
		'label'         => 'Cover Photo',
		'name'          => 'pe_cover',
		'type'          => 'image',
		'instructions'  => 'The picture on the tile and across the top of this event\'s page. Landscape, at least 1600px wide. Leave empty and the first gallery photo below is used.',
		'return_format' => 'id',
		'preview_size'  => 'medium',
		'required'      => 0,
	);
	$fields[] = array(
		'key'          => 'field_pe_tag',
		'label'        => 'Small Label On The Tile',
		'name'         => 'pe_tag',
		'type'         => 'text',
		'instructions' => 'What kind of event it was — "Corporate Family Day", "School Carnival", "Mall Roadshow".',
		'required'     => 0,
		'wrapper'      => array( 'width' => '34' ),
	);
	$fields[] = array(
		'key'          => 'field_pe_venue',
		'label'        => 'Where',
		'name'         => 'pe_venue',
		'type'         => 'text',
		'instructions' => 'The venue — "Marina Bay Sands Ballroom". Shown under the event name.',
		'required'     => 0,
		'wrapper'      => array( 'width' => '33' ),
	);
	$fields[] = array(
		'key'          => 'field_pe_date_label',
		'label'        => 'When',
		'name'         => 'pe_date_label',
		'type'         => 'text',
		'instructions' => 'Written however you like — "March 2026", "Q2 2026". Not a date picker on purpose.',
		'required'     => 0,
		'wrapper'      => array( 'width' => '33' ),
	);
	$fields[] = array(
		'key'          => 'field_pe_client',
		'label'        => 'Who It Was For',
		'name'         => 'pe_client',
		'type'         => 'text',
		'instructions' => 'Optional, and only if they are happy to be named. Leave empty for "a logistics firm" style write-ups.',
		'required'     => 0,
	);
	$fields[] = array(
		'key'          => 'field_pe_summary',
		'label'        => 'One Or Two Lines For The Tile',
		'name'         => 'pe_summary',
		'type'         => 'textarea',
		'rows'         => 2,
		'instructions' => 'The text under the event name on the tile. Also used as the description when this page is shared or shown in Google, so write it for someone who has not seen the photos.',
		'required'     => 0,
	);
	$fields[] = array(
		'key'           => 'field_pe_featured',
		'label'         => 'Show On The Equipment Rental Page',
		'name'          => 'pe_featured',
		'type'          => 'true_false',
		'instructions'  => 'On: this event can appear in the gallery on the Equipment Rental page (the ' . OW_PE_RENTAL_TILES . ' newest ticked events are shown). Off: it still has its own page and still appears on the Past Events listing.',
		'ui'            => 1,
		'default_value' => 1,
		'required'      => 0,
	);

	// --- Top of the event page ---
	$fields[] = array(
		'key'          => 'field_pe_top_tab',
		'label'        => 'Top Of The Event Page',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_pe_eyebrow',
		'label'        => 'Small Line Above The Title',
		'name'         => 'pe_eyebrow',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['eyebrow'] . '"',
		'required'     => 0,
	);
	$fields[] = array(
		'key'          => 'field_pe_intro',
		'label'        => 'Text Under The Title',
		'name'         => 'pe_intro',
		'type'         => 'textarea',
		'rows'         => 3,
		'instructions' => 'A short lead paragraph. Leave empty and the tile text above is used.',
		'required'     => 0,
	);
	for ( $i = 1; $i <= OW_PE_STAT_SLOTS; $i++ ) {
		$fields[] = array(
			'key'          => "field_pe_stat_{$i}_value",
			'label'        => "Highlight {$i} — Big Number",
			'name'         => "pe_stat_{$i}_value",
			'type'         => 'text',
			'instructions' => 1 === $i ? 'Three figures in a row under the title — guests, games, hours. Leave all three empty to hide the row.' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '16' ),
		);
		$fields[] = array(
			'key'      => "field_pe_stat_{$i}_label",
			'label'    => "Highlight {$i} — Text",
			'name'     => "pe_stat_{$i}_label",
			'type'     => 'text',
			'required' => 0,
			'wrapper'  => array( 'width' => '17' ),
		);
	}

	// --- The write-up ---
	$fields[] = array(
		'key'          => 'field_pe_story_tab',
		'label'        => 'The Write-Up',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'       => 'field_pe_story_msg',
		'label'     => '',
		'name'      => '',
		'type'      => 'message',
		'message'   => 'The main story goes in the <strong>normal editor above this box</strong> — headings, paragraphs, lists, all of it. This heading sits above it on the page.',
		'new_lines' => '',
		'esc_html'  => 0,
	);
	$fields[] = array(
		'key'          => 'field_pe_story_title',
		'label'        => 'Heading Above The Write-Up',
		'name'         => 'pe_story_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['story_title'] . '"',
		'required'     => 0,
	);

	// --- Games ---
	$fields[] = array(
		'key'          => 'field_pe_games_tab',
		'label'        => 'Games We Brought (up to ' . OW_PE_GAME_SLOTS . ')',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_pe_games_title',
		'label'        => 'Section Heading',
		'name'         => 'pe_games_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['games_title'] . '"',
		'required'     => 0,
	);
	for ( $i = 1; $i <= OW_PE_GAME_SLOTS; $i++ ) {
		$fields[] = array(
			'key'           => "field_pe_game_{$i}_image",
			'label'         => "Game {$i} — Photo",
			'name'          => "pe_game_{$i}_image",
			'type'          => 'image',
			'instructions'  => 1 === $i ? 'Leave a game\'s name empty to skip that slot. The whole section is hidden when none are filled in.' : '',
			'return_format' => 'id',
			'preview_size'  => 'thumbnail',
			'required'      => 0,
			'wrapper'       => array( 'width' => '20' ),
		);
		$fields[] = array(
			'key'      => "field_pe_game_{$i}_name",
			'label'    => "Game {$i} — Name",
			'name'     => "pe_game_{$i}_name",
			'type'     => 'text',
			'required' => 0,
			'wrapper'  => array( 'width' => '25' ),
		);
		$fields[] = array(
			'key'      => "field_pe_game_{$i}_text",
			'label'    => "Game {$i} — One Line About It",
			'name'     => "pe_game_{$i}_text",
			'type'     => 'text',
			'required' => 0,
			'wrapper'  => array( 'width' => '30' ),
		);
		$fields[] = array(
			'key'          => "field_pe_game_{$i}_url",
			'label'        => "Game {$i} — Links To",
			'name'         => "pe_game_{$i}_url",
			'type'         => 'text',
			'instructions' => 1 === $i ? 'The game\'s own page, e.g. "/vr-free-roam/".' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '25' ),
		);
	}

	// --- Photos ---
	$fields[] = array(
		'key'          => 'field_pe_photos_tab',
		'label'        => 'Photos (up to ' . OW_PE_PHOTO_SLOTS . ')',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_pe_photos_title',
		'label'        => 'Section Heading',
		'name'         => 'pe_photos_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['photos_title'] . '"',
		'required'     => 0,
	);
	$fields[] = array(
		'key'       => 'field_pe_photos_msg',
		'label'     => '',
		'name'      => '',
		'type'      => 'message',
		'message'   => 'Photo 1 is the large tile. Fill any number of slots — empty ones are skipped, and the section is hidden when all are empty. Landscape photos work best (1600&times;1200 or wider, under 400KB each). Clicking a photo on the page opens it full size.',
		'new_lines' => '',
		'esc_html'  => 0,
	);
	for ( $i = 1; $i <= OW_PE_PHOTO_SLOTS; $i++ ) {
		$fields[] = array(
			'key'           => "field_pe_photo_{$i}",
			'label'         => 'Photo ' . $i . ( 1 === $i ? ' — large tile' : '' ),
			'name'          => "pe_photo_{$i}",
			'type'          => 'image',
			'return_format' => 'id',
			'preview_size'  => 'thumbnail',
			'required'      => 0,
			'wrapper'       => array( 'width' => '25' ),
		);
	}

	// --- CTA ---
	$fields[] = array(
		'key'          => 'field_pe_cta_tab',
		'label'        => 'Bottom Call To Action',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'       => 'field_pe_cta_msg',
		'label'     => '',
		'name'      => '',
		'type'      => 'message',
		'message'   => 'Leave these empty on most events — the built-in wording already asks the reader to enquire. Fill them in when one event deserves its own ask.',
		'new_lines' => '',
		'esc_html'  => 0,
	);
	$fields[] = array(
		'key'          => 'field_pe_cta_eyebrow',
		'label'        => 'Small Line Above The Heading',
		'name'         => 'pe_cta_eyebrow',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['cta_eyebrow'] . '"',
		'required'     => 0,
	);
	$fields[] = array(
		'key'          => 'field_pe_cta_title',
		'label'        => 'Heading',
		'name'         => 'pe_cta_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['cta_title'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key'          => 'field_pe_cta_text',
		'label'        => 'Text',
		'name'         => 'pe_cta_text',
		'type'         => 'textarea',
		'rows'         => 2,
		'instructions' => 'Default: the standard quote wording.',
		'required'     => 0,
		'wrapper'      => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key'          => 'field_pe_cta_label',
		'label'        => 'Button — Text',
		'name'         => 'pe_cta_label',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['cta_label'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '25' ),
	);
	$fields[] = array(
		'key'          => 'field_pe_cta_url',
		'label'        => 'Button — Link',
		'name'         => 'pe_cta_url',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['cta_url'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '25' ),
	);
	$fields[] = array(
		'key'          => 'field_pe_cta_ghost_label',
		'label'        => 'Second Button — Text',
		'name'         => 'pe_cta_ghost_label',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['cta_ghost_label'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '25' ),
	);
	$fields[] = array(
		'key'          => 'field_pe_cta_ghost_url',
		'label'        => 'Second Button — Link',
		'name'         => 'pe_cta_ghost_url',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['cta_ghost_url'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '25' ),
	);
	$fields[] = array(
		'key'      => 'field_pe_end',
		'label'    => '',
		'type'     => 'accordion',
		'endpoint' => 1,
	);

	acf_add_local_field_group( array(
		'key'             => 'group_ow_past_event',
		'title'           => 'Past Event Details',
		'fields'          => $fields,
		'location'        => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => OW_PE_POST_TYPE,
				),
			),
		),
		'menu_order'      => 2,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
		'description'     => 'Everything about this past event. All of it is optional — a title, a cover photo and a few pictures already make a finished page.',
	) );
} );

/**
 * After the client saves an event:
 *   - the cover photo becomes the featured image, so sharing the page on
 *     WhatsApp shows the picture without a second upload;
 *   - the tile text becomes the excerpt when there is none, which is what
 *     overworld-seo.php reads for the meta description;
 *   - Elementor's render cache for the page is cleared.
 */
add_action( 'acf/save_post', function ( $post_id ) {
	if ( ! is_numeric( $post_id ) || OW_PE_POST_TYPE !== get_post_type( $post_id ) ) {
		return;
	}
	$post_id = (int) $post_id;

	$cover = ow_pe_attachment( get_post_meta( $post_id, 'pe_cover', true ) );
	if ( $cover && (int) get_post_thumbnail_id( $post_id ) !== $cover ) {
		set_post_thumbnail( $post_id, $cover );
	}

	$summary = trim( (string) get_post_meta( $post_id, 'pe_summary', true ) );
	$post    = get_post( $post_id );
	if ( '' !== $summary && $post instanceof WP_Post && '' === trim( (string) $post->post_excerpt ) ) {
		// remove_action first: this runs inside a save, and wp_update_post
		// would re-enter save_post.
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_excerpt' => wp_strip_all_tags( $summary ),
			)
		);
	}

	delete_post_meta( $post_id, '_elementor_element_cache' );
}, 20 );

/**
 * A past event appears on two pages it does not own: the gallery on the
 * Equipment Rental page and the /past-events/ listing. LiteSpeed purges the
 * event's own URL when it is saved and knows nothing about those two, so
 * without this the client edits an event, looks at the rental page and sees
 * the old tile — the commonest way a CMS feels broken to the person using it.
 *
 * @return void
 */
function ow_pe_purge_related_pages() {
	if ( function_exists( 'ow_rental_page_ids' ) ) {
		foreach ( ow_rental_page_ids() as $page_id ) {
			do_action( 'litespeed_purge_post', $page_id );
			// Elementor caches the page's rendered output separately.
			delete_post_meta( $page_id, '_elementor_element_cache' );
		}
	}

	do_action( 'litespeed_purge_url', ow_pe_archive_url() );
	do_action( 'litespeed_purge_posttype', OW_PE_POST_TYPE );
}

add_action(
	'save_post_' . OW_PE_POST_TYPE,
	function ( $post_id ) {
		// Autosaves and revisions change nothing anyone can see.
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		ow_pe_purge_related_pages();
	},
	20
);

// Deleting or binning an event takes its tile off those pages too.
foreach ( array( 'trashed_post', 'untrashed_post', 'deleted_post' ) as $ow_pe_event ) {
	add_action(
		$ow_pe_event,
		function ( $post_id ) {
			if ( OW_PE_POST_TYPE === get_post_type( $post_id ) ) {
				ow_pe_purge_related_pages();
			}
		},
		20
	);
}

// ===== Search-engine plumbing (through overworld-seo.php) =====

/**
 * Past events get the same title, description, sharing image and schema
 * treatment as the rest of the site.
 */
add_filter( 'ow_seo_post_types', function ( $types ) {
	if ( ! in_array( OW_PE_POST_TYPE, $types, true ) ) {
		$types[] = OW_PE_POST_TYPE;
	}

	return $types;
} );

/**
 * Titles and descriptions for a past event. The per-post SEO box still wins.
 */
add_filter( 'ow_seo_generated', function ( $seo, $post ) {
	if ( ! $post instanceof WP_Post || OW_PE_POST_TYPE !== $post->post_type ) {
		return $seo;
	}

	$name  = get_the_title( $post );
	$parts = ow_pe_meta_parts( $post->ID );
	$tag   = trim( (string) get_post_meta( $post->ID, 'pe_tag', true ) );

	$seo['title'] = function_exists( 'ow_seo_title_join' )
		? ow_seo_title_join( array( $name, $tag ? $tag : 'Past Event', 'Overworld' ) )
		: $name . ' | Overworld';

	$desc = trim( (string) get_post_meta( $post->ID, 'pe_summary', true ) );
	if ( '' === $desc && function_exists( 'ow_seo_post_description' ) ) {
		$desc = ow_seo_post_description( $post );
	}

	$context = sprintf(
		'%s%s — interactive games delivered, set up and run on site by Overworld Singapore.',
		$tag ? $tag . ': ' : '',
		$parts ? implode( ', ', $parts ) : $name
	);

	if ( function_exists( 'ow_seo_pad' ) ) {
		$desc = ow_seo_pad( $desc, $context );
	} elseif ( '' === $desc ) {
		$desc = $context;
	}

	$seo['desc'] = function_exists( 'ow_seo_trim' ) ? ow_seo_trim( $desc ) : $desc;

	return $seo;
}, 10, 2 );

/**
 * The /past-events/ listing.
 */
add_filter( 'ow_seo_archive_seo', function ( $seo, $obj ) {
	if ( ! $obj || ! isset( $obj->name ) || OW_PE_POST_TYPE !== $obj->name ) {
		return $seo;
	}

	$copy = ow_pe_archive_copy();

	$seo['title'] = 'Past Events We Have Run In Singapore | Overworld';
	$seo['desc']  = function_exists( 'ow_seo_trim' ) ? ow_seo_trim( $copy['text'] ) : $copy['text'];

	return $seo;
}, 10, 2 );

/**
 * An ImageGallery node listing the photos actually rendered, plus a
 * breadcrumb trail back through the rental page.
 */
add_filter( 'ow_seo_schema_graph', function ( $graph, $seo ) {
	$post = isset( $seo['post'] ) ? $seo['post'] : null;
	if ( ! $post instanceof WP_Post || OW_PE_POST_TYPE !== $post->post_type ) {
		return $graph;
	}

	$photos = ow_pe_photos( $post->ID );
	if ( ! $photos ) {
		return $graph;
	}

	$images = array();
	foreach ( $photos as $id ) {
		$url = wp_get_attachment_image_url( $id, 'large' );
		if ( ! $url ) {
			continue;
		}
		$alt      = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		$images[] = array(
			'@type'      => 'ImageObject',
			'contentUrl' => $url,
			'caption'    => '' !== $alt ? $alt : get_the_title( $post ),
		);
	}

	if ( $images ) {
		$graph[] = array(
			'@type'           => 'ImageGallery',
			'@id'             => $seo['url'] . '#gallery',
			'name'            => sprintf( '%s — photos', get_the_title( $post ) ),
			'associatedMedia' => $images,
		);
	}

	return $graph;
}, 10, 2 );

/**
 * Shared CSS for single-past_event.php and archive-past_event.php.
 *
 * Same design language as page-event-rental.php: Anton display type, Space
 * Grotesk body, the client's green on near-black.
 *
 * @return string
 */
function ow_pe_styles() {
	return <<<'CSS'
  .ow-pe{
    --accent:#c3fb33;
    --bg:#0a0a14;
    --bg-2:#13131f;
    --fg:#fff;
    --dim:rgba(220,225,240,.65);
    --line:rgba(255,255,255,.08);
    background:var(--bg);
    color:var(--fg);
    font-family:'Space Grotesk','Inter',system-ui,sans-serif;
  }
  .ow-pe *{box-sizing:border-box;}
  .ow-pe a{text-decoration:none;}
  .ow-pe img{display:block;max-width:100%;height:auto;}

  .ow-pe__inner{max-width:1300px;margin:0 auto;}
  .ow-pe__section{padding:72px 40px;border-top:1px solid var(--line);}
  .ow-pe__section--alt{background:var(--bg-2);}
  .ow-pe__section-title{
    font-family:'Anton','Bebas Neue',sans-serif;font-size:clamp(26px,3vw,36px);
    text-transform:uppercase;letter-spacing:.5px;margin:0 0 28px;
  }
  .ow-pe__section-head{
    display:flex;align-items:flex-end;justify-content:space-between;
    gap:20px;flex-wrap:wrap;margin-bottom:28px;
  }
  .ow-pe__section-head .ow-pe__section-title{margin:0;}

  .ow-pe__eyebrow{
    display:inline-flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:11px;letter-spacing:2.4px;text-transform:uppercase;
    color:var(--accent);border:1px solid rgba(195,251,51,.35);
    border-radius:999px;padding:7px 16px;margin-bottom:20px;
  }
  .ow-pe__eyebrow::before{
    content:"";width:6px;height:6px;border-radius:50%;background:var(--accent);
  }

  .ow-pe__btn{
    display:inline-flex;align-items:center;gap:10px;
    padding:14px 24px;border-radius:999px;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:12px;letter-spacing:1.4px;text-transform:uppercase;font-weight:600;
    border:1px solid transparent;transition:transform .2s ease,gap .2s ease,background .2s ease;
  }
  .ow-pe__btn:hover{transform:translateY(-2px);gap:14px;}
  .ow-pe__btn--primary{background:var(--accent);color:#0a0a14;}
  .ow-pe__btn--ghost{background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.18);color:#fff;}
  .ow-pe__btn--ghost:hover{background:rgba(255,255,255,.08);border-color:var(--accent);}
  .ow-pe__arrow{width:16px;height:16px;flex:0 0 auto;}

  .ow-pe__back{
    display:inline-flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:11px;letter-spacing:1.6px;text-transform:uppercase;color:var(--dim);
    margin-bottom:22px;transition:color .2s ease,gap .2s ease;
  }
  .ow-pe__back:hover{color:var(--accent);gap:12px;}
  .ow-pe__back svg{width:14px;height:14px;transform:rotate(180deg);}

  /* ---- hero ---- */
  .ow-pe__hero{position:relative;padding:64px 40px 56px;overflow:hidden;}
  /* z-index 0, not a negative one: a negative z-index paints the photo behind
     the theme's own opaque background and it never shows at all. The copy is
     lifted above it instead. */
  .ow-pe__hero-photo{position:absolute;inset:0;z-index:0;pointer-events:none;}
  /* Brighter than the rental page's hero, where the photo is only texture
     behind centred text. Here the cover photo is the point of the page, so
     it is dimmed only as far as the copy needs: heavily on the left where
     the text sits, barely at all on the right. */
  .ow-pe__hero-photo img{width:100%;height:100%;object-fit:cover;object-position:center;opacity:.62;}
  .ow-pe__hero-photo::after{
    content:"";position:absolute;inset:0;
    background:
      linear-gradient(90deg,rgba(10,10,20,.94) 0%,rgba(10,10,20,.8) 38%,rgba(10,10,20,.3) 78%,rgba(10,10,20,.15) 100%),
      linear-gradient(180deg,rgba(10,10,20,.3) 0%,rgba(10,10,20,.1) 40%,rgba(10,10,20,.88) 88%,#0a0a14 100%);
  }
  .ow-pe__hero-inner{position:relative;z-index:2;max-width:1300px;margin:0 auto;}
  .ow-pe__title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(38px,6vw,76px);line-height:.98;text-transform:uppercase;
    letter-spacing:-1px;margin:0 0 18px;max-width:18ch;
  }
  .ow-pe__lead{font-size:clamp(16px,1.5vw,19px);line-height:1.55;color:var(--fg);max-width:68ch;margin:0 0 24px;}
  .ow-pe__facts{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:26px;}
  .ow-pe__fact{
    display:inline-flex;align-items:center;gap:8px;
    background:rgba(255,255,255,.05);border:1px solid var(--line);
    border-radius:999px;padding:8px 16px;font-size:13px;color:var(--fg);
  }
  .ow-pe__fact svg{width:15px;height:15px;color:var(--accent);flex:0 0 auto;}
  .ow-pe__hero-ctas{display:flex;flex-wrap:wrap;gap:12px;}

  .ow-pe__stats{
    display:grid;grid-template-columns:repeat(3,1fr);gap:0;
    border:1px solid rgba(195,251,51,.28);border-radius:20px;overflow:hidden;
    background:rgba(19,19,31,.75);margin-top:36px;max-width:760px;
  }
  .ow-pe__stat{padding:24px 26px;border-right:1px solid var(--line);}
  .ow-pe__stat:last-child{border-right:0;}
  .ow-pe__stat strong{
    display:block;font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(30px,3.4vw,44px);color:var(--accent);line-height:1;margin-bottom:8px;
  }
  .ow-pe__stat span{font-size:13.5px;color:var(--dim);line-height:1.35;}

  /* ---- write-up ---- */
  .ow-pe__prose{max-width:74ch;font-size:16.5px;line-height:1.72;color:var(--dim);}
  .ow-pe__prose p{margin:0 0 18px;}
  .ow-pe__prose h2,.ow-pe__prose h3{
    font-family:'Anton','Bebas Neue',sans-serif;color:var(--fg);
    text-transform:uppercase;letter-spacing:.4px;margin:32px 0 12px;
  }
  .ow-pe__prose h2{font-size:26px;}
  .ow-pe__prose h3{font-size:20px;}
  .ow-pe__prose ul,.ow-pe__prose ol{margin:0 0 18px;padding-left:22px;}
  .ow-pe__prose li{margin-bottom:8px;}
  .ow-pe__prose a{color:var(--accent);text-decoration:underline;text-underline-offset:3px;}
  .ow-pe__prose strong{color:var(--fg);}
  .ow-pe__prose img{border-radius:14px;margin:8px 0 20px;}
  .ow-pe__prose blockquote{
    border-left:2px solid var(--accent);margin:0 0 20px;padding:4px 0 4px 20px;
    color:var(--fg);font-size:18px;
  }

  /* ---- games ---- */
  /* --cols is set on the element from how many games the client filled in,
     so two games are two sensible cards rather than two stretched ones. */
  .ow-pe__games{
    display:grid;grid-template-columns:repeat(var(--cols,4),minmax(0,1fr));
    gap:18px;max-width:calc(var(--cols,4) * 330px);
  }
  .ow-pe__game{
    background:var(--bg-2);border:1px solid var(--line);border-radius:16px;
    overflow:hidden;display:flex;flex-direction:column;
    transition:transform .25s ease,border-color .25s ease;
  }
  a.ow-pe__game:hover{transform:translateY(-4px);border-color:rgba(195,251,51,.45);}
  .ow-pe__game-media{aspect-ratio:4/3;overflow:hidden;background:rgba(255,255,255,.04);}
  .ow-pe__game-media img{width:100%;height:100%;object-fit:cover;}
  .ow-pe__game-body{padding:18px 20px 20px;}
  .ow-pe__game-name{
    font-family:'Anton','Bebas Neue',sans-serif;font-size:19px;
    text-transform:uppercase;letter-spacing:.3px;margin:0 0 6px;color:var(--fg);
  }
  .ow-pe__game-text{font-size:14px;line-height:1.45;color:var(--dim);margin:0;}

  /* ---- photo grid ---- */
  .ow-pe__photos{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;}
  .ow-pe__photo{
    position:relative;display:block;border-radius:16px;overflow:hidden;
    background:var(--bg-2);border:1px solid var(--line);aspect-ratio:4/3;
    cursor:zoom-in;transition:border-color .25s ease,transform .25s ease;
  }
  .ow-pe__photo:first-child{grid-column:span 2;grid-row:span 2;aspect-ratio:auto;}
  .ow-pe__photo img{width:100%;height:100%;object-fit:cover;transition:transform .5s ease;}
  .ow-pe__photo:hover{border-color:rgba(195,251,51,.45);}
  .ow-pe__photo:hover img{transform:scale(1.04);}

  .ow-pe__lightbox{
    position:fixed;inset:0;z-index:99999;background:rgba(5,5,12,.94);
    display:flex;align-items:center;justify-content:center;padding:32px;
  }
  .ow-pe__lightbox img{max-width:min(1200px,92vw);max-height:88vh;width:auto;border-radius:12px;}
  .ow-pe__lightbox-close{
    position:absolute;top:18px;right:20px;width:44px;height:44px;border-radius:50%;
    background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.2);
    color:#fff;font-size:22px;line-height:1;cursor:pointer;
  }
  .ow-pe__lightbox-close:hover{background:var(--accent);color:#0a0a14;}

  /* ---- cards (listing + "more events") ---- */
  .ow-pe__grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
  .ow-pe__card{
    position:relative;display:flex;flex-direction:column;
    background:var(--bg-2);border:1px solid var(--line);border-radius:18px;
    overflow:hidden;transition:transform .25s ease,border-color .25s ease,box-shadow .25s ease;
  }
  .ow-pe__card::before{
    content:"";position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(90deg,var(--accent),transparent);
    opacity:0;transition:opacity .25s ease;z-index:2;
  }
  .ow-pe__card:hover{
    transform:translateY(-6px);border-color:rgba(195,251,51,.45);
    box-shadow:0 30px 60px -40px rgba(195,251,51,.5);
  }
  .ow-pe__card:hover::before{opacity:1;}
  .ow-pe__card-media{aspect-ratio:16/10;overflow:hidden;background:rgba(255,255,255,.04);position:relative;}
  .ow-pe__card-media img{width:100%;height:100%;object-fit:cover;transition:transform .5s ease;}
  .ow-pe__card:hover .ow-pe__card-media img{transform:scale(1.05);}
  .ow-pe__card-media--empty{display:flex;align-items:center;justify-content:center;}
  .ow-pe__card-media--empty svg{width:44px;height:44px;color:rgba(195,251,51,.45);}
  .ow-pe__card-tag{
    position:absolute;left:14px;top:14px;z-index:2;
    background:rgba(10,10,20,.82);border:1px solid rgba(195,251,51,.4);
    color:var(--accent);border-radius:999px;padding:6px 12px;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:10px;letter-spacing:1.6px;text-transform:uppercase;
  }
  .ow-pe__card-count{
    position:absolute;right:14px;bottom:14px;z-index:2;
    background:rgba(10,10,20,.78);border:1px solid var(--line);
    color:#fff;border-radius:999px;padding:5px 11px;
    font-family:'JetBrains Mono',ui-monospace,monospace;font-size:10px;letter-spacing:1.2px;
  }
  .ow-pe__card-body{padding:20px 22px 22px;display:flex;flex-direction:column;gap:8px;flex:1;}
  .ow-pe__card-name{
    font-family:'Anton','Bebas Neue',sans-serif;font-size:21px;line-height:1.12;
    text-transform:uppercase;letter-spacing:.3px;margin:0;color:var(--fg);
  }
  .ow-pe__card-meta{
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:10.5px;letter-spacing:1.4px;text-transform:uppercase;color:var(--dim);
  }
  .ow-pe__card-blurb{font-size:14.5px;line-height:1.5;color:var(--dim);margin:0;}
  .ow-pe__card-more{
    margin-top:auto;padding-top:12px;display:inline-flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:11px;letter-spacing:1.4px;text-transform:uppercase;color:var(--accent);
  }
  .ow-pe__card:hover .ow-pe__card-more{gap:12px;}

  .ow-pe__empty{
    border:1px dashed rgba(195,251,51,.35);border-radius:18px;
    padding:44px 28px;text-align:center;color:var(--dim);font-size:15px;line-height:1.6;
  }

  /* ---- CTA ---- */
  .ow-pe__cta{padding:72px 40px 90px;background:var(--bg);border-top:1px solid var(--line);}
  .ow-pe__cta-inner{
    max-width:900px;margin:0 auto;text-align:center;
    background:var(--bg-2);border:1px solid rgba(195,251,51,.28);
    border-radius:24px;padding:48px 40px;
  }
  .ow-pe__cta-title{
    font-family:'Anton','Bebas Neue',sans-serif;font-size:clamp(26px,3.2vw,38px);
    text-transform:uppercase;margin:0 0 14px;
  }
  .ow-pe__cta-text{color:var(--dim);font-size:15.5px;line-height:1.6;margin:0 auto 26px;max-width:60ch;}
  .ow-pe__cta-buttons{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}

  @media(max-width:1024px){
    .ow-pe__hero{padding:52px 28px 46px;}
    .ow-pe__section,.ow-pe__cta{padding-left:28px;padding-right:28px;}
    .ow-pe__grid{grid-template-columns:repeat(2,1fr);}
    .ow-pe__games{grid-template-columns:repeat(min(var(--cols,4),2),minmax(0,1fr));max-width:none;}
    .ow-pe__photos{grid-template-columns:repeat(3,1fr);}
  }
  @media(max-width:640px){
    .ow-pe__hero{padding:38px 18px 36px;}
    .ow-pe__hero-photo img{opacity:.5;}
    .ow-pe__hero-photo::after{
      background:linear-gradient(180deg,rgba(10,10,20,.5) 0%,rgba(10,10,20,.75) 45%,#0a0a14 100%);
    }
    .ow-pe__section{padding:44px 18px;}
    .ow-pe__cta{padding:44px 18px 60px;}
    .ow-pe__cta-inner{padding:34px 22px;}
    .ow-pe__grid{grid-template-columns:1fr;max-width:520px;margin:0 auto;}
    .ow-pe__games{grid-template-columns:1fr;max-width:520px;margin:0 auto;}
    .ow-pe__photos{grid-template-columns:repeat(2,1fr);gap:10px;}
    .ow-pe__photo:first-child{grid-column:span 2;grid-row:auto;aspect-ratio:4/3;}
    .ow-pe__stats{grid-template-columns:1fr;max-width:none;}
    .ow-pe__stat{border-right:0;border-bottom:1px solid var(--line);padding:18px 20px;}
    .ow-pe__stat:last-child{border-bottom:0;}
    .ow-pe__hero-ctas .ow-pe__btn,.ow-pe__cta-buttons .ow-pe__btn{width:100%;justify-content:center;}
  }
CSS;
}

/**
 * The click-to-enlarge script for the photo grid. Vanilla, ~30 lines, no
 * library: the site already loads enough JavaScript.
 *
 * @return string
 */
function ow_pe_lightbox_script() {
	return <<<'JS'
(function(){
  var grid = document.querySelector('.ow-pe__photos');
  if(!grid){return;}
  var box = null;
  function close(){ if(box){ box.remove(); box = null; document.removeEventListener('keydown', onKey); } }
  function onKey(e){ if(e.key === 'Escape'){ close(); } }
  grid.addEventListener('click', function(e){
    var link = e.target.closest('.ow-pe__photo');
    if(!link || !link.dataset.full){ return; }
    e.preventDefault();
    close();
    box = document.createElement('div');
    box.className = 'ow-pe__lightbox';
    box.innerHTML = '<button class="ow-pe__lightbox-close" aria-label="Close">&times;</button>';
    var img = document.createElement('img');
    img.src = link.dataset.full;
    img.alt = link.dataset.alt || '';
    box.appendChild(img);
    box.addEventListener('click', function(ev){ if(ev.target !== img){ close(); } });
    document.body.appendChild(box);
    document.addEventListener('keydown', onKey);
    box.querySelector('.ow-pe__lightbox-close').focus();
  });
})();
JS;
}

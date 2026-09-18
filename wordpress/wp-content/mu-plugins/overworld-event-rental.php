<?php
/**
 * Plugin Name: Overworld — Event Rental Page (Interactive Game Rental for Events)
 * Description: Makes the /event-rental/ page (template page-event-rental.php) fully client-editable — hero, intro, activity cards, "what's included", "suitable for", FAQ and the bottom call to action — and wires its title, description, keywords and FAQ structured data through overworld-seo.php.
 * Author: Overworld
 * Version: 1.0.0
 *
 * Must-use plugin: auto-loads, no activation needed.
 *
 * Every field is optional. Left empty, the page falls back to the built-in
 * copy below, so the page looks finished the moment it is created and the
 * client only has to touch what they want to change — same convention as the
 * event hub pages (overworld-event-hub-content.php).
 *
 * Defaults live here rather than in page-event-rental.php because the SEO
 * output (title, meta description, FAQ schema) needs them too.
 *
 * Lists (activities, what's included, suitable for, FAQ) are numbered slots
 * rather than an ACF repeater: the site runs ACF free, which has no repeater
 * field. Filling in any slot of a list replaces that list's built-in copy
 * entirely, so the client is never left with a mix of their rows and ours.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_RENTAL_TEMPLATE = 'page-event-rental.php';

const OW_RENTAL_ACTIVITY_SLOTS = 6;
const OW_RENTAL_INCLUDED_SLOTS = 6;
const OW_RENTAL_AUDIENCE_SLOTS = 6;
const OW_RENTAL_FAQ_SLOTS      = 6;

/**
 * Built-in copy for the page.
 *
 * @return array
 */
function ow_rental_defaults() {
	return array(
		'seo_title' => 'Interactive Game Rental for Events in Singapore | Overworld',
		'meta_desc' => 'Rent VR free roam, Floor Is Lava and XR party games for your event in Singapore. Overworld delivers, sets up and runs the games at your venue — corporate events, family days, schools, roadshows and community events.',

		// Hero
		'h1'            => 'Interactive Game Rental',
		'h1_accent'     => 'For Events',
		'tagline'       => 'Bring crowd-pulling VR and interactive games directly to your venue.',
		'primary_label' => 'View Activities',
		'primary_url'   => '#activities',
		'ghost_label'   => 'Get A Quote',
		'ghost_url'     => '/contact/',

		// Intro
		'intro_title' => 'Make Your Event More Interactive',
		'intro_text'  => 'We provide engaging VR and interactive game rentals that turn any space into a fun, social and memorable experience for your guests.',

		// Activities
		'activities_title'    => 'Our Event Rental Activities',
		'activities_all_label' => 'View All Activities',
		'activities_all_url'   => '', // The site has no all-activities page yet; the link stays hidden until one is set.
		'activities'          => array(
			array(
				'title'   => 'VR Free Roam',
				'text'    => 'Explore virtual worlds together in a free-roam multiplayer experience.',
				'url'     => '/vr-free-roam/',
				'seo_key' => 'VR Free Roam',
			),
			array(
				'title'   => 'Floor Is Lava',
				'text'    => 'Move, jump and survive in a real-life lava game show.',
				'url'     => '/floor-is-lava/',
				'seo_key' => 'Floor Is Lava',
			),
			array(
				'title'   => 'XR Party Game',
				'text'    => 'A next-gen party game experience for all ages.',
				'url'     => '/xr-party-game/',
				'seo_key' => 'XR Party Game',
			),
		),
		'activity_link_label' => 'Learn More',
		'coming_soon_label'   => 'More Activities Coming Soon',

		// What's included
		'included_title' => "What's Included",
		'included'       => array(
			array( 'icon' => 'truck',     'label' => 'Delivery' ),
			array( 'icon' => 'cog',       'label' => 'Setup & Testing' ),
			array( 'icon' => 'operators', 'label' => 'On-site Operators' ),
			array( 'icon' => 'headset',   'label' => 'Technical Support' ),
			array( 'icon' => 'clipboard', 'label' => 'Player Briefing' ),
			array( 'icon' => 'wrench',    'label' => 'Dismantling' ),
		),

		// Suitable for
		'audience_title' => 'Suitable For',
		'audience'       => array(
			array( 'icon' => 'briefcase', 'label' => 'Corporate Events' ),
			array( 'icon' => 'family',    'label' => 'Family Days' ),
			array( 'icon' => 'school',    'label' => 'Schools' ),
			array( 'icon' => 'flag',      'label' => 'Roadshows' ),
			array( 'icon' => 'community', 'label' => 'Community Events' ),
		),

		// FAQ
		'faq_title'     => 'Frequently Asked Questions',
		'faq_all_label' => 'View All FAQs',
		'faq_all_url'   => '/faq/',
		'faqs'          => array(
			array(
				'q' => 'What space do we need?',
				'a' => 'Each activity has its own footprint, and most fit comfortably into a function room, atrium or open-air tentage. Send us your venue dimensions or a floor plan and we will confirm which activities fit and how best to lay them out.',
			),
			array(
				'q' => 'How many people can play at once?',
				'a' => 'It depends on the activity and how many stations you rent. Our games run in short rounds so a queue keeps moving, and we can advise on the right setup for your expected crowd and event duration.',
			),
			array(
				'q' => 'Do you provide staff?',
				'a' => 'Yes. Every rental comes with our own operators on site for the full event. They set up, brief every player, run the sessions, handle any technical issues and pack down when it ends.',
			),
			array(
				'q' => 'Can you customise the experience for our event?',
				'a' => 'Yes. Tell us the theme, the audience and what you want your guests to walk away with, and we will recommend the game mix, session length and setup that fit. Branding and scoreboard options can be discussed for larger events.',
			),
		),

		// Bottom CTA
		'cta_title'  => 'Plan Your Event With Us',
		'cta_text'   => "Tell us about your event and we'll recommend the best activities for your venue.",
		'cta_label'  => 'Request A Quotation',
		'cta_url'    => '/contact/',
	);
}

/**
 * A field's value, falling back to the built-in copy when the client has not
 * filled it in.
 *
 * @param int    $post_id Page ID.
 * @param string $name    Meta key without the 'rental_' prefix.
 * @param mixed  $default Fallback; when null, the built-in default for $name.
 * @return string
 */
function ow_rental_val( $post_id, $name, $default = null ) {
	if ( null === $default ) {
		$defaults = ow_rental_defaults();
		$default  = isset( $defaults[ $name ] ) ? $defaults[ $name ] : '';
	}

	$value = trim( (string) get_post_meta( $post_id, 'rental_' . $name, true ) );

	return '' !== $value ? $value : (string) $default;
}

/**
 * A relative URL from the client ('/contact/', '#activities') made absolute;
 * anything already absolute is left alone.
 *
 * @param string $url URL as typed.
 * @return string
 */
function ow_rental_url( $url ) {
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
 * The hero image: the client's upload, else nothing (the template paints a
 * gradient panel in its place).
 *
 * @param int $post_id Page ID.
 * @return int Attachment ID, or 0.
 */
function ow_rental_hero_image_id( $post_id ) {
	$id = (int) get_post_meta( $post_id, 'rental_hero_image', true );

	return ( $id && 'attachment' === get_post_type( $id ) ) ? $id : 0;
}

/**
 * Activity cards: client slots first, built-in copy when none are filled in.
 *
 * Each row: title, text, url, image_id (0 = none), image_url ('' = none).
 * Built-in rows borrow the photo the outlet pages already use for that
 * activity (via overworld-seo.php), so the page has real pictures before the
 * client uploads any.
 *
 * @param int $post_id Page ID.
 * @return array
 */
function ow_rental_activities( $post_id ) {
	$rows = array();

	for ( $i = 1; $i <= OW_RENTAL_ACTIVITY_SLOTS; $i++ ) {
		$title = trim( (string) get_post_meta( $post_id, "rental_act_{$i}_title", true ) );
		if ( '' === $title ) {
			continue;
		}
		$image_id = (int) get_post_meta( $post_id, "rental_act_{$i}_image", true );
		if ( $image_id && 'attachment' !== get_post_type( $image_id ) ) {
			$image_id = 0;
		}
		$rows[] = array(
			'title'     => $title,
			'text'      => trim( (string) get_post_meta( $post_id, "rental_act_{$i}_text", true ) ),
			'url'       => ow_rental_url( get_post_meta( $post_id, "rental_act_{$i}_url", true ) ),
			'image_id'  => $image_id,
			'image_url' => $image_id ? (string) wp_get_attachment_image_url( $image_id, 'large' ) : '',
		);
	}

	if ( $rows ) {
		return $rows;
	}

	$defaults = ow_rental_defaults();
	foreach ( $defaults['activities'] as $row ) {
		$image_url = '';
		if ( function_exists( 'ow_seo_activity_image' ) ) {
			$image_url = (string) ow_seo_activity_image( $row['seo_key'], $row['url'] );
		}
		$rows[] = array(
			'title'     => $row['title'],
			'text'      => $row['text'],
			'url'       => ow_rental_url( $row['url'] ),
			'image_id'  => 0,
			'image_url' => $image_url,
		);
	}

	return $rows;
}

/**
 * Icon + label lists ("what's included", "suitable for"): client slots first,
 * built-in copy when none are filled in.
 *
 * @param int    $post_id  Page ID.
 * @param string $prefix   Slot meta prefix, e.g. 'inc' or 'aud'.
 * @param int    $slots    Number of slots.
 * @param array  $defaults Built-in rows.
 * @return array<int, array{icon: string, label: string}>
 */
function ow_rental_icon_list( $post_id, $prefix, $slots, array $defaults ) {
	$rows = array();
	for ( $i = 1; $i <= $slots; $i++ ) {
		$label = trim( (string) get_post_meta( $post_id, "rental_{$prefix}_{$i}_label", true ) );
		if ( '' === $label ) {
			continue;
		}
		$rows[] = array(
			'icon'  => sanitize_key( (string) get_post_meta( $post_id, "rental_{$prefix}_{$i}_icon", true ) ),
			'label' => $label,
		);
	}

	return $rows ? $rows : $defaults;
}

/**
 * FAQ rows: client slots first, built-in copy when none are filled in.
 *
 * @param int $post_id Page ID.
 * @return array<int, array{q: string, a: string}>
 */
function ow_rental_faqs( $post_id ) {
	$faqs = array();
	for ( $i = 1; $i <= OW_RENTAL_FAQ_SLOTS; $i++ ) {
		$q = trim( (string) get_post_meta( $post_id, "rental_faq_{$i}_q", true ) );
		$a = trim( (string) get_post_meta( $post_id, "rental_faq_{$i}_a", true ) );
		if ( '' === $q || '' === $a ) {
			continue;
		}
		$faqs[] = array( 'q' => $q, 'a' => $a );
	}

	if ( $faqs ) {
		return $faqs;
	}

	$defaults = ow_rental_defaults();

	return $defaults['faqs'];
}

/**
 * Should the activity grid be padded with "coming soon" placeholders?
 *
 * Defaults to on — the reference design shows three real cards over three
 * placeholders — and the client can switch it off once the list is full.
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function ow_rental_show_placeholders( $post_id ) {
	$value = get_post_meta( $post_id, 'rental_activities_placeholders', true );

	return ( '' === $value || null === $value ) ? true : (bool) $value;
}

/**
 * The built-in icon set. Keyed by the value stored in the icon select field.
 *
 * Plain stroked SVGs on a 24-unit grid so they inherit the accent colour from
 * CSS. Kept small on purpose: a client picking from a dozen names is easier
 * than one pasting SVG.
 *
 * @return array<string, array{label: string, svg: string}>
 */
function ow_rental_icons() {
	return array(
		'truck'     => array(
			'label' => 'Truck (delivery)',
			'svg'   => '<path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/>',
		),
		'cog'       => array(
			'label' => 'Cog (setup)',
			'svg'   => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
		),
		'operators' => array(
			'label' => 'People (operators / team)',
			'svg'   => '<circle cx="12" cy="8" r="3"/><circle cx="5" cy="10" r="2.2"/><circle cx="19" cy="10" r="2.2"/><path d="M7 20v-1a5 5 0 0 1 10 0v1M1.5 19v-.5a3.5 3.5 0 0 1 4-3.5M22.5 19v-.5a3.5 3.5 0 0 0-4-3.5"/>',
		),
		'headset'   => array(
			'label' => 'Headset (support)',
			'svg'   => '<path d="M4 13v-1a8 8 0 0 1 16 0v1"/><path d="M4 13h3v5H5a1 1 0 0 1-1-1zM20 13h-3v5h2a1 1 0 0 0 1-1z"/><path d="M17 18v1a2 2 0 0 1-2 2h-2"/>',
		),
		'clipboard' => array(
			'label' => 'Clipboard (briefing)',
			'svg'   => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V3h6v1M9 10h6M9 14h6M9 18h3"/>',
		),
		'wrench'    => array(
			'label' => 'Wrench (dismantling)',
			'svg'   => '<path d="M14.5 3.5a5 5 0 0 0-5.3 6.9L3 16.6 6.4 20l6.2-6.2a5 5 0 0 0 6.9-5.3l-3 3-2.8-.7-.7-2.8z"/>',
		),
		'briefcase' => array(
			'label' => 'Briefcase (corporate)',
			'svg'   => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2M3 12h18"/>',
		),
		'family'    => array(
			'label' => 'Family (two people)',
			'svg'   => '<circle cx="9" cy="8" r="3"/><circle cx="17" cy="9.5" r="2.2"/><path d="M3 20v-1a6 6 0 0 1 12 0v1M15 20v-.5a4 4 0 0 1 6 0v.5"/>',
		),
		'school'    => array(
			'label' => 'Graduation cap (schools)',
			'svg'   => '<path d="M2 9l10-4 10 4-10 4z"/><path d="M6 11v4c0 1.5 2.7 3 6 3s6-1.5 6-3v-4M22 9v5"/>',
		),
		'flag'      => array(
			'label' => 'Flag (roadshows)',
			'svg'   => '<path d="M5 21V4"/><path d="M5 4h12l-2.5 3.5L17 11H5"/>',
		),
		'community' => array(
			'label' => 'Community (three people)',
			'svg'   => '<circle cx="12" cy="7" r="2.6"/><circle cx="5.5" cy="10" r="2"/><circle cx="18.5" cy="10" r="2"/><path d="M8 20v-1.5a4 4 0 0 1 8 0V20M2 19v-1a3 3 0 0 1 3.5-3M22 19v-1a3 3 0 0 0-3.5-3"/>',
		),
		'gamepad'   => array(
			'label' => 'Game controller',
			'svg'   => '<path d="M7 8h10a4 4 0 0 1 4 4l-.8 4.5a2 2 0 0 1-3.6.7L15 15H9l-1.6 2.2a2 2 0 0 1-3.6-.7L3 12a4 4 0 0 1 4-4z"/><path d="M8 11v3M6.5 12.5h3"/><circle cx="16" cy="11.5" r=".6"/><circle cx="18" cy="13.5" r=".6"/>',
		),
		'clock'     => array(
			'label' => 'Clock',
			'svg'   => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
		),
		'shield'    => array(
			'label' => 'Shield (safety)',
			'svg'   => '<path d="M12 3l7 3v5c0 4.5-3 8.2-7 10-4-1.8-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/>',
		),
		'star'      => array(
			'label' => 'Star',
			'svg'   => '<path d="M12 3.5l2.6 5.4 5.9.8-4.3 4.1 1.1 5.9L12 16.9l-5.3 2.8 1.1-5.9-4.3-4.1 5.9-.8z"/>',
		),
		'pin'       => array(
			'label' => 'Map pin (venue)',
			'svg'   => '<path d="M12 21s-6-5.5-6-11a6 6 0 0 1 12 0c0 5.5-6 11-6 11z"/><circle cx="12" cy="10" r="2.2"/>',
		),
		'vr'        => array(
			'label' => 'VR headset',
			'svg'   => '<rect x="2.5" y="8" width="19" height="9" rx="3"/><path d="M9.5 15.5a2.5 2.5 0 0 1 5 0M8 6h8"/>',
		),
	);
}

/**
 * One icon's SVG markup, or the game-controller when the key is unknown.
 *
 * @param string $key Icon key.
 * @return string
 */
function ow_rental_icon( $key ) {
	$icons = ow_rental_icons();
	$key   = isset( $icons[ $key ] ) ? $key : 'gamepad';

	return '<svg class="ow-rental__icon ow-rental__icon--' . esc_attr( $key ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $icons[ $key ]['svg'] . '</svg>';
}

/**
 * Is this page the event rental page?
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function ow_is_rental_page( $post_id ) {
	return OW_RENTAL_TEMPLATE === get_post_meta( (int) $post_id, '_wp_page_template', true );
}

/**
 * IDs of the pages using the event rental template.
 *
 * Looked up by template rather than written down, so the page can be renamed
 * or re-slugged without this file changing. Cached for the request.
 *
 * @return int[]
 */
function ow_rental_page_ids() {
	static $ids = null;

	if ( null !== $ids ) {
		return $ids;
	}

	$ids = get_posts(
		array(
			'post_type'        => 'page',
			'post_status'      => array( 'publish', 'draft', 'private' ),
			'numberposts'      => -1,
			'fields'           => 'ids',
			'meta_key'         => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'       => OW_RENTAL_TEMPLATE,  // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'suppress_filters' => false,
		)
	);

	$ids = array_map( 'intval', (array) $ids );

	return $ids;
}

// ===== ACF fields =====
add_action( 'acf/init', function () {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$d = ow_rental_defaults();

	$icon_choices = array( '' => '— pick an icon —' );
	foreach ( ow_rental_icons() as $key => $icon ) {
		$icon_choices[ $key ] = $icon['label'];
	}

	$fields = array();

	// --- Hero ---
	$fields[] = array(
		'key'          => 'field_rental_hero_tab',
		'label'        => 'Top Of Page',
		'type'         => 'accordion',
		'open'         => 1,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_rental_h1',
		'label'        => 'Main Heading (H1) — first line',
		'name'         => 'rental_h1',
		'type'         => 'text',
		'instructions' => 'Shown in white. Default: "' . $d['h1'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key'          => 'field_rental_h1_accent',
		'label'        => 'Main Heading — second line',
		'name'         => 'rental_h1_accent',
		'type'         => 'text',
		'instructions' => 'Shown in the accent colour. Default: "' . $d['h1_accent'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key'          => 'field_rental_tagline',
		'label'        => 'Text Under The Heading',
		'name'         => 'rental_tagline',
		'type'         => 'textarea',
		'rows'         => 2,
		'instructions' => 'Default: "' . $d['tagline'] . '"',
		'required'     => 0,
	);
	$fields[] = array(
		'key'           => 'field_rental_hero_image',
		'label'         => 'Hero Photo',
		'name'          => 'rental_hero_image',
		'type'          => 'image',
		'instructions'  => 'The large photo beside the heading. Landscape, at least 1200px wide. Also used as the sharing image when the page is sent on WhatsApp, unless the SEO box below sets its own.',
		'return_format' => 'id',
		'preview_size'  => 'medium',
		'required'      => 0,
	);
	$fields[] = array(
		'key'          => 'field_rental_primary_label',
		'label'        => 'Main Button — Text',
		'name'         => 'rental_primary_label',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['primary_label'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '25' ),
	);
	$fields[] = array(
		'key'          => 'field_rental_primary_url',
		'label'        => 'Main Button — Link',
		'name'         => 'rental_primary_url',
		'type'         => 'text',
		'instructions' => '"#activities" scrolls to the activities. Default: "' . $d['primary_url'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '25' ),
	);
	$fields[] = array(
		'key'          => 'field_rental_ghost_label',
		'label'        => 'Second Button — Text',
		'name'         => 'rental_ghost_label',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['ghost_label'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '25' ),
	);
	$fields[] = array(
		'key'          => 'field_rental_ghost_url',
		'label'        => 'Second Button — Link',
		'name'         => 'rental_ghost_url',
		'type'         => 'text',
		'instructions' => 'A page like "/contact/", or a WhatsApp link. Default: "' . $d['ghost_url'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '25' ),
	);

	// --- Intro ---
	$fields[] = array(
		'key'          => 'field_rental_intro_tab',
		'label'        => 'Intro Section',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_rental_intro_title',
		'label'        => 'Heading',
		'name'         => 'rental_intro_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['intro_title'] . '"',
		'required'     => 0,
	);
	$fields[] = array(
		'key'          => 'field_rental_intro_text',
		'label'        => 'Text',
		'name'         => 'rental_intro_text',
		'type'         => 'textarea',
		'rows'         => 3,
		'instructions' => 'One or two sentences. Default: "' . $d['intro_text'] . '"',
		'required'     => 0,
	);

	// --- Activities ---
	$fields[] = array(
		'key'          => 'field_rental_activities_tab',
		'label'        => 'Activities (up to ' . OW_RENTAL_ACTIVITY_SLOTS . ' cards)',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_rental_activities_title',
		'label'        => 'Section Heading',
		'name'         => 'rental_activities_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['activities_title'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '40' ),
	);
	$fields[] = array(
		'key'          => 'field_rental_activities_all_label',
		'label'        => 'Top-Right Link — Text',
		'name'         => 'rental_activities_all_label',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['activities_all_label'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '30' ),
	);
	$fields[] = array(
		'key'          => 'field_rental_activities_all_url',
		'label'        => 'Top-Right Link — Goes To',
		'name'         => 'rental_activities_all_url',
		'type'         => 'text',
		'instructions' => 'e.g. a page that lists every activity. Leave empty to hide the link (the default, as the site has no such page yet).',
		'required'     => 0,
		'wrapper'      => array( 'width' => '30' ),
	);
	$fields[] = array(
		'key'          => 'field_rental_activity_link_label',
		'label'        => 'Card Button Text',
		'name'         => 'rental_activity_link_label',
		'type'         => 'text',
		'instructions' => 'The button on every card. Default: "' . $d['activity_link_label'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '40' ),
	);
	$fields[] = array(
		'key'           => 'field_rental_activities_placeholders',
		'label'         => 'Show "Coming Soon" Cards',
		'name'          => 'rental_activities_placeholders',
		'type'          => 'true_false',
		'instructions'  => 'Fills the grid up to ' . OW_RENTAL_ACTIVITY_SLOTS . ' with "coming soon" cards. Switch off once every slot is a real activity.',
		'default_value' => 1,
		'ui'            => 1,
		'required'      => 0,
		'wrapper'       => array( 'width' => '30' ),
	);
	$fields[] = array(
		'key'          => 'field_rental_coming_soon_label',
		'label'        => '"Coming Soon" Card Text',
		'name'         => 'rental_coming_soon_label',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['coming_soon_label'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '30' ),
	);
	for ( $i = 1; $i <= OW_RENTAL_ACTIVITY_SLOTS; $i++ ) {
		$fields[] = array(
			'key'           => "field_rental_act_{$i}_image",
			'label'         => "Activity {$i} — Photo",
			'name'          => "rental_act_{$i}_image",
			'type'          => 'image',
			'instructions'  => 1 === $i ? 'Landscape, at least 800px wide.' : '',
			'return_format' => 'id',
			'preview_size'  => 'medium',
			'required'      => 0,
			'wrapper'       => array( 'width' => '25' ),
		);
		$fields[] = array(
			'key'          => "field_rental_act_{$i}_title",
			'label'        => "Activity {$i} — Name",
			'name'         => "rental_act_{$i}_title",
			'type'         => 'text',
			'instructions' => 1 === $i ? 'Filling in any activity replaces the three built-in cards (VR Free Roam, Floor Is Lava, XR Party Game). Leave a name empty to skip that slot.' : 'Leave empty to skip this slot.',
			'required'     => 0,
			'wrapper'      => array( 'width' => '25' ),
		);
		$fields[] = array(
			'key'      => "field_rental_act_{$i}_text",
			'label'    => "Activity {$i} — One-Line Description",
			'name'     => "rental_act_{$i}_text",
			'type'     => 'text',
			'required' => 0,
			'wrapper'  => array( 'width' => '30' ),
		);
		$fields[] = array(
			'key'          => "field_rental_act_{$i}_url",
			'label'        => "Activity {$i} — Button Goes To",
			'name'         => "rental_act_{$i}_url",
			'type'         => 'text',
			'instructions' => 1 === $i ? 'e.g. "/vr-free-roam/". Leave empty to hide the button.' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '20' ),
		);
	}

	// --- What's included ---
	$fields[] = array(
		'key'          => 'field_rental_included_tab',
		'label'        => "What's Included (up to " . OW_RENTAL_INCLUDED_SLOTS . ' items)',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_rental_included_title',
		'label'        => 'Section Heading',
		'name'         => 'rental_included_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['included_title'] . '"',
		'required'     => 0,
	);
	for ( $i = 1; $i <= OW_RENTAL_INCLUDED_SLOTS; $i++ ) {
		$fields[] = array(
			'key'      => "field_rental_inc_{$i}_icon",
			'label'    => "Item {$i} — Icon",
			'name'     => "rental_inc_{$i}_icon",
			'type'     => 'select',
			'choices'  => $icon_choices,
			'required' => 0,
			'wrapper'  => array( 'width' => '40' ),
		);
		$fields[] = array(
			'key'          => "field_rental_inc_{$i}_label",
			'label'        => "Item {$i} — Text",
			'name'         => "rental_inc_{$i}_label",
			'type'         => 'text',
			'instructions' => 1 === $i ? 'Filling in any item replaces the built-in list (Delivery, Setup & Testing, On-site Operators, Technical Support, Player Briefing, Dismantling). Leave empty to skip a slot.' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '60' ),
		);
	}

	// --- Suitable for ---
	$fields[] = array(
		'key'          => 'field_rental_audience_tab',
		'label'        => 'Suitable For (up to ' . OW_RENTAL_AUDIENCE_SLOTS . ' items)',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_rental_audience_title',
		'label'        => 'Section Heading',
		'name'         => 'rental_audience_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['audience_title'] . '"',
		'required'     => 0,
	);
	for ( $i = 1; $i <= OW_RENTAL_AUDIENCE_SLOTS; $i++ ) {
		$fields[] = array(
			'key'      => "field_rental_aud_{$i}_icon",
			'label'    => "Item {$i} — Icon",
			'name'     => "rental_aud_{$i}_icon",
			'type'     => 'select',
			'choices'  => $icon_choices,
			'required' => 0,
			'wrapper'  => array( 'width' => '40' ),
		);
		$fields[] = array(
			'key'          => "field_rental_aud_{$i}_label",
			'label'        => "Item {$i} — Text",
			'name'         => "rental_aud_{$i}_label",
			'type'         => 'text',
			'instructions' => 1 === $i ? 'Filling in any item replaces the built-in list (Corporate Events, Family Days, Schools, Roadshows, Community Events). Leave empty to skip a slot.' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '60' ),
		);
	}

	// --- FAQ ---
	$fields[] = array(
		'key'          => 'field_rental_faq_tab',
		'label'        => 'FAQ (up to ' . OW_RENTAL_FAQ_SLOTS . ')',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_rental_faq_title',
		'label'        => 'Section Heading',
		'name'         => 'rental_faq_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['faq_title'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '40' ),
	);
	$fields[] = array(
		'key'          => 'field_rental_faq_all_label',
		'label'        => 'Top-Right Link — Text',
		'name'         => 'rental_faq_all_label',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['faq_all_label'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '30' ),
	);
	$fields[] = array(
		'key'          => 'field_rental_faq_all_url',
		'label'        => 'Top-Right Link — Goes To',
		'name'         => 'rental_faq_all_url',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['faq_all_url'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '30' ),
	);
	for ( $i = 1; $i <= OW_RENTAL_FAQ_SLOTS; $i++ ) {
		$fields[] = array(
			'key'          => "field_rental_faq_{$i}_q",
			'label'        => "Question {$i}",
			'name'         => "rental_faq_{$i}_q",
			'type'         => 'text',
			'instructions' => 1 === $i ? 'Filling in any question replaces the built-in FAQ list entirely. Questions here also feed the FAQ box Google can show under your result.' : '',
			'required'     => 0,
		);
		$fields[] = array(
			'key'      => "field_rental_faq_{$i}_a",
			'label'    => "Answer {$i}",
			'name'     => "rental_faq_{$i}_a",
			'type'     => 'textarea',
			'rows'     => 3,
			'required' => 0,
		);
	}

	// --- CTA ---
	$fields[] = array(
		'key'          => 'field_rental_cta_tab',
		'label'        => 'Bottom Call To Action',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_rental_cta_title',
		'label'        => 'Heading',
		'name'         => 'rental_cta_title',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['cta_title'] . '"',
		'required'     => 0,
	);
	$fields[] = array(
		'key'          => 'field_rental_cta_text',
		'label'        => 'Text',
		'name'         => 'rental_cta_text',
		'type'         => 'textarea',
		'rows'         => 2,
		'instructions' => 'Default: "' . $d['cta_text'] . '"',
		'required'     => 0,
	);
	$fields[] = array(
		'key'          => 'field_rental_cta_label',
		'label'        => 'Button — Text',
		'name'         => 'rental_cta_label',
		'type'         => 'text',
		'instructions' => 'Default: "' . $d['cta_label'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key'          => 'field_rental_cta_url',
		'label'        => 'Button — Link',
		'name'         => 'rental_cta_url',
		'type'         => 'text',
		'instructions' => 'A page like "/contact/", or a WhatsApp link. Default: "' . $d['cta_url'] . '"',
		'required'     => 0,
		'wrapper'      => array( 'width' => '50' ),
	);

	$fields[] = array(
		'key'      => 'field_rental_end',
		'label'    => '',
		'type'     => 'accordion',
		'endpoint' => 1,
	);

	acf_add_local_field_group( array(
		'key'             => 'group_ow_event_rental',
		'title'           => 'Event Rental Page Content',
		'fields'          => $fields,
		'location'        => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => OW_RENTAL_TEMPLATE,
				),
			),
		),
		'menu_order'      => 2,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
		'description'     => 'Everything on the event rental page, top to bottom. Every field is optional — leave one empty and the built-in text is used instead. The search title and description live in the SEO box below.',
	) );
} );

// ===== Search-engine plumbing (through overworld-seo.php) =====

/**
 * Title and description. The per-page SEO box overrides these.
 */
add_filter( 'ow_seo_page_map', function ( $map ) {
	$d = ow_rental_defaults();
	foreach ( ow_rental_page_ids() as $id ) {
		$map[ $id ] = array(
			'title' => $d['seo_title'],
			'desc'  => $d['meta_desc'],
		);
	}

	return $map;
} );

/**
 * Keywords. Same caveat as everywhere else in overworld-seo.php: emitted
 * because it was asked for, kept to an honest description of the page.
 */
add_filter( 'ow_seo_page_keywords', function ( $map ) {
	foreach ( ow_rental_page_ids() as $id ) {
		$map[ $id ] = array(
			'interactive game rental Singapore',
			'VR rental for events Singapore',
			'event game rental Singapore',
			'VR free roam rental',
			'Floor Is Lava rental',
			'XR party game rental',
			'corporate event games Singapore',
			'family day activities Singapore',
			'school event activities Singapore',
			'roadshow activities Singapore',
			'community event games Singapore',
			'event entertainment rental Singapore',
		);
	}

	return $map;
} );

/**
 * FAQPage and Service nodes for the rental page — the questions actually
 * rendered, and only those.
 */
add_filter( 'ow_seo_schema_graph', function ( $graph, $seo ) {
	$post_id = ( isset( $seo['post'] ) && $seo['post'] instanceof WP_Post ) ? $seo['post']->ID : 0;
	if ( ! $post_id || ! ow_is_rental_page( $post_id ) ) {
		return $graph;
	}

	$org = home_url( '/' ) . '#organization';

	$faqs = ow_rental_faqs( $post_id );
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

	$offered = array();
	foreach ( ow_rental_activities( $post_id ) as $row ) {
		$offered[] = array(
			'@type' => 'Offer',
			'name'  => $row['title'],
		);
	}

	$service = array(
		'@type'       => 'Service',
		'@id'         => $seo['url'] . '#service',
		'name'        => trim( ow_rental_val( $post_id, 'h1' ) . ' ' . ow_rental_val( $post_id, 'h1_accent' ) ),
		'serviceType' => 'Interactive game rental for events',
		'description' => $seo['desc'],
		'provider'    => array( '@id' => $org ),
		'areaServed'  => array(
			'@type' => 'Country',
			'name'  => 'Singapore',
		),
	);
	if ( $offered ) {
		$service['hasOfferCatalog'] = array(
			'@type'           => 'OfferCatalog',
			'name'            => ow_rental_val( $post_id, 'activities_title' ),
			'itemListElement' => $offered,
		);
	}
	$graph[] = $service;

	return $graph;
}, 10, 2 );

/**
 * Keep the page's template locked, the same way the outlet and hub pages
 * are: opening a page in Elementor used to reset its template to Default and
 * render it blank.
 */
add_filter( 'ow_guarded_page_templates', function ( $map ) {
	foreach ( ow_rental_page_ids() as $id ) {
		$map[ $id ] = OW_RENTAL_TEMPLATE;
	}

	return $map;
} );

/**
 * After the client saves:
 *   - the hero photo becomes the page's featured image, so overworld-seo.php
 *     picks it up as the sharing image without a second upload;
 *   - Elementor's per-page render cache is cleared so the change shows up
 *     immediately.
 */
add_action( 'acf/save_post', function ( $post_id ) {
	if ( ! is_numeric( $post_id ) || ! ow_is_rental_page( $post_id ) ) {
		return;
	}

	$hero = ow_rental_hero_image_id( (int) $post_id );
	if ( $hero && (int) get_post_thumbnail_id( (int) $post_id ) !== $hero ) {
		set_post_thumbnail( (int) $post_id, $hero );
	}

	delete_post_meta( $post_id, '_elementor_element_cache' );
}, 20 );

<?php
/**
 * Plugin Name: Overworld — Event Landing Pages (Team Building / Birthday Party)
 * Description: Makes the /team-building/ and /birthday-party/ hub pages fully client-editable (hero, intro copy, outlet blurbs, "why us" points, FAQ, CTA) and gives them the search-engine plumbing a landing page needs: title tag, meta description, Open Graph/Twitter cards, and FAQPage + Breadcrumb structured data.
 * Author: Overworld
 * Version: 1.0.0
 *
 * Every field is optional. Left empty, the page falls back to the built-in
 * copy for that event type, so the pages look the same until someone edits
 * them — same convention as the outlet pages.
 *
 * Defaults live here rather than in page-event-hub.php because the SEO
 * output (title, meta description) needs them too.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_HUB_TEMPLATE = 'page-event-hub.php';

/**
 * Built-in copy per event type. Keyed by page slug.
 *
 * @param string $slug Page slug ('team-building' | 'birthday-party').
 * @return array
 */
function ow_hub_defaults( $slug ) {

	$defaults = array(

		'team-building' => array(
			'seo_title'  => 'Team Building Activities in Singapore | Overworld',
			'meta_desc'  => 'Team building activities at three Overworld outlets in Singapore — VR arcades, escape rooms, laser maze and Floor Is Lava. Compare packages at Kallang, Orchard and Funan.',
			'eyebrow'    => 'Team Building At Overworld',
			'h1'         => 'Team Building Activities in Singapore',
			'tagline'    => 'Stronger squads. Sharper teams.',
			'intro'      => 'Bring your team out of the office and into the action. Three outlets across Singapore, each with its own mix of VR and physical challenges — pick the one that fits your crew.',
			'body'       => 'Overworld runs team building sessions at three locations in Singapore: Kallang Wave Mall, Orchard Central and Funan. Every venue puts your team into shared challenges rather than a meeting room — virtual reality missions, timed physical games, and rooms that only open when people talk to each other. Sessions suit new-hire ice breakers, department offsites and end-of-year celebrations alike. Pick the outlet nearest your office, or the activity mix your team will enjoy most, and browse that outlet\'s packages below.',
			'points_title' => 'Why Teams Book Overworld',
			'points'     => array(
				array( 'icon' => '🎯', 'title' => 'Built Around Teamwork', 'text' => 'Escape rooms, co-op VR missions and head-to-head games that only work when people communicate.' ),
				array( 'icon' => '📍', 'title' => 'Three Locations', 'text' => 'Kallang, Orchard and Funan — all near MRT, so the whole team can get there easily.' ),
				array( 'icon' => '🕹️', 'title' => 'No Experience Needed', 'text' => 'Every activity is pick-up-and-play. First-timers and gamers end up on level ground.' ),
				array( 'icon' => '💬', 'title' => 'We Help You Plan', 'text' => 'Tell us your group size and date, and we will recommend the outlet and package that fit.' ),
			),
			'faq_title'  => 'Team Building FAQs',
			'faqs'       => array(
				array(
					'q' => 'Which Overworld outlet is best for team building?',
					'a' => 'It depends on the mix you want. Kallang Wave Mall is the VR-heavy outlet, with the arcade, VR escape rooms and the VR machine ride. Orchard Central is entirely physical — Floor Is Lava, Laser Maze and Tap Tap. Funan sits in between, with free-roam VR, XR party games and Floor Is Lava. If you are unsure, message us with your group size and we will point you to the right one.',
				),
				array(
					'q' => 'How do we book a team building session?',
					'a' => 'Open the outlet you want below and view its packages, then WhatsApp or call that outlet to confirm your date and group size. We will hold the slot and confirm the details with you.',
				),
				array(
					'q' => 'What activities can the team do?',
					'a' => 'Across the three outlets: VR Arcade, VR Escape rooms, VR Machine Ride, VR Free Roam, XR Party Game, Floor Is Lava, Laser Maze and Tap Tap. Each outlet page lists exactly what is available there.',
				),
			),
			'cta_eyebrow' => 'Not Sure Which Outlet?',
			'cta_title'   => 'We\'ll Point You To The Right One',
			'cta_text'    => 'Tell us your group size, preferred date, and the vibe you\'re after — we\'ll recommend the outlet and package that fit best.',
		),

		'birthday-party' => array(
			'seo_title'  => 'Birthday Party Venues in Singapore | Overworld',
			'meta_desc'  => 'Birthday party packages at three Overworld outlets in Singapore — VR, Floor Is Lava, laser maze and XR party games. Compare venues at Kallang, Orchard and Funan.',
			'eyebrow'    => 'Birthday Parties At Overworld',
			'h1'         => 'Birthday Party Venues in Singapore',
			'tagline'    => 'A birthday they\'ll actually remember.',
			'intro'      => 'Forget cake-and-balloons. Throw a birthday that\'s loud, active, and full of bragging rights — at any of our three outlets across Singapore.',
			'body'       => 'Overworld hosts birthday parties at three locations in Singapore: Kallang Wave Mall, Orchard Central and Funan. Instead of sitting around a table, the group spends the session inside the games — virtual reality worlds, a floor that turns into lava, laser grids and big-screen party rounds everyone can join. It works for kids\' parties, teen celebrations and adult birthdays that want something louder than dinner. Pick the outlet closest to you, or the games the birthday guest will love most, and browse that outlet\'s packages below.',
			'points_title' => 'Why Birthdays Work Here',
			'points'     => array(
				array( 'icon' => '🎉', 'title' => 'Everyone Plays', 'text' => 'Party games run in groups, so nobody sits out watching — including the adults.' ),
				array( 'icon' => '📍', 'title' => 'Three Locations', 'text' => 'Kallang, Orchard and Funan — all near MRT, easy for guests coming from anywhere.' ),
				array( 'icon' => '🎮', 'title' => 'No Setup Needed', 'text' => 'The games are the entertainment. Turn up, play, and let us run the session.' ),
				array( 'icon' => '💬', 'title' => 'We Help You Plan', 'text' => 'Tell us the age group and headcount, and we will recommend the outlet and package that fit.' ),
			),
			'faq_title'  => 'Birthday Party FAQs',
			'faqs'       => array(
				array(
					'q' => 'Which Overworld outlet is best for a birthday party?',
					'a' => 'Kallang Wave Mall is the VR-heavy outlet, with the arcade, VR escape rooms and the VR machine ride. Orchard Central is entirely physical — Floor Is Lava, Laser Maze and Tap Tap — which suits younger, high-energy groups. Funan mixes both, with free-roam VR, XR party games and Floor Is Lava. Message us with the age group and headcount if you would like a recommendation.',
				),
				array(
					'q' => 'How do we book a birthday party?',
					'a' => 'Open the outlet you want below and view its packages, then WhatsApp or call that outlet to confirm your date and group size. We will hold the slot and confirm the details with you.',
				),
				array(
					'q' => 'What games are included?',
					'a' => 'Across the three outlets: VR Arcade, VR Escape rooms, VR Machine Ride, VR Free Roam, XR Party Game, Floor Is Lava, Laser Maze and Tap Tap. Each outlet page lists exactly what is available there.',
				),
			),
			'cta_eyebrow' => 'Not Sure Which Outlet?',
			'cta_title'   => 'We\'ll Point You To The Right One',
			'cta_text'    => 'Tell us the age group, headcount and date you have in mind — we\'ll recommend the outlet and package that fit best.',
		),
	);

	return isset( $defaults[ $slug ] ) ? $defaults[ $slug ] : $defaults['team-building'];
}

/**
 * A field's value, falling back to the built-in copy when the client has not
 * filled it in.
 *
 * @param int    $post_id Page ID.
 * @param string $name    Meta key without the 'hub_' prefix.
 * @param mixed  $default Fallback.
 * @return string
 */
function ow_hub_val( $post_id, $name, $default = '' ) {
	$value = trim( (string) get_post_meta( $post_id, 'hub_' . $name, true ) );

	return '' !== $value ? $value : (string) $default;
}

/**
 * "Why us" points: client rows first, built-in copy when none are filled in.
 *
 * @param int   $post_id  Page ID.
 * @param array $defaults Built-in points.
 * @return array
 */
function ow_hub_points( $post_id, array $defaults ) {
	$points = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$title = trim( (string) get_post_meta( $post_id, "hub_point_{$i}_title", true ) );
		if ( '' === $title ) {
			continue;
		}
		$points[] = array(
			'icon'  => trim( (string) get_post_meta( $post_id, "hub_point_{$i}_icon", true ) ),
			'title' => $title,
			'text'  => trim( (string) get_post_meta( $post_id, "hub_point_{$i}_text", true ) ),
		);
	}

	return $points ? $points : $defaults;
}

/**
 * FAQ rows: client rows first, built-in copy when none are filled in.
 *
 * @param int   $post_id  Page ID.
 * @param array $defaults Built-in FAQs.
 * @return array
 */
function ow_hub_faqs( $post_id, array $defaults ) {
	$faqs = array();
	for ( $i = 1; $i <= 6; $i++ ) {
		$q = trim( (string) get_post_meta( $post_id, "hub_faq_{$i}_q", true ) );
		$a = trim( (string) get_post_meta( $post_id, "hub_faq_{$i}_a", true ) );
		if ( '' === $q || '' === $a ) {
			continue;
		}
		$faqs[] = array( 'q' => $q, 'a' => $a );
	}

	return $faqs ? $faqs : $defaults;
}

/**
 * Is this page one of the event hub landing pages?
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function ow_is_hub_page( $post_id ) {
	return OW_HUB_TEMPLATE === get_post_meta( (int) $post_id, '_wp_page_template', true );
}

// ===== ACF fields =====
add_action( 'acf/init', function () {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$fields = array();

	// --- Search & sharing ---
	$fields[] = array(
		'key'   => 'field_hub_seo_tab',
		'label' => 'Google & Sharing',
		'type'  => 'accordion',
		'open'  => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_hub_seo_title',
		'label'        => 'Google Result Title',
		'name'         => 'hub_seo_title',
		'type'         => 'text',
		'instructions' => 'The clickable headline in Google and the browser tab. Aim for under 60 characters. Leave empty to use the built-in default.',
		'required'     => 0,
	);
	$fields[] = array(
		'key'          => 'field_hub_meta_desc',
		'label'        => 'Google Description',
		'name'         => 'hub_meta_desc',
		'type'         => 'textarea',
		'rows'         => 3,
		'instructions' => 'The grey text under the title in Google results. Aim for 140–160 characters. Leave empty to use the built-in default.',
		'required'     => 0,
	);
	$fields[] = array(
		'key'           => 'field_hub_share_image',
		'label'         => 'Sharing Image',
		'name'          => 'hub_share_image',
		'type'          => 'image',
		'instructions'  => 'Shown when the page is shared on WhatsApp, Facebook or LinkedIn. Landscape, at least 1200×630.',
		'return_format' => 'id',
		'preview_size'  => 'medium',
		'required'      => 0,
	);

	// --- Hero ---
	$fields[] = array(
		'key'   => 'field_hub_hero_tab',
		'label' => 'Top Of Page',
		'type'  => 'accordion',
		'open'  => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_hub_eyebrow',
		'label'        => 'Small Line Above The Heading',
		'name'         => 'hub_eyebrow',
		'type'         => 'text',
		'required'     => 0,
		'wrapper'      => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key'          => 'field_hub_h1',
		'label'        => 'Main Heading (H1)',
		'name'         => 'hub_h1',
		'type'         => 'text',
		'instructions' => 'The biggest text on the page, and what Google reads first. Include what you offer and where, e.g. "Team Building Activities in Singapore".',
		'required'     => 0,
		'wrapper'      => array( 'width' => '50' ),
	);
	$fields[] = array(
		'key'      => 'field_hub_tagline',
		'label'    => 'Tagline',
		'name'     => 'hub_tagline',
		'type'     => 'text',
		'required' => 0,
	);
	$fields[] = array(
		'key'      => 'field_hub_intro',
		'label'    => 'Intro Paragraph',
		'name'     => 'hub_intro',
		'type'     => 'textarea',
		'rows'     => 3,
		'required' => 0,
	);

	// --- Body copy ---
	$fields[] = array(
		'key'   => 'field_hub_body_tab',
		'label' => 'About This Page (search copy)',
		'type'  => 'accordion',
		'open'  => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_hub_body',
		'label'        => 'Body Copy',
		'name'         => 'hub_body',
		'type'         => 'textarea',
		'rows'         => 6,
		'instructions' => 'A few sentences describing what you offer and where. This is the text Google has to work with, so write it for a person deciding whether to book. Blank lines start a new paragraph.',
		'required'     => 0,
	);

	// --- Why us ---
	$fields[] = array(
		'key'   => 'field_hub_points_tab',
		'label' => 'Why Book With Us (4 points)',
		'type'  => 'accordion',
		'open'  => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_hub_points_title',
		'label'        => 'Section Heading',
		'name'         => 'hub_points_title',
		'type'         => 'text',
		'required'     => 0,
	);
	for ( $i = 1; $i <= 4; $i++ ) {
		$fields[] = array(
			'key'          => "field_hub_point_{$i}_icon",
			'label'        => "Point {$i} — Icon",
			'name'         => "hub_point_{$i}_icon",
			'type'         => 'text',
			'instructions' => 'An emoji, e.g. 🎯',
			'required'     => 0,
			'wrapper'      => array( 'width' => '15' ),
		);
		$fields[] = array(
			'key'          => "field_hub_point_{$i}_title",
			'label'        => "Point {$i} — Title",
			'name'         => "hub_point_{$i}_title",
			'type'         => 'text',
			'instructions' => 'Leave empty to hide this point.',
			'required'     => 0,
			'wrapper'      => array( 'width' => '35' ),
		);
		$fields[] = array(
			'key'      => "field_hub_point_{$i}_text",
			'label'    => "Point {$i} — Text",
			'name'     => "hub_point_{$i}_text",
			'type'     => 'text',
			'required' => 0,
			'wrapper'  => array( 'width' => '50' ),
		);
	}

	// --- Outlet blurbs ---
	$fields[] = array(
		'key'   => 'field_hub_outlets_tab',
		'label' => 'Outlet Card Blurbs',
		'type'  => 'accordion',
		'open'  => 0,
		'multi_expand' => 1,
	);
	foreach ( array(
		'kallang' => 'Kallang Wave Mall',
		'orchard' => 'Orchard Central',
		'funan'   => 'Funan',
	) as $key => $label ) {
		$fields[] = array(
			'key'          => "field_hub_blurb_{$key}",
			'label'        => $label,
			'name'         => "hub_blurb_{$key}",
			'type'         => 'textarea',
			'rows'         => 2,
			'instructions' => 'One or two lines on this outlet, for this event type. Leave empty to use the built-in text.',
			'required'     => 0,
		);
	}

	// --- FAQ ---
	$fields[] = array(
		'key'          => 'field_hub_faq_tab',
		'label'        => 'FAQ (up to 6)',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'          => 'field_hub_faq_title',
		'label'        => 'Section Heading',
		'name'         => 'hub_faq_title',
		'type'         => 'text',
		'required'     => 0,
	);
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = array(
			'key'          => "field_hub_faq_{$i}_q",
			'label'        => "Question {$i}",
			'name'         => "hub_faq_{$i}_q",
			'type'         => 'text',
			'instructions' => 1 === $i ? 'Filling in any question replaces the built-in FAQ list entirely. Questions here also feed the FAQ box Google can show under your result.' : '',
			'required'     => 0,
		);
		$fields[] = array(
			'key'      => "field_hub_faq_{$i}_a",
			'label'    => "Answer {$i}",
			'name'     => "hub_faq_{$i}_a",
			'type'     => 'textarea',
			'rows'     => 3,
			'required' => 0,
		);
	}

	// --- CTA ---
	$fields[] = array(
		'key'          => 'field_hub_cta_tab',
		'label'        => 'Bottom Call To Action',
		'type'         => 'accordion',
		'open'         => 0,
		'multi_expand' => 1,
	);
	$fields[] = array(
		'key'      => 'field_hub_cta_eyebrow',
		'label'    => 'Small Line',
		'name'     => 'hub_cta_eyebrow',
		'type'     => 'text',
		'required' => 0,
		'wrapper'  => array( 'width' => '40' ),
	);
	$fields[] = array(
		'key'      => 'field_hub_cta_title',
		'label'    => 'Heading',
		'name'     => 'hub_cta_title',
		'type'     => 'text',
		'required' => 0,
		'wrapper'  => array( 'width' => '60' ),
	);
	$fields[] = array(
		'key'      => 'field_hub_cta_text',
		'label'    => 'Text',
		'name'     => 'hub_cta_text',
		'type'     => 'textarea',
		'rows'     => 2,
		'required' => 0,
	);

	$fields[] = array(
		'key'      => 'field_hub_end',
		'label'    => '',
		'type'     => 'accordion',
		'endpoint' => 1,
	);

	acf_add_local_field_group( array(
		'key'             => 'group_ow_event_hub',
		'title'           => 'Landing Page Content',
		'fields'          => $fields,
		'location'        => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => OW_HUB_TEMPLATE,
				),
			),
		),
		'menu_order'      => 2,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
		'description'     => 'Everything on this landing page. Every field is optional — leave one empty and the built-in text is used instead.',
	) );
} );

// ===== Search-engine plumbing =====

/**
 * Title tag.
 */
add_filter( 'document_title_parts', function ( $parts ) {
	if ( ! is_page() ) {
		return $parts;
	}
	$post_id = get_queried_object_id();
	if ( ! ow_is_hub_page( $post_id ) ) {
		return $parts;
	}

	$defaults = ow_hub_defaults( get_post_field( 'post_name', $post_id ) );
	$title    = ow_hub_val( $post_id, 'seo_title', $defaults['seo_title'] );

	return array( 'title' => $title );
} );

/**
 * Meta description, social cards and structured data.
 */
add_action( 'wp_head', function () {
	if ( ! is_page() ) {
		return;
	}
	$post_id = get_queried_object_id();
	if ( ! ow_is_hub_page( $post_id ) ) {
		return;
	}

	$slug     = get_post_field( 'post_name', $post_id );
	$defaults = ow_hub_defaults( $slug );
	$desc     = ow_hub_val( $post_id, 'meta_desc', $defaults['meta_desc'] );
	$title    = ow_hub_val( $post_id, 'seo_title', $defaults['seo_title'] );
	$url      = get_permalink( $post_id );

	$image_id = get_post_meta( $post_id, 'hub_share_image', true );
	$image    = ( $image_id && is_numeric( $image_id ) ) ? wp_get_attachment_image_url( (int) $image_id, 'full' ) : '';

	echo "\n<!-- Overworld landing page meta -->\n";
	printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:type" content="website" />' . "\n" );
	printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $desc ) );
	printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	if ( $image ) {
		printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image ) );
	}
	printf( '<meta name="twitter:card" content="%s" />' . "\n", $image ? 'summary_large_image' : 'summary' );
	printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $desc ) );

	$graph = array();

	// Breadcrumbs
	$graph[] = array(
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Home',
				'item'     => home_url( '/' ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => get_the_title( $post_id ),
				'item'     => $url,
			),
		),
	);

	// FAQ — only what is actually rendered on the page.
	$faqs = ow_hub_faqs( $post_id, $defaults['faqs'] );
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
			'mainEntity' => $entities,
		);
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode(
			array( '@context' => 'https://schema.org', '@graph' => $graph ),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		)
	);
}, 2 );

/**
 * Elementor caches rendered output per page; clear it when these pages are
 * edited so copy changes show up immediately.
 */
add_action( 'acf/save_post', function ( $post_id ) {
	if ( is_numeric( $post_id ) && ow_is_hub_page( $post_id ) ) {
		delete_post_meta( $post_id, '_elementor_element_cache' );
	}
}, 20 );

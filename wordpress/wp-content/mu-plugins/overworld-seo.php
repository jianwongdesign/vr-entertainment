<?php
/**
 * Plugin Name: Overworld — SEO Metadata
 * Description: Site-wide search-engine metadata: title tag, meta description, robots, Open Graph, Twitter cards and schema.org structured data for every page, post, experience, event package and promo. Hand-written defaults per page, all client-editable through an "SEO" box on the edit screen.
 * Author: Overworld
 * Version: 1.0.0
 *
 * WHY THIS EXISTS
 * There is no SEO plugin on this site. Before this plugin, almost every URL
 * shipped the WordPress fallback title ("VR Arcade – Overworld") and no meta
 * description at all, so Google wrote its own snippets from whatever text it
 * found first. Only three places had any metadata: single.php (blog posts),
 * home.php (blog index) and overworld-event-hub-content.php (the two event
 * landing pages).
 *
 * This plugin becomes the single source of truth. The meta blocks were removed
 * from single.php and home.php and folded in here. The event-hub plugin still
 * owns /team-building/ and /birthday-party/ because its copy is driven by that
 * page's own ACF fields — this plugin detects those pages and stays out.
 *
 * DEFAULTS ARE KEYED BY PAGE ID, NOT SLUG.
 * Twelve pages share three slugs — "kallang-wave-mall" is page 504 (outlet),
 * 524 (team building), 527 (birthday party) and 530 (gift voucher). Matching
 * on slug would give four different pages the same metadata. Same reasoning as
 * overworld-outlet-template-guard.php.
 *
 * Every default can be overridden per post in the SEO box, and a page that has
 * no entry in the map still gets a sensible generated title and description
 * rather than nothing.
 *
 * NOTE ON META KEYWORDS: not emitted. Google dropped it as a ranking signal in
 * 2009 and Bing treats it as a spam signal. The effort belongs in the title,
 * description and structured data instead.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_SEO_VERSION = '1.0.0';

/**
 * Post types that get an SEO box and metadata output.
 *
 * @return string[]
 */
function ow_seo_post_types() {
	return apply_filters( 'ow_seo_post_types', array( 'page', 'post', 'experience', 'event_package', 'promo' ) );
}

/**
 * The three outlets. Addresses, phones and hours are taken from the live
 * /contact/ and /faq/ pages — do not invent values here, they feed the
 * LocalBusiness structured data that Google may show in search results.
 *
 * @return array
 */
function ow_seo_outlets() {
	return array(
		'kallang' => array(
			'name'     => 'Overworld Kallang Wave Mall',
			'street'   => '1 Stadium Place, #01-63/64 Kallang Wave Mall',
			'postcode' => '397628',
			'phone'    => '+65 6513 0561',
			'email'    => 'support@overworldvr.com',
			'url'      => '/outlet/kallang-wave-mall/',
		),
		'orchard' => array(
			'name'     => 'Overworld Orchard Central',
			'street'   => '181 Orchard Road, #05-30/K1/K3 Orchard Central',
			'postcode' => '238896',
			'phone'    => '+65 8801 4303',
			'email'    => 'ocsupport@overworld.com.sg',
			'url'      => '/outlet/orchard-central/',
		),
		'funan'   => array(
			'name'     => 'Overworld Funan',
			'street'   => '107 North Bridge Road, #04-14 & K1 Funan',
			'postcode' => '179105',
			'phone'    => '+65 8914 0061',
			'email'    => 'funansupport@overworld.com.sg',
			'url'      => '/outlet/funan/',
		),
	);
}

/**
 * Hand-written metadata per page, keyed by post ID.
 *
 * 'noindex' => true marks a page that should stay out of the index. The five
 * flagged below are empty stubs (20 bytes of post_content, no H1, no Elementor
 * data) that were sitting in wp-sitemap.xml. Thin pages like these dilute the
 * crawl and can suppress the pages that do have content. Give them real copy
 * and the flag can come off.
 *
 * @return array<int, array{title: string, desc: string, noindex?: bool}>
 */
function ow_seo_page_map() {
	$map = array(

		// --- Core ---
		16   => array(
			'title' => 'VR Arcade & Immersive Game Arenas in Singapore | Overworld',
			'desc'  => "Singapore's home of VR arcades, VR escape rooms and physical game arenas. Three outlets at Kallang Wave Mall, Orchard Central and Funan. Open daily 11am-10pm.",
		),
		934  => array(
			'title' => 'About Overworld | VR & Physical Game Outlets in Singapore',
			'desc'  => 'Overworld brings VR, physical challenge rooms and group play together across three Singapore outlets - built for friends, families, schools and teams.',
		),
		935  => array(
			'title' => 'Contact Overworld | Kallang, Orchard & Funan Outlets',
			'desc'  => 'Phone, WhatsApp and email for all three Overworld outlets in Singapore. Contact the outlet you plan to visit for bookings, group events and enquiries.',
		),
		372  => array(
			'title' => 'FAQ | Opening Hours, Booking & Age Limits | Overworld',
			'desc'  => 'Answers on opening hours, booking, age and group limits, what to wear and how each activity works at Overworld Kallang, Orchard and Funan. Open daily 11am-10pm.',
		),
		1350 => array(
			'title' => 'The Overworld Blog | VR & Gaming in Singapore',
			'desc'  => "News, updates and behind-the-scenes stories from Singapore's home of VR gaming and immersive physical play across Kallang, Orchard and Funan.",
		),

		// --- Booking ---
		867  => array(
			'title' => 'Book Your Session | Overworld Singapore',
			'desc'  => 'Check live availability and book a session at Overworld Kallang Wave Mall, Orchard Central or Funan. Pick your outlet to see time slots and reserve online.',
		),
		898  => array(
			'title' => 'Book Overworld Kallang Wave Mall | Live Availability',
			'desc'  => 'Book VR Arcade, VR Escape, Floor Is Lava or VR Machine Ride at Overworld Kallang Wave Mall. Check live availability and reserve your time slot online.',
		),
		886  => array(
			'title' => 'Book Overworld Orchard Central | Live Availability',
			'desc'  => 'Book Floor Is Lava, Laser Maze or Tap Tap at Overworld Orchard Central. Check live availability and reserve your session online at 181 Orchard Road.',
		),
		893  => array(
			'title' => 'Book Overworld Funan | Live Availability',
			'desc'  => 'Book VR Free Roam, Floor Is Lava or XR Party Game at Overworld Funan. Check live availability and reserve your session online at 107 North Bridge Road.',
		),

		// --- Outlets ---
		503  => array(
			'title'   => 'Our Outlets | Overworld Singapore',
			'desc'    => 'Overworld has three outlets in Singapore - Kallang Wave Mall, Orchard Central and Funan. Compare activities, pricing and opening hours for each.',
			'noindex' => true, // Empty stub page.
		),
		504  => array(
			'title' => 'Overworld Kallang Wave Mall | VR Arcade & Floor Is Lava',
			'desc'  => "Singapore's premier VR arcade at Kallang Wave Mall - 30+ VR Arcade titles, VR Escape rooms, VR Machine Ride and Floor Is Lava. Pricing, combos and booking.",
		),
		505  => array(
			'title' => 'Overworld Orchard Central | Laser Maze & Floor Is Lava',
			'desc'  => 'Pure physical play in the heart of Orchard - Floor Is Lava, Laser Maze and Tap Tap. See pricing, combo deals and group packages at Orchard Central.',
		),
		506  => array(
			'title' => 'Overworld Funan | VR Free Roam & XR Party Games',
			'desc'  => 'Free-roam VR arenas, Floor Is Lava and big-screen XR party games at Overworld Funan. See pricing, combo deals and group packages at 107 North Bridge Road.',
		),

		// --- Activities ---
		326  => array(
			'title' => 'VR Arcade Singapore | 30+ VR Games at Kallang | Overworld',
			'desc'  => 'Pay for time, play everything. 30+ VR titles across shooters, rhythm, puzzle and party games, on up to 17 stations at Overworld Kallang Wave Mall.',
		),
		420  => array(
			'title' => 'VR Escape Room Singapore | 23 Rooms | Overworld',
			'desc'  => 'VR escape rooms without walls - 23 rooms across horror, adventure, mystery and fantasy. Team up, crack the puzzles and escape at Overworld Kallang Wave Mall.',
		),
		646  => array(
			'title' => 'VR Free Roam Singapore | Walk-In VR Arena at Funan',
			'desc'  => 'Untethered free-roam VR at Overworld Funan - walk, run, duck and fight in full physical space across 20+ games. Book a 25 or 50-minute session.',
		),
		338  => array(
			'title' => "VR Machine Ride | Singapore's First VR Motion Ride",
			'desc'  => "Singapore's first VR motion ride - a full-motion seat synced frame-by-frame with the headset. Coasters, starship runs and free-falls at Kallang Wave Mall.",
		),
		577  => array(
			'title' => 'XR Party Game Singapore | 6 Modes at Funan | Overworld',
			'desc'  => 'Mixed-reality party games on the big screen at Overworld Funan. Six modes, 25 minutes non-stop - your real-world moves control the carnival action.',
		),
		312  => array(
			'title' => 'Laser Maze Singapore | Orchard Central | Overworld',
			'desc'  => 'Slide, crawl and weave through a dark arena lit only by lasers. 30+ progressively tougher modes at Overworld Orchard Central. Book a 25-minute mission.',
		),
		294  => array(
			'title' => 'Floor Is Lava Singapore | Interactive Lava Floor | Overworld',
			'desc'  => "Don't touch the lava. An interactive LED floor that tests reflexes, teamwork and balance - at all three Overworld outlets: Kallang, Orchard and Funan.",
		),
		364  => array(
			'title' => 'Tap Tap Singapore | Reflex Light Wall at Orchard Central',
			'desc'  => 'Lights flash, patterns shift, the clock runs down. Tap the right ones faster than your friends on the light wall at Overworld Orchard Central.',
		),

		// --- Team building (per outlet) ---
		524  => array(
			'title' => 'Team Building at Overworld Kallang Wave Mall | Packages',
			'desc'  => 'Corporate team building at Overworld Kallang Wave Mall - VR escape rooms, arcade competitions, private venue hire, event photos and prizes. See packages.',
		),
		525  => array(
			'title' => 'Team Building at Overworld Orchard Central | Packages',
			'desc'  => 'Corporate team building at Overworld Orchard Central - Floor Is Lava, Laser Maze and Tap Tap challenges run by our Game Masters. Compare packages and enquire.',
		),
		526  => array(
			'title' => 'Team Building at Overworld Funan | Packages',
			'desc'  => 'Corporate team building at Overworld Funan - free-roam VR missions, Floor Is Lava and XR party rounds run by our Game Masters. Compare packages and enquire.',
		),

		// --- Birthday parties (per outlet) ---
		527  => array(
			'title' => 'Birthday Parties at Overworld Kallang Wave Mall',
			'desc'  => 'Throw a VR birthday party at Overworld Kallang Wave Mall - arcade, escape rooms, motion ride and lava floor. Compare party packages and enquire online.',
		),
		528  => array(
			'title' => 'Birthday Parties at Overworld Orchard Central',
			'desc'  => 'Throw an active birthday party at Overworld Orchard Central - Floor Is Lava, Laser Maze and Tap Tap. Compare party packages and enquire online.',
		),
		529  => array(
			'title' => 'Birthday Parties at Overworld Funan',
			'desc'  => 'Throw a birthday party at Overworld Funan - free-roam VR, Floor Is Lava and XR party games. Compare party packages and enquire online.',
		),

		// --- Gift vouchers (empty stubs) ---
		523  => array(
			'title'   => 'Gift Vouchers | Overworld Singapore',
			'desc'    => 'Give a day of VR, lava floors and immersive challenges. Overworld gift vouchers can be used at our Kallang Wave Mall, Orchard Central and Funan outlets.',
			'noindex' => true,
		),
		530  => array(
			'title'   => 'Gift Vouchers | Overworld Kallang Wave Mall',
			'desc'    => 'Overworld gift vouchers for the Kallang Wave Mall outlet - VR Arcade, VR Escape, VR Machine Ride and Floor Is Lava.',
			'noindex' => true,
		),
		531  => array(
			'title'   => 'Gift Vouchers | Overworld Orchard Central',
			'desc'    => 'Overworld gift vouchers for the Orchard Central outlet - Floor Is Lava, Laser Maze and Tap Tap.',
			'noindex' => true,
		),
		532  => array(
			'title'   => 'Gift Vouchers | Overworld Funan',
			'desc'    => 'Overworld gift vouchers for the Funan outlet - VR Free Roam, Floor Is Lava and XR Party Game.',
			'noindex' => true,
		),

		// --- Legal ---
		936  => array(
			'title' => 'Privacy Policy | Overworld Singapore',
			'desc'  => 'How Overworld collects, uses and protects personal data when you browse the site, contact an outlet, buy vouchers or make a booking.',
		),
		937  => array(
			'title' => 'Terms of Service | Overworld Singapore',
			'desc'  => 'The general conditions for using the Overworld website, making bookings and visiting our outlets - including safety, payments and promotions.',
		),
		938  => array(
			'title' => 'Refund & Booking Change Policy | Overworld Singapore',
			'desc'  => 'How Overworld handles refunds, rescheduling, vouchers, no-shows and late arrivals. Contact your outlet as early as possible for help with a booking.',
		),
	);

	return apply_filters( 'ow_seo_page_map', $map );
}

/**
 * Trim text to a description length without cutting a word in half.
 *
 * @param string $text Raw text.
 * @param int    $max  Maximum characters.
 * @return string
 */
function ow_seo_trim( $text, $max = 158 ) {
	$text = wp_strip_all_tags( (string) $text, true );
	$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
	$text = preg_replace( '/\s+/u', ' ', $text );
	$text = trim( $text );

	if ( '' === $text || mb_strlen( $text ) <= $max ) {
		return $text;
	}

	$cut   = mb_substr( $text, 0, $max - 1 );
	$space = mb_strrpos( $cut, ' ' );
	if ( false !== $space && $space > 40 ) {
		$cut = mb_substr( $cut, 0, $space );
	}

	return rtrim( $cut, " ,.;:-" ) . '…';
}

/**
 * Description text taken from the post itself: the excerpt, then the body.
 *
 * The excerpt is only accepted when it is long enough to actually describe the
 * page. Several experiences carry a category label as their excerpt — Beat
 * Saber's is the two words "VR Games" — and an eight-character description is
 * worse than none, so anything that short is skipped in favour of the real
 * copy held in custom fields.
 *
 * @param WP_Post $post Post object.
 * @param int     $min  Shortest excerpt worth using.
 * @return string
 */
function ow_seo_post_description( $post, $min = 60 ) {
	$excerpt = trim( wp_strip_all_tags( (string) $post->post_excerpt ) );
	if ( mb_strlen( $excerpt ) >= $min ) {
		return ow_seo_trim( $excerpt );
	}

	// Elementor keeps the real copy in meta, so post_content is often empty.
	$content = trim( wp_strip_all_tags( (string) $post->post_content ) );
	if ( mb_strlen( $content ) >= $min ) {
		return ow_seo_trim( $content );
	}

	return '';
}

/**
 * Top up a description that is too short to be useful in a search result.
 *
 * Promo taglines in particular are headline-length ("FREE 1-Hour Private Room
 * with Every Eligible Group Booking!"), which leaves most of the snippet
 * unused. Appending the relevant context uses the space without inventing
 * anything.
 *
 * @param string $desc    Description so far.
 * @param string $context Sentence to append when there is room.
 * @param int    $min     Length below which the context is added.
 * @return string
 */
function ow_seo_pad( $desc, $context, $min = 90 ) {
	$desc = trim( $desc );
	if ( '' === $desc ) {
		return ow_seo_trim( $context );
	}
	if ( mb_strlen( $desc ) >= $min ) {
		return ow_seo_trim( $desc );
	}

	return ow_seo_trim( rtrim( $desc, ' ' ) . ( preg_match( '/[.!?]$/u', $desc ) ? ' ' : '. ' ) . $context );
}

/**
 * Join title parts with " | ", dropping the least important ones from the end
 * until the whole thing fits a search result.
 *
 * Some post names are long on their own ("Unlock a FREE Private Room at
 * Overworld Funan!"). Appending a brand suffix regardless pushed those past
 * 60 characters, where Google truncates. Parts must be ordered most to least
 * important; the first is never dropped, only truncated as a last resort.
 *
 * @param string[] $parts Title parts, most important first.
 * @param int      $max   Target maximum length.
 * @return string
 */
function ow_seo_title_join( array $parts, $max = 60 ) {
	$parts = array_values( array_filter( array_map( 'trim', $parts ) ) );
	if ( ! $parts ) {
		return '';
	}

	while ( count( $parts ) > 1 && mb_strlen( implode( ' | ', $parts ) ) > $max ) {
		array_pop( $parts );
	}

	$title = implode( ' | ', $parts );

	return ( mb_strlen( $title ) > $max ) ? ow_seo_trim( $title, $max ) : $title;
}

/**
 * First non-empty custom field on a post.
 *
 * @param int      $post_id Post ID.
 * @param string[] $keys    Meta keys in order of preference.
 * @return string
 */
function ow_seo_meta_first( $post_id, array $keys ) {
	foreach ( $keys as $key ) {
		$value = get_post_meta( $post_id, $key, true );
		if ( is_string( $value ) ) {
			$value = trim( wp_strip_all_tags( $value ) );
			if ( '' !== $value ) {
				return $value;
			}
		}
	}

	return '';
}

/**
 * Generated metadata per post type, used when a post has no hand-written map
 * entry and no SEO field filled in.
 *
 * These post types carry real editorial copy in custom fields — exp_intro,
 * exp_tagline, event_tagline, promo_tagline — so the description is written
 * from that rather than from a template. It also keeps the 7 same-named posts
 * distinct: "Pixel Hack" exists as both a VR Arcade and a VR Free Roam title,
 * and every event package name appears once for team building and once for
 * birthday parties. Without the qualifier those pairs shipped byte-identical
 * titles and descriptions, which is the duplicate-content case Google
 * penalises.
 *
 * @param WP_Post $post Post object.
 * @return array{title: string, desc: string}
 */
function ow_seo_generated( $post ) {
	$name  = get_the_title( $post );
	$brand = get_bloginfo( 'name' );

	// Deliberately NOT computed up front: these post types keep better copy in
	// custom fields than in post_excerpt, so the excerpt is a fallback, not the
	// first choice.
	$desc = '';

	switch ( $post->post_type ) {

		case 'experience':
			// "VR Arcade" / "VR Escape" / "VR Free Roam".
			$terms     = wp_get_post_terms( $post->ID, 'experience_type', array( 'fields' => 'names' ) );
			$qualifier = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : '';

			$title = ow_seo_title_join( array( $name, $qualifier, 'Overworld' ) );

			$intro   = ow_seo_meta_first( $post->ID, array( 'exp_intro' ) );
			$tagline = ow_seo_meta_first( $post->ID, array( 'exp_tagline' ) );

			if ( mb_strlen( $intro ) >= 80 ) {
				$desc = $intro;
			} elseif ( '' !== $tagline ) {
				$desc = ow_seo_pad(
					$tagline,
					sprintf( 'Play %s at Overworld Singapore.', $name )
				);
			} else {
				$desc = ow_seo_post_description( $post );
			}

			$desc = ow_seo_pad(
				$desc,
				$qualifier
					? sprintf( 'A %s title at Overworld Singapore.', $qualifier )
					: 'Book a session at Overworld Singapore.'
			);
			break;

		case 'event_package':
			$type      = get_post_meta( $post->ID, 'event_type', true );
			$qualifier = ( 'birthday-party' === $type ) ? 'Birthday Party' : ( ( 'team-building' === $type ) ? 'Team Building' : '' );

			$title = ow_seo_title_join( array( $name, $qualifier, 'Overworld' ) );

			$desc = ow_seo_meta_first( $post->ID, array( 'event_tagline' ) );
			if ( '' === $desc ) {
				$desc = ow_seo_post_description( $post );
			}
			$desc = ow_seo_pad(
				$desc,
				sprintf(
					'%s at Overworld Singapore - see what is included and enquire.',
					$qualifier ? $qualifier . ' package' : 'Group event package'
				)
			);
			break;

		case 'promo':
			$title = ow_seo_title_join( array( $name, 'Overworld Promotions' ) );

			$desc = ow_seo_meta_first( $post->ID, array( 'promo_tagline', 'promo_description' ) );
			if ( '' === $desc ) {
				$desc = ow_seo_post_description( $post );
			}
			$desc = ow_seo_pad(
				$desc,
				'A current promotion at Overworld Singapore - check the terms and book at Kallang Wave Mall, Orchard Central or Funan.'
			);
			break;

		case 'post':
			$title = ow_seo_title_join( array( $name, $brand ) );
			$desc  = ow_seo_pad(
				ow_seo_post_description( $post ),
				sprintf(
					'%s - from the Overworld blog, Singapore\'s home of VR gaming and immersive physical play.',
					$name
				)
			);
			break;

		default:
			$title = ow_seo_title_join( array( $name, $brand ) );
			$desc  = ow_seo_pad(
				ow_seo_post_description( $post ),
				sprintf(
					'%s at Overworld Singapore - VR arcades, escape rooms and physical game arenas at Kallang Wave Mall, Orchard Central and Funan.',
					$name
				)
			);
			break;
	}

	return array(
		'title' => $title,
		'desc'  => ow_seo_trim( $desc ),
	);
}

/**
 * True when the event-hub plugin already owns this page's metadata.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function ow_seo_handled_elsewhere( $post_id ) {
	return function_exists( 'ow_is_hub_page' ) && ow_is_hub_page( $post_id );
}

/**
 * Resolve the metadata for whatever is currently being viewed.
 *
 * Precedence: per-post SEO field -> hand-written map entry -> generated
 * fallback. Returns null when this plugin should stay silent.
 *
 * @return array{title: string, desc: string, url: string, image: string, noindex: bool, type: string, post: WP_Post|null}|null
 */
function ow_seo_current() {
	// Resolved once per request: this runs from both document_title_parts and
	// wp_head, and the lookups behind it are not free.
	static $cache = null;
	static $done  = false;

	if ( $done ) {
		return $cache;
	}
	$done = true;

	// Never touch admin, feeds, or non-200 responses.
	if ( is_admin() || is_feed() || is_embed() || is_404() ) {
		return $cache;
	}

	$post    = null;
	$title   = '';
	$desc    = '';
	$url     = '';
	$image   = '';
	$noindex = false;
	$type    = 'website';

	if ( is_singular( ow_seo_post_types() ) ) {
		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return $cache;
		}
		if ( ow_seo_handled_elsewhere( $post->ID ) ) {
			return $cache;
		}

		$url  = get_permalink( $post );
		$type = ( 'post' === $post->post_type ) ? 'article' : 'website';

		$map   = ow_seo_page_map();
		$entry = isset( $map[ $post->ID ] ) ? $map[ $post->ID ] : null;
		$gen   = ow_seo_generated( $post );

		$title   = $entry ? $entry['title'] : $gen['title'];
		$desc    = $entry ? $entry['desc'] : $gen['desc'];
		$noindex = $entry && ! empty( $entry['noindex'] );

		// Per-post overrides win over everything.
		$f_title = trim( (string) get_post_meta( $post->ID, 'ow_seo_title', true ) );
		$f_desc  = trim( (string) get_post_meta( $post->ID, 'ow_seo_desc', true ) );
		$f_img   = get_post_meta( $post->ID, 'ow_seo_image', true );
		$f_noidx = get_post_meta( $post->ID, 'ow_seo_noindex', true );

		if ( '' !== $f_title ) {
			$title = $f_title;
		}
		if ( '' !== $f_desc ) {
			$desc = $f_desc;
		}
		if ( '' !== $f_noidx && null !== $f_noidx ) {
			$noindex = (bool) $f_noidx;
		}

		if ( $f_img && is_numeric( $f_img ) ) {
			$image = (string) wp_get_attachment_image_url( (int) $f_img, 'full' );
		}
		if ( '' === $image ) {
			$image = (string) get_the_post_thumbnail_url( $post->ID, 'full' );
		}
	} elseif ( is_front_page() ) {
		$map     = ow_seo_page_map();
		$home_id = (int) get_option( 'page_on_front' );
		$entry   = isset( $map[ $home_id ] ) ? $map[ $home_id ] : ( isset( $map[16] ) ? $map[16] : null );
		$title   = $entry ? $entry['title'] : get_bloginfo( 'name' );
		$desc    = $entry ? $entry['desc'] : get_bloginfo( 'description' );
		$url     = home_url( '/' );
	} elseif ( is_home() ) {
		$map      = ow_seo_page_map();
		$blog_id  = (int) get_option( 'page_for_posts' );
		$entry    = isset( $map[ $blog_id ] ) ? $map[ $blog_id ] : ( isset( $map[1350] ) ? $map[1350] : null );
		$title    = $entry ? $entry['title'] : 'Blog | ' . get_bloginfo( 'name' );
		$desc     = $entry ? $entry['desc'] : '';
		$url      = $blog_id ? get_permalink( $blog_id ) : home_url( '/' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( ! $term instanceof WP_Term ) {
			return $cache;
		}
		$title = sprintf( '%s | Overworld Singapore', $term->name );
		$desc  = $term->description
			? ow_seo_trim( $term->description )
			: sprintf(
				'%s experiences at Overworld Singapore. Browse the line-up and book at Kallang Wave Mall, Orchard Central or Funan.',
				$term->name
			);
		$url = get_term_link( $term );
		if ( is_wp_error( $url ) ) {
			$url = '';
		}
	} elseif ( is_search() ) {
		$title   = sprintf( 'Search results for "%s" | Overworld', get_search_query() );
		$desc    = 'Search Overworld Singapore for VR games, experiences and outlets.';
		$noindex = true;
	} elseif ( is_post_type_archive( ow_seo_post_types() ) ) {
		$obj   = get_queried_object();
		$label = ( $obj && isset( $obj->labels->name ) ) ? $obj->labels->name : 'Archive';
		$title = sprintf( '%s | Overworld Singapore', $label );
		$desc  = sprintf( '%s at Overworld Singapore - VR arcades, escape rooms and physical game arenas across three outlets.', $label );
	} elseif ( is_author() || is_date() || is_attachment() ) {
		// Thin duplicates of content that already has a canonical home. Keep
		// them out of the index. The title is built here rather than via
		// wp_get_document_title(), which would re-enter the
		// document_title_parts filter that calls this function.
		$noindex = true;
		$obj     = get_queried_object();
		if ( is_author() && $obj instanceof WP_User ) {
			$title = sprintf( 'Posts by %s | %s', $obj->display_name, get_bloginfo( 'name' ) );
		} elseif ( is_attachment() && $obj instanceof WP_Post ) {
			$title = sprintf( '%s | %s', $obj->post_title, get_bloginfo( 'name' ) );
		} else {
			$title = sprintf( 'Archive | %s', get_bloginfo( 'name' ) );
		}
	} else {
		return $cache;
	}

	// Paged results are the same content further down - do not compete with page 1.
	$paged = max( (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	if ( $paged > 1 ) {
		$title = sprintf( '%s - Page %d', $title, $paged );
	}

	if ( '' === $image ) {
		$icon = get_site_icon_url( 512 );
		if ( $icon ) {
			$image = $icon;
		}
	}

	$cache = array(
		'title'   => $title,
		'desc'    => $desc,
		'url'     => $url,
		'image'   => $image,
		'noindex' => $noindex,
		'type'    => $type,
		'post'    => $post,
	);

	return $cache;
}

/**
 * The Hello Elementor parent theme emits its own <meta name="description">
 * from post_excerpt on every singular view (see
 * hello_elementor_add_description_meta_tag). Left on, every post with an
 * excerpt shipped two description tags and Google picks one arbitrarily.
 * This plugin is the single source of truth, so switch the theme's off.
 */
add_filter( 'hello_elementor_description_meta_tag', '__return_false' );

// ===== Title tag =====

add_filter(
	'document_title_parts',
	function ( $parts ) {
		$seo = ow_seo_current();
		if ( ! $seo || '' === $seo['title'] ) {
			return $parts;
		}

		// Returning a single part replaces the whole tag, so the title reads
		// exactly as written instead of gaining a second " – Overworld".
		return array( 'title' => $seo['title'] );
	}
);

// ===== Meta description, robots, social cards, structured data =====

add_action(
	'wp_head',
	function () {
		$seo = ow_seo_current();

		if ( ! $seo ) {
			// The event-hub plugin owns the title, description and social tags
			// on /team-building/ and /birthday-party/, but emits no robots
			// directive. Supply just that, so every indexable URL on the site
			// asks for large image previews and untruncated snippets.
			if ( is_singular() && ow_seo_handled_elsewhere( get_queried_object_id() ) ) {
				echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />' . "\n";
			}
			return;
		}

		echo "\n<!-- Overworld SEO " . esc_html( OW_SEO_VERSION ) . " -->\n";

		if ( $seo['noindex'] ) {
			echo '<meta name="robots" content="noindex, follow" />' . "\n";
		} else {
			echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />' . "\n";
		}

		if ( '' !== $seo['desc'] ) {
			printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $seo['desc'] ) );
		}

		// WordPress core only emits rel=canonical on singular views, so the
		// blog index and the taxonomy archives had none. Page 2 onwards points
		// at itself rather than at page 1 — each page lists different posts.
		if ( ! is_singular() && '' !== $seo['url'] ) {
			$canonical = $seo['url'];
			$paged     = max( (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
			if ( $paged > 1 ) {
				$canonical = trailingslashit( $canonical ) . 'page/' . $paged . '/';
			}
			printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $canonical ) );
		}

		printf( '<meta property="og:type" content="%s" />' . "\n", esc_attr( $seo['type'] ) );
		printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $seo['title'] ) );
		if ( '' !== $seo['desc'] ) {
			printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $seo['desc'] ) );
		}
		if ( '' !== $seo['url'] ) {
			printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $seo['url'] ) );
		}
		printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:locale" content="en_SG" />' . "\n" );
		if ( '' !== $seo['image'] ) {
			printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $seo['image'] ) );
		}

		printf( '<meta name="twitter:card" content="%s" />' . "\n", $seo['image'] ? 'summary_large_image' : 'summary' );
		printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $seo['title'] ) );
		if ( '' !== $seo['desc'] ) {
			printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $seo['desc'] ) );
		}
		if ( '' !== $seo['image'] ) {
			printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_url( $seo['image'] ) );
		}

		$graph = ow_seo_schema_graph( $seo );
		if ( $graph ) {
			printf(
				'<script type="application/ld+json">%s</script>' . "\n",
				wp_json_encode(
					array(
						'@context' => 'https://schema.org',
						'@graph'   => $graph,
					),
					JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
				)
			);
		}
	},
	3
);

/**
 * Build the schema.org graph for the current view.
 *
 * Organization and WebSite are emitted everywhere so the entity is consistent
 * across the site; the three outlets are attached on the homepage, the outlet
 * hub and each outlet's own page.
 *
 * @param array $seo Resolved metadata.
 * @return array
 */
function ow_seo_schema_graph( array $seo ) {
	$home  = home_url( '/' );
	$org   = $home . '#organization';
	$brand = get_bloginfo( 'name' );
	$graph = array();

	$organization = array(
		'@type' => 'Organization',
		'@id'   => $org,
		'name'  => $brand,
		'url'   => $home,
		'description' => "Singapore's home of VR arcades, VR escape rooms and immersive physical game arenas, with outlets at Kallang Wave Mall, Orchard Central and Funan.",
	);

	$icon = get_site_icon_url( 512 );
	if ( $icon ) {
		$organization['logo'] = $icon;
	}

	$contacts = array();
	foreach ( ow_seo_outlets() as $outlet ) {
		$contacts[] = array(
			'@type'       => 'ContactPoint',
			'contactType' => 'customer service',
			'name'        => $outlet['name'],
			'telephone'   => $outlet['phone'],
			'email'       => $outlet['email'],
			'areaServed'  => 'SG',
			'availableLanguage' => 'English',
		);
	}
	$organization['contactPoint'] = $contacts;

	$graph[] = $organization;

	$graph[] = array(
		'@type'     => 'WebSite',
		'@id'       => $home . '#website',
		'url'       => $home,
		'name'      => $brand,
		'publisher' => array( '@id' => $org ),
		'inLanguage' => 'en-SG',
	);

	// LocalBusiness entries where they are relevant.
	$post_id     = $seo['post'] instanceof WP_Post ? $seo['post']->ID : 0;
	$outlet_page = array( 504 => 'kallang', 505 => 'orchard', 506 => 'funan' );

	$emit = array();
	if ( is_front_page() || 503 === $post_id ) {
		$emit = array_keys( ow_seo_outlets() );
	} elseif ( isset( $outlet_page[ $post_id ] ) ) {
		$emit = array( $outlet_page[ $post_id ] );
	}

	foreach ( $emit as $key ) {
		$outlet  = ow_seo_outlets()[ $key ];
		$graph[] = array(
			'@type'   => 'EntertainmentBusiness',
			'@id'     => home_url( $outlet['url'] ) . '#business',
			'name'    => $outlet['name'],
			'url'     => home_url( $outlet['url'] ),
			'parentOrganization' => array( '@id' => $org ),
			'telephone' => $outlet['phone'],
			'email'     => $outlet['email'],
			'address'   => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $outlet['street'],
				'addressLocality' => 'Singapore',
				'postalCode'      => $outlet['postcode'],
				'addressCountry'  => 'SG',
			),
			'openingHoursSpecification' => array(
				array(
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
					'opens'     => '11:00',
					'closes'    => '22:00',
				),
			),
		);
	}

	// Article for blog posts.
	if ( $seo['post'] instanceof WP_Post && 'post' === $seo['post']->post_type ) {
		$p       = $seo['post'];
		$article = array(
			'@type'            => 'Article',
			'headline'         => get_the_title( $p ),
			'description'      => $seo['desc'],
			'datePublished'    => get_the_date( 'c', $p ),
			'dateModified'     => get_the_modified_date( 'c', $p ),
			'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => $seo['url'] ),
			'author'           => array( '@id' => $org ),
			'publisher'        => array( '@id' => $org ),
			'inLanguage'       => 'en-SG',
		);
		if ( '' !== $seo['image'] ) {
			$article['image'] = $seo['image'];
		}
		$graph[] = $article;
	}

	// Breadcrumbs mirroring the real URL path.
	$crumbs = ow_seo_breadcrumbs( $seo );
	if ( count( $crumbs ) > 1 ) {
		$items = array();
		foreach ( $crumbs as $i => $crumb ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => $crumb['name'],
				'item'     => $crumb['url'],
			);
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);
	}

	return apply_filters( 'ow_seo_schema_graph', $graph, $seo );
}

/**
 * Breadcrumb trail built from real ancestors, so /team-building/funan/ reads
 * Home > Team Building > Funan rather than Home > Funan.
 *
 * @param array $seo Resolved metadata.
 * @return array<int, array{name: string, url: string}>
 */
function ow_seo_breadcrumbs( array $seo ) {
	$crumbs = array(
		array(
			'name' => 'Home',
			'url'  => home_url( '/' ),
		),
	);

	$post = $seo['post'];
	if ( ! $post instanceof WP_Post ) {
		if ( '' !== $seo['url'] && home_url( '/' ) !== $seo['url'] ) {
			$crumbs[] = array(
				'name' => $seo['title'],
				'url'  => $seo['url'],
			);
		}
		return $crumbs;
	}

	if ( 'post' === $post->post_type ) {
		$blog = (int) get_option( 'page_for_posts' );
		if ( $blog ) {
			$crumbs[] = array(
				'name' => get_the_title( $blog ),
				'url'  => get_permalink( $blog ),
			);
		}
	} else {
		foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor_id ) {
			$crumbs[] = array(
				'name' => get_the_title( $ancestor_id ),
				'url'  => get_permalink( $ancestor_id ),
			);
		}
	}

	$crumbs[] = array(
		'name' => get_the_title( $post ),
		'url'  => get_permalink( $post ),
	);

	return $crumbs;
}

// ===== Editable SEO box =====

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$locations = array();
		foreach ( ow_seo_post_types() as $post_type ) {
			$locations[] = array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $post_type,
				),
			);
		}

		acf_add_local_field_group(
			array(
				'key'         => 'group_ow_seo',
				'title'       => 'SEO',
				'fields'      => array(
					array(
						'key'          => 'field_ow_seo_title',
						'label'        => 'Search Title',
						'name'         => 'ow_seo_title',
						'type'         => 'text',
						'instructions' => 'The blue clickable line in Google. Aim for under 60 characters or it gets cut off. Leave empty to use the built-in title for this page.',
						'maxlength'    => 90,
					),
					array(
						'key'          => 'field_ow_seo_desc',
						'label'        => 'Search Description',
						'name'         => 'ow_seo_desc',
						'type'         => 'textarea',
						'rows'         => 3,
						'instructions' => 'The grey summary under the title in Google. Aim for 140-158 characters. Leave empty to use the built-in description.',
						'maxlength'    => 200,
					),
					array(
						'key'          => 'field_ow_seo_image',
						'label'        => 'Share Image',
						'name'         => 'ow_seo_image',
						'type'         => 'image',
						'return_format' => 'id',
						'preview_size' => 'medium',
						'instructions' => 'Shown when the page is shared on WhatsApp, Facebook or LinkedIn. Ideally 1200x630px. Falls back to the featured image.',
					),
					array(
						'key'          => 'field_ow_seo_noindex',
						'label'        => 'Hide From Search Engines',
						'name'         => 'ow_seo_noindex',
						'type'         => 'true_false',
						'ui'           => 1,
						'instructions' => 'Turn on to keep this page out of Google. Use for pages that are not ready or are duplicates.',
					),
				),
				'location'    => $locations,
				'position'    => 'normal',
				'style'       => 'default',
				'menu_order'  => 50,
				'active'      => true,
				'description' => 'Every field is optional. Leave one empty and the built-in text for this page is used instead.',
			)
		);
	}
);

/**
 * Elementor caches rendered output per page; clear it when the SEO fields are
 * saved so the change is visible immediately.
 */
add_action(
	'acf/save_post',
	function ( $post_id ) {
		if ( is_numeric( $post_id ) ) {
			delete_post_meta( (int) $post_id, '_elementor_element_cache' );
		}
	},
	20
);

// ===== Crawl hygiene =====

/**
 * The header and footer builder templates (elementor-hf) are layout fragments,
 * not pages. They were listed in wp-sitemap.xml, which invites Google to index
 * a bare header as if it were content.
 */
add_filter(
	'wp_sitemaps_post_types',
	function ( $post_types ) {
		unset( $post_types['elementor-hf'] );
		return $post_types;
	}
);

/**
 * Same fragments, belt and braces: if one is requested directly, tell robots
 * not to keep it.
 */
add_action(
	'wp_head',
	function () {
		if ( is_singular( array( 'elementor-hf', 'elementor_library', 'e-floating-buttons' ) ) ) {
			echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
		}
	},
	2
);

/**
 * Author archives list one author's posts under a second URL and add nothing.
 * Drop them from the sitemap to match the noindex above.
 */
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return ( 'users' === $name ) ? false : $provider;
}, 10, 2 );

<?php
/**
 * Template Name: Google Ads Landing (Team Building + Birthday)
 *
 * A single paid-traffic landing page covering BOTH event types across ALL
 * THREE outlets, built for Google Ads rather than for the client to edit.
 *
 *   /group-events-singapore/
 *
 * Deliberately NOT client-editable: no ACF, no Elementor, no mu-plugin copy
 * fields. Everything a visitor sees is in this file, so the page an ad points
 * at cannot drift while a campaign is running.
 *
 * CONVERSION: the Bookeo widget is embedded on the page itself, so a visitor
 * arriving from an ad can book without a second click. Bookeo's widget.js
 * refuses to run twice on one document (it alerts and bails), so exactly ONE
 * widget is rendered — the outlet switcher is a set of plain links that
 * reload this page with ?outlet=<slug>, carrying every other query parameter
 * with them so gclid / utm_* survive the switch.
 *
 * QUERY PARAMETERS (all optional, all safe to omit):
 *   ?outlet=kallang-wave-mall|orchard-central|funan   which calendar to embed
 *   ?e=tb|bp                                          swaps the hero to one
 *                                                     event type, so a Team
 *                                                     Building ad and a
 *                                                     Birthday ad can share
 *                                                     this URL and still read
 *                                                     as a match for the
 *                                                     search term
 *
 * Suggested final URLs in Google Ads:
 *   Team building ad group → /group-events-singapore/?e=tb
 *   Birthday ad group      → /group-events-singapore/?e=bp
 *   Outlet-specific ads    → …&outlet=orchard-central
 *
 * SEO: mu-plugin overworld-ads-landing.php gives this page its title,
 * description, keywords and a noindex directive — a paid landing page that
 * duplicates /team-building/ and /birthday-party/ should not compete with
 * them in organic results.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// ===== Outlets =====
// Accents, addresses, phones and Bookeo widget keys are the same values the
// outlet pages (page-pricing.php) and the Book Now pages already use, so a
// visitor who clicks through sees the outlet they just read about.
$ads_outlets = array(
	'kallang-wave-mall' => array(
		'slug'       => 'kallang-wave-mall',
		'brand'      => 'Overworld VR',
		'name'       => 'Kallang Wave Mall',
		'short'      => 'Kallang',
		'address'    => '1 Stadium Place, #01-63/64, Singapore 397628',
		'mrt'        => 'Stadium MRT (CC6)',
		'phone'      => '+65 6513 0561',
		'phone_raw'  => '+6565130561',
		'whatsapp'   => 'https://wa.me/6596101682',
		'book_page'  => '/book-now-kwm/',
		'bookeo_key' => '231YALUW419DA4CE7973',
		'accent'     => '#2f6bff',
		'glow'       => '#6f9bff',
		'pitch'      => 'The VR-heavy flagship. Headsets, escape rooms, the motion ride and the lava floor under one roof.',
		'best_for'   => 'Best for VR-first team building and birthdays that want the full arcade.',
		'games'      => array( 'vr-arcade', 'vr-escape', 'vr-machine-ride', 'floor-is-lava' ),
	),
	'orchard-central'   => array(
		'slug'       => 'orchard-central',
		'brand'      => 'Overworld Lava',
		'name'       => 'Orchard Central',
		'short'      => 'Orchard',
		'address'    => '181 Orchard Road, #05-30/K1/K3, Singapore 238896',
		'mrt'        => 'Somerset MRT (NS23)',
		'phone'      => '+65 8801 4303',
		'phone_raw'  => '+6588014303',
		'whatsapp'   => 'https://wa.me/message/WJ7MGRFFVGHAF1',
		'book_page'  => '/book-now-orchard/',
		'bookeo_key' => '231T6UX7U19D0A676CD2',
		'accent'     => '#ff5722',
		'glow'       => '#ff8a3d',
		'pitch'      => 'All-physical, all-energy play in the heart of town — no headsets, just sweat and shouting.',
		'best_for'   => 'Best for high-energy, head-to-head groups and younger birthday crowds.',
		'games'      => array( 'floor-is-lava', 'laser-maze', 'tap-tap' ),
	),
	'funan'             => array(
		'slug'       => 'funan',
		'brand'      => 'Overworld Funan',
		'name'       => 'Funan',
		'short'      => 'Funan',
		'address'    => '107 North Bridge Road, #04-14 & K1, Singapore 179105',
		'mrt'        => 'City Hall MRT (EW13 / NS25)',
		'phone'      => '+65 8914 0061',
		'phone_raw'  => '+6589140061',
		'whatsapp'   => 'https://wa.me/6589140061',
		'book_page'  => '/book-now-funan/',
		'bookeo_key' => '231RYKULN19D91C736C8',
		'accent'     => '#a855f7',
		'glow'       => '#c89aff',
		'pitch'      => "City Hall's mixed-reality playground — free-roam VR, big-screen party games and lava.",
		'best_for'   => 'Best for CBD offices and mixed groups who want both VR and physical.',
		'games'      => array( 'vr-free-roam', 'xr-party-game', 'floor-is-lava' ),
	),
);

// ===== Games =====
// The eight formats across the three outlets. 'stat' repeats the count each
// activity page already states, so nothing here is a new claim.
$ads_games = array(
	'vr-arcade'       => array(
		'name'    => 'VR Arcade',
		'icon'    => '👾',
		'url'     => '/vr-arcade/',
		'stat'    => '30+ titles',
		'desc'    => 'Pay for time, play everything — shooters, rhythm, horror, sports and party titles on up to 17 stations.',
		'outlets' => array( 'kallang-wave-mall' ),
	),
	'vr-escape'       => array(
		'name'    => 'VR Escape Room',
		'icon'    => '🔑',
		'url'     => '/vr-escape/',
		'stat'    => '23 rooms',
		'desc'    => 'Escape rooms without walls. Horror, adventure, mystery and fantasy — nobody escapes without talking to each other.',
		'outlets' => array( 'kallang-wave-mall' ),
	),
	'vr-machine-ride' => array(
		'name'    => 'VR Machine Ride',
		'icon'    => '🚀',
		'url'     => '/vr-machine-ride/',
		'stat'    => "Singapore's first",
		'desc'    => 'A full-motion seat synced frame-by-frame with the headset — coasters, starship runs and free-falls.',
		'outlets' => array( 'kallang-wave-mall' ),
	),
	'vr-free-roam'    => array(
		'name'    => 'VR Free Roam',
		'icon'    => '🥽',
		'url'     => '/vr-free-roam/',
		'stat'    => '20+ games',
		'desc'    => 'Untethered VR in full physical space — walk, run, duck and fight through shared arenas with your whole crew.',
		'outlets' => array( 'funan' ),
	),
	'xr-party-game'   => array(
		'name'    => 'XR Party Game',
		'icon'    => '🎉',
		'url'     => '/xr-party-game/',
		'stat'    => '6 modes',
		'desc'    => 'Mixed reality on the big screen. Your real-world moves control the game — no headset experience needed.',
		'outlets' => array( 'funan' ),
	),
	'floor-is-lava'   => array(
		'name'    => 'Floor Is Lava',
		'icon'    => '🌋',
		'url'     => '/floor-is-lava/',
		'stat'    => 'All 3 outlets',
		'desc'    => "Don't touch the lava. An interactive LED floor that tests reflexes, teamwork and balance.",
		'outlets' => array( 'kallang-wave-mall', 'orchard-central', 'funan' ),
	),
	'laser-maze'      => array(
		'name'    => 'Laser Maze',
		'icon'    => '🔦',
		'url'     => '/laser-maze/',
		'stat'    => '30+ modes',
		'desc'    => 'Slide, crawl and weave through a dark arena lit only by lasers. Fewest touches wins.',
		'outlets' => array( 'orchard-central' ),
	),
	'tap-tap'         => array(
		'name'    => 'Tap Tap',
		'icon'    => '⚡',
		'url'     => '/tap-tap/',
		'stat'    => 'Reflex wall',
		'desc'    => 'Lights flash, patterns shift, the clock runs down. Tap the right ones faster than your friends.',
		'outlets' => array( 'orchard-central' ),
	),
);

// ===== Which outlet's calendar is embedded =====
$ads_outlet_slug = isset( $_GET['outlet'] ) ? sanitize_key( wp_unslash( $_GET['outlet'] ) ) : '';
if ( ! isset( $ads_outlets[ $ads_outlet_slug ] ) ) {
	$ads_outlet_slug = 'kallang-wave-mall';
}
$ads_outlet = $ads_outlets[ $ads_outlet_slug ];

// ===== Which event type the hero leads with =====
$ads_focus = isset( $_GET['e'] ) ? sanitize_key( wp_unslash( $_GET['e'] ) ) : '';
if ( ! in_array( $ads_focus, array( 'tb', 'bp' ), true ) ) {
	$ads_focus = '';
}

$ads_hero = array(
	''   => array(
		'eyebrow' => 'Team Building &amp; Birthday Parties',
		'h1'      => 'Team Building &amp; Birthday Parties in Singapore',
		'tagline' => 'Three outlets. Eight games. One booking.',
	),
	'tb' => array(
		'eyebrow' => 'Corporate Team Building',
		'h1'      => 'Team Building Activities in Singapore',
		'tagline' => 'Stronger squads. Sharper teams.',
	),
	'bp' => array(
		'eyebrow' => 'Birthday Parties',
		'h1'      => 'Birthday Party Venues in Singapore',
		'tagline' => "A birthday they'll actually remember.",
	),
);
$ads_hero = $ads_hero[ $ads_focus ];

/**
 * This page's URL with one query parameter changed and everything else kept.
 *
 * The outlet switcher reloads the page, and an ad click carries gclid plus
 * whatever utm_* the campaign is tagged with. Dropping those on the switch
 * would break attribution for exactly the visitors who are furthest down the
 * funnel, so every existing parameter is carried across.
 *
 * @param string $key      Parameter to set.
 * @param string $value    Value to set it to.
 * @param string $fragment Optional #fragment to land on.
 * @return string
 */
if ( ! function_exists( 'ow_ads_url' ) ) :
function ow_ads_url( $key, $value, $fragment = '' ) {
	$base  = get_permalink( get_queried_object_id() );
	$query = array();

	foreach ( (array) $_GET as $k => $v ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( is_array( $v ) ) {
			continue;
		}
		$k = sanitize_key( $k );
		if ( '' === $k ) {
			continue;
		}
		$query[ $k ] = sanitize_text_field( wp_unslash( $v ) );
	}

	$query[ $key ] = $value;

	$url = add_query_arg( $query, $base );

	return $fragment ? $url . '#' . $fragment : $url;
}
endif;

// "From" price per outlet, read from the live pricing rows rather than typed
// in here, so a price change on the pricing pages reaches the ad landing page
// too. Silently omitted when an outlet has no published pricing.
$ads_from_price = array();
if ( function_exists( 'ow_seo_outlet_price_range' ) ) {
	foreach ( $ads_outlets as $slug => $o ) {
		$range = ow_seo_outlet_price_range( $slug );
		if ( '' === $range ) {
			continue;
		}
		$parts = explode( '-', $range );
		if ( '' !== $parts[0] ) {
			$ads_from_price[ $slug ] = $parts[0];
		}
	}
}

// ===== Packages =====
// Read live from the event_package CPT — the same posts the
// /team-building/[outlet]/ and /birthday-party/[outlet]/ pages list, and the
// same query they use. The packages are written and priced in WP Admin, so
// this page must read them rather than restate them: a price edited there has
// to reach the page an ad is pointing at, on the next request, without a
// deploy.
$ads_types = array(
	'team-building'  => array(
		'label' => 'Team Building',
		'hub'   => '/team-building/',
		'note'  => 'Priced per person. Every package is hosted end to end by our Game Masters.',
	),
	'birthday-party' => array(
		'label' => 'Birthday Party',
		'hub'   => '/birthday-party/',
		'note'  => 'Priced per package, not per head. Private room, cake cutting and dining time included.',
	),
);

// Lead with whichever type the ad is about.
if ( 'bp' === $ads_focus ) {
	$ads_types = array_reverse( $ads_types, true );
}

$ads_packages = array();

foreach ( array_keys( $ads_types ) as $ads_type_slug ) {
	foreach ( array_keys( $ads_outlets ) as $ads_pkg_outlet ) {
		$posts = get_posts(
			array(
				'post_type'      => 'event_package',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'meta_query'     => array(
					'relation' => 'AND',
					array( 'key' => 'event_type', 'value' => $ads_type_slug ),
					array( 'key' => 'event_outlet', 'value' => $ads_pkg_outlet ),
					array(
						'relation' => 'OR',
						array( 'key' => 'event_active', 'value' => '1' ),
						array( 'key' => 'event_active', 'compare' => 'NOT EXISTS' ),
					),
				),
				'meta_key'       => 'event_display_order',
				'orderby'        => 'meta_value_num',
				'order'          => 'ASC',
			)
		);

		foreach ( $posts as $pkg ) {
			$pdf     = get_post_meta( $pkg->ID, 'event_pdf', true );
			$pdf_url = '';
			if ( $pdf ) {
				$pdf_url = is_numeric( $pdf ) ? (string) wp_get_attachment_url( (int) $pdf ) : (string) $pdf;
			}

			// "Kallang Wave Mall - Package A" sits next to a chip already
			// naming the outlet, so the prefix is dropped for display only.
			$title = $pkg->post_title;
			$strip = $ads_outlets[ $ads_pkg_outlet ]['name'] . ' - ';
			if ( 0 === stripos( $title, $strip ) ) {
				$title = substr( $title, strlen( $strip ) );
			}

			$ads_packages[ $ads_type_slug ][] = array(
				'title'      => $title,
				'outlet'     => $ads_pkg_outlet,
				'tagline'    => (string) get_post_meta( $pkg->ID, 'event_tagline', true ),
				'price'      => (string) get_post_meta( $pkg->ID, 'event_price_from', true ),
				'duration'   => (string) get_post_meta( $pkg->ID, 'event_duration', true ),
				'group_size' => (string) get_post_meta( $pkg->ID, 'event_group_size', true ),
				'pdf'        => $pdf_url,
				'permalink'  => (string) get_permalink( $pkg->ID ),
			);
		}
	}
}

/**
 * The lowest price across a set of packages, shown the way it was written.
 *
 * event_price_from is a free text field — "$33 - $39/pax", "$384 - $887.30" —
 * because team building is priced per head and birthdays are priced per
 * package. Rather than normalise the two into a number this page would then
 * have to label, the leading amount is read off each package, the smallest
 * wins, and the unit is carried across from that same package's own string.
 *
 * Returns '' when nothing parses, and the caller then shows no price at all
 * rather than a wrong one.
 *
 * @param array $packages Packages for one event type.
 * @return string e.g. "$33/pax", or ''.
 */
function ow_ads_from_price( array $packages ) {
	$low  = null;
	$unit = '';

	foreach ( $packages as $pkg ) {
		if ( ! preg_match( '/\$\s*([0-9]+(?:\.[0-9]+)?)/', $pkg['price'], $m ) ) {
			continue;
		}
		$value = (float) $m[1];
		if ( null !== $low && $value >= $low ) {
			continue;
		}
		$low  = $value;
		$unit = ( false !== stripos( $pkg['price'], 'pax' ) ) ? '/pax' : '';
	}

	if ( null === $low ) {
		return '';
	}

	return '$' . rtrim( rtrim( number_format( $low, 2, '.', '' ), '0' ), '.' ) . $unit;
}

// ===== FAQ =====
// Written for paid traffic: the questions someone asks in the ninety seconds
// between clicking an ad and deciding whether to book.
$ads_faqs = array(
	array(
		'q' => 'How much does team building or a birthday party cost?',
		'a' => 'Sessions are priced per person and vary by outlet, activity and whether you visit on a weekday or weekend. The live calendar on this page shows the exact price for every slot before you pay. For a full package — a private room, a fixed itinerary, food time between rounds — WhatsApp the outlet and we will quote for your headcount.',
	),
	array(
		'q' => 'Which outlet should we pick?',
		'a' => 'Kallang Wave Mall is the VR-heavy one: VR Arcade, VR Escape rooms, the VR Machine Ride and Floor Is Lava. Orchard Central is entirely physical: Floor Is Lava, Laser Maze and Tap Tap. Funan mixes both, with VR Free Roam, XR Party Game and Floor Is Lava. If you are unsure, message us with your group size and we will point you to the right one.',
	),
	array(
		'q' => 'How big can the group be?',
		'a' => 'Small groups can book straight through the calendar on this page. Larger groups — company departments, whole classes, big birthday crowds — are better handled by message, so we can stagger rotations and hold the right number of stations. WhatsApp the outlet with your headcount and date.',
	),
	array(
		'q' => 'Do we need any gaming or VR experience?',
		'a' => 'No. Every activity is pick-up-and-play and our Game Masters brief the group before each round. First-timers and regular gamers end up on level ground, which is exactly what makes it work as a team activity.',
	),
	array(
		'q' => 'Can we bring a cake, or eat between games?',
		'a' => 'Birthday groups usually split the session into rounds with a break in between. Tell us when you book and we will build the break into the run sheet for you.',
	),
	array(
		'q' => 'What are the opening hours?',
		'a' => 'All three outlets are open daily from 11am to 10pm. The booking calendar on this page only shows slots that are actually still available.',
	),
	array(
		'q' => 'How do we get there?',
		'a' => 'Kallang Wave Mall is at Stadium MRT, Orchard Central is at Somerset MRT, and Funan is at City Hall MRT. All three are inside malls, so the whole group can get there under cover.',
	),
	array(
		'q' => 'Can we book for a specific date and hold it?',
		'a' => 'Booking through the calendar on this page confirms the slot immediately. For a package or a large group, message the outlet — we will hold the slot while the details are confirmed.',
	),
);
?>

<style>
  .ow-ads{
    --lava:#ff5722;
    --lava-glow:#ff8a3d;
    --bg:#08080f;
    --bg-2:#0f0f1a;
    --bg-3:#13131f;
    --fg:#fff;
    --dim:rgba(220,225,240,.66);
    --faint:rgba(220,225,240,.42);
    --line:rgba(255,255,255,.09);
    --accent:<?php echo esc_attr( $ads_outlet['accent'] ); ?>;
    --glow:<?php echo esc_attr( $ads_outlet['glow'] ); ?>;
    background:var(--bg);
    color:var(--fg);
    font-family:'Space Grotesk','Inter',system-ui,sans-serif;
    overflow-x:hidden;
    padding-bottom:86px; /* clears the sticky CTA bar */
  }
  .ow-ads *{box-sizing:border-box;}
  .ow-ads h1,.ow-ads h2,.ow-ads h3,.ow-ads h4{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-weight:400;text-transform:uppercase;letter-spacing:-.005em;
  }
  .ow-ads__mono{
    font-family:'JetBrains Mono',monospace;
    text-transform:uppercase;letter-spacing:.2em;
  }
  .ow-ads__wrap{max-width:1240px;margin:0 auto;}

  /* ===== HERO ===== */
  .ow-ads__hero{
    position:relative;overflow:hidden;
    padding:96px 40px 72px;
    background:
      radial-gradient(ellipse 80% 60% at 50% -10%,rgba(255,87,34,.22),transparent 70%),
      radial-gradient(ellipse 60% 50% at 50% 110%,rgba(255,87,34,.16),transparent 70%),
      #08080f;
  }
  .ow-ads__hero-grid{
    position:absolute;inset:0;pointer-events:none;
    background-image:
      linear-gradient(rgba(255,87,34,.055) 1px,transparent 1px),
      linear-gradient(90deg,rgba(255,87,34,.055) 1px,transparent 1px);
    background-size:64px 64px;
    mask-image:radial-gradient(ellipse at center,#000 0%,transparent 72%);
    -webkit-mask-image:radial-gradient(ellipse at center,#000 0%,transparent 72%);
  }
  .ow-ads__hero-inner{
    position:relative;z-index:2;text-align:center;
    max-width:900px;margin:0 auto;
  }
  .ow-ads__eyebrow{
    display:inline-flex;align-items:center;gap:11px;
    font-family:'JetBrains Mono',monospace;
    font-size:11.5px;letter-spacing:.24em;text-transform:uppercase;
    color:var(--lava-glow);
    padding:9px 18px;border-radius:999px;
    border:1px solid rgba(255,87,34,.42);
    background:rgba(255,87,34,.09);
    margin-bottom:26px;
  }
  .ow-ads__eyebrow::before{
    content:"";width:7px;height:7px;border-radius:50%;
    background:var(--lava);box-shadow:0 0 12px var(--lava-glow);
  }
  .ow-ads__h1{
    font-size:clamp(40px,6.4vw,92px);line-height:.98;
    margin:0 0 16px;
    background:linear-gradient(180deg,#fff 0%,#fff 48%,var(--lava-glow) 100%);
    -webkit-background-clip:text;background-clip:text;
    -webkit-text-fill-color:transparent;
  }
  .ow-ads__tagline{
    font-size:clamp(17px,2vw,21px);line-height:1.35;
    margin:0 0 14px;color:#fff;
  }
  .ow-ads__lede{
    font-size:15.5px;line-height:1.65;color:var(--dim);
    max-width:640px;margin:0 auto 30px;
  }
  .ow-ads__hero-ctas{
    display:flex;gap:12px;justify-content:center;flex-wrap:wrap;
    margin-bottom:34px;
  }
  .ow-ads__btn{
    display:inline-flex;align-items:center;justify-content:center;gap:10px;
    padding:15px 28px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.15em;text-transform:uppercase;font-weight:700;
    text-decoration:none;border:1px solid transparent;
    transition:transform .22s ease,gap .22s ease,background .22s ease,border-color .22s ease;
  }
  .ow-ads__btn:hover{transform:translateY(-2px);gap:14px;}
  .ow-ads__btn--primary{
    background:var(--lava);color:#0a0a14;
    box-shadow:0 14px 34px -12px var(--lava);
  }
  .ow-ads__btn--primary:hover{background:var(--lava-glow);color:#0a0a14;}
  .ow-ads__btn--ghost{
    background:rgba(255,255,255,.05);color:#fff;border-color:var(--line);
  }
  .ow-ads__btn--ghost:hover{background:rgba(255,255,255,.1);border-color:var(--lava);color:#fff;}
  .ow-ads__btn--wa{
    background:rgba(37,211,102,.12);color:#5df08d;border-color:rgba(37,211,102,.4);
  }
  .ow-ads__btn--wa:hover{background:rgba(37,211,102,.2);color:#7df7a6;}

  .ow-ads__trust{
    display:flex;gap:10px;justify-content:center;flex-wrap:wrap;
  }
  .ow-ads__trust span{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--dim);
    padding:8px 14px;border-radius:999px;
    border:1px solid var(--line);background:rgba(255,255,255,.03);
  }
  .ow-ads__trust strong{color:var(--lava-glow);font-weight:700;}

  /* ===== SECTION FRAME ===== */
  .ow-ads__section{padding:76px 40px;border-top:1px solid var(--line);}
  .ow-ads__section--alt{background:var(--bg-2);}
  .ow-ads__head{margin-bottom:40px;max-width:760px;}
  .ow-ads__head--center{margin-left:auto;margin-right:auto;text-align:center;}
  .ow-ads__kicker{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.22em;text-transform:uppercase;
    color:var(--lava-glow);margin-bottom:12px;
  }
  .ow-ads__h2{
    font-size:clamp(28px,3.6vw,44px);line-height:1.02;
    margin:0 0 14px;color:#fff;
  }
  .ow-ads__sub{
    font-size:15.5px;line-height:1.65;color:var(--dim);margin:0;
  }

  /* ===== EVENT TYPE CARDS ===== */
  .ow-ads__types{display:grid;grid-template-columns:1fr 1fr;gap:22px;}
  .ow-ads__type{
    position:relative;overflow:hidden;
    display:flex;flex-direction:column;
    border:1px solid var(--line);border-radius:22px;
    background:var(--bg-3);
    padding:34px 32px 32px;
    transition:border-color .25s ease,transform .3s ease;
  }
  .ow-ads__type::before{
    content:"";position:absolute;inset:0 0 auto 0;height:3px;
    background:linear-gradient(90deg,transparent,var(--tint),transparent);
  }
  .ow-ads__type:hover{border-color:var(--tint);transform:translateY(-4px);}
  .ow-ads__type-icon{font-size:30px;line-height:1;margin-bottom:16px;}
  .ow-ads__type-title{font-size:27px;line-height:1.05;margin:0 0 10px;color:#fff;}
  .ow-ads__type-text{font-size:14.5px;line-height:1.65;color:var(--dim);margin:0 0 20px;}
  .ow-ads__type-list{list-style:none;margin:0 0 auto;padding:0 0 24px;}
  .ow-ads__type-list li{
    position:relative;padding:0 0 0 26px;margin-bottom:10px;
    font-size:14px;line-height:1.55;color:var(--dim);
  }
  .ow-ads__type-list li::before{
    content:"";position:absolute;left:4px;top:8px;
    width:7px;height:7px;border-radius:50%;
    background:var(--tint);box-shadow:0 0 10px var(--tint);
  }
  .ow-ads__type-ctas{display:flex;gap:10px;flex-wrap:wrap;}

  /* ===== PACKAGES ===== */
  .ow-ads__pk-block{margin-bottom:52px;}
  .ow-ads__pk-block:last-child{margin-bottom:0;}
  .ow-ads__pk-head{
    display:flex;align-items:baseline;gap:16px;flex-wrap:wrap;
    padding-bottom:16px;margin-bottom:24px;
    border-bottom:1px solid var(--line);
  }
  .ow-ads__pk-title{font-size:27px;line-height:1;margin:0;color:#fff;}
  .ow-ads__pk-from{
    display:inline-flex;align-items:baseline;gap:7px;
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.16em;text-transform:uppercase;
    color:var(--faint);
  }
  .ow-ads__pk-from strong{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:25px;line-height:1;letter-spacing:0;font-weight:400;
    color:var(--lava-glow);
  }
  .ow-ads__pk-note{
    font-size:13.5px;line-height:1.55;color:var(--dim);
    margin:0 0 0 auto;max-width:420px;
  }
  .ow-ads__pk-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
  .ow-ads__pk{
    display:flex;flex-direction:column;position:relative;overflow:hidden;
    border:1px solid var(--line);border-radius:18px;
    background:var(--bg-3);
    padding:22px 22px 20px;
    transition:transform .28s ease,border-color .25s ease,box-shadow .3s ease;
  }
  .ow-ads__pk::before{
    content:"";position:absolute;inset:0 0 auto 0;height:2px;
    background:linear-gradient(90deg,transparent,var(--tint),transparent);
  }
  .ow-ads__pk:hover{
    transform:translateY(-5px);border-color:var(--tint);
    box-shadow:0 20px 50px -26px var(--tint);
  }
  .ow-ads__pk-outlet{
    display:inline-flex;align-items:center;gap:7px;align-self:flex-start;
    font-family:'JetBrains Mono',monospace;
    font-size:9px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--tint-fg);
    padding:4px 10px;border-radius:999px;
    border:1px solid var(--tint);background:rgba(255,255,255,.03);
    margin-bottom:13px;
  }
  .ow-ads__pk-name{font-size:19px;line-height:1.1;margin:0 0 11px;color:#fff;}
  .ow-ads__pk-meta{
    display:flex;flex-wrap:wrap;gap:6px;margin:0 0 13px;
  }
  .ow-ads__pk-meta span{
    font-family:'JetBrains Mono',monospace;
    font-size:9.5px;letter-spacing:.08em;
    color:var(--dim);
    padding:5px 10px;border-radius:8px;
    border:1px solid var(--line);background:rgba(255,255,255,.03);
  }
  .ow-ads__pk-text{
    font-size:13px;line-height:1.6;color:var(--dim);
    margin:0 0 auto;padding:0 0 16px;
    display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;
    overflow:hidden;
  }
  .ow-ads__pk-price{
    display:flex;align-items:baseline;gap:8px;flex-wrap:wrap;
    padding:13px 0;border-top:1px solid var(--line);
    font-family:'JetBrains Mono',monospace;
    font-size:9.5px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--faint);
  }
  .ow-ads__pk-price strong{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:21px;line-height:1;letter-spacing:0;font-weight:400;
    color:var(--tint-fg);
  }
  .ow-ads__pk-ctas{display:flex;gap:8px;}
  .ow-ads__pk-ctas .ow-ads__btn{flex:1;padding:11px 14px;font-size:10.5px;}
  .ow-ads__pk-ctas .ow-ads__btn--primary{
    background:var(--tint);color:#08080f;box-shadow:none;
  }
  .ow-ads__pk-ctas .ow-ads__btn--primary:hover{background:var(--tint-fg);}
  .ow-ads__pk-ctas .ow-ads__btn--ghost{flex:0 0 auto;}
  .ow-ads__pk-all{
    display:flex;justify-content:center;margin-top:28px;
  }

  /* ===== GAMES ===== */
  .ow-ads__games{
    display:grid;grid-template-columns:repeat(4,1fr);gap:16px;
  }
  .ow-ads__game{
    display:flex;flex-direction:column;
    border:1px solid var(--line);border-radius:18px;
    background:rgba(255,255,255,.025);
    padding:24px 22px 22px;
    text-decoration:none;color:inherit;
    transition:transform .28s ease,border-color .25s ease,background .25s ease;
  }
  .ow-ads__game:hover{
    transform:translateY(-5px);
    border-color:rgba(255,87,34,.5);
    background:rgba(255,87,34,.06);
  }
  .ow-ads__game-top{
    display:flex;align-items:flex-start;justify-content:space-between;gap:12px;
    margin-bottom:14px;
  }
  .ow-ads__game-icon{font-size:26px;line-height:1;}
  .ow-ads__game-stat{
    font-family:'JetBrains Mono',monospace;
    font-size:9.5px;letter-spacing:.12em;text-transform:uppercase;
    color:var(--lava-glow);
    padding:5px 9px;border-radius:999px;
    border:1px solid rgba(255,87,34,.3);background:rgba(255,87,34,.08);
    white-space:nowrap;
  }
  .ow-ads__game-name{font-size:19px;line-height:1.1;margin:0 0 9px;color:#fff;}
  .ow-ads__game-desc{
    font-size:13.2px;line-height:1.6;color:var(--dim);margin:0 0 16px;flex:1;
  }
  .ow-ads__game-where{
    display:flex;flex-wrap:wrap;gap:6px;
    padding-top:14px;border-top:1px solid var(--line);
  }
  .ow-ads__chip{
    font-family:'JetBrains Mono',monospace;
    font-size:9px;letter-spacing:.1em;text-transform:uppercase;
    padding:4px 9px;border-radius:999px;
    border:1px solid var(--chip);
    color:var(--chip-fg);
    background:rgba(255,255,255,.03);
  }

  /* ===== OUTLETS ===== */
  .ow-ads__outlets{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
  .ow-ads__outlet{
    position:relative;display:flex;flex-direction:column;
    border:1px solid var(--line);border-radius:22px;
    background:var(--bg-3);overflow:hidden;
    transition:transform .3s ease,border-color .25s ease,box-shadow .3s ease;
  }
  .ow-ads__outlet::before{
    content:"";position:absolute;inset:0 0 auto 0;height:3px;
    background:linear-gradient(90deg,transparent,var(--tint),transparent);
  }
  .ow-ads__outlet:hover{
    transform:translateY(-6px);border-color:var(--tint);
    box-shadow:0 24px 60px -28px var(--tint);
  }
  .ow-ads__outlet-body{padding:30px 26px 26px;display:flex;flex-direction:column;flex:1;}
  .ow-ads__outlet-brand{
    display:flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--tint-fg);margin-bottom:11px;
  }
  .ow-ads__outlet-brand::before{
    content:"";width:7px;height:7px;border-radius:50%;
    background:var(--tint);box-shadow:0 0 10px var(--tint);
  }
  .ow-ads__outlet-name{font-size:25px;line-height:1.05;margin:0 0 8px;color:#fff;}
  .ow-ads__outlet-addr{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;line-height:1.55;letter-spacing:.05em;
    color:var(--faint);margin:0 0 4px;
  }
  .ow-ads__outlet-mrt{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;
    color:var(--tint-fg);margin:0 0 16px;
  }
  .ow-ads__outlet-pitch{font-size:13.8px;line-height:1.6;color:var(--dim);margin:0 0 8px;}
  .ow-ads__outlet-best{font-size:13px;line-height:1.55;color:var(--faint);margin:0 0 18px;}
  .ow-ads__outlet-games{
    display:flex;flex-wrap:wrap;gap:7px;
    margin:0 0 auto;padding:0 0 18px;list-style:none;
  }
  .ow-ads__outlet-games li{
    font-family:'JetBrains Mono',monospace;
    font-size:9.5px;letter-spacing:.1em;text-transform:uppercase;
    color:#fff;padding:5px 11px;border-radius:999px;
    border:1px solid var(--line);background:rgba(255,255,255,.04);
  }
  .ow-ads__outlet-price{
    display:flex;align-items:baseline;gap:8px;
    padding:14px 0;
    border-top:1px solid var(--line);
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:var(--faint);
  }
  .ow-ads__outlet-price strong{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:25px;line-height:1;letter-spacing:0;
    color:var(--tint-fg);font-weight:400;
  }
  .ow-ads__outlet-ctas{display:flex;gap:8px;}
  .ow-ads__outlet-ctas .ow-ads__btn{flex:1;padding:13px 16px;font-size:11px;}
  .ow-ads__outlet-ctas .ow-ads__btn--primary{
    background:var(--tint);color:#08080f;box-shadow:none;
  }
  .ow-ads__outlet-ctas .ow-ads__btn--primary:hover{background:var(--tint-fg);}
  .ow-ads__outlet-ctas .ow-ads__btn--ghost{flex:0 0 auto;}

  /* ===== BOOK ===== */
  .ow-ads__book{
    padding:76px 40px 84px;
    border-top:1px solid var(--line);
    background:
      radial-gradient(ellipse 70% 55% at 50% 0%,color-mix(in srgb,var(--accent) 20%,transparent),transparent 70%),
      #000;
  }
  .ow-ads__switch{
    display:flex;gap:10px;justify-content:center;flex-wrap:wrap;
    margin:0 0 12px;
  }
  .ow-ads__switch a{
    display:inline-flex;align-items:center;gap:9px;
    padding:12px 20px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;font-weight:700;
    text-decoration:none;color:var(--dim);
    border:1px solid var(--line);background:rgba(255,255,255,.03);
    transition:border-color .22s ease,color .22s ease,background .22s ease;
  }
  .ow-ads__switch a::before{
    content:"";width:8px;height:8px;border-radius:50%;
    background:var(--pill);opacity:.55;
  }
  .ow-ads__switch a:hover{color:#fff;border-color:var(--pill);background:rgba(255,255,255,.06);}
  .ow-ads__switch a[aria-current="true"]{
    color:#fff;border-color:var(--pill);
    background:color-mix(in srgb,var(--pill) 16%,transparent);
  }
  .ow-ads__switch a[aria-current="true"]::before{opacity:1;box-shadow:0 0 10px var(--pill);}
  .ow-ads__switch-note{
    text-align:center;font-size:12.5px;color:var(--faint);margin:0 0 28px;
  }
  .ow-ads__widget-card{
    max-width:1000px;margin:0 auto;
    border:1px solid var(--line);border-radius:22px;
    background:rgba(255,255,255,.02);
    padding:22px;
  }
  .ow-ads__widget-head{
    display:flex;align-items:center;justify-content:space-between;
    gap:14px;flex-wrap:wrap;margin-bottom:18px;
  }
  .ow-ads__widget-tag{
    display:inline-flex;align-items:center;gap:9px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:#fff;
  }
  .ow-ads__widget-tag::before{
    content:"";width:8px;height:8px;border-radius:50%;
    background:#3ddc84;box-shadow:0 0 10px #3ddc84;
  }
  .ow-ads__widget-note{
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:var(--faint);
  }
  /* Bookeo paints its own document inside the frame, so the card stays
     transparent and the iframe is forced to full width. */
  .ow-ads__widget{background:transparent;border-radius:14px;overflow:hidden;}
  .ow-ads__widget iframe{display:block!important;width:100%!important;max-width:100%!important;border:0;}
  .ow-ads__book-help{
    max-width:1000px;margin:22px auto 0;
    display:flex;align-items:center;justify-content:center;gap:10px;flex-wrap:wrap;
    font-size:13.5px;color:var(--dim);text-align:center;
  }
  .ow-ads__book-help a{color:var(--glow);text-decoration:none;font-weight:600;}
  .ow-ads__book-help a:hover{text-decoration:underline;}

  /* ===== STEPS ===== */
  .ow-ads__steps{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;counter-reset:step;}
  .ow-ads__step{
    position:relative;border:1px solid var(--line);border-radius:18px;
    background:rgba(255,255,255,.025);padding:30px 26px;
  }
  .ow-ads__step::before{
    counter-increment:step;content:counter(step,decimal-leading-zero);
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:42px;line-height:1;color:rgba(255,87,34,.28);
    display:block;margin-bottom:14px;
  }
  .ow-ads__step h3{font-size:19px;line-height:1.1;margin:0 0 9px;color:#fff;}
  .ow-ads__step p{font-size:13.8px;line-height:1.62;color:var(--dim);margin:0;}

  /* ===== POINTS ===== */
  .ow-ads__points{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;}
  .ow-ads__point{
    border:1px solid var(--line);border-radius:16px;
    background:rgba(255,255,255,.025);padding:26px 24px;
  }
  .ow-ads__point-icon{font-size:24px;line-height:1;margin-bottom:13px;}
  .ow-ads__point h3{font-size:17px;line-height:1.12;margin:0 0 8px;color:#fff;}
  .ow-ads__point p{font-size:13.5px;line-height:1.62;color:var(--dim);margin:0;}

  /* ===== BODY COPY ===== */
  .ow-ads__copy{max-width:840px;margin:0 auto;}
  .ow-ads__copy p{font-size:15.5px;line-height:1.8;color:var(--dim);margin:0 0 18px;}
  .ow-ads__copy p:last-child{margin-bottom:0;}
  .ow-ads__copy strong{color:#fff;font-weight:600;}
  .ow-ads__copy a{color:var(--lava-glow);text-decoration:none;}
  .ow-ads__copy a:hover{text-decoration:underline;}

  /* ===== FAQ ===== */
  .ow-ads__faq{max-width:860px;margin:0 auto;}
  .ow-ads__faq-item{
    border:1px solid var(--line);border-radius:14px;
    background:rgba(255,255,255,.025);
    margin-bottom:11px;overflow:hidden;
  }
  .ow-ads__faq-item[open]{border-color:rgba(255,87,34,.42);}
  .ow-ads__faq-q{
    cursor:pointer;list-style:none;position:relative;
    padding:18px 54px 18px 22px;
    font-size:15.5px;font-weight:600;line-height:1.45;color:#fff;
  }
  .ow-ads__faq-q::-webkit-details-marker{display:none;}
  .ow-ads__faq-q::after{
    content:"+";position:absolute;right:22px;top:50%;transform:translateY(-50%);
    font-family:'JetBrains Mono',monospace;font-size:19px;color:var(--lava-glow);
  }
  .ow-ads__faq-item[open] .ow-ads__faq-q::after{content:"–";}
  .ow-ads__faq-q:hover{color:var(--lava-glow);}
  .ow-ads__faq-a{padding:0 22px 20px;font-size:14.5px;line-height:1.72;color:var(--dim);}

  /* ===== FINAL CTA ===== */
  .ow-ads__final{
    padding:80px 40px 96px;border-top:1px solid var(--line);
    background:linear-gradient(180deg,var(--bg) 0%,var(--bg-2) 100%);
  }
  .ow-ads__final-inner{
    max-width:900px;margin:0 auto;text-align:center;
    padding:52px 40px;border-radius:26px;
    border:1px solid rgba(255,87,34,.32);
    background:radial-gradient(ellipse at center,rgba(255,87,34,.16) 0%,var(--bg-3) 72%);
  }
  .ow-ads__final h2{font-size:clamp(28px,4vw,46px);line-height:1.02;margin:0 0 14px;color:#fff;}
  .ow-ads__final p{font-size:15.5px;line-height:1.65;color:var(--dim);margin:0 auto 28px;max-width:560px;}
  .ow-ads__final-ctas{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
  .ow-ads__final-contacts{
    display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:26px;
  }
  .ow-ads__final-contacts a{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.12em;text-transform:uppercase;
    color:var(--dim);text-decoration:none;
    padding:9px 15px;border-radius:999px;
    border:1px solid var(--line);background:rgba(255,255,255,.03);
    transition:color .2s ease,border-color .2s ease;
  }
  .ow-ads__final-contacts a:hover{color:#fff;border-color:var(--lava);}

  /* ===== STICKY CTA BAR ===== */
  .ow-ads__sticky{
    position:fixed;left:0;right:0;bottom:0;z-index:9000;
    display:flex;align-items:center;gap:14px;
    padding:12px 20px;
    background:rgba(8,8,15,.94);
    -webkit-backdrop-filter:blur(14px);backdrop-filter:blur(14px);
    border-top:1px solid rgba(255,87,34,.3);
    transform:translateY(110%);
    transition:transform .32s cubic-bezier(.4,0,.2,1);
  }
  .ow-ads__sticky.is-on{transform:translateY(0);}
  .ow-ads__sticky-text{flex:1;min-width:0;}
  .ow-ads__sticky-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:17px;line-height:1.1;text-transform:uppercase;
    color:#fff;margin:0;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  }
  .ow-ads__sticky-sub{
    font-family:'JetBrains Mono',monospace;
    font-size:9.5px;letter-spacing:.13em;text-transform:uppercase;
    color:var(--faint);margin:3px 0 0;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  }
  .ow-ads__sticky-ctas{display:flex;gap:8px;flex-shrink:0;}
  .ow-ads__sticky .ow-ads__btn{padding:12px 20px;font-size:11px;}

  /* ===== RESPONSIVE ===== */
  @media (max-width:1100px){
    .ow-ads__games{grid-template-columns:repeat(2,1fr);}
    .ow-ads__pk-grid{grid-template-columns:repeat(2,1fr);}
    .ow-ads__pk-note{margin-left:0;max-width:none;}
    .ow-ads__outlets{grid-template-columns:1fr;max-width:560px;margin:0 auto;}
    .ow-ads__points{grid-template-columns:repeat(2,1fr);}
  }
  @media (max-width:860px){
    .ow-ads__hero{padding:56px 20px 56px;}
    .ow-ads__section{padding:56px 20px;}
    .ow-ads__book{padding:56px 16px 64px;}
    .ow-ads__final{padding:56px 20px 72px;}
    .ow-ads__types{grid-template-columns:1fr;}
    .ow-ads__steps{grid-template-columns:1fr;}
    .ow-ads__widget-card{padding:14px;}
  }
  @media (max-width:620px){
    .ow-ads{padding-bottom:78px;}
    .ow-ads__h1{font-size:38px;}
    .ow-ads__hero-ctas{flex-direction:column;}
    .ow-ads__hero-ctas .ow-ads__btn{width:100%;}
    .ow-ads__games{grid-template-columns:1fr;}
    .ow-ads__pk-grid{grid-template-columns:1fr;}
    .ow-ads__pk-title{font-size:23px;}
    .ow-ads__points{grid-template-columns:1fr;}
    .ow-ads__final-inner{padding:36px 22px;}
    .ow-ads__final-ctas{flex-direction:column;}
    .ow-ads__final-ctas .ow-ads__btn{width:100%;}
    .ow-ads__type-ctas{flex-direction:column;}
    .ow-ads__type-ctas .ow-ads__btn{width:100%;}
    .ow-ads__sticky{padding:10px 14px;gap:10px;}
    .ow-ads__sticky-text{display:none;}
    .ow-ads__sticky-ctas{width:100%;}
    .ow-ads__sticky-ctas .ow-ads__btn{flex:1;padding:13px 10px;font-size:10.5px;letter-spacing:.1em;}
  }
  @media (prefers-reduced-motion:reduce){
    .ow-ads *{transition:none!important;}
  }
</style>

<div class="ow-ads">

  <!-- ===================== HERO ===================== -->
  <section class="ow-ads__hero" id="ow-ads-hero">
    <div class="ow-ads__hero-grid" aria-hidden="true"></div>
    <div class="ow-ads__hero-inner">
      <div class="ow-ads__eyebrow"><?php echo wp_kses_post( $ads_hero['eyebrow'] ); ?> &middot; Singapore</div>
      <h1 class="ow-ads__h1"><?php echo wp_kses_post( $ads_hero['h1'] ); ?></h1>
      <p class="ow-ads__tagline"><?php echo esc_html( $ads_hero['tagline'] ); ?></p>
      <p class="ow-ads__lede">
        VR arcades, VR escape rooms, free-roam VR, laser maze and the Floor Is Lava
        across three outlets — Kallang Wave Mall, Orchard Central and Funan.
        Book a session in under two minutes, or take a full package with a
        private room, hosted games and food time built in.
      </p>
      <div class="ow-ads__hero-ctas">
        <a class="ow-ads__btn ow-ads__btn--primary" href="#book" data-ow-cta="book_scroll">
          Check Availability &amp; Book →
        </a>
        <a class="ow-ads__btn ow-ads__btn--wa" href="<?php echo esc_url( $ads_outlet['whatsapp'] ); ?>" target="_blank" rel="noopener" data-ow-cta="whatsapp_hero">
          WhatsApp For 20+ Pax
        </a>
      </div>
      <div class="ow-ads__trust">
        <span><strong>3</strong> Outlets</span>
        <span><strong>8</strong> Game Formats</span>
        <?php
        // Price anchors, read off the live packages. Shown only for a type
        // that actually has a package whose price parses — an ad landing page
        // stating a price the packages do not back up is worse than one that
        // states none.
        foreach ( $ads_types as $ads_hero_slug => $ads_hero_type ) :
            if ( empty( $ads_packages[ $ads_hero_slug ] ) ) {
                continue;
            }
            $ads_hero_from = ow_ads_from_price( $ads_packages[ $ads_hero_slug ] );
            if ( '' === $ads_hero_from ) {
                continue;
            }
            ?>
            <span><?php echo esc_html( $ads_hero_type['label'] ); ?> From <strong><?php echo esc_html( $ads_hero_from ); ?></strong></span>
        <?php endforeach; ?>
        <span>Open Daily <strong>11am–10pm</strong></span>
        <span>Game Masters <strong>Included</strong></span>
      </div>
    </div>
  </section>

  <!-- ===================== EVENT TYPES ===================== -->
  <section class="ow-ads__section ow-ads__section--alt">
    <div class="ow-ads__wrap">
      <div class="ow-ads__head ow-ads__head--center">
        <div class="ow-ads__kicker">What Are You Planning?</div>
        <h2 class="ow-ads__h2">Team Building Or Birthday Party</h2>
        <p class="ow-ads__sub">
          Same venues, same games, two very different run sheets. Both are hosted
          end to end by our Game Masters, so nobody in your group has to organise
          anything on the day.
        </p>
      </div>

      <div class="ow-ads__types">

        <article class="ow-ads__type" style="--tint:#ff5722;">
          <div class="ow-ads__type-icon">🎯</div>
          <h3 class="ow-ads__type-title">Corporate Team Building</h3>
          <p class="ow-ads__type-text">
            Company outings, department offsites, new-hire ice breakers and
            end-of-year celebrations. Team bonding activities that put people into
            a shared problem instead of a meeting room.
          </p>
          <ul class="ow-ads__type-list">
            <li>Briefing, rotations, scoreboards and prizes run by our Game Masters</li>
            <li>VR escape rooms and co-op missions that only work if people talk</li>
            <li>Head-to-head physical challenges for teams that would rather move</li>
            <li>Private venue hire available at Kallang Wave Mall</li>
          </ul>
          <div class="ow-ads__type-ctas">
            <a class="ow-ads__btn ow-ads__btn--primary" href="#book" data-ow-cta="book_tb">Book A Session →</a>
            <a class="ow-ads__btn ow-ads__btn--ghost" href="<?php echo esc_url( home_url( '/team-building/' ) ); ?>">See Packages</a>
          </div>
        </article>

        <article class="ow-ads__type" style="--tint:#a855f7;">
          <div class="ow-ads__type-icon">🎉</div>
          <h3 class="ow-ads__type-title">Birthday Parties</h3>
          <p class="ow-ads__type-text">
            Kids' parties, teen celebrations and adult birthdays that want
            something louder than dinner. The games are the entertainment — turn
            up, play, and let us run the session.
          </p>
          <ul class="ow-ads__type-list">
            <li>Everyone plays at once, so nobody sits out watching</li>
            <li>Rounds split with a break for cake and food in between</li>
            <li>No decorating, no setup, no goodie-bag logistics</li>
            <li>Works for six-year-olds and thirty-six-year-olds alike</li>
          </ul>
          <div class="ow-ads__type-ctas">
            <a class="ow-ads__btn ow-ads__btn--primary" href="#book" data-ow-cta="book_bp">Book A Party →</a>
            <a class="ow-ads__btn ow-ads__btn--ghost" href="<?php echo esc_url( home_url( '/birthday-party/' ) ); ?>">See Packages</a>
          </div>
        </article>

      </div>
    </div>
  </section>

  <!-- ===================== PACKAGES ===================== -->
  <?php if ( ! empty( $ads_packages ) ) : ?>
  <section class="ow-ads__section">
    <div class="ow-ads__wrap">
      <div class="ow-ads__head ow-ads__head--center">
        <div class="ow-ads__kicker">What You Actually Get</div>
        <h2 class="ow-ads__h2">Packages &amp; Pricing</h2>
        <p class="ow-ads__sub">
          Real packages, real prices — what is in each one, how long it runs and
          how many people it fits. Every one is hosted by our Game Masters.
        </p>
      </div>

      <?php foreach ( $ads_types as $ads_type_slug => $ads_type ) :
        if ( empty( $ads_packages[ $ads_type_slug ] ) ) {
            continue;
        }
        $ads_type_from = ow_ads_from_price( $ads_packages[ $ads_type_slug ] );
        ?>
        <div class="ow-ads__pk-block">

          <div class="ow-ads__pk-head">
            <h3 class="ow-ads__pk-title"><?php echo esc_html( $ads_type['label'] ); ?></h3>
            <?php if ( '' !== $ads_type_from ) : ?>
              <div class="ow-ads__pk-from">From <strong><?php echo esc_html( $ads_type_from ); ?></strong></div>
            <?php endif; ?>
            <p class="ow-ads__pk-note"><?php echo esc_html( $ads_type['note'] ); ?></p>
          </div>

          <div class="ow-ads__pk-grid">
            <?php foreach ( $ads_packages[ $ads_type_slug ] as $pkg ) :
              $pkg_outlet = $ads_outlets[ $pkg['outlet'] ]; ?>
              <article class="ow-ads__pk" style="--tint:<?php echo esc_attr( $pkg_outlet['accent'] ); ?>;--tint-fg:<?php echo esc_attr( $pkg_outlet['glow'] ); ?>;">
                <span class="ow-ads__pk-outlet"><?php echo esc_html( $pkg_outlet['name'] ); ?></span>
                <h4 class="ow-ads__pk-name"><?php echo esc_html( $pkg['title'] ); ?></h4>

                <?php if ( '' !== $pkg['duration'] || '' !== $pkg['group_size'] ) : ?>
                  <div class="ow-ads__pk-meta">
                    <?php if ( '' !== $pkg['duration'] ) : ?>
                      <span>&#9201; <?php echo esc_html( $pkg['duration'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( '' !== $pkg['group_size'] ) : ?>
                      <span>&#128101; <?php echo esc_html( $pkg['group_size'] ); ?></span>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>

                <?php if ( '' !== $pkg['tagline'] ) : ?>
                  <p class="ow-ads__pk-text"><?php echo esc_html( $pkg['tagline'] ); ?></p>
                <?php endif; ?>

                <?php if ( '' !== $pkg['price'] ) : ?>
                  <div class="ow-ads__pk-price">
                    From <strong><?php echo esc_html( $pkg['price'] ); ?></strong>
                  </div>
                <?php endif; ?>

                <div class="ow-ads__pk-ctas">
                  <a class="ow-ads__btn ow-ads__btn--primary"
                     href="<?php echo esc_url( $pkg['permalink'] ); ?>"
                     data-ow-cta="package_<?php echo esc_attr( $ads_type_slug ); ?>">
                    What&rsquo;s Included →
                  </a>
                  <?php if ( '' !== $pkg['pdf'] ) : ?>
                    <a class="ow-ads__btn ow-ads__btn--ghost"
                       href="<?php echo esc_url( $pkg['pdf'] ); ?>" target="_blank" rel="noopener"
                       aria-label="Download the <?php echo esc_attr( $pkg['title'] ); ?> PDF"
                       data-ow-cta="package_pdf_<?php echo esc_attr( $ads_type_slug ); ?>">
                      PDF
                    </a>
                  <?php endif; ?>
                </div>
              </article>
            <?php endforeach; ?>
          </div>

          <div class="ow-ads__pk-all">
            <a class="ow-ads__btn ow-ads__btn--ghost" href="<?php echo esc_url( home_url( $ads_type['hub'] ) ); ?>">
              All <?php echo esc_html( $ads_type['label'] ); ?> Packages →
            </a>
          </div>

        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- ===================== GAMES ===================== -->
  <section class="ow-ads__section ow-ads__section--alt">
    <div class="ow-ads__wrap">
      <div class="ow-ads__head ow-ads__head--center">
        <div class="ow-ads__kicker">Every Game, Every Outlet</div>
        <h2 class="ow-ads__h2">Eight Ways To Play</h2>
        <p class="ow-ads__sub">
          Virtual reality and physical challenge games across the three outlets.
          Mix two or three in one visit — most groups do.
        </p>
      </div>

      <div class="ow-ads__games">
        <?php foreach ( $ads_games as $game ) : ?>
          <a class="ow-ads__game" href="<?php echo esc_url( home_url( $game['url'] ) ); ?>">
            <div class="ow-ads__game-top">
              <span class="ow-ads__game-icon" aria-hidden="true"><?php echo esc_html( $game['icon'] ); ?></span>
              <span class="ow-ads__game-stat"><?php echo esc_html( $game['stat'] ); ?></span>
            </div>
            <h3 class="ow-ads__game-name"><?php echo esc_html( $game['name'] ); ?></h3>
            <p class="ow-ads__game-desc"><?php echo esc_html( $game['desc'] ); ?></p>
            <div class="ow-ads__game-where">
              <?php foreach ( $game['outlets'] as $game_outlet_slug ) :
                $game_outlet = $ads_outlets[ $game_outlet_slug ]; ?>
                <span class="ow-ads__chip" style="--chip:<?php echo esc_attr( $game_outlet['accent'] ); ?>;--chip-fg:<?php echo esc_attr( $game_outlet['glow'] ); ?>;">
                  <?php echo esc_html( $game_outlet['short'] ); ?>
                </span>
              <?php endforeach; ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ===================== OUTLETS ===================== -->
  <section class="ow-ads__section">
    <div class="ow-ads__wrap">
      <div class="ow-ads__head ow-ads__head--center">
        <div class="ow-ads__kicker">Three Locations Across Singapore</div>
        <h2 class="ow-ads__h2">Pick The Outlet Nearest You</h2>
        <p class="ow-ads__sub">
          All three sit inside malls, right on the MRT line, so the whole group
          gets there under cover — and stays there after the session.
        </p>
      </div>

      <div class="ow-ads__outlets">
        <?php foreach ( $ads_outlets as $slug => $o ) : ?>
          <article class="ow-ads__outlet" style="--tint:<?php echo esc_attr( $o['accent'] ); ?>;--tint-fg:<?php echo esc_attr( $o['glow'] ); ?>;">
            <div class="ow-ads__outlet-body">
              <div class="ow-ads__outlet-brand"><?php echo esc_html( $o['brand'] ); ?></div>
              <h3 class="ow-ads__outlet-name"><?php echo esc_html( $o['name'] ); ?></h3>
              <p class="ow-ads__outlet-addr"><?php echo esc_html( $o['address'] ); ?></p>
              <p class="ow-ads__outlet-mrt"><?php echo esc_html( $o['mrt'] ); ?></p>
              <p class="ow-ads__outlet-pitch"><?php echo esc_html( $o['pitch'] ); ?></p>
              <p class="ow-ads__outlet-best"><?php echo esc_html( $o['best_for'] ); ?></p>
              <ul class="ow-ads__outlet-games">
                <?php foreach ( $o['games'] as $game_key ) : ?>
                  <li><?php echo esc_html( $ads_games[ $game_key ]['name'] ); ?></li>
                <?php endforeach; ?>
              </ul>
              <?php if ( isset( $ads_from_price[ $slug ] ) ) : ?>
                <div class="ow-ads__outlet-price">
                  From <strong><?php echo esc_html( $ads_from_price[ $slug ] ); ?></strong> per person
                </div>
              <?php endif; ?>
              <div class="ow-ads__outlet-ctas">
                <a class="ow-ads__btn ow-ads__btn--primary"
                   href="<?php echo esc_url( ow_ads_url( 'outlet', $slug, 'book' ) ); ?>"
                   data-ow-cta="book_outlet_<?php echo esc_attr( $slug ); ?>">
                  Book <?php echo esc_html( $o['short'] ); ?> →
                </a>
                <a class="ow-ads__btn ow-ads__btn--ghost"
                   href="<?php echo esc_url( $o['whatsapp'] ); ?>" target="_blank" rel="noopener"
                   aria-label="WhatsApp Overworld <?php echo esc_attr( $o['name'] ); ?>"
                   data-ow-cta="whatsapp_outlet_<?php echo esc_attr( $slug ); ?>">
                  WhatsApp
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ===================== BOOK (Bookeo) ===================== -->
  <section class="ow-ads__book" id="book">
    <div class="ow-ads__wrap">
      <div class="ow-ads__head ow-ads__head--center">
        <div class="ow-ads__kicker">Live Availability</div>
        <h2 class="ow-ads__h2">Book Your Group</h2>
        <p class="ow-ads__sub">
          Pick your outlet, pick a slot, pay online. Confirmation is immediate —
          nothing to chase, nobody to call back.
        </p>
      </div>

      <nav class="ow-ads__switch" aria-label="Choose an outlet to book">
        <?php foreach ( $ads_outlets as $slug => $o ) :
          $is_current = ( $slug === $ads_outlet_slug ); ?>
          <a href="<?php echo esc_url( ow_ads_url( 'outlet', $slug, 'book' ) ); ?>"
             style="--pill:<?php echo esc_attr( $o['accent'] ); ?>;"
             <?php echo $is_current ? 'aria-current="true"' : ''; ?>
             data-ow-cta="switch_<?php echo esc_attr( $slug ); ?>">
            <?php echo esc_html( $o['name'] ); ?>
          </a>
        <?php endforeach; ?>
      </nav>
      <p class="ow-ads__switch-note">
        Showing live slots for <strong><?php echo esc_html( $ads_outlet['name'] ); ?></strong>
        &middot; <?php echo esc_html( $ads_outlet['address'] ); ?>
      </p>

      <div class="ow-ads__widget-card">
        <div class="ow-ads__widget-head">
          <span class="ow-ads__widget-tag">Live availability &middot; <?php echo esc_html( $ads_outlet['name'] ); ?></span>
          <span class="ow-ads__widget-note">Secure booking powered by Bookeo</span>
        </div>
        <div class="ow-ads__widget">
          <?php
          // Exactly one Bookeo widget per document — widget.js alerts and bails
          // if it finds a second copy, which is why the outlet switcher above
          // reloads the page rather than swapping this in place.
          printf(
            '<script type="text/javascript" src="https://bookeo.com/widget.js?a=%s"></script>',
            esc_attr( $ads_outlet['bookeo_key'] )
          );
          ?>
        </div>
      </div>

      <p class="ow-ads__book-help">
        Booking a full package — private room, fixed itinerary, food time between rounds, or a large headcount?
        <a href="<?php echo esc_url( $ads_outlet['whatsapp'] ); ?>" target="_blank" rel="noopener" data-ow-cta="whatsapp_book">WhatsApp <?php echo esc_html( $ads_outlet['short'] ); ?></a>
        or call <a href="tel:<?php echo esc_attr( $ads_outlet['phone_raw'] ); ?>" data-ow-cta="call_book"><?php echo esc_html( $ads_outlet['phone'] ); ?></a>
        and we will build it around your group.
      </p>
    </div>
  </section>

  <!-- ===================== HOW IT WORKS ===================== -->
  <section class="ow-ads__section">
    <div class="ow-ads__wrap">
      <div class="ow-ads__head ow-ads__head--center">
        <div class="ow-ads__kicker">How It Works</div>
        <h2 class="ow-ads__h2">Three Steps, No Planning</h2>
      </div>
      <div class="ow-ads__steps">
        <div class="ow-ads__step">
          <h3>Pick Your Outlet</h3>
          <p>Kallang for VR, Orchard for pure physical play, Funan for both. Or tell us your group and we will pick for you.</p>
        </div>
        <div class="ow-ads__step">
          <h3>Book Your Slot</h3>
          <p>Live availability on this page. Choose a date and time, pay online, and the confirmation lands straight away.</p>
        </div>
        <div class="ow-ads__step">
          <h3>Turn Up And Play</h3>
          <p>Our Game Masters brief the group, run the rotations, keep score and hand out the prizes. You just play.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== WHY ===================== -->
  <section class="ow-ads__section ow-ads__section--alt">
    <div class="ow-ads__wrap">
      <div class="ow-ads__head ow-ads__head--center">
        <div class="ow-ads__kicker">Why Groups Book Overworld</div>
        <h2 class="ow-ads__h2">Built For Groups, Not Individuals</h2>
      </div>
      <div class="ow-ads__points">
        <div class="ow-ads__point">
          <div class="ow-ads__point-icon">🤝</div>
          <h3>Designed Around Teamwork</h3>
          <p>Escape rooms, co-op VR missions and head-to-head games that only work when people communicate.</p>
        </div>
        <div class="ow-ads__point">
          <div class="ow-ads__point-icon">🎮</div>
          <h3>No Experience Needed</h3>
          <p>Every game is pick-up-and-play. First-timers and gamers end up on exactly the same level.</p>
        </div>
        <div class="ow-ads__point">
          <div class="ow-ads__point-icon">🧑‍🏫</div>
          <h3>Hosted End To End</h3>
          <p>Game Masters handle the briefing, the rotations, the scoreboard and the prizes so you can join in.</p>
        </div>
        <div class="ow-ads__point">
          <div class="ow-ads__point-icon">📍</div>
          <h3>Three MRT-Linked Outlets</h3>
          <p>Stadium, Somerset and City Hall. Indoor, air-conditioned, and easy for a group arriving from everywhere.</p>
        </div>
        <div class="ow-ads__point">
          <div class="ow-ads__point-icon">🌦️</div>
          <h3>Rain Or Shine</h3>
          <p>Entirely indoors. No weather backup plan, no last-minute reshuffle, no wet team building.</p>
        </div>
        <div class="ow-ads__point">
          <div class="ow-ads__point-icon">⚡</div>
          <h3>Instant Confirmation</h3>
          <p>Book on this page and the slot is yours immediately — no waiting on an email to come back.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== BODY COPY ===================== -->
  <section class="ow-ads__section">
    <div class="ow-ads__copy">
      <h2 class="ow-ads__h2" style="font-size:clamp(24px,3vw,34px);margin-bottom:20px;">
        Team Building &amp; Birthday Party Venues Across Singapore
      </h2>
      <p>
        Overworld runs <strong>team building activities in Singapore</strong> and
        <strong>birthday parties</strong> at three outlets: Kallang Wave Mall,
        Orchard Central and Funan. Every session puts the group inside a shared
        challenge instead of around a table — virtual reality missions, timed
        physical games, and rooms that only open when people start talking to each
        other. It works as a corporate team building day, a department offsite, a
        company outing, a kids' birthday party or an adult celebration that wants
        more than dinner.
      </p>
      <p>
        <strong><?php echo esc_html( $ads_outlets['kallang-wave-mall']['name'] ); ?></strong>
        is the VR-heavy outlet and the natural pick for
        <a href="<?php echo esc_url( home_url( '/vr-arcade/' ) ); ?>">VR arcade</a> sessions,
        <a href="<?php echo esc_url( home_url( '/vr-escape/' ) ); ?>">VR escape room</a> challenges
        and the <a href="<?php echo esc_url( home_url( '/vr-machine-ride/' ) ); ?>">VR Machine Ride</a>,
        with the Floor Is Lava alongside them.
        <strong><?php echo esc_html( $ads_outlets['orchard-central']['name'] ); ?></strong>
        is entirely physical — <a href="<?php echo esc_url( home_url( '/floor-is-lava/' ) ); ?>">Floor Is Lava</a>,
        <a href="<?php echo esc_url( home_url( '/laser-maze/' ) ); ?>">Laser Maze</a> and
        <a href="<?php echo esc_url( home_url( '/tap-tap/' ) ); ?>">Tap Tap</a> — which suits
        high-energy groups and younger birthday crowds looking for a party venue on
        Orchard Road.
        <strong><?php echo esc_html( $ads_outlets['funan']['name'] ); ?></strong> sits between
        the two, with <a href="<?php echo esc_url( home_url( '/vr-free-roam/' ) ); ?>">VR Free Roam</a>,
        <a href="<?php echo esc_url( home_url( '/xr-party-game/' ) ); ?>">XR Party Game</a> and
        Floor Is Lava, a short walk from the CBD at City Hall.
      </p>
      <p>
        Groups book us for <strong>corporate team building</strong>, team bonding
        activities, company D&amp;D warm-ups, school and university outings,
        <strong>VR birthday parties</strong>, kids' birthday party venues, teen
        celebrations and family gatherings. Because everything is indoors and
        air-conditioned, a Singapore afternoon downpour never becomes your problem.
        Sessions run every day from 11am to 10pm, and the calendar above shows
        exactly which slots are still open.
      </p>
      <p>
        Small and mid-sized groups can book straight through this page. For larger
        headcounts, a private room, a fixed itinerary or a package with food time
        built in, message the outlet directly and we will put it together around
        your date, your group size and your budget. Full package details also live
        on the <a href="<?php echo esc_url( home_url( '/team-building/' ) ); ?>">team building</a>
        and <a href="<?php echo esc_url( home_url( '/birthday-party/' ) ); ?>">birthday party</a> pages.
      </p>
    </div>
  </section>

  <!-- ===================== FAQ ===================== -->
  <section class="ow-ads__section ow-ads__section--alt">
    <div class="ow-ads__wrap">
      <div class="ow-ads__head ow-ads__head--center">
        <div class="ow-ads__kicker">Before You Book</div>
        <h2 class="ow-ads__h2">Questions Groups Ask</h2>
      </div>
      <div class="ow-ads__faq">
        <?php foreach ( $ads_faqs as $index => $faq ) : ?>
          <details class="ow-ads__faq-item"<?php echo 0 === $index ? ' open' : ''; ?>>
            <summary class="ow-ads__faq-q"><?php echo esc_html( $faq['q'] ); ?></summary>
            <div class="ow-ads__faq-a"><?php echo esc_html( $faq['a'] ); ?></div>
          </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ===================== FINAL CTA ===================== -->
  <section class="ow-ads__final">
    <div class="ow-ads__final-inner">
      <div class="ow-ads__kicker">Ready When You Are</div>
      <h2>Get Your Group Booked In</h2>
      <p>
        Three outlets, eight games, open daily 11am–10pm. Check live availability
        and lock in your date — or message us and we will build the session around
        your group.
      </p>
      <div class="ow-ads__final-ctas">
        <a class="ow-ads__btn ow-ads__btn--primary" href="#book" data-ow-cta="book_final">Check Availability &amp; Book →</a>
        <a class="ow-ads__btn ow-ads__btn--wa" href="<?php echo esc_url( $ads_outlet['whatsapp'] ); ?>" target="_blank" rel="noopener" data-ow-cta="whatsapp_final">WhatsApp Us</a>
      </div>
      <div class="ow-ads__final-contacts">
        <?php foreach ( $ads_outlets as $slug => $o ) : ?>
          <a href="tel:<?php echo esc_attr( $o['phone_raw'] ); ?>" data-ow-cta="call_<?php echo esc_attr( $slug ); ?>">
            <?php echo esc_html( $o['short'] ); ?> <?php echo esc_html( $o['phone'] ); ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ===================== STICKY CTA ===================== -->
  <div class="ow-ads__sticky" id="ow-ads-sticky">
    <div class="ow-ads__sticky-text">
      <p class="ow-ads__sticky-title">Team Building &amp; Birthdays</p>
      <p class="ow-ads__sticky-sub">3 Outlets &middot; Open Daily 11am–10pm</p>
    </div>
    <div class="ow-ads__sticky-ctas">
      <a class="ow-ads__btn ow-ads__btn--primary" href="#book" data-ow-cta="book_sticky">Book Now →</a>
      <a class="ow-ads__btn ow-ads__btn--wa" href="<?php echo esc_url( $ads_outlet['whatsapp'] ); ?>" target="_blank" rel="noopener" data-ow-cta="whatsapp_sticky">WhatsApp</a>
    </div>
  </div>

</div><!-- /.ow-ads -->

<?php
// FAQPage structured data. The SEO mu-plugin emits the Organization and
// WebSite entities for this page but no FAQ block, so this is the only copy
// on the document — two would contradict each other.
$ads_faq_schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array(),
);
foreach ( $ads_faqs as $faq ) {
	$ads_faq_schema['mainEntity'][] = array(
		'@type'          => 'Question',
		'name'           => $faq['q'],
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => $faq['a'],
		),
	);
}
printf(
	'<script type="application/ld+json">%s</script>' . "\n",
	wp_json_encode( $ads_faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
);
?>

<script>
(function () {
  'use strict';

  // Sticky CTA bar: hidden over the hero (its buttons are already on screen
  // there), shown for the rest of the page.
  var bar  = document.getElementById('ow-ads-sticky');
  var hero = document.getElementById('ow-ads-hero');

  if (bar && hero && 'IntersectionObserver' in window) {
    new IntersectionObserver(function (entries) {
      bar.classList.toggle('is-on', !entries[0].isIntersecting);
    }, { rootMargin: '-120px 0px 0px 0px' }).observe(hero);
  } else if (bar) {
    bar.classList.add('is-on');
  }

  // Smooth scroll to the booking widget without pushing a history entry the
  // back button then has to chew through.
  document.addEventListener('click', function (e) {
    var link = e.target.closest ? e.target.closest('a[href="#book"]') : null;
    if (!link) { return; }
    var target = document.getElementById('book');
    if (!target) { return; }
    e.preventDefault();
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  // Conversion signals for Google Ads / GA4. Pushed to dataLayer when one
  // exists and passed to gtag when one does not; silent when neither is set
  // up yet, so adding tracking later needs no change to this page.
  document.addEventListener('click', function (e) {
    var el = e.target.closest ? e.target.closest('[data-ow-cta]') : null;
    if (!el) { return; }

    var name = el.getAttribute('data-ow-cta');
    var payload = {
      cta: name,
      outlet: <?php echo wp_json_encode( $ads_outlet_slug ); ?>,
      focus: <?php echo wp_json_encode( $ads_focus ? $ads_focus : 'both' ); ?>
    };

    try {
      if (window.dataLayer && typeof window.dataLayer.push === 'function') {
        window.dataLayer.push(Object.assign({ event: 'ow_cta_click' }, payload));
      } else if (typeof window.gtag === 'function') {
        window.gtag('event', 'ow_cta_click', payload);
      }
    } catch (err) { /* tracking must never break the page */ }
  });
}());
</script>

<?php get_footer(); ?>

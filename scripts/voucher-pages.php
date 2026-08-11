<?php
/**
 * Gift Voucher pages — outlet chooser + per-outlet Bookeo voucher embed.
 *
 * Mirrors the Book Now set built by scripts/booking-redesign.php:
 *   /booking/        (outlet chooser)   ->  gift voucher hub
 *   /book-now-kwm/   (Bookeo embed)     ->  /voucher-kwm/    and siblings
 *
 * The voucher flow uses the SAME Bookeo widget keys as booking, with
 * &buyvoucher=true appended. Verified against Bookeo's widget.js: the script
 * forwards the parameter into the frame it builds —
 *   b_<KEY>_start.html?inwidget=true&a=<KEY>&buyvoucher=true
 * so no new widget has to be created in the Bookeo back office.
 *
 * NOTHING HERE GOES LIVE. All four pages are created as DRAFTS, the existing
 * published /gift-voucher/ stub (523) is not touched, and the nav menu is not
 * touched. See "GO LIVE" at the bottom of this file for the steps, which are
 * deliberately not automated.
 *
 * Idempotent: re-running updates the same pages rather than creating more.
 * Refuses to overwrite any page that is already published.
 *
 * Run: wp eval-file voucher-pages.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =====================================================================
   Outlets — same data as the booking pages, same Bookeo keys
   ===================================================================== */

$outlets = array(
	'voucher-kwm'     => array(
		'title'     => 'Gift Voucher - KWM',
		'short'     => 'Kallang',
		'brand'     => 'Overworld VR',
		'venue'     => 'Kallang Wave Mall',
		'addr'      => '1 Stadium Place, #01-63/64, S(397628)',
		'addr_1'    => '1 Stadium Place, #01-63/64',
		'addr_2'    => 'Kallang Wave Mall · S(397628)',
		'accent'    => '#2f6bff',
		'glow'      => '#6f9bff',
		'soft'      => 'rgba(47,107,255,.22)',
		'key'       => '231YALUW419DA4CE7973',
		'acts'      => array( 'VR Arcade', 'VR Escape', 'Floor Is Lava', 'Combo Deals' ),
		'feats'     => array( 'VR Arcade · 30+ games', 'Floor Is Lava', 'Largest outlet' ),
		'phone'     => '+65 6513 0561',
		'phone_raw' => '+6565130561',
		'wa'        => 'https://wa.me/+6596101682',
	),
	'voucher-orchard' => array(
		'title'     => 'Gift Voucher - Orchard',
		'short'     => 'Orchard',
		'brand'     => 'Overworld Lava',
		'venue'     => 'Orchard Central',
		'addr'      => '181 Orchard Road, #05-30/K1/K3, S(238896)',
		'addr_1'    => '181 Orchard Road, #05-30/K1/K3',
		'addr_2'    => 'Orchard Central · S(238896)',
		'accent'    => '#ff5722',
		'glow'      => '#ff8a3d',
		'soft'      => 'rgba(255,87,34,.22)',
		'key'       => '231T6UX7U19D0A676CD2',
		'acts'      => array( 'Floor Is Lava', 'Laser Maze', 'Tap Tap', 'Combo Deals' ),
		'feats'     => array( 'Floor Is Lava', 'Laser Maze', 'Heart of Orchard' ),
		'phone'     => '+65 8801 4303',
		'phone_raw' => '+6588014303',
		'wa'        => 'https://wa.me/message/WJ7MGRFFVGHAF1',
	),
	'voucher-funan'   => array(
		'title'     => 'Gift Voucher - Funan',
		'short'     => 'Funan',
		'brand'     => 'Overworld Funan',
		'venue'     => 'Funan',
		'addr'      => '107 North Bridge Road, #04-14 & K1, S(179105)',
		'addr_1'    => '107 North Bridge Road, #04-14 &amp; K1',
		'addr_2'    => 'Funan · S(179105)',
		'accent'    => '#a855f7',
		'glow'      => '#c89aff',
		'soft'      => 'rgba(168,85,247,.22)',
		'key'       => '231RYKULN19D91C736C8',
		'acts'      => array( 'VR Free Roam', 'XR Party Game', 'Floor Is Lava', 'Combo Deals' ),
		'feats'     => array( 'XR Party Game', 'VR Free Roam', 'In the CBD' ),
		'phone'     => '+65 8914 0061',
		'phone_raw' => '+6589140061',
		'wa'        => 'https://wa.me/6589140061',
	),
);

// Where the hub will finally live. The switch link points at it now so that
// nothing needs editing on go-live.
const OW_GV_HUB_URL = '/gift-voucher/';

/* =====================================================================
   Per-outlet voucher page — the Book Now layout, voucher wording
   ===================================================================== */

$page_css = <<<'CSS'
<style>
  .ow-gv{background:#000;color:#fff;font-family:'Space Grotesk','Inter',system-ui,sans-serif;}
  .ow-gv *{box-sizing:border-box;}
  .ow-gv__hero{
    padding:110px 24px 54px;text-align:center;position:relative;overflow:hidden;
    background:
      radial-gradient(ellipse 70% 55% at 50% -10%, color-mix(in srgb, var(--accent) 22%, transparent), transparent 70%),
      #000;
  }
  .ow-gv__hero-inner{max-width:860px;margin:0 auto;position:relative;z-index:2;}
  .ow-gv__eyebrow{
    display:inline-flex;align-items:center;gap:10px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.26em;text-transform:uppercase;
    color:var(--glow);margin-bottom:20px;
  }
  .ow-gv__eyebrow::before,.ow-gv__eyebrow::after{content:"";width:26px;height:1px;background:var(--accent);}
  .ow-gv__title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(40px,6vw,72px);line-height:1;font-weight:400;
    text-transform:uppercase;margin:0 0 16px;color:#fff;letter-spacing:.005em;
  }
  .ow-gv__title span{
    background:linear-gradient(180deg,#fff 0%,var(--glow) 130%);
    -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;
  }
  .ow-gv__sub{
    font-size:14.5px;color:rgba(220,225,240,.62);line-height:1.6;margin:0 auto 24px;max-width:520px;
  }
  .ow-gv__sub strong{color:#fff;font-weight:600;}
  .ow-gv__pills{display:flex;justify-content:center;flex-wrap:wrap;gap:8px;margin-bottom:30px;}
  .ow-gv__pill{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.12em;text-transform:uppercase;
    padding:7px 14px;border-radius:999px;
    border:1px solid rgba(255,255,255,.14);color:rgba(255,255,255,.72);
    background:rgba(255,255,255,.03);white-space:nowrap;
  }
  .ow-gv__switch{
    display:inline-flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    color:rgba(255,255,255,.55);text-decoration:none;
    border:1px solid rgba(255,255,255,.14);border-radius:999px;
    padding:10px 20px;transition:color .2s ease,border-color .2s ease;
  }
  .ow-gv__switch:hover{color:#fff;border-color:var(--accent);}
  .ow-gv__embed{background:#000;padding:52px 24px 72px;}
  .ow-gv__embed-inner{max-width:1080px;margin:0 auto;}
  .ow-gv__embed-head{
    display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;
    padding-bottom:16px;margin-bottom:28px;border-bottom:1px solid rgba(255,255,255,.1);
  }
  .ow-gv__embed-tag{
    display:inline-flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.72);
  }
  .ow-gv__embed-tag::before{
    content:"";width:7px;height:7px;border-radius:50%;
    background:var(--accent);box-shadow:0 0 8px var(--accent);
  }
  .ow-gv__embed-note{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.42);
  }
  /* Bookeo paints its own document inside the frame — keep the card
     transparent and force the iframe block/full-width so no section colour
     shows at its edges. Same treatment as the Book Now pages. */
  .ow-gv__widget{min-height:420px;background:transparent;border-radius:16px;overflow:hidden;}
  .ow-gv__widget iframe{display:block;width:100%;border:0;}
  .ow-gv__help{
    background:#000;border-top:1px solid rgba(255,255,255,.08);
    padding:36px 24px;text-align:center;
  }
  .ow-gv__help p{
    margin:0;font-family:'JetBrains Mono',monospace;
    font-size:11.5px;letter-spacing:.12em;text-transform:uppercase;
    color:rgba(255,255,255,.5);line-height:2;
  }
  .ow-gv__help a{color:var(--glow);text-decoration:none;}
  .ow-gv__help a:hover{color:#fff;}
  @media (max-width:600px){
    .ow-gv__hero{padding:84px 18px 44px;}
    .ow-gv__title{font-size:38px;}
    .ow-gv__embed{padding:40px 14px 56px;}
    .ow-gv__embed-head{justify-content:center;text-align:center;}
  }
</style>
CSS;

/* =====================================================================
   Hub — the /booking/ chooser, voucher wording
   ===================================================================== */

$hub_css = <<<'CSS'
<style>
  .ow-gvh{
    --bg:#06060c;
    --fg:#fff;
    --dim:rgba(220,225,240,.62);
    --line:rgba(255,255,255,.08);
    background:
      radial-gradient(ellipse at 20% 0%,rgba(47,107,255,.12),transparent 45%),
      radial-gradient(ellipse at 80% 10%,rgba(168,85,247,.12),transparent 45%),
      radial-gradient(ellipse at 50% 100%,rgba(255,87,34,.10),transparent 50%),
      var(--bg);
    color:var(--fg);
    font-family:'Space Grotesk','Inter',system-ui,sans-serif;
    padding:120px 40px;
    position:relative;overflow:hidden;
  }
  .ow-gvh *{box-sizing:border-box;}
  /* Hello/Elementor injects link underlines — kill them inside the cards */
  .ow-gvh a,.ow-gvh a:link,.ow-gvh a:visited,.ow-gvh a:hover,.ow-gvh a:focus,.ow-gvh a:active,
  .ow-gvh .ow-gvh__card,.ow-gvh .ow-gvh__card-tag,.ow-gvh .ow-gvh__card-name,
  .ow-gvh .ow-gvh__card-addr,.ow-gvh .ow-gvh__card-feats,.ow-gvh .ow-gvh__card-feats li,
  .ow-gvh .ow-gvh__card-btn{
    text-decoration:none !important;text-decoration-line:none !important;
    border-bottom:none !important;background-image:none !important;
  }
  .ow-gvh .ow-gvh__note a{border-bottom:1px solid var(--line) !important;}
  .ow-gvh .ow-gvh__note a:hover{border-bottom-color:#fff !important;}
  .ow-gvh::before{
    content:"";position:absolute;inset:0;pointer-events:none;
    background-image:
      linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),
      linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);
    background-size:64px 64px;
    mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
    -webkit-mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
  }
  .ow-gvh__inner{max-width:1200px;margin:0 auto;position:relative;z-index:2;}
  .ow-gvh__head{text-align:center;margin-bottom:64px;}
  .ow-gvh__eyebrow{
    display:inline-flex;align-items:center;gap:10px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.24em;text-transform:uppercase;
    color:var(--dim);margin-bottom:20px;
  }
  .ow-gvh__eyebrow::before,.ow-gvh__eyebrow::after{content:"";width:30px;height:1px;background:rgba(255,255,255,.25);}
  .ow-gvh__title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(48px,7vw,96px);
    line-height:1;letter-spacing:-.02em;
    text-transform:uppercase;font-weight:400;margin:0 0 18px;
    background:linear-gradient(180deg,#fff 0%,rgba(255,255,255,.6) 100%);
    -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;
  }
  .ow-gvh__sub{font-size:17px;color:var(--dim);line-height:1.6;margin:0 auto;max-width:560px;}
  .ow-gvh__grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
  .ow-gvh__card{
    position:relative;display:flex;flex-direction:column;
    border:1px solid var(--line);border-radius:24px;
    padding:40px 32px 32px;background:rgba(255,255,255,.025);
    text-decoration:none;color:inherit;overflow:hidden;min-height:340px;
    transition:transform .35s ease, border-color .3s ease, box-shadow .35s ease, background .3s ease;
  }
  .ow-gvh__card::before{
    content:"";position:absolute;top:0;left:0;right:0;height:4px;
    background:linear-gradient(to right,transparent,var(--c),transparent);opacity:.7;
  }
  .ow-gvh__card::after{
    content:"";position:absolute;top:-80px;right:-80px;width:240px;height:240px;border-radius:50%;
    background:radial-gradient(circle,var(--c-soft),transparent 70%);
    filter:blur(40px);pointer-events:none;transition:opacity .35s ease;opacity:.6;
  }
  .ow-gvh__card:hover{
    transform:translateY(-8px);border-color:var(--c);
    background:rgba(255,255,255,.04);box-shadow:0 30px 70px -25px var(--c);
  }
  .ow-gvh__card:hover::after{opacity:1;}
  .ow-gvh__card-tag{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--c-glow);margin-bottom:16px;
    display:flex;align-items:center;gap:10px;position:relative;z-index:1;
  }
  .ow-gvh__card-tag::before{
    content:"";width:8px;height:8px;border-radius:50%;
    background:var(--c);box-shadow:0 0 12px var(--c);
  }
  .ow-gvh__card-name{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:38px;line-height:1;font-weight:400;text-transform:uppercase;color:#fff;
    margin:0 0 12px;letter-spacing:-.01em;position:relative;z-index:1;
  }
  .ow-gvh__card-addr{font-size:13.5px;color:var(--dim);line-height:1.5;margin:0 0 24px;position:relative;z-index:1;}
  .ow-gvh__card-feats{
    list-style:none;padding:0;margin:0 0 28px;
    display:flex;flex-direction:column;gap:8px;position:relative;z-index:1;
  }
  .ow-gvh__card-feats li{font-size:13px;color:var(--dim);padding-left:20px;position:relative;line-height:1.4;}
  .ow-gvh__card-feats li::before{
    content:"";position:absolute;left:0;top:6px;width:11px;height:6px;
    border-left:2px solid var(--c);border-bottom:2px solid var(--c);transform:rotate(-45deg);
  }
  .ow-gvh__card-btn{
    margin-top:auto;display:inline-flex;align-items:center;justify-content:center;gap:10px;
    padding:16px 24px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:13px;letter-spacing:.14em;text-transform:uppercase;font-weight:700;
    background:var(--c);color:#06060c;position:relative;z-index:1;
    transition:gap .25s ease, box-shadow .35s ease;box-shadow:0 10px 30px -12px var(--c);
  }
  .ow-gvh__card:hover .ow-gvh__card-btn{gap:14px;box-shadow:0 14px 40px -10px var(--c);}
  .ow-gvh__card-btn .arr{transition:transform .25s ease;}
  .ow-gvh__card:hover .ow-gvh__card-btn .arr{transform:translateX(3px);}
  .ow-gvh__note{
    text-align:center;margin-top:48px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:var(--dim);
  }
  .ow-gvh__note a{color:#fff;text-decoration:none;border-bottom:1px solid var(--line);}
  .ow-gvh__note a:hover{border-bottom-color:#fff;}
  @media (max-width:980px){
    .ow-gvh{padding:90px 28px;}
    .ow-gvh__grid{grid-template-columns:1fr;gap:18px;max-width:560px;margin:0 auto;}
    .ow-gvh__card{min-height:auto;}
  }
  @media (max-width:560px){
    .ow-gvh{padding:70px 18px;}
    .ow-gvh__title{font-size:52px;}
    .ow-gvh__card{padding:32px 26px 26px;}
    .ow-gvh__card-name{font-size:32px;}
  }
</style>
CSS;

/* =====================================================================
   Build + write
   ===================================================================== */

/**
 * Create or update a page as a DRAFT holding one Elementor HTML widget.
 * Refuses to touch anything already published.
 */
function ow_gv_upsert_draft( $slug, $title, $html ) {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );

	if ( $existing && 'publish' === $existing->post_status ) {
		printf( "SKIP   /%s/ is already published (ID %d) — not touching it\n", $slug, $existing->ID );
		return 0;
	}

	if ( $existing ) {
		$id = $existing->ID;
		wp_update_post( array( 'ID' => $id, 'post_title' => $title, 'post_status' => 'draft' ) );
		$verb = 'updated';
	} else {
		$id = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'draft',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_content' => '',
		) );
		$verb = 'created';
	}

	if ( ! $id || is_wp_error( $id ) ) {
		printf( "ERROR  /%s/ could not be saved\n", $slug );
		return 0;
	}

	// Same skeleton the Book Now pages use: one full-width, zero-padding
	// container holding a single HTML widget.
	$data = array(
		array(
			'id'       => substr( md5( $slug . 'container' ), 0, 7 ),
			'elType'   => 'container',
			'settings' => array(
				'content_width' => 'full',
				'padding'       => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ),
			),
			'elements' => array(
				array(
					'id'         => substr( md5( $slug . 'widget' ), 0, 7 ),
					'elType'     => 'widget',
					'settings'   => array( 'html' => $html ),
					'elements'   => array(),
					'widgetType' => 'html',
				),
			),
			'isInner'  => false,
		),
	);

	update_post_meta( $id, '_wp_page_template', 'default' );
	update_post_meta( $id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $id, '_elementor_version', get_post_meta( 898, '_elementor_version', true ) ?: '4.1.4' );
	update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) );
	delete_post_meta( $id, '_elementor_element_cache' );

	printf( "OK     /%s/ %s as DRAFT (ID %d) — preview: %s\n", $slug, $verb, $id, get_preview_post_link( $id ) );
	return $id;
}

$ids = array();

// --- the three outlet pages -------------------------------------------
foreach ( $outlets as $slug => $o ) {
	$pills = '';
	foreach ( $o['acts'] as $a ) {
		$pills .= '<span class="ow-gv__pill">' . esc_html( $a ) . '</span>';
	}

	$html = $page_css
		. '<div class="ow-gv" style="--accent:' . $o['accent'] . ';--glow:' . $o['glow'] . ';">'
		. '<div class="ow-gv__hero"><div class="ow-gv__hero-inner">'
		. '<div class="ow-gv__eyebrow">' . esc_html( $o['brand'] ) . ' &middot; Gift Voucher</div>'
		. '<h1 class="ow-gv__title">Gift <span>' . esc_html( $o['short'] ) . '</span></h1>'
		. '<p class="ow-gv__sub">Buy a gift voucher for <strong>' . esc_html( $o['venue'] ) . '</strong> &middot; ' . esc_html( $o['addr'] ) . '</p>'
		. '<div class="ow-gv__pills">' . $pills . '</div>'
		. '<a class="ow-gv__switch" href="' . OW_GV_HUB_URL . '">&#8596; Choose a different outlet</a>'
		. '</div></div>'
		. '<div class="ow-gv__embed"><div class="ow-gv__embed-inner">'
		. '<div class="ow-gv__embed-head">'
		. '<span class="ow-gv__embed-tag">Gift vouchers &middot; ' . esc_html( $o['venue'] ) . '</span>'
		. '<span class="ow-gv__embed-note">Secure checkout powered by Bookeo</span>'
		. '</div>'
		. '<div class="ow-gv__widget">'
		. '<script type="text/javascript" src="https://bookeo.com/widget.js?a=' . $o['key'] . '&amp;buyvoucher=true"></script>'
		. '</div>'
		. '</div></div>'
		. '<div class="ow-gv__help"><p>Questions about vouchers? Call <a href="tel:' . $o['phone_raw'] . '">' . esc_html( $o['phone'] ) . '</a> &middot; <a href="' . esc_url( $o['wa'] ) . '" target="_blank" rel="noopener">WhatsApp us</a></p></div>'
		. '</div>';

	$ids[ $slug ] = ow_gv_upsert_draft( $slug, $o['title'], $html );
}

// --- the hub ----------------------------------------------------------
$cards = '';
foreach ( $outlets as $slug => $o ) {
	$feats = '';
	foreach ( $o['feats'] as $f ) {
		$feats .= '<li>' . $f . '</li>';
	}
	$cards .= '<a class="ow-gvh__card" style="--c:' . $o['accent'] . ';--c-glow:' . $o['glow'] . ';--c-soft:' . $o['soft'] . ';" href="/' . $slug . '/">'
		. '<div class="ow-gvh__card-tag">' . esc_html( $o['brand'] ) . '</div>'
		. '<h2 class="ow-gvh__card-name">' . esc_html( $o['short'] ) . '</h2>'
		. '<p class="ow-gvh__card-addr">' . $o['addr_1'] . '<br>' . $o['addr_2'] . '</p>'
		. '<ul class="ow-gvh__card-feats">' . $feats . '</ul>'
		. '<span class="ow-gvh__card-btn">' . esc_html( $o['short'] ) . ' Voucher <span class="arr">&rarr;</span></span>'
		. '</a>';
}

$hub_html = $hub_css
	. '<section class="ow-gvh"><div class="ow-gvh__inner">'
	. '<div class="ow-gvh__head">'
	. '<div class="ow-gvh__eyebrow">Gift Vouchers</div>'
	. '<h1 class="ow-gvh__title">Choose Your Outlet</h1>'
	. '<p class="ow-gvh__sub">Each outlet issues its own vouchers. Pick the one your voucher is for and buy it through our secure Bookeo checkout.</p>'
	. '</div>'
	. '<div class="ow-gvh__grid">' . $cards . '</div>'
	. '<div class="ow-gvh__note">Looking to book a session instead? <a href="/booking/">Book now &rarr;</a></div>'
	. '</div></section>';

$ids['gift-voucher-preview'] = ow_gv_upsert_draft( 'gift-voucher-preview', 'Gift Voucher - Hub (preview)', $hub_html );

echo "\ndone: " . implode( ', ', array_filter( $ids ) ) . "\n";

/* =====================================================================
   GO LIVE — deliberately NOT automated. Run by hand once approved.
   ---------------------------------------------------------------------
   1. Publish the three outlet pages:
        wp post update <kwm-id> <orchard-id> <funan-id> --post_status=publish

   2. Move the hub onto the existing /gift-voucher/ page (523), which is
      currently a published empty stub:
        wp eval '
          $html = json_decode(get_post_meta(<hub-draft-id>, "_elementor_data", true), true);
          update_post_meta(523, "_elementor_data", wp_slash(wp_json_encode($html)));
          update_post_meta(523, "_elementor_edit_mode", "builder");
          update_post_meta(523, "_elementor_template_type", "wp-page");
          delete_post_meta(523, "_elementor_element_cache");'
      Then trash the hub draft.

   3. Repoint the nav. The three "Gift Voucher" children currently link
      straight out to bookeo.com/<account>/buyvoucher. Point them at
      /voucher-kwm/, /voucher-orchard/, /voucher-funan/ and make the parent
      "Gift Voucher" link to /gift-voucher/ instead of "#".

   4. Purge: delete _elementor_element_cache on every touched page and run
      wp litespeed-purge all.
   ===================================================================== */

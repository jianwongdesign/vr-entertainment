<?php
/**
 * Redesign the three Book Now pages: dark site-style hero per outlet +
 * white middle section purpose-built for the (white-bg) Bookeo embed +
 * dark help strip. Keeps each page's existing Bookeo widget key.
 * Run: wp eval-file booking-redesign.php
 */

$outlets = array(
	898 => array(
		'short'      => 'Kallang',
		'brand'      => 'Overworld VR',
		'venue'      => 'Kallang Wave Mall',
		'addr'       => '1 Stadium Place, #01-63/64, S(397628)',
		'accent'     => '#2f6bff',
		'glow'       => '#6f9bff',
		'key'        => '231YALUW419DA4CE7973',
		'acts'       => array( 'VR Arcade', 'VR Escape', 'Floor Is Lava', 'Combo Deals' ),
		'phone'      => '+65 6513 0561',
		'phone_raw'  => '+6565130561',
		'wa'         => 'https://wa.me/+6596101682',
	),
	886 => array(
		'short'      => 'Orchard',
		'brand'      => 'Overworld Lava',
		'venue'      => 'Orchard Central',
		'addr'       => '181 Orchard Road, #05-30/K1/K3, S(238896)',
		'accent'     => '#ff5722',
		'glow'       => '#ff8a3d',
		'key'        => '231T6UX7U19D0A676CD2',
		'acts'       => array( 'Floor Is Lava', 'Laser Maze', 'Tap Tap', 'Combo Deals' ),
		'phone'      => '+65 8801 4303',
		'phone_raw'  => '+6588014303',
		'wa'         => 'https://wa.me/message/WJ7MGRFFVGHAF1',
	),
	893 => array(
		'short'      => 'Funan',
		'brand'      => 'Overworld Funan',
		'venue'      => 'Funan',
		'addr'       => '107 North Bridge Road, #04-14 & K1, S(179105)',
		'accent'     => '#a855f7',
		'glow'       => '#c89aff',
		'key'        => '231RYKULN19D91C736C8',
		'acts'       => array( 'VR Free Roam', 'XR Party Game', 'Floor Is Lava', 'Combo Deals' ),
		'phone'      => '+65 8914 0061',
		'phone_raw'  => '+6589140061',
		'wa'         => 'https://wa.me/6589140061',
	),
);

$css = <<<'CSS'
<style>
  .ow-bk{background:#000;color:#fff;font-family:'Space Grotesk','Inter',system-ui,sans-serif;}
  .ow-bk *{box-sizing:border-box;}
  .ow-bk__hero{
    padding:110px 24px 54px;text-align:center;position:relative;overflow:hidden;
    background:
      radial-gradient(ellipse 70% 55% at 50% -10%, color-mix(in srgb, var(--accent) 22%, transparent), transparent 70%),
      #000;
  }
  .ow-bk__hero-inner{max-width:860px;margin:0 auto;position:relative;z-index:2;}
  .ow-bk__eyebrow{
    display:inline-flex;align-items:center;gap:10px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.26em;text-transform:uppercase;
    color:var(--glow);margin-bottom:20px;
  }
  .ow-bk__eyebrow::before,.ow-bk__eyebrow::after{content:"";width:26px;height:1px;background:var(--accent);}
  .ow-bk__title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(40px,6vw,72px);line-height:1;font-weight:400;
    text-transform:uppercase;margin:0 0 16px;color:#fff;letter-spacing:.005em;
  }
  .ow-bk__title span{
    background:linear-gradient(180deg,#fff 0%,var(--glow) 130%);
    -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;
  }
  .ow-bk__sub{
    font-size:14.5px;color:rgba(220,225,240,.62);line-height:1.6;margin:0 auto 24px;max-width:520px;
  }
  .ow-bk__sub strong{color:#fff;font-weight:600;}
  .ow-bk__pills{display:flex;justify-content:center;flex-wrap:wrap;gap:8px;margin-bottom:30px;}
  .ow-bk__pill{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.12em;text-transform:uppercase;
    padding:7px 14px;border-radius:999px;
    border:1px solid rgba(255,255,255,.14);color:rgba(255,255,255,.72);
    background:rgba(255,255,255,.03);white-space:nowrap;
  }
  .ow-bk__switch{
    display:inline-flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    color:rgba(255,255,255,.55);text-decoration:none;
    border:1px solid rgba(255,255,255,.14);border-radius:999px;
    padding:10px 20px;transition:color .2s ease,border-color .2s ease;
  }
  .ow-bk__switch:hover{color:#fff;border-color:var(--accent);}
  .ow-bk__embed{background:#fff;padding:52px 24px 72px;}
  .ow-bk__embed-inner{max-width:1080px;margin:0 auto;}
  .ow-bk__embed-head{
    display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;
    padding-bottom:16px;margin-bottom:28px;border-bottom:1px solid #eceef2;
  }
  .ow-bk__embed-tag{
    display:inline-flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.2em;text-transform:uppercase;color:#7b8291;
  }
  .ow-bk__embed-tag::before{
    content:"";width:7px;height:7px;border-radius:50%;
    background:var(--accent);box-shadow:0 0 8px var(--accent);
  }
  .ow-bk__embed-note{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.12em;text-transform:uppercase;color:#aab0bc;
  }
  .ow-bk__widget{min-height:420px;}
  .ow-bk__help{
    background:#000;border-top:1px solid rgba(255,255,255,.08);
    padding:36px 24px;text-align:center;
  }
  .ow-bk__help p{
    margin:0;font-family:'JetBrains Mono',monospace;
    font-size:11.5px;letter-spacing:.12em;text-transform:uppercase;
    color:rgba(255,255,255,.5);line-height:2;
  }
  .ow-bk__help a{color:var(--glow);text-decoration:none;}
  .ow-bk__help a:hover{color:#fff;}
  @media (max-width:600px){
    .ow-bk__hero{padding:84px 18px 44px;}
    .ow-bk__title{font-size:38px;}
    .ow-bk__embed{padding:40px 14px 56px;}
    .ow-bk__embed-head{justify-content:center;text-align:center;}
  }
</style>
CSS;

$updated = array();
foreach ( $outlets as $pid => $o ) {
	$pills = '';
	foreach ( $o['acts'] as $a ) {
		$pills .= '<span class="ow-bk__pill">' . esc_html( $a ) . '</span>';
	}

	$html = $css
		. '<div class="ow-bk" style="--accent:' . $o['accent'] . ';--glow:' . $o['glow'] . ';">'
		. '<div class="ow-bk__hero"><div class="ow-bk__hero-inner">'
		. '<div class="ow-bk__eyebrow">' . esc_html( $o['brand'] ) . ' &middot; Book Your Session</div>'
		. '<h1 class="ow-bk__title">Book <span>' . esc_html( $o['short'] ) . '</span></h1>'
		. '<p class="ow-bk__sub"><strong>' . esc_html( $o['venue'] ) . '</strong> &middot; ' . esc_html( $o['addr'] ) . '</p>'
		. '<div class="ow-bk__pills">' . $pills . '</div>'
		. '<a class="ow-bk__switch" href="/booking/">&#8596; Choose a different outlet</a>'
		. '</div></div>'
		. '<div class="ow-bk__embed"><div class="ow-bk__embed-inner">'
		. '<div class="ow-bk__embed-head">'
		. '<span class="ow-bk__embed-tag">Live availability &middot; ' . esc_html( $o['venue'] ) . '</span>'
		. '<span class="ow-bk__embed-note">Secure booking powered by Bookeo</span>'
		. '</div>'
		. '<div class="ow-bk__widget">'
		. '<script type="text/javascript" src="https://bookeo.com/widget.js?a=' . $o['key'] . '"></script>'
		. '</div>'
		. '</div></div>'
		. '<div class="ow-bk__help"><p>Need help booking? Call <a href="tel:' . $o['phone_raw'] . '">' . esc_html( $o['phone'] ) . '</a> &middot; <a href="' . esc_url( $o['wa'] ) . '" target="_blank" rel="noopener">WhatsApp us</a></p></div>'
		. '</div>';

	$d = json_decode( get_post_meta( $pid, '_elementor_data', true ), true );
	if ( ! $d || ! isset( $d[0]['elements'][0]['settings'] ) ) {
		echo "page $pid: UNEXPECTED STRUCTURE, skipped\n";
		continue;
	}
	$d[0]['elements'][0]['settings']['html'] = $html;
	update_post_meta( $pid, '_elementor_data', wp_slash( wp_json_encode( $d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) );
	delete_post_meta( $pid, '_elementor_element_cache' );
	$updated[] = $pid;
	echo "page $pid ({$o['short']}): redesigned, key {$o['key']} kept\n";
}
echo 'done: ' . implode( ',', $updated ) . "\n";

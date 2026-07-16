<?php
/**
 * Plugin Name: Overworld — XR Party Game Modes (editable + images)
 * Description: Client-editable "Game Modes" section for the XR Party Game page (577): 6 mode slots (name, icon, hook, description, optional image) edited on the page in WP Admin, rendered by the [ow_xr_modes] shortcode. Empty name hides a slot; empty everything falls back to the built-in modes.
 * Author: Overworld
 * Version: 1.0.0
 *
 * Must-use plugin. Same conventions as the outlet "What We Offer" cards:
 * fixed ACF slots (free ACF), accordion rows in admin, optional image with
 * a fixed 16:9 crop so uploads can never distort the layout.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_XR_PAGE_ID = 577;

function ow_xr_default_modes() {
	return array(
		array( 'icon' => '⏹️', 'name' => 'Glass Run Challenge', 'hook' => '// Escape the cracking floor', 'desc' => 'Hijacked by a cosmic dragon — <strong>escape the cracking glass panels</strong> before plunging into the abyss. Last survivor faces the dragon.' ),
		array( 'icon' => '💰', 'name' => 'Vault Heist', 'hook' => '// Dodge the spotlights', 'desc' => 'Sneak into the billionaire\'s vault. <strong>Collect coins and chests</strong> while dodging the roaming spotlights. Highest score takes the crown.' ),
		array( 'icon' => '🪨', 'name' => 'Boulder Dash', 'hook' => '// Outrun the crush', 'desc' => 'Dodge obstacles and <strong>outrun the boulders</strong>. First to finish wins the title of squad leader. Don\'t trip.' ),
		array( 'icon' => '🍅', 'name' => 'Fruit vs Zombies', 'hook' => '// Repel the invasion', 'desc' => '<strong>Throw fruit bombs</strong> from afar and don\'t let the zombies get too close. Coordinate with your squad — defense wins this one.' ),
		array( 'icon' => '🍭', 'name' => 'Candy Carnival', 'hook' => '// Catch the rain, win the fame', 'desc' => 'Candy is falling from the sky. <strong>Collect the most</strong> to be crowned the next Candy Catch King or Queen.' ),
		array( 'icon' => '🐉', 'name' => 'Dragon Clash', 'hook' => '//  Defeat the dragon', 'desc' => '<strong>Attack the dragon</strong>, survive the dragon\'s attacks, and achieve the highest score possible!' ),
	);
}

// ===== ACF fields (accordion per mode, on the XR Party Game page) =====
add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	$fields = array(
		array(
			'key'       => 'field_xr_modes_msg',
			'label'     => '',
			'name'      => '',
			'type'      => 'message',
			'message'   => 'The six cards in the "Available Game Modes" section. Click a mode row to expand it. <strong>Leave a mode\'s name empty to hide that card.</strong> Images are optional and crop safely to 16:9 on every screen size.',
			'new_lines' => '',
			'esc_html'  => 0,
		),
	);
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = array(
			'key'          => 'field_xr_mode_' . $i . '_accordion',
			'label'        => "Mode {$i}",
			'name'         => '',
			'type'         => 'accordion',
			'open'         => 0,
			'multi_expand' => 1,
			'endpoint'     => 0,
		);
		$fields[] = array(
			'key'     => 'field_xr_mode_' . $i . '_name',
			'label'   => 'Name',
			'name'    => 'xr_mode_' . $i . '_name',
			'type'    => 'text',
			'wrapper' => array( 'width' => '45' ),
		);
		$fields[] = array(
			'key'     => 'field_xr_mode_' . $i . '_icon',
			'label'   => 'Icon (emoji)',
			'name'    => 'xr_mode_' . $i . '_icon',
			'type'    => 'text',
			'wrapper' => array( 'width' => '15' ),
		);
		$fields[] = array(
			'key'          => 'field_xr_mode_' . $i . '_hook',
			'label'        => 'Hook line',
			'name'         => 'xr_mode_' . $i . '_hook',
			'type'         => 'text',
			'instructions' => 1 === $i ? 'Short teaser, e.g. "// Escape the cracking floor"' : '',
			'wrapper'      => array( 'width' => '40' ),
		);
		$fields[] = array(
			'key'   => 'field_xr_mode_' . $i . '_desc',
			'label' => 'Description',
			'name'  => 'xr_mode_' . $i . '_desc',
			'type'  => 'textarea',
			'rows'  => 2,
		);
		$fields[] = array(
			'key'           => 'field_xr_mode_' . $i . '_image',
			'label'         => 'Image (optional)',
			'name'          => 'xr_mode_' . $i . '_image',
			'type'          => 'image',
			'return_format' => 'id',
			'preview_size'  => 'medium',
			'library'       => 'all',
		);
	}
	$fields[] = array(
		'key'      => 'field_xr_mode_end',
		'label'    => '',
		'name'     => '',
		'type'     => 'accordion',
		'endpoint' => 1,
	);

	acf_add_local_field_group( array(
		'key'             => 'group_ow_xr_modes',
		'title'           => 'XR Party — Game Modes',
		'fields'          => $fields,
		'location'        => array(
			array(
				array(
					'param'    => 'page',
					'operator' => '==',
					'value'    => OW_XR_PAGE_ID,
				),
			),
		),
		'menu_order'      => 3,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
		'description'     => 'Cards for the Game Modes section. Rendered by [ow_xr_modes].',
	) );
} );

// ===== Section renderer =====
add_shortcode( 'ow_xr_modes', function () {
	$page_id = OW_XR_PAGE_ID;

	// Read ACF slots; fall back to built-in defaults if all empty.
	$modes = array();
	for ( $i = 1; $i <= 6; $i++ ) {
		$name = trim( (string) get_post_meta( $page_id, 'xr_mode_' . $i . '_name', true ) );
		if ( '' === $name ) continue;
		$img_id  = get_post_meta( $page_id, 'xr_mode_' . $i . '_image', true );
		$img_src = ( $img_id && is_numeric( $img_id ) ) ? wp_get_attachment_image_url( (int) $img_id, 'large' ) : '';
		$modes[] = array(
			'name' => $name,
			'icon' => (string) get_post_meta( $page_id, 'xr_mode_' . $i . '_icon', true ),
			'hook' => (string) get_post_meta( $page_id, 'xr_mode_' . $i . '_hook', true ),
			'desc' => (string) get_post_meta( $page_id, 'xr_mode_' . $i . '_desc', true ),
			'img'  => $img_src,
		);
	}
	if ( empty( $modes ) ) {
		foreach ( ow_xr_default_modes() as $d ) {
			$d['img'] = '';
			$modes[]  = $d;
		}
	}

	$css = <<<'CSS'

  .ow-xr-modes{
    --ow-bg:#0a0612;
    --ow-bg-2:#15101e;
    --ow-yellow:#ffd60a;
    --ow-yellow-glow:#ffed4d;
    --ow-purple:#a855f7;
    --ow-purple-glow:#c89aff;
    --ow-pink:#ff2db8;
    --ow-cyan:#22e3ff;
    --ow-orange:#ff9d00;
    --ow-green:#39ff14;
    --ow-blue:#22e3ff;
    --ow-fg:#fff;
    --ow-dim:rgba(235,225,240,.65);
    --ow-line:rgba(255,255,255,.08);
    background:linear-gradient(180deg,var(--ow-bg) 0%,#10081a 100%);
    color:var(--ow-fg);
    font-family:'Space Grotesk','Inter',system-ui,sans-serif;
    padding:120px 40px;
    position:relative;overflow:hidden;
  }
  .ow-xr-modes *{box-sizing:border-box;}

  .ow-xr-modes::before{
    content:"";position:absolute;inset:0;pointer-events:none;
    background-image:
      linear-gradient(rgba(255,214,10,.04) 1px,transparent 1px),
      linear-gradient(90deg,rgba(255,214,10,.04) 1px,transparent 1px);
    background-size:60px 60px;
    mask-image:radial-gradient(ellipse at center,black 0%,transparent 70%);
    -webkit-mask-image:radial-gradient(ellipse at center,black 0%,transparent 70%);
  }

  .ow-xr-modes__inner{max-width:1300px;margin:0 auto;position:relative;z-index:2;}

  /* Header */
  .ow-xr-modes__head{text-align:center;margin-bottom:80px;}
  .ow-xr-modes__eyebrow{
    display:inline-flex;align-items:center;gap:10px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--ow-yellow-glow);margin-bottom:18px;
  }
  .ow-xr-modes__eyebrow::before,
  .ow-xr-modes__eyebrow::after{
    content:"";width:28px;height:1px;background:var(--ow-yellow);
  }
  .ow-xr-modes__title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(40px,5vw,72px);
    line-height:1;letter-spacing:-.02em;text-transform:uppercase;
    margin:0 0 18px;font-weight:400;
  }
  .ow-xr-modes__title em{font-style:normal;color:var(--ow-yellow);}
  .ow-xr-modes__lede{
    font-size:17px;line-height:1.6;color:var(--ow-dim);
    margin:0 auto;max-width:580px;
  }

  /* Grid: 3 top + 2 bottom centered */
  .ow-xr-modes__row{
    display:grid;gap:24px;margin-bottom:24px;
  }
  .ow-xr-modes__row--3{
    grid-template-columns:repeat(3,1fr);
  }
  .ow-xr-modes__row--2{
    grid-template-columns:repeat(2,1fr);
    max-width:870px;margin-left:auto;margin-right:auto;
    margin-bottom:0;
  }

  /* Mode card */
  .ow-xr-modes__card{
    position:relative;
    border:1px solid var(--ow-line);
    border-radius:20px;
    padding:36px 30px 32px;
    background:var(--ow-bg-2);
    transition:transform .35s ease, border-color .25s ease, box-shadow .35s ease;
    overflow:hidden;
    display:flex;flex-direction:column;gap:0;
  }
  .ow-xr-modes__card::before{
    content:"";position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(to right,transparent,var(--accent,var(--ow-yellow)),transparent);
  }
  /* Subtle bg glow */
  .ow-xr-modes__card::after{
    content:"";position:absolute;top:-40%;right:-40%;
    width:240px;height:240px;border-radius:50%;
    background:radial-gradient(circle,
      color-mix(in srgb,var(--accent,var(--ow-yellow)) 18%,transparent),
      transparent 70%);
    filter:blur(40px);pointer-events:none;
  }
  .ow-xr-modes__card:hover{
    transform:translateY(-6px);
    border-color:var(--accent,var(--ow-yellow));
    box-shadow:0 20px 60px -20px var(--accent,var(--ow-yellow));
  }

  /* Color assignments per card */
  .ow-xr-modes__card--01{--accent:var(--ow-yellow);}
  .ow-xr-modes__card--02{--accent:var(--ow-purple);}
  .ow-xr-modes__card--03{--accent:var(--ow-orange);}
  .ow-xr-modes__card--04{--accent:var(--ow-green);}
  .ow-xr-modes__card--05{--accent:var(--ow-pink);}
  .ow-xr-modes__card--06{--accent:var(--ow-blue);}

  .ow-xr-modes__num{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.18em;text-transform:uppercase;
    color:var(--accent);margin-bottom:18px;
    display:flex;align-items:center;gap:10px;
    position:relative;z-index:1;
  }
  .ow-xr-modes__num::before{
    content:"";width:6px;height:6px;border-radius:50%;background:var(--accent);
    box-shadow:0 0 12px var(--accent);
  }
  .ow-xr-modes__icon{
    width:60px;height:60px;border-radius:16px;
    background:linear-gradient(135deg,
      color-mix(in srgb,var(--accent) 20%,transparent),
      color-mix(in srgb,var(--accent) 5%,transparent));
    border:1px solid color-mix(in srgb,var(--accent) 35%,transparent);
    display:flex;align-items:center;justify-content:center;
    font-size:30px;line-height:1;
    margin-bottom:22px;
    box-shadow:0 0 20px -4px color-mix(in srgb,var(--accent) 60%,transparent);
    position:relative;z-index:1;
  }
  .ow-xr-modes__name{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:28px;line-height:1;font-weight:400;
    text-transform:uppercase;color:#fff;
    margin:0 0 12px;letter-spacing:.005em;
    position:relative;z-index:1;
  }
  .ow-xr-modes__hook{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--accent);font-weight:600;
    margin-bottom:14px;
    position:relative;z-index:1;
  }
  .ow-xr-modes__desc{
    font-size:13.5px;line-height:1.6;color:var(--ow-dim);
    margin:0;position:relative;z-index:1;
  }
  .ow-xr-modes__desc strong{color:var(--ow-fg);font-weight:600;}

  /* Bottom note: "More modes coming" */
  .ow-xr-modes__note{
    margin-top:50px;
    padding:18px 28px;
    background:linear-gradient(135deg,
      rgba(255,214,10,.06) 0%,
      rgba(168,85,247,.06) 100%);
    border:1px solid rgba(255,214,10,.2);
    border-radius:14px;
    text-align:center;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--ow-dim);
    line-height:1.6;
    max-width:680px;margin-left:auto;margin-right:auto;
  }
  .ow-xr-modes__note strong{color:var(--ow-yellow-glow);font-weight:700;}

  /* Responsive */
  @media (max-width:1100px){
    .ow-xr-modes{padding:90px 28px;}
    .ow-xr-modes__row--3{grid-template-columns:repeat(2,1fr);}
    /* When 3-col becomes 2-col, the 3rd card spans 2 cols */
    .ow-xr-modes__row--3 .ow-xr-modes__card:nth-child(3){
      grid-column:1 / -1;max-width:600px;margin:0 auto;width:100%;
    }
  }
  @media (max-width:700px){
    .ow-xr-modes{padding:80px 22px;}
    .ow-xr-modes__title{font-size:42px;}
    .ow-xr-modes__row--3,.ow-xr-modes__row--2{grid-template-columns:1fr;gap:18px;}
    .ow-xr-modes__row--3 .ow-xr-modes__card:nth-child(3){grid-column:auto;max-width:none;}
    .ow-xr-modes__card{padding:30px 26px;}
    .ow-xr-modes__name{font-size:24px;}
  }
  @media (max-width:480px){
    .ow-xr-modes{padding:70px 18px;}
    .ow-xr-modes__title{font-size:36px;}
    .ow-xr-modes__icon{width:54px;height:54px;font-size:26px;margin-bottom:18px;}
  }

  /* Optional mode image: full-bleed top of card, fixed 16:9 crop */
  .ow-xr-modes__img{
    position:relative;aspect-ratio:16/9;
    margin:-36px -30px 24px;
    overflow:hidden;
    border-bottom:1px solid var(--ow-line);
    background:rgba(255,255,255,.02);
  }
  .ow-xr-modes__img img{
    width:100% !important;height:100% !important;
    object-fit:cover !important;object-position:center !important;
    display:block !important;
    position:absolute !important;top:0 !important;left:0 !important;
    max-width:none !important;max-height:none !important;
    transition:transform .4s ease;
  }
  .ow-xr-modes__card:hover .ow-xr-modes__img img{transform:scale(1.05);}

CSS;

	$allowed = array( 'strong' => array(), 'em' => array(), 'br' => array() );

	// Cards markup, chunked into rows of 3 (last row of 2 gets the centered style)
	$rows  = array_chunk( $modes, 3 );
	$body  = '';
	$n     = 0;
	foreach ( $rows as $row ) {
		$row_class = count( $row ) === 2 ? 'ow-xr-modes__row ow-xr-modes__row--2' : 'ow-xr-modes__row ow-xr-modes__row--3';
		$body     .= '<div class="' . $row_class . '">';
		foreach ( $row as $mode ) {
			$n++;
			$num   = str_pad( (string) $n, 2, '0', STR_PAD_LEFT );
			$body .= '<article class="ow-xr-modes__card ow-xr-modes__card--' . $num . '">';
			if ( ! empty( $mode['img'] ) ) {
				$body .= '<div class="ow-xr-modes__img"><img src="' . esc_url( $mode['img'] ) . '" alt="' . esc_attr( $mode['name'] . ' — XR Party Game mode at Overworld Funan' ) . '" loading="lazy" /></div>';
			}
			$body .= '<div class="ow-xr-modes__num">Mode ' . $num . '</div>';
			if ( ! empty( $mode['icon'] ) ) {
				$body .= '<div class="ow-xr-modes__icon">' . esc_html( $mode['icon'] ) . '</div>';
			}
			$body .= '<h3 class="ow-xr-modes__name">' . esc_html( $mode['name'] ) . '</h3>';
			if ( ! empty( $mode['hook'] ) ) {
				$body .= '<div class="ow-xr-modes__hook">' . esc_html( $mode['hook'] ) . '</div>';
			}
			$body .= '<p class="ow-xr-modes__desc">' . wp_kses( $mode['desc'], $allowed ) . '</p>';
			$body .= '</article>';
		}
		$body .= '</div>';
	}

	$count = count( $modes );

	return '<!-- OVERWORLD :: XR PARTY GAME :: GAME MODES :: SECTION 03 (dynamic) -->'
		. '<style>' . $css . '</style>'
		. '<section class="ow-xr-modes" id="game-modes"><div class="ow-xr-modes__inner">'
		. '<div class="ow-xr-modes__head">'
		. '<div class="ow-xr-modes__eyebrow">Available Game Modes</div>'
		. '<h2 class="ow-xr-modes__title">' . esc_html( $count ) . ' Modes. <em>One Carnival.</em></h2>'
		. '<p class="ow-xr-modes__lede">Every mode brings a new challenge. Run, jump, throw, collect — stack the highest score and crown your party champion.</p>'
		. '</div>'
		. $body
		. '</div></section>';
} );

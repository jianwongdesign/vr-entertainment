<?php
/**
 * Plugin Name: Overworld — Dynamic Experience Grid
 * Description: [ow_experience_grid term="vr-roam"] renders a live games grid from the Experience CPT (ordered like the library archives: exp_display_order, then A-Z). First 8 cards visible, "View More" expands in-page, "Browse All" links to the archive. New games published to the term appear automatically — no page regeneration needed.
 * Author: Overworld
 * Version: 1.0.0
 *
 * Images use a fixed 16:9 cover-crop, so small or oddly-sized uploads always
 * fill the card edge-to-edge without distortion.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ow_experience_grid_config() {
	return array(
		'vr-roam' => array(
			'accent'      => '#00ff88',
			'accent_glow' => '#39ffaa',
			'dark'        => '#020609',
			'bg'          => '#060d08',
			'bg2'         => '#0d1810',
			'eyebrow'     => 'Games Library',
			'title'       => 'Worlds To Explore',
			'lede'        => 'Pick your game on arrival — or let our Game Masters recommend one. New titles added regularly.',
			'unit'        => 'Game',
		),
		'vr-arcade' => array(
			'accent'      => '#ff5722',
			'accent_glow' => '#ff8a3d',
			'dark'        => '#0a0a14',
			'bg'          => '#0a0a14',
			'bg2'         => '#101020',
			'eyebrow'     => 'Games Library',
			'title'       => 'Worlds On Tap',
			'lede'        => 'Swap between any of these titles during your session — or let our crew recommend one for your group.',
			'unit'        => 'Game',
		),
		'vr-escape' => array(
			'accent'      => '#a855f7',
			'accent_glow' => '#c89aff',
			'dark'        => '#0a081a',
			'bg'          => '#0a081a',
			'bg2'         => '#110d24',
			'eyebrow'     => 'Room Library',
			'title'       => 'Pick Your Escape',
			'lede'        => 'Haunted mansions, ancient tombs, deep space and more — every room is a different story to survive.',
			'unit'        => 'Room',
		),
	);
}

add_shortcode( 'ow_experience_grid', function ( $atts ) {
	$atts    = shortcode_atts( array( 'term' => 'vr-roam', 'visible' => 8 ), $atts );
	$term    = sanitize_key( $atts['term'] );
	$visible = max( 1, (int) $atts['visible'] );
	$configs = ow_experience_grid_config();
	if ( ! isset( $configs[ $term ] ) ) {
		return '';
	}
	$c = $configs[ $term ];

	// Live query — mirrors the library archive ordering.
	$posts = get_posts( array(
		'post_type'      => 'experience',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'tax_query'      => array(
			array(
				'taxonomy' => 'experience_type',
				'field'    => 'slug',
				'terms'    => $term,
			),
		),
	) );
	if ( empty( $posts ) ) {
		return '';
	}
	$games = array();
	foreach ( $posts as $p ) {
		$ord     = get_post_meta( $p->ID, 'exp_display_order', true );
		$games[] = array(
			'title' => $p->post_title,
			'ord'   => ( '' === $ord ) ? 9999 : (int) $ord,
			'url'   => get_permalink( $p->ID ),
			'img'   => get_the_post_thumbnail_url( $p->ID, 'large' ),
		);
	}
	usort( $games, function ( $a, $b ) {
		return $a['ord'] <=> $b['ord'] ?: strcasecmp( $a['title'], $b['title'] );
	} );

	$term_obj    = get_term_by( 'slug', $term, 'experience_type' );
	$archive_url = ( $term_obj && ! is_wp_error( $term_obj ) ) ? get_term_link( $term_obj ) : home_url( '/experience-type/' . $term . '/' );
	$n           = count( $games );
	$uid         = 'exg-' . $term;

	$cards = '';
	foreach ( $games as $i => $g ) {
		$hidden = $i >= $visible ? ' is-hidden' : '';
		$name   = esc_html( $g['title'] );
		$img    = $g['img']
			? '<img src="' . esc_url( $g['img'] ) . '" alt="' . esc_attr( $g['title'] . ' — ' . $c['eyebrow'] . ' at Overworld Singapore' ) . '" loading="lazy" />'
			: '<div class="ow-exg__card-noimg">🎮</div>';
		$cards .= '<div class="ow-exg__card' . $hidden . '">'
			. '<div class="ow-exg__card-img">' . $img . '</div>'
			. '<div class="ow-exg__card-body">'
			. '<h3 class="ow-exg__card-name">' . $name . '</h3>'
			. '<a class="ow-exg__card-link" href="' . esc_url( $g['url'] ) . '">Learn More →</a>'
			. '</div></div>';
	}

	$css = '
  .ow-exg{
    --accent:' . $c['accent'] . ';
    --accent-glow:' . $c['accent_glow'] . ';
    --dark:' . $c['dark'] . ';
    --fg:#fff;
    --dim:rgba(220,225,240,.65);
    --bg:' . $c['bg'] . ';
    --bg-2:' . $c['bg2'] . ';
    --line:rgba(255,255,255,.08);
    background:linear-gradient(180deg,var(--bg) 0%,var(--bg-2) 140%);
    color:var(--fg);
    font-family:\'Space Grotesk\',\'Inter\',system-ui,sans-serif;
    padding:100px 40px;
    position:relative;overflow:hidden;
  }
  .ow-exg *{box-sizing:border-box;}
  .ow-exg__inner{max-width:1300px;margin:0 auto;position:relative;z-index:2;}
  .ow-exg__head{text-align:center;margin-bottom:48px;}
  .ow-exg__eyebrow{
    display:inline-flex;align-items:center;gap:10px;
    font-family:\'JetBrains Mono\',monospace;
    font-size:12px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--accent-glow);margin-bottom:18px;
  }
  .ow-exg__eyebrow::before,.ow-exg__eyebrow::after{content:"";width:28px;height:1px;background:var(--accent);}
  .ow-exg__title{
    font-family:\'Anton\',\'Bebas Neue\',sans-serif;
    font-size:clamp(40px,5vw,72px);
    line-height:1;font-weight:400;text-transform:uppercase;
    margin:0 0 16px;
    background:linear-gradient(180deg,#fff 0%,var(--accent-glow) 130%);
    -webkit-background-clip:text;background-clip:text;
    -webkit-text-fill-color:transparent;
  }
  .ow-exg__lede{font-size:16px;color:var(--dim);line-height:1.6;margin:0 auto;max-width:520px;}
  .ow-exg__count{
    text-align:center;
    font-family:\'JetBrains Mono\',monospace;
    font-size:11px;letter-spacing:.18em;text-transform:uppercase;
    color:var(--dim);margin-bottom:36px;
  }
  .ow-exg__count strong{color:var(--accent-glow);font-weight:700;}
  .ow-exg__grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;}
  .ow-exg__card{
    background:var(--bg-2);
    border:1px solid var(--line);
    border-radius:18px;overflow:hidden;
    display:flex;flex-direction:column;
    transition:transform .3s ease, border-color .25s ease, box-shadow .3s ease;
  }
  .ow-exg__card:hover{
    transform:translateY(-5px);
    border-color:var(--accent);
    box-shadow:0 18px 50px -18px var(--accent);
  }
  .ow-exg__card.is-hidden{display:none;}
  /* Fixed 16:9 cover-crop: small uploads scale up to fill the border */
  .ow-exg__card-img{
    position:relative;aspect-ratio:16/9;
    background:rgba(255,255,255,.02);overflow:hidden;
  }
  .ow-exg__card-img img{
    width:100% !important;height:100% !important;
    object-fit:cover !important;object-position:center !important;
    display:block !important;
    position:absolute !important;top:0 !important;left:0 !important;
    max-width:none !important;max-height:none !important;
    transition:transform .4s ease;
  }
  .ow-exg__card:hover .ow-exg__card-img img{transform:scale(1.06);}
  .ow-exg__card-noimg{
    position:absolute;inset:0;
    display:flex;align-items:center;justify-content:center;
    font-size:36px;opacity:.35;color:var(--dim);
  }
  .ow-exg__card-body{
    padding:16px 18px 18px;
    flex:1;display:flex;flex-direction:column;gap:12px;
  }
  .ow-exg__card-name{
    font-family:\'Anton\',\'Bebas Neue\',sans-serif;
    font-size:19px;line-height:1.15;font-weight:400;
    text-transform:uppercase;color:#fff;margin:0;letter-spacing:-.005em;
    flex:1;
  }
  .ow-exg__card-link{
    display:inline-flex;align-items:center;justify-content:center;gap:8px;
    padding:11px 16px;border-radius:999px;
    font-family:\'JetBrains Mono\',monospace;
    font-size:11px;letter-spacing:.12em;text-transform:uppercase;
    text-decoration:none;font-weight:700;
    background:rgba(255,255,255,.04);color:#fff;
    border:1px solid var(--line);
    transition:background .25s ease, color .25s ease, border-color .25s ease;
  }
  .ow-exg__card-link:hover{background:var(--accent);color:var(--dark);border-color:var(--accent);}
  .ow-exg__actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:44px;}
  .ow-exg__btn{
    display:inline-flex;align-items:center;gap:10px;
    padding:16px 28px;border-radius:999px;
    font-family:\'JetBrains Mono\',monospace;
    font-size:12px;letter-spacing:.14em;text-transform:uppercase;
    text-decoration:none;font-weight:700;cursor:pointer;
    transition:transform .25s ease, gap .25s ease, border-color .25s ease, color .25s ease;
  }
  .ow-exg__btn--more{
    background:var(--accent);color:var(--dark);
    border:1px solid var(--accent);
    box-shadow:0 12px 34px -10px var(--accent);
  }
  .ow-exg__btn--more:hover{transform:translateY(-2px);gap:14px;}
  .ow-exg__btn--more.is-hidden{display:none;}
  .ow-exg__btn--browse{
    background:rgba(255,255,255,.04);color:#fff;
    border:1px solid var(--line);
  }
  .ow-exg__btn--browse:hover{border-color:var(--accent);color:var(--accent-glow);transform:translateY(-2px);gap:14px;}
  @media (max-width:1000px){
    .ow-exg{padding:80px 28px;}
    .ow-exg__grid{grid-template-columns:repeat(2,1fr);gap:14px;}
  }
  @media (max-width:600px){
    .ow-exg{padding:64px 18px;}
    .ow-exg__grid{grid-template-columns:1fr;}
    .ow-exg__actions{flex-direction:column;align-items:stretch;}
    .ow-exg__btn{justify-content:center;}
  }';

	$more_btn = $n > $visible
		? '<button class="ow-exg__btn ow-exg__btn--more" id="' . esc_attr( $uid ) . '-more" type="button">View More ' . esc_html( $c['unit'] ) . 's ↓</button>'
		: '';

	return '<!-- OVERWORLD :: DYNAMIC EXPERIENCE GRID :: ' . esc_html( $term ) . ' -->'
		. '<style>' . $css . '</style>'
		. '<section class="ow-exg" id="games"><div class="ow-exg__inner">'
		. '<div class="ow-exg__head">'
		. '<div class="ow-exg__eyebrow">' . esc_html( $c['eyebrow'] ) . '</div>'
		. '<h2 class="ow-exg__title">' . esc_html( $n ) . ' ' . esc_html( $c['title'] ) . '</h2>'
		. '<p class="ow-exg__lede">' . esc_html( $c['lede'] ) . '</p>'
		. '</div>'
		. '<div class="ow-exg__count"><strong>' . esc_html( $n ) . '</strong> ' . esc_html( $c['unit'] ) . 's Available</div>'
		. '<div class="ow-exg__grid" id="' . esc_attr( $uid ) . '-grid">' . $cards . '</div>'
		. '<div class="ow-exg__actions">'
		. $more_btn
		. '<a class="ow-exg__btn ow-exg__btn--browse" href="' . esc_url( $archive_url ) . '">Browse All ' . esc_html( $c['unit'] ) . 's →</a>'
		. '</div>'
		. '</div></section>'
		. '<script>(function(){var b=document.getElementById("' . esc_js( $uid ) . '-more");if(!b)return;b.addEventListener("click",function(){document.querySelectorAll("#' . esc_js( $uid ) . '-grid .ow-exg__card.is-hidden").forEach(function(c){c.classList.remove("is-hidden");});b.classList.add("is-hidden");});})();</script>';
} );

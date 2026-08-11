<?php
/**
 * Plugin Name: Overworld — Colour-Coded Admin UI
 * Description: Colour-codes wp-admin so the client can tell at a glance which section they are in, and splits the left menu into two zones: "Website Content" (everything the client edits, each with its own colour) on top, "Web Admin Tools" (everything technical) below a labelled divider.
 * Author: Overworld
 * Version: 2.0.0
 *
 * Must-use plugin: auto-loads, no activation needed.
 *
 * Replaces the WPCode snippet "Overworld :: Color-Coded Admin UI" (v3), which
 * lived only in the database. Same colours and same visual language, plus:
 *   - Popups, Pages, Media and Posts added to the colour map
 *   - the left menu reordered into the two labelled zones
 *
 * The colours match the outlet brand palette (Kallang orange, Orchard cyan,
 * Funan purple) so the admin reads like the site.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =====================================================================
   1. The map
   ---------------------------------------------------------------------
   One entry per thing the client owns. `menu_id` is the <li> id WordPress
   gives the item in the sidebar; `slug` is the menu slug used for ordering.
   `acf_key` is optional — only the five CPTs with database field groups
   have one, so their rows on the Field Groups screen get colour-coded too.
   ===================================================================== */

function ow_admin_color_map() {
	return array(

		'page' => array(
			'menu_id' => 'menu-pages',
			'slug'    => 'edit.php?post_type=page',
			'color'   => '#3b82f6',
			'tint'    => 'rgba(59,130,246,.10)',
			'hover'   => 'rgba(59,130,246,.18)',
			'icon'    => '📄',
			'desc'    => 'Every page on the site — home, outlets, events, contact. Most are built in Elementor.',
		),

		'experience' => array(
			'menu_id' => 'menu-posts-experience',
			'slug'    => 'edit.php?post_type=experience',
			'acf_key' => 'group_69f1a8e4825d4',
			'color'   => '#22e3ff',
			'tint'    => 'rgba(34,227,255,.10)',
			'hover'   => 'rgba(34,227,255,.18)',
			'icon'    => '🎮',
			'desc'    => 'Game experience pages — VR Arcade, Floor Is Lava, Tap Tap, Laser Maze, XR Party, etc.',
		),

		'event_package' => array(
			'menu_id' => 'menu-posts-event_package',
			'slug'    => 'edit.php?post_type=event_package',
			'acf_key' => 'group_overworld_event_package',
			'color'   => '#a855f7',
			'tint'    => 'rgba(168,85,247,.10)',
			'hover'   => 'rgba(168,85,247,.18)',
			'icon'    => '🎂',
			'desc'    => 'Team Building & Birthday Party packages — per outlet, with PDF brochures.',
		),

		'pricing_item' => array(
			'menu_id' => 'menu-posts-pricing_item',
			'slug'    => 'edit.php?post_type=pricing_item',
			'acf_key' => 'group_6a07f1d49d540',
			'color'   => '#ff5722',
			'tint'    => 'rgba(255,87,34,.10)',
			'hover'   => 'rgba(255,87,34,.18)',
			'icon'    => '💰',
			'desc'    => 'Per-outlet pricing items (Kallang / Orchard / Funan) grouped by activity.',
		),

		'faq' => array(
			'menu_id' => 'menu-posts-faq',
			'slug'    => 'edit.php?post_type=faq',
			'acf_key' => 'group_69f4a8eac6168',
			'color'   => '#fbbf24',
			'tint'    => 'rgba(251,191,36,.10)',
			'hover'   => 'rgba(251,191,36,.18)',
			'icon'    => '❓',
			'desc'    => 'Frequently asked questions shown on the FAQ page, grouped by category.',
		),

		'promo' => array(
			'menu_id' => 'menu-posts-promo',
			'slug'    => 'edit.php?post_type=promo',
			'acf_key' => 'group_69f4292d437ca',
			'color'   => '#ec4899',
			'tint'    => 'rgba(236,72,153,.10)',
			'hover'   => 'rgba(236,72,153,.18)',
			'icon'    => '🎉',
			'desc'    => 'Time-bound promotions and special offers — auto-expire by date.',
		),

		'ow_popup' => array(
			'menu_id' => 'menu-posts-ow_popup',
			'slug'    => 'edit.php?post_type=ow_popup',
			'color'   => '#22c55e',
			'tint'    => 'rgba(34,197,94,.10)',
			'hover'   => 'rgba(34,197,94,.18)',
			'icon'    => '📢',
			'desc'    => 'Homepage popups — poster, dates, button. Switch one on and it shows to visitors.',
		),

		'attachment' => array(
			'menu_id' => 'menu-media',
			'slug'    => 'upload.php',
			'color'   => '#94a3b8',
			'tint'    => 'rgba(148,163,184,.10)',
			'hover'   => 'rgba(148,163,184,.18)',
			'icon'    => '🖼️',
			'desc'    => 'Every image, video and PDF uploaded to the site.',
		),

		'post' => array(
			'menu_id' => 'menu-posts',
			'slug'    => 'edit.php',
			'color'   => '#94a3b8',
			'tint'    => 'rgba(148,163,184,.10)',
			'hover'   => 'rgba(148,163,184,.18)',
			'icon'    => '📝',
			'desc'    => 'Blog articles.',
		),

	);
}

/* =====================================================================
   2. Menu order — two zones
   ---------------------------------------------------------------------
   Everything in the colour map goes up top under "Website Content", in the
   order below. Everything else falls under "Web Admin Tools" keeping its
   default relative order, so a newly installed plugin lands in the admin
   zone on its own without this file needing to know about it.
   ===================================================================== */

function ow_admin_client_menu_slugs() {
	$order = array();
	foreach ( ow_admin_color_map() as $m ) {
		$order[] = $m['slug'];
	}
	return $order;
}

/**
 * Drop the stock separators and add our two labelled ones.
 *
 * Core deletes the second of any two adjacent separators, so leaving the
 * stock ones in place could silently eat ours. Priority 9999: every plugin
 * has registered its menu by then.
 */
add_action(
	'admin_menu',
	function () {
		global $menu;

		foreach ( $menu as $key => $item ) {
			if ( isset( $item[4] ) && false !== strpos( $item[4], 'wp-menu-separator' ) ) {
				unset( $menu[ $key ] );
			}
		}

		// Slugs must start with "separator" — core keys its separator
		// handling off that prefix, not off the class alone.
		$menu[] = array( '', 'read', 'separator-ow-client', '', 'wp-menu-separator ow-sep ow-sep--client' );
		$menu[] = array( '', 'read', 'separator-ow-admin', '', 'wp-menu-separator ow-sep ow-sep--admin' );
	},
	9999
);

add_filter( 'custom_menu_order', '__return_true' );

add_filter(
	'menu_order',
	function ( $order ) {
		$top = array_merge(
			array( 'index.php', 'separator-ow-client' ),
			ow_admin_client_menu_slugs(),
			array( 'separator-ow-admin' )
		);

		// Only the ones this install actually has, then everything else
		// untouched and in its original order.
		$top  = array_values( array_intersect( $top, $order ) );
		$rest = array_values( array_diff( $order, $top ) );

		return array_merge( $top, $rest );
	},
	// Late: Site Kit (and others) also filter menu_order to hoist themselves
	// next to the Dashboard. Ours runs last so the two zones hold.
	9999
);

/* =====================================================================
   3. Styles
   ===================================================================== */

add_action( 'admin_head', 'ow_admin_inject_styles' );

function ow_admin_inject_styles() {
	$map         = ow_admin_color_map();
	$screen      = get_current_screen();
	$current_cpt = $screen ? $screen->post_type : '';

	echo '<style id="ow-admin-color-ui">';

	// ===== Zone dividers =====
	echo '
	#adminmenu li.ow-sep {
		height: auto !important;
		margin: 16px 0 2px !important;
		padding: 0 !important;
		background: transparent !important;
	}
	#adminmenu li.ow-sep > .separator {
		margin: 0 0 7px !important;
	}
	#adminmenu li.ow-sep::after {
		display: block;
		padding: 0 0 4px 13px;
		font-size: 10px;
		font-weight: 600;
		letter-spacing: .09em;
		text-transform: uppercase;
		color: #8f98a1;
	}
	#adminmenu li.ow-sep--client::after { content: "Website Content"; }
	#adminmenu li.ow-sep--admin::after  { content: "Web Admin Tools"; }

	/* Collapsed sidebar has no room for a label. */
	.folded #adminmenu li.ow-sep::after { content: "" !important; padding: 0 !important; }
	@media only screen and (max-width: 960px) {
		.auto-fold #adminmenu li.ow-sep::after { content: "" !important; padding: 0 !important; }
	}
	@media screen and (max-width: 782px) {
		.auto-fold #adminmenu li.ow-sep--client::after { content: "Website Content" !important; padding: 0 0 4px 13px !important; }
		.auto-fold #adminmenu li.ow-sep--admin::after  { content: "Web Admin Tools" !important; padding: 0 0 4px 13px !important; }
	}
	';

	// ===== Sidebar menu — always visible across all admin pages =====
	foreach ( $map as $m ) {
		$id = '#' . $m['menu_id'];
		echo '
		#adminmenu ' . esc_attr( $id ) . ' > a.menu-top {
			border-left: 4px solid ' . esc_attr( $m['color'] ) . ' !important;
			padding-left: 8px !important;
		}
		#adminmenu ' . esc_attr( $id ) . ' > a.menu-top .wp-menu-image::before,
		#adminmenu ' . esc_attr( $id ) . ' > a.menu-top:hover .wp-menu-image::before {
			color: ' . esc_attr( $m['color'] ) . ' !important;
		}
		#adminmenu ' . esc_attr( $id ) . '.wp-has-current-submenu > a.menu-top,
		#adminmenu ' . esc_attr( $id ) . '.current > a.menu-top,
		#adminmenu ' . esc_attr( $id ) . ' > a.menu-top.wp-has-current-submenu {
			background-color: ' . esc_attr( $m['hover'] ) . ' !important;
			color: #fff !important;
		}
		';
	}

	// ===== List screens and edit screens =====
	$key = $current_cpt;

	if ( $key && isset( $map[ $key ] ) && $screen && in_array( $screen->base, array( 'edit', 'post', 'upload' ), true ) ) {
		$m = $map[ $key ];

		// The media library list has no post-type body class of its own.
		$body = ( 'upload' === $screen->base ) ? 'body.upload-php' : 'body.post-type-' . $key;

		echo '
		/* Page title accent */
		' . esc_attr( $body ) . ' .wrap > h1.wp-heading-inline {
			border-left: 5px solid ' . esc_attr( $m['color'] ) . ';
			padding-left: 14px;
			display: inline-block;
		}
		/* Add New button */
		' . esc_attr( $body ) . ' .wrap > .page-title-action {
			background-color: ' . esc_attr( $m['color'] ) . ' !important;
			border-color: ' . esc_attr( $m['color'] ) . ' !important;
			color: #fff !important;
			text-shadow: none !important;
			box-shadow: none !important;
		}
		' . esc_attr( $body ) . ' .wrap > .page-title-action:hover { opacity: .9; }
		/* Row hover tint */
		' . esc_attr( $body ) . ' .wp-list-table tbody tr:hover {
			background-color: ' . esc_attr( $m['tint'] ) . ' !important;
		}
		/* Publish button + focused title on the edit screen */
		' . esc_attr( $body ) . ' #publishing-action .button-primary {
			background-color: ' . esc_attr( $m['color'] ) . ' !important;
			border-color: ' . esc_attr( $m['color'] ) . ' !important;
			color: #fff !important;
			text-shadow: none !important;
			box-shadow: none !important;
		}
		' . esc_attr( $body ) . ' #titlediv #title:focus {
			border-color: ' . esc_attr( $m['color'] ) . ' !important;
			box-shadow: 0 0 0 1px ' . esc_attr( $m['color'] ) . ' !important;
		}
		/* Bar across the top of the page */
		' . esc_attr( $body ) . ' #wpbody-content::before {
			content: "";
			display: block;
			height: 3px;
			background: linear-gradient(to right, ' . esc_attr( $m['color'] ) . ', transparent);
			margin: -10px -20px 20px;
		}
		';
	}

	// ===== ACF Field Groups list =====
	if ( 'acf-field-group' === $current_cpt && $screen && 'edit' === $screen->base ) {
		echo '
		.wp-list-table tbody tr.ow-acf-row {
			border-left: 4px solid transparent !important;
			transition: background-color .25s ease;
		}
		.ow-acf-icon {
			display: inline-block; margin-right: 10px;
			font-size: 18px; vertical-align: -3px;
		}
		.ow-acf-desc {
			color: #2c3338; font-size: 13px; line-height: 1.45;
			display: block; max-width: 480px;
		}
		.wp-list-table .column-description { width: 30%; }
		';

		foreach ( $map as $m ) {
			if ( empty( $m['acf_key'] ) ) {
				continue;
			}
			$class = ow_admin_acf_row_class( $m['acf_key'] );
			echo '
			.wp-list-table tbody tr.' . esc_attr( $class ) . ' {
				border-left-color: ' . esc_attr( $m['color'] ) . ' !important;
				background-color: ' . esc_attr( $m['tint'] ) . ' !important;
			}
			.wp-list-table tbody tr.' . esc_attr( $class ) . ':hover {
				background-color: ' . esc_attr( $m['hover'] ) . ' !important;
			}
			';
		}
	}

	echo '</style>';

	// ===== Icons + descriptions on the ACF Field Groups list =====
	if ( 'acf-field-group' === $current_cpt && $screen && 'edit' === $screen->base ) {
		$js_map = array();
		foreach ( $map as $m ) {
			if ( empty( $m['acf_key'] ) ) {
				continue;
			}
			$js_map[] = array(
				'cls'  => ow_admin_acf_row_class( $m['acf_key'] ),
				'icon' => $m['icon'],
				'desc' => $m['desc'],
			);
		}

		echo '<script>';
		echo 'window.owAcfMap = ' . wp_json_encode( $js_map ) . ';';
		echo '
		jQuery(function($) {
			if (!window.owAcfMap) return;
			$(".wp-list-table tbody tr.ow-acf-row").each(function() {
				var $row = $(this), found = null;
				window.owAcfMap.forEach(function(m) { if ($row.hasClass(m.cls)) found = m; });
				if (!found) return;

				var $title = $row.find(".row-title").first();
				if ($title.length && !$title.prev(".ow-acf-icon").length) {
					$title.before("<span class=\"ow-acf-icon\">" + found.icon + "</span>");
				}
				var $desc = $row.find(".column-description").first();
				if ($desc.length) {
					var existing = $desc.text().trim();
					if (existing === "" || existing === "—" || existing === "–" || existing === "-") {
						$desc.html("<span class=\"ow-acf-desc\">" + found.desc + "</span>");
					}
				}
			});
		});
		';
		echo '</script>';
	}
}

/* =====================================================================
   4. ACF Field Groups list — row classes
   ===================================================================== */

function ow_admin_acf_row_class( $acf_key ) {
	return 'ow-acf-' . preg_replace( '/[^a-z0-9_]/i', '', str_replace( 'group_', '', $acf_key ) );
}

add_filter( 'post_class', 'ow_acf_row_classes', 10, 3 );

function ow_acf_row_classes( $classes, $class, $post_id ) {
	if ( get_post_type( $post_id ) !== 'acf-field-group' ) {
		return $classes;
	}
	if ( ! function_exists( 'acf_get_field_group' ) ) {
		return $classes;
	}

	$fg = acf_get_field_group( $post_id );
	if ( ! $fg || empty( $fg['key'] ) ) {
		return $classes;
	}

	foreach ( ow_admin_color_map() as $m ) {
		if ( ! empty( $m['acf_key'] ) && $m['acf_key'] === $fg['key'] ) {
			$classes[] = 'ow-acf-row';
			$classes[] = ow_admin_acf_row_class( $fg['key'] );
			break;
		}
	}
	return $classes;
}

<?php
/**
 * Plugin Name: Overworld — Outlet "What We Offer" (intro + activity cards)
 * Description: Client-editable intro text and up to 6 activity cards (title, description, link, emoji icon, optional image) for the outlet pages. Consumed by page-pricing.php. Leave a card's title empty to hide that slot; leave all empty to fall back to the theme's built-in defaults.
 * Author: Overworld
 * Version: 1.1.0
 *
 * Must-use plugin: auto-loads, no activation needed. Free ACF has no
 * repeater, so this mirrors the outlet_gallery_* convention: fixed slots.
 * v1.1.0: cards reorganised into collapsible accordions with aligned field
 * widths so the metabox is easy to scan.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', function () {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$fields = array(
		array(
			'key'          => 'field_outlet_intro',
			'label'        => 'Section Intro',
			'name'         => 'outlet_intro',
			'type'         => 'textarea',
			'rows'         => 3,
			'instructions' => 'Short paragraph under the "What We Offer" heading. Leave empty to use the built-in default for this outlet.',
			'required'     => 0,
		),
	);

	for ( $i = 1; $i <= 6; $i++ ) {
		// Collapsible header per card
		$fields[] = array(
			'key'          => 'field_outlet_act_' . $i . '_accordion',
			'label'        => "Card {$i}",
			'name'         => '',
			'type'         => 'accordion',
			'open'         => 0,
			'multi_expand' => 1,
			'endpoint'     => 0,
			'instructions' => '',
		);
		$fields[] = array(
			'key'          => 'field_outlet_act_' . $i . '_title',
			'label'        => 'Title',
			'name'         => 'outlet_act_' . $i . '_title',
			'type'         => 'text',
			'instructions' => 'Leave empty to hide this card.',
			'required'     => 0,
			'wrapper'      => array( 'width' => '50' ),
		);
		$fields[] = array(
			'key'          => 'field_outlet_act_' . $i . '_icon',
			'label'        => 'Icon (emoji)',
			'name'         => 'outlet_act_' . $i . '_icon',
			'type'         => 'text',
			'instructions' => 'e.g. 🎮',
			'required'     => 0,
			'wrapper'      => array( 'width' => '15' ),
		);
		$fields[] = array(
			'key'          => 'field_outlet_act_' . $i . '_link',
			'label'        => 'Link',
			'name'         => 'outlet_act_' . $i . '_link',
			'type'         => 'text',
			'instructions' => 'e.g. /vr-arcade/',
			'required'     => 0,
			'wrapper'      => array( 'width' => '35' ),
		);
		$fields[] = array(
			'key'      => 'field_outlet_act_' . $i . '_desc',
			'label'    => 'Description',
			'name'     => 'outlet_act_' . $i . '_desc',
			'type'     => 'textarea',
			'rows'     => 2,
			'required' => 0,
		);
		$fields[] = array(
			'key'           => 'field_outlet_act_' . $i . '_image',
			'label'         => 'Image (optional)',
			'name'          => 'outlet_act_' . $i . '_image',
			'type'          => 'image',
			'instructions'  => 'Landscape works best — the card crops it to 16:9 safely on every screen size.',
			'return_format' => 'id',
			'preview_size'  => 'medium',
			'library'       => 'all',
			'required'      => 0,
		);
	}

	// Close the last accordion
	$fields[] = array(
		'key'      => 'field_outlet_act_end',
		'label'    => '',
		'name'     => '',
		'type'     => 'accordion',
		'endpoint' => 1,
	);

	acf_add_local_field_group( array(
		'key'             => 'group_ow_outlet_activities',
		'title'           => 'What We Offer — Intro & Activity Cards',
		'fields'          => $fields,
		'location'        => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page-pricing.php',
				),
			),
		),
		'menu_order'      => 3,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
		'description'     => 'The second section of the outlet page: intro text + activity/game cards. Click a card row to expand it.',
	) );
} );

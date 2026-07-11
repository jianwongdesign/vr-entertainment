<?php
/**
 * Plugin Name: Overworld — Outlet "What We Offer" (intro + activity cards)
 * Description: Client-editable intro text and up to 6 activity cards (title, description, link, emoji icon, optional image) for the outlet pages. Consumed by page-pricing.php. Leave a card's title empty to hide that slot; leave all empty to fall back to the theme's built-in defaults.
 * Author: Overworld
 * Version: 1.0.0
 *
 * Must-use plugin: auto-loads, no activation needed. Free ACF has no
 * repeater, so this mirrors the outlet_gallery_* convention: fixed slots.
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
		$fields[] = array(
			'key'          => 'field_outlet_act_' . $i . '_title',
			'label'        => "Card {$i} — Title",
			'name'         => 'outlet_act_' . $i . '_title',
			'type'         => 'text',
			'instructions' => 1 === $i ? 'Leave the title empty to hide this card.' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '25' ),
		);
		$fields[] = array(
			'key'      => 'field_outlet_act_' . $i . '_desc',
			'label'    => "Card {$i} — Description",
			'name'     => 'outlet_act_' . $i . '_desc',
			'type'     => 'textarea',
			'rows'     => 2,
			'required' => 0,
			'wrapper'  => array( 'width' => '30' ),
		);
		$fields[] = array(
			'key'          => 'field_outlet_act_' . $i . '_link',
			'label'        => "Card {$i} — Link",
			'name'         => 'outlet_act_' . $i . '_link',
			'type'         => 'text',
			'instructions' => 1 === $i ? 'e.g. /vr-arcade/' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '15' ),
		);
		$fields[] = array(
			'key'          => 'field_outlet_act_' . $i . '_icon',
			'label'        => "Card {$i} — Icon",
			'name'         => 'outlet_act_' . $i . '_icon',
			'type'         => 'text',
			'instructions' => 1 === $i ? 'One emoji, e.g. 🎮' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '10' ),
		);
		$fields[] = array(
			'key'           => 'field_outlet_act_' . $i . '_image',
			'label'         => "Card {$i} — Image (optional)",
			'name'          => 'outlet_act_' . $i . '_image',
			'type'          => 'image',
			'instructions'  => 1 === $i ? 'Optional. Landscape works best (16:9-ish); the card crops it safely on every screen size.' : '',
			'return_format' => 'id',
			'preview_size'  => 'thumbnail',
			'library'       => 'all',
			'required'      => 0,
			'wrapper'       => array( 'width' => '20' ),
		);
	}

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
		'description'     => 'The second section of the outlet page: intro text + activity/game cards.',
	) );
} );

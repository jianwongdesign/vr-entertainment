<?php
/**
 * Plugin Name: Overworld — Outlet Combo Deals
 * Description: Client-editable combo deals for the outlet pages: intro text plus up to 4 combos (title, badge, description, what's included, image, weekday/weekend price, link). Consumed by page-pricing.php, rendered directly under "Activities & Games". Leave a combo's title empty to hide that slot; leave all empty to hide the whole section.
 * Author: Overworld
 * Version: 1.0.0
 *
 * Must-use plugin: auto-loads, no activation needed. Free ACF has no
 * repeater, so this mirrors the outlet_act_* / outlet_gallery_* convention:
 * fixed slots inside collapsible accordions.
 *
 * Prices are optional. Left empty, the template falls back to the matching
 * combo rows in the Pricing CPT, so the section stays in step with the
 * pricing tables lower down the page.
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
			'key'          => 'field_outlet_combos_intro',
			'label'        => 'Section Intro',
			'name'         => 'outlet_combos_intro',
			'type'         => 'textarea',
			'rows'         => 2,
			'instructions' => 'Short line under the "Combo Deals" heading. Leave empty to use the built-in default.',
			'required'     => 0,
		),
	);

	for ( $i = 1; $i <= 4; $i++ ) {
		// Collapsible header per combo
		$fields[] = array(
			'key'          => 'field_outlet_combo_' . $i . '_accordion',
			'label'        => "Combo {$i}",
			'name'         => '',
			'type'         => 'accordion',
			'open'         => 0,
			'multi_expand' => 1,
			'endpoint'     => 0,
			'instructions' => '',
		);
		$fields[] = array(
			'key'          => 'field_outlet_combo_' . $i . '_title',
			'label'        => 'Combo Name',
			'name'         => 'outlet_combo_' . $i . '_title',
			'type'         => 'text',
			'instructions' => 'e.g. Floor Is Lava + XR Party Game. Leave empty to hide this combo.',
			'required'     => 0,
			'wrapper'      => array( 'width' => '60' ),
		);
		$fields[] = array(
			'key'          => 'field_outlet_combo_' . $i . '_badge',
			'label'        => 'Badge (optional)',
			'name'         => 'outlet_combo_' . $i . '_badge',
			'type'         => 'text',
			'instructions' => 'Small pill on the image, e.g. Save $9 or Most Popular.',
			'required'     => 0,
			'wrapper'      => array( 'width' => '40' ),
		);
		$fields[] = array(
			'key'      => 'field_outlet_combo_' . $i . '_desc',
			'label'    => 'Description',
			'name'     => 'outlet_combo_' . $i . '_desc',
			'type'     => 'textarea',
			'rows'     => 2,
			'required' => 0,
		);
		$fields[] = array(
			'key'          => 'field_outlet_combo_' . $i . '_includes',
			'label'        => "What's Included",
			'name'         => 'outlet_combo_' . $i . '_includes',
			'type'         => 'text',
			'instructions' => 'Comma-separated, shown as pills. e.g. 25 min Floor Is Lava, 25 min XR Party Game',
			'required'     => 0,
		);
		$fields[] = array(
			'key'          => 'field_outlet_combo_' . $i . '_weekday',
			'label'        => 'Weekday Price',
			'name'         => 'outlet_combo_' . $i . '_weekday',
			'type'         => 'number',
			'min'          => 0,
			'step'         => '0.01',
			'instructions' => 'Per pax, Mon–Thu. Leave empty to pull the price from Pricing.',
			'required'     => 0,
			'wrapper'      => array( 'width' => '25' ),
		);
		$fields[] = array(
			'key'          => 'field_outlet_combo_' . $i . '_weekend',
			'label'        => 'Weekend & PH Price',
			'name'         => 'outlet_combo_' . $i . '_weekend',
			'type'         => 'number',
			'min'          => 0,
			'step'         => '0.01',
			'instructions' => 'Per pax, Fri–Sun & PH.',
			'required'     => 0,
			'wrapper'      => array( 'width' => '25' ),
		);
		$fields[] = array(
			'key'          => 'field_outlet_combo_' . $i . '_link',
			'label'        => 'Button Link (optional)',
			'name'         => 'outlet_combo_' . $i . '_link',
			'type'         => 'text',
			'instructions' => 'e.g. /book-now-funan/. Leave empty to hide the button.',
			'required'     => 0,
			'wrapper'      => array( 'width' => '50' ),
		);
		$fields[] = array(
			'key'           => 'field_outlet_combo_' . $i . '_image',
			'label'         => 'Image (optional)',
			'name'          => 'outlet_combo_' . $i . '_image',
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
		'key'      => 'field_outlet_combo_end',
		'label'    => '',
		'name'     => '',
		'type'     => 'accordion',
		'endpoint' => 1,
	);

	acf_add_local_field_group( array(
		'key'             => 'group_ow_outlet_combos',
		'title'           => 'Combo Deals',
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
		'menu_order'      => 4,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
		'description'     => 'Bundle offers shown right under "Activities & Games" on the outlet page. Click a combo row to expand it. Fill in at least a Combo Name to make one appear.',
	) );
} );

/**
 * Elementor caches rendered widgets per page; ACF saves need that cleared or
 * the outlet page keeps serving the old combos. Mirrors overworld-xr-modes.php.
 */
add_action( 'acf/save_post', function ( $post_id ) {
	if ( is_numeric( $post_id ) && get_post_meta( $post_id, '_elementor_element_cache', true ) ) {
		delete_post_meta( $post_id, '_elementor_element_cache' );
	}
}, 20 );

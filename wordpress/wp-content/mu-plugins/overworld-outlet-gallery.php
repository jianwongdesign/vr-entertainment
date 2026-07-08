<?php
/**
 * Plugin Name: Overworld — Outlet Gallery
 * Description: Adds 6 ACF image slots ("Outlet Gallery") to every page using the Pricing Page template (the 3 outlet pages), so the client can feature outlet photos without touching code. Consumed by the theme template page-pricing.php.
 * Author: Overworld
 * Version: 1.0.0
 *
 * Must-use plugin: auto-loads, no activation needed. Free ACF has no gallery
 * field, so this mirrors the exp_image_1 / exp_image_2 convention: fixed image
 * slots (outlet_gallery_1..6). The template renders only the slots that are
 * filled — image 1 becomes the large lead tile — and hides the whole section
 * from visitors when every slot is empty (editors see placeholder tiles).
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', function () {

	// ACF must be active.
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$fields = array();
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = array(
			'key'           => 'field_outlet_gallery_' . $i,
			'label'         => 'Gallery Image ' . $i . ( 1 === $i ? ' (large lead tile)' : '' ),
			'name'          => 'outlet_gallery_' . $i,
			'type'          => 'image',
			'instructions'  => 1 === $i
				? 'Shown biggest, top-left of the gallery. Landscape photos work best (recommended 1600x1200 or wider, under 400KB).'
				: '',
			'required'      => 0,
			'return_format' => 'id',
			'preview_size'  => 'medium',
			'library'       => 'all',
			'wrapper'       => array(
				'width' => '33',
				'class' => '',
				'id'    => '',
			),
		);
	}

	acf_add_local_field_group( array(
		'key'             => 'group_ow_outlet_gallery',
		'title'           => 'Outlet Gallery',
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
		'menu_order'      => 5,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
		'description'     => 'Photos featured in the Gallery section of the outlet page. Fill any number of slots — empty slots are skipped, and the section is hidden from visitors if all are empty.',
	) );
} );

<?php
/**
 * Overworld — Experience "Display Order" ACF field
 *
 * Adds a manual sort field to the Experience CPT so VR Arcade, VR Escape and
 * VR Free Roam games can be ordered by hand in the library archives
 * (/experience-type/vr-arcade/, /vr-escape/, /vr-roam/).
 *
 * Consumed by the theme template `taxonomy-experience_type.php`, which sorts
 * the grid by `exp_display_order` (lower first) and falls back to alphabetical
 * for any game left blank — so nothing ever drops out of the grid.
 *
 * Registered as its own lightweight field group (a compact sidebar box) rather
 * than editing the main Experience group, so it is self-contained and cannot
 * clash with the UI-created fields. Mirrors the existing
 * event_display_order / pricing_display_order / faq_display_order convention.
 *
 * WP Code Snippets scope: Run Everywhere.
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

	acf_add_local_field_group( array(
		'key'      => 'group_ow_experience_ordering',
		'title'    => 'Display Order',
		'fields'   => array(
			array(
				'key'          => 'field_exp_display_order',
				'label'        => 'Display Order',
				'name'         => 'exp_display_order',
				'type'         => 'number',
				'instructions' => 'Lower numbers appear first in the game library. Leave blank to sort this game last (alphabetically).',
				'required'     => 0,
				'default_value' => '',
				'placeholder'  => 'e.g. 1',
				'min'          => 0,
				'step'         => 1,
				'wrapper'      => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'experience',
				),
			),
		),
		'menu_order'      => 0,
		'position'        => 'side',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
		'description'     => 'Manual ordering for the Experience library archives.',
	) );
} );

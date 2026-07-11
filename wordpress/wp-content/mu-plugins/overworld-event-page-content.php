<?php
/**
 * Plugin Name: Overworld — Event Pages (intro, gallery, reviews)
 * Description: Client-editable intro text, 6-slot photo gallery, and 4 review slots for the Team Building / Birthday Party outlet pages (Event Listing template). Packages stay driven by the Events CPT. Consumed by page-event-listing.php.
 * Author: Overworld
 * Version: 1.1.0
 *
 * Must-use plugin: auto-loads, no activation needed. Free ACF has no
 * repeater/gallery, so fixed slots (same convention as outlet_gallery_*).
 * Empty gallery/review slots are skipped; fully-empty sections are hidden
 * from visitors (editors see a placeholder hint).
 * v1.1.0: metabox reorganised into Intro / Gallery / Reviews tabs, reviews
 * into collapsible accordions, so it is easy to navigate.
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

	$fields = array();

	// ---- Tab: Intro -------------------------------------------------
	$fields[] = array(
		'key'       => 'field_event_tab_intro',
		'label'     => 'Intro',
		'name'      => '',
		'type'      => 'tab',
		'placement' => 'top',
	);
	$fields[] = array(
		'key'          => 'field_event_page_intro',
		'label'        => 'Intro Text',
		'name'         => 'event_page_intro',
		'type'         => 'textarea',
		'rows'         => 4,
		'instructions' => 'Shown right under the hero as a "How It Works" paragraph. Leave empty to use the built-in default for this event type.',
		'required'     => 0,
	);

	// ---- Tab: Gallery ------------------------------------------------
	$fields[] = array(
		'key'       => 'field_event_tab_gallery',
		'label'     => 'Gallery',
		'name'      => '',
		'type'      => 'tab',
		'placement' => 'top',
	);
	$fields[] = array(
		'key'       => 'field_event_gallery_msg',
		'label'     => '',
		'name'      => '',
		'type'      => 'message',
		'message'   => 'Photos of past events. <strong>Image 1 is the large lead tile.</strong> Fill any number of slots — the section is hidden from visitors if all are empty.',
		'new_lines' => '',
		'esc_html'  => 0,
	);
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = array(
			'key'           => 'field_event_gallery_' . $i,
			'label'         => 'Image ' . $i . ( 1 === $i ? ' — lead tile' : '' ),
			'name'          => 'event_gallery_' . $i,
			'type'          => 'image',
			'instructions'  => '',
			'return_format' => 'id',
			'preview_size'  => 'medium',
			'library'       => 'all',
			'required'      => 0,
			'wrapper'       => array( 'width' => '33.33' ),
		);
	}

	// ---- Tab: Reviews --------------------------------------------------
	$fields[] = array(
		'key'       => 'field_event_tab_reviews',
		'label'     => 'Reviews',
		'name'      => '',
		'type'      => 'tab',
		'placement' => 'top',
	);
	$fields[] = array(
		'key'       => 'field_event_reviews_msg',
		'label'     => '',
		'name'      => '',
		'type'      => 'message',
		'message'   => 'Shown as "What Groups Say" cards above the enquiry section. A review with empty text is hidden; the whole section is hidden from visitors if all are empty. Click a review row to expand it.',
		'new_lines' => '',
		'esc_html'  => 0,
	);
	for ( $i = 1; $i <= 4; $i++ ) {
		$fields[] = array(
			'key'          => 'field_event_review_' . $i . '_accordion',
			'label'        => "Review {$i}",
			'name'         => '',
			'type'         => 'accordion',
			'open'         => 0,
			'multi_expand' => 1,
			'endpoint'     => 0,
		);
		$fields[] = array(
			'key'      => 'field_event_review_' . $i . '_text',
			'label'    => 'Review Text',
			'name'     => 'event_review_' . $i . '_text',
			'type'     => 'textarea',
			'rows'     => 3,
			'required' => 0,
		);
		$fields[] = array(
			'key'      => 'field_event_review_' . $i . '_name',
			'label'    => 'Name',
			'name'     => 'event_review_' . $i . '_name',
			'type'     => 'text',
			'required' => 0,
			'wrapper'  => array( 'width' => '40' ),
		);
		$fields[] = array(
			'key'          => 'field_event_review_' . $i . '_meta',
			'label'        => 'Detail',
			'name'         => 'event_review_' . $i . '_meta',
			'type'         => 'text',
			'instructions' => 'e.g. "Team of 15 · Tech company"',
			'required'     => 0,
			'wrapper'      => array( 'width' => '40' ),
		);
		$fields[] = array(
			'key'           => 'field_event_review_' . $i . '_stars',
			'label'         => 'Stars',
			'name'          => 'event_review_' . $i . '_stars',
			'type'          => 'number',
			'default_value' => 5,
			'min'           => 1,
			'max'           => 5,
			'step'          => 1,
			'required'      => 0,
			'wrapper'       => array( 'width' => '20' ),
		);
	}
	// Close the last accordion
	$fields[] = array(
		'key'      => 'field_event_review_end',
		'label'    => '',
		'name'     => '',
		'type'     => 'accordion',
		'endpoint' => 1,
	);

	acf_add_local_field_group( array(
		'key'             => 'group_ow_event_page_content',
		'title'           => 'Event Page — Intro, Gallery & Reviews',
		'fields'          => $fields,
		'location'        => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page-event-listing.php',
				),
			),
		),
		'menu_order'      => 3,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
		'description'     => 'Editable sections of the Team Building / Birthday Party pages. Packages are managed separately under Events.',
	) );
} );

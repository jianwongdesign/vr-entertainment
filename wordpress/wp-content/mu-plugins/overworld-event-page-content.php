<?php
/**
 * Plugin Name: Overworld — Event Pages (intro, gallery, reviews)
 * Description: Client-editable intro text, 6-slot photo gallery, and 4 review slots for the Team Building / Birthday Party outlet pages (Event Listing template). Packages stay driven by the Events CPT. Consumed by page-event-listing.php.
 * Author: Overworld
 * Version: 1.0.0
 *
 * Must-use plugin: auto-loads, no activation needed. Free ACF has no
 * repeater/gallery, so fixed slots (same convention as outlet_gallery_*).
 * Empty gallery/review slots are skipped; fully-empty sections are hidden
 * from visitors (editors see a placeholder hint).
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
			'key'          => 'field_event_page_intro',
			'label'        => 'Intro',
			'name'         => 'event_page_intro',
			'type'         => 'textarea',
			'rows'         => 4,
			'instructions' => 'Intro paragraph shown right under the hero. Leave empty to use the built-in default for this event type.',
			'required'     => 0,
		),
	);

	// Gallery slots
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = array(
			'key'           => 'field_event_gallery_' . $i,
			'label'         => 'Gallery Image ' . $i . ( 1 === $i ? ' (large lead tile)' : '' ),
			'name'          => 'event_gallery_' . $i,
			'type'          => 'image',
			'instructions'  => 1 === $i ? 'Photos of past events. Fill any number of slots — the section is hidden from visitors if all are empty.' : '',
			'return_format' => 'id',
			'preview_size'  => 'medium',
			'library'       => 'all',
			'required'      => 0,
			'wrapper'       => array( 'width' => '33' ),
		);
	}

	// Review slots
	for ( $i = 1; $i <= 4; $i++ ) {
		$fields[] = array(
			'key'          => 'field_event_review_' . $i . '_text',
			'label'        => "Review {$i} — Text",
			'name'         => 'event_review_' . $i . '_text',
			'type'         => 'textarea',
			'rows'         => 3,
			'instructions' => 1 === $i ? 'Leave the text empty to hide this review. Section is hidden if all reviews are empty.' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '40' ),
		);
		$fields[] = array(
			'key'      => 'field_event_review_' . $i . '_name',
			'label'    => "Review {$i} — Name",
			'name'     => 'event_review_' . $i . '_name',
			'type'     => 'text',
			'required' => 0,
			'wrapper'  => array( 'width' => '20' ),
		);
		$fields[] = array(
			'key'          => 'field_event_review_' . $i . '_meta',
			'label'        => "Review {$i} — Detail",
			'name'         => 'event_review_' . $i . '_meta',
			'type'         => 'text',
			'instructions' => 1 === $i ? 'e.g. "Team of 15 · Tech company" or "Birthday, age 10"' : '',
			'required'     => 0,
			'wrapper'      => array( 'width' => '25' ),
		);
		$fields[] = array(
			'key'           => 'field_event_review_' . $i . '_stars',
			'label'         => "Review {$i} — Stars",
			'name'          => 'event_review_' . $i . '_stars',
			'type'          => 'number',
			'default_value' => 5,
			'min'           => 1,
			'max'           => 5,
			'step'          => 1,
			'required'      => 0,
			'wrapper'       => array( 'width' => '15' ),
		);
	}

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

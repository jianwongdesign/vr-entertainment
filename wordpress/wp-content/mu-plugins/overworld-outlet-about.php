<?php
/**
 * Plugin Name: Overworld — Outlet Store Introduction
 * Description: Adds a client-editable "Store Introduction" block (heading + rich text) to every page using the Pricing Page template — the 3 outlet pages. Rendered by page-pricing.php as the first section under the hero. Left empty, the template falls back to a per-outlet default so the section is never blank.
 * Author: Overworld
 * Version: 1.0.0
 *
 * Must-use plugin: auto-loads, no activation needed.
 *
 * Two fields only, on purpose: this is the one place on the outlet page where
 * the client can write freely about the store itself, in their own words. The
 * rest of the page is structured data (activities, combos, prices, FAQs).
 *
 * The body is a WYSIWYG rather than the plain textarea used elsewhere in the
 * outlet fields — an introduction wants paragraphs, the odd bold phrase and a
 * link. The template runs it through wp_kses_post().
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
		'key'    => 'group_ow_outlet_about',
		'title'  => 'Store Introduction',
		'fields' => array(
			array(
				'key'       => 'field_outlet_about_msg',
				'label'     => '',
				'name'      => '',
				'type'      => 'message',
				'message'   => 'The first thing visitors read under the big outlet name — a few lines about this store in your own words. <strong>Leave both fields empty and the built-in introduction for this outlet is used instead</strong>, so the section is never blank. Anything you type here replaces it.',
				'new_lines' => '',
				'esc_html'  => 0,
			),
			array(
				'key'          => 'field_outlet_about_heading',
				'label'        => 'Heading',
				'name'         => 'outlet_about_heading',
				'type'         => 'text',
				'instructions' => 'Short. Empty = "Welcome To [outlet name]".',
				'required'     => 0,
				'maxlength'    => 60,
			),
			array(
				'key'           => 'field_outlet_about_text',
				'label'         => 'Introduction',
				'name'          => 'outlet_about_text',
				'type'          => 'wysiwyg',
				'instructions'  => 'Two or three short paragraphs works best — what this store is, what makes it worth the trip, who it suits. Bold and links are fine; headings and images are not (use the Gallery block for photos).',
				'required'      => 0,
				'tabs'          => 'visual',
				'toolbar'       => 'basic',
				'media_upload'  => 0,
				'delay'         => 0,
			),
		),
		'location'        => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page-pricing.php',
				),
			),
		),
		'menu_order'      => 2,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
		'description'     => 'Opening copy for the outlet page, directly under the hero.',
	) );
} );

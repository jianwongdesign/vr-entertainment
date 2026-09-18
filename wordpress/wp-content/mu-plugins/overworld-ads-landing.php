<?php
/**
 * Plugin Name: Overworld — Google Ads Landing Page (SEO wiring)
 * Description: Gives the paid-traffic landing page (template page-ads-events.php) its title tag, meta description, keywords and a noindex directive, without hardcoding a page ID. Registers through the ow_seo_page_map and ow_seo_page_keywords filters that overworld-seo.php already exposes.
 * Author: Overworld
 * Version: 1.0.0
 *
 * Must-use plugin: auto-loads, no activation needed.
 *
 * WHY noindex. The landing page deliberately restates what
 * /team-building/ and /birthday-party/ already say, because a Google Ads
 * visitor should not have to click twice to reach a booking calendar. Left
 * indexable, it would be a third page competing with those two for the same
 * queries, and the two hub pages are the ones with the internal links and the
 * package data behind them. Google Ads does not read the robots directive, so
 * nothing about the campaign changes.
 *
 * To index it anyway, set the SEO box's "noindex" field to 0 on the page
 * itself — the per-post field wins over everything here.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_ADS_TEMPLATE = 'page-ads-events.php';

/**
 * IDs of the pages using the Google Ads landing template.
 *
 * Looked up by template rather than written down, so the page can be renamed,
 * re-slugged or duplicated for a second campaign without this file changing.
 * Cached for the request — ow_seo_page_map() is called several times per page
 * load and this should not become three queries.
 *
 * @return int[]
 */
function ow_ads_page_ids() {
	static $ids = null;

	if ( null !== $ids ) {
		return $ids;
	}

	$ids = get_posts(
		array(
			'post_type'        => 'page',
			'post_status'      => array( 'publish', 'draft', 'private' ),
			'numberposts'      => -1,
			'fields'           => 'ids',
			'meta_key'         => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'       => OW_ADS_TEMPLATE,     // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'suppress_filters' => false,
		)
	);

	$ids = array_map( 'intval', (array) $ids );

	return $ids;
}

/**
 * Title, description and the noindex flag for the landing page.
 */
add_filter(
	'ow_seo_page_map',
	function ( $map ) {
		foreach ( ow_ads_page_ids() as $id ) {
			$map[ $id ] = array(
				'title'   => 'Team Building & Birthday Parties in Singapore | Overworld',
				'desc'    => 'Book team building activities and birthday parties at Overworld Singapore — VR arcade, VR escape rooms, free-roam VR, laser maze and Floor Is Lava at Kallang, Orchard and Funan. Live availability, instant confirmation.',
				'noindex' => true,
			);
		}

		return $map;
	}
);

/**
 * Keywords for the landing page.
 *
 * Same caveat as everywhere else in overworld-seo.php: the tag is emitted
 * because it was asked for, not because Google reads it. Kept to an honest
 * description of what the page covers so it cannot read as stuffing.
 */
add_filter(
	'ow_seo_page_keywords',
	function ( $map ) {
		foreach ( ow_ads_page_ids() as $id ) {
			$map[ $id ] = array(
				'team building Singapore',
				'corporate team building Singapore',
				'team building activities Singapore',
				'VR team building Singapore',
				'company outing Singapore',
				'birthday party Singapore',
				'VR birthday party Singapore',
				'kids birthday party venue Singapore',
				'birthday party venue Singapore',
				'group activities Singapore',
				'indoor activities Singapore',
				'VR arcade Singapore',
				'VR escape room Singapore',
				'laser maze Singapore',
				'Floor Is Lava Singapore',
				'Kallang Orchard Funan',
			);
		}

		return $map;
	}
);

/**
 * Keep the landing page out of wp-sitemap.xml.
 *
 * The noindex directive above already tells a crawler not to index it, but
 * listing a noindex URL in the sitemap is a contradiction that shows up in
 * Search Console as a "Excluded by 'noindex'" warning on every crawl.
 */
add_filter(
	'wp_sitemaps_posts_query_args',
	function ( $args, $post_type ) {
		if ( 'page' !== $post_type ) {
			return $args;
		}

		$ids = ow_ads_page_ids();
		if ( empty( $ids ) ) {
			return $args;
		}

		$args['post__not_in'] = array_merge(
			isset( $args['post__not_in'] ) ? (array) $args['post__not_in'] : array(),
			$ids
		);

		return $args;
	},
	10,
	2
);

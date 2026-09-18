<?php
/**
 * Create the Google Ads landing page and put it on the paid-traffic template.
 *
 * The page itself is empty in the database on purpose. Everything a visitor
 * sees lives in the child theme's page-ads-events.php, so the page an ad
 * points at cannot be edited out from under a running campaign — no Elementor
 * document, no ACF fields, no shortcodes.
 *
 * Created as a DRAFT. Nothing goes live until the go-live block at the bottom
 * of this file is run by hand.
 *
 * Idempotent: re-running finds the existing page by slug, re-asserts the
 * template, and never flips a published page back to draft.
 *
 * Run: wp eval-file ads-landing-page.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_ADS_SLUG     = 'group-events-singapore';
const OW_ADS_TITLE    = 'Team Building & Birthday Parties Singapore';
const OW_ADS_TEMPLATE = 'page-ads-events.php';

// The template has to exist before a page is pointed at it, or WordPress
// silently falls back to page.php and the landing page renders as a blank
// document with a site header.
$template_path = get_stylesheet_directory() . '/' . OW_ADS_TEMPLATE;
if ( ! file_exists( $template_path ) ) {
	printf( "ABORT  %s is not in the child theme — deploy it first.\n", OW_ADS_TEMPLATE );
	return;
}

$existing = get_page_by_path( OW_ADS_SLUG, OBJECT, 'page' );

if ( $existing ) {
	$page_id = (int) $existing->ID;
	printf(
		"found  %d /%s/ (%s)\n",
		$page_id,
		OW_ADS_SLUG,
		$existing->post_status
	);
} else {
	$page_id = wp_insert_post(
		array(
			'post_type'      => 'page',
			'post_status'    => 'draft',
			'post_title'     => OW_ADS_TITLE,
			'post_name'      => OW_ADS_SLUG,
			'post_content'   => '',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		),
		true
	);

	if ( is_wp_error( $page_id ) ) {
		printf( "ABORT  could not create the page: %s\n", $page_id->get_error_message() );
		return;
	}

	printf( "create %d /%s/ (draft)\n", $page_id, OW_ADS_SLUG );
}

// Assign the template, and keep the page out of Elementor's hands: an
// Elementor edit mode on this ID would let the builder take over rendering.
update_post_meta( $page_id, '_wp_page_template', OW_ADS_TEMPLATE );
delete_post_meta( $page_id, '_elementor_edit_mode' );
delete_post_meta( $page_id, '_elementor_data' );
delete_post_meta( $page_id, '_elementor_element_cache' );

printf( "set    _wp_page_template = %s\n", OW_ADS_TEMPLATE );

// Title, description, keywords and the noindex flag come from the mu-plugin
// overworld-ads-landing.php, which matches on the template rather than on
// this ID. Report whether it is actually loaded, because a page that is live
// without it would be indexable and competing with /team-building/.
if ( function_exists( 'ow_ads_page_ids' ) ) {
	echo "seo    overworld-ads-landing.php is loaded — title, description, keywords and noindex are wired\n";
} else {
	echo "WARN   overworld-ads-landing.php is NOT loaded — the page would be indexable, deploy the mu-plugin\n";
}

$ads_url = get_permalink( $page_id );

echo "\n";
printf( "page id : %d\n", $page_id );
printf( "url     : %s\n", $ads_url );
echo "\nGoogle Ads final URLs:\n";
printf( "  all           %s\n", $ads_url );
printf( "  team building %s\n", add_query_arg( 'e', 'tb', $ads_url ) );
printf( "  birthday      %s\n", add_query_arg( 'e', 'bp', $ads_url ) );
printf( "  kallang       %s\n", add_query_arg( array( 'e' => 'tb', 'outlet' => 'kallang-wave-mall' ), $ads_url ) );
printf( "  orchard       %s\n", add_query_arg( array( 'e' => 'bp', 'outlet' => 'orchard-central' ), $ads_url ) );
printf( "  funan         %s\n", add_query_arg( array( 'e' => 'tb', 'outlet' => 'funan' ), $ads_url ) );
echo "\ndone\n";

/* =====================================================================
   GO LIVE — deliberately NOT automated. Run by hand once approved.
   ---------------------------------------------------------------------
   1. Preview it while it is still a draft (logged in as an admin):
        <url>?preview=true

      Check on a phone as well as a desktop: the Bookeo calendar, the outlet
      switcher, and the sticky bar at the bottom.

   2. Publish:
        wp post update <page-id> --post_status=publish

   3. Confirm the page is NOT indexable and NOT in the sitemap:
        curl -s <url> | grep -i 'name="robots"'      # expect noindex, follow
        curl -s https://overworld.com.sg/wp-sitemap-posts-page-1.xml \
          | grep -c group-events-singapore           # expect 0

   4. Purge caches:
        wp litespeed-purge all
        wp cache flush

   5. Do NOT add it to the navigation menu. It is an ad destination, not a
      site page — a nav link would send organic visitors to a noindex page
      that duplicates /team-building/ and /birthday-party/.
   ===================================================================== */

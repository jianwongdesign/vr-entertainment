<?php
/**
 * Create the event rental page and put it on the event rental template.
 *
 * The page body is empty in the database on purpose. Everything a visitor
 * sees comes from the child theme's page-event-rental.php, reading the ACF
 * fields that overworld-event-rental.php registers — no Elementor document.
 * Until the client fills a field in, the template shows the built-in copy.
 *
 * Created as a DRAFT. Nothing goes live until the go-live block at the bottom
 * of this file is run by hand.
 *
 * Idempotent: re-running finds the existing page by slug, re-asserts the
 * template, and never flips a published page back to draft.
 *
 * Run: wp eval-file event-rental-page.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_RENTAL_PAGE_SLUG  = 'event-rental';
const OW_RENTAL_PAGE_TITLE = 'Interactive Game Rental for Events';
const OW_RENTAL_PAGE_TPL   = 'page-event-rental.php';

// The template has to exist before a page is pointed at it, or WordPress
// silently falls back to page.php and the page renders as a blank document
// with a site header.
$template_path = get_stylesheet_directory() . '/' . OW_RENTAL_PAGE_TPL;
if ( ! file_exists( $template_path ) ) {
	printf( "ABORT  %s is not in the child theme — deploy it first.\n", OW_RENTAL_PAGE_TPL );
	return;
}

$existing = get_page_by_path( OW_RENTAL_PAGE_SLUG, OBJECT, 'page' );

if ( $existing ) {
	$page_id = (int) $existing->ID;
	printf(
		"found  %d /%s/ (%s)\n",
		$page_id,
		OW_RENTAL_PAGE_SLUG,
		$existing->post_status
	);
} else {
	$page_id = wp_insert_post(
		array(
			'post_type'      => 'page',
			'post_status'    => 'draft',
			'post_title'     => OW_RENTAL_PAGE_TITLE,
			'post_name'      => OW_RENTAL_PAGE_SLUG,
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

	printf( "create %d /%s/ (draft)\n", $page_id, OW_RENTAL_PAGE_SLUG );
}

// Assign the template, and keep the page out of Elementor's hands: an
// Elementor edit mode on this ID would let the builder take over rendering.
update_post_meta( $page_id, '_wp_page_template', OW_RENTAL_PAGE_TPL );
delete_post_meta( $page_id, '_elementor_edit_mode' );
delete_post_meta( $page_id, '_elementor_data' );
delete_post_meta( $page_id, '_elementor_element_cache' );

printf( "set    _wp_page_template = %s\n", OW_RENTAL_PAGE_TPL );

// The editable fields, defaults, SEO title/description and FAQ schema all
// come from the mu-plugin overworld-event-rental.php, which matches on the
// template rather than on this ID. Report whether it is actually loaded,
// because without it the page has no content box in WP Admin and no copy.
if ( function_exists( 'ow_rental_page_ids' ) ) {
	echo "acf    overworld-event-rental.php is loaded — content box, defaults and SEO are wired\n";
} else {
	echo "WARN   overworld-event-rental.php is NOT loaded — the page has no editable fields; deploy the mu-plugin\n";
}

$url = get_permalink( $page_id );

echo "\n";
printf( "page id : %d\n", $page_id );
printf( "url     : %s\n", $url );
printf( "edit    : %s\n", admin_url( 'post.php?post=' . $page_id . '&action=edit' ) );
echo "\ndone\n";

/* =====================================================================
   GO LIVE — deliberately NOT automated. Run by hand once approved.
   ---------------------------------------------------------------------
   1. Preview it while it is still a draft (logged in as an admin):
        <url>?preview=true

      Check on a phone as well as a desktop: the hero image fade, the
      activity grid at one column, the "what's included" strip wrapping.

   2. Upload the hero photo and (optionally) activity photos in
        WP Admin → Pages → Interactive Game Rental for Events
        → "Event Rental Page Content" box.
      Without a hero photo the hero shows a gradient panel; without
      activity photos the cards borrow the outlet pages' pictures.

   3. Publish:
        wp post update <page-id> --post_status=publish

   4. Confirm it IS indexable and IS in the sitemap (this is a real service
      page, unlike the ads landing page):
        curl -s <url> | grep -i 'name="robots"'      # expect index, follow (or no tag)
        curl -s https://overworld.com.sg/wp-sitemap-posts-page-1.xml \
          | grep -c event-rental                     # expect 1

   5. Purge caches:
        wp litespeed-purge all
        wp cache flush

   6. Add it to the navigation menu as "Event Rental" if that is wanted —
      Appearance → Menus, or the Elementor header.
   ===================================================================== */

<?php
/**
 * Add "Equipment Rental" → /event-rental/ to the site navigation.
 *
 *   Header: Events ▾ → (after Birthday Party) a new "At Your Venue" sub-head
 *           with an "Equipment Rental" item.
 *   Footer: the Events column, after "Birthday Party".
 *
 * Both menus are raw HTML inside Elementor HTML widgets on the header-footer
 * templates (post_type elementor-hf). Rather than hard-code post and widget
 * IDs, this walks every elementor-hf template and patches whichever HTML
 * widget contains the anchor markup — the same anchors the live nav has today
 * (checked against the rendered homepage on 22 Sep 2026).
 *
 * Idempotent: a widget that already links to /event-rental/ is skipped.
 * Aborts without saving when an anchor is not found, so a redesigned nav is
 * never half-patched.
 *
 * Run: wp eval-file nav-event-rental-link.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_RENTAL_NAV_HREF  = '/event-rental/';
const OW_RENTAL_NAV_LABEL = 'Equipment Rental';

// Header: the Birthday Party <li class="has-submenu"> block ends with this,
// immediately before the Events submenu closes.
$header_anchor = '<li><a href="/birthday-party/funan">Funan</a></li>' . "\n"
	. '            </ul>' . "\n"
	. '          </li>' . "\n";
$header_insert = $header_anchor
	. "\n"
	. '          <li class="sub-head">At Your Venue</li>' . "\n"
	. '          <li><a href="' . OW_RENTAL_NAV_HREF . '">' . OW_RENTAL_NAV_LABEL . '</a></li>' . "\n";

// Footer: plain list in the Events column.
$footer_anchor = '<li><a href="/birthday-party/">Birthday Party</a></li>';
$footer_insert = $footer_anchor . "\n"
	. '          <li><a href="' . OW_RENTAL_NAV_HREF . '">' . OW_RENTAL_NAV_LABEL . '</a></li>';

$templates = get_posts( array(
	'post_type'   => 'elementor-hf',
	'post_status' => 'any',
	'numberposts' => -1,
	'fields'      => 'ids',
) );

if ( ! $templates ) {
	echo "no elementor-hf templates found, aborted\n";
	return;
}

$patched = 0;

foreach ( $templates as $post_id ) {
	$raw = get_post_meta( $post_id, '_elementor_data', true );
	if ( '' === $raw ) {
		continue;
	}
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		continue;
	}

	$changed = false;

	$walk = function ( $elements ) use ( &$walk, &$changed, $post_id, $header_anchor, $header_insert, $footer_anchor, $footer_insert ) {
		foreach ( $elements as $i => $element ) {
			if ( isset( $element['settings']['html'] ) && is_string( $element['settings']['html'] ) ) {
				$html = $element['settings']['html'];
				$id   = isset( $element['id'] ) ? $element['id'] : '?';

				if ( false !== strpos( $html, 'href="' . OW_RENTAL_NAV_HREF . '"' ) ) {
					printf( "post %d widget %s: already links to %s, skipped\n", $post_id, $id, OW_RENTAL_NAV_HREF );
				} elseif ( false !== strpos( $html, $header_anchor ) ) {
					$html    = str_replace( $header_anchor, $header_insert, $html );
					$changed = true;
					printf( "post %d widget %s: header — Events ▾ gained \"%s\"\n", $post_id, $id, OW_RENTAL_NAV_LABEL );
				} elseif ( false !== strpos( $html, $footer_anchor ) ) {
					$html    = str_replace( $footer_anchor, $footer_insert, $html );
					$changed = true;
					printf( "post %d widget %s: footer — Events list gained \"%s\"\n", $post_id, $id, OW_RENTAL_NAV_LABEL );
				}

				$elements[ $i ]['settings']['html'] = $html;
			}
			if ( ! empty( $element['elements'] ) ) {
				$elements[ $i ]['elements'] = $walk( $element['elements'] );
			}
		}
		return $elements;
	};

	$data = $walk( $data );

	if ( $changed ) {
		update_post_meta(
			$post_id,
			'_elementor_data',
			wp_slash( wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) )
		);
		delete_post_meta( $post_id, '_elementor_element_cache' );
		$patched++;
	}
}

if ( 0 === $patched ) {
	echo "nothing changed — either already linked, or the nav markup no longer matches the anchors in this script\n";
} else {
	printf( "%d template(s) updated. Purge caches so the new nav renders.\n", $patched );
}

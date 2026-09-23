<?php
/**
 * One past event card.
 *
 * Included in a loop by single-past_event.php ("more events") and
 * archive-past_event.php. Expects two variables already in scope:
 *
 *   $card      one row from ow_pe_cards()
 *   $pe_arrow  the shared arrow SVG
 *
 * Kept as a partial rather than a function so the markup stays readable HTML,
 * the way the rest of the child theme's templates are written. The equivalent
 * tile on the Equipment Rental page is inline in page-event-rental.php with
 * its own class prefix, because that page carries its own stylesheet.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) || empty( $card ) ) {
	return;
}

$pe_card_arrow = isset( $pe_arrow ) ? $pe_arrow : '';
?>
<a class="ow-pe__card" href="<?php echo esc_url( $card['url'] ); ?>">
  <?php if ( $card['image_id'] ) : ?>
    <div class="ow-pe__card-media">
      <?php echo wp_get_attachment_image( $card['image_id'], 'large', false, array( 'loading' => 'lazy' ) ); ?>
      <?php if ( '' !== $card['tag'] ) : ?>
        <span class="ow-pe__card-tag"><?php echo esc_html( $card['tag'] ); ?></span>
      <?php endif; ?>
      <?php if ( $card['photos'] > 1 ) : ?>
        <span class="ow-pe__card-count"><?php echo (int) $card['photos']; ?> Photos</span>
      <?php endif; ?>
    </div>
  <?php else : ?>
    <div class="ow-pe__card-media ow-pe__card-media--empty">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><circle cx="9" cy="10" r="1.8"/><path d="m4 17 5-4.5 4 3.5 3-2.5 4 3.5"/></svg>
      <?php if ( '' !== $card['tag'] ) : ?>
        <span class="ow-pe__card-tag"><?php echo esc_html( $card['tag'] ); ?></span>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="ow-pe__card-body">
    <h3 class="ow-pe__card-name"><?php echo esc_html( $card['title'] ); ?></h3>
    <?php if ( ! empty( $card['meta'] ) ) : ?>
      <div class="ow-pe__card-meta"><?php echo esc_html( implode( ' · ', $card['meta'] ) ); ?></div>
    <?php endif; ?>
    <?php if ( '' !== $card['summary'] ) : ?>
      <p class="ow-pe__card-blurb"><?php echo esc_html( wp_trim_words( $card['summary'], 26 ) ); ?></p>
    <?php endif; ?>
    <span class="ow-pe__card-more">
      See The Event <?php echo $pe_card_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </span>
  </div>
</a>

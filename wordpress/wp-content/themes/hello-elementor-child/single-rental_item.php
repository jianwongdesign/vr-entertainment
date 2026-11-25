<?php
/**
 * Template — single-rental_item.php
 *
 * One rental item: /event-rental/<name>/
 *
 * Every word and picture comes from the "Rental Item Details" box on the
 * entry (mu-plugin overworld-rental-items.php), and the description from the
 * normal WordPress editor. Empty sections are left off.
 *
 * Sections, top to bottom:
 *   hero (cover photo, tag, title, highlights, price)
 *   → about → what's in the package + what's covered → space & setup
 *   → photos → questions → pair it with (the other rental items) → CTA
 *
 * Styling: the Past Event page's stylesheet (ow_pe_styles) for the shared
 * parts, plus ow_ri_styles for the parts only a rental item has.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$ri_id  = get_the_ID();
	$ri_get = function ( $name ) use ( $ri_id ) {
		return function_exists( 'ow_ri_val' ) ? ow_ri_val( $ri_id, $name ) : '';
	};

	$ri_cover      = function_exists( 'ow_ri_cover_id' ) ? ow_ri_cover_id( $ri_id ) : 0;
	$ri_highlights = function_exists( 'ow_ri_highlights' ) ? ow_ri_highlights( $ri_id ) : array();
	$ri_pack       = function_exists( 'ow_ri_pack' ) ? ow_ri_pack( $ri_id ) : array();
	$ri_included   = function_exists( 'ow_ri_included' ) ? ow_ri_included( $ri_id ) : array();
	$ri_specs      = function_exists( 'ow_ri_specs' ) ? ow_ri_specs( $ri_id ) : array();
	$ri_photos     = function_exists( 'ow_ri_photos' ) ? ow_ri_photos( $ri_id ) : array();
	$ri_faqs       = function_exists( 'ow_ri_faqs' ) ? ow_ri_faqs( $ri_id ) : array();
	$ri_more       = function_exists( 'ow_ri_cards' ) ? ow_ri_cards( 3, true, $ri_id ) : array();

	$ri_badge = trim( (string) get_post_meta( $ri_id, 'ri_badge', true ) );
	$ri_price = trim( (string) get_post_meta( $ri_id, 'ri_price', true ) );
	$ri_intro = trim( (string) get_post_meta( $ri_id, 'ri_intro', true ) );
	if ( '' === $ri_intro ) {
		$ri_intro = trim( (string) get_post_meta( $ri_id, 'ri_tagline', true ) );
	}

	$ri_back_url = ( function_exists( 'ow_ri_rental_page_url' ) ? ow_ri_rental_page_url() : home_url( '/event-rental/' ) ) . '#activities';
	$ri_cta_url  = function_exists( 'ow_ri_url' ) ? ow_ri_url( $ri_get( 'cta_url' ) ) : home_url( '/contact/' );

	$ri_icon = function ( $key ) {
		return function_exists( 'ow_rental_icon' ) ? ow_rental_icon( $key ) : '';
	};

	$ri_arrow = '<svg class="ow-pe__arrow" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10h13M11 5l5 5-5 5"/></svg>';
	$ri_check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9.5"/><path d="M8 12.3l2.7 2.7L16.2 9.5"/></svg>';

	$ri_content = trim( (string) get_the_content() );
	?>

<style>
<?php
echo function_exists( 'ow_pe_styles' ) ? ow_pe_styles() : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo function_exists( 'ow_ri_styles' ) ? ow_ri_styles() : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</style>

<article class="ow-pe ow-ri">

  <!-- HERO -->
  <div class="ow-pe__hero">
    <?php if ( $ri_cover ) : ?>
      <div class="ow-pe__hero-photo">
        <?php echo wp_get_attachment_image( $ri_cover, 'full', false, array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
      </div>
    <?php endif; ?>
    <div class="ow-pe__hero-inner">
      <a class="ow-pe__back" href="<?php echo esc_url( $ri_back_url ); ?>">
        <?php echo $ri_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php echo esc_html( $ri_get( 'back_label' ) ); ?>
      </a>
      <div>
        <?php if ( '' !== $ri_badge ) : ?>
          <span class="ow-ri-tag ow-ri-tag--inline"><?php echo esc_html( $ri_badge ); ?></span>
        <?php else : ?>
          <div class="ow-pe__eyebrow"><?php echo esc_html( $ri_get( 'eyebrow' ) ); ?></div>
        <?php endif; ?>
      </div>
      <h1 class="ow-pe__title"><?php the_title(); ?></h1>
      <?php if ( '' !== $ri_intro ) : ?>
        <p class="ow-pe__lead"><?php echo nl2br( esc_html( $ri_intro ) ); ?></p>
      <?php endif; ?>
      <?php if ( '' !== $ri_price ) : ?>
        <p class="ow-ri-price"><?php echo esc_html( $ri_price ); ?></p>
      <?php endif; ?>

      <div class="ow-pe__hero-ctas">
        <a class="ow-pe__btn ow-pe__btn--primary" href="<?php echo esc_url( $ri_cta_url ); ?>">
          <?php echo esc_html( $ri_get( 'cta_label' ) ); ?> <?php echo $ri_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </a>
        <?php if ( ! empty( $ri_pack ) || ! empty( $ri_included ) ) : ?>
          <a class="ow-pe__btn ow-pe__btn--ghost" href="#included">What's Included</a>
        <?php endif; ?>
      </div>

      <?php if ( ! empty( $ri_highlights ) ) : ?>
        <div class="ow-ri__highlights" style="--n:<?php echo (int) count( $ri_highlights ); ?>;">
          <?php foreach ( $ri_highlights as $hl ) : ?>
            <div class="ow-ri__hl">
              <?php echo $ri_icon( $hl['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
              <strong><?php echo esc_html( $hl['text'] ); ?></strong>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ABOUT -->
  <?php if ( '' !== $ri_content ) : ?>
  <div class="ow-pe__section">
    <div class="ow-pe__inner">
      <h2 class="ow-pe__section-title"><?php echo esc_html( $ri_get( 'about_title' ) ); ?></h2>
      <div class="ow-pe__prose"><?php the_content(); ?></div>
    </div>
  </div>
  <?php endif; ?>

  <!-- WHAT'S IN THE PACKAGE + WHAT'S COVERED -->
  <?php if ( ! empty( $ri_pack ) || ! empty( $ri_included ) ) : ?>
  <div class="ow-pe__section ow-pe__section--alt" id="included">
    <div class="ow-pe__inner">
      <div class="ow-ri__split"<?php echo ( empty( $ri_pack ) || empty( $ri_included ) ) ? ' style="grid-template-columns:1fr;max-width:820px;"' : ''; ?>>
        <?php if ( ! empty( $ri_pack ) ) : ?>
          <div class="ow-ri__panel">
            <h2 class="ow-pe__section-title"><?php echo esc_html( $ri_get( 'pack_title' ) ); ?></h2>
            <ul class="ow-ri__pack">
              <?php foreach ( $ri_pack as $row ) : ?>
                <li>
                  <?php echo $ri_check; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                  <div>
                    <strong><?php echo esc_html( $row['label'] ); ?></strong>
                    <?php if ( '' !== $row['note'] ) : ?><span><?php echo esc_html( $row['note'] ); ?></span><?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <?php if ( ! empty( $ri_included ) ) : ?>
          <div class="ow-ri__panel">
            <h2 class="ow-pe__section-title"><?php echo esc_html( $ri_get( 'inc_title' ) ); ?></h2>
            <div class="ow-ri__inc">
              <?php foreach ( $ri_included as $row ) : ?>
                <div class="ow-ri__inc-item">
                  <?php echo $ri_icon( $row['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                  <span><?php echo esc_html( $row['label'] ); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- SPACE & SETUP -->
  <?php if ( ! empty( $ri_specs ) ) : ?>
  <div class="ow-pe__section">
    <div class="ow-pe__inner">
      <h2 class="ow-pe__section-title"><?php echo esc_html( $ri_get( 'spec_title' ) ); ?></h2>
      <dl class="ow-ri__specs">
        <?php foreach ( $ri_specs as $spec ) : ?>
          <div class="ow-ri__spec">
            <dt><?php echo esc_html( $spec['label'] ); ?></dt>
            <dd><?php echo esc_html( $spec['value'] ); ?></dd>
          </div>
        <?php endforeach; ?>
      </dl>
    </div>
  </div>
  <?php endif; ?>

  <!-- PHOTOS -->
  <?php if ( ! empty( $ri_photos ) ) : ?>
  <div class="ow-pe__section ow-pe__section--alt" id="photos">
    <div class="ow-pe__inner">
      <h2 class="ow-pe__section-title"><?php echo esc_html( $ri_get( 'photos_title' ) ); ?></h2>
      <div class="ow-pe__photos">
        <?php
        foreach ( $ri_photos as $photo_id ) :
	        $full = wp_get_attachment_image_url( $photo_id, 'full' );
	        $alt  = trim( (string) get_post_meta( $photo_id, '_wp_attachment_image_alt', true ) );
	        if ( '' === $alt ) {
		        $alt = get_the_title() . ' rental';
	        }
	        ?>
          <a class="ow-pe__photo" href="<?php echo esc_url( $full ); ?>" data-full="<?php echo esc_url( $full ); ?>" data-alt="<?php echo esc_attr( $alt ); ?>">
            <?php echo wp_get_attachment_image( $photo_id, 'large', false, array( 'loading' => 'lazy', 'alt' => $alt ) ); ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- QUESTIONS -->
  <?php if ( ! empty( $ri_faqs ) ) : ?>
  <div class="ow-pe__section">
    <div class="ow-pe__inner">
      <h2 class="ow-pe__section-title"><?php echo esc_html( $ri_get( 'faq_title' ) ); ?></h2>
      <div class="ow-ri__faqs">
        <?php foreach ( $ri_faqs as $index => $faq ) : ?>
          <details class="ow-ri__faq"<?php echo 0 === $index ? ' open' : ''; ?>>
            <summary><?php echo esc_html( $faq['q'] ); ?></summary>
            <p><?php echo nl2br( esc_html( $faq['a'] ) ); ?></p>
          </details>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- PAIR IT WITH -->
  <?php if ( ! empty( $ri_more ) ) : ?>
  <div class="ow-pe__section ow-pe__section--alt">
    <div class="ow-pe__inner">
      <div class="ow-pe__section-head">
        <h2 class="ow-pe__section-title"><?php echo esc_html( $ri_get( 'more_title' ) ); ?></h2>
        <a class="ow-pe__back" style="margin:0;" href="<?php echo esc_url( $ri_back_url ); ?>">
          <?php echo $ri_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          <?php echo esc_html( $ri_get( 'back_label' ) ); ?>
        </a>
      </div>
      <div class="ow-pe__grid">
        <?php foreach ( $ri_more as $card ) : ?>
          <a class="ow-pe__card ow-ri__card" href="<?php echo esc_url( $card['url'] ); ?>">
            <div class="ow-pe__card-media<?php echo $card['image_id'] ? '' : ' ow-pe__card-media--empty'; ?>">
              <?php
              echo $card['image_id']
	              ? wp_get_attachment_image( $card['image_id'], 'large', false, array( 'loading' => 'lazy' ) )
	              : $ri_icon( 'gamepad' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
              ?>
              <?php if ( '' !== $card['badge'] ) : ?>
                <span class="ow-ri-tag"><?php echo esc_html( $card['badge'] ); ?></span>
              <?php endif; ?>
            </div>
            <div class="ow-pe__card-body">
              <h3 class="ow-pe__card-name"><?php echo esc_html( $card['title'] ); ?></h3>
              <?php if ( ! empty( $card['highlights'] ) ) : ?>
                <div class="ow-ri-chips">
                  <?php foreach ( array_slice( $card['highlights'], 0, 3 ) as $hl ) : ?>
                    <span class="ow-ri-chip"><?php echo $ri_icon( $hl['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $hl['text'] ); ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <?php if ( '' !== $card['text'] ) : ?>
                <p class="ow-pe__card-blurb"><?php echo esc_html( $card['text'] ); ?></p>
              <?php endif; ?>
              <span class="ow-pe__card-more">View Details <?php echo $ri_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- CTA -->
  <div class="ow-pe__cta">
    <div class="ow-pe__cta-inner">
      <?php if ( '' !== $ri_get( 'cta_eyebrow' ) ) : ?>
        <div class="ow-pe__eyebrow"><?php echo esc_html( $ri_get( 'cta_eyebrow' ) ); ?></div>
      <?php endif; ?>
      <h2 class="ow-pe__cta-title"><?php echo esc_html( $ri_get( 'cta_title' ) ); ?></h2>
      <p class="ow-pe__cta-text"><?php echo esc_html( $ri_get( 'cta_text' ) ); ?></p>
      <div class="ow-pe__cta-buttons">
        <a class="ow-pe__btn ow-pe__btn--primary" href="<?php echo esc_url( $ri_cta_url ); ?>">
          <?php echo esc_html( $ri_get( 'cta_label' ) ); ?> <?php echo $ri_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </a>
        <a class="ow-pe__btn ow-pe__btn--ghost" href="<?php echo esc_url( $ri_back_url ); ?>">
          <?php echo esc_html( $ri_get( 'back_label' ) ); ?>
        </a>
      </div>
    </div>
  </div>

</article>

<?php if ( ! empty( $ri_photos ) && function_exists( 'ow_pe_lightbox_script' ) ) : ?>
<script>
<?php echo ow_pe_lightbox_script(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</script>
<?php endif; ?>

	<?php
endwhile;

get_footer();

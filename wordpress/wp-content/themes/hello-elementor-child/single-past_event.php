<?php
/**
 * Template — single-past_event.php
 *
 * One past event: /past-events/<name>/
 *
 * Every word and picture comes from the "Past Event Details" box on the entry
 * (mu-plugin overworld-event-gallery.php), and the main write-up from the
 * normal WordPress editor. Nothing here is required — an entry with a title,
 * a cover photo and a few pictures already renders a finished page.
 *
 * Sections, top to bottom:
 *   hero (cover photo, title, venue/date chips, highlights)
 *   → the write-up → games we brought → photo gallery
 *   → more past events → call to action
 *
 * Design language matches page-event-rental.php — the page these are reached
 * from — and the shared CSS lives in the mu-plugin (ow_pe_styles) because
 * archive-past_event.php renders the same cards.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$pe_id = get_the_ID();

	$pe_get = function ( $name ) use ( $pe_id ) {
		return function_exists( 'ow_pe_val' ) ? ow_pe_val( $pe_id, $name ) : '';
	};

	$pe_cover  = function_exists( 'ow_pe_cover_id' ) ? ow_pe_cover_id( $pe_id ) : 0;
	$pe_stats  = function_exists( 'ow_pe_stats' ) ? ow_pe_stats( $pe_id ) : array();
	$pe_games  = function_exists( 'ow_pe_games' ) ? ow_pe_games( $pe_id ) : array();
	$pe_photos = function_exists( 'ow_pe_photos' ) ? ow_pe_photos( $pe_id ) : array();
	$pe_facts  = function_exists( 'ow_pe_meta_parts' ) ? ow_pe_meta_parts( $pe_id ) : array();
	$pe_more   = function_exists( 'ow_pe_cards' ) ? ow_pe_cards( 3, false, $pe_id ) : array();

	$pe_eyebrow = trim( (string) get_post_meta( $pe_id, 'pe_tag', true ) );
	if ( '' === $pe_eyebrow ) {
		$pe_eyebrow = $pe_get( 'eyebrow' );
	}

	$pe_intro = trim( (string) get_post_meta( $pe_id, 'pe_intro', true ) );
	if ( '' === $pe_intro ) {
		$pe_intro = trim( (string) get_post_meta( $pe_id, 'pe_summary', true ) );
	}

	$pe_archive_url = function_exists( 'ow_pe_archive_url' ) ? ow_pe_archive_url() : home_url( '/past-events/' );

	// Not a field: the wording of the "back" link is the same on every event,
	// so it stays built in rather than becoming one more box to fill.
	$pe_defaults   = function_exists( 'ow_pe_defaults' ) ? ow_pe_defaults() : array();
	$pe_back_label = isset( $pe_defaults['back_label'] ) ? $pe_defaults['back_label'] : 'All Past Events';

	$pe_arrow = '<svg class="ow-pe__arrow" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10h13M11 5l5 5-5 5"/></svg>';
	$pe_pin   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11Z"/><circle cx="12" cy="10" r="2.6"/></svg>';
	$pe_clock = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.2 2"/></svg>';
	$pe_pad   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="7" width="19" height="10" rx="4"/><path d="M7.5 10.5v3M6 12h3M16 11h.01M18.5 13.5h.01"/></svg>';
	$pe_icons = array( $pe_pin, $pe_clock, $pe_pad );
	?>

<style>
<?php echo function_exists( 'ow_pe_styles' ) ? ow_pe_styles() : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</style>

<article class="ow-pe">

  <!-- HERO -->
  <div class="ow-pe__hero">
    <?php if ( $pe_cover ) : ?>
      <div class="ow-pe__hero-photo">
        <?php echo wp_get_attachment_image( $pe_cover, 'full', false, array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
      </div>
    <?php endif; ?>
    <div class="ow-pe__hero-inner">
      <a class="ow-pe__back" href="<?php echo esc_url( $pe_archive_url ); ?>">
        <?php echo $pe_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php echo esc_html( $pe_back_label ); ?>
      </a>
      <?php if ( '' !== $pe_eyebrow ) : ?>
        <div class="ow-pe__eyebrow"><?php echo esc_html( $pe_eyebrow ); ?></div>
      <?php endif; ?>
      <h1 class="ow-pe__title"><?php the_title(); ?></h1>
      <?php if ( '' !== $pe_intro ) : ?>
        <p class="ow-pe__lead"><?php echo nl2br( esc_html( $pe_intro ) ); ?></p>
      <?php endif; ?>

      <?php if ( ! empty( $pe_facts ) ) : ?>
        <div class="ow-pe__facts">
          <?php foreach ( $pe_facts as $index => $fact ) : ?>
            <span class="ow-pe__fact">
              <?php echo $pe_icons[ $index % count( $pe_icons ) ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
              <?php echo esc_html( $fact ); ?>
            </span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="ow-pe__hero-ctas">
        <?php if ( ! empty( $pe_photos ) ) : ?>
          <a class="ow-pe__btn ow-pe__btn--primary" href="#photos">
            See The Photos <?php echo $pe_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          </a>
        <?php endif; ?>
        <a class="ow-pe__btn ow-pe__btn--ghost" href="<?php echo esc_url( ow_pe_url( $pe_get( 'cta_url' ) ) ); ?>">
          <?php echo esc_html( $pe_get( 'cta_label' ) ); ?>
        </a>
      </div>

      <?php if ( ! empty( $pe_stats ) ) : ?>
        <div class="ow-pe__stats">
          <?php foreach ( $pe_stats as $stat ) : ?>
            <div class="ow-pe__stat">
              <?php if ( '' !== $stat['value'] ) : ?><strong><?php echo esc_html( $stat['value'] ); ?></strong><?php endif; ?>
              <span><?php echo esc_html( $stat['label'] ); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- THE WRITE-UP -->
  <?php
  $pe_content = trim( (string) get_the_content() );
  if ( '' !== $pe_content ) :
	  ?>
  <div class="ow-pe__section">
    <div class="ow-pe__inner">
      <h2 class="ow-pe__section-title"><?php echo esc_html( $pe_get( 'story_title' ) ); ?></h2>
      <div class="ow-pe__prose"><?php the_content(); ?></div>
    </div>
  </div>
  <?php endif; ?>

  <!-- GAMES WE BROUGHT -->
  <?php if ( ! empty( $pe_games ) ) : ?>
  <div class="ow-pe__section ow-pe__section--alt">
    <div class="ow-pe__inner">
      <h2 class="ow-pe__section-title"><?php echo esc_html( $pe_get( 'games_title' ) ); ?></h2>
      <div class="ow-pe__games" style="--cols:<?php echo (int) min( 4, count( $pe_games ) ); ?>;">
        <?php
        foreach ( $pe_games as $game ) :
	        $tag = '' !== $game['url'] ? 'a' : 'div';
	        ?>
          <<?php echo $tag; ?> class="ow-pe__game"<?php echo '' !== $game['url'] ? ' href="' . esc_url( $game['url'] ) . '"' : ''; ?>>
            <?php if ( $game['image_id'] ) : ?>
              <div class="ow-pe__game-media">
                <?php echo wp_get_attachment_image( $game['image_id'], 'medium_large', false, array( 'loading' => 'lazy' ) ); ?>
              </div>
            <?php endif; ?>
            <div class="ow-pe__game-body">
              <h3 class="ow-pe__game-name"><?php echo esc_html( $game['name'] ); ?></h3>
              <?php if ( '' !== $game['text'] ) : ?>
                <p class="ow-pe__game-text"><?php echo esc_html( $game['text'] ); ?></p>
              <?php endif; ?>
            </div>
          </<?php echo $tag; ?>>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- PHOTOS -->
  <?php if ( ! empty( $pe_photos ) ) : ?>
  <div class="ow-pe__section" id="photos">
    <div class="ow-pe__inner">
      <h2 class="ow-pe__section-title"><?php echo esc_html( $pe_get( 'photos_title' ) ); ?></h2>
      <div class="ow-pe__photos">
        <?php
        foreach ( $pe_photos as $photo_id ) :
	        $full = wp_get_attachment_image_url( $photo_id, 'full' );
	        $alt  = trim( (string) get_post_meta( $photo_id, '_wp_attachment_image_alt', true ) );
	        if ( '' === $alt ) {
		        $alt = get_the_title();
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

  <!-- MORE PAST EVENTS -->
  <?php if ( ! empty( $pe_more ) ) : ?>
  <div class="ow-pe__section ow-pe__section--alt">
    <div class="ow-pe__inner">
      <div class="ow-pe__section-head">
        <h2 class="ow-pe__section-title">More Events We Have Run</h2>
        <a class="ow-pe__back" style="margin:0;" href="<?php echo esc_url( $pe_archive_url ); ?>">
          <?php echo $pe_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          <?php echo esc_html( $pe_back_label ); ?>
        </a>
      </div>
      <div class="ow-pe__grid">
        <?php
        foreach ( $pe_more as $card ) :
	        include __DIR__ . '/parts/past-event-card.php';
        endforeach;
        ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- CTA -->
  <div class="ow-pe__cta">
    <div class="ow-pe__cta-inner">
      <?php if ( '' !== $pe_get( 'cta_eyebrow' ) ) : ?>
        <div class="ow-pe__eyebrow"><?php echo esc_html( $pe_get( 'cta_eyebrow' ) ); ?></div>
      <?php endif; ?>
      <h2 class="ow-pe__cta-title"><?php echo esc_html( $pe_get( 'cta_title' ) ); ?></h2>
      <p class="ow-pe__cta-text"><?php echo esc_html( $pe_get( 'cta_text' ) ); ?></p>
      <div class="ow-pe__cta-buttons">
        <a class="ow-pe__btn ow-pe__btn--primary" href="<?php echo esc_url( ow_pe_url( $pe_get( 'cta_url' ) ) ); ?>">
          <?php echo esc_html( $pe_get( 'cta_label' ) ); ?> <?php echo $pe_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </a>
        <a class="ow-pe__btn ow-pe__btn--ghost" href="<?php echo esc_url( ow_pe_url( $pe_get( 'cta_ghost_url' ) ) ); ?>">
          <?php echo esc_html( $pe_get( 'cta_ghost_label' ) ); ?>
        </a>
      </div>
    </div>
  </div>

</article>

<?php if ( ! empty( $pe_photos ) && function_exists( 'ow_pe_lightbox_script' ) ) : ?>
<script>
<?php echo ow_pe_lightbox_script(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</script>
<?php endif; ?>

	<?php
endwhile;

get_footer();

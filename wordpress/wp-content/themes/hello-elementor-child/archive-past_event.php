<?php
/**
 * Template — archive-past_event.php
 *
 * Every past event: /past-events/
 *
 * Reached from the gallery section on /event-rental/ and from the "All Past
 * Events" link on each event. Cards are the same partial the single template
 * uses, and the CSS comes from the mu-plugin (ow_pe_styles).
 *
 * The heading and intro are client-editable from the Equipment Rental page's
 * box ("Past Events Page — …"), because ACF free has no options screen.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$pe_copy     = function_exists( 'ow_pe_archive_copy' ) ? ow_pe_archive_copy() : array(
	'eyebrow' => '',
	'title'   => 'Past Events',
	'text'    => '',
);
$pe_defaults = function_exists( 'ow_pe_defaults' ) ? ow_pe_defaults() : array();

$pe_arrow = '<svg class="ow-pe__arrow" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10h13M11 5l5 5-5 5"/></svg>';

$pe_rental_url = home_url( '/event-rental/' );
$pe_can_edit   = current_user_can( 'edit_posts' );
$pe_add_url    = defined( 'OW_PE_POST_TYPE' ) ? admin_url( 'post-new.php?post_type=' . OW_PE_POST_TYPE ) : '';
?>

<style>
<?php echo function_exists( 'ow_pe_styles' ) ? ow_pe_styles() : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
  .ow-pe__pager{
    display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:44px;
  }
  .ow-pe__pager .page-numbers{
    display:inline-flex;align-items:center;justify-content:center;
    min-width:42px;height:42px;padding:0 14px;border-radius:999px;
    border:1px solid var(--line);color:var(--dim);
    font-family:'JetBrains Mono',ui-monospace,monospace;font-size:12px;
  }
  .ow-pe__pager .page-numbers:hover{border-color:rgba(195,251,51,.45);color:#fff;}
  .ow-pe__pager .page-numbers.current{background:var(--accent);color:#0a0a14;border-color:var(--accent);}
</style>

<section class="ow-pe">

  <div class="ow-pe__hero">
    <div class="ow-pe__hero-inner">
      <a class="ow-pe__back" href="<?php echo esc_url( $pe_rental_url ); ?>">
        <?php echo $pe_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        Equipment Rental
      </a>
      <?php if ( '' !== $pe_copy['eyebrow'] ) : ?>
        <div class="ow-pe__eyebrow"><?php echo esc_html( $pe_copy['eyebrow'] ); ?></div>
      <?php endif; ?>
      <h1 class="ow-pe__title"><?php echo esc_html( $pe_copy['title'] ); ?></h1>
      <?php if ( '' !== $pe_copy['text'] ) : ?>
        <p class="ow-pe__lead"><?php echo nl2br( esc_html( $pe_copy['text'] ) ); ?></p>
      <?php endif; ?>
      <div class="ow-pe__hero-ctas">
        <a class="ow-pe__btn ow-pe__btn--primary" href="<?php echo esc_url( $pe_rental_url ); ?>">
          See What We Rent <?php echo $pe_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </a>
        <a class="ow-pe__btn ow-pe__btn--ghost" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
          Get A Quote
        </a>
      </div>
    </div>
  </div>

  <div class="ow-pe__section">
    <div class="ow-pe__inner">
      <?php if ( have_posts() ) : ?>
        <div class="ow-pe__grid">
          <?php
          while ( have_posts() ) :
	          the_post();
	          $card = function_exists( 'ow_pe_card' ) ? ow_pe_card( get_post() ) : array();
	          include __DIR__ . '/parts/past-event-card.php';
          endwhile;
          ?>
        </div>

        <?php
        $pe_links = paginate_links(
	        array(
		        'type'      => 'list',
		        'prev_text' => 'Previous',
		        'next_text' => 'Next',
	        )
        );
        if ( $pe_links ) :
	        ?>
          <nav class="ow-pe__pager" aria-label="Past events, more pages">
            <?php echo wp_kses_post( $pe_links ); ?>
          </nav>
        <?php endif; ?>

      <?php else : ?>
        <div class="ow-pe__empty">
          <?php echo esc_html( isset( $pe_defaults['archive_empty'] ) ? $pe_defaults['archive_empty'] : '' ); ?>
          <?php if ( $pe_can_edit && '' !== $pe_add_url ) : ?>
            <br /><br />
            <a class="ow-pe__btn ow-pe__btn--primary" href="<?php echo esc_url( $pe_add_url ); ?>">
              Add A Past Event <?php echo $pe_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="ow-pe__cta">
    <div class="ow-pe__cta-inner">
      <div class="ow-pe__eyebrow">Plan Your Event With Us</div>
      <h2 class="ow-pe__cta-title">Your Event Could Be Next</h2>
      <p class="ow-pe__cta-text">Tell us your venue, date and headcount. We will come back with the games that fit the space, what each one needs and a quote — delivery, setup, crew and pack-down included.</p>
      <div class="ow-pe__cta-buttons">
        <a class="ow-pe__btn ow-pe__btn--primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
          Get A Quote <?php echo $pe_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </a>
        <a class="ow-pe__btn ow-pe__btn--ghost" href="<?php echo esc_url( $pe_rental_url ); ?>">
          See What We Rent
        </a>
      </div>
    </div>
  </div>

</section>

<?php
get_footer();

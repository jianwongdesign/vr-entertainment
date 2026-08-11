<?php
/**
 * Plugin Name: Overworld — Homepage Popups
 * Description: A "Popups" section where the client prepares homepage popups ahead of time: Facebook-poster image, optional heading and text, a button with their own label and link, a start/end validity window, an on/off switch and a display order. Popups render as one image modal on the homepage; when more than one is live the modal becomes a carousel with arrows, dots and keyboard navigation.
 * Author: Overworld
 * Version: 1.0.0
 *
 * WHY A POST TYPE AND NOT A REPEATER
 * ACF here is the free tier (6.8.4) — no Repeater, no Flexible Content, no
 * Options page. The rest of this codebase works around that with flat numbered
 * fields (outlet_act_1_title, outlet_act_2_title, ...), which caps the list at
 * whatever was hardcoded. Popups are open-ended and each needs its own dates
 * and button, so each popup is a post instead. That also gives the client the
 * familiar add/edit/trash flow and lets them prepare a popup weeks early and
 * leave it switched off.
 *
 * The post type is public => false. It has no URL, no archive and never enters
 * the sitemap — it exists only to be rendered inside the modal. It is also
 * absent from ow_seo_post_types(), so overworld-seo.php ignores it entirely.
 *
 * WHY THE DATE CHECK HAPPENS TWICE
 * LiteSpeed caches the homepage HTML. A popup filtered out server-side is only
 * filtered at the moment the page is generated, so a cached copy could keep
 * serving a popup hours after it expired. Every slide therefore also carries
 * data-start/data-end unix timestamps and the script re-checks them before
 * opening. Cache purges on save cover the common case; the JS check covers a
 * window that rolls over while a cached page is still being served.
 *
 * VALIDITY WINDOW
 * Dates are inclusive and read in Asia/Singapore, matching
 * overworld-promo-countdown.php: a start date begins 00:00:00 that day, an end
 * date runs to 23:59:59 that day. Either can be left blank — no start means
 * "live as soon as it is switched on", no end means "runs until switched off".
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OW_POPUP_VERSION = '1.0.0';
const OW_POPUP_CPT     = 'ow_popup';
const OW_POPUP_TZ      = 'Asia/Singapore';

/* =====================================================================
   1. The Popups post type
   ===================================================================== */

add_action(
	'init',
	function () {
		register_post_type(
			OW_POPUP_CPT,
			array(
				'labels'              => array(
					'name'                  => 'Popups',
					'singular_name'         => 'Popup',
					'menu_name'             => 'Popups',
					'add_new'               => 'Add Popup',
					'add_new_item'          => 'Add Popup',
					'edit_item'             => 'Edit Popup',
					'new_item'              => 'New Popup',
					'view_item'             => 'View Popup',
					'search_items'          => 'Search Popups',
					'not_found'             => 'No popups yet',
					'not_found_in_trash'    => 'No popups in Trash',
					'all_items'             => 'All Popups',
					'item_published'        => 'Popup saved.',
					'item_updated'          => 'Popup updated.',
				),
				// No front-end URL of its own — it only ever renders in the modal.
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'rewrite'             => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_rest'        => false,
				'menu_position'       => 26,
				'menu_icon'           => 'dashicons-format-image',
				// page-attributes gives the client a drag-free "Order" box as a
				// fallback; the Display order field below is the one documented.
				'supports'            => array( 'title', 'page-attributes' ),
				'capability_type'     => 'post',
			)
		);

		// Facebook poster ratio. Cropped so an off-ratio upload still fills the
		// frame rather than letterboxing.
		add_image_size( 'ow_popup', 1200, 630, true );
	}
);

/* =====================================================================
   2. Fields
   ===================================================================== */

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'    => 'group_ow_popup',
				'title'  => 'Popup',
				'fields' => array(

					array(
						'key'           => 'field_ow_popup_enabled',
						'label'         => 'Show this popup',
						'name'          => 'ow_popup_enabled',
						'type'          => 'true_false',
						'ui'            => 1,
						'ui_on_text'    => 'On',
						'ui_off_text'   => 'Off',
						'default_value' => 0,
						'instructions'  => 'Off by default so a popup can be built and checked before it goes anywhere near the homepage. A popup only appears when this is On <em>and</em> today falls inside the dates below.',
					),

					array(
						'key'           => 'field_ow_popup_image',
						'label'         => 'Poster image',
						'name'          => 'ow_popup_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'library'       => 'all',
						'mime_types'    => 'jpg,jpeg,png,webp',
						'required'      => 1,
						'instructions'  => 'Facebook poster size — <strong>1200 × 630 px</strong> (1.91:1 landscape). Other sizes still work: the image is centre-cropped to that shape, so keep faces and text away from the edges.',
					),

					array(
						'key'          => 'field_ow_popup_heading',
						'label'        => 'Heading',
						'name'         => 'ow_popup_heading',
						'type'         => 'text',
						'instructions' => 'Optional. Shown under the image. Leave blank for a poster-only popup.',
					),

					array(
						'key'          => 'field_ow_popup_text',
						'label'        => 'Text',
						'name'         => 'ow_popup_text',
						'type'         => 'textarea',
						'rows'         => 3,
						'new_lines'    => '',
						'instructions' => 'Optional. One or two short lines read best.',
					),

					array(
						'key'          => 'field_ow_popup_btn_label',
						'label'        => 'Button label',
						'name'         => 'ow_popup_btn_label',
						'type'         => 'text',
						'placeholder'  => 'Book Now',
						'instructions' => 'Your own wording — "Book Now", "See the deal", "Grab a slot". Leave blank and no button is shown.',
					),

					array(
						'key'          => 'field_ow_popup_btn_url',
						'label'        => 'Button link',
						'name'         => 'ow_popup_btn_url',
						'type'         => 'url',
						'placeholder'  => 'https://overworld.com.sg/booking/',
						'instructions' => 'Where the button goes. Needed for the button to appear.',
					),

					array(
						'key'           => 'field_ow_popup_btn_blank',
						'label'         => 'Open link in a new tab',
						'name'          => 'ow_popup_btn_blank',
						'type'          => 'true_false',
						'ui'            => 1,
						'default_value' => 0,
						'instructions'  => 'Leave off for pages on this site. Turn on for anything external.',
					),

					array(
						'key'            => 'field_ow_popup_start',
						'label'          => 'Start date',
						'name'           => 'ow_popup_start',
						'type'           => 'date_picker',
						'display_format' => 'd/m/Y',
						'return_format'  => 'Ymd',
						'first_day'      => 1,
						'instructions'   => 'First day the popup may show, from 00:00. Leave blank to start the moment it is switched on.',
					),

					array(
						'key'            => 'field_ow_popup_end',
						'label'          => 'End date',
						'name'           => 'ow_popup_end',
						'type'           => 'date_picker',
						'display_format' => 'd/m/Y',
						'return_format'  => 'Ymd',
						'first_day'      => 1,
						'instructions'   => 'Last day the popup shows — it runs to 23:59 that day, so the date itself is included. Leave blank to run until switched off.',
					),

					array(
						'key'           => 'field_ow_popup_order',
						'label'         => 'Display order',
						'name'          => 'ow_popup_order',
						'type'          => 'number',
						'default_value' => 0,
						'step'          => 1,
						'instructions'  => 'Which popup comes first when several are live at once. Lower numbers lead — 1 shows before 2. Ties fall back to the newest.',
					),

					array(
						'key'           => 'field_ow_popup_frequency',
						'label'         => 'How often per visitor',
						'name'          => 'ow_popup_frequency',
						'type'          => 'select',
						'choices'       => array(
							'day'     => 'Once a day',
							'session' => 'Once per browsing session',
							'always'  => 'Every time they land on the homepage',
						),
						'default_value' => 'day',
						'return_format' => 'value',
						'allow_null'    => 0,
						'instructions'  => 'Once a visitor closes the popup it stays closed for this long. When several popups are live the most frequent setting among them wins, since they all share one window. Adding a new live popup resets everyone regardless.',
					),
				),

				'location'        => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => OW_POPUP_CPT,
						),
					),
				),
				'menu_order'      => 0,
				'position'        => 'normal',
				'style'           => 'default',
				'label_placement' => 'top',
				'active'          => true,
				'description'     => 'Everything the homepage popup needs.',
			)
		);
	}
);

/* =====================================================================
   3. Validity helpers
   ===================================================================== */

/**
 * Start/end unix timestamps for a popup's validity window.
 *
 * ACF date_picker stores Ymd. A start date opens at 00:00:00 and an end date
 * closes at 23:59:59 on that day, both in Singapore time, so the window reads
 * the way a person would say it out loud.
 *
 * @param int $post_id Popup ID.
 * @return array{0: int|null, 1: int|null} [start, end], either may be null.
 */
function ow_popup_window( $post_id ) {
	$tz    = new DateTimeZone( OW_POPUP_TZ );
	$parse = static function ( $raw, $end_of_day ) use ( $tz ) {
		if ( ! $raw || ! preg_match( '/^\d{8}$/', (string) $raw ) ) {
			return null;
		}
		$dt = DateTime::createFromFormat( 'Ymd', (string) $raw, $tz );
		if ( ! $dt ) {
			return null;
		}
		if ( $end_of_day ) {
			$dt->setTime( 23, 59, 59 );
		} else {
			$dt->setTime( 0, 0, 0 );
		}
		return $dt->getTimestamp();
	};

	return array(
		$parse( get_post_meta( $post_id, 'ow_popup_start', true ), false ),
		$parse( get_post_meta( $post_id, 'ow_popup_end', true ), true ),
	);
}

/**
 * Whether a popup is switched on.
 *
 * @param int $post_id Popup ID.
 * @return bool
 */
function ow_popup_is_enabled( $post_id ) {
	return '1' === (string) get_post_meta( $post_id, 'ow_popup_enabled', true );
}

/**
 * Plain-language state, used by the admin list and nothing else.
 *
 * @param int $post_id Popup ID.
 * @return string One of: off, scheduled, expired, live.
 */
function ow_popup_status( $post_id ) {
	if ( ! ow_popup_is_enabled( $post_id ) ) {
		return 'off';
	}
	list( $start, $end ) = ow_popup_window( $post_id );
	$now                 = time();

	if ( null !== $start && $now < $start ) {
		return 'scheduled';
	}
	if ( null !== $end && $now > $end ) {
		return 'expired';
	}
	return 'live';
}

/**
 * Whether this request is an editor previewing popups.
 *
 * Lets the client build a popup, leave it switched off, and still see exactly
 * how it will look on the homepage before anyone else does — add
 * ?ow_popup_preview=1 to the homepage URL while logged in. Preview ignores the
 * on/off switch and the dates, and never writes a dismissal, so it can be
 * reloaded freely.
 *
 * @return bool
 */
function ow_popup_is_preview() {
	return isset( $_GET['ow_popup_preview'] ) && current_user_can( 'edit_posts' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

/**
 * Every popup that should render right now, in display order.
 *
 * Sorting happens in PHP rather than through meta_query/orderby because the
 * order field is stored as a string; a numeric meta sort would need casting
 * and would still put popups with no value in an arbitrary spot.
 *
 * @param bool $preview Ignore the on/off switch and the validity window.
 * @return WP_Post[]
 */
function ow_popup_active( $preview = false ) {
	$args = array(
		'post_type'        => OW_POPUP_CPT,
		'post_status'      => 'publish',
		'posts_per_page'   => -1,
		'no_found_rows'    => true,
		'suppress_filters' => false,
	);

	if ( ! $preview ) {
		$args['meta_query'] = array(
			array(
				'key'   => 'ow_popup_enabled',
				'value' => '1',
			),
		);
	}

	/**
	 * Query arguments behind the popup list.
	 *
	 * @param array $args    get_posts() arguments.
	 * @param bool  $preview Whether this is an editor preview.
	 */
	$args  = apply_filters( 'ow_popup_query_args', $args, $preview );
	$posts = get_posts( $args );

	$live = array();
	foreach ( $posts as $post ) {
		if ( ! $preview && 'live' !== ow_popup_status( $post->ID ) ) {
			continue;
		}
		if ( ! get_post_meta( $post->ID, 'ow_popup_image', true ) ) {
			continue; // Nothing to show without the poster.
		}
		$live[] = $post;
	}

	usort(
		$live,
		static function ( $a, $b ) {
			$oa = (int) get_post_meta( $a->ID, 'ow_popup_order', true );
			$ob = (int) get_post_meta( $b->ID, 'ow_popup_order', true );
			if ( $oa !== $ob ) {
				return $oa <=> $ob;
			}
			if ( $a->menu_order !== $b->menu_order ) {
				return $a->menu_order <=> $b->menu_order;
			}
			return strcmp( $b->post_date, $a->post_date ); // Newest first on a tie.
		}
	);

	return $live;
}

/**
 * The shared frequency for a set of popups.
 *
 * They share one modal, so they share one dismissal. The most frequent setting
 * wins — if any live campaign is set to show every visit, suppressing it
 * because an unrelated popup said "once a day" would be the wrong call.
 *
 * @param WP_Post[] $popups Active popups.
 * @return string always|session|day
 */
function ow_popup_frequency( array $popups ) {
	$rank  = array(
		'always'  => 0,
		'session' => 1,
		'day'     => 2,
	);
	$best  = 'day';
	$score = 2;

	foreach ( $popups as $post ) {
		$freq = (string) get_post_meta( $post->ID, 'ow_popup_frequency', true );
		if ( ! isset( $rank[ $freq ] ) ) {
			continue;
		}
		if ( $rank[ $freq ] < $score ) {
			$score = $rank[ $freq ];
			$best  = $freq;
		}
	}
	return $best;
}

/* =====================================================================
   4. Front-end render
   ===================================================================== */

/**
 * Whether the popup should render on this request.
 *
 * Homepage only, per the brief. Filterable so extending it to other pages
 * later does not mean editing this plugin.
 *
 * @return bool
 */
function ow_popup_should_render() {
	$should = is_front_page() && ! is_admin() && ! is_feed() && ! is_embed();
	return (bool) apply_filters( 'ow_popup_should_render', $should );
}

add_action(
	'wp_footer',
	function () {
		if ( ! ow_popup_should_render() ) {
			return;
		}

		$preview = ow_popup_is_preview();
		$popups  = ow_popup_active( $preview );
		if ( empty( $popups ) ) {
			return;
		}

		$multi = count( $popups ) > 1;
		// A preview must reopen on every reload, otherwise the first dismissal
		// would hide it for the rest of the day and look like a broken feature.
		$freq = $preview ? 'always' : ow_popup_frequency( $popups );

		// Identity of the current live set. When it changes — a popup added,
		// switched on, or expired — every visitor sees the modal again even if
		// they dismissed the previous set.
		$ids       = wp_list_pluck( $popups, 'ID' );
		$set_token = implode( '-', $ids );

		// How long the modal waits before opening. Instant popups read as an
		// error; a beat lets the homepage paint first.
		$delay = (int) apply_filters( 'ow_popup_delay_ms', 1200 );

		ow_popup_styles();
		?>
		<div class="ow-pop<?php echo $preview ? ' ow-pop--preview' : ''; ?>" id="ow-pop" hidden
			data-set="<?php echo esc_attr( $set_token ); ?>"
			data-frequency="<?php echo esc_attr( $freq ); ?>"
			data-preview="<?php echo $preview ? '1' : ''; ?>"
			data-delay="<?php echo esc_attr( (string) ( $preview ? 0 : $delay ) ); ?>">

			<div class="ow-pop__backdrop" data-ow-pop-close></div>

			<div class="ow-pop__dialog" role="dialog" aria-modal="true"
				aria-labelledby="ow-pop-label" tabindex="-1">

				<?php if ( $preview ) : ?>
					<p class="ow-pop__preview-flag">Preview — showing every popup, ignoring the on/off switch and dates. Only you can see this.</p>
				<?php endif; ?>

				<h2 class="ow-pop__sr" id="ow-pop-label">
					<?php echo esc_html( $multi ? 'Latest from Overworld' : get_the_title( $popups[0]->ID ) ); ?>
				</h2>

				<button type="button" class="ow-pop__close" data-ow-pop-close aria-label="Close">
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
						<path d="M6 6l12 12M18 6L6 18" />
					</svg>
				</button>

				<div class="ow-pop__track">
					<?php foreach ( $popups as $i => $post ) : ?>
						<?php ow_popup_slide( $post, (int) $i ); ?>
					<?php endforeach; ?>
				</div>

				<?php if ( $multi ) : ?>
					<div class="ow-pop__nav">
						<button type="button" class="ow-pop__arrow" data-ow-pop-prev aria-label="Previous">
							<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
								<path d="M15 5l-7 7 7 7" />
							</svg>
						</button>

						<div class="ow-pop__dots" role="tablist" aria-label="Choose popup">
							<?php foreach ( $popups as $i => $post ) : ?>
								<button type="button"
									class="ow-pop__dot<?php echo 0 === (int) $i ? ' is-active' : ''; ?>"
									data-ow-pop-go="<?php echo esc_attr( (string) $i ); ?>"
									role="tab"
									aria-selected="<?php echo 0 === (int) $i ? 'true' : 'false'; ?>"
									aria-label="<?php echo esc_attr( sprintf( '%s (%d of %d)', get_the_title( $post->ID ), (int) $i + 1, count( $popups ) ) ); ?>">
									<span></span>
								</button>
							<?php endforeach; ?>
						</div>

						<button type="button" class="ow-pop__arrow" data-ow-pop-next aria-label="Next">
							<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
								<path d="M9 5l7 7-7 7" />
							</svg>
						</button>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		ow_popup_script();
	},
	20
);

/**
 * One slide.
 *
 * @param WP_Post $post  Popup.
 * @param int     $index Zero-based position.
 * @return void
 */
function ow_popup_slide( $post, $index ) {
	$image  = get_post_meta( $post->ID, 'ow_popup_image', true );
	$img_id = is_array( $image ) ? (int) ( $image['ID'] ?? 0 ) : (int) $image;
	if ( ! $img_id ) {
		return;
	}

	$heading   = trim( (string) get_post_meta( $post->ID, 'ow_popup_heading', true ) );
	$text      = trim( (string) get_post_meta( $post->ID, 'ow_popup_text', true ) );
	$btn_label = trim( (string) get_post_meta( $post->ID, 'ow_popup_btn_label', true ) );
	$btn_url   = trim( (string) get_post_meta( $post->ID, 'ow_popup_btn_url', true ) );
	$btn_blank = '1' === (string) get_post_meta( $post->ID, 'ow_popup_btn_blank', true );

	list( $start, $end ) = ow_popup_window( $post->ID );

	// Alt text comes from the attachment so the client controls it in the
	// Media library rather than through a field that duplicates it.
	$alt = trim( (string) get_post_meta( $img_id, '_wp_attachment_image_alt', true ) );
	if ( '' === $alt ) {
		$alt = $heading ? $heading : get_the_title( $post->ID );
	}
	?>
	<article class="ow-pop__slide<?php echo 0 === $index ? ' is-active' : ''; ?>"
		data-index="<?php echo esc_attr( (string) $index ); ?>"
		data-start="<?php echo esc_attr( null === $start ? '' : (string) $start ); ?>"
		data-end="<?php echo esc_attr( null === $end ? '' : (string) $end ); ?>"
		<?php echo 0 === $index ? '' : 'aria-hidden="true"'; ?>>

		<div class="ow-pop__media">
			<?php
			echo wp_get_attachment_image(
				$img_id,
				'ow_popup',
				false,
				array(
					'alt'      => $alt,
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
			?>
		</div>

		<?php if ( $heading || $text || ( $btn_label && $btn_url ) ) : ?>
			<div class="ow-pop__body">
				<?php if ( $heading ) : ?>
					<h3 class="ow-pop__title"><?php echo esc_html( $heading ); ?></h3>
				<?php endif; ?>

				<?php if ( $text ) : ?>
					<p class="ow-pop__text"><?php echo nl2br( esc_html( $text ) ); ?></p>
				<?php endif; ?>

				<?php if ( $btn_label && $btn_url ) : ?>
					<a class="ow-pop__btn" href="<?php echo esc_url( $btn_url ); ?>"
						<?php echo $btn_blank ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
						<?php echo esc_html( $btn_label ); ?>
						<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
							<path d="M5 12h14M13 6l6 6-6 6" />
						</svg>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</article>
	<?php
}

/* =====================================================================
   5. Styles
   ===================================================================== */

/**
 * Scoped styles, printed only when a popup is actually rendering.
 *
 * Palette and type match the info/pricing templates: lava orange on near
 * black, Anton display face, Helvetica body.
 *
 * @return void
 */
function ow_popup_styles() {
	?>
	<style id="ow-pop-css">
	.ow-pop{
		--pop-bg:#141417; --pop-line:rgba(255,255,255,.10);
		--pop-text:#f4f4f5; --pop-dim:#a1a1aa;
		--pop-accent:#ff5a1f; --pop-accent-hi:#ff7a4a;
		position:fixed; inset:0; z-index:100000;
		display:flex; align-items:center; justify-content:center;
		padding:24px 20px;
		font-family:'Helvetica W01','Helvetica Neue',Helvetica,Arial,sans-serif;
	}
	.ow-pop[hidden]{display:none;}
	.ow-pop *,.ow-pop *::before,.ow-pop *::after{box-sizing:border-box;}

	.ow-pop__backdrop{
		position:absolute; inset:0;
		background:rgba(6,6,8,.82);
		-webkit-backdrop-filter:blur(6px); backdrop-filter:blur(6px);
		opacity:0; transition:opacity .28s ease;
	}
	.ow-pop.is-open .ow-pop__backdrop{opacity:1;}

	.ow-pop__dialog{
		position:relative; width:min(560px,100%);
		max-height:calc(100vh - 48px); max-height:calc(100dvh - 48px);
		overflow-y:auto; overscroll-behavior:contain;
		background:var(--pop-bg);
		border:1px solid var(--pop-line); border-radius:20px;
		box-shadow:0 30px 80px -20px rgba(0,0,0,.9),0 0 70px -34px rgba(255,90,31,.55);
		opacity:0; transform:translateY(14px) scale(.97);
		transition:opacity .3s ease,transform .3s cubic-bezier(.2,.8,.3,1);
	}
	.ow-pop.is-open .ow-pop__dialog{opacity:1; transform:none;}
	.ow-pop__dialog:focus{outline:none;}

	.ow-pop__sr{
		position:absolute; width:1px; height:1px; margin:-1px;
		padding:0; overflow:hidden; clip:rect(0 0 0 0); white-space:nowrap; border:0;
	}

	.ow-pop__close{
		position:absolute; top:12px; right:12px; z-index:3;
		width:40px; height:40px; display:flex; align-items:center; justify-content:center;
		border:1px solid rgba(255,255,255,.14); border-radius:50%;
		background:rgba(10,10,12,.62);
		-webkit-backdrop-filter:blur(8px); backdrop-filter:blur(8px);
		color:#fff; cursor:pointer; padding:0;
		transition:background .2s ease,border-color .2s ease,transform .2s ease;
	}
	.ow-pop__close:hover{background:rgba(10,10,12,.9); border-color:var(--pop-accent); transform:rotate(90deg);}
	.ow-pop__close svg{width:18px; height:18px; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round;}

	/* Slides stack in one grid cell: the dialog takes the height of the tallest
	   so moving between popups never makes the box jump. */
	.ow-pop__track{display:grid;}
	.ow-pop__slide{
		grid-area:1/1; min-width:0;
		opacity:0; visibility:hidden; pointer-events:none;
		transition:opacity .32s ease;
	}
	.ow-pop__slide.is-active{opacity:1; visibility:visible; pointer-events:auto;}

	.ow-pop__media{
		aspect-ratio:1200/630; width:100%; overflow:hidden;
		border-radius:20px 20px 0 0; background:#0b0b0c;
	}
	.ow-pop__media img{width:100%; height:100%; object-fit:cover; display:block;}

	.ow-pop__body{padding:26px 28px 28px; text-align:center;}
	.ow-pop__title{
		font-family:'Anton','Bebas Neue',sans-serif;
		font-size:clamp(24px,5vw,32px); line-height:1.1;
		letter-spacing:.01em; text-transform:uppercase;
		color:var(--pop-text); margin:0;
	}
	.ow-pop__text{
		margin:12px 0 0; font-size:15px; line-height:1.6; color:var(--pop-dim);
	}
	.ow-pop__btn{
		display:inline-flex; align-items:center; gap:10px;
		margin-top:22px; padding:14px 30px;
		background:var(--pop-accent); color:#fff !important;
		font-size:14px; font-weight:700; letter-spacing:.04em; text-transform:uppercase;
		text-decoration:none; border-radius:999px;
		box-shadow:0 12px 30px -10px rgba(255,90,31,.7);
		transition:transform .2s ease,gap .2s ease,background .2s ease;
	}
	.ow-pop__btn:hover{background:var(--pop-accent-hi); transform:translateY(-2px); gap:14px;}
	.ow-pop__btn svg{width:16px; height:16px; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;}

	.ow-pop__nav{
		display:flex; align-items:center; justify-content:center; gap:14px;
		padding:0 20px 22px;
	}
	.ow-pop__arrow{
		width:38px; height:38px; flex:0 0 38px;
		display:flex; align-items:center; justify-content:center;
		border:1px solid var(--pop-line); border-radius:50%;
		background:rgba(255,255,255,.04); color:var(--pop-text);
		cursor:pointer; padding:0;
		transition:background .2s ease,border-color .2s ease,color .2s ease;
	}
	.ow-pop__arrow:hover{background:rgba(255,90,31,.14); border-color:var(--pop-accent); color:var(--pop-accent-hi);}
	.ow-pop__arrow svg{width:17px; height:17px; fill:none; stroke:currentColor; stroke-width:2.2; stroke-linecap:round; stroke-linejoin:round;}

	.ow-pop__dots{display:flex; align-items:center; gap:9px;}
	.ow-pop__dot{
		width:22px; height:22px; padding:0; border:0; background:none;
		display:flex; align-items:center; justify-content:center; cursor:pointer;
	}
	.ow-pop__dot span{
		display:block; width:7px; height:7px; border-radius:50%;
		background:rgba(255,255,255,.26);
		transition:background .2s ease,width .2s ease,border-radius .2s ease;
	}
	.ow-pop__dot:hover span{background:rgba(255,255,255,.5);}
	.ow-pop__dot.is-active span{width:20px; border-radius:4px; background:var(--pop-accent);}

	.ow-pop__close:focus-visible,
	.ow-pop__arrow:focus-visible,
	.ow-pop__dot:focus-visible,
	.ow-pop__btn:focus-visible{
		outline:2px solid var(--pop-accent-hi); outline-offset:3px;
	}

	.ow-pop__preview-flag{
		margin:0; padding:10px 46px 10px 16px;
		background:#8a6100; color:#fff;
		font-size:11.5px; line-height:1.45; letter-spacing:.02em;
		border-radius:20px 20px 0 0;
	}
	.ow-pop--preview .ow-pop__media{border-radius:0;}

	body.ow-pop-open{overflow:hidden;}

	@media (max-width:560px){
		.ow-pop{padding:14px;}
		.ow-pop__dialog{border-radius:16px;}
		.ow-pop__media{border-radius:16px 16px 0 0;}
		.ow-pop__body{padding:22px 20px 24px;}
		.ow-pop__btn{width:100%; justify-content:center;}
		.ow-pop__nav{padding:0 16px 18px; gap:10px;}
	}

	@media (prefers-reduced-motion:reduce){
		.ow-pop__backdrop,.ow-pop__dialog,.ow-pop__slide,
		.ow-pop__btn,.ow-pop__close,.ow-pop__arrow,.ow-pop__dot span{
			transition:none !important;
		}
		.ow-pop__close:hover{transform:none;}
	}
	</style>
	<?php
}

/* =====================================================================
   6. Behaviour
   ===================================================================== */

/**
 * The modal script. Self-contained, no dependencies, runs once.
 *
 * @return void
 */
function ow_popup_script() {
	?>
	<script id="ow-pop-js">
	(function () {
		var root = document.getElementById('ow-pop');
		if (!root || root.dataset.owReady) { return; }
		root.dataset.owReady = '1';

		var dialog = root.querySelector('.ow-pop__dialog');
		var slides = Array.prototype.slice.call(root.querySelectorAll('.ow-pop__slide'));
		var dots   = Array.prototype.slice.call(root.querySelectorAll('.ow-pop__dot'));
		var key    = 'ow_pop_seen';
		var freq   = root.dataset.frequency || 'day';
		var token  = root.dataset.set || '';
		var opener = null;
		var index  = 0;

		/* The server already dropped popups outside their window, but this page
		   may be a cached copy generated before one expired. Re-check here.
		   Preview deliberately shows everything, so it skips this. */
		if (!root.dataset.preview) {
			var now = Math.floor(Date.now() / 1000);
			slides = slides.filter(function (el) {
				var s = parseInt(el.dataset.start, 10);
				var e = parseInt(el.dataset.end, 10);
				if (!isNaN(s) && now < s) { el.remove(); return false; }
				if (!isNaN(e) && now > e) { el.remove(); return false; }
				return true;
			});
			if (!slides.length) { root.remove(); return; }
		}

		/* Slides may have been dropped above. Keep only the dots that still have
		   a slide behind them, then renumber so dot N maps to slides[N]. */
		dots = dots.filter(function (d) {
			var target = parseInt(d.dataset.owPopGo, 10);
			var keep = slides.some(function (s) {
				return parseInt(s.dataset.index, 10) === target;
			});
			if (!keep) { d.remove(); }
			return keep;
		});
		dots.forEach(function (d, i) { d.dataset.owPopGo = String(i); });

		var nav = root.querySelector('.ow-pop__nav');
		if (nav && slides.length < 2) { nav.remove(); dots = []; }

		function store() {
			try {
				return freq === 'session' ? window.sessionStorage : window.localStorage;
			} catch (err) { return null; }
		}

		function dismissed() {
			if (freq === 'always') { return false; }
			var s = store();
			if (!s) { return false; }
			try {
				var raw = s.getItem(key);
				if (!raw) { return false; }
				var saved = JSON.parse(raw);
				if (saved.token !== token) { return false; }   // New set — show again.
				if (freq === 'session') { return true; }
				return (Date.now() - saved.at) < 86400000;      // 24h.
			} catch (err) { return false; }
		}

		function remember() {
			if (freq === 'always') { return; }
			var s = store();
			if (!s) { return; }
			try {
				s.setItem(key, JSON.stringify({ token: token, at: Date.now() }));
			} catch (err) { /* private mode — show again next time, harmless. */ }
		}

		function go(next) {
			if (!slides.length) { return; }
			index = (next + slides.length) % slides.length;
			slides.forEach(function (el, i) {
				var on = i === index;
				el.classList.toggle('is-active', on);
				/* inert keeps the off-screen slides out of the tab order.
				   visibility:hidden alone does not — their buttons stay
				   focusable and the trap would tab into an invisible slide. */
				if (on) {
					el.removeAttribute('aria-hidden');
					el.removeAttribute('inert');
				} else {
					el.setAttribute('aria-hidden', 'true');
					el.setAttribute('inert', '');
				}
			});
			dots.forEach(function (d, i) {
				var on = i === index;
				d.classList.toggle('is-active', on);
				d.setAttribute('aria-selected', on ? 'true' : 'false');
			});
		}

		function focusables() {
			return Array.prototype.slice.call(dialog.querySelectorAll(
				'a[href],button:not([disabled]),[tabindex]:not([tabindex="-1"])'
			)).filter(function (el) {
				if (el.offsetParent === null) { return false; }
				/* Belt and braces alongside inert, for browsers without it. */
				var slide = el.closest ? el.closest('.ow-pop__slide') : null;
				return !slide || slide.classList.contains('is-active');
			});
		}

		function onKey(e) {
			if (e.key === 'Escape') { close(); return; }
			if (slides.length > 1) {
				if (e.key === 'ArrowRight') { go(index + 1); return; }
				if (e.key === 'ArrowLeft') { go(index - 1); return; }
			}
			if (e.key !== 'Tab') { return; }
			var items = focusables();
			if (!items.length) { return; }
			var first = items[0];
			var last  = items[items.length - 1];
			if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
			else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
		}

		function open() {
			opener = document.activeElement;
			root.hidden = false;
			document.body.classList.add('ow-pop-open');
			requestAnimationFrame(function () { root.classList.add('is-open'); });
			document.addEventListener('keydown', onKey);
			var target = root.querySelector('.ow-pop__close');
			if (target) { target.focus(); }
		}

		function close() {
			root.classList.remove('is-open');
			document.body.classList.remove('ow-pop-open');
			document.removeEventListener('keydown', onKey);
			remember();
			window.setTimeout(function () { root.hidden = true; }, 300);
			if (opener && typeof opener.focus === 'function') { opener.focus(); }
		}

		root.querySelectorAll('[data-ow-pop-close]').forEach(function (el) {
			el.addEventListener('click', close);
		});
		var prev = root.querySelector('[data-ow-pop-prev]');
		var next = root.querySelector('[data-ow-pop-next]');
		if (prev) { prev.addEventListener('click', function () { go(index - 1); }); }
		if (next) { next.addEventListener('click', function () { go(index + 1); }); }
		dots.forEach(function (d) {
			d.addEventListener('click', function () { go(parseInt(d.dataset.owPopGo, 10) || 0); });
		});

		/* Clicking the button counts as engagement — don't nag them again. */
		root.querySelectorAll('.ow-pop__btn').forEach(function (b) {
			b.addEventListener('click', remember);
		});

		go(0);
		if (!dismissed()) {
			window.setTimeout(open, parseInt(root.dataset.delay, 10) || 1200);
		}
	})();
	</script>
	<?php
}

/* =====================================================================
   7. Admin list
   ===================================================================== */

add_filter(
	'manage_' . OW_POPUP_CPT . '_posts_columns',
	function ( $cols ) {
		$out = array();
		foreach ( $cols as $k => $v ) {
			if ( 'title' === $k ) {
				$out['ow_thumb'] = 'Poster';
			}
			$out[ $k ] = $v;
			if ( 'title' === $k ) {
				$out['ow_state']  = 'Status';
				$out['ow_window'] = 'Shows';
				$out['ow_order']  = 'Order';
			}
		}
		unset( $out['date'] );
		return $out;
	}
);

add_action(
	'manage_' . OW_POPUP_CPT . '_posts_custom_column',
	function ( $col, $post_id ) {
		if ( 'ow_thumb' === $col ) {
			$image  = get_post_meta( $post_id, 'ow_popup_image', true );
			$img_id = is_array( $image ) ? (int) ( $image['ID'] ?? 0 ) : (int) $image;
			if ( $img_id ) {
				echo wp_get_attachment_image(
					$img_id,
					array( 90, 47 ),
					false,
					array( 'style' => 'width:90px;height:47px;object-fit:cover;border-radius:4px;display:block;' )
				);
			} else {
				echo '<span style="color:#b32d2e;">No image</span>';
			}
			return;
		}

		if ( 'ow_state' === $col ) {
			$state = ow_popup_status( $post_id );
			$map   = array(
				'live'      => array( 'Live now', '#0a7c2f', '#e6f4ea' ),
				'scheduled' => array( 'Scheduled', '#8a6100', '#fdf3d8' ),
				'expired'   => array( 'Finished', '#6b6b6b', '#eeeeee' ),
				'off'       => array( 'Off', '#b32d2e', '#fbeaea' ),
			);
			list( $label, $fg, $bg ) = $map[ $state ];
			printf(
				'<span style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;color:%s;background:%s;">%s</span>',
				esc_attr( $fg ),
				esc_attr( $bg ),
				esc_html( $label )
			);
			return;
		}

		if ( 'ow_window' === $col ) {
			list( $start, $end ) = ow_popup_window( $post_id );
			$fmt                 = static function ( $ts ) {
				return $ts ? wp_date( 'j M Y', $ts ) : null;
			};
			$a = $fmt( $start );
			$b = $fmt( $end );

			if ( $a && $b ) {
				echo esc_html( $a . ' — ' . $b );
			} elseif ( $a ) {
				echo esc_html( 'From ' . $a );
			} elseif ( $b ) {
				echo esc_html( 'Until ' . $b );
			} else {
				echo '<span style="color:#787c82;">Always (while On)</span>';
			}
			return;
		}

		if ( 'ow_order' === $col ) {
			echo esc_html( (string) (int) get_post_meta( $post_id, 'ow_popup_order', true ) );
		}
	},
	10,
	2
);

add_filter(
	'manage_edit-' . OW_POPUP_CPT . '_sortable_columns',
	function ( $cols ) {
		$cols['ow_order'] = 'ow_order';
		return $cols;
	}
);

// Default the list to display order, so the admin table reads in the same
// sequence the visitor will see.
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( OW_POPUP_CPT !== $query->get( 'post_type' ) ) {
			return;
		}
		if ( '' !== $query->get( 'orderby' ) && 'ow_order' !== $query->get( 'orderby' ) ) {
			return;
		}
		// A bare meta_key would INNER JOIN and silently hide any popup whose
		// order field has never been saved. The OR/NOT EXISTS pair keeps those
		// in the list instead of losing them from the admin screen.
		$query->set(
			'meta_query',
			array(
				'relation' => 'OR',
				'ow_order' => array(
					'key'     => 'ow_popup_order',
					'compare' => 'EXISTS',
					'type'    => 'NUMERIC',
				),
				array(
					'key'     => 'ow_popup_order',
					'compare' => 'NOT EXISTS',
				),
			)
		);
		$query->set( 'orderby', array( 'ow_order' => 'ASC', 'date' => 'DESC' ) );
	}
);

/* =====================================================================
   8. Cache
   ===================================================================== */

/**
 * Drop the page cache whenever a popup changes.
 *
 * Without this the homepage keeps serving whatever popup was live when it was
 * cached — the client switches one on and nothing happens, which reads as the
 * feature being broken.
 *
 * @param int $post_id Saved post.
 * @return void
 */
function ow_popup_purge_cache( $post_id ) {
	if ( OW_POPUP_CPT !== get_post_type( $post_id ) ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	do_action( 'litespeed_purge_all' );
}
add_action( 'save_post', 'ow_popup_purge_cache', 20 );
add_action( 'trashed_post', 'ow_popup_purge_cache', 20 );
add_action( 'untrashed_post', 'ow_popup_purge_cache', 20 );
add_action( 'deleted_post', 'ow_popup_purge_cache', 20 );

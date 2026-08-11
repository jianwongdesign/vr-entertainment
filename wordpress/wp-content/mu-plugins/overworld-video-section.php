<?php
/**
 * Plugin Name: Overworld — Video Section
 * Description: A client-editable YouTube section for the 8 game pages and the 3 outlet pages, sitting directly under the hero. Renders a site-styled cover with a play button instead of a YouTube player, loads nothing from YouTube until it is clicked, and drops back to the cover when the video ends so the "watch next" grid never appears. The whole section is skipped when no link is set — nothing renders for anyone, including editors.
 * Author: Overworld
 * Version: 1.0.0
 *
 * Must-use plugin: auto-loads, no activation needed.
 *
 * Two ways in, one renderer:
 *   - game pages (Elementor, in the DB)  ->  [ow_video accent="#ff5722"]
 *   - outlet pages (page-pricing.php)    ->  ow_video_section()
 *
 * On the YouTube chrome, honestly:
 *   - Nothing loads from youtube.com until the play button is clicked — the
 *     cover is our own markup, so there is no YouTube branding, no cookie and
 *     no third-party request on page load.
 *   - The player then opens with rel=0, modestbranding=1, iv_load_policy=3,
 *     which is as far as the embed API lets anyone go.
 *   - The "watch next" grid YouTube paints when a video finishes is killed by
 *     watching the player for ENDED and swapping the cover back in.
 *   - End screens and cards *inside* the last seconds of the video are a
 *     per-video setting in YouTube Studio. No embed parameter removes them —
 *     they have to be turned off on the video itself.
 *
 * @package Overworld
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The 8 game pages, with the accent each hero already uses.
 * Also drives the ACF location rules below.
 */
function ow_video_game_pages() {
	return array(
		326 => '#ff5722', // vr-arcade
		420 => '#a855f7', // vr-escape
		294 => '#ff5722', // floor-is-lava
		312 => '#22e3ff', // laser-maze
		364 => '#ff2db8', // tap-tap
		338 => '#00d4ff', // vr-machine-ride
		646 => '#00ff88', // vr-free-roam
		577 => '#ffd60a', // xr-party-game
	);
}

/* =====================================================================
   1. Fields
   ===================================================================== */

add_action( 'acf/init', function () {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// Outlet pages by template, game pages by ID. Each array is an OR group.
	$location = array(
		array(
			array(
				'param'    => 'page_template',
				'operator' => '==',
				'value'    => 'page-pricing.php',
			),
		),
	);
	foreach ( array_keys( ow_video_game_pages() ) as $page_id ) {
		$location[] = array(
			array(
				'param'    => 'page',
				'operator' => '==',
				'value'    => (string) $page_id,
			),
		);
	}

	acf_add_local_field_group( array(
		'key'    => 'group_ow_video',
		'title'  => 'Video',
		'fields' => array(
			array(
				'key'       => 'field_ow_video_msg',
				'label'     => '',
				'name'      => '',
				'type'      => 'message',
				'message'   => 'A video section can sit directly under the hero on this page. <strong>Leave the link empty and the section does not exist</strong> — the page reads as if it were never there. Paste a YouTube link in and it appears. Nothing loads from YouTube until a visitor presses play.',
				'new_lines' => '',
				'esc_html'  => 0,
			),
			array(
				'key'          => 'field_ow_video_url',
				'label'        => 'YouTube Link',
				'name'         => 'ow_video_url',
				'type'         => 'text',
				'instructions' => 'Paste the normal YouTube link — youtube.com/watch?v=..., youtu.be/..., or a Shorts link. The video ID on its own works too. Empty = no section at all.',
				'required'     => 0,
			),
			array(
				'key'           => 'field_ow_video_poster',
				'label'         => 'Cover Image (optional)',
				'name'          => 'ow_video_poster',
				'type'          => 'image',
				'instructions'  => 'The still shown before play. Leave empty to use the video\'s own thumbnail. Landscape 16:9 works best (1600×900 or wider).',
				'required'      => 0,
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'library'       => 'all',
			),
			array(
				'key'          => 'field_ow_video_heading',
				'label'        => 'Heading (optional)',
				'name'         => 'ow_video_heading',
				'type'         => 'text',
				'instructions' => 'Empty = "See It In Action".',
				'required'     => 0,
				'maxlength'    => 60,
			),
			array(
				'key'          => 'field_ow_video_caption',
				'label'        => 'One Line Under The Heading (optional)',
				'name'         => 'ow_video_caption',
				'type'         => 'textarea',
				'rows'         => 2,
				'instructions' => 'Optional. Leave empty to show the heading on its own.',
				'required'     => 0,
			),
		),
		'location'        => $location,
		'menu_order'      => 1,
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
		'description'     => 'The video section under the hero.',
	) );
} );

/* =====================================================================
   2. Helpers
   ===================================================================== */

/**
 * Pull the 11-character video ID out of whatever the client pasted.
 */
function ow_video_parse_id( $raw ) {
	$raw = trim( (string) $raw );
	if ( '' === $raw ) {
		return '';
	}

	// Already just an ID.
	if ( preg_match( '~^[A-Za-z0-9_-]{11}$~', $raw ) ) {
		return $raw;
	}

	$patterns = array(
		'~youtu\.be/([A-Za-z0-9_-]{11})~',
		'~[?&]v=([A-Za-z0-9_-]{11})~',
		'~/embed/([A-Za-z0-9_-]{11})~',
		'~/shorts/([A-Za-z0-9_-]{11})~',
		'~/live/([A-Za-z0-9_-]{11})~',
	);
	foreach ( $patterns as $p ) {
		if ( preg_match( $p, $raw, $m ) ) {
			return $m[1];
		}
	}

	return '';
}

/* =====================================================================
   3. Renderer
   ===================================================================== */

/**
 * The section markup. Returns '' when no video is set, whoever is looking.
 *
 * @param int|null    $post_id Page to read the fields from. Defaults to current.
 * @param string|null $accent  Hex accent. Defaults to the page's own --accent.
 */
function ow_video_section( $post_id = null, $accent = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	$id = ow_video_parse_id( get_post_meta( $post_id, 'ow_video_url', true ) );

	// No link, no section — for everyone, editors included. The page reads as
	// if the block were never there; it appears the moment a link is saved.
	if ( '' === $id ) {
		return '';
	}

	$heading = trim( (string) get_post_meta( $post_id, 'ow_video_heading', true ) );
	$caption = trim( (string) get_post_meta( $post_id, 'ow_video_caption', true ) );
	if ( '' === $heading ) {
		$heading = 'See It In Action';
	}

	$poster_id  = get_post_meta( $post_id, 'ow_video_poster', true );
	$poster_url = ( $poster_id && is_numeric( $poster_id ) ) ? wp_get_attachment_image_url( (int) $poster_id, 'large' ) : '';
	if ( '' === $poster_url && '' !== $id ) {
		// YouTube's own still. hqdefault always exists; maxres often does not.
		$poster_url = 'https://i.ytimg.com/vi/' . $id . '/maxresdefault.jpg';
	}

	// Inherit the page's accent unless the caller names one.
	$accent_css = $accent ? $accent : 'var(--accent, #ffffff)';

	ob_start();

	// Style + script once per request, however many sections render.
	static $assets_done = false;
	if ( ! $assets_done ) {
		$assets_done = true;
		ow_video_assets();
	}
	?>
	<section class="ow-vid" style="--ow-vid-accent:<?php echo esc_attr( $accent_css ); ?>;">
		<div class="ow-vid__inner">

			<div class="ow-vid__head">
				<div class="ow-vid__eyebrow">Watch</div>
				<h2 class="ow-vid__title"><?php echo esc_html( $heading ); ?></h2>
				<?php if ( '' !== $caption ) : ?>
					<p class="ow-vid__caption"><?php echo esc_html( $caption ); ?></p>
				<?php endif; ?>
			</div>

				<div class="ow-vid__frame">
					<button class="ow-vid__cover" type="button"
						data-ow-video="<?php echo esc_attr( $id ); ?>"
						aria-label="<?php echo esc_attr( 'Play video: ' . $heading ); ?>">
						<?php if ( $poster_url ) : ?>
							<img class="ow-vid__poster" src="<?php echo esc_url( $poster_url ); ?>" alt="" loading="lazy" decoding="async" />
						<?php endif; ?>
						<span class="ow-vid__scrim" aria-hidden="true"></span>
						<span class="ow-vid__play" aria-hidden="true">
							<svg viewBox="0 0 24 24" width="34" height="34" focusable="false"><path d="M8 5.5v13l11-6.5z" fill="currentColor"/></svg>
						</span>
						<span class="ow-vid__hint" aria-hidden="true">Play</span>
					</button>
				</div>

		</div>
	</section>
	<?php

	return (string) ob_get_clean();
}

/**
 * Style + behaviour. Printed once per request.
 */
function ow_video_assets() {
	?>
	<style id="ow-vid-css">
	.ow-vid{
		/* Sits inside .ow-pri on the outlet pages, where --bg is the page's
		   own dark tone; standalone on the game pages, where it is not. */
		background:var(--ow-vid-bg,var(--bg,#0a0a14));
		color:#fff;
		font-family:'Space Grotesk','Inter',system-ui,sans-serif;
		padding:96px 40px;
	}
	.ow-vid *{box-sizing:border-box;}
	.ow-vid__inner{max-width:1200px;margin:0 auto;}
	.ow-vid__head{margin-bottom:34px;}
	.ow-vid__eyebrow{
		display:flex;align-items:center;gap:10px;
		font-family:'JetBrains Mono',monospace;
		font-size:11px;letter-spacing:.2em;text-transform:uppercase;
		color:var(--ow-vid-accent);margin-bottom:12px;
	}
	.ow-vid__eyebrow::before{content:"";width:24px;height:1px;background:var(--ow-vid-accent);}
	.ow-vid__title{
		font-family:'Anton','Bebas Neue',sans-serif;
		font-size:clamp(28px,3.5vw,42px);
		line-height:1;letter-spacing:-.02em;font-weight:400;
		text-transform:uppercase;margin:0;color:#fff;
	}
	.ow-vid__caption{
		margin:14px 0 0;max-width:720px;
		font-size:15.5px;line-height:1.7;color:rgba(220,225,240,.62);
	}
	.ow-vid__frame{
		position:relative;width:100%;aspect-ratio:16/9;
		border-radius:20px;overflow:hidden;
		border:1px solid rgba(255,255,255,.08);
		background:#000;
	}
	/* On the game pages this section is a shortcode widget inside an Elementor
	   flex-column container, and that container works out its own height from
	   the widget's *min-content* width rather than its real one. The frame's
	   height comes from its width (16/9), so it is measured against ~167px
	   instead of ~347px and the container lands about 100px short — the next
	   section then rides up over the video. Only bites below 1200px, where
	   .ow-vid__inner's max-width no longer pins the width to a fixed number,
	   which is why it read as a phone-only bug.

	   A definite width on the widget is the whole fix: the frame's height then
	   resolves against the width it actually gets. Scoped with :has() so it
	   touches this one widget and no other. The outlet pages render the
	   section from page-pricing.php, outside any Elementor widget, so this
	   selector never matches there and their layout is unchanged. */
	.elementor-widget:has(> .elementor-shortcode > .ow-vid){width:100%;}
	/* The cover is a button, not a player: no YouTube chrome, no third-party
	   request, nothing loaded until it is pressed. */
	.ow-vid__cover{
		position:absolute;inset:0;width:100%;height:100%;
		padding:0;margin:0;border:0;background:#000;
		cursor:pointer;display:block;overflow:hidden;
	}
	.ow-vid__poster{
		position:absolute;inset:0;width:100%;height:100%;
		object-fit:cover;display:block;
		transition:transform .6s cubic-bezier(.2,.7,.3,1),filter .4s ease;
	}
	.ow-vid__scrim{
		position:absolute;inset:0;
		background:radial-gradient(ellipse at center,rgba(0,0,0,.15) 0%,rgba(0,0,0,.55) 100%);
		transition:background .4s ease;
	}
	.ow-vid__play{
		position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
		width:84px;height:84px;border-radius:50%;
		display:flex;align-items:center;justify-content:center;
		color:#0a0a14;background:var(--ow-vid-accent);
		box-shadow:0 18px 50px -12px var(--ow-vid-accent);
		transition:transform .35s cubic-bezier(.2,.7,.3,1),box-shadow .35s ease;
	}
	.ow-vid__play svg{margin-left:4px;}
	.ow-vid__hint{
		position:absolute;left:50%;bottom:26px;transform:translateX(-50%);
		font-family:'JetBrains Mono',monospace;
		font-size:10.5px;letter-spacing:.22em;text-transform:uppercase;
		color:rgba(255,255,255,.75);
	}
	.ow-vid__cover:hover .ow-vid__poster{transform:scale(1.04);}
	.ow-vid__cover:hover .ow-vid__scrim{background:radial-gradient(ellipse at center,rgba(0,0,0,.05) 0%,rgba(0,0,0,.45) 100%);}
	.ow-vid__cover:hover .ow-vid__play{transform:translate(-50%,-50%) scale(1.08);}
	.ow-vid__cover:focus-visible{outline:2px solid var(--ow-vid-accent);outline-offset:3px;}
	/* Cover sits above the iframe while the player spins up. */
	.ow-vid__cover{z-index:2;}
	.ow-vid__cover.is-loading{cursor:default;}
	.ow-vid__cover.is-loading .ow-vid__play svg{opacity:0;}
	.ow-vid__cover.is-loading .ow-vid__play::after{
		content:"";width:26px;height:26px;border-radius:50%;
		border:2px solid rgba(10,10,20,.25);border-top-color:#0a0a14;
		animation:ow-vid-spin .7s linear infinite;
	}
	.ow-vid__cover.is-loading .ow-vid__hint{opacity:0;}
	@keyframes ow-vid-spin{to{transform:rotate(360deg);}}
	.ow-vid__frame iframe{position:absolute;inset:0;width:100%;height:100%;border:0;display:block;}
	/* Only shown when the browser refused to unmute (iOS). One tap fixes it. */
	.ow-vid__sound{
		position:absolute;left:14px;bottom:14px;z-index:3;
		display:inline-flex;align-items:center;gap:7px;
		padding:9px 14px;border:0;border-radius:999px;cursor:pointer;
		font-family:'JetBrains Mono',monospace;
		font-size:10.5px;letter-spacing:.14em;text-transform:uppercase;font-weight:700;
		background:var(--ow-vid-accent);color:#0a0a14;
		box-shadow:0 10px 26px -10px rgba(0,0,0,.9);
	}
	@media (max-width:1000px){
		.ow-vid{padding:70px 28px;}
		.ow-vid__frame{border-radius:16px;}
		.ow-vid__play{width:68px;height:68px;}
		.ow-vid__play svg{width:28px;height:28px;}
	}
	@media (max-width:600px){
		/* Tighter side padding than the rest of the page on purpose: YouTube's
		   own chrome crowds and crops below roughly 340px of player width, so
		   every pixel of frame width is worth having. */
		.ow-vid{padding:54px 12px;}
		.ow-vid__head{margin-bottom:24px;padding:0 6px;}
		.ow-vid__play{width:58px;height:58px;}
		.ow-vid__play svg{width:24px;height:24px;}
		.ow-vid__hint{bottom:16px;font-size:9.5px;}
		.ow-vid__sound{left:10px;bottom:10px;padding:8px 12px;font-size:9.5px;}
	}
	</style>
	<script id="ow-vid-js">
	(function () {
		if (window.owVideoReady) { return; }
		window.owVideoReady = true;

		var apiRequested = false;

		function loadApi(cb) {
			if (window.YT && window.YT.Player) { cb(); return; }
			var prev = window.onYouTubeIframeAPIReady;
			window.onYouTubeIframeAPIReady = function () {
				if (typeof prev === 'function') { prev(); }
				cb();
			};
			if (apiRequested) { return; }
			apiRequested = true;
			var s = document.createElement('script');
			s.src = 'https://www.youtube.com/iframe_api';
			document.head.appendChild(s);
		}

		function showSoundPill(frame, player) {
			if (frame.querySelector('.ow-vid__sound')) { return; }
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'ow-vid__sound';
			b.innerHTML = '<span aria-hidden="true">&#128264;</span> Tap for sound';
			b.addEventListener('click', function (ev) {
				ev.preventDefault();
				// A direct tap is its own user gesture, which is the one thing
				// iOS will accept for turning sound on.
				try { player.unMute(); player.setVolume(100); } catch (err) {}
				if (b.parentNode) { b.parentNode.removeChild(b); }
			});
			frame.appendChild(b);
		}

		function play(btn) {
			var id = btn.getAttribute('data-ow-video');
			if (!id) { return; }
			var frame = btn.parentNode;
			var cover = btn;
			if (frame.querySelector('iframe')) { return; } // already playing

			var params = [
				'autoplay=1',
				// Muted autoplay is the only kind every browser allows. Without
				// it iOS refuses to start and YouTube paints its own poster —
				// which it crops and zooms hard at phone widths. Sound is turned
				// back on below the moment the player is ready.
				'mute=1',
				'rel=0',              // keep suggestions to this channel only
				'modestbranding=1',
				'iv_load_policy=3',   // no annotations
				'playsinline=1',
				'color=white',
				'enablejsapi=1',
				'origin=' + encodeURIComponent(window.location.origin)
			].join('&');

			var iframe = document.createElement('iframe');
			iframe.src = 'https://www.youtube-nocookie.com/embed/' + id + '?' + params;
			iframe.title = cover.getAttribute('aria-label') || 'Video';
			iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
			iframe.setAttribute('allowfullscreen', '');
			iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');

			// Behind the cover, not instead of it: our own still stays on top
			// until the video is genuinely playing, so YouTube's pre-play
			// screen is never what the visitor looks at.
			cover.classList.add('is-loading');
			frame.insertBefore(iframe, cover);

			var revealed = false;
			function reveal() {
				if (revealed) { return; }
				revealed = true;
				cover.classList.remove('is-loading');
				cover.style.display = 'none';
			}
			// If the API never answers, don't trap the visitor behind the cover.
			var fallback = window.setTimeout(reveal, 2500);

			loadApi(function () {
				try {
					var player = new window.YT.Player(iframe, {
						events: {
							onReady: function () {
								try { player.playVideo(); } catch (err) {}
							},
							onStateChange: function (e) {
								if (e.data === window.YT.PlayerState.PLAYING) {
									window.clearTimeout(fallback);
									reveal();
									// Unmute only once playback has actually
									// begun — doing it earlier can trip the
									// autoplay gate and leave the video paused.
									// The visitor pressed our button, so the
									// page has the activation this needs.
									try { player.unMute(); player.setVolume(100); } catch (err) {}
									// iOS ignores that. One tap fixes it.
									window.setTimeout(function () {
										try {
											if (player.isMuted()) { showSoundPill(frame, player); }
										} catch (err) {}
									}, 400);
								}
								if (e.data === window.YT.PlayerState.ENDED) {
									// Put the cover back rather than let YouTube
									// paint its "watch next" grid over the last frame.
									var pill = frame.querySelector('.ow-vid__sound');
									if (pill) { pill.parentNode.removeChild(pill); }
									try { player.destroy(); } catch (err) {}
									var left = frame.querySelector('iframe');
									if (left) { left.parentNode.removeChild(left); }
									revealed = false;
									cover.style.display = '';
								}
							}
						}
					});
				} catch (err) { /* video still plays; only the extras are lost */ }
			});
		}

		document.addEventListener('click', function (e) {
			var btn = e.target.closest ? e.target.closest('[data-ow-video]') : null;
			if (btn) { e.preventDefault(); play(btn); }
		});
	})();
	</script>
	<?php
}

/* =====================================================================
   4. Shortcode — how the Elementor game pages call it
   ===================================================================== */

add_shortcode( 'ow_video', function ( $atts ) {
	$atts = shortcode_atts(
		array(
			'accent' => '',
			'id'     => '',
		),
		$atts,
		'ow_video'
	);

	$post_id = $atts['id'] ? (int) $atts['id'] : get_the_ID();
	$accent  = $atts['accent'] ? $atts['accent'] : null;

	return ow_video_section( $post_id, $accent );
} );

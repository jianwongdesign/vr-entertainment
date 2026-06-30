<?php
/**
 * Single Experience Template
 *
 * Powers individual experience pages:
 *   /experience/{slug}/
 *
 * Matches the design language of taxonomy-experience_type.php archive template.
 *
 * @package Overworld
 */

get_header();

// Vibe label map (must match archive template).
$vibe_labels = array(
    'shooter'   => 'Shooter',
    'pvp'       => 'PvP / Competitive',
    'rhythm'    => 'Rhythm & Music',
    'puzzle'    => 'Puzzle & Escape',
    'casual'    => 'Casual & Party',
    'adventure' => 'Adventure',
);

while ( have_posts() ) : the_post();

    $tagline    = function_exists( 'get_field' ) ? get_field( 'exp_tagline' )      : '';
    $intro      = function_exists( 'get_field' ) ? get_field( 'exp_intro' )        : '';
    $difficulty = function_exists( 'get_field' ) ? get_field( 'exp_difficulty' )   : '';
    $players    = function_exists( 'get_field' ) ? get_field( 'exp_players' )      : '';
    $age        = function_exists( 'get_field' ) ? get_field( 'exp_age' )          : '';
    $video      = function_exists( 'get_field' ) ? get_field( 'exp_video' )        : '';
    $image_1    = function_exists( 'get_field' ) ? get_field( 'exp_image_1' )      : null;
    $image_2    = function_exists( 'get_field' ) ? get_field( 'exp_image_2' )      : null;
    $vibes      = function_exists( 'get_field' ) ? get_field( 'exp_vibes' )        : array();
    $booking    = function_exists( 'get_field' ) ? get_field( 'exp_booking_url' )  : '';
    $thumb      = get_the_post_thumbnail_url( get_the_ID(), 'full' );

    if ( ! is_array( $vibes ) ) $vibes = array();
    $images = array_filter( array( $image_1, $image_2 ) );

    // Get the primary experience type term.
    $types     = get_the_terms( get_the_ID(), 'experience_type' );
    $type      = ( ! empty( $types ) && ! is_wp_error( $types ) ) ? $types[0] : null;
    $type_name = $type ? $type->name : 'Experience';
    $type_link = $type ? get_term_link( $type ) : home_url( '/experiences/' );
?>

<article class="ow-single">

    <!-- ===== HERO ===== -->
    <header class="ow-single__hero">
        <?php if ( $thumb ) : ?>
            <div class="ow-single__hero-bg" style="background-image: url('<?php echo esc_url( $thumb ); ?>');"></div>
        <?php endif; ?>
        <div class="ow-single__hero-grid"></div>
        <div class="ow-single__hero-grad"></div>

        <div class="ow-single__hero-inner">

            <nav class="ow-single__crumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
                <span class="ow-single__crumb-sep">/</span>
                <a href="<?php echo esc_url( $type_link ); ?>"><?php echo esc_html( $type_name ); ?></a>
                <span class="ow-single__crumb-sep">/</span>
                <span class="ow-single__crumb-current"><?php echo esc_html( get_the_title() ); ?></span>
            </nav>

            <p class="ow-single__eyebrow">
                <?php if ( $difficulty ) : ?>
                    <span class="ow-single__diff" data-diff="<?php echo esc_attr( $difficulty ); ?>"><?php echo esc_html( $difficulty ); ?></span>
                <?php endif; ?>
                <span class="ow-single__eyebrow-text"><?php echo esc_html( $type_name ); ?></span>
            </p>

            <h1 class="ow-single__title"><?php the_title(); ?></h1>

            <?php if ( $tagline ) : ?>
                <p class="ow-single__tagline"><?php echo esc_html( $tagline ); ?></p>
            <?php endif; ?>

            <!-- meta strip -->
            <div class="ow-single__meta">
                <?php if ( $players ) : ?>
                    <div class="ow-single__meta-item">
                        <span class="ow-single__meta-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                        <span class="ow-single__meta-label">Players</span>
                        <span class="ow-single__meta-val"><?php echo esc_html( $players ); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ( $difficulty ) : ?>
                    <div class="ow-single__meta-item">
                        <span class="ow-single__meta-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h4l3-9 4 18 3-9h4"/></svg>
                        </span>
                        <span class="ow-single__meta-label">Difficulty</span>
                        <span class="ow-single__meta-val"><?php echo esc_html( ucfirst( $difficulty ) ); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ( $age ) : ?>
                    <div class="ow-single__meta-item">
                        <span class="ow-single__meta-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"/><path d="M12 18v4"/><path d="m4.93 4.93 2.83 2.83"/><path d="m16.24 16.24 2.83 2.83"/><path d="M2 12h4"/><path d="M18 12h4"/><path d="m4.93 19.07 2.83-2.83"/><path d="m16.24 7.76 2.83-2.83"/></svg>
                        </span>
                        <span class="ow-single__meta-label">Age</span>
                        <span class="ow-single__meta-val"><?php echo esc_html( $age ); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $vibes ) ) : ?>
                <div class="ow-single__vibes">
                    <?php foreach ( $vibes as $v ) : ?>
                        <span class="ow-single__vibe-chip"><?php echo esc_html( isset( $vibe_labels[ $v ] ) ? $vibe_labels[ $v ] : ucfirst( $v ) ); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ( $booking ) : ?>
                <div class="ow-single__hero-cta">
                    <a href="<?php echo esc_url( $booking ); ?>" class="ow-single__cta" target="_blank" rel="noopener">
                        Book This Experience
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </header>

    <!-- ===== TRAILER ===== -->
    <?php if ( $video ) : ?>
        <section class="ow-single__section">
            <div class="ow-single__section-inner">
                <div class="ow-single__section-label">
                    <span class="ow-single__section-line"></span>
                    Trailer
                </div>
                <div class="ow-single__video-frame">
                    <?php echo $video; // ACF oEmbed outputs the iframe ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ===== INTRO ===== -->
    <?php if ( $intro ) : ?>
        <section class="ow-single__section">
            <div class="ow-single__section-inner">
                <div class="ow-single__section-label">
                    <span class="ow-single__section-line"></span>
                    About This Experience
                </div>
                <div class="ow-single__intro-body">
                    <?php echo wp_kses_post( $intro ); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ===== GALLERY ===== -->
    <?php if ( ! empty( $images ) ) : ?>
        <section class="ow-single__section">
            <div class="ow-single__section-inner">
                <div class="ow-single__section-label">
                    <span class="ow-single__section-line"></span>
                    Gallery
                </div>
                <div class="ow-single__gallery <?php echo count( $images ) === 2 ? 'ow-single__gallery--two' : ''; ?>">
                    <?php foreach ( $images as $img ) : ?>
                        <a href="<?php echo esc_url( $img['url'] ); ?>"
                           class="ow-single__gallery-item"
                           data-lightbox="experience">
                            <img src="<?php echo esc_url( $img['sizes']['large'] ?? $img['url'] ); ?>"
                                 alt="<?php echo esc_attr( $img['alt'] ?? get_the_title() ); ?>"
                                 loading="lazy">
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ===== BOOKING CTA ===== -->
    <?php if ( $booking ) : ?>
        <section class="ow-single__section">
            <div class="ow-single__section-inner">
                <div class="ow-single__book">
                    <div class="ow-single__book-grid"></div>
                    <div class="ow-single__book-content">
                        <p class="ow-single__book-eyebrow">
                            <span class="ow-single__book-line"></span>
                            Ready to play
                        </p>
                        <h2 class="ow-single__book-title">Step Into <span><?php echo esc_html( get_the_title() ); ?></span></h2>
                        <p class="ow-single__book-desc">Reserve your slot. Limited capacity per session.</p>
                        <a href="<?php echo esc_url( $booking ); ?>" class="ow-single__cta ow-single__cta--lg" target="_blank" rel="noopener">
                            Book Now
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ===== RELATED ===== -->
    <?php
    if ( $type ) :
        $related = new WP_Query( array(
            'post_type'      => 'experience',
            'posts_per_page' => 3,
            'post__not_in'   => array( get_the_ID() ),
            'tax_query'      => array(
                array(
                    'taxonomy' => 'experience_type',
                    'field'    => 'term_id',
                    'terms'    => $type->term_id,
                ),
            ),
        ) );

        if ( $related->have_posts() ) :
    ?>
        <section class="ow-single__section">
            <div class="ow-single__section-inner">
                <div class="ow-single__related-head">
                    <div>
                        <div class="ow-single__section-label">
                            <span class="ow-single__section-line"></span>
                            More to explore
                        </div>
                        <h3 class="ow-single__related-title">More <?php echo esc_html( $type_name ); ?></h3>
                    </div>
                    <a href="<?php echo esc_url( $type_link ); ?>" class="ow-single__related-all">
                        View all
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="ow-single__related-grid">
                    <?php while ( $related->have_posts() ) : $related->the_post();
                        $r_thumb   = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                        $r_tagline = function_exists( 'get_field' ) ? get_field( 'exp_tagline' )    : '';
                        $r_diff    = function_exists( 'get_field' ) ? get_field( 'exp_difficulty' ) : '';
                        $r_players = function_exists( 'get_field' ) ? get_field( 'exp_players' )    : '';
                        $r_age     = function_exists( 'get_field' ) ? get_field( 'exp_age' )        : '';
                    ?>
                        <a href="<?php the_permalink(); ?>" class="ow-rel-card">
                            <div class="ow-rel-card__media">
                                <?php if ( $r_diff ) : ?>
                                    <span class="ow-rel-card__diff" data-diff="<?php echo esc_attr( $r_diff ); ?>"><?php echo esc_html( $r_diff ); ?></span>
                                <?php endif; ?>
                                <?php if ( $r_thumb ) : ?>
                                    <img src="<?php echo esc_url( $r_thumb ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
                                <?php else : ?>
                                    <div class="ow-rel-card__placeholder"><?php echo esc_html( substr( get_the_title(), 0, 1 ) ); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="ow-rel-card__body">
                                <h4 class="ow-rel-card__name"><?php the_title(); ?></h4>
                                <?php if ( $r_tagline ) : ?>
                                    <p class="ow-rel-card__tagline"><?php echo esc_html( $r_tagline ); ?></p>
                                <?php endif; ?>
                                <div class="ow-rel-card__meta">
                                    <?php if ( $r_players ) : ?><span><?php echo esc_html( $r_players ); ?></span><?php endif; ?>
                                    <?php if ( $r_age ) : ?><span><?php echo esc_html( $r_age ); ?></span><?php endif; ?>
                                </div>
                                <div class="ow-rel-card__cta">
                                    <span>Learn More</span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    <?php
            wp_reset_postdata();
        endif;
    endif;
    ?>

</article>

<style>
/* ============================================
   OVERWORLD VR — Single Experience
   Matches taxonomy archive design language
   ============================================ */
@font-face {
    font-family: 'Lulo Clean One Bold';
    font-style: normal; font-weight: 400;
    src: url('//static.parastorage.com/fonts/v2/e3cf8f7e-35c4-446f-9b93-de93e989f66f/v1/lulo-clean-w01-one-bold.woff2') format('woff2');
    font-display: swap;
}
@font-face {
    font-family: 'Helvetica W01';
    font-style: normal; font-weight: 400;
    src: url('//static.parastorage.com/fonts/v2/2af1bf48-e783-4da8-9fa0-599dde29f2d5/v1/helvetica-w01-roman.woff2') format('woff2');
    font-display: swap;
}

.ow-single {
    --ow-bg:        #0b0b0c;
    --ow-bg-2:      #141417;
    --ow-panel:     rgba(255, 255, 255, 0.04);
    --ow-border:    rgba(255, 255, 255, 0.08);
    --ow-border-strong: rgba(255, 255, 255, 0.16);
    --ow-text:      #f4f4f5;
    --ow-text-dim:  #a1a1aa;
    --ow-text-faint:#71717a;
    --ow-accent:    #ff5a1f;
    --ow-accent-glow: rgba(255, 90, 31, 0.35);
    --ow-easy:      #4ade80;
    --ow-medium:    #facc15;
    --ow-hard:      #ef4444;
    --ow-extreme:   #b91c1c;
    --font-display: 'Lulo Clean One Bold', 'Impact', 'Arial Black', sans-serif;
    --font-body:    'Helvetica W01', 'Helvetica Neue', Helvetica, Arial, sans-serif;

    background: var(--ow-bg);
    color: var(--ow-text);
    font-family: var(--font-body);
    padding-bottom: 96px;
}
.ow-single * { box-sizing: border-box; }

/* ===== HERO ===== */
.ow-single__hero {
    position: relative;
    min-height: 70vh;
    padding: 100px 24px 64px;
    overflow: hidden;
    display: flex;
    align-items: flex-end;
    background: radial-gradient(ellipse at top, #1a0e08 0%, var(--ow-bg) 65%) var(--ow-bg);
}

.ow-single__hero-bg {
    position: absolute; inset: 0;
    background-size: cover;
    background-position: center;
    filter: brightness(.45) saturate(1.1);
    transform: scale(1.05);
    transition: transform 1.2s ease;
    z-index: 0;
}
.ow-single:hover .ow-single__hero-bg { transform: scale(1.02); }

.ow-single__hero-grid {
    position: absolute; inset: 0;
    background-image:
      linear-gradient(rgba(255, 90, 31, 0.06) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255, 90, 31, 0.06) 1px, transparent 1px);
    background-size: 48px 48px;
    mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
    -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
    pointer-events: none;
    z-index: 1;
}

.ow-single__hero-grad {
    position: absolute; inset: 0;
    background: linear-gradient(
        180deg,
        rgba(11,11,12,.5) 0%,
        rgba(11,11,12,.4) 30%,
        rgba(11,11,12,.85) 70%,
        var(--ow-bg) 100%
    );
    z-index: 1;
}

.ow-single__hero-inner {
    position: relative; z-index: 2;
    max-width: 1400px;
    width: 100%;
    margin: 0 auto;
}

/* breadcrumbs */
.ow-single__crumbs {
    font-family: var(--font-body);
    font-size: 11px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--ow-text-dim);
    margin-bottom: 36px;
    display: flex; align-items: center; flex-wrap: wrap;
    gap: 10px;
}
.ow-single__crumbs a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s ease;
}
.ow-single__crumbs a:hover { color: var(--ow-accent); }
.ow-single__crumb-sep { color: var(--ow-text-faint); }
.ow-single__crumb-current { color: var(--ow-accent); }

/* eyebrow with difficulty */
.ow-single__eyebrow {
    display: inline-flex; align-items: center; gap: 12px;
    margin: 0 0 22px;
    font-size: 11px;
    letter-spacing: 0.32em;
    text-transform: uppercase;
}
.ow-single__eyebrow-text {
    font-family: var(--font-body);
    color: var(--ow-accent);
    display: inline-flex; align-items: center; gap: 10px;
}
.ow-single__eyebrow-text::before { content: ""; width: 28px; height: 1px; background: var(--ow-accent); }

.ow-single__diff {
    font-family: var(--font-display);
    font-size: 10px; letter-spacing: 0.14em; text-transform: uppercase;
    padding: 5px 10px; border-radius: 4px;
    backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
}
.ow-single__diff[data-diff="easy"]    { background: rgba(74, 222, 128, 0.15); color: var(--ow-easy);    border: 1px solid rgba(74, 222, 128, 0.35); }
.ow-single__diff[data-diff="medium"]  { background: rgba(250, 204, 21, 0.12); color: var(--ow-medium);  border: 1px solid rgba(250, 204, 21, 0.3); }
.ow-single__diff[data-diff="hard"]    { background: rgba(239, 68, 68, 0.15);  color: var(--ow-hard);    border: 1px solid rgba(239, 68, 68, 0.35); }
.ow-single__diff[data-diff="extreme"] { background: rgba(185, 28, 28, 0.18);  color: #fca5a5;           border: 1px solid rgba(185, 28, 28, 0.4); }

/* title */
.ow-single__title {
    font-family: var(--font-display);
    font-size: clamp(40px, 6vw, 80px);
    line-height: 1.02;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    margin: 0 0 24px;
    color: var(--ow-text);
}

.ow-single__tagline {
    font-family: var(--font-body);
    font-size: clamp(16px, 1.6vw, 19px);
    line-height: 1.5;
    color: var(--ow-text-dim);
    max-width: 720px;
    margin: 0 0 40px;
}

/* meta strip */
.ow-single__meta {
    display: flex; flex-wrap: wrap;
    gap: 0;
    margin: 0 0 28px;
    border-top: 1px solid var(--ow-border);
    border-bottom: 1px solid var(--ow-border);
    background: var(--ow-panel);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-radius: 14px;
    padding: 4px 8px;
}
.ow-single__meta-item {
    display: flex; align-items: center;
    gap: 12px;
    padding: 16px 24px;
    border-right: 1px solid var(--ow-border);
}
.ow-single__meta-item:last-child { border-right: none; }
.ow-single__meta-icon {
    display: inline-flex; align-items: center; justify-content: center;
    width: 36px; height: 36px;
    background: rgba(255, 90, 31, 0.12);
    border: 1px solid rgba(255, 90, 31, 0.25);
    border-radius: 10px;
    color: var(--ow-accent);
    flex-shrink: 0;
}
.ow-single__meta-label {
    font-family: var(--font-display);
    font-size: 9.5px;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--ow-text-faint);
    display: block;
    margin-bottom: 3px;
}
.ow-single__meta-val {
    font-family: var(--font-display);
    font-size: 14px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--ow-text);
    display: block;
}
.ow-single__meta-item > span:not(.ow-single__meta-icon) {
    display: flex; flex-direction: column;
}

/* vibe chips */
.ow-single__vibes {
    display: flex; flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 36px;
}
.ow-single__vibe-chip {
    font-size: 10px; letter-spacing: 0.14em; text-transform: uppercase;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--ow-border-strong);
    border-radius: 6px;
    color: var(--ow-text-dim);
    font-family: var(--font-body);
}

/* CTA buttons */
.ow-single__hero-cta { margin-top: 8px; }

.ow-single__cta {
    display: inline-flex; align-items: center;
    gap: 12px;
    padding: 16px 28px;
    font-family: var(--font-display);
    font-size: 12px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--ow-bg);
    text-decoration: none;
    background: var(--ow-accent);
    border-radius: 999px;
    transition: all 0.25s ease;
    box-shadow: 0 0 0 3px rgba(255, 90, 31, 0.18), 0 12px 32px var(--ow-accent-glow);
}
.ow-single__cta:hover {
    background: #ff7a4a;
    transform: translateY(-2px);
    box-shadow: 0 0 0 3px rgba(255, 90, 31, 0.25), 0 16px 40px var(--ow-accent-glow);
}
.ow-single__cta svg { transition: transform 0.25s ease; }
.ow-single__cta:hover svg { transform: translateX(4px); }
.ow-single__cta--lg {
    padding: 20px 36px;
    font-size: 13px;
}

/* ===== SECTIONS — shared layout ===== */
.ow-single__section {
    position: relative;
    padding: 72px 24px 0;
}
.ow-single__section-inner {
    max-width: 1400px;
    margin: 0 auto;
}

.ow-single__section-label {
    display: inline-flex; align-items: center; gap: 12px;
    font-family: var(--font-body);
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--ow-accent);
    margin-bottom: 24px;
}
.ow-single__section-line {
    width: 28px; height: 1px;
    background: var(--ow-accent);
    flex-shrink: 0;
}

/* VIDEO */
.ow-single__video-frame {
    aspect-ratio: 16 / 9;
    border-radius: 20px;
    overflow: hidden;
    background: var(--ow-bg-2);
    border: 1px solid var(--ow-border);
    position: relative;
}
.ow-single__video-frame iframe,
.ow-single__video-frame video,
.ow-single__video-frame embed {
    width: 100% !important;
    height: 100% !important;
    border: none;
    display: block;
    position: absolute; inset: 0;
}

/* INTRO */
.ow-single__intro-body {
    max-width: 760px;
    font-family: var(--font-body);
    font-size: 17px;
    line-height: 1.7;
    color: var(--ow-text-dim);
}
.ow-single__intro-body p { margin: 0 0 1.4em; }
.ow-single__intro-body p:last-child { margin-bottom: 0; }
.ow-single__intro-body strong { color: var(--ow-text); font-weight: 700; }
.ow-single__intro-body a {
    color: var(--ow-accent);
    text-decoration: none;
    border-bottom: 1px solid currentColor;
    transition: color 0.2s ease;
}
.ow-single__intro-body a:hover { color: #ff7a4a; }

/* GALLERY */
.ow-single__gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}
.ow-single__gallery--two {
    grid-template-columns: 1fr 1fr;
}
.ow-single__gallery-item {
    display: block;
    aspect-ratio: 4 / 3;
    overflow: hidden;
    border-radius: 16px;
    background: var(--ow-bg-2);
    border: 1px solid var(--ow-border);
    transition: transform 0.3s ease, border-color 0.3s ease;
}
.ow-single__gallery-item:hover {
    transform: scale(1.02);
    border-color: rgba(255, 90, 31, 0.4);
}
.ow-single__gallery-item img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.6s ease;
}
.ow-single__gallery-item:hover img { transform: scale(1.08); }

/* BOOKING CTA SECTION */
.ow-single__book {
    position: relative;
    text-align: center;
    padding: 80px 40px;
    background: radial-gradient(ellipse at center, rgba(255, 90, 31, 0.12), rgba(255, 90, 31, 0.02));
    border: 1px solid rgba(255, 90, 31, 0.25);
    border-radius: 24px;
    overflow: hidden;
}
.ow-single__book-grid {
    position: absolute; inset: 0;
    background-image:
      linear-gradient(rgba(255, 90, 31, 0.06) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255, 90, 31, 0.06) 1px, transparent 1px);
    background-size: 32px 32px;
    mask-image: radial-gradient(ellipse at center, black 20%, transparent 70%);
    -webkit-mask-image: radial-gradient(ellipse at center, black 20%, transparent 70%);
    pointer-events: none;
}
.ow-single__book-content { position: relative; z-index: 1; }

.ow-single__book-eyebrow {
    display: inline-flex; align-items: center; gap: 12px;
    font-family: var(--font-body);
    font-size: 11px;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--ow-accent);
    margin: 0 0 16px;
}
.ow-single__book-line {
    width: 28px; height: 1px;
    background: var(--ow-accent);
}

.ow-single__book-title {
    font-family: var(--font-display);
    font-size: clamp(28px, 4vw, 52px);
    line-height: 1.05;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    margin: 0 0 16px;
    color: var(--ow-text);
}
.ow-single__book-title span { color: var(--ow-accent); }

.ow-single__book-desc {
    font-family: var(--font-body);
    font-size: 16px;
    color: var(--ow-text-dim);
    margin: 0 0 36px;
}

/* RELATED */
.ow-single__related-head {
    display: flex; align-items: flex-end; justify-content: space-between;
    margin-bottom: 32px;
    flex-wrap: wrap;
    gap: 24px;
}
.ow-single__related-title {
    font-family: var(--font-display);
    font-size: clamp(22px, 2.5vw, 36px);
    line-height: 1.05;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin: 0;
    color: var(--ow-text);
}
.ow-single__related-all {
    display: inline-flex; align-items: center; gap: 8px;
    color: var(--ow-text-dim);
    text-decoration: none;
    font-family: var(--font-body);
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    transition: color 0.2s ease;
}
.ow-single__related-all:hover { color: var(--ow-accent); }
.ow-single__related-all svg { transition: transform 0.25s ease; }
.ow-single__related-all:hover svg { transform: translateX(3px); }

.ow-single__related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.ow-rel-card {
    text-decoration: none; color: inherit;
    background: var(--ow-bg-2);
    border: 1px solid var(--ow-border);
    border-radius: 20px;
    overflow: hidden;
    transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    display: flex; flex-direction: column;
}
.ow-rel-card:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 90, 31, 0.5);
    box-shadow: 0 20px 40px -20px rgba(255, 90, 31, 0.4);
}

.ow-rel-card__media {
    position: relative;
    aspect-ratio: 16 / 10;
    overflow: hidden;
    background: #0a0a0a;
}
.ow-rel-card__media img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.ow-rel-card:hover .ow-rel-card__media img { transform: scale(1.06); }
.ow-rel-card__media::after {
    content: ""; position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(11,11,12,0.85) 0%, transparent 45%);
    pointer-events: none;
}
.ow-rel-card__placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #1a1d2e 0%, #0f111a 100%);
    font-family: var(--font-display);
    font-size: 56px;
    color: rgba(255,255,255,.08);
}
.ow-rel-card__diff {
    position: absolute; top: 12px; left: 12px;
    font-family: var(--font-display);
    font-size: 10px; letter-spacing: 0.14em; text-transform: uppercase;
    padding: 5px 10px; border-radius: 4px;
    backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
    z-index: 2;
}
.ow-rel-card__diff[data-diff="easy"]    { background: rgba(74, 222, 128, 0.15); color: var(--ow-easy);    border: 1px solid rgba(74, 222, 128, 0.35); }
.ow-rel-card__diff[data-diff="medium"]  { background: rgba(250, 204, 21, 0.12); color: var(--ow-medium);  border: 1px solid rgba(250, 204, 21, 0.3); }
.ow-rel-card__diff[data-diff="hard"]    { background: rgba(239, 68, 68, 0.15);  color: var(--ow-hard);    border: 1px solid rgba(239, 68, 68, 0.35); }
.ow-rel-card__diff[data-diff="extreme"] { background: rgba(185, 28, 28, 0.18);  color: #fca5a5;           border: 1px solid rgba(185, 28, 28, 0.4); }

.ow-rel-card__body {
    padding: 18px 18px 20px;
    display: flex; flex-direction: column;
    gap: 10px; flex: 1;
}
.ow-rel-card__name {
    font-family: var(--font-display);
    font-size: 14px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    line-height: 1.25;
    margin: 0;
    color: var(--ow-text);
}
.ow-rel-card__tagline {
    font-family: var(--font-body);
    font-size: 13px;
    line-height: 1.5;
    color: var(--ow-text-dim);
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.ow-rel-card__meta {
    display: flex; flex-wrap: wrap; gap: 12px;
    font-size: 11px; color: var(--ow-text-dim);
    letter-spacing: 0.04em;
}
.ow-rel-card__cta {
    display: flex; align-items: center; justify-content: space-between;
    padding-top: 12px; margin-top: auto;
    border-top: 1px solid var(--ow-border);
    font-family: var(--font-display);
    font-size: 10px; letter-spacing: 0.2em;
    color: var(--ow-accent);
}
.ow-rel-card__cta svg { transition: transform 0.2s ease; }
.ow-rel-card:hover .ow-rel-card__cta svg { transform: translateX(4px); }

/* ===== TABLET ===== */
@media (max-width: 980px) {
    .ow-single__hero { padding: 80px 32px 48px; min-height: 60vh; }
    .ow-single__section { padding-left: 32px; padding-right: 32px; padding-top: 60px; }
    .ow-single__title { font-size: clamp(36px, 7vw, 60px); }
    .ow-single__tagline { font-size: 16px; }

    .ow-single__meta {
        flex-direction: column;
        padding: 0;
    }
    .ow-single__meta-item {
        padding: 14px 20px;
        border-right: none;
        border-bottom: 1px solid var(--ow-border);
        width: 100%;
    }
    .ow-single__meta-item:last-child { border-bottom: none; }
    .ow-single__book { padding: 60px 28px; }
    .ow-single__gallery--two { grid-template-columns: 1fr; }
}

/* ===== MOBILE ===== */
@media (max-width: 640px) {
    .ow-single__hero {
        padding: 56px 18px 40px;
        min-height: auto;        /* let content drive height — no forced 55vh that creates empty space */
    }
    .ow-single__section { padding-left: 18px; padding-right: 18px; padding-top: 44px; }

    .ow-single__crumbs {
        font-size: 9.5px;
        gap: 6px;
        margin-bottom: 24px;
        letter-spacing: 0.14em;
    }
    .ow-single__crumb-current {
        max-width: 140px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ow-single__eyebrow { font-size: 10px; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
    .ow-single__eyebrow-text { letter-spacing: 0.22em; }
    .ow-single__eyebrow-text::before { width: 18px; }
    .ow-single__diff { font-size: 9px; padding: 4px 8px; letter-spacing: 0.1em; }

    .ow-single__title {
        font-size: clamp(26px, 8vw, 40px);
        line-height: 1.05;
        letter-spacing: 0.015em;
        margin-bottom: 16px;
        word-break: break-word;
    }
    .ow-single__tagline { font-size: 14.5px; line-height: 1.45; margin-bottom: 24px; }

    .ow-single__meta {
        border-radius: 10px;
        padding: 0;
        margin-bottom: 20px;
    }
    .ow-single__meta-item { padding: 12px 16px; gap: 10px; }
    .ow-single__meta-icon { width: 30px; height: 30px; }
    .ow-single__meta-icon svg { width: 14px; height: 14px; }
    .ow-single__meta-label { font-size: 8.5px; letter-spacing: 0.16em; margin-bottom: 2px; }
    .ow-single__meta-val { font-size: 12.5px; letter-spacing: 0.04em; }

    .ow-single__vibes { gap: 6px; margin-bottom: 24px; }
    .ow-single__vibe-chip { font-size: 9.5px; padding: 5px 10px; letter-spacing: 0.1em; }

    .ow-single__cta {
        padding: 13px 20px;
        font-size: 11px;
        letter-spacing: 0.14em;
        gap: 8px;
        width: 100%;
        justify-content: center;
    }
    .ow-single__cta--lg { padding: 15px 24px; font-size: 11.5px; }

    .ow-single__section-label { font-size: 10px; letter-spacing: 0.22em; margin-bottom: 18px; }
    .ow-single__section-line { width: 18px; }

    .ow-single__intro-body { font-size: 14.5px; line-height: 1.65; }

    .ow-single__video-frame { border-radius: 14px; }
    .ow-single__gallery { gap: 12px; }
    .ow-single__gallery-item { border-radius: 12px; }

    .ow-single__book {
        padding: 44px 20px;
        border-radius: 18px;
    }
    .ow-single__book-eyebrow { font-size: 10px; gap: 8px; margin-bottom: 12px; }
    .ow-single__book-line { width: 18px; }
    .ow-single__book-title {
        font-size: clamp(22px, 6.5vw, 32px);
        line-height: 1.05;
        margin-bottom: 12px;
    }
    .ow-single__book-desc { font-size: 14.5px; margin-bottom: 24px; }

    .ow-single__related-head { flex-direction: column; align-items: flex-start; gap: 10px; margin-bottom: 22px; }
    .ow-single__related-title { font-size: 22px; letter-spacing: 0.03em; }
    .ow-single__related-grid { grid-template-columns: 1fr; gap: 14px; }

    .ow-rel-card { border-radius: 14px; }
    .ow-rel-card__body { padding: 14px 14px 16px; gap: 8px; }
    .ow-rel-card__name { font-size: 13px; }
    .ow-rel-card__tagline { font-size: 12.5px; }
}

/* ===== SMALL PHONES ===== */
@media (max-width: 380px) {
    .ow-single__hero { padding: 48px 14px 32px; }
    .ow-single__section { padding-left: 14px; padding-right: 14px; padding-top: 36px; }
    .ow-single__title { font-size: clamp(22px, 9vw, 32px); }
    .ow-single__meta-item { padding: 11px 14px; }
    .ow-single__cta { padding: 12px 16px; font-size: 10.5px; letter-spacing: 0.1em; }
    .ow-single__book { padding: 36px 16px; }
}
</style>

<?php
endwhile;
get_footer();
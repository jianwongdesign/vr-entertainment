<?php
/**
 * Taxonomy Archive: experience_type
 *
 * Powers all 4 library pages from the Experience CPT:
 *   /experience-type/vr-arcade/
 *   /experience-type/vr-escape/
 *   /experience-type/vr-roam/
 *   /experience-type/private-room/
 *
 * Design ported from the Elementor HTML widget, data sourced from CPT/ACF.
 *
 * @package Overworld
 */

// === Force all experiences to load on a single page ===
// Client-side filters need the full dataset present in the DOM, so we override
// WordPress's default 10-per-page limit just for this taxonomy archive.
// Cap at 200 as a sanity ceiling — adjust upward if you ever exceed it.
if ( is_tax( 'experience_type' ) && ! is_admin() ) {
    $wp_query->query_vars['posts_per_page'] = 200;
    $wp_query = new WP_Query( $wp_query->query_vars );
}

get_header();

$term         = get_queried_object();
$term_name    = $term->name;
$term_slug    = $term->slug;
$total_posts  = $term->count;

/**
 * Marketing copy hardcoded per division.
 * Edit the headline / desc here to update the front-end.
 */
$marketing = array(
    'vr-arcade' => array(
        'eyebrow'   => 'VR Arcade',
        'headline'  => 'Pick your',
        'highlight' => 'next battle',
        'desc'      => 'From zombie shooters to rhythm mayhem, filter by difficulty, party size or vibe to find the perfect game for your session. Mix & match during your booked time.',
    ),
    'vr-escape' => array(
        'eyebrow'   => 'VR Escape Rooms',
        'headline'  => 'Crack the next',
        'highlight' => 'mystery',
        'desc'      => 'Team up and solve immersive escape rooms in virtual worlds. Choose your difficulty and challenge your crew to beat the clock.',
    ),
    'vr-roam' => array(
        'eyebrow'   => 'Free-Roam VR',
        'headline'  => 'Step into',
        'highlight' => 'another world',
        'desc'      => 'Free-roam VR experiences where you walk, run, and feel every moment. No controllers — just full-body immersion.',
    ),
    'private-room' => array(
        'eyebrow'   => 'Private Bookings',
        'headline'  => 'Book your',
        'highlight' => 'arena',
        'desc'      => 'Reserve a private space for your group. Birthdays, corporate events, team-building — fully customizable to your crew.',
    ),
);

$copy = isset( $marketing[ $term_slug ] ) ? $marketing[ $term_slug ] : array(
    'eyebrow'   => 'Overworld VR',
    'headline'  => 'Explore',
    'highlight' => $term_name,
    'desc'      => $term->description,
);

// Vibe label map (matches the ACF Vibes choices).
$vibe_labels = array(
    'shooter'   => 'Shooter',
    'pvp'       => 'PvP / Competitive',
    'rhythm'    => 'Rhythm & Music',
    'puzzle'    => 'Puzzle & Escape',
    'casual'    => 'Casual & Party',
    'adventure' => 'Adventure',
);

// Build the full games dataset as JSON for the JS layer.
$games = array();
if ( have_posts() ) :
    foreach ( $wp_query->posts as $p ) :
        $pid     = $p->ID;
        $tagline = get_field( 'exp_tagline', $pid );
        $vibes   = get_field( 'exp_vibes', $pid );
        $aliases = get_field( 'exp_aliases', $pid );
        $thumb   = get_the_post_thumbnail_url( $pid, 'large' );

        if ( ! is_array( $vibes ) ) $vibes = array();

        // Parse aliases (comma-separated string into array).
        $aliases_arr = array();
        if ( ! empty( $aliases ) ) :
            $aliases_arr = array_map( 'trim', explode( ',', $aliases ) );
            $aliases_arr = array_filter( $aliases_arr );
        endif;

        $games[] = array(
            'name'       => get_the_title( $pid ),
            'url'        => get_permalink( $pid ),
            'img'        => $thumb ? $thumb : '',
            'tagline'    => $tagline ? $tagline : '',
            'players'    => get_field( 'exp_players', $pid ),
            'difficulty' => get_field( 'exp_difficulty', $pid ),
            'age'        => get_field( 'exp_age', $pid ),
            'vibes'      => array_values( $vibes ),
            'aliases'    => array_values( $aliases_arr ),
        );
    endforeach;
endif;

// Pre-compute filter pools (only show pills for values actually present in this division).
$difficulty_pool = array();
$players_pool    = array();
$age_pool        = array();
$vibes_pool      = array();

foreach ( $games as $g ) :
    if ( $g['difficulty'] && ! in_array( $g['difficulty'], $difficulty_pool, true ) ) $difficulty_pool[] = $g['difficulty'];
    if ( $g['players']    && ! in_array( $g['players'],    $players_pool, true ) )    $players_pool[]    = $g['players'];
    if ( $g['age']        && ! in_array( $g['age'],        $age_pool, true ) )        $age_pool[]        = $g['age'];
    foreach ( $g['vibes'] as $v ) :
        if ( ! in_array( $v, $vibes_pool, true ) ) $vibes_pool[] = $v;
    endforeach;
endforeach;

// Sort difficulty meaningfully.
$difficulty_order = array( 'easy', 'medium', 'hard', 'extreme' );
usort( $difficulty_pool, function( $a, $b ) use ( $difficulty_order ) {
    return array_search( $a, $difficulty_order, true ) - array_search( $b, $difficulty_order, true );
} );
sort( $players_pool );
sort( $age_pool );

// Sort vibes by the canonical label order.
$vibe_order = array_keys( $vibe_labels );
usort( $vibes_pool, function( $a, $b ) use ( $vibe_order ) {
    return array_search( $a, $vibe_order, true ) - array_search( $b, $vibe_order, true );
} );
?>

<section class="ow-arcade">
    <div class="ow-arcade__inner">

        <p class="ow-arcade__eyebrow"><?php echo esc_html( $copy['eyebrow'] ); ?> &middot; <?php echo esc_html( $total_posts ); ?> <?php echo $total_posts === 1 ? 'experience' : 'experiences'; ?></p>
        <h1 class="ow-arcade__title"><?php echo esc_html( $copy['headline'] ); ?> <span><?php echo esc_html( $copy['highlight'] ); ?></span>.</h1>
        <p class="ow-arcade__sub"><?php echo esc_html( $copy['desc'] ); ?></p>

        <!-- division switcher -->
        <nav class="ow-arcade__nav" aria-label="Experience categories">
            <?php
            // Only show these terms as filter tabs, in this exact order.
            // To add/remove a tab, edit this array.
            $allowed_tabs = array( 'vr-arcade', 'vr-escape', 'vr-roam' );

            foreach ( $allowed_tabs as $tab_slug ) :
                $type = get_term_by( 'slug', $tab_slug, 'experience_type' );
                if ( ! $type || is_wp_error( $type ) ) continue;
                $is_active = ( $type->slug === $term_slug );
                ?>
                <a href="<?php echo esc_url( get_term_link( $type ) ); ?>"
                   class="ow-arcade__nav-link <?php echo $is_active ? 'is-active' : ''; ?>">
                    <?php echo esc_html( $type->name ); ?>
                    <span class="ow-arcade__nav-count"><?php echo esc_html( $type->count ); ?></span>
                </a>
            <?php
            endforeach;
            ?>
        </nav>

        <?php if ( ! empty( $games ) ) : ?>

            <!-- ===== SEARCH BAR ===== -->
            <div class="ow-controls">
                <label class="ow-search">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                    <input id="owSearch" type="search" placeholder="Try 'beat saber', 'zombie', 'kids', 'party'…" autocomplete="off" />
                    <button type="button" class="clear" id="owClear" aria-label="Clear search">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <line x1="6" y1="6" x2="18" y2="18"/>
                            <line x1="18" y1="6" x2="6" y2="18"/>
                        </svg>
                    </button>
                </label>
                <span class="ow-count">Showing <b id="owCount"><?php echo esc_html( count( $games ) ); ?></b> <?php echo count( $games ) === 1 ? 'Experience' : 'Experiences'; ?></span>
            </div>

            <!-- ===== FILTERS ===== -->
            <div class="ow-filters">

                <?php if ( ! empty( $vibes_pool ) ) : ?>
                    <div class="ow-filter-group" data-group="vibe">
                        <span class="ow-filter-label">Vibe</span>
                        <?php foreach ( $vibes_pool as $v ) : ?>
                            <button type="button" class="ow-pill" aria-pressed="false" data-key="<?php echo esc_attr( $v ); ?>">
                                <?php echo esc_html( isset( $vibe_labels[ $v ] ) ? $vibe_labels[ $v ] : ucfirst( $v ) ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $difficulty_pool ) ) : ?>
                    <div class="ow-filter-group" data-group="difficulty">
                        <span class="ow-filter-label">Difficulty</span>
                        <?php foreach ( $difficulty_pool as $d ) : ?>
                            <button type="button" class="ow-pill" aria-pressed="false" data-key="<?php echo esc_attr( $d ); ?>">
                                <?php echo esc_html( ucfirst( $d ) ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $players_pool ) ) : ?>
                    <div class="ow-filter-group" data-group="players">
                        <span class="ow-filter-label">Players</span>
                        <?php foreach ( $players_pool as $p ) :
                            $label = ( $p === '1' ) ? 'Solo' : $p;
                        ?>
                            <button type="button" class="ow-pill" aria-pressed="false" data-key="<?php echo esc_attr( $p ); ?>">
                                <?php echo esc_html( $label ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="ow-filter-group" data-group="age">
                    <?php if ( ! empty( $age_pool ) ) : ?>
                        <span class="ow-filter-label">Age</span>
                        <?php foreach ( $age_pool as $a ) : ?>
                            <button type="button" class="ow-pill" aria-pressed="false" data-key="<?php echo esc_attr( $a ); ?>">
                                <?php echo esc_html( $a ); ?>
                            </button>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <button type="button" class="ow-reset" id="owReset">Reset all</button>
                </div>

            </div>

            <!-- ===== GRID (rendered by JS) ===== -->
            <div class="ow-grid" id="owGrid"></div>

        <?php else : ?>

            <div class="ow-empty-state">
                <p class="ow-empty-state__title">No <?php echo esc_html( strtolower( $term_name ) ); ?> yet</p>
                <p class="ow-empty-state__hint">New experiences are loading. Check back soon — or explore another category above.</p>
            </div>

        <?php endif; ?>

    </div>
</section>

<style>
/* ============================================
   OVERWORLD VR — Archive Library
   Design ported from Elementor widget, CPT-powered
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

.ow-arcade {
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

    background: radial-gradient(ellipse at top, #1a0e08 0%, var(--ow-bg) 55%) var(--ow-bg);
    color: var(--ow-text);
    font-family: var(--font-body);
    padding: 64px 24px 96px;
    min-height: 100vh;
    position: relative;
    overflow: hidden;
}
.ow-arcade::before {
    content: ""; position: absolute; inset: 0;
    background-image:
      linear-gradient(rgba(255, 90, 31, 0.04) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255, 90, 31, 0.04) 1px, transparent 1px);
    background-size: 48px 48px;
    mask-image: radial-gradient(ellipse at center, black 20%, transparent 75%);
    -webkit-mask-image: radial-gradient(ellipse at center, black 20%, transparent 75%);
    pointer-events: none;
}
.ow-arcade * { box-sizing: border-box; }
.ow-arcade__inner { max-width: 1400px; margin: 0 auto; position: relative; z-index: 1; }

.ow-arcade__eyebrow {
    font-size: 11px; letter-spacing: 0.32em; text-transform: uppercase;
    color: var(--ow-accent); margin: 0 0 12px;
    display: inline-flex; align-items: center; gap: 10px;
}
.ow-arcade__eyebrow::before { content: ""; width: 28px; height: 1px; background: var(--ow-accent); }

.ow-arcade__title {
    font-family: var(--font-display);
    font-size: clamp(28px, 4.5vw, 52px);
    letter-spacing: 0.04em; line-height: 1.05;
    margin: 0 0 16px; text-transform: uppercase;
}
.ow-arcade__title span { color: var(--ow-accent); }

.ow-arcade__sub {
    font-size: 15px; line-height: 1.6;
    color: var(--ow-text-dim); max-width: 620px; margin: 0 0 24px;
}

/* Division switcher */
.ow-arcade__nav {
    display: flex; flex-wrap: wrap; gap: 8px;
    margin: 0 0 32px;
}
.ow-arcade__nav-link {
    display: inline-flex; align-items: center; gap: 10px;
    padding: 10px 18px;
    font-family: var(--font-body);
    font-size: 12px; font-weight: 500;
    letter-spacing: 0.04em;
    color: var(--ow-text-dim);
    text-decoration: none;
    border: 1px solid var(--ow-border);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.02);
    transition: all 0.2s ease;
}
.ow-arcade__nav-link:hover {
    color: var(--ow-text);
    border-color: var(--ow-border-strong);
    background: rgba(255, 255, 255, 0.05);
}
.ow-arcade__nav-link.is-active {
    color: var(--ow-bg);
    background: var(--ow-text);
    border-color: var(--ow-text);
    font-weight: 600;
}
.ow-arcade__nav-count {
    font-size: 11px;
    font-variant-numeric: tabular-nums;
    opacity: 0.65;
}
.ow-arcade__nav-link.is-active .ow-arcade__nav-count { opacity: 0.55; }

/* Search controls bar */
.ow-controls {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 12px; align-items: center;
    background: var(--ow-panel);
    border: 1px solid var(--ow-border);
    border-radius: 20px;
    padding: 10px 10px 10px 20px;
    backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
    margin-bottom: 24px;
}
.ow-search { display: flex; align-items: center; gap: 12px; min-width: 0; }
.ow-search svg { flex-shrink: 0; color: var(--ow-text-faint); }
.ow-search input {
    flex: 1; min-width: 0; background: transparent; border: 0; outline: 0;
    color: var(--ow-text); font-family: var(--font-body); font-size: 15px;
    padding: 12px 0; letter-spacing: 0.01em;
}
.ow-search input::placeholder { color: var(--ow-text-faint); }
.ow-search button.clear {
    background: transparent; border: 0; color: var(--ow-text-faint);
    cursor: pointer; padding: 4px; display: none;
}
.ow-search button.clear.show { display: flex; }
.ow-search button.clear:hover { color: var(--ow-text); }

.ow-count {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px;
    background: rgba(255, 90, 31, 0.08);
    border: 1px solid rgba(255, 90, 31, 0.25);
    border-radius: 999px;
    font-family: var(--font-display);
    font-size: 11px; letter-spacing: 0.12em;
    color: var(--ow-accent); white-space: nowrap;
}
.ow-count b { font-weight: 400; color: #ffb896; }

/* Filter rows */
.ow-filters { display: flex; flex-direction: column; gap: 14px; margin-bottom: 40px; }
.ow-filter-group { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }
.ow-filter-label {
    font-family: var(--font-display);
    font-size: 10px; letter-spacing: 0.24em; text-transform: uppercase;
    color: var(--ow-text-faint); min-width: 88px; padding-right: 4px;
}

/* Pill buttons — !important everywhere to defeat Hello Elementor's button styles */
.ow-arcade button.ow-pill,
.ow-arcade button.ow-pill:link,
.ow-arcade button.ow-pill:visited {
    background: transparent !important;
    background-color: transparent !important;
    background-image: none !important;
    border: 1px solid var(--ow-border-strong) !important;
    color: var(--ow-text-dim) !important;
    padding: 8px 16px !important;
    border-radius: 999px !important;
    font-family: var(--font-body) !important;
    font-size: 12px !important;
    letter-spacing: 0.06em !important;
    text-transform: uppercase !important;
    cursor: pointer !important;
    transition: all 0.18s ease !important;
    white-space: nowrap !important;
    box-shadow: none !important;
    text-shadow: none !important;
    text-decoration: none !important;
    outline: none !important;
    line-height: 1.2 !important;
    margin: 0 !important;
    -webkit-appearance: none !important;
            appearance: none !important;
}
.ow-arcade button.ow-pill:hover,
.ow-arcade button.ow-pill:focus,
.ow-arcade button.ow-pill:active,
.ow-arcade button.ow-pill:focus-visible {
    background: transparent !important;
    background-color: transparent !important;
    background-image: none !important;
    color: var(--ow-text) !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
    box-shadow: none !important;
    outline: none !important;
    transform: none !important;
}
.ow-arcade button.ow-pill[aria-pressed="true"],
.ow-arcade button.ow-pill[aria-pressed="true"]:hover,
.ow-arcade button.ow-pill[aria-pressed="true"]:focus,
.ow-arcade button.ow-pill[aria-pressed="true"]:active,
.ow-arcade button.ow-pill[aria-pressed="true"]:focus-visible {
    background: var(--ow-accent) !important;
    background-color: var(--ow-accent) !important;
    background-image: none !important;
    border-color: var(--ow-accent) !important;
    color: #0b0b0c !important;
    font-weight: 700 !important;
    box-shadow: 0 0 0 3px rgba(255, 90, 31, 0.18), 0 6px 18px -6px var(--ow-accent-glow) !important;
    outline: none !important;
}

/* Reset link-button */
.ow-arcade button.ow-reset,
.ow-arcade button.ow-reset:link,
.ow-arcade button.ow-reset:visited {
    margin-left: auto !important;
    background: transparent !important;
    background-color: transparent !important;
    background-image: none !important;
    border: 0 !important;
    color: var(--ow-text-faint) !important;
    cursor: pointer !important;
    font-family: var(--font-body) !important;
    font-size: 12px !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    text-decoration: underline !important;
    text-underline-offset: 4px !important;
    padding: 6px !important;
    box-shadow: none !important;
    outline: none !important;
    -webkit-appearance: none !important;
            appearance: none !important;
}
.ow-arcade button.ow-reset:hover,
.ow-arcade button.ow-reset:focus,
.ow-arcade button.ow-reset:active,
.ow-arcade button.ow-reset:focus-visible {
    background: transparent !important;
    background-color: transparent !important;
    color: var(--ow-accent) !important;
    box-shadow: none !important;
    outline: none !important;
}

/* Search input — defend against Elementor form styles */
.ow-arcade .ow-search input,
.ow-arcade .ow-search input:focus,
.ow-arcade .ow-search input:hover {
    background: transparent !important;
    background-color: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    outline: none !important;
    color: var(--ow-text) !important;
    -webkit-appearance: none !important;
            appearance: none !important;
}
.ow-arcade .ow-search button.clear,
.ow-arcade .ow-search button.clear:hover,
.ow-arcade .ow-search button.clear:focus,
.ow-arcade .ow-search button.clear:active {
    background: transparent !important;
    background-color: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    outline: none !important;
    color: var(--ow-text-faint) !important;
    padding: 4px !important;
    -webkit-appearance: none !important;
            appearance: none !important;
}
.ow-arcade .ow-search button.clear:hover { color: var(--ow-text) !important; }

/* Grid */
.ow-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}
.ow-card {
    position: relative;
    background: var(--ow-bg-2);
    border: 1px solid var(--ow-border);
    border-radius: 20px; overflow: hidden;
    transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    text-decoration: none; color: inherit;
    display: flex; flex-direction: column;
    animation: cardIn 0.4s ease backwards;
}
@keyframes cardIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
.ow-card:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 90, 31, 0.5);
    box-shadow: 0 20px 40px -20px rgba(255, 90, 31, 0.4);
}

.ow-card__media { position: relative; aspect-ratio: 16 / 10; overflow: hidden; background: #0a0a0a; }
.ow-card__media img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform 0.5s ease;
}
.ow-card:hover .ow-card__media img { transform: scale(1.06); }
.ow-card__media::after {
    content: ""; position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(11,11,12,0.85) 0%, transparent 45%);
    pointer-events: none;
}
.ow-card__placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #1a1d2e 0%, #0f111a 100%);
    font-family: var(--font-display);
    font-size: 64px; color: rgba(255,255,255,.08);
}
.ow-card__diff {
    position: absolute; top: 12px; left: 12px;
    font-family: var(--font-display);
    font-size: 10px; letter-spacing: 0.14em; text-transform: uppercase;
    padding: 5px 10px; border-radius: 4px;
    backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 2;
}
.ow-card__diff[data-diff="easy"]    { background: rgba(74, 222, 128, 0.15); color: var(--ow-easy);    border: 1px solid rgba(74, 222, 128, 0.35); }
.ow-card__diff[data-diff="medium"]  { background: rgba(250, 204, 21, 0.12); color: var(--ow-medium);  border: 1px solid rgba(250, 204, 21, 0.3); }
.ow-card__diff[data-diff="hard"]    { background: rgba(239, 68, 68, 0.15);  color: var(--ow-hard);    border: 1px solid rgba(239, 68, 68, 0.35); }
.ow-card__diff[data-diff="extreme"] { background: rgba(185, 28, 28, 0.18);  color: #fca5a5;           border: 1px solid rgba(185, 28, 28, 0.4); }

.ow-card__body {
    padding: 18px 18px 20px;
    display: flex; flex-direction: column; gap: 12px; flex: 1;
}
.ow-card__name {
    font-family: var(--font-display);
    font-size: 14px; letter-spacing: 0.05em; text-transform: uppercase;
    line-height: 1.25; margin: 0; color: var(--ow-text);
}
.ow-card__meta {
    display: flex; flex-wrap: wrap; gap: 6px 10px;
    font-size: 11px; color: var(--ow-text-dim); letter-spacing: 0.04em;
}
.ow-card__meta span { display: inline-flex; align-items: center; gap: 5px; }
.ow-card__meta svg { opacity: 0.6; }

.ow-card__genres {
    display: flex; flex-wrap: wrap; gap: 6px;
    margin-top: auto; padding-top: 4px;
}
.ow-card__genre {
    font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase;
    padding: 3px 8px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--ow-border);
    border-radius: 4px; color: var(--ow-text-dim);
}

.ow-card__cta {
    display: flex; align-items: center; justify-content: space-between;
    padding-top: 12px; margin-top: 8px;
    border-top: 1px solid var(--ow-border);
    font-family: var(--font-display);
    font-size: 10px; letter-spacing: 0.2em; color: var(--ow-accent);
}
.ow-card__cta svg { transition: transform 0.2s ease; }
.ow-card:hover .ow-card__cta svg { transform: translateX(4px); }

.ow-empty {
    grid-column: 1 / -1; text-align: center;
    padding: 80px 20px; color: var(--ow-text-faint);
}
.ow-empty__title {
    font-family: var(--font-display);
    font-size: 16px; letter-spacing: 0.15em;
    color: var(--ow-text-dim); margin: 0 0 8px;
}
.ow-empty__hint { font-size: 13px; }

.ow-empty-state {
    text-align: center; padding: 100px 20px;
    color: var(--ow-text-faint);
}
.ow-empty-state__title {
    font-family: var(--font-display);
    font-size: 16px; letter-spacing: 0.15em;
    color: var(--ow-text-dim); margin: 0 0 8px;
}
.ow-empty-state__hint { font-size: 13px; }

mark {
    background: rgba(255, 90, 31, 0.25);
    color: #ffd4bf; padding: 0 2px; border-radius: 2px;
}

@media (max-width: 980px) {
    .ow-arcade { padding: 56px 24px 80px; }
    .ow-arcade__title { font-size: clamp(26px, 5vw, 44px); }
    .ow-arcade__sub { font-size: 14.5px; }
    .ow-arcade__nav { gap: 6px; margin-bottom: 28px; }
    .ow-arcade__nav-link { padding: 9px 14px; font-size: 11.5px; }

    .ow-controls {
        padding: 8px 8px 8px 16px;
        gap: 10px;
    }
    .ow-search input { font-size: 14px; }
    .ow-count { padding: 9px 14px; font-size: 10.5px; }

    .ow-filter-label { min-width: 76px; font-size: 9.5px; letter-spacing: 0.2em; }
    .ow-arcade button.ow-pill { padding: 7px 14px !important; font-size: 11px !important; }

    .ow-grid { grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
}

@media (max-width: 640px) {
    .ow-arcade { padding: 40px 16px 64px; }
    .ow-arcade__eyebrow { font-size: 10px; letter-spacing: 0.24em; margin-bottom: 10px; }
    .ow-arcade__title { font-size: clamp(24px, 7vw, 36px); margin-bottom: 12px; }
    .ow-arcade__sub { font-size: 14px; margin-bottom: 20px; }

    .ow-arcade__nav { margin-bottom: 24px; }
    .ow-arcade__nav-link { padding: 8px 12px; font-size: 11px; gap: 6px; }
    .ow-arcade__nav-count { font-size: 10px; }

    .ow-controls {
        grid-template-columns: 1fr;
        padding: 8px 12px 12px;
        border-radius: 16px;
        margin-bottom: 18px;
    }
    .ow-search { padding-bottom: 6px; border-bottom: 1px solid var(--ow-border); }
    .ow-search input { padding: 10px 0; font-size: 14px; }
    .ow-count { justify-self: start; padding: 7px 12px; font-size: 10px; }

    .ow-filters { gap: 12px; margin-bottom: 28px; }
    .ow-filter-group { gap: 8px; }
    .ow-filter-label {
        min-width: 100%;
        margin-bottom: -4px;
        font-size: 9.5px;
        letter-spacing: 0.22em;
    }
    .ow-arcade button.ow-pill {
        padding: 7px 13px !important;
        font-size: 10.5px !important;
        letter-spacing: 0.04em !important;
    }
    .ow-arcade button.ow-reset { margin-left: 0 !important; font-size: 11px !important; }

    .ow-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; }
    .ow-card { border-radius: 14px; }
    .ow-card__body { padding: 12px 12px 14px; gap: 8px; }
    .ow-card__name { font-size: 11.5px; letter-spacing: 0.04em; line-height: 1.2; }
    .ow-card__meta { font-size: 10px; gap: 4px 8px; }
    .ow-card__meta svg { width: 11px; height: 11px; }
    .ow-card__genre { font-size: 9px; padding: 2px 6px; }
    .ow-card__cta { font-size: 9px; padding-top: 10px; margin-top: 4px; letter-spacing: 0.16em; }
    .ow-card__diff { font-size: 9px; padding: 4px 8px; top: 8px; left: 8px; letter-spacing: 0.1em; }

    .ow-arcade__nav-link { font-size: 11px; padding: 8px 14px; }
}

@media (max-width: 380px) {
    .ow-arcade { padding: 32px 12px 56px; }
    .ow-search input { font-size: 13.5px; }
    .ow-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
    .ow-card__body { padding: 10px 10px 12px; }
}
</style>

<script>
(function(){
    if (window.__owArcadeInit) return;
    window.__owArcadeInit = true;

    // Data injected from PHP
    const GAMES = <?php echo wp_json_encode( $games ); ?>;
    const VIBE_LABELS = <?php echo wp_json_encode( $vibe_labels ); ?>;

    if (!GAMES || !GAMES.length) return;

    function init(){
        const grid    = document.getElementById('owGrid');
        const search  = document.getElementById('owSearch');
        const clearBtn = document.getElementById('owClear');
        const countEl = document.getElementById('owCount');
        const reset   = document.getElementById('owReset');

        if (!grid) return;

        const state = {
            search: '',
            vibe:       new Set(),
            difficulty: new Set(),
            players:    new Set(),
            age:        new Set(),
        };

        // Wire pills
        document.querySelectorAll('.ow-filter-group').forEach(group => {
            const type = group.dataset.group;
            if (!type || !state[type]) return;
            group.querySelectorAll('.ow-pill').forEach(pill => {
                pill.addEventListener('click', () => {
                    const key = pill.dataset.key;
                    if (state[type].has(key)) {
                        state[type].delete(key);
                        pill.setAttribute('aria-pressed', 'false');
                    } else {
                        state[type].add(key);
                        pill.setAttribute('aria-pressed', 'true');
                    }
                    render();
                });
            });
        });

        // Wire search
        if (search) {
            // Shorten placeholder on small screens
            const updatePlaceholder = () => {
                if (window.innerWidth < 480) {
                    search.placeholder = "Search…";
                } else if (window.innerWidth < 760) {
                    search.placeholder = "Search 'beat saber', 'kids'…";
                } else {
                    search.placeholder = "Try 'beat saber', 'zombie', 'kids', 'party'…";
                }
            };
            updatePlaceholder();
            window.addEventListener('resize', updatePlaceholder);

            search.addEventListener('input', e => {
                state.search = e.target.value;
                render();
            });
        }
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                state.search = '';
                if (search) { search.value = ''; search.focus(); }
                render();
            });
        }

        // Wire reset
        if (reset) {
            reset.addEventListener('click', () => {
                state.search = '';
                state.vibe.clear();
                state.difficulty.clear();
                state.players.clear();
                state.age.clear();
                if (search) search.value = '';
                document.querySelectorAll('.ow-pill[aria-pressed="true"]').forEach(p => {
                    p.setAttribute('aria-pressed', 'false');
                });
                render();
            });
        }

        // Search helpers
        function normalize(s){
            return (s || '').toLowerCase()
                .replace(/[:'"\-.,!?()]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim();
        }
        function searchableHaystack(g){
            return normalize([
                g.name,
                g.tagline,
                g.players,
                g.difficulty,
                g.age ? ('age ' + g.age + ' ' + g.age + ' plus') : '',
                ...(g.vibes || []),
                ...((g.vibes || []).map(v => VIBE_LABELS[v] || '')),
                ...(g.aliases || []),
            ].join(' '));
        }
        function tokensMatch(haystack, query){
            const tokens = query.split(' ').filter(Boolean);
            return tokens.every(t => haystack.indexOf(t) !== -1);
        }
        function highlight(text, q){
            if (!q) return text;
            const tokens = q.split(/\s+/).filter(Boolean);
            if (!tokens.length) return text;
            const re = new RegExp('(' + tokens.map(t => t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|') + ')', 'ig');
            return text.replace(re, '<mark>$1</mark>');
        }
        function escapeHtml(s){
            return String(s || '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

        function matchFilters(g){
            if (state.vibe.size) {
                const any = [...state.vibe].some(v => (g.vibes || []).includes(v));
                if (!any) return false;
            }
            if (state.difficulty.size && !state.difficulty.has(g.difficulty)) return false;
            if (state.players.size && !state.players.has(g.players)) return false;
            if (state.age.size && !state.age.has(g.age)) return false;
            if (state.search) {
                const q = normalize(state.search);
                if (q && !tokensMatch(searchableHaystack(g), q)) return false;
            }
            return true;
        }

        function render(){
            const results = GAMES.filter(matchFilters);
            if (countEl) countEl.textContent = results.length;
            if (clearBtn) clearBtn.classList.toggle('show', state.search.length > 0);

            if (!results.length) {
                grid.innerHTML = `
                    <div class="ow-empty">
                        <p class="ow-empty__title">No experiences match that combo</p>
                        <p class="ow-empty__hint">Try loosening a filter, or reset them all.</p>
                    </div>`;
                return;
            }

            const qNorm = normalize(state.search);

            grid.innerHTML = results.map((g, i) => {
                const vibeChips = (g.vibes || []).map(v => `<span class="ow-card__genre">${escapeHtml(VIBE_LABELS[v] || v)}</span>`).join('');
                const diffBadge = g.difficulty
                    ? `<span class="ow-card__diff" data-diff="${escapeHtml(g.difficulty)}">${escapeHtml(g.difficulty)}</span>`
                    : '';
                const mediaInner = g.img
                    ? `<img decoding="async" loading="lazy" src="${escapeHtml(g.img)}" alt="${escapeHtml(g.name)}" onerror="this.style.opacity=0.3" />`
                    : `<div class="ow-card__placeholder">${escapeHtml((g.name || '?').charAt(0))}</div>`;
                const playersLine = g.players ? `<span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    ${escapeHtml(g.players)}
                </span>` : '';
                const ageLine = g.age ? `<span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"/><path d="M12 18v4"/><path d="m4.93 4.93 2.83 2.83"/><path d="m16.24 16.24 2.83 2.83"/><path d="M2 12h4"/><path d="M18 12h4"/><path d="m4.93 19.07 2.83-2.83"/><path d="m16.24 7.76 2.83-2.83"/></svg>
                    ${escapeHtml(g.age)}
                </span>` : '';

                return `
                    <a class="ow-card" href="${escapeHtml(g.url)}" style="animation-delay:${Math.min(i*30, 400)}ms">
                        <div class="ow-card__media">
                            ${diffBadge}
                            ${mediaInner}
                        </div>
                        <div class="ow-card__body">
                            <h3 class="ow-card__name">${highlight(escapeHtml(g.name), qNorm)}</h3>
                            <div class="ow-card__meta">
                                ${playersLine}
                                ${ageLine}
                            </div>
                            ${vibeChips ? `<div class="ow-card__genres">${vibeChips}</div>` : ''}
                            <div class="ow-card__cta">
                                <span>Learn More</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </div>
                        </div>
                    </a>`;
            }).join('');
        }

        render();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

<?php
get_footer();
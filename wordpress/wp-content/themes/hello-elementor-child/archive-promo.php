<?php
/**
 * Template — archive-promo.php
 *
 * Lists all published promos at /promos/ in a clean horizontal list layout:
 * poster on the left, all metadata on the right. Auto-expired promos are
 * filtered out by the pre_get_posts hook in the CPT snippet.
 *
 * INSTALLATION:
 * =============
 * Save this file to your child theme:
 *   /wp-content/themes/hello-elementor-child/archive-promo.php
 *
 * @package OverworldChild
 */

get_header();
?>

<style>
  /* ============================================================
     OVERWORLD :: PROMOS ARCHIVE — LIST VIEW
     ============================================================ */
  .ow-promos{
    --ow-bg:#0a0a0f;
    --ow-bg-2:#141419;
    --ow-lava:#ff5722;
    --ow-cyan:#22e3ff;
    --ow-fg:#fff;
    --ow-dim:rgba(255,255,255,.55);
    --ow-line:rgba(255,255,255,.1);
    --ow-line-strong:rgba(255,255,255,.2);
    background:var(--ow-bg);
    color:var(--ow-fg);
    font-family:'Space Grotesk','Inter',system-ui,sans-serif;
    min-height:100vh;
    padding:80px 40px 120px;
  }
  .ow-promos *{box-sizing:border-box;}
  .ow-promos__inner{max-width:1200px;margin:0 auto;}

  /* ====== Header ====== */
  .ow-promos__eyebrow{
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.2em;text-transform:uppercase;
    color:var(--ow-lava);
    display:flex;align-items:center;gap:10px;margin-bottom:20px;
  }
  .ow-promos__eyebrow::before{
    content:"";width:28px;height:1px;background:var(--ow-lava);
  }
  .ow-promos__title{
    font-size:clamp(48px,6vw,84px);
    line-height:.95;letter-spacing:-.03em;
    font-weight:700;margin:0 0 16px;
    text-transform:uppercase;
  }
  .ow-promos__title em{
    font-style:normal;color:var(--ow-lava);font-weight:700;
  }
  .ow-promos__lede{
    font-size:18px;color:var(--ow-dim);
    max-width:640px;margin:0 0 60px;line-height:1.55;
  }

  /* ====== List of promos ====== */
  .ow-promos__list{
    display:flex;flex-direction:column;gap:20px;
  }

  .ow-promos__item{
    display:grid;
    grid-template-columns:340px 1fr;
    gap:0;
    background:var(--ow-bg-2);
    border:1px solid var(--ow-line);
    border-radius:18px;
    overflow:hidden;
    text-decoration:none;
    color:inherit;
    transition:transform .35s ease, border-color .25s ease, box-shadow .35s ease;
  }
  .ow-promos__item:hover{
    transform:translateY(-3px);
    border-color:rgba(255,87,34,.4);
    box-shadow:0 16px 50px -16px rgba(255,87,34,.25);
  }

  /* Left: poster */
  .ow-promos__poster{
    aspect-ratio:1/1;
    overflow:hidden;
    position:relative;
    background:#0a0a0f;
  }
  .ow-promos__poster img{
    width:100%;height:100%;
    object-fit:cover;display:block;
    transition:transform .6s ease;
  }
  .ow-promos__item:hover .ow-promos__poster img{transform:scale(1.06);}
  .ow-promos__poster--empty{
    display:flex;align-items:center;justify-content:center;
    background:repeating-linear-gradient(45deg,#1c1c24,#1c1c24 8px,#22222c 8px,#22222c 16px);
    color:rgba(255,255,255,.25);
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.15em;text-transform:uppercase;
    text-align:center;padding:20px;
  }

  /* Right: body */
  .ow-promos__body{
    padding:32px 36px;
    display:flex;flex-direction:column;justify-content:center;
    gap:14px;
    min-height:0;
  }

  .ow-promos__meta-row{
    display:flex;align-items:center;gap:14px;
    flex-wrap:wrap;
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.18em;text-transform:uppercase;
  }
  .ow-promos__date{color:var(--ow-lava);}
  .ow-promos__valid{
    color:var(--ow-dim);
    display:inline-flex;align-items:center;gap:6px;
  }
  .ow-promos__valid::before{
    content:"";width:4px;height:4px;border-radius:50%;
    background:var(--ow-dim);
  }
  .ow-promos__valid--expiring{color:#f5c84b;}
  .ow-promos__valid--expiring::before{background:#f5c84b;}

  .ow-promos__name{
    font-size:30px;font-weight:700;
    line-height:1.05;letter-spacing:-.02em;
    text-transform:uppercase;margin:0;
    color:var(--ow-fg);
  }

  .ow-promos__tagline{
    font-size:15px;color:var(--ow-cyan);
    font-weight:500;letter-spacing:.005em;
    margin:0;line-height:1.4;
  }

  .ow-promos__excerpt{
    font-size:14.5px;color:var(--ow-dim);
    line-height:1.55;margin:0;
    display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;
    overflow:hidden;
  }

  .ow-promos__outlets{
    display:flex;flex-wrap:wrap;gap:6px;margin-top:2px;
  }
  .ow-promos__outlet{
    font-family:'JetBrains Mono',monospace;
    font-size:10px;letter-spacing:.14em;text-transform:uppercase;
    padding:5px 10px;border:1px solid var(--ow-line-strong);
    border-radius:999px;color:rgba(255,255,255,.75);
  }
  .ow-promos__outlet--all{
    border-color:var(--ow-lava);color:var(--ow-lava);
  }

  .ow-promos__cta-row{
    display:flex;align-items:center;gap:14px;
    margin-top:8px;flex-wrap:wrap;
  }
  .ow-promos__cta{
    font-family:'JetBrains Mono',monospace;
    font-size:11.5px;letter-spacing:.14em;text-transform:uppercase;
    padding:11px 18px;border-radius:999px;
    background:var(--ow-lava);color:#0a0a0f;font-weight:700;
    text-decoration:none;display:inline-flex;align-items:center;gap:6px;
    border:1px solid var(--ow-lava);
    transition:transform .2s ease, gap .25s ease;
  }
  .ow-promos__cta:hover{transform:translateY(-1px);gap:10px;}
  .ow-promos__view{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    color:var(--ow-dim);
    display:inline-flex;align-items:center;gap:6px;
    transition:color .2s ease, gap .25s ease;
  }
  .ow-promos__item:hover .ow-promos__view{color:var(--ow-fg);gap:10px;}

  /* ====== Empty state ====== */
  .ow-promos__empty{
    border:1px dashed var(--ow-line);
    border-radius:16px;
    padding:80px 30px;
    text-align:center;color:var(--ow-dim);
  }
  .ow-promos__empty h3{
    color:var(--ow-fg);font-size:24px;margin:0 0 12px;
    text-transform:uppercase;letter-spacing:.02em;
  }
  .ow-promos__empty p{font-size:15px;line-height:1.6;margin:0;}

  /* ====== Pagination ====== */
  .ow-promos__pagination{
    margin-top:60px;display:flex;justify-content:center;gap:8px;flex-wrap:wrap;
  }
  .ow-promos__pagination a,
  .ow-promos__pagination span{
    font-family:'JetBrains Mono',monospace;font-size:12px;
    letter-spacing:.14em;text-transform:uppercase;
    padding:10px 16px;border-radius:999px;
    border:1px solid var(--ow-line);color:var(--ow-dim);
    text-decoration:none;transition:.2s;
  }
  .ow-promos__pagination a:hover{border-color:var(--ow-lava);color:var(--ow-fg);}
  .ow-promos__pagination .current{
    background:var(--ow-lava);border-color:var(--ow-lava);color:#0a0a0f;font-weight:700;
  }

  /* ====== Responsive ====== */
  @media (max-width:900px){
    .ow-promos{padding:60px 28px 100px;}
    .ow-promos__item{grid-template-columns:240px 1fr;}
    .ow-promos__body{padding:24px 28px;gap:12px;}
    .ow-promos__name{font-size:24px;}
  }
  @media (max-width:680px){
    .ow-promos{padding:50px 18px 80px;}
    .ow-promos__item{grid-template-columns:1fr;}
    .ow-promos__poster{aspect-ratio:16/10;}
    .ow-promos__body{padding:22px 22px 26px;}
    .ow-promos__name{font-size:22px;}
    .ow-promos__lede{font-size:16px;margin-bottom:40px;}
  }
</style>

<section class="ow-promos">
  <div class="ow-promos__inner">

    <div class="ow-promos__eyebrow">Live Offers · Updated Weekly</div>
    <h1 class="ow-promos__title">Current <em>Promos</em></h1>
    <p class="ow-promos__lede">Limited-time deals, seasonal bundles, and exclusive offers across all Overworld outlets. Catch them while they last.</p>

    <?php if ( have_posts() ) : ?>

      <div class="ow-promos__list">
        <?php while ( have_posts() ) : the_post();
          // Pull ACF fields.
          $tagline      = function_exists( 'get_field' ) ? get_field( 'promo_tagline' )      : '';
          $valid_until  = function_exists( 'get_field' ) ? get_field( 'promo_valid_until' )  : '';
          $outlets      = function_exists( 'get_field' ) ? get_field( 'promo_outlets' )      : array();
          $cta_label    = function_exists( 'get_field' ) ? get_field( 'promo_cta_label' )    : '';
          $cta_url      = function_exists( 'get_field' ) ? get_field( 'promo_cta_url' )      : '';
          $poster       = function_exists( 'get_field' ) ? get_field( 'promo_poster' )       : null;

          // Fallback: use Featured Image if no ACF poster set.
          $poster_url = '';
          $poster_alt = get_the_title();
          if ( is_array( $poster ) && ! empty( $poster['url'] ) ) {
              $poster_url = $poster['url'];
              if ( ! empty( $poster['alt'] ) ) $poster_alt = $poster['alt'];
          } elseif ( has_post_thumbnail() ) {
              $poster_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
          }

          // Format valid-until date — flag promos expiring within 7 days.
          $valid_label = '';
          $valid_class = '';
          if ( $valid_until ) {
              $end_ts = strtotime( $valid_until );
              if ( $end_ts ) {
                  $days_left = floor( ( $end_ts - time() ) / DAY_IN_SECONDS );
                  $valid_label = 'Valid until ' . date_i18n( 'd M Y', $end_ts );
                  if ( $days_left <= 7 && $days_left >= 0 ) {
                      $valid_class = 'ow-promos__valid--expiring';
                      $valid_label .= ' · ' . ( $days_left === 0 ? 'Last day!' : $days_left . ' days left' );
                  }
              }
          }
        ?>

          <a class="ow-promos__item" href="<?php the_permalink(); ?>">

            <?php if ( $poster_url ) : ?>
              <div class="ow-promos__poster">
                <img src="<?php echo esc_url( $poster_url ); ?>" alt="<?php echo esc_attr( $poster_alt ); ?>" loading="lazy" />
              </div>
            <?php else : ?>
              <div class="ow-promos__poster ow-promos__poster--empty">[ Add Poster ]</div>
            <?php endif; ?>

            <div class="ow-promos__body">

              <div class="ow-promos__meta-row">
                <span class="ow-promos__date"><?php echo esc_html( get_the_date( 'M Y' ) ); ?></span>
                <?php if ( $valid_label ) : ?>
                  <span class="ow-promos__valid <?php echo esc_attr( $valid_class ); ?>"><?php echo esc_html( $valid_label ); ?></span>
                <?php endif; ?>
              </div>

              <h2 class="ow-promos__name"><?php the_title(); ?></h2>

              <?php if ( $tagline ) : ?>
                <p class="ow-promos__tagline"><?php echo esc_html( $tagline ); ?></p>
              <?php endif; ?>

              <?php if ( has_excerpt() ) : ?>
                <p class="ow-promos__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
              <?php endif; ?>

              <?php if ( ! empty( $outlets ) && is_array( $outlets ) ) : ?>
                <div class="ow-promos__outlets">
                  <?php foreach ( $outlets as $outlet ) :
                    $is_all = ( strtolower( $outlet ) === 'all' || strtolower( $outlet ) === 'all outlets' );
                    $cls = $is_all ? 'ow-promos__outlet ow-promos__outlet--all' : 'ow-promos__outlet';
                  ?>
                    <span class="<?php echo esc_attr( $cls ); ?>"><?php echo esc_html( $outlet ); ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <div class="ow-promos__cta-row">
                <?php if ( $cta_url && $cta_label ) : ?>
                  <span class="ow-promos__cta" onclick="event.preventDefault();event.stopPropagation();window.open('<?php echo esc_js( $cta_url ); ?>','_blank');">
                    <?php echo esc_html( $cta_label ); ?> →
                  </span>
                <?php endif; ?>
                <span class="ow-promos__view">View Details →</span>
              </div>

            </div>

          </a>

        <?php endwhile; ?>
      </div>

      <?php
      // Pagination
      $pagination = paginate_links( array(
          'prev_text' => '← Prev',
          'next_text' => 'Next →',
      ) );

      if ( $pagination ) {
          echo '<div class="ow-promos__pagination">' . $pagination . '</div>';
      }
      ?>

    <?php else : ?>

      <div class="ow-promos__empty">
        <h3>No Active Promos Right Now</h3>
        <p>Check back soon — new offers are added every few weeks.<br>
        Follow us on social to be the first to know.</p>
      </div>

    <?php endif; ?>

  </div>
</section>

<?php get_footer(); ?>

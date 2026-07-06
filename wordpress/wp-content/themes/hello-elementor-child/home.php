<?php
/**
 * Blog index template — renders the posts page (/blog/).
 *
 * INSTALL: Upload to your child theme:
 *   /wp-content/themes/hello-elementor-child/home.php
 *
 * Dark card-grid listing of standard WordPress posts, in the same design
 * language as the outlet / event templates. Orange (lava) accent, since the
 * blog is site-wide rather than outlet-specific.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Meta description for the blog index (no SEO plugin on this site).
add_action( 'wp_head', function () {
    echo '<meta name="description" content="News and behind-the-scenes stories from Overworld — Singapore\'s VR arcade and immersive game destination at Kallang Wave Mall, Orchard Central and Funan." />' . "\n";
}, 5 );

get_header();
?>

<style>
  .ow-blog{
    --accent:#ff5a1f;
    --accent-glow:#ff8a3d;
    --bg:#0a0a14;
    --bg-2:#13131f;
    --fg:#fff;
    --dim:rgba(220,225,240,.65);
    --line:rgba(255,255,255,.08);
    background:var(--bg);
    color:var(--fg);
    font-family:'Space Grotesk','Inter',system-ui,sans-serif;
  }
  .ow-blog *{box-sizing:border-box;}

  /* ===== HERO ===== */
  .ow-blog__hero{
    position:relative;
    background:radial-gradient(ellipse at 50% 110%,#1a0a05 0%,#0d0608 50%,#0a0606 100%);
    padding:110px 40px 70px;
    overflow:hidden;
  }
  .ow-blog__hero::before{
    content:"";position:absolute;left:0;right:0;bottom:0;height:80%;
    background:radial-gradient(ellipse at center,rgba(255,90,31,.18) 0%,transparent 70%);
    filter:blur(60px);pointer-events:none;
  }
  .ow-blog__hero-grid{
    position:absolute;inset:0;pointer-events:none;
    background-image:
      linear-gradient(rgba(255,90,31,.05) 1px,transparent 1px),
      linear-gradient(90deg,rgba(255,90,31,.05) 1px,transparent 1px);
    background-size:60px 60px;
    mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
    -webkit-mask-image:radial-gradient(ellipse at center,black 0%,transparent 75%);
  }
  .ow-blog__hero-inner{
    max-width:1100px;margin:0 auto;
    position:relative;z-index:2;text-align:center;
  }
  .ow-blog__eyebrow{
    display:inline-flex;align-items:center;gap:12px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.24em;text-transform:uppercase;
    color:var(--accent-glow);
    padding:9px 18px;
    border:1px solid rgba(255,90,31,.4);
    border-radius:999px;
    background:rgba(255,90,31,.08);
    margin-bottom:26px;
  }
  .ow-blog__eyebrow::before{
    content:"";width:8px;height:8px;border-radius:50%;
    background:var(--accent);
    box-shadow:0 0 12px var(--accent-glow);
  }
  .ow-blog__title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(44px,6.5vw,96px);
    line-height:1;letter-spacing:-.02em;
    font-weight:400;text-transform:uppercase;
    margin:0 0 16px;
    background:linear-gradient(180deg,#fff 0%,#fff 45%,var(--accent-glow) 100%);
    -webkit-background-clip:text;background-clip:text;
    -webkit-text-fill-color:transparent;
  }
  .ow-blog__desc{
    font-size:15px;color:var(--dim);line-height:1.6;
    margin:0 auto;max-width:560px;
  }

  /* ===== GRID ===== */
  .ow-blog__main{
    padding:70px 40px 100px;
    background:var(--bg);
  }
  .ow-blog__main-inner{
    max-width:1200px;margin:0 auto;
  }
  .ow-blog__grid{
    display:grid;grid-template-columns:repeat(3,1fr);gap:24px;
  }
  .ow-blog__card{
    display:flex;flex-direction:column;
    background:var(--bg-2);
    border:1px solid var(--line);
    border-radius:20px;
    overflow:hidden;text-decoration:none;
    position:relative;
    transition:transform .35s ease, border-color .25s ease, box-shadow .35s ease;
  }
  .ow-blog__card::before{
    content:"";position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(to right,transparent,var(--accent),transparent);
    opacity:.6;z-index:1;
  }
  .ow-blog__card:hover{
    transform:translateY(-6px);
    border-color:var(--accent);
    box-shadow:0 20px 60px -20px var(--accent);
  }
  .ow-blog__card-img{
    position:relative;aspect-ratio:16/9;
    background:rgba(255,255,255,.02);overflow:hidden;
  }
  .ow-blog__card-img img{
    width:100% !important;height:100% !important;
    object-fit:cover !important;display:block !important;
    position:absolute !important;top:0 !important;left:0 !important;
    max-width:none !important;max-height:none !important;
  }
  .ow-blog__card-img-empty{
    width:100%;height:100%;
    display:flex;align-items:center;justify-content:center;
    font-size:40px;opacity:.4;color:var(--dim);
    background:radial-gradient(ellipse at center,rgba(255,90,31,.1) 0%,transparent 60%);
  }
  .ow-blog__card-body{
    padding:22px 24px 24px;
    flex:1;display:flex;flex-direction:column;
  }
  .ow-blog__card-date{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.16em;text-transform:uppercase;
    color:var(--accent-glow);margin-bottom:10px;
  }
  .ow-blog__card-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:22px;line-height:1.15;font-weight:400;
    text-transform:uppercase;color:#fff;
    margin:0 0 10px;letter-spacing:-.005em;
  }
  .ow-blog__card-excerpt{
    font-size:13.5px;line-height:1.55;color:var(--dim);
    margin:0 0 18px;flex:1;
  }
  .ow-blog__card-more{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.14em;text-transform:uppercase;
    color:#fff;font-weight:700;
    display:inline-flex;align-items:center;gap:8px;
    transition:gap .25s ease, color .25s ease;
  }
  .ow-blog__card:hover .ow-blog__card-more{gap:12px;color:var(--accent-glow);}

  /* Empty state */
  .ow-blog__empty{
    text-align:center;padding:80px 30px;
    border:1px dashed var(--line);
    border-radius:24px;color:var(--dim);
  }
  .ow-blog__empty h2{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:30px;color:#fff;margin:0 0 12px;
    font-weight:400;text-transform:uppercase;
  }

  /* Pagination */
  .ow-blog__pagination{margin-top:48px;text-align:center;}
  .ow-blog__pagination .nav-links{
    display:inline-flex;gap:8px;align-items:center;flex-wrap:wrap;justify-content:center;
  }
  .ow-blog__pagination .page-numbers{
    display:inline-flex;align-items:center;justify-content:center;
    min-width:42px;height:42px;padding:0 14px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.1em;
    color:#fff;text-decoration:none;
    background:rgba(255,255,255,.04);
    border:1px solid var(--line);
    transition:border-color .2s ease, background .2s ease;
  }
  .ow-blog__pagination .page-numbers:hover{border-color:var(--accent);}
  .ow-blog__pagination .page-numbers.current{
    background:var(--accent);color:#0a0a14;border-color:var(--accent);font-weight:700;
  }

  @media (max-width:1000px){
    .ow-blog__hero{padding:90px 28px 55px;}
    .ow-blog__main{padding:55px 28px 80px;}
    .ow-blog__grid{grid-template-columns:repeat(2,1fr);gap:18px;}
  }
  @media (max-width:640px){
    .ow-blog__hero{padding:70px 18px 45px;}
    .ow-blog__main{padding:45px 18px 70px;}
    .ow-blog__title{font-size:48px;}
    .ow-blog__grid{grid-template-columns:1fr;}
  }
</style>

<section class="ow-blog">

  <!-- ===== HERO ===== -->
  <div class="ow-blog__hero">
    <div class="ow-blog__hero-grid" aria-hidden="true"></div>
    <div class="ow-blog__hero-inner">
      <div class="ow-blog__eyebrow">Overworld · Blog</div>
      <h1 class="ow-blog__title">The Overworld Blog</h1>
      <p class="ow-blog__desc">News, updates and behind-the-scenes stories from Singapore's home of VR gaming and immersive physical play.</p>
    </div>
  </div>

  <!-- ===== POSTS ===== -->
  <div class="ow-blog__main">
    <div class="ow-blog__main-inner">

      <?php if ( have_posts() ) : ?>

        <div class="ow-blog__grid">
          <?php while ( have_posts() ) : the_post(); ?>
            <a class="ow-blog__card" href="<?php the_permalink(); ?>">
              <div class="ow-blog__card-img">
                <?php if ( has_post_thumbnail() ) : ?>
                  <?php the_post_thumbnail( 'large' ); ?>
                <?php else : ?>
                  <div class="ow-blog__card-img-empty">📰</div>
                <?php endif; ?>
              </div>
              <div class="ow-blog__card-body">
                <div class="ow-blog__card-date"><?php echo esc_html( get_the_date() ); ?></div>
                <h2 class="ow-blog__card-title"><?php the_title(); ?></h2>
                <p class="ow-blog__card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26 ) ); ?></p>
                <span class="ow-blog__card-more">Read Article →</span>
              </div>
            </a>
          <?php endwhile; ?>
        </div>

        <div class="ow-blog__pagination">
          <?php the_posts_pagination( array( 'mid_size' => 2, 'prev_text' => '←', 'next_text' => '→' ) ); ?>
        </div>

      <?php else : ?>

        <div class="ow-blog__empty">
          <h2>No posts yet</h2>
          <p>Check back soon — stories from the arcade floor are on the way.</p>
        </div>

      <?php endif; ?>

    </div>
  </div>

</section>

<?php get_footer(); ?>

<?php
/**
 * Single blog post template — standard WordPress posts (/[post-slug]/).
 *
 * INSTALL: Upload to your child theme:
 *   /wp-content/themes/hello-elementor-child/single.php
 *
 * Dark reading layout in the site's design language, plus the SEO/GEO layer
 * this site otherwise lacks (no SEO plugin installed):
 *   - meta description + Open Graph / Twitter tags from the post excerpt
 *   - schema.org Article + BreadcrumbList JSON-LD in <head>
 * Content is styled for long-form reading (headings, lists, blockquotes,
 * info boxes) so posts written in the block editor look on-brand.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ===== SEO / GEO head tags (runs before get_header) =====
add_action( 'wp_head', function () {
    if ( ! is_singular( 'post' ) ) return;
    $p = get_queried_object();
    if ( ! $p ) return;

    $desc  = $p->post_excerpt ? $p->post_excerpt : wp_trim_words( wp_strip_all_tags( $p->post_content ), 28, '…' );
    $desc  = esc_attr( wp_strip_all_tags( $desc ) );
    $url   = get_permalink( $p );
    $title = esc_attr( get_the_title( $p ) );
    $img   = get_the_post_thumbnail_url( $p->ID, 'full' );

    echo '<meta name="description" content="' . $desc . '" />' . "\n";
    echo '<meta property="og:type" content="article" />' . "\n";
    echo '<meta property="og:title" content="' . $title . '" />' . "\n";
    echo '<meta property="og:description" content="' . $desc . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
    echo '<meta property="og:site_name" content="Overworld" />' . "\n";
    if ( $img ) echo '<meta property="og:image" content="' . esc_url( $img ) . '" />' . "\n";
    echo '<meta name="twitter:card" content="' . ( $img ? 'summary_large_image' : 'summary' ) . '" />' . "\n";

    $schema = array(
        '@context' => 'https://schema.org',
        '@graph'   => array(
            array(
                '@type'            => 'Article',
                'headline'         => get_the_title( $p ),
                'description'      => wp_strip_all_tags( $desc ),
                'datePublished'    => get_the_date( 'c', $p ),
                'dateModified'     => get_the_modified_date( 'c', $p ),
                'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => $url ),
                'author'           => array(
                    '@type' => 'Organization',
                    'name'  => 'Overworld',
                    'url'   => home_url( '/' ),
                ),
                'publisher'        => array(
                    '@type' => 'Organization',
                    'name'  => 'Overworld',
                    'url'   => home_url( '/' ),
                ),
                'inLanguage'       => 'en-SG',
            ),
            array(
                '@type'           => 'BreadcrumbList',
                'itemListElement' => array(
                    array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url( '/' ) ),
                    array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => home_url( '/blog/' ) ),
                    array( '@type' => 'ListItem', 'position' => 3, 'name' => get_the_title( $p ), 'item' => $url ),
                ),
            ),
        ),
    );
    if ( $img ) $schema['@graph'][0]['image'] = $img;

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 5 );

get_header();

while ( have_posts() ) : the_post();
$word_count   = str_word_count( wp_strip_all_tags( get_the_content() ) );
$reading_mins = max( 1, (int) ceil( $word_count / 220 ) );
?>

<style>
  .ow-post{
    --accent:#ff5a1f;
    --accent-glow:#ff8a3d;
    --bg:#0a0a14;
    --bg-2:#13131f;
    --fg:#fff;
    --dim:rgba(220,225,240,.72);
    --line:rgba(255,255,255,.08);
    background:var(--bg);
    color:var(--fg);
    font-family:'Space Grotesk','Inter',system-ui,sans-serif;
  }
  .ow-post *{box-sizing:border-box;}

  /* ===== HERO ===== */
  .ow-post__hero{
    position:relative;
    background:radial-gradient(ellipse at 50% 110%,#1a0a05 0%,#0d0608 50%,#0a0606 100%);
    padding:110px 40px 60px;
    overflow:hidden;
  }
  .ow-post__hero::before{
    content:"";position:absolute;left:0;right:0;bottom:0;height:80%;
    background:radial-gradient(ellipse at center,rgba(255,90,31,.16) 0%,transparent 70%);
    filter:blur(60px);pointer-events:none;
  }
  .ow-post__hero-inner{
    max-width:860px;margin:0 auto;
    position:relative;z-index:2;text-align:center;
  }
  .ow-post__back{
    display:inline-flex;align-items:center;gap:8px;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.16em;text-transform:uppercase;
    color:var(--dim);text-decoration:none;
    margin-bottom:26px;transition:color .2s ease;
  }
  .ow-post__back:hover{color:var(--accent-glow);}
  .ow-post__title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(34px,4.6vw,64px);
    line-height:1.05;letter-spacing:-.015em;
    font-weight:400;text-transform:uppercase;
    margin:0 0 20px;
    background:linear-gradient(180deg,#fff 0%,#fff 45%,var(--accent-glow) 100%);
    -webkit-background-clip:text;background-clip:text;
    -webkit-text-fill-color:transparent;
  }
  .ow-post__meta{
    display:inline-flex;align-items:center;gap:18px;flex-wrap:wrap;justify-content:center;
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.16em;text-transform:uppercase;
    color:var(--dim);
  }
  .ow-post__meta span{color:var(--accent-glow);}

  /* ===== BODY ===== */
  .ow-post__main{
    padding:60px 40px 90px;
    background:var(--bg);
  }
  .ow-post__content{
    max-width:760px;margin:0 auto;
    font-size:16.5px;line-height:1.75;color:var(--dim);
  }
  .ow-post__content > *:first-child{margin-top:0;}
  .ow-post__content h2{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(24px,2.8vw,34px);line-height:1.1;
    font-weight:400;text-transform:uppercase;letter-spacing:-.01em;
    color:#fff;margin:44px 0 16px;
  }
  .ow-post__content h3{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(19px,2.2vw,24px);line-height:1.15;
    font-weight:400;text-transform:uppercase;
    color:#fff;margin:34px 0 12px;
  }
  .ow-post__content p{margin:0 0 18px;}
  .ow-post__content a{
    color:var(--accent-glow);text-decoration:none;
    border-bottom:1px solid rgba(255,90,31,.5);
    transition:color .2s ease, border-color .2s ease;
  }
  .ow-post__content a:hover{color:#fff;border-color:#fff;}
  .ow-post__content strong{color:#fff;font-weight:600;}
  .ow-post__content ul,.ow-post__content ol{
    margin:0 0 20px;padding-left:22px;
    display:flex;flex-direction:column;gap:8px;
  }
  .ow-post__content li::marker{color:var(--accent);}
  .ow-post__content blockquote{
    margin:28px 0;padding:22px 26px;
    background:var(--bg-2);
    border-left:3px solid var(--accent);
    border-radius:0 16px 16px 0;
    font-size:17px;color:#fff;font-style:italic;
  }
  .ow-post__content blockquote p:last-child{margin-bottom:0;}
  .ow-post__content img{
    max-width:100%;height:auto;border-radius:16px;
    border:1px solid var(--line);
  }
  .ow-post__content hr{
    border:0;border-top:1px solid var(--line);margin:36px 0;
  }
  /* Callout box: add class "ow-callout" to a group/paragraph block */
  .ow-post__content .ow-callout{
    background:radial-gradient(ellipse at 20% 0%,rgba(255,90,31,.14) 0%,var(--bg-2) 70%);
    border:1px solid rgba(255,90,31,.3);
    border-radius:16px;
    padding:22px 26px;margin:0 0 24px;
  }
  .ow-post__content .ow-callout p:last-child{margin-bottom:0;}

  /* ===== FOOT CTA ===== */
  .ow-post__cta{
    background:linear-gradient(180deg,var(--bg) 0%,var(--bg-2) 100%);
    border-top:1px solid var(--line);
    padding:60px 40px 90px;
  }
  .ow-post__cta-inner{
    max-width:760px;margin:0 auto;
    padding:40px 36px;
    background:radial-gradient(ellipse at center,rgba(255,90,31,.14) 0%,var(--bg-2) 70%);
    border:1px solid rgba(255,90,31,.3);
    border-radius:24px;
    text-align:center;
  }
  .ow-post__cta-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(24px,3vw,34px);line-height:1.05;
    text-transform:uppercase;font-weight:400;
    margin:0 0 12px;color:#fff;
  }
  .ow-post__cta-sub{
    font-size:14.5px;color:var(--dim);line-height:1.55;
    margin:0 auto 24px;max-width:480px;
  }
  .ow-post__cta-buttons{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
  .ow-post__cta-btn{
    display:inline-flex;align-items:center;gap:10px;
    padding:14px 24px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.14em;text-transform:uppercase;
    text-decoration:none;font-weight:700;
    transition:transform .25s ease, gap .25s ease;
  }
  .ow-post__cta-btn--primary{
    background:var(--accent);color:#0a0a14;
    box-shadow:0 12px 30px -10px var(--accent);
  }
  .ow-post__cta-btn--primary:hover{transform:translateY(-2px);gap:14px;}
  .ow-post__cta-btn--ghost{
    background:rgba(255,255,255,.04);color:#fff;
    border:1px solid var(--line);
  }
  .ow-post__cta-btn--ghost:hover{
    background:rgba(255,255,255,.08);
    border-color:var(--accent);
    transform:translateY(-2px);gap:14px;
  }

  @media (max-width:640px){
    .ow-post__hero{padding:80px 18px 45px;}
    .ow-post__main{padding:45px 18px 70px;}
    .ow-post__cta{padding:45px 18px 70px;}
    .ow-post__cta-inner{padding:30px 22px;}
    .ow-post__cta-buttons{flex-direction:column;}
    .ow-post__cta-btn{justify-content:center;}
  }
</style>

<article class="ow-post">

  <!-- ===== HERO ===== -->
  <div class="ow-post__hero">
    <div class="ow-post__hero-inner">
      <a class="ow-post__back" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">← Back to Blog</a>
      <h1 class="ow-post__title"><?php the_title(); ?></h1>
      <div class="ow-post__meta">
        <div><span>📅</span> <?php echo esc_html( get_the_date() ); ?></div>
        <div><span>⏱</span> <?php echo esc_html( $reading_mins ); ?> min read</div>
      </div>
    </div>
  </div>

  <!-- ===== CONTENT ===== -->
  <div class="ow-post__main">
    <div class="ow-post__content">
      <?php the_content(); ?>
    </div>
  </div>

  <!-- ===== CTA ===== -->
  <div class="ow-post__cta">
    <div class="ow-post__cta-inner">
      <h2 class="ow-post__cta-title">Ready to Play?</h2>
      <p class="ow-post__cta-sub">Three outlets across Singapore — VR arcades, escape rooms, lava floors and more. Book a session or plan something bigger.</p>
      <div class="ow-post__cta-buttons">
        <a class="ow-post__cta-btn ow-post__cta-btn--primary" href="<?php echo esc_url( home_url( '/booking/' ) ); ?>">Book Your Session →</a>
        <a class="ow-post__cta-btn ow-post__cta-btn--ghost" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">More Articles</a>
      </div>
    </div>
  </div>

</article>

<?php
endwhile;
get_footer();
?>

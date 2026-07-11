<?php
/**
 * Template Name: FAQ Page (v3 - Outlet Filtered)
 *
 * REPLACES: page-faq.php in your child theme
 *
 * INSTALL: Upload to your child theme at:
 *   /wp-content/themes/hello-elementor-child/page-faq.php
 *   (overwrites existing file)
 *
 * v3 CHANGES:
 *   - OUTLET FILTER TABS on top (Kallang / Orchard / Funan)
 *   - Within each outlet, FAQs still grouped by category
 *   - Each outlet uses its own accent color (Kallang=blue, Orchard=orange, Funan=purple)
 *   - Outlet switching is instant (no page reload) via JS show/hide
 *   - Contact card updates to match the selected outlet
 *
 * REQUIRES NEW ACF FIELD:
 *   faq_outlet (Select) — values: kallang-wave-mall / orchard-central / funan
 *   Add this in ACF → Field Groups → FAQ Details → + Add Field
 *
 * EXISTING ACF FIELDS (unchanged):
 *   faq_question, faq_answer, faq_category, faq_display_order
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

// ===== Outlet config =====
$outlets = array(
    'kallang-wave-mall' => array(
        'slug'       => 'kallang-wave-mall',
        'brand'      => 'Overworld VR',
        'name'       => 'Kallang Wave Mall',
        'short_name' => 'Kallang',
        'phone'      => '+65 6513 0561',
        'phone_raw'  => '+6565130561',
        'whatsapp'   => 'https://wa.me/+6596101682',
        'wa_label'   => '+65 9610 1682',
        'email'      => 'support@overworldvr.com',
        'accent'     => '#2f6bff',
        'accent_glow'=> '#6f9bff',
    ),
    'orchard-central' => array(
        'slug'       => 'orchard-central',
        'brand'      => 'Overworld Lava',
        'name'       => 'Orchard Central',
        'short_name' => 'Orchard',
        'phone'      => '+65 8801 4303',
        'phone_raw'  => '+6588014303',
        'whatsapp'   => 'https://wa.me/message/WJ7MGRFFVGHAF1',
        'wa_label'   => '+65 8801 4303',
        'email'      => 'ocsupport@overworld.com.sg',
        'accent'     => '#ff5722',
        'accent_glow'=> '#ff8a3d',
    ),
    'funan' => array(
        'slug'       => 'funan',
        'brand'      => 'Overworld Funan',
        'name'       => 'Funan',
        'short_name' => 'Funan',
        'phone'      => '+65 8915 0061',
        'phone_raw'  => '+6589150061',
        'whatsapp'   => 'https://wa.me/6589140061',
        'wa_label'   => '+65 8914 0061',
        'email'      => 'funansupport@overworld.com.sg',
        'accent'     => '#a855f7',
        'accent_glow'=> '#c89aff',
    ),
);

// ===== Fetch all FAQs =====
$faqs = get_posts( array(
    'post_type'      => 'faq',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'meta_key'       => 'faq_display_order',
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
) );

// ===== Group by outlet → category =====
$by_outlet = array();
foreach ( $outlets as $oslug => $oconf ) {
    $by_outlet[ $oslug ] = array();
}

foreach ( $faqs as $faq ) {
    $outlet_slug = get_post_meta( $faq->ID, 'faq_outlet', true );
    $cat = get_post_meta( $faq->ID, 'faq_category', true );
    if ( ! $cat ) $cat = 'General';

    // No outlet set (or unknown value) -> the FAQ applies to EVERY outlet.
    $targets = ( $outlet_slug && isset( $outlets[ $outlet_slug ] ) )
        ? array( $outlet_slug )
        : array_keys( $outlets );

    foreach ( $targets as $target_slug ) {
        if ( ! isset( $by_outlet[ $target_slug ][ $cat ] ) ) {
            $by_outlet[ $target_slug ][ $cat ] = array();
        }
        $by_outlet[ $target_slug ][ $cat ][] = $faq;
    }
}

// Category display order
$category_order = array( 'General', 'Booking', 'VR Arcade', 'VR Free Roam', 'XR Party Game', 'Floor Is Lava', 'Laser Maze', 'Tap Tap' );

function ow_faq_order_categories( $cats, $category_order ) {
    $ordered = array();
    foreach ( $category_order as $cat_name ) {
        if ( isset( $cats[ $cat_name ] ) ) {
            $ordered[ $cat_name ] = $cats[ $cat_name ];
        }
    }
    foreach ( $cats as $cat_name => $items ) {
        if ( ! isset( $ordered[ $cat_name ] ) ) {
            $ordered[ $cat_name ] = $items;
        }
    }
    return $ordered;
}

// Determine default active outlet (first one that has FAQs, else kallang)
$default_outlet = 'kallang-wave-mall';
foreach ( $outlets as $oslug => $oconf ) {
    if ( ! empty( $by_outlet[ $oslug ] ) ) {
        $default_outlet = $oslug;
        break;
    }
}
?>

<style>
  .ow-faq{
    --ow-bg:#000;
    --ow-fg:#fff;
    --ow-dim:rgba(255,255,255,.55);
    --ow-line:rgba(255,255,255,.12);
    --ow-line-soft:rgba(255,255,255,.06);
    /* default accent — overridden per active outlet via JS data attr */
    --accent:#2f6bff;
    --accent-glow:#6f9bff;
    background:var(--ow-bg);
    color:var(--ow-fg);
    font-family:'Space Grotesk','Inter',system-ui,sans-serif;
  }
  .ow-faq *{box-sizing:border-box;}

  /* HERO */
  .ow-faq__hero{
    padding:120px 40px 50px;
    text-align:center;
  }
  .ow-faq__hero-inner{max-width:900px;margin:0 auto;}
  .ow-faq__eyebrow{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.28em;text-transform:uppercase;
    color:var(--ow-dim);margin-bottom:24px;
  }
  .ow-faq__title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(36px,5.5vw,68px);
    line-height:1;letter-spacing:.005em;
    text-transform:uppercase;
    margin:0 0 28px;font-weight:400;color:var(--ow-fg);
  }
  .ow-faq__sub{
    font-size:16px;color:var(--ow-dim);line-height:1.6;
    margin:0 auto;max-width:540px;
  }

  /* OUTLET TABS */
  .ow-faq__tabs{
    display:flex;justify-content:center;gap:0;flex-wrap:wrap;
    max-width:max-content;margin:48px auto 0;
    border:1px solid var(--ow-line);
    border-radius:999px;overflow:hidden;
  }
  .ow-faq__tab{
    display:inline-flex;align-items:center;gap:9px;
    padding:14px 30px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.16em;text-transform:uppercase;
    color:var(--ow-dim);font-weight:600;
    background:transparent;border:none;cursor:pointer;
    transition:background .25s ease, color .25s ease;
    -webkit-appearance:none;appearance:none;
    font-family:'JetBrains Mono',monospace;
  }
  .ow-faq__tab + .ow-faq__tab{
    border-left:1px solid var(--ow-line);
  }
  .ow-faq__tab::before{
    content:"";width:8px;height:8px;border-radius:50%;
    background:var(--tab-color);
    box-shadow:0 0 10px var(--tab-color);
    opacity:.5;transition:opacity .25s ease, transform .25s ease;
  }
  .ow-faq__tab:hover{color:var(--ow-fg);}
  .ow-faq__tab:hover::before{opacity:1;}
  .ow-faq__tab.is-active{
    background:var(--tab-color);
    color:#000;
  }
  .ow-faq__tab.is-active::before{
    opacity:1;background:#000;box-shadow:none;transform:scale(1.1);
  }
  .ow-faq__tab--kallang{--tab-color:#2f6bff;}
  .ow-faq__tab--orchard{--tab-color:#ff5722;}
  .ow-faq__tab--funan{--tab-color:#a855f7;}

  /* CONTACT card for active outlet */
  .ow-faq__contact-wrap{
    max-width:1000px;margin:40px auto 0;
    padding:0 40px;
  }
  .ow-faq__contact{
    display:none;
    padding:24px 28px;
    border:1px solid var(--ow-line);
    border-radius:16px;
    align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;
    background:rgba(255,255,255,.02);
  }
  .ow-faq__contact.is-active{display:flex;}
  .ow-faq__contact-left{}
  .ow-faq__contact-tag{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.22em;text-transform:uppercase;
    color:var(--c-accent);margin-bottom:8px;
    display:flex;align-items:center;gap:8px;
  }
  .ow-faq__contact-tag::before{
    content:"";width:6px;height:6px;border-radius:50%;
    background:var(--c-accent);box-shadow:0 0 10px var(--c-accent);
  }
  .ow-faq__contact-name{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:26px;line-height:1;font-weight:400;
    text-transform:uppercase;color:var(--ow-fg);margin:0;
    letter-spacing:.005em;
  }
  .ow-faq__contact-rows{
    display:flex;gap:22px;flex-wrap:wrap;
  }
  .ow-faq__contact-row{
    font-size:13px;color:var(--ow-dim);line-height:1.4;
    display:flex;align-items:center;gap:7px;
  }
  .ow-faq__contact-row span{color:var(--c-accent);}
  .ow-faq__contact-row a{
    color:var(--ow-fg);text-decoration:none;transition:color .2s ease;
  }
  .ow-faq__contact-row a:hover{color:var(--c-accent);}
  .ow-faq__contact--kallang{--c-accent:#6f9bff;}
  .ow-faq__contact--orchard{--c-accent:#ff8a3d;}
  .ow-faq__contact--funan{--c-accent:#c89aff;}

  /* ACCORDION AREA */
  .ow-faq__acc{
    padding:60px 40px 120px;
  }
  .ow-faq__acc-inner{max-width:1000px;margin:0 auto;}

  /* Outlet panel — only active one shows */
  .ow-faq__panel{display:none;}
  .ow-faq__panel.is-active{display:block;}

  /* Category section */
  .ow-faq__cat{margin-bottom:72px;scroll-margin-top:60px;}
  .ow-faq__cat:last-of-type{margin-bottom:0;}
  .ow-faq__cat-head{padding-bottom:20px;}
  .ow-faq__cat-num{
    font-family:'JetBrains Mono',monospace;
    font-size:10.5px;letter-spacing:.24em;text-transform:uppercase;
    color:var(--accent-glow);margin-bottom:10px;display:block;
  }
  .ow-faq__cat-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:34px;line-height:1;letter-spacing:.005em;
    text-transform:uppercase;margin:0;font-weight:400;color:var(--ow-fg);
  }

  /* FAQ items */
  .ow-faq__list{border-top:1px solid var(--ow-line-soft);}
  .ow-faq__item{border-bottom:1px solid var(--ow-line-soft);}
  .ow-faq__q{
    list-style:none;cursor:pointer;
    padding:24px 4px;
    display:flex;align-items:center;justify-content:space-between;gap:24px;
    font-size:15.5px;font-weight:400;color:var(--ow-fg);
    line-height:1.45;
    transition:color .2s ease, padding-left .25s ease;
    user-select:none;
  }
  .ow-faq__q::-webkit-details-marker{display:none;}
  .ow-faq__q > span:first-child{
    flex:1;min-width:0;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  }
  .ow-faq__q:hover{padding-left:8px;}
  .ow-faq__item[open] .ow-faq__q > span:first-child{
    white-space:normal;overflow:visible;text-overflow:clip;
  }

  /* Chevron */
  .ow-faq__chev{
    flex-shrink:0;width:14px;height:14px;position:relative;
    transition:transform .3s ease;color:var(--ow-dim);
  }
  .ow-faq__chev::before{
    content:"";position:absolute;left:50%;top:35%;
    width:8px;height:8px;
    border-right:1.5px solid currentColor;
    border-bottom:1.5px solid currentColor;
    transform:translate(-50%,-50%) rotate(45deg);
    transition:transform .3s ease;
  }
  .ow-faq__item[open] .ow-faq__chev{color:var(--accent-glow);}
  .ow-faq__item[open] .ow-faq__chev::before{
    transform:translate(-50%,-50%) rotate(-135deg);top:55%;
  }
  .ow-faq__q:hover .ow-faq__chev{color:var(--ow-fg);}

  /* Answer */
  .ow-faq__a{
    padding:0 4px 28px;max-width:780px;
    font-size:14.5px;line-height:1.7;color:var(--ow-dim);
  }
  .ow-faq__a p{margin:0 0 12px;}
  .ow-faq__a p:last-child{margin-bottom:0;}
  .ow-faq__a strong{color:var(--ow-fg);font-weight:600;}
  .ow-faq__a a{
    color:var(--ow-fg);text-decoration:none;
    border-bottom:1px solid var(--ow-line);transition:border-color .2s ease;
  }
  .ow-faq__a a:hover{border-bottom-color:var(--ow-fg);}

  /* Empty state per outlet */
  .ow-faq__empty{
    text-align:center;padding:80px 20px;
    color:var(--ow-dim);font-size:15px;
    border:1px dashed var(--ow-line);border-radius:16px;
  }
  .ow-faq__empty strong{color:var(--ow-fg);}

  /* Bottom CTA */
  .ow-faq__cta{
    margin-top:90px;padding-top:60px;
    border-top:1px solid var(--ow-line);text-align:center;
  }
  .ow-faq__cta-eyebrow{
    font-family:'JetBrains Mono',monospace;
    font-size:11px;letter-spacing:.24em;text-transform:uppercase;
    color:var(--ow-dim);margin-bottom:18px;
  }
  .ow-faq__cta-title{
    font-family:'Anton','Bebas Neue',sans-serif;
    font-size:clamp(28px,3.5vw,42px);
    line-height:1;letter-spacing:.005em;text-transform:uppercase;
    margin:0 0 14px;font-weight:400;color:var(--ow-fg);
  }
  .ow-faq__cta-sub{
    font-size:15px;color:var(--ow-dim);line-height:1.6;
    margin:0 auto 32px;max-width:460px;
  }
  .ow-faq__cta-btn{
    display:inline-flex;align-items:center;gap:8px;
    padding:15px 32px;border-radius:999px;
    font-family:'JetBrains Mono',monospace;
    font-size:12px;letter-spacing:.16em;text-transform:uppercase;
    text-decoration:none;font-weight:700;
    background:var(--accent);color:#000;
    transition:transform .25s ease, gap .25s ease;
  }
  .ow-faq__cta-btn:hover{transform:translateY(-2px);gap:12px;}

  /* Responsive */
  @media (max-width:760px){
    .ow-faq__hero{padding:90px 24px 40px;}
    .ow-faq__acc{padding:50px 24px 90px;}
    .ow-faq__contact-wrap{padding:0 24px;}
    /* keep the outlet filter in ONE straight row on mobile */
    .ow-faq__tabs{width:100%;max-width:100%;flex-direction:row;flex-wrap:nowrap;border-radius:999px;}
    .ow-faq__tab{flex:1;justify-content:center;padding:12px 6px;font-size:11px;letter-spacing:.1em;gap:6px;}
    .ow-faq__tab::before{width:6px;height:6px;}
    .ow-faq__contact{flex-direction:column;align-items:flex-start;}
    .ow-faq__cat-title{font-size:28px;}
    .ow-faq__q{font-size:14.5px;padding:20px 4px;gap:16px;}
  }
  @media (max-width:480px){
    .ow-faq__hero{padding:70px 18px 32px;}
    .ow-faq__acc{padding:40px 18px 70px;}
    .ow-faq__title{font-size:34px;}
  }
</style>

<section class="ow-faq" id="ow-faq-root">

  <!-- HERO -->
  <div class="ow-faq__hero">
    <div class="ow-faq__hero-inner">
      <div class="ow-faq__eyebrow">Need Help?</div>
      <h1 class="ow-faq__title">Frequently Asked Questions</h1>
      <p class="ow-faq__sub">Pick your outlet below — bookings, ages, group sizes, what to wear, and how it all works.</p>

      <!-- OUTLET TABS -->
      <div class="ow-faq__tabs" role="tablist">
        <?php
        $tab_class_map = array(
            'kallang-wave-mall' => 'kallang',
            'orchard-central'   => 'orchard',
            'funan'             => 'funan',
        );
        foreach ( $outlets as $oslug => $oconf ) :
            $tc = $tab_class_map[ $oslug ];
            $is_active = ( $oslug === $default_outlet );
        ?>
          <button
            type="button"
            class="ow-faq__tab ow-faq__tab--<?php echo esc_attr( $tc ); ?> <?php echo $is_active ? 'is-active' : ''; ?>"
            data-outlet="<?php echo esc_attr( $oslug ); ?>"
            data-accent="<?php echo esc_attr( $oconf['accent'] ); ?>"
            data-accent-glow="<?php echo esc_attr( $oconf['accent_glow'] ); ?>"
          >
            <?php echo esc_html( $oconf['short_name'] ); ?>
          </button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- CONTACT CARDS (one per outlet, only active shows) -->
  <div class="ow-faq__contact-wrap">
    <?php foreach ( $outlets as $oslug => $oconf ) :
      $tc = $tab_class_map[ $oslug ];
      $is_active = ( $oslug === $default_outlet );
    ?>
      <div class="ow-faq__contact ow-faq__contact--<?php echo esc_attr( $tc ); ?> <?php echo $is_active ? 'is-active' : ''; ?>" data-outlet-contact="<?php echo esc_attr( $oslug ); ?>">
        <div class="ow-faq__contact-left">
          <div class="ow-faq__contact-tag"><?php echo esc_html( $oconf['brand'] ); ?></div>
          <h3 class="ow-faq__contact-name"><?php echo esc_html( $oconf['name'] ); ?></h3>
        </div>
        <div class="ow-faq__contact-rows">
          <div class="ow-faq__contact-row"><span>📞</span><a href="tel:<?php echo esc_attr( $oconf['phone_raw'] ); ?>"><?php echo esc_html( $oconf['phone'] ); ?></a></div>
          <div class="ow-faq__contact-row"><span>💬</span><a href="<?php echo esc_url( $oconf['whatsapp'] ); ?>" target="_blank" rel="noopener">WhatsApp</a></div>
          <div class="ow-faq__contact-row"><span>✉</span><a href="mailto:<?php echo esc_attr( $oconf['email'] ); ?>"><?php echo esc_html( $oconf['email'] ); ?></a></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- ACCORDION PANELS -->
  <div class="ow-faq__acc">
    <div class="ow-faq__acc-inner">

      <?php foreach ( $outlets as $oslug => $oconf ) :
        $is_active = ( $oslug === $default_outlet );
        $cats = ow_faq_order_categories( $by_outlet[ $oslug ], $category_order );
      ?>
        <div
          class="ow-faq__panel <?php echo $is_active ? 'is-active' : ''; ?>"
          data-outlet-panel="<?php echo esc_attr( $oslug ); ?>"
          data-accent="<?php echo esc_attr( $oconf['accent'] ); ?>"
          data-accent-glow="<?php echo esc_attr( $oconf['accent_glow'] ); ?>"
        >
          <?php if ( empty( $cats ) ) : ?>
            <div class="ow-faq__empty">
              <p>No FAQs published for <strong><?php echo esc_html( $oconf['short_name'] ); ?></strong> yet.<br>
              Add some via <strong>WP Admin → FAQs → Add New</strong> and set Outlet to <strong><?php echo esc_html( $oconf['name'] ); ?></strong>.</p>
            </div>
          <?php else : ?>
            <?php $idx = 1; foreach ( $cats as $cat_name => $items ) : ?>
              <div class="ow-faq__cat">
                <div class="ow-faq__cat-head">
                  <span class="ow-faq__cat-num"><?php echo str_pad( $idx, 2, '0', STR_PAD_LEFT ); ?> · Category</span>
                  <h2 class="ow-faq__cat-title"><?php echo esc_html( $cat_name ); ?></h2>
                </div>
                <div class="ow-faq__list">
                  <?php foreach ( $items as $faq ) :
                    $question = get_post_meta( $faq->ID, 'faq_question', true );
                    if ( ! $question ) $question = $faq->post_title;
                    $answer = get_post_meta( $faq->ID, 'faq_answer', true );
                  ?>
                    <details class="ow-faq__item">
                      <summary class="ow-faq__q">
                        <span><?php echo esc_html( $question ); ?></span>
                        <span class="ow-faq__chev" aria-hidden="true"></span>
                      </summary>
                      <div class="ow-faq__a">
                        <?php echo wp_kses_post( wpautop( $answer ) ); ?>
                      </div>
                    </details>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php $idx++; endforeach; ?>
          <?php endif; ?>

          <!-- per-outlet CTA -->
          <div class="ow-faq__cta">
            <div class="ow-faq__cta-eyebrow">Still got questions?</div>
            <h3 class="ow-faq__cta-title">We're Here to Help</h3>
            <p class="ow-faq__cta-sub">Can't find what you're looking for? Message our <?php echo esc_html( $oconf['short_name'] ); ?> team directly.</p>
            <a class="ow-faq__cta-btn" href="<?php echo esc_url( $oconf['whatsapp'] ); ?>" target="_blank" rel="noopener">
              WhatsApp <?php echo esc_html( $oconf['short_name'] ); ?> →
            </a>
          </div>
        </div>
      <?php endforeach; ?>

    </div>
  </div>

</section>

<script>
(function(){
  var root = document.getElementById('ow-faq-root');
  if (!root) return;

  var tabs     = root.querySelectorAll('.ow-faq__tab');
  var panels   = root.querySelectorAll('.ow-faq__panel');
  var contacts = root.querySelectorAll('.ow-faq__contact');

  function setAccent(accent, glow){
    root.style.setProperty('--accent', accent);
    root.style.setProperty('--accent-glow', glow);
  }

  // Initialise accent from default active tab
  var activeTab = root.querySelector('.ow-faq__tab.is-active');
  if (activeTab) {
    setAccent(activeTab.dataset.accent, activeTab.dataset.accentGlow);
  }

  tabs.forEach(function(tab){
    tab.addEventListener('click', function(){
      var outlet = tab.dataset.outlet;

      // toggle tab active
      tabs.forEach(function(t){ t.classList.remove('is-active'); });
      tab.classList.add('is-active');

      // update root accent
      setAccent(tab.dataset.accent, tab.dataset.accentGlow);

      // toggle panels
      panels.forEach(function(p){
        p.classList.toggle('is-active', p.dataset.outletPanel === outlet);
      });

      // toggle contact cards
      contacts.forEach(function(c){
        c.classList.toggle('is-active', c.dataset.outletContact === outlet);
      });

      // close any open FAQ items in newly shown panel for a clean slate
      var activePanel = root.querySelector('.ow-faq__panel.is-active');
      if (activePanel) {
        activePanel.querySelectorAll('.ow-faq__item[open]').forEach(function(d){
          d.removeAttribute('open');
        });
      }
    });
  });
})();
</script>

<?php get_footer(); ?>
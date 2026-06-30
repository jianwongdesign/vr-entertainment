#!/usr/bin/env node

import { spawnSync } from 'node:child_process';
import { Buffer } from 'node:buffer';

const wp = (args, options = {}) => {
  const result = spawnSync('./scripts/remote-wp-cli.sh', args, {
    cwd: process.cwd(),
    encoding: 'utf8',
    maxBuffer: 1024 * 1024 * 20,
    ...options,
  });

  if (result.stdout) process.stdout.write(result.stdout);
  if (result.stderr) process.stderr.write(result.stderr);
  if (result.status !== 0) {
    process.exit(result.status ?? 1);
  }
};

const updatedDate = '30 June 2026';

const page = ({ title, slug, eyebrow, lede, pills = [], sections, extra = '' }) => {
  const pillHtml = pills.map((pill) => `<span class="ow-info__pill">${pill}</span>`).join('\n        ');
  const sectionsHtml = sections.map((section) => {
    if (section.type === 'tiles') {
      const tiles = section.tiles.map((tile) => `
          <div class="ow-info__tile">
            <h3>${tile.title}</h3>
            ${tile.body}
          </div>`).join('');

      return `
    <section class="ow-info__section">
      <div class="ow-info__inner">
        <h2 class="ow-info__section-title">${section.title}</h2>
        <div class="ow-info__body">${section.body || ''}</div>
        <div class="ow-info__grid">${tiles}
        </div>
      </div>
    </section>`;
    }

    if (section.type === 'contact') {
      const cards = section.cards.map((card) => `
          <article class="ow-info__contact-card">
            <h2>${card.title}</h2>
            ${card.body}
          </article>`).join('');

      return `
    <section class="ow-info__section">
      <div class="ow-info__inner">
        <h2 class="ow-info__section-title">${section.title}</h2>
        <div class="ow-info__body">${section.body || ''}</div>
        <div class="ow-info__contact-grid">${cards}
        </div>
      </div>
    </section>`;
    }

    const blocks = section.blocks.map((block) => `
        <div class="ow-info__policy">
          <h2>${block.title}</h2>
          ${block.body}
        </div>`).join('');

    return `
    <section class="ow-info__section">
      <div class="ow-info__inner">
        <h2 class="ow-info__section-title">${section.title}</h2>
        <div class="ow-info__body">
          ${blocks}
        </div>
      </div>
    </section>`;
  }).join('\n');

  return `<!-- wp:html -->
<main class="ow-info ow-info--${slug}">
  <section class="ow-info__hero">
    <div class="ow-info__inner">
      <p class="ow-info__eyebrow">${eyebrow}</p>
      <h1 class="ow-info__title">${title}</h1>
      <p class="ow-info__lede">${lede}</p>
      ${pillHtml ? `<div class="ow-info__meta">${pillHtml}</div>` : ''}
    </div>
  </section>
  ${sectionsHtml}
  ${extra}
</main>
<!-- /wp:html -->`;
};

const pages = [
  {
    title: 'About Overworld',
    slug: 'about',
    content: page({
      title: 'About Overworld',
      slug: 'about',
      eyebrow: 'Our Story',
      lede: 'Overworld brings VR, physical challenge rooms, and high-energy group play together across Singapore. The experience is built for friends, families, schools, teams, and anyone who wants something more active than a normal day out.',
      pills: ['Kallang Wave Mall', 'Orchard Central', 'Funan'],
      sections: [
        {
          type: 'tiles',
          title: 'What We Build',
          body: '<p>Each outlet has its own mix of experiences, but the goal is the same: easy-to-book games that feel exciting, social, and memorable.</p>',
          tiles: [
            {
              title: 'Virtual Reality',
              body: '<p>VR Arcade, VR Escape, machine rides, and free-roam adventures for players who want to step into another world.</p>',
            },
            {
              title: 'Physical Games',
              body: '<p>Floor Is Lava, Laser Maze, Tap Tap, and other real-world challenges that reward speed, teamwork, and reflexes.</p>',
            },
            {
              title: 'Group Events',
              body: '<p>Birthday parties, school outings, team building, and private group sessions planned around the right outlet and activity mix.</p>',
            },
          ],
        },
        {
          type: 'tiles',
          title: 'Our Outlets',
          body: '',
          tiles: [
            {
              title: 'Kallang Wave Mall',
              body: '<p>Overworld VR at Kallang is the flagship VR-heavy outlet with VR Arcade, VR Escape, VR Machine Ride, and Floor Is Lava.</p><p><a href="/pricing/kallang-wave-mall">View Kallang pricing</a></p>',
            },
            {
              title: 'Orchard Central',
              body: '<p>Overworld Lava at Orchard focuses on physical play with Floor Is Lava, Laser Maze, Tap Tap, parties, and team challenges.</p><p><a href="/pricing/orchard-central">View Orchard pricing</a></p>',
            },
            {
              title: 'Funan',
              body: '<p>Overworld Funan brings Floor Is Lava, VR Free Roam, and XR Party Game into a central mall location for groups and casual players.</p><p><a href="/pricing/funan">View Funan pricing</a></p>',
            },
          ],
        },
      ],
      extra: `
  <section class="ow-info__section">
    <div class="ow-info__inner">
      <h2 class="ow-info__section-title">Plan Your Visit</h2>
      <div class="ow-info__body">
        <p>Choose an outlet, compare the activities, and book the session that fits your group size and occasion.</p>
      </div>
      <div class="ow-info__cta-row">
        <a class="ow-info__button" href="/pricing">View Pricing</a>
        <a class="ow-info__button ow-info__button--ghost" href="/contact">Contact Us</a>
      </div>
    </div>
  </section>`,
    }),
  },
  {
    title: 'Contact Us',
    slug: 'contact',
    content: page({
      title: 'Contact Us',
      slug: 'contact',
      eyebrow: 'Support & Enquiries',
      lede: 'Reach the outlet directly for bookings, group events, changes to a reservation, or general questions before your visit.',
      pills: ['Call', 'WhatsApp', 'Email'],
      sections: [
        {
          type: 'contact',
          title: 'Outlet Contacts',
          body: '<p>For the fastest response, contact the outlet you plan to visit.</p>',
          cards: [
            {
              title: 'Kallang Wave Mall',
              body: '<p>1 Stadium Place, #01-63/64<br>Kallang Wave Mall, Singapore 397628</p><p><a href="tel:+6565130561">+65 6513 0561</a><br><a href="https://wa.me/6596101682" target="_blank" rel="noopener">WhatsApp +65 9610 1682</a><br><a href="mailto:support@overworldvr.com">support@overworldvr.com</a></p><p><a href="https://bookeo.com/overworldkallangwavemall" target="_blank" rel="noopener">Book Kallang</a></p>',
            },
            {
              title: 'Orchard Central',
              body: '<p>181 Orchard Road, #05-30/K1/K3<br>Orchard Central, Singapore 238896</p><p><a href="tel:+6588014303">+65 8801 4303</a><br><a href="https://wa.me/6588014303" target="_blank" rel="noopener">WhatsApp +65 8801 4303</a><br><a href="mailto:ocsupport@overworld.com.sg">ocsupport@overworld.com.sg</a></p><p><a href="https://bookeo.com/overworldorchardcentral" target="_blank" rel="noopener">Book Orchard</a></p>',
            },
            {
              title: 'Funan',
              body: '<p>107 North Bridge Road, #04-14 & K1<br>Funan, Singapore 179105</p><p><a href="tel:+6589150061">+65 8915 0061</a><br><a href="https://wa.me/6589140061" target="_blank" rel="noopener">WhatsApp +65 8914 0061</a><br><a href="mailto:funansupport@overworld.com.sg">funansupport@overworld.com.sg</a></p><p><a href="https://bookeo.com/overworldfunan" target="_blank" rel="noopener">Book Funan</a></p>',
            },
          ],
        },
      ],
      extra: `
  <section class="ow-info__section">
    <div class="ow-info__inner">
      <h2 class="ow-info__section-title">Event Enquiries</h2>
      <div class="ow-info__body">
        <p>For birthday parties, team building, school visits, or private events, share your preferred date, outlet, group size, and activity interest so the team can recommend the right format.</p>
      </div>
      <div class="ow-info__cta-row">
        <a class="ow-info__button" href="/team-building">Team Building</a>
        <a class="ow-info__button ow-info__button--ghost" href="/birthday-party">Birthday Party</a>
      </div>
    </div>
  </section>`,
    }),
  },
  {
    title: 'Privacy Policy',
    slug: 'privacy-policy',
    content: page({
      title: 'Privacy Policy',
      slug: 'privacy-policy',
      eyebrow: 'Customer Data',
      lede: 'This policy explains how Overworld may collect, use, and protect personal data when customers browse the website, contact an outlet, buy vouchers, or make bookings.',
      pills: [`Last updated ${updatedDate}`, 'Editable page'],
      sections: [
        {
          type: 'policy',
          title: 'Privacy Details',
          blocks: [
            {
              title: 'Personal Data We May Collect',
              body: '<p>We may collect details such as your name, phone number, email address, booking details, event requirements, payment or voucher references, and messages sent to our team.</p>',
            },
            {
              title: 'How We Use Personal Data',
              body: '<p>We use personal data to handle enquiries, manage bookings, arrange events, provide customer support, improve the website, send service updates, and meet operational or legal requirements.</p>',
            },
            {
              title: 'Bookings, Vouchers, And Third-Party Platforms',
              body: '<p>Some bookings or voucher purchases may be handled through third-party platforms such as Bookeo or voucher providers. Those platforms may collect and process information under their own policies.</p>',
            },
            {
              title: 'Sharing And Protection',
              body: '<p>We do not sell customer personal data. We may share data with service providers where needed to operate bookings, payments, communications, analytics, website hosting, or customer support. We take reasonable steps to protect personal data from unauthorised access or misuse.</p>',
            },
            {
              title: 'Access, Correction, And Contact',
              body: '<p>If you wish to access, correct, or ask about personal data held by Overworld, contact the relevant outlet or email <a href="mailto:support@overworldvr.com">support@overworldvr.com</a>.</p>',
            },
          ],
        },
      ],
    }),
  },
  {
    title: 'Terms of Service',
    slug: 'terms-of-service',
    content: page({
      title: 'Terms of Service',
      slug: 'terms-of-service',
      eyebrow: 'Website & Visit Terms',
      lede: 'These terms set out the general conditions for using the Overworld website, making bookings, and visiting our outlets.',
      pills: [`Last updated ${updatedDate}`, 'Editable page'],
      sections: [
        {
          type: 'policy',
          title: 'Terms',
          blocks: [
            {
              title: 'Website Use',
              body: '<p>Information on this website is provided for general customer reference. We try to keep details accurate, but activities, prices, packages, opening hours, and availability may change without prior notice.</p>',
            },
            {
              title: 'Bookings And Attendance',
              body: '<p>Customers are responsible for selecting the correct outlet, activity, date, and time when making a booking. Please arrive on time and follow outlet instructions so your session can run smoothly.</p>',
            },
            {
              title: 'Safety And Conduct',
              body: '<p>Customers must follow staff guidance, safety rules, age or height restrictions, and activity instructions. Overworld may refuse or stop participation where behaviour is unsafe, disruptive, or unsuitable for the activity.</p>',
            },
            {
              title: 'Payments And Promotions',
              body: '<p>Prices, promotions, package terms, and voucher conditions may differ by outlet and campaign. Promotional offers cannot always be combined unless stated by Overworld.</p>',
            },
            {
              title: 'Website Links',
              body: '<p>The website may link to third-party services for booking, vouchers, maps, payment, or social media. Overworld is not responsible for the content or policies of third-party websites.</p>',
            },
          ],
        },
      ],
    }),
  },
  {
    title: 'Refund Policy',
    slug: 'refund-policy',
    content: page({
      title: 'Refund Policy',
      slug: 'refund-policy',
      eyebrow: 'Booking Changes',
      lede: 'This page explains the general approach for refunds, rescheduling, vouchers, and missed bookings. Customers should contact the outlet as early as possible for help.',
      pills: [`Last updated ${updatedDate}`, 'Editable page'],
      sections: [
        {
          type: 'policy',
          title: 'Refunds And Changes',
          blocks: [
            {
              title: 'Booking Changes',
              body: '<p>If you need to change a booking, contact the outlet directly with your booking reference, name, phone number, preferred new date, and preferred time. Change requests depend on availability and outlet confirmation.</p>',
            },
            {
              title: 'Refund Requests',
              body: '<p>Refund requests are reviewed case by case. Approval may depend on the booking channel, timing of the request, package type, voucher conditions, and whether the session has already started or passed.</p>',
            },
            {
              title: 'No-Shows And Late Arrivals',
              body: '<p>Missed bookings or late arrivals may reduce play time and may not qualify for a refund. Contact the outlet as soon as possible if you are delayed.</p>',
            },
            {
              title: 'Vouchers And Promotions',
              body: '<p>Voucher and promotional purchases may carry their own terms, expiry dates, blackout dates, or redemption limits. Please check the voucher details before purchase or redemption.</p>',
            },
            {
              title: 'Outlet Disruption',
              body: '<p>If Overworld has to reschedule or cancel a session due to operational issues, the outlet team will advise available options, which may include rescheduling or another suitable arrangement.</p>',
            },
          ],
        },
      ],
      extra: `
  <section class="ow-info__section">
    <div class="ow-info__inner">
      <h2 class="ow-info__section-title">Need Help?</h2>
      <div class="ow-info__body">
        <p>Send your booking reference and preferred outlet to the relevant contact listed on the Contact page.</p>
      </div>
      <div class="ow-info__cta-row">
        <a class="ow-info__button" href="/contact">Contact An Outlet</a>
      </div>
    </div>
  </section>`,
    }),
  },
];

for (const item of pages) {
  const encoded = Buffer.from(item.content, 'utf8').toString('base64');
  const php = `
$slug = ${JSON.stringify(item.slug)};
$title = ${JSON.stringify(item.title)};
$content = base64_decode(${JSON.stringify(encoded)});
$existing = get_page_by_path($slug, OBJECT, 'page');
$postarr = [
  'post_title' => $title,
  'post_name' => $slug,
  'post_content' => $content,
  'post_status' => 'publish',
  'post_type' => 'page',
  'comment_status' => 'closed',
  'ping_status' => 'closed',
];
if ($existing) {
  $postarr['ID'] = $existing->ID;
  $id = wp_update_post(wp_slash($postarr), true);
} else {
  $id = wp_insert_post(wp_slash($postarr), true);
}
if (is_wp_error($id)) {
  fwrite(STDERR, $id->get_error_message() . PHP_EOL);
  exit(1);
}
delete_post_meta($id, '_elementor_edit_mode');
delete_post_meta($id, '_elementor_data');
delete_post_meta($id, '_elementor_element_cache');
clean_post_cache($id);
echo "upserted {$slug}: {$id}" . PHP_EOL;
`;
  wp(['eval', php]);
}

const footerPhp = `
$id = 566;
$backup_dir = '/home/u146877548/overworld-backups';
if (!is_dir($backup_dir)) {
  mkdir($backup_dir, 0755, true);
}
$data = get_post_meta($id, '_elementor_data', true);
file_put_contents($backup_dir . '/footer-566-elementor-data-before-info-pages-' . date('Ymd-His') . '.json', $data);
$replacements = [
  'mailto:REPLACE_ME_EMAIL_KALLANG@overworldvr.com' => 'mailto:support@overworldvr.com',
  'mailto:REPLACE_ME_EMAIL_ORCHARD@overworld.com.sg' => 'mailto:ocsupport@overworld.com.sg',
  'mailto:REPLACE_ME_EMAIL_FUNAN@overworld.com.sg' => 'mailto:funansupport@overworld.com.sg',
  'tel:+65REPLACE_ME_PHONE">+65 8914 0061' => 'tel:+6589150061">+65 8915 0061',
  'https://wa.me/+6596101682' => 'https://wa.me/6596101682',
  'https://wa.me/+6588014303' => 'https://wa.me/6588014303',
  'https://wa.me/+6589140061' => 'https://wa.me/6589140061',
];
$count = 0;
$data = str_replace(array_keys($replacements), array_values($replacements), $data, $count);
update_post_meta($id, '_elementor_data', wp_slash($data));
delete_post_meta($id, '_elementor_element_cache');
clean_post_cache($id);
wp_update_post(['ID' => $id, 'post_modified' => current_time('mysql'), 'post_modified_gmt' => current_time('mysql', true)]);
echo "footer replacements: {$count}" . PHP_EOL;
`;

wp(['eval', footerPhp]);
wp(['cache', 'flush']);
wp(['elementor', 'flush_css']);
wp(['eval', 'do_action("litespeed_purge_all"); echo "LiteSpeed purge requested" . PHP_EOL;']);

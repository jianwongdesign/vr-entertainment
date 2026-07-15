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

const blockAttrs = (attrs) => {
  const clean = Object.fromEntries(
    Object.entries(attrs).filter(([, value]) => {
      if (value === undefined || value === null) return false;
      if (typeof value === 'string' && value.length === 0) return false;
      return true;
    }),
  );

  return Object.keys(clean).length ? ` ${JSON.stringify(clean)}` : '';
};

const htmlClasses = (...classes) => classes.filter(Boolean).join(' ');

const group = ({ tagName = 'div', className = '', align = '', layout = 'constrained', content }) => {
  const attrs = {
    tagName,
    align,
    className,
    layout: { type: layout },
  };
  const classes = htmlClasses('wp-block-group', align ? `align${align}` : '', className);

  return `<!-- wp:group${blockAttrs(attrs)} -->
<${tagName} class="${classes}">
${content.trim()}
</${tagName}>
<!-- /wp:group -->`;
};

const paragraph = (html, className = '') => `<!-- wp:paragraph${blockAttrs({ className })} -->
<p${className ? ` class="${className}"` : ''}>${html}</p>
<!-- /wp:paragraph -->`;

const heading = (level, html, className = '') => `<!-- wp:heading${blockAttrs({ level, className })} -->
<h${level} class="${htmlClasses('wp-block-heading', className)}">${html}</h${level}>
<!-- /wp:heading -->`;

const paragraphList = (items = [], className = '') => items.map((item) => paragraph(item, className)).join('\n');

const bodyBlock = (items = []) => {
  if (!items.length) return '';

  return group({
    className: 'ow-info__body',
    layout: 'default',
    content: paragraphList(items),
  });
};

const renderPills = (pills = []) => {
  if (!pills.length) return '';

  return group({
    className: 'ow-info__meta',
    layout: 'default',
    content: paragraphList(pills, 'ow-info__pill'),
  });
};

const renderStats = (stats = []) => {
  if (!stats.length) return '';

  const statBlocks = stats.map((stat) => group({
    className: 'ow-info__stat',
    layout: 'default',
    content: [
      paragraph(stat.value, 'ow-info__stat-value'),
      paragraph(stat.label, 'ow-info__stat-label'),
    ].join('\n'),
  })).join('\n');

  return group({
    className: 'ow-info__stat-grid',
    layout: 'default',
    content: statBlocks,
  });
};

const renderHeroPanel = (panel) => {
  if (!panel) return '';

  return group({
    tagName: 'aside',
    className: 'ow-info__hero-panel',
    layout: 'default',
    content: [
      paragraph(panel.kicker, 'ow-info__panel-kicker'),
      heading(2, panel.title, 'ow-info__panel-title'),
      paragraph(panel.body, 'ow-info__panel-copy'),
      renderStats(panel.stats),
    ].filter(Boolean).join('\n'),
  });
};

const renderButtons = (buttons = []) => {
  if (!buttons.length) return '';

  const buttonBlocks = buttons.map((button) => `<!-- wp:button${blockAttrs({ className: htmlClasses('ow-info__button', button.variant === 'ghost' ? 'ow-info__button--ghost' : '') })} -->
<div class="${htmlClasses('wp-block-button', 'ow-info__button', button.variant === 'ghost' ? 'ow-info__button--ghost' : '')}"><a class="wp-block-button__link wp-element-button" href="${button.href}">${button.label}</a></div>
<!-- /wp:button -->`).join('\n');

  return `<!-- wp:buttons${blockAttrs({ className: 'ow-info__cta-row' })} -->
<div class="wp-block-buttons ow-info__cta-row">
${buttonBlocks}
</div>
<!-- /wp:buttons -->`;
};

const renderTile = (tile) => group({
  tagName: 'article',
  className: 'ow-info__tile',
  layout: 'default',
  content: [
    heading(3, tile.title, 'ow-info__card-title'),
    paragraphList(tile.body),
  ].join('\n'),
});

const renderContactCard = (card) => group({
  tagName: 'article',
  className: 'ow-info__contact-card',
  layout: 'default',
  content: [
    heading(3, card.title, 'ow-info__card-title'),
    paragraphList(card.body),
  ].join('\n'),
});

const renderPolicyBlock = (block) => group({
  tagName: 'article',
  className: 'ow-info__policy',
  layout: 'default',
  content: [
    heading(3, block.title, 'ow-info__card-title'),
    paragraphList(block.body),
  ].join('\n'),
});

const renderSection = (section) => {
  let contentBlocks = '';

  if (section.type === 'tiles') {
    contentBlocks = group({
      className: 'ow-info__grid',
      layout: 'default',
      content: section.tiles.map(renderTile).join('\n'),
    });
  }

  if (section.type === 'contact') {
    contentBlocks = group({
      className: 'ow-info__contact-grid',
      layout: 'default',
      content: section.cards.map(renderContactCard).join('\n'),
    });
  }

  if (section.type === 'policy') {
    contentBlocks = group({
      className: 'ow-info__policy-list',
      layout: 'default',
      content: section.blocks.map(renderPolicyBlock).join('\n'),
    });
  }

  return group({
    tagName: 'section',
    align: 'full',
    className: 'ow-info__section',
    content: group({
      className: 'ow-info__inner',
      content: [
        heading(2, section.title, 'ow-info__section-title'),
        bodyBlock(section.body),
        contentBlocks,
      ].filter(Boolean).join('\n'),
    }),
  });
};

const renderCtaSection = (section) => group({
  tagName: 'section',
  align: 'full',
  className: 'ow-info__section ow-info__section--cta',
  content: group({
    className: 'ow-info__inner',
    content: [
      heading(2, section.title, 'ow-info__section-title'),
      bodyBlock(section.body),
      renderButtons(section.buttons),
    ].filter(Boolean).join('\n'),
  }),
});

const page = ({ title, slug, eyebrow, lede, pills = [], panel, sections, ctaSections = [] }) => {
  const hero = group({
    tagName: 'section',
    align: 'full',
    className: 'ow-info__hero',
    content: group({
      className: 'ow-info__inner ow-info__hero-inner',
      content: [
        group({
          className: 'ow-info__hero-copy',
          layout: 'default',
          content: [
            paragraph(eyebrow, 'ow-info__eyebrow'),
            heading(1, title, 'ow-info__title'),
            paragraph(lede, 'ow-info__lede'),
            renderPills(pills),
          ].filter(Boolean).join('\n'),
        }),
        renderHeroPanel(panel),
      ].filter(Boolean).join('\n'),
    }),
  });

  return group({
    tagName: 'main',
    align: 'full',
    className: `ow-info ow-info--${slug}`,
    content: [
      hero,
      sections.map(renderSection).join('\n'),
      ctaSections.map(renderCtaSection).join('\n'),
    ].filter(Boolean).join('\n'),
  });
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
      panel: {
        kicker: 'Singapore Play Network',
        title: '3 outlets built for fast, social, replayable fun.',
        body: 'Choose the outlet by activity mix, group size, and occasion, then book the session directly.',
        stats: [
          { value: 'VR', label: 'Arcade, escape, rides' },
          { value: 'Lava', label: 'Physical challenge rooms' },
          { value: 'Events', label: 'Parties and teams' },
        ],
      },
      sections: [
        {
          type: 'tiles',
          title: 'What We Build',
          body: ['Each outlet has its own mix of experiences, but the goal is the same: easy-to-book games that feel exciting, social, and memorable.'],
          tiles: [
            {
              title: 'Virtual Reality',
              body: ['VR Arcade, VR Escape, machine rides, and free-roam adventures for players who want to step into another world.'],
            },
            {
              title: 'Physical Games',
              body: ['Floor Is Lava, Laser Maze, Tap Tap, and other real-world challenges that reward speed, teamwork, and reflexes.'],
            },
            {
              title: 'Group Events',
              body: ['Birthday parties, school outings, team building, and private group sessions planned around the right outlet and activity mix.'],
            },
          ],
        },
        {
          type: 'tiles',
          title: 'Our Outlets',
          tiles: [
            {
              title: 'Kallang Wave Mall',
              body: [
                'Overworld VR at Kallang is the flagship VR-heavy outlet with VR Arcade, VR Escape, VR Machine Ride, and Floor Is Lava.',
                '<a href="/pricing/kallang-wave-mall">View Kallang pricing</a>',
              ],
            },
            {
              title: 'Orchard Central',
              body: [
                'Overworld Lava at Orchard focuses on physical play with Floor Is Lava, Laser Maze, Tap Tap, parties, and team challenges.',
                '<a href="/pricing/orchard-central">View Orchard pricing</a>',
              ],
            },
            {
              title: 'Funan',
              body: [
                'Overworld Funan brings Floor Is Lava, VR Free Roam, and XR Party Game into a central mall location for groups and casual players.',
                '<a href="/pricing/funan">View Funan pricing</a>',
              ],
            },
          ],
        },
      ],
    }),
  },
  {
    title: 'Contact Us',
    slug: 'contact',
    content: page({
      title: 'Contact Us',
      slug: 'contact',
      eyebrow: 'Support And Enquiries',
      lede: 'Reach the outlet directly for bookings, group events, changes to a reservation, or general questions before your visit.',
      pills: ['Call', 'WhatsApp', 'Email'],
      panel: {
        kicker: 'Fastest Route',
        title: 'Contact the outlet you plan to visit.',
        body: 'For birthday parties, school visits, or team building, send the date, outlet, group size, and preferred activities.',
        stats: [
          { value: '3', label: 'Outlet contacts' },
          { value: 'Bookeo', label: 'Direct booking links' },
          { value: 'SG', label: 'Mall locations' },
        ],
      },
      sections: [
        {
          type: 'contact',
          title: 'Outlet Contacts',
          body: ['For the fastest response, contact the outlet you plan to visit.'],
          cards: [
            {
              title: 'Kallang Wave Mall',
              body: [
                '1 Stadium Place, #01-63/64<br>Kallang Wave Mall, Singapore 397628',
                '<a href="tel:+6565130561">+65 6513 0561</a><br><a href="https://wa.me/6596101682" target="_blank" rel="noopener">WhatsApp +65 9610 1682</a><br><a href="mailto:support@overworldvr.com">support@overworldvr.com</a>',
                '<a href="https://bookeo.com/overworldkallangwavemall" target="_blank" rel="noopener">Book Kallang</a>',
              ],
            },
            {
              title: 'Orchard Central',
              body: [
                '181 Orchard Road, #05-30/K1/K3<br>Orchard Central, Singapore 238896',
                '<a href="tel:+6588014303">+65 8801 4303</a><br><a href="https://wa.me/6588014303" target="_blank" rel="noopener">WhatsApp +65 8801 4303</a><br><a href="mailto:ocsupport@overworld.com.sg">ocsupport@overworld.com.sg</a>',
                '<a href="https://bookeo.com/overworldorchardcentral" target="_blank" rel="noopener">Book Orchard</a>',
              ],
            },
            {
              title: 'Funan',
              body: [
                '107 North Bridge Road, #04-14 & K1<br>Funan, Singapore 179105',
                '<a href="tel:+6589140061">+65 8914 0061</a><br><a href="https://wa.me/6589140061" target="_blank" rel="noopener">WhatsApp +65 8914 0061</a><br><a href="mailto:funansupport@overworld.com.sg">funansupport@overworld.com.sg</a>',
                '<a href="https://bookeo.com/overworldfunan" target="_blank" rel="noopener">Book Funan</a>',
              ],
            },
          ],
        },
      ],
      ctaSections: [
        {
          title: 'Event Enquiries',
          body: ['For birthday parties, team building, school visits, or private events, share your preferred date, outlet, group size, and activity interest so the team can recommend the right format.'],
          buttons: [
            { label: 'Team Building', href: '/team-building' },
            { label: 'Birthday Party', href: '/birthday-party', variant: 'ghost' },
          ],
        },
      ],
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
      panel: {
        kicker: 'Client Editable',
        title: 'Structured policy sections ready for updates.',
        body: 'Each policy item is a separate editor block, so future copy changes can be made without touching the site layout.',
        stats: [
          { value: 'PDPA', label: 'Singapore-friendly structure' },
          { value: 'Blocks', label: 'Easy content edits' },
          { value: 'Live', label: 'Footer linked' },
        ],
      },
      sections: [
        {
          type: 'policy',
          title: 'Privacy Details',
          blocks: [
            {
              title: 'Personal Data We May Collect',
              body: ['We may collect details such as your name, phone number, email address, booking details, event requirements, payment or voucher references, and messages sent to our team.'],
            },
            {
              title: 'How We Use Personal Data',
              body: ['We use personal data to handle enquiries, manage bookings, arrange events, provide customer support, improve the website, send service updates, and meet operational or legal requirements.'],
            },
            {
              title: 'Bookings, Vouchers, And Third-Party Platforms',
              body: ['Some bookings or voucher purchases may be handled through third-party platforms such as Bookeo or voucher providers. Those platforms may collect and process information under their own policies.'],
            },
            {
              title: 'Sharing And Protection',
              body: ['We do not sell customer personal data. We may share data with service providers where needed to operate bookings, payments, communications, analytics, website hosting, or customer support. We take reasonable steps to protect personal data from unauthorised access or misuse.'],
            },
            {
              title: 'Access, Correction, And Contact',
              body: ['If you wish to access, correct, or ask about personal data held by Overworld, contact the relevant outlet or email <a href="mailto:support@overworldvr.com">support@overworldvr.com</a>.'],
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
      eyebrow: 'Website And Visit Terms',
      lede: 'These terms set out the general conditions for using the Overworld website, making bookings, and visiting our outlets.',
      pills: [`Last updated ${updatedDate}`, 'Editable page'],
      panel: {
        kicker: 'Client Editable',
        title: 'Terms laid out as readable policy cards.',
        body: 'The page is structured for quick scanning on mobile and desktop, while keeping each section simple to update later.',
        stats: [
          { value: '5', label: 'Term sections' },
          { value: 'Footer', label: 'Linked from sitewide footer' },
          { value: 'Blocks', label: 'Editor-ready layout' },
        ],
      },
      sections: [
        {
          type: 'policy',
          title: 'Terms',
          blocks: [
            {
              title: 'Website Use',
              body: ['Information on this website is provided for general customer reference. We try to keep details accurate, but activities, prices, packages, opening hours, and availability may change without prior notice.'],
            },
            {
              title: 'Bookings And Attendance',
              body: ['Customers are responsible for selecting the correct outlet, activity, date, and time when making a booking. Please arrive on time and follow outlet instructions so your session can run smoothly.'],
            },
            {
              title: 'Safety And Conduct',
              body: ['Customers must follow staff guidance, safety rules, age or height restrictions, and activity instructions. Overworld may refuse or stop participation where behaviour is unsafe, disruptive, or unsuitable for the activity.'],
            },
            {
              title: 'Payments And Promotions',
              body: ['Prices, promotions, package terms, and voucher conditions may differ by outlet and campaign. Promotional offers cannot always be combined unless stated by Overworld.'],
            },
            {
              title: 'Website Links',
              body: ['The website may link to third-party services for booking, vouchers, maps, payment, or social media. Overworld is not responsible for the content or policies of third-party websites.'],
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
      panel: {
        kicker: 'Customer Support',
        title: 'Clear refund and change guidance.',
        body: 'Customers can quickly see what to prepare before contacting the outlet about a change, missed session, or voucher question.',
        stats: [
          { value: 'Ref', label: 'Booking reference' },
          { value: 'Outlet', label: 'Contact directly' },
          { value: 'Early', label: 'Best time to ask' },
        ],
      },
      sections: [
        {
          type: 'policy',
          title: 'Refunds And Changes',
          blocks: [
            {
              title: 'Booking Changes',
              body: ['If you need to change a booking, contact the outlet directly with your booking reference, name, phone number, preferred new date, and preferred time. Change requests depend on availability and outlet confirmation.'],
            },
            {
              title: 'Refund Requests',
              body: ['Refund requests are reviewed case by case. Approval may depend on the booking channel, timing of the request, package type, voucher conditions, and whether the session has already started or passed.'],
            },
            {
              title: 'No-Shows And Late Arrivals',
              body: ['Missed bookings or late arrivals may reduce play time and may not qualify for a refund. Contact the outlet as soon as possible if you are delayed.'],
            },
            {
              title: 'Vouchers And Promotions',
              body: ['Voucher and promotional purchases may carry their own terms, expiry dates, blackout dates, or redemption limits. Please check the voucher details before purchase or redemption.'],
            },
            {
              title: 'Outlet Disruption',
              body: ['If Overworld has to reschedule or cancel a session due to operational issues, the outlet team will advise available options, which may include rescheduling or another suitable arrangement.'],
            },
          ],
        },
      ],
      ctaSections: [
        {
          title: 'Need Help?',
          body: ['Send your booking reference and preferred outlet to the relevant contact listed on the Contact page.'],
          buttons: [
            { label: 'Contact An Outlet', href: '/contact' },
          ],
        },
      ],
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
update_post_meta($id, '_wp_page_template', 'elementor_header_footer');
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
  'tel:+65REPLACE_ME_PHONE">+65 8914 0061' => 'tel:+6589140061">+65 8914 0061',
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

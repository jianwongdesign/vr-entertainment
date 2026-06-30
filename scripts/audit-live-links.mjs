#!/usr/bin/env node

const BASE_URL = process.env.LINK_AUDIT_BASE_URL || 'https://overworld.com.sg';
const USER_AGENT = 'OverworldLinkAudit/1.0 (+https://overworld.com.sg)';
const TIMEOUT_MS = Number(process.env.LINK_AUDIT_TIMEOUT_MS || 15000);

function decodeHtml(value) {
  return value
    .replace(/&#(\d+);/g, (_, code) => String.fromCharCode(Number(code)))
    .replace(/&#x([0-9a-f]+);/gi, (_, code) => String.fromCharCode(parseInt(code, 16)))
    .replace(/&amp;/g, '&')
    .replace(/&quot;/g, '"')
    .replace(/&#039;/g, "'")
    .replace(/&apos;/g, "'")
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&nbsp;/g, ' ');
}

function stripTags(value) {
  return decodeHtml(value.replace(/<script[\s\S]*?<\/script>/gi, '')
    .replace(/<style[\s\S]*?<\/style>/gi, '')
    .replace(/<[^>]+>/g, ' ')
    .replace(/\s+/g, ' ')
    .trim());
}

function getAttr(attrs, name) {
  const escapedName = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  const pattern = new RegExp(`\\b${escapedName}\\s*=\\s*(?:"([^"]*)"|'([^']*)'|([^\\s"'>]+))`, 'i');
  const match = attrs.match(pattern);
  if (!match) return '';
  return decodeHtml(match[1] ?? match[2] ?? match[3] ?? '').trim();
}

async function fetchText(url) {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), TIMEOUT_MS);
  try {
    const response = await fetch(url, {
      redirect: 'follow',
      signal: controller.signal,
      headers: {
        'user-agent': USER_AGENT,
        accept: 'text/html,application/json;q=0.9,*/*;q=0.8',
      },
    });
    return {
      ok: response.ok,
      status: response.status,
      url: response.url,
      text: await response.text(),
    };
  } finally {
    clearTimeout(timer);
  }
}

async function fetchJson(url) {
  const result = await fetchText(url);
  if (!result.ok) {
    throw new Error(`Failed to fetch ${url}: HTTP ${result.status}`);
  }
  return JSON.parse(result.text);
}

function extractLinks(html, pageUrl) {
  const links = [];
  const anchorPattern = /<a\b([^>]*)>([\s\S]*?)<\/a>/gi;
  let match;

  while ((match = anchorPattern.exec(html))) {
    const attrs = match[1];
    const inner = match[2];
    const href = getAttr(attrs, 'href');
    const text = stripTags(inner) || getAttr(attrs, 'aria-label') || getAttr(attrs, 'title') || '[no visible text]';
    const className = getAttr(attrs, 'class');
    const id = getAttr(attrs, 'id');
    const isButton = /\b(button|btn|cta)\b|elementor-button|wp-block-button|ow-foot-a__cta/i.test(`${className} ${id}`);

    links.push({
      type: isButton ? 'button-link' : 'link',
      href,
      text,
      className,
      id,
      pageUrl,
    });
  }

  const buttonPattern = /<button\b([^>]*)>([\s\S]*?)<\/button>/gi;
  while ((match = buttonPattern.exec(html))) {
    const attrs = match[1];
    const text = stripTags(match[2]) || getAttr(attrs, 'aria-label') || getAttr(attrs, 'title') || '[no visible text]';
    links.push({
      type: 'button-element',
      href: '',
      text,
      className: getAttr(attrs, 'class'),
      id: getAttr(attrs, 'id'),
      pageUrl,
    });
  }

  return links;
}

function normalizeHref(href, pageUrl) {
  if (!href) return { kind: 'empty', normalized: '' };
  const trimmed = href.trim();
  if (trimmed === '#') return { kind: 'hash', normalized: trimmed };
  if (/^javascript:/i.test(trimmed)) return { kind: 'javascript', normalized: trimmed };
  if (/^(mailto|tel|sms):/i.test(trimmed)) return { kind: 'protocol', normalized: trimmed };

  try {
    const url = new URL(trimmed, pageUrl);
    url.hash = '';
    return { kind: url.protocol === 'http:' || url.protocol === 'https:' ? 'http' : 'protocol', normalized: url.toString() };
  } catch (error) {
    return { kind: 'invalid', normalized: trimmed, error: error.message };
  }
}

function canonicalUrl(urlString) {
  const url = new URL(urlString);
  url.hash = '';
  if (url.pathname !== '/' && url.pathname.endsWith('/')) {
    url.pathname = url.pathname.slice(0, -1);
  }
  url.searchParams.sort();
  return url.toString();
}

function classifyRawLink(link, normalized) {
  const value = `${link.href} ${link.text}`;
  const issues = [];

  if (link.type === 'button-element') {
    issues.push('native button element; verify JavaScript/form action manually');
  }
  if (normalized.kind === 'empty') issues.push('missing href');
  if (normalized.kind === 'hash') issues.push('href is #');
  if (normalized.kind === 'javascript') issues.push('javascript href');
  if (normalized.kind === 'invalid') issues.push('invalid URL');
  if (/REPLACE_ME|TODO|TBD|INSERT_|CHANGE_ME/i.test(value)) issues.push('placeholder value');
  if (/^mailto:/i.test(link.href)) {
    const mailtoTarget = link.href.replace(/^mailto:/i, '').split('?')[0];
    if (!/^[^@\s:]+@[^@\s]+\.[^@\s]+$/i.test(mailtoTarget)) {
      issues.push('invalid mailto target');
    }
  }
  if (/^tel:/i.test(link.href) && !/^\+?[0-9\s().-]+$/i.test(link.href.replace(/^tel:/i, ''))) {
    issues.push('invalid tel target');
  }
  if (/wa\.me\/\+/i.test(link.href)) {
    issues.push('WhatsApp wa.me link contains plus sign');
  }

  return issues;
}

async function checkHttpLink(urlString) {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), TIMEOUT_MS);
  try {
    const response = await fetch(urlString, {
      redirect: 'follow',
      signal: controller.signal,
      headers: {
        'user-agent': USER_AGENT,
        accept: 'text/html,*/*;q=0.8',
      },
    });

    return {
      status: response.status,
      ok: response.status < 400,
      finalUrl: response.url,
      redirected: canonicalUrl(response.url) !== canonicalUrl(urlString),
    };
  } catch (error) {
    return {
      status: null,
      ok: false,
      finalUrl: '',
      redirected: false,
      error: error.name === 'AbortError' ? 'timeout' : error.message,
    };
  } finally {
    clearTimeout(timer);
  }
}

function uniqueSorted(values) {
  return [...new Set(values.filter(Boolean))].sort((a, b) => a.localeCompare(b));
}

function groupLinks(rawLinks) {
  const groups = new Map();

  for (const link of rawLinks) {
    const normalized = normalizeHref(link.href, link.pageUrl);
    const key = `${normalized.kind}:${normalized.normalized}`;
    if (!groups.has(key)) {
      groups.set(key, {
        key,
        kind: normalized.kind,
        href: link.href,
        normalized: normalized.normalized,
        links: [],
        rawIssues: [],
      });
    }
    const group = groups.get(key);
    group.links.push(link);
    group.rawIssues.push(...classifyRawLink(link, normalized));
  }

  return [...groups.values()].map((group) => ({
    ...group,
    rawIssues: uniqueSorted(group.rawIssues),
    labels: uniqueSorted(group.links.map((link) => link.text)).slice(0, 8),
    pages: uniqueSorted(group.links.map((link) => link.pageUrl)),
    types: uniqueSorted(group.links.map((link) => link.type)),
  }));
}

function pageTitle(page) {
  return stripTags(page.title?.rendered || page.slug || page.link);
}

function printSection(title, rows, formatRow, limit = 80) {
  console.log(`\n## ${title}`);
  if (!rows.length) {
    console.log('\nNone found.');
    return;
  }

  for (const row of rows.slice(0, limit)) {
    console.log(formatRow(row));
  }

  if (rows.length > limit) {
    console.log(`\n...and ${rows.length - limit} more.`);
  }
}

async function main() {
  const base = new URL(BASE_URL);
  const pages = await fetchJson(new URL('/wp-json/wp/v2/pages?per_page=100&_fields=id,link,slug,title', base).toString());

  const pageResults = [];
  const allLinks = [];

  for (const page of pages) {
    const pageUrl = page.link;
    const result = await fetchText(pageUrl);
    pageResults.push({
      title: pageTitle(page),
      url: pageUrl,
      status: result.status,
      ok: result.ok,
      finalUrl: result.url,
    });
    if (result.ok) {
      allLinks.push(...extractLinks(result.text, pageUrl));
    }
  }

  const grouped = groupLinks(allLinks);
  const httpGroups = grouped.filter((group) => group.kind === 'http');

  const checks = new Map();
  for (const group of httpGroups) {
    checks.set(group.key, await checkHttpLink(group.normalized));
  }

  const withChecks = grouped.map((group) => ({
    ...group,
    check: checks.get(group.key) || null,
    isInternal: group.kind === 'http' && new URL(group.normalized).hostname === base.hostname,
  }));

  const brokenInternal = withChecks
    .filter((group) => group.isInternal && group.check && !group.check.ok)
    .sort((a, b) => (b.links.length - a.links.length) || a.normalized.localeCompare(b.normalized));

  const suspicious = withChecks
    .filter((group) => group.rawIssues.length)
    .sort((a, b) => (b.links.length - a.links.length) || a.normalized.localeCompare(b.normalized));

  const internalRedirects = withChecks
    .filter((group) => group.isInternal && group.check?.ok && group.check.redirected)
    .sort((a, b) => (b.links.length - a.links.length) || a.normalized.localeCompare(b.normalized));

  const externalProblems = withChecks
    .filter((group) => !group.isInternal && group.kind === 'http' && group.check && !group.check.ok)
    .sort((a, b) => (b.links.length - a.links.length) || a.normalized.localeCompare(b.normalized));

  console.log(`# Link Audit: ${base.origin}`);
  console.log(`\nChecked pages: ${pageResults.length}`);
  console.log(`Anchors/buttons found: ${allLinks.length}`);
  console.log(`Unique href targets: ${grouped.length}`);
  console.log(`Confirmed broken internal targets: ${brokenInternal.length}`);
  console.log(`Suspicious placeholder/empty/button targets: ${suspicious.length}`);
  console.log(`Internal redirects to review: ${internalRedirects.length}`);
  console.log(`External targets with fetch problems: ${externalProblems.length}`);

  printSection('Pages Crawled', pageResults, (page) => (
    `- ${page.status} ${page.title}: ${page.url}${page.finalUrl !== page.url ? ` -> ${page.finalUrl}` : ''}`
  ));

  printSection('Confirmed Broken Internal Links', brokenInternal, (group) => (
    `- HTTP ${group.check.status || group.check.error} ${group.normalized}\n` +
    `  labels: ${group.labels.join(' | ')}\n` +
    `  seen on: ${group.pages.slice(0, 5).join(', ')}${group.pages.length > 5 ? `, +${group.pages.length - 5} more` : ''}`
  ));

  printSection('Suspicious Links And Buttons', suspicious, (group) => (
    `- ${group.rawIssues.join('; ')}: ${group.href || '[no href]'}\n` +
    `  labels: ${group.labels.join(' | ')}\n` +
    `  types: ${group.types.join(', ')}\n` +
    `  seen on: ${group.pages.slice(0, 5).join(', ')}${group.pages.length > 5 ? `, +${group.pages.length - 5} more` : ''}`
  ));

  printSection('Internal Redirects To Review', internalRedirects, (group) => (
    `- ${group.normalized} -> ${group.check.finalUrl}\n` +
    `  labels: ${group.labels.join(' | ')}\n` +
    `  seen on: ${group.pages.slice(0, 5).join(', ')}${group.pages.length > 5 ? `, +${group.pages.length - 5} more` : ''}`
  ));

  printSection('External Fetch Problems', externalProblems, (group) => (
    `- ${group.check.status || group.check.error} ${group.normalized}\n` +
    `  labels: ${group.labels.join(' | ')}\n` +
    `  seen on: ${group.pages.slice(0, 5).join(', ')}${group.pages.length > 5 ? `, +${group.pages.length - 5} more` : ''}`
  ));
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});

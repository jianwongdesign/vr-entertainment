#!/usr/bin/env bash
#
# Take the event rental page live, end to end.
#
#   1. deploy-event-rental.sh --apply   sends the mu-plugin + template,
#                                       creates /event-rental/ as a draft
#   2. nav-event-rental-link.php        Events ▾ → "Equipment Rental" in the
#                                       header, and in the footer's Events list
#   3. publish the page
#   4. purge LiteSpeed + object cache
#   5. verify: HTTP 200, title tag, robots (index), sitemap, ACF box wired,
#      the mu-plugin loaded, the page's own template locked
#   6. audit: active plugins, and any WordPress core / plugin / theme updates
#      pending — REPORTED, NOT APPLIED. Updating Elementor unattended is what
#      took the site down on 6 Aug 2026; apply updates by hand, one at a time,
#      with a backup first.
#
# Dry run (default) prints the plan and changes nothing:
#   ./scripts/go-live-event-rental.sh
#
# Go live:
#   CONFIRM_PUSH=overworld.com.sg ./scripts/go-live-event-rental.sh --apply
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=scripts/lib/env.sh
source "${SCRIPT_DIR}/lib/env.sh"

APPLY=0
if [[ "${1:-}" == "--apply" ]]; then
  APPLY=1
elif [[ "$#" -gt 0 ]]; then
  echo "Usage: $0 [--apply]" >&2
  exit 2
fi

SITE_URL="https://overworld.com.sg"
PAGE_SLUG="event-rental"

if [[ "${APPLY}" -eq 0 ]]; then
  echo "Dry run. Nothing is sent, created, published or purged."
  echo
  echo "Would:"
  echo "  1. deploy-event-rental.sh --apply   (2 files + draft page)"
  echo "  2. run nav-event-rental-link.php    (header + footer links)"
  echo "  3. publish /${PAGE_SLUG}/"
  echo "  4. purge caches"
  echo "  5. verify the live page"
  echo "  6. list active plugins and pending updates (no updates applied)"
  echo
  echo "Re-run with: CONFIRM_PUSH=overworld.com.sg $0 --apply"
  exit 0
fi

if [[ "${CONFIRM_PUSH:-}" != "overworld.com.sg" ]]; then
  echo "Refusing live deploy. Re-run with CONFIRM_PUSH=overworld.com.sg" >&2
  exit 1
fi

printf -v QUOTED_PATH "%q" "${REMOTE_WP_PATH}"
remote() { ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" "cd ${QUOTED_PATH} && $*"; }
remote_wp_file() {
  local local_file="$1" remote_tmp="/tmp/$(basename "$1" .php)-$$.php"
  scp "${SSH_OPTS[@]}" "${local_file}" "${SSH_TARGET}:${remote_tmp}"
  remote "wp eval-file ${remote_tmp}; rm -f ${remote_tmp}"
}

# --- 1. files + draft page --------------------------------------------------
echo "==> 1. Deploying files and creating the draft"
CONFIRM_PUSH=overworld.com.sg "${SCRIPT_DIR}/deploy-event-rental.sh" --apply

PAGE_ID="$(remote "wp post list --post_type=page --name=${PAGE_SLUG} --field=ID --post_status=any" | tr -d '[:space:]')"
if [[ -z "${PAGE_ID}" ]]; then
  echo "Could not find the page after deploy — stopping before nav/publish." >&2
  exit 1
fi
echo "    page id ${PAGE_ID}"

# --- 2. navigation -----------------------------------------------------------
echo
echo "==> 2. Adding \"Equipment Rental\" to the header and footer"
remote_wp_file "${SCRIPT_DIR}/nav-event-rental-link.php"

# --- 3. publish --------------------------------------------------------------
echo
echo "==> 3. Publishing"
remote "wp post update ${PAGE_ID} --post_status=publish"

# --- 4. caches ---------------------------------------------------------------
echo
echo "==> 4. Purging caches"
remote "wp cache flush; wp litespeed-purge all" || echo "(cache purge reported an error — check by hand)"
# Elementor's CSS cache holds the header/footer render.
remote "wp elementor flush-css" 2>/dev/null || true

# --- 5. verify ---------------------------------------------------------------
echo
echo "==> 5. Verifying ${SITE_URL}/${PAGE_SLUG}/"
sleep 3
PAGE_HTML="$(curl -sL -A 'overworld-go-live-check' "${SITE_URL}/${PAGE_SLUG}/")"
STATUS="$(curl -sL -A 'overworld-go-live-check' -o /dev/null -w '%{http_code}' "${SITE_URL}/${PAGE_SLUG}/")"
check() { if [[ "$2" -eq 1 ]]; then echo "    ok    $1"; else echo "    FAIL  $1"; fi; }

check "HTTP ${STATUS}"                                  "$([[ "${STATUS}" == "200" ]] && echo 1 || echo 0)"
check "template markup present (ow-rental)"             "$(grep -q 'class="ow-rental"' <<<"${PAGE_HTML}" && echo 1 || echo 0)"
check "title tag from the SEO map"                      "$(grep -q '<title>Interactive Game Rental for Events' <<<"${PAGE_HTML}" && echo 1 || echo 0)"
check "meta description present"                        "$(grep -q 'name="description"' <<<"${PAGE_HTML}" && echo 1 || echo 0)"
check "not noindex"                                     "$(grep -qi 'name="robots"[^>]*noindex' <<<"${PAGE_HTML}" && echo 0 || echo 1)"
check "FAQPage + Service schema"                        "$(grep -q '"FAQPage"' <<<"${PAGE_HTML}" && grep -q '"Service"' <<<"${PAGE_HTML}" && echo 1 || echo 0)"
check "header nav links to /${PAGE_SLUG}/"              "$(grep -q "href=\"/${PAGE_SLUG}/\">Equipment Rental" <<<"${PAGE_HTML}" && echo 1 || echo 0)"
check "activity cards have photos"                      "$(grep -c 'ow-rental__card-media"' <<<"${PAGE_HTML}" | awk '{print ($1>=3)?1:0}')"

SITEMAP_HITS="$(curl -sL "${SITE_URL}/wp-sitemap-posts-page-1.xml" | grep -c "/${PAGE_SLUG}/" || true)"
check "in the sitemap (${SITEMAP_HITS})"                "$([[ "${SITEMAP_HITS}" -ge 1 ]] && echo 1 || echo 0)"

echo
echo "    server-side:"
remote "wp eval '
\$id = ${PAGE_ID};
echo \"    template  : \" . get_post_meta(\$id, \"_wp_page_template\", true) . \"\n\";
echo \"    status    : \" . get_post_status(\$id) . \"\n\";
echo \"    mu-plugin : \" . (function_exists(\"ow_rental_defaults\") ? \"loaded\" : \"NOT LOADED\") . \"\n\";
echo \"    ACF group : \" . (function_exists(\"acf_get_field_group\") && acf_get_field_group(\"group_ow_event_rental\") ? \"registered\" : \"NOT REGISTERED\") . \"\n\";
echo \"    guarded   : \" . (function_exists(\"ow_guarded_page_templates\") && isset(ow_guarded_page_templates()[\$id]) ? \"yes\" : \"no\") . \"\n\";
echo \"    edit url  : \" . admin_url(\"post.php?post=\$id&action=edit\") . \"\n\";
'"

# --- 6. plugin + update audit (report only) ----------------------------------
echo
echo "==> 6. Plugin and update audit (nothing is updated by this script)"
echo "    core:"
remote "wp core version; wp core check-update || true" | sed 's/^/      /'
echo "    plugins:"
remote "wp plugin list --fields=name,status,version,update,update_version" | sed 's/^/      /'
echo "    themes:"
remote "wp theme list --fields=name,status,version,update" | sed 's/^/      /'
echo "    mu-plugins:"
remote "wp plugin list --status=must-use --fields=name,version" | sed 's/^/      /'

echo
echo "Done. ${SITE_URL}/${PAGE_SLUG}/ is live."
echo "If anything above says FAIL, NOT LOADED or NOT REGISTERED, stop and check before telling the client."
echo "Pending updates listed above are for you to apply by hand (backup first: ./scripts/backup-remote.sh)."

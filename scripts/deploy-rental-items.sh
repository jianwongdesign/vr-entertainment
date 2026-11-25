#!/usr/bin/env bash
#
# Deploy Rental Items — a page per rental activity, with a tag and highlight
# chips on its card on /event-rental/.
#
#   mu-plugins/overworld-rental-items.php           the content type, its ACF
#                                                   box, CSS and SEO wiring
#   mu-plugins/overworld-event-rental.php           cards now come from Rental
#                                                   Items; three new icons
#   mu-plugins/overworld-admin-ui.php               sidebar entry under
#                                                   "Website Content"
#   themes/hello-elementor-child/page-event-rental.php   card tag + chips
#   themes/hello-elementor-child/single-rental_item.php  one item's page
#
# Then flushes the rewrite rules (/event-rental/<name>/ 404s without it),
# creates the three current activities as published Rental Items the first
# time (scripts/rental-item-samples.php), and purges the caches.
#
# Dry run (default):
#   ./scripts/deploy-rental-items.sh
# Deploy:
#   CONFIRM_PUSH=overworld.com.sg ./scripts/deploy-rental-items.sh --apply
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

FILES=(
  "mu-plugins/overworld-rental-items.php"
  "mu-plugins/overworld-event-rental.php"
  "mu-plugins/overworld-admin-ui.php"
  "themes/hello-elementor-child/page-event-rental.php"
  "themes/hello-elementor-child/single-rental_item.php"
)

LOCAL_SOURCE="${PROJECT_ROOT}/${LOCAL_WP_CONTENT}"

for REL in "${FILES[@]}"; do
  if [[ ! -f "${LOCAL_SOURCE}/${REL}" ]]; then
    echo "Missing ${LOCAL_SOURCE}/${REL}" >&2
    exit 1
  fi
  php -l "${LOCAL_SOURCE}/${REL}" >/dev/null
done

if [[ "${APPLY}" -eq 0 ]]; then
  echo "Dry run. Nothing is sent, flushed or created."
  echo
  echo "Would send to ${SSH_TARGET}:${REMOTE_WP_PATH}/wp-content/"
  for REL in "${FILES[@]}"; do
    printf '  %s\n' "${REL}"
  done
  echo
  echo "Would then flush the rewrite rules, create the three current activities"
  echo "as published Rental Items if none exist, and purge the caches."
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

# --- 0. back up the files we are about to overwrite -------------------------
STAMP="$(date +%Y%m%d-%H%M%S)"
echo "==> Backing up the current remote copies to /tmp/*.bak-${STAMP}"
for REL in "${FILES[@]}"; do
  BASE="$(basename "${REL}")"
  remote "[ -f wp-content/${REL} ] && cp wp-content/${REL} /tmp/${BASE}.bak-${STAMP} || true"
done

# --- 1. the files -----------------------------------------------------------
echo
echo "==> Sending ${#FILES[@]} files"
# One plain rsync per file to its explicit destination directory. Not
# --relative with a /./ anchor: macOS ships openrsync, which ignores the
# anchor and recreates the full local path on the server.
for REL in "${FILES[@]}"; do
  rsync -vz -e "${RSYNC_SSH}" \
    "${LOCAL_SOURCE}/${REL}" \
    "${SSH_TARGET}:${REMOTE_WP_PATH}/wp-content/$(dirname "${REL}")/"
done

# --- 2. rewrite rules -------------------------------------------------------
echo
echo "==> Flushing the rewrite rules (/event-rental/<name>/ URLs depend on this)"
remote "wp rewrite flush --hard"

# --- 3. the three current activities ---------------------------------------
echo
echo "==> Rental Items (only if none exist)"
REMOTE_TMP="/tmp/rental-item-samples-$$.php"
scp "${SCP_OPTS[@]}" "${SCRIPT_DIR}/rental-item-samples.php" "${SSH_TARGET}:${REMOTE_TMP}"
remote "wp eval-file ${REMOTE_TMP}; rm -f ${REMOTE_TMP}"

# --- 4. caches --------------------------------------------------------------
echo
echo "==> Purging caches"
remote "wp cache flush; wp litespeed-purge all" || echo "(cache purge reported an error — check by hand)"
remote "wp elementor flush-css" 2>/dev/null || true

# --- 5. verify -------------------------------------------------------------
echo
echo "==> Verifying"
remote "wp eval '
echo \"    content type : \" . ( post_type_exists( \"rental_item\" ) ? \"registered\" : \"NOT REGISTERED\" ) . \"\n\";
echo \"    ACF box      : \" . ( function_exists( \"acf_get_field_group\" ) && acf_get_field_group( \"group_ow_rental_item\" ) ? \"registered\" : \"NOT REGISTERED\" ) . \"\n\";
echo \"    published    : \" . count( get_posts( array( \"post_type\" => \"rental_item\", \"numberposts\" => -1, \"fields\" => \"ids\" ) ) ) . \"\n\";
'"

SITE_URL="https://overworld.com.sg"
check() { if [[ "$2" -eq 1 ]]; then echo "    ok    $1"; else echo "    FAIL  $1"; fi; }

sleep 2
for SLUG in vr-free-roam floor-is-lava xr-party-game; do
  STATUS="$(curl -sL -A 'overworld-deploy-check' -o /dev/null -w '%{http_code}' "${SITE_URL}/event-rental/${SLUG}/")"
  check "/event-rental/${SLUG}/ returns ${STATUS}" "$([[ "${STATUS}" == "200" ]] && echo 1 || echo 0)"
done

RENTAL_HTML="$(curl -sL -A 'overworld-deploy-check' "${SITE_URL}/event-rental/")"
check "rental page still renders"         "$(grep -q 'class="ow-rental"' <<<"${RENTAL_HTML}" && echo 1 || echo 0)"
check "rental cards carry tags"           "$(grep -q 'class="ow-ri-tag"' <<<"${RENTAL_HTML}" && echo 1 || echo 0)"
check "rental cards link to item pages"   "$(grep -q '/event-rental/vr-free-roam/' <<<"${RENTAL_HTML}" && echo 1 || echo 0)"
check "rental page still has its FAQ"     "$(grep -q 'ow-rental__faq-item' <<<"${RENTAL_HTML}" && echo 1 || echo 0)"

echo
echo "Done."

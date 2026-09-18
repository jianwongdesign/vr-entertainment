#!/usr/bin/env bash
#
# Deploy the Google Ads landing page and create it as a draft.
#
# Sends only the two files the feature needs. push-wp-content.sh syncs the
# whole of wp-content — around 900 files — which is the wrong tool for a
# two-file change: it is slow and it puts unrelated local state on the live
# site.
#
#   wp-content/themes/hello-elementor-child/page-ads-events.php
#   wp-content/mu-plugins/overworld-ads-landing.php
#
# Then uploads scripts/ads-landing-page.php to a temp path, runs it through
# wp-cli to create /group-events-singapore/ as a DRAFT, removes it again, and
# purges the caches.
#
# Dry run (default) shows what would be sent and changes nothing:
#   ./scripts/deploy-ads-landing.sh
#
# Deploy:
#   CONFIRM_PUSH=overworld.com.sg ./scripts/deploy-ads-landing.sh --apply
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
  "themes/hello-elementor-child/page-ads-events.php"
  "mu-plugins/overworld-ads-landing.php"
)

LOCAL_SOURCE="${PROJECT_ROOT}/${LOCAL_WP_CONTENT}"

for REL in "${FILES[@]}"; do
  if [[ ! -f "${LOCAL_SOURCE}/${REL}" ]]; then
    echo "Missing ${LOCAL_SOURCE}/${REL}" >&2
    exit 1
  fi
done

if [[ "${APPLY}" -eq 0 ]]; then
  echo "Dry run. Nothing is sent and nothing is created."
  echo
  echo "Would send to ${SSH_TARGET}:${REMOTE_WP_PATH}/wp-content/"
  for REL in "${FILES[@]}"; do
    printf '  %s\n' "${REL}"
  done
  echo
  echo "Would then run ads-landing-page.php through wp-cli to create"
  echo "/group-events-singapore/ as a DRAFT, and purge the caches."
  echo
  echo "Re-run with: CONFIRM_PUSH=overworld.com.sg $0 --apply"
  exit 0
fi

if [[ "${CONFIRM_PUSH:-}" != "overworld.com.sg" ]]; then
  echo "Refusing live deploy. Re-run with CONFIRM_PUSH=overworld.com.sg" >&2
  exit 1
fi

# --- 1. the two files ------------------------------------------------------
echo "==> Sending ${#FILES[@]} files"
for REL in "${FILES[@]}"; do
  rsync -vz --relative -e "${RSYNC_SSH}" \
    "${LOCAL_SOURCE}/./${REL}" \
    "${SSH_TARGET}:${REMOTE_WP_PATH}/wp-content/"
done

# --- 2. create the draft ---------------------------------------------------
# Uploaded to a temp path rather than into wp-content: it is a one-shot
# maintenance script, not part of the site, and leaving it under the docroot
# would make it web-reachable.
echo
echo "==> Creating the draft page"
REMOTE_TMP="/tmp/ads-landing-page-$$.php"

scp "${SSH_OPTS[@]}" \
  "${PROJECT_ROOT}/scripts/ads-landing-page.php" \
  "${SSH_TARGET}:${REMOTE_TMP}"

printf -v QUOTED_PATH "%q" "${REMOTE_WP_PATH}"
ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" \
  "cd ${QUOTED_PATH} && wp eval-file ${REMOTE_TMP}; rm -f ${REMOTE_TMP}"

# --- 3. caches -------------------------------------------------------------
echo
echo "==> Purging caches"
ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" \
  "cd ${QUOTED_PATH} && wp cache flush && wp litespeed-purge all" || \
  echo "(cache purge reported an error — check it by hand)"

echo
echo "Done. The page is a DRAFT. Publish it with:"
echo "  ./scripts/remote-wp-cli.sh post update <page-id> --post_status=publish"

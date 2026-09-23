#!/usr/bin/env bash
#
# Deploy the past event gallery.
#
# Sends only the files the feature needs:
#
#   mu-plugins/overworld-event-gallery.php          the Past Events content
#                                                   type, its ACF box, the
#                                                   shared CSS and the SEO
#                                                   wiring
#   mu-plugins/overworld-event-rental.php           the gallery section's own
#                                                   wording on /event-rental/
#   mu-plugins/overworld-seo.php                    two new extension points
#                                                   (ow_seo_generated,
#                                                   ow_seo_archive_seo)
#   themes/hello-elementor-child/page-event-rental.php   the section itself
#   themes/hello-elementor-child/single-past_event.php   one event's page
#   themes/hello-elementor-child/archive-past_event.php  /past-events/
#   themes/hello-elementor-child/parts/past-event-card.php
#
# Then:
#   - flushes the rewrite rules. Registering a content type adds URL rules
#     that WordPress only rebuilds when asked; without this every
#     /past-events/ URL 404s even though the code is right.
#   - creates one example event as a DRAFT the first time (invisible to
#     visitors; it just gives the client a filled-in form to look at).
#   - purges the caches.
#
# Dry run (default) shows what would be sent and changes nothing:
#   ./scripts/deploy-event-gallery.sh
#
# Deploy:
#   CONFIRM_PUSH=overworld.com.sg ./scripts/deploy-event-gallery.sh --apply
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
  "mu-plugins/overworld-event-gallery.php"
  "mu-plugins/overworld-event-rental.php"
  "mu-plugins/overworld-seo.php"
  "themes/hello-elementor-child/page-event-rental.php"
  "themes/hello-elementor-child/single-past_event.php"
  "themes/hello-elementor-child/archive-past_event.php"
  "themes/hello-elementor-child/parts/past-event-card.php"
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
  echo "Would then flush the rewrite rules, create one example Past Event as a"
  echo "DRAFT if none exist, and purge the caches."
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
# parts/ is new on the server; rsync writes the file but will not create a
# missing parent directory.
remote "mkdir -p wp-content/themes/hello-elementor-child/parts"
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
echo "==> Flushing the rewrite rules (/past-events/ URLs depend on this)"
remote "wp rewrite flush --hard"

# --- 3. the example draft ---------------------------------------------------
echo
echo "==> Example Past Event (draft, only if none exist)"
REMOTE_TMP="/tmp/past-event-sample-$$.php"
scp "${SCP_OPTS[@]}" "${SCRIPT_DIR}/past-event-sample.php" "${SSH_TARGET}:${REMOTE_TMP}"
remote "wp eval-file ${REMOTE_TMP}; rm -f ${REMOTE_TMP}"

# --- 4. caches --------------------------------------------------------------
echo
echo "==> Purging caches"
remote "wp cache flush; wp litespeed-purge all" || echo "(cache purge reported an error — check by hand)"
remote "wp elementor flush-css" 2>/dev/null || true

# --- 5. verify --------------------------------------------------------------
echo
echo "==> Verifying"
remote "wp eval '
echo \"    content type : \" . ( post_type_exists( \"past_event\" ) ? \"registered\" : \"NOT REGISTERED\" ) . \"\n\";
echo \"    ACF box      : \" . ( function_exists( \"acf_get_field_group\" ) && acf_get_field_group( \"group_ow_past_event\" ) ? \"registered\" : \"NOT REGISTERED\" ) . \"\n\";
echo \"    listing URL  : \" . get_post_type_archive_link( \"past_event\" ) . \"\n\";
echo \"    published    : \" . count( get_posts( array( \"post_type\" => \"past_event\", \"numberposts\" => -1, \"fields\" => \"ids\" ) ) ) . \"\n\";
echo \"    drafts       : \" . count( get_posts( array( \"post_type\" => \"past_event\", \"post_status\" => \"draft\", \"numberposts\" => -1, \"fields\" => \"ids\" ) ) ) . \"\n\";
echo \"    add new      : \" . admin_url( \"post-new.php?post_type=past_event\" ) . \"\n\";
'"

SITE_URL="https://overworld.com.sg"
check() { if [[ "$2" -eq 1 ]]; then echo "    ok    $1"; else echo "    FAIL  $1"; fi; }

sleep 2
LIST_STATUS="$(curl -sL -A 'overworld-deploy-check' -o /dev/null -w '%{http_code}' "${SITE_URL}/past-events/")"
check "/past-events/ returns ${LIST_STATUS}" "$([[ "${LIST_STATUS}" == "200" ]] && echo 1 || echo 0)"

RENTAL_HTML="$(curl -sL -A 'overworld-deploy-check' "${SITE_URL}/event-rental/")"
check "rental page still renders"           "$(grep -q 'class="ow-rental"' <<<"${RENTAL_HTML}" && echo 1 || echo 0)"
check "rental page still has its FAQ"       "$(grep -q 'ow-rental__faq-item' <<<"${RENTAL_HTML}" && echo 1 || echo 0)"

echo
echo "Done."
echo "The gallery section stays hidden from visitors until the first Past Event is"
echo "published — that is deliberate, not a fault. Anyone logged in sees the empty"
echo "state with a link to Add New."

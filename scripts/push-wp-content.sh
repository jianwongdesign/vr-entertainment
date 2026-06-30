#!/usr/bin/env bash
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

LOCAL_SOURCE="${PROJECT_ROOT}/${LOCAL_WP_CONTENT}"
if [[ ! -d "${LOCAL_SOURCE}" ]]; then
  echo "Missing ${LOCAL_SOURCE}. Run ./scripts/pull-wp-content.sh first." >&2
  exit 1
fi

RSYNC_ARGS=(-avz --progress --exclude-from "${PROJECT_ROOT}/.rsyncignore")

if [[ "${ALLOW_REMOTE_DELETE:-0}" == "1" ]]; then
  RSYNC_ARGS+=(--delete)
fi

if [[ "${APPLY}" -eq 0 ]]; then
  echo "Dry run only. Review this output before deploying."
  RSYNC_ARGS+=(-n)
else
  if [[ "${CONFIRM_PUSH:-}" != "overworld.com.sg" ]]; then
    echo "Refusing live deploy. Re-run with CONFIRM_PUSH=overworld.com.sg" >&2
    exit 1
  fi
fi

rsync "${RSYNC_ARGS[@]}" \
  -e "${RSYNC_SSH}" \
  "${LOCAL_SOURCE}/" \
  "${SSH_TARGET}:${REMOTE_WP_PATH}/wp-content/"

#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=scripts/lib/env.sh
source "${SCRIPT_DIR}/lib/env.sh"

LOCAL_TARGET="${PROJECT_ROOT}/${LOCAL_WP_CONTENT}"
mkdir -p "${LOCAL_TARGET}"

rsync -avz --progress \
  --exclude-from "${PROJECT_ROOT}/.rsyncignore" \
  -e "${RSYNC_SSH}" \
  "${SSH_TARGET}:${REMOTE_WP_PATH}/wp-content/" \
  "${LOCAL_TARGET}/"

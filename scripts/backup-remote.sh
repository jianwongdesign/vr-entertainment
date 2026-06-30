#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=scripts/lib/env.sh
source "${SCRIPT_DIR}/lib/env.sh"

printf -v QUOTED_PATH "%q" "${REMOTE_WP_PATH}"

exec ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" \
  "set -e; stamp=\$(date +%Y%m%d-%H%M%S); backup_dir=\"\$HOME/overworld-backups\"; mkdir -p \"\$backup_dir\"; cd ${QUOTED_PATH}; if command -v wp >/dev/null 2>&1; then wp db export \"\$backup_dir/db-\$stamp.sql\"; else echo 'wp cli not found; skipping database export'; fi; tar -czf \"\$backup_dir/wp-content-\$stamp.tar.gz\" wp-content; echo \"Backup saved in \$backup_dir\""

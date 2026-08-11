#!/usr/bin/env bash
#
# Full backup of the live site: database dump + wp-content archive, both left
# in ~/overworld-backups on the server.
#
# WHY THIS DOES NOT USE `wp db export`
# On this host `wp db export` exits 255 and writes nothing, printing no error
# even with --debug — it dies after "Running command: db export". `wp db size`
# works, so wp-cli and the database connection are both fine; only the export
# path is broken. The previous version of this script piped that failure into
# `set -e` behind an `exec ssh`, so it exited 0 and printed nothing: running it
# looked exactly like a successful backup while producing no file at all. That
# is the worst possible failure mode for a backup script.
#
# So this dumps with mysqldump directly, reading the credentials from
# wp-config.php on the server so they never travel or land in this repo, and
# verifies both artefacts before reporting success.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=scripts/lib/env.sh
source "${SCRIPT_DIR}/lib/env.sh"

printf -v QUOTED_PATH "%q" "${REMOTE_WP_PATH}"

ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" bash -s <<REMOTE
set -euo pipefail

BACKUP_DIR="\$HOME/overworld-backups"
mkdir -p "\$BACKUP_DIR"
cd ${QUOTED_PATH}

STAMP=\$(date +%Y%m%d-%H%M%S)
DB_FILE="\$BACKUP_DIR/db-\$STAMP.sql"
CONTENT_FILE="\$BACKUP_DIR/wp-content-\$STAMP.tar.gz"

echo "==> Database"
DB_NAME=\$(wp config get DB_NAME)
DB_USER=\$(wp config get DB_USER)
DB_HOST=\$(wp config get DB_HOST)

# Credentials go in a 0600 temp file so they never appear in the process list.
CNF=\$(mktemp)
chmod 600 "\$CNF"
trap 'rm -f "\$CNF"' EXIT
printf '[client]\nuser=%s\npassword=%s\nhost=%s\n' \
  "\$DB_USER" "\$(wp config get DB_PASSWORD)" "\$DB_HOST" > "\$CNF"

mysqldump --defaults-extra-file="\$CNF" \
  --single-transaction --quick --no-tablespaces \
  "\$DB_NAME" > "\$DB_FILE"

# mysqldump writes this line last, so its absence means a truncated dump.
if ! tail -5 "\$DB_FILE" | grep -q "Dump completed on"; then
  echo "ERROR: database dump is truncated - no completion marker" >&2
  exit 1
fi
echo "    \$DB_FILE (\$(du -h "\$DB_FILE" | cut -f1), \$(grep -c 'CREATE TABLE' "\$DB_FILE") tables)"

echo "==> wp-content"
# Caches and the migration plugin's own backups are regenerable bulk.
tar -czf "\$CONTENT_FILE" \
  --exclude='wp-content/cache' \
  --exclude='wp-content/litespeed' \
  --exclude='wp-content/ai1wm-backups' \
  wp-content

tar -tzf "\$CONTENT_FILE" > /dev/null
echo "    \$CONTENT_FILE (\$(du -h "\$CONTENT_FILE" | cut -f1), \$(tar -tzf "\$CONTENT_FILE" | wc -l | tr -d ' ') entries)"

echo
echo "Backup complete in \$BACKUP_DIR"
REMOTE

#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=scripts/lib/env.sh
source "${SCRIPT_DIR}/lib/env.sh"

if [[ "$#" -eq 0 ]]; then
  echo "Usage: $0 <wp-cli args>" >&2
  exit 2
fi

printf -v QUOTED_PATH "%q" "${REMOTE_WP_PATH}"

REMOTE_ARGS=""
for ARG in "$@"; do
  printf -v QUOTED_ARG "%q" "${ARG}"
  REMOTE_ARGS+="${QUOTED_ARG} "
done

exec ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" "cd ${QUOTED_PATH} && wp ${REMOTE_ARGS}"

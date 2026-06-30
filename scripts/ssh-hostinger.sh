#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=scripts/lib/env.sh
source "${SCRIPT_DIR}/lib/env.sh"

if [[ "$#" -eq 0 ]]; then
  exec ssh "${SSH_OPTS[@]}" "${SSH_TARGET}"
fi

exec ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" "$@"

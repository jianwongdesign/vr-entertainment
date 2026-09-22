#!/usr/bin/env bash
#
# Open one shared SSH session to the live host, by password, and keep it in
# the background for two hours. Every deploy script then reuses it (see
# scripts/lib/env.sh), so the password is typed exactly once, at this
# prompt, and never stored anywhere.
#
# Use this only while the deploy key is not on the server. Once the key is
# back in hPanel the scripts work without it.
#
# Run it YOURSELF in the terminal (it needs your keyboard for the password):
#   ./scripts/ssh-session-open.sh
#
# Close it early with:
#   ./scripts/ssh-session-open.sh --close
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=scripts/lib/env.sh
source "${SCRIPT_DIR}/lib/env.sh"

if [[ "${1:-}" == "--close" ]]; then
  if [[ -S "${OW_SSH_SOCK}" ]]; then
    ssh -o "ControlPath=${OW_SSH_SOCK}" -O exit "${SSH_TARGET}" && echo "closed"
  else
    echo "no open session"
  fi
  exit 0
fi

if [[ -S "${OW_SSH_SOCK}" ]] && ssh -o "ControlPath=${OW_SSH_SOCK}" -O check "${SSH_TARGET}" 2>/dev/null; then
  echo "a shared session is already open"
  exit 0
fi

echo "Opening a shared session to ${SSH_TARGET} (port ${HOSTINGER_SSH_PORT})."
echo "Enter the Hostinger SSH password when asked. It is used once and not saved."
ssh -M -S "${OW_SSH_SOCK}" \
    -o ControlPersist=2h \
    -o PreferredAuthentications=password \
    -o PubkeyAuthentication=no \
    -p "${HOSTINGER_SSH_PORT}" \
    "${SSH_TARGET}" 'echo CONNECTED as $(whoami) on $(hostname)'

echo
echo "Session open for 2 hours. Deploy scripts will reuse it automatically."

#!/usr/bin/env bash
set -euo pipefail

LIB_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${LIB_DIR}/../.." && pwd)"
ENV_FILE="${ENV_FILE:-"${PROJECT_ROOT}/.env"}"

if [[ ! -f "${ENV_FILE}" ]]; then
  echo "Missing ${ENV_FILE}. Copy .env.example to .env and fill in the Hostinger SSH details." >&2
  exit 1
fi

set -a
# shellcheck disable=SC1090
source "${ENV_FILE}"
set +a

: "${HOSTINGER_SSH_HOST:?Set HOSTINGER_SSH_HOST in .env}"
: "${HOSTINGER_SSH_USER:?Set HOSTINGER_SSH_USER in .env}"
: "${REMOTE_WP_PATH:?Set REMOTE_WP_PATH in .env}"

HOSTINGER_SSH_PORT="${HOSTINGER_SSH_PORT:-65002}"
LOCAL_WP_CONTENT="${LOCAL_WP_CONTENT:-wordpress/wp-content}"
REMOTE_WP_PATH="${REMOTE_WP_PATH%/}"
SSH_TARGET="${HOSTINGER_SSH_USER}@${HOSTINGER_SSH_HOST}"

SSH_OPTS=(-p "${HOSTINGER_SSH_PORT}")
SCP_OPTS=(-P "${HOSTINGER_SSH_PORT}")   # scp spells the port -P; -p means "preserve times"
RSYNC_SSH="ssh -p ${HOSTINGER_SSH_PORT}"

if [[ -n "${SSH_KEY_PATH:-}" ]]; then
  SSH_OPTS+=(-i "${SSH_KEY_PATH}")
  SCP_OPTS+=(-i "${SSH_KEY_PATH}")
  RSYNC_SSH="${RSYNC_SSH} -i ${SSH_KEY_PATH}"
fi

# Shared SSH session. When the deploy key is not accepted by the server,
# open ONE password login by hand (scripts/ssh-session-open.sh — the
# password is typed at the terminal, never stored) and every script here
# rides on that connection through its control socket instead of prompting
# again for each rsync / scp / ssh call.
OW_SSH_SOCK="${OW_SSH_SOCK:-${HOME}/.ssh/ow-hostinger.sock}"
if [[ -S "${OW_SSH_SOCK}" ]]; then
  SSH_OPTS+=(-o "ControlPath=${OW_SSH_SOCK}")
  SCP_OPTS+=(-o "ControlPath=${OW_SSH_SOCK}")
  RSYNC_SSH="${RSYNC_SSH} -o ControlPath=${OW_SSH_SOCK}"
fi

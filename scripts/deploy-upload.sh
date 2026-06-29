#!/usr/bin/env bash
# Uploads the deployment payload to the remote server via lftp.
# Required environment variables:
#   FTP_HOST, FTP_USER, FTP_PASSWORD, FTP_PROTOCOL, FTP_PATH
# Optional environment variables:
#   FTP_PORT
set -Eeuo pipefail

ALLOWED_PROTOCOLS="sftp ftps ftp"

# ── Validate required variables ────────────────────────────────
for var in FTP_HOST FTP_USER FTP_PASSWORD FTP_PATH FTP_PROTOCOL; do
  if [ -z "${!var:-}" ]; then
    echo "ERROR: Required variable '${var}' is not set or empty." >&2
    exit 1
  fi
done

# ── Validate protocol ──────────────────────────────────────────
valid=0
for p in ${ALLOWED_PROTOCOLS}; do
  [ "${FTP_PROTOCOL}" = "${p}" ] && valid=1 && break
done

if [ "${valid}" -eq 0 ]; then
  echo "ERROR: Unsupported protocol '${FTP_PROTOCOL}'. Allowed values: ${ALLOWED_PROTOCOLS}." >&2
  exit 1
fi

case "${FTP_PROTOCOL}" in
  sftp)
    DEFAULT_PORT=22
    PROTOCOL_SETTINGS='set sftp:auto-confirm yes;'
    ;;
  ftps)
    DEFAULT_PORT=21
    PROTOCOL_SETTINGS='set ftp:ssl-allow yes; set ssl:check-hostname yes;'
    ;;
  ftp)
    DEFAULT_PORT=21
    PROTOCOL_SETTINGS='set ftp:ssl-allow no;'
    ;;
esac

FTP_PORT="${FTP_PORT:-${DEFAULT_PORT}}"

echo "==> Uploading to ${FTP_PROTOCOL}://${FTP_HOST}:${FTP_PORT}${FTP_PATH}"

if [ "${FTP_PROTOCOL}" = "sftp" ] && [ "${FTP_PORT}" != "22" ]; then
  echo "WARN: SFTP usually uses port 22. Current port is '${FTP_PORT}'." >&2
fi

lftp -u "${FTP_USER}","${FTP_PASSWORD}" "${FTP_PROTOCOL}://${FTP_HOST}:${FTP_PORT}" -e \
  "set cmd:fail-exit yes; \
   ${PROTOCOL_SETTINGS} \
   set net:max-retries 1; \
   set net:timeout 30; \
   set net:reconnect-interval-base 5; \
   mkdir -p \"${FTP_PATH}\"; \
   cls -1 \"${FTP_PATH}\"; \
   mirror -R ./ \"${FTP_PATH}\" --verbose --parallel=2 \
     --exclude-glob .git* \
     --exclude-glob .github \
     --exclude-glob node_modules \
     --exclude-glob tests \
     --exclude-glob .env \
     --exclude-glob .env.* \
     --exclude-glob phpunit.xml \
     --exclude-glob vite.config.js; \
   bye"

echo "==> Upload complete"

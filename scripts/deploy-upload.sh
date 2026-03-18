#!/usr/bin/env bash
# Uploads the deployment payload to the remote server via lftp.
# Required environment variables:
#   FTP_HOST, FTP_USER, FTP_PASSWORD, FTP_PORT, FTP_PROTOCOL, FTP_PATH
set -Eeuo pipefail

ALLOWED_PROTOCOLS="sftp ftps ftp"

# ── Validate required variables ────────────────────────────────
for var in FTP_HOST FTP_USER FTP_PASSWORD FTP_PATH FTP_PROTOCOL FTP_PORT; do
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

echo "==> Uploading to ${FTP_PROTOCOL}://${FTP_HOST}:${FTP_PORT}${FTP_PATH}"

lftp -u "${FTP_USER}","${FTP_PASSWORD}" "${FTP_PROTOCOL}://${FTP_HOST}:${FTP_PORT}" -e \
  "set ${FTP_PROTOCOL}:auto-confirm yes; \
   set net:max-retries 2; \
   set net:timeout 20; \
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

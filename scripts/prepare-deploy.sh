#!/usr/bin/env bash
set -Eeuo pipefail

# Prepares the workspace for FTP deployment by removing files
# that should not be uploaded to the server.

echo "==> Removing development & CI-only files before FTP upload"

# Remove directories not needed on the server
rm -rf node_modules
rm -rf tests
rm -rf .github
rm -rf storage/logs/*.log

# Remove dev-only config files
rm -f .env .env.* .editorconfig .gitattributes .gitignore
rm -f phpunit.xml vite.config.js package.json package-lock.json
rm -f composer.lock

echo "==> Deployment payload ready"

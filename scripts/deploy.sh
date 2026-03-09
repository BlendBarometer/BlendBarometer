#!/usr/bin/env bash
set -Eeuo pipefail

# Production deployment script for BlendBarometer (Laravel)
# Usage:
#   BRANCH=main APP_PATH=/var/www/blendbarometer bash scripts/deploy.sh

APP_PATH="${APP_PATH:-$(pwd)}"
BRANCH="${BRANCH:-main}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"
SKIP_NPM_BUILD="${SKIP_NPM_BUILD:-0}"
SKIP_MIGRATIONS="${SKIP_MIGRATIONS:-0}"

rollback_maintenance_mode() {
  if [[ "${MAINTENANCE_ENABLED:-0}" == "1" ]]; then
    "${PHP_BIN}" artisan up || true
  fi
}
trap rollback_maintenance_mode EXIT

cd "${APP_PATH}"

if [[ ! -f artisan ]]; then
  echo "Error: artisan not found in ${APP_PATH}. Set APP_PATH correctly."
  exit 1
fi

echo "==> Deploying branch ${BRANCH} in ${APP_PATH}"

echo "==> Putting app in maintenance mode"
"${PHP_BIN}" artisan down --render="errors::503" --retry=60 || true
MAINTENANCE_ENABLED=1

echo "==> Syncing code"
git fetch --prune origin
git checkout "${BRANCH}"
git pull --ff-only origin "${BRANCH}"

echo "==> Installing PHP dependencies"
"${COMPOSER_BIN}" install --no-dev --prefer-dist --no-interaction --optimize-autoloader

echo "==> Installing Node dependencies and building assets"
if [[ "${SKIP_NPM_BUILD}" == "1" ]]; then
  echo "Skipping npm build because SKIP_NPM_BUILD=1"
else
  "${NPM_BIN}" ci
  "${NPM_BIN}" run build
fi

echo "==> Running database migrations"
if [[ "${SKIP_MIGRATIONS}" == "1" ]]; then
  echo "Skipping migrations because SKIP_MIGRATIONS=1"
else
  "${PHP_BIN}" artisan migrate --force
fi

echo "==> Caching Laravel config"
"${PHP_BIN}" artisan optimize:clear
"${PHP_BIN}" artisan config:cache
"${PHP_BIN}" artisan route:cache
"${PHP_BIN}" artisan view:cache
"${PHP_BIN}" artisan event:cache

echo "==> Ensuring storage symlink exists"
"${PHP_BIN}" artisan storage:link || true

echo "==> Restarting queue workers (if any)"
"${PHP_BIN}" artisan queue:restart || true

echo "==> Bringing app back online"
"${PHP_BIN}" artisan up
MAINTENANCE_ENABLED=0

echo "==> Deployment finished successfully"

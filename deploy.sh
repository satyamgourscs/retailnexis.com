#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR"

echo "==> Composer (no dev)"
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

echo "==> NPM assets (Vite + Mix)"
if [[ -f package-lock.json ]]; then
  npm ci
else
  npm install
fi
npm run build
npm run build:mix

echo "==> Laravel"
php artisan storage:link --force || true
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force

echo "==> Done. Ensure .env has APP_ENV=production APP_DEBUG=false QUEUE_CONNECTION=sync (or run a worker)."

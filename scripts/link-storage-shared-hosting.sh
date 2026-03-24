#!/usr/bin/env bash
# Hostinger often disables PHP symlink() — use shell ln -s instead of php artisan storage:link
#
# Usage:
#   bash scripts/link-storage-shared-hosting.sh [PUBLIC_HTML] [LARAVEL_ROOT]
#
# Example:
#   bash scripts/link-storage-shared-hosting.sh \
#     "$HOME/domains/retailnexis.com/public_html" \
#     "$HOME/laravel_app"

set -euo pipefail

PUBLIC_HTML="${1:-$HOME/public_html}"
LARAVEL_ROOT="${2:-$HOME/laravel_app}"

TARGET="$LARAVEL_ROOT/storage/app/public"
LINK="$PUBLIC_HTML/storage"

if [[ ! -d "$TARGET" ]]; then
  echo "Missing: $TARGET — create storage/app/public first"
  exit 1
fi

rm -rf "$LINK" 2>/dev/null || true
ln -sfn "$TARGET" "$LINK"
echo "OK: $LINK -> $TARGET"
ls -la "$LINK"

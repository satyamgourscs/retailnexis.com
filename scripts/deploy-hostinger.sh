#!/usr/bin/env bash
#
# Hostinger — automated Laravel deploy (run on the SERVER via SSH).
# Zero remote execution from GitHub/Cursor: you must run this script on Hostinger or via CI SSH.
#
# Usage:
#   export DEPLOY_DOMAIN="https://yourdomain.com"
#   bash scripts/deploy-hostinger.sh
#
# Optional env:
#   LARAVEL_ROOT=/home/user/laravel_app
#   PUBLIC_HTML=/home/user/public_html
#   GIT_REPO=git@github.com:satyamgourscs/retailnexis.com.git
#   SKIP_NPM=1
#   SKIP_ROUTE_CACHE=1
#   RUN_TENANT_MIGRATE=1
#   RSYNC_DELETE=1   # add --delete to rsync public → public_html (destructive)
#

set -euo pipefail
umask 022

START_TS="$(date -u +%Y-%m-%dT%H:%M:%SZ)"
SAFE_TS="${START_TS//:/-}"

LARAVEL_ROOT="${LARAVEL_ROOT:-$HOME/laravel_app}"
PUBLIC_HTML="${PUBLIC_HTML:-$HOME/public_html}"
GIT_REPO="${GIT_REPO:-git@github.com:satyamgourscs/retailnexis.com.git}"
DEPLOY_DOMAIN="${DEPLOY_DOMAIN:-}"

SKIP_NPM="${SKIP_NPM:-0}"
SKIP_ROUTE_CACHE="${SKIP_ROUTE_CACHE:-0}"
RUN_TENANT_MIGRATE="${RUN_TENANT_MIGRATE:-0}"
RSYNC_DELETE="${RSYNC_DELETE:-0}"

WARNINGS=()
ERRORS=()
STEPS=()

REPORT_DIR="$HOME/laravel_deploy_reports"
mkdir -p "$REPORT_DIR"
LOG_FILE="${REPORT_DIR}/deploy-${SAFE_TS}.log"
REPORT_MD="${REPORT_DIR}/deploy-${SAFE_TS}.report.md"

log() {
  local line="[$(date -u +%Y-%m-%dT%H:%M:%SZ)] $*"
  echo "$line" | tee -a "$LOG_FILE"
}

step() {
  STEPS+=("$1")
  log "STEP: $1"
}

warn() {
  WARNINGS+=("$1")
  log "WARN: $1"
}

fail() {
  ERRORS+=("$1")
  log "ERROR: $1"
}

write_report() {
  local status=$1
  local end_ts
  end_ts="$(date -u +%Y-%m-%dT%H:%M:%SZ)"
  {
    echo "# Deployment report"
    echo ""
    echo "- **Started:** $START_TS"
    echo "- **Finished:** $end_ts"
    echo "- **Exit code:** $status"
    echo "- **Laravel root:** \`$LARAVEL_ROOT\`"
    echo "- **Public HTML:** \`$PUBLIC_HTML\`"
    echo ""
    echo "## Steps"
    local i=1
    for s in "${STEPS[@]}"; do
      echo "$i. $s"
      i=$((i + 1))
    done
    echo ""
    echo "## Warnings"
    if [[ ${#WARNINGS[@]} -eq 0 ]]; then
      echo "(none)"
    else
      for w in "${WARNINGS[@]}"; do echo "- $w"; done
    fi
    echo ""
    echo "## Errors"
    if [[ ${#ERRORS[@]} -eq 0 ]]; then
      echo "(none)"
    else
      for e in "${ERRORS[@]}"; do echo "- $e"; done
    fi
    echo ""
    echo "## Log file"
    echo "\`$LOG_FILE\`"
  } > "$REPORT_MD"
}

on_exit() {
  local status=$?
  trap - EXIT
  write_report "$status" 2>/dev/null || true
  echo "[deploy] Report: $REPORT_MD" >> "$LOG_FILE" 2>/dev/null || true
  echo "[deploy] Log:   $LOG_FILE" >> "$LOG_FILE" 2>/dev/null || true
  exit "$status"
}
trap on_exit EXIT

touch "$LOG_FILE"
log "=== deploy-hostinger.sh start ==="

# --- 1) Clone or update ---
step "Repository: clone or pull"
if [[ -d "$LARAVEL_ROOT/.git" ]]; then
  git -C "$LARAVEL_ROOT" pull --ff-only 2>&1 | tee -a "$LOG_FILE" || true
else
  mkdir -p "$(dirname "$LARAVEL_ROOT")"
  git clone --depth 1 "$GIT_REPO" "$LARAVEL_ROOT" 2>&1 | tee -a "$LOG_FILE"
fi

step "Remove unnecessary paths (tests, zip)"
rm -rf "$LARAVEL_ROOT/tests" 2>/dev/null || true
find "$LARAVEL_ROOT" -maxdepth 4 -type f -name '*.zip' ! -path '*/vendor/*' -delete 2>/dev/null || true

# --- 2) .env & APP_KEY ---
step "Environment file"
cd "$LARAVEL_ROOT"
if [[ ! -f .env ]]; then
  if [[ -f .env.example ]]; then
    cp .env.example .env
    warn ".env created from .env.example — set DB_*, APP_URL, APP_DEBUG=false"
  else
    fail "Missing .env and .env.example"
    exit 1
  fi
fi

if ! grep -qE '^APP_KEY=(base64:.+|[^[:space:]]+)' .env 2>/dev/null; then
  step "Generate APP_KEY"
  php artisan key:generate --force 2>&1 | tee -a "$LOG_FILE" || fail "key:generate failed"
fi

# --- 3) Composer ---
step "Composer install"
if command -v composer >/dev/null 2>&1; then
  composer install --optimize-autoloader --no-dev --no-interaction 2>&1 | tee -a "$LOG_FILE" || fail "composer install failed"
else
  warn "composer not in PATH — upload vendor/ or install Composer"
  [[ -f vendor/autoload.php ]] || { fail "vendor/autoload.php missing"; exit 1; }
fi

# --- 4) NPM ---
if [[ "$SKIP_NPM" != "1" ]] && [[ -f package.json ]] && command -v npm >/dev/null 2>&1; then
  step "npm install && npm run prod"
  ( npm ci --no-audit 2>&1 || npm install --no-audit 2>&1 ) | tee -a "$LOG_FILE" || warn "npm install issues"
  npm run prod 2>&1 | tee -a "$LOG_FILE" || warn "npm run prod failed"
elif [[ "$SKIP_NPM" != "1" ]] && [[ -f package.json ]]; then
  warn "npm not found — skipped frontend build"
fi

# --- 5) public_html ---
step "Sync public/ to public_html"
mkdir -p "$PUBLIC_HTML"
RSYNC_OPTS=(-a)
[[ "$RSYNC_DELETE" == "1" ]] && RSYNC_OPTS+=(--delete)
rsync "${RSYNC_OPTS[@]}" --exclude 'index.php' \
  "$LARAVEL_ROOT/public/" "$PUBLIC_HTML/" 2>&1 | tee -a "$LOG_FILE"

if [[ -f "$LARAVEL_ROOT/public/index.shared-hosting.php" ]]; then
  cp "$LARAVEL_ROOT/public/index.shared-hosting.php" "$PUBLIC_HTML/index.php"
else
  cp "$LARAVEL_ROOT/public/index.php" "$PUBLIC_HTML/index.php"
  warn "Using stock index.php — prefer public/index.shared-hosting.php for shared hosting"
fi

NAME="$(basename "$LARAVEL_ROOT")"
if [[ "$NAME" != "laravel_app" ]]; then
  sed -i "s|'/laravel_app'|'/${NAME}'|g" "$PUBLIC_HTML/index.php" 2>/dev/null || true
fi

[[ -f "$LARAVEL_ROOT/public/.htaccess" ]] && cp "$LARAVEL_ROOT/public/.htaccess" "$PUBLIC_HTML/.htaccess"

# --- 6) Permissions ---
step "Permissions storage bootstrap/cache"
chmod -R u+rwX "$LARAVEL_ROOT/storage" "$LARAVEL_ROOT/bootstrap/cache" 2>/dev/null || true
find "$LARAVEL_ROOT/storage" "$LARAVEL_ROOT/bootstrap/cache" -type d -exec chmod 775 {} \; 2>/dev/null || true
find "$LARAVEL_ROOT/storage" "$LARAVEL_ROOT/bootstrap/cache" -type f -exec chmod 664 {} \; 2>/dev/null || true

# --- 7) Migrations ---
step "Migrate"
cd "$LARAVEL_ROOT"
set +e
php artisan migrate --force 2>&1 | tee -a "$LOG_FILE"
MIGRATE_RC=$?
set -e
[[ $MIGRATE_RC -eq 0 ]] || warn "migrate exited $MIGRATE_RC — verify DB exists in hPanel and .env credentials"

if [[ "$RUN_TENANT_MIGRATE" == "1" ]]; then
  step "tenants:migrate"
  php artisan tenants:migrate --force 2>&1 | tee -a "$LOG_FILE" || warn "tenants:migrate failed"
fi

# --- 8) Optimize (clear stale, then cache; do not clear after cache) ---
step "Optimize"
php artisan optimize:clear 2>&1 | tee -a "$LOG_FILE" || true
php artisan config:cache 2>&1 | tee -a "$LOG_FILE" || warn "config:cache failed"
if [[ "$SKIP_ROUTE_CACHE" != "1" ]]; then
  php artisan route:cache 2>&1 | tee -a "$LOG_FILE" || warn "route:cache failed — retry with SKIP_ROUTE_CACHE=1"
else
  php artisan route:clear 2>&1 | tee -a "$LOG_FILE" || true
fi
php artisan view:cache 2>&1 | tee -a "$LOG_FILE" || warn "view:cache failed"

# --- 9) storage:link ---
step "storage:link"
php artisan storage:link 2>&1 | tee -a "$LOG_FILE" || warn "storage:link (may already exist)"

# --- 10) HTTP ---
step "HTTP verification"
if [[ -n "$DEPLOY_DOMAIN" ]]; then
  CODE="$(curl -s -o /dev/null -w '%{http_code}' -L --max-time 25 -k "$DEPLOY_DOMAIN" || echo "000")"
  log "curl $DEPLOY_DOMAIN → HTTP $CODE"
  [[ "$CODE" =~ ^[23][0-9][0-9]$ ]] || warn "Unexpected HTTP $CODE — check APP_URL, SSL, storage/logs/laravel.log"
else
  warn "DEPLOY_DOMAIN not set — skipped HTTP check"
fi

log "=== deploy-hostinger.sh finished ==="
exit 0

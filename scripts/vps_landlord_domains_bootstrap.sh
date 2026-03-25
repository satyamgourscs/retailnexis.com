#!/usr/bin/env bash
#
# Landlord bootstrap: DB + minimal tenants/domains if missing, seed test rows, artisan cache clear, verify.
# VPS example:
#   FORCE_LANDLORD_SCHEMA=saleprosaas_landlord bash /var/www/retailnexis/scripts/vps_landlord_domains_bootstrap.sh /var/www/retailnexis
#
set -euo pipefail

APP_ROOT="${1:-${APP_ROOT:-$(pwd)}}"
cd "$APP_ROOT" || { echo "ERROR: cannot cd to $APP_ROOT" >&2; exit 1; }

if ! command -v php >/dev/null 2>&1; then
  echo "ERROR: php CLI not found" >&2
  exit 1
fi
if ! command -v mysql >/dev/null 2>&1; then
  echo "ERROR: mysql client not found" >&2
  exit 1
fi

eval "$(
php <<'PHP' "$APP_ROOT"
<?php
$root = $argv[1] ?? getcwd();
$path = $root . '/.env';
if (! is_readable($path)) {
    fwrite(STDERR, "ERROR: cannot read {$path}\n");
    exit(1);
}
$keys = ['DB_HOST', 'DB_PORT', 'DB_USERNAME', 'DB_PASSWORD', 'LANDLORD_DB', 'DB_DATABASE'];
$out = [];
foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }
    if (! str_contains($line, '=')) {
        continue;
    }
    [$k, $v] = explode('=', $line, 2);
    $k = trim($k);
    if (! in_array($k, $keys, true)) {
        continue;
    }
    $v = trim($v);
    if (str_starts_with($v, '"') && str_ends_with($v, '"') && strlen($v) >= 2) {
        $v = stripcslashes(substr($v, 1, -1));
    } elseif (str_starts_with($v, "'") && str_ends_with($v, "'") && strlen($v) >= 2) {
        $v = stripcslashes(substr($v, 1, -1));
    }
    $out[$k] = $v;
}
$host = $out['DB_HOST'] ?? '127.0.0.1';
$port = $out['DB_PORT'] ?? '3306';
$user = $out['DB_USERNAME'] ?? 'root';
$pass = $out['DB_PASSWORD'] ?? '';
$schema = $out['LANDLORD_DB'] ?? '';
if ($schema === '') {
    $schema = $out['DB_DATABASE'] ?? '';
}
if ($schema === '') {
    $schema = 'saleprosaas_landlord';
}
foreach (['MYSQLHOST' => $host, 'MYSQLPORT' => $port, 'MYSQLUSER' => $user, 'MYSQLPASS' => $pass, 'LANDLORD_SCHEMA' => $schema] as $name => $val) {
    echo 'export ' . $name . '=' . escapeshellarg((string) $val) . PHP_EOL;
}
PHP
)"

if [[ -n "${FORCE_LANDLORD_SCHEMA:-}" ]]; then
  export LANDLORD_SCHEMA="$FORCE_LANDLORD_SCHEMA"
fi

mysql_base=(mysql -h"$MYSQLHOST" -P"$MYSQLPORT" -u"$MYSQLUSER" --default-character-set=utf8mb4)
if [[ -n "${MYSQLPASS}" ]]; then
  mysql_base+=(-p"${MYSQLPASS}")
fi

mysql_1() {
  "${mysql_base[@]}" -N -e "$1"
}

echo "==> Landlord schema: ${LANDLORD_SCHEMA} (host=${MYSQLHOST})"

echo "==> CHECK database ${LANDLORD_SCHEMA}"
DB_EXISTS="$(mysql_1 "SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='${LANDLORD_SCHEMA}';")"
if [[ "${DB_EXISTS}" != "1" ]]; then
  echo "==> CREATE DATABASE ${LANDLORD_SCHEMA} CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci"
  "${mysql_base[@]}" <<SQL
CREATE DATABASE \`${LANDLORD_SCHEMA}\`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
SQL
else
  echo "    Database already exists (ok)"
fi

echo "==> CHECK tenants"
TENANTS_EXISTS="$(mysql_1 "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${LANDLORD_SCHEMA}' AND table_name='tenants';")"
if [[ "${TENANTS_EXISTS}" != "1" ]]; then
  echo "==> CREATE TABLE tenants (id VARCHAR(255) PRIMARY KEY, data JSON)"
  "${mysql_base[@]}" <<SQL
USE \`${LANDLORD_SCHEMA}\`;
CREATE TABLE \`tenants\` (
  \`id\` VARCHAR(255) NOT NULL,
  \`data\` JSON NULL,
  PRIMARY KEY (\`id\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
else
  echo "    tenants already exists (ok)"
fi

echo "==> CHECK domains"
DOMAINS_EXISTS="$(mysql_1 "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${LANDLORD_SCHEMA}' AND table_name='domains';")"
if [[ "${DOMAINS_EXISTS}" != "1" ]]; then
  echo "==> CREATE TABLE domains (...)"
  "${mysql_base[@]}" <<SQL
USE \`${LANDLORD_SCHEMA}\`;
CREATE TABLE \`domains\` (
  \`id\` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  \`domain\` VARCHAR(255) NOT NULL,
  \`tenant_id\` VARCHAR(255) NOT NULL,
  \`created_at\` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  \`updated_at\` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`),
  UNIQUE KEY \`domains_domain_unique\` (\`domain\`),
  CONSTRAINT \`domains_tenant_id_foreign\` FOREIGN KEY (\`tenant_id\`) REFERENCES \`tenants\` (\`id\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
else
  echo "    domains already exists (ok)"
fi

echo "==> INSERT test tenant + domain (idempotent)"
"${mysql_base[@]}" <<SQL
USE \`${LANDLORD_SCHEMA}\`;

INSERT INTO \`tenants\` (\`id\`, \`data\`) VALUES ('test', JSON_OBJECT())
ON DUPLICATE KEY UPDATE \`data\` = JSON_OBJECT();

INSERT INTO \`domains\` (\`domain\`, \`tenant_id\`) VALUES ('test.retailnexis.com', 'test')
ON DUPLICATE KEY UPDATE \`tenant_id\` = 'test';
SQL

echo "==> CLEAR Laravel cache"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "==> VERIFY SELECT * FROM domains"
"${mysql_base[@]}" -e "USE \`${LANDLORD_SCHEMA}\`; SELECT * FROM \`domains\`\\G"

echo "==> VERIFY Tenant::all() (count via artisan)"
php artisan tinker --execute='echo "tenants=".(\App\Models\landlord\Tenant::query()->count()).PHP_EOL;'
echo "    Open /superadmin/clients — uses Tenant::all(); ensure DB_CONNECTION / landlord config points at ${LANDLORD_SCHEMA}."

#!/usr/bin/env bash
# Landlord / domains + test seed + artisan cache clear.
# Same as vps_landlord_domains_bootstrap.sh but stable path for deploy snippets.
# Usage (from anywhere):
#   FORCE_LANDLORD_SCHEMA=saleprosaas_landlord bash /var/www/retailnexis/scripts/fix_clients.sh
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
exec bash "$(dirname "$0")/vps_landlord_domains_bootstrap.sh" "$ROOT"

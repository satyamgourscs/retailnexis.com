# Automated deployment (Hostinger)

## Important limitation

**Fully automated deployment with zero manual steps is not possible** without:

- **SSH access** to Hostinger (or another runner that can reach your server), and  
- A **database already created** in hPanel (MySQL cannot be created reliably from a generic bash script without hPanel API credentials or `mysql` admin rights).

This repository includes **automation you run** on the server or from CI:

| Artifact | Purpose |
|----------|---------|
| `scripts/deploy-hostinger.sh` | Clone/pull, composer, optional npm, sync `public` → `public_html`, permissions, migrate, caches, `storage:link`, optional `curl` check |
| `public/index.shared-hosting.php` | Correct `index.php` when the app lives outside `public_html` |
| `.github/workflows/deploy-hostinger.yml.example` | Template for GitHub Actions + SSH deploy |

## What the script does (structured log)

1. Writes a **timestamped log** under `$HOME/laravel_deploy_reports/deploy-*.log`.
2. Writes a **Markdown report** `deploy-*.report.md` next to it (steps, warnings, errors).
3. **Does not** create MySQL databases (do that in hPanel → **Databases**).
4. **Does not** enable SSL (use hPanel **SSL** or **Force HTTPS**).
5. Uses `optimize:clear` **before** `config:cache` / `route:cache` / `view:cache` (never clears after caching).

## One-time manual steps on Hostinger

1. Create **MySQL database + user**; assign user to DB with ALL privileges.
2. Put credentials in `laravel_app/.env` (`DB_*`).
3. Ensure **document root** is `public_html` and app path matches `index.php` (`laravel_app` sibling or adjust `$laravelRoot` in `index.shared-hosting.php`).
4. Enable **Let’s Encrypt** SSL if needed.

## Run deploy (SSH)

```bash
chmod +x scripts/deploy-hostinger.sh
export DEPLOY_DOMAIN="https://yourdomain.com"
export SKIP_ROUTE_CACHE=1    # often safer for Stancl multi-tenant
# export RUN_TENANT_MIGRATE=1
bash scripts/deploy-hostinger.sh
```

## Environment variables

| Variable | Default | Meaning |
|----------|---------|---------|
| `LARAVEL_ROOT` | `$HOME/laravel_app` | Full Laravel path |
| `PUBLIC_HTML` | `$HOME/public_html` | Web root |
| `GIT_REPO` | `git@github.com:satyamgourscs/retailnexis.com.git` | Clone URL |
| `DEPLOY_DOMAIN` | (empty) | URL for final `curl` check |
| `SKIP_NPM` | `0` | Set `1` if Node not installed on server |
| `SKIP_ROUTE_CACHE` | `0` | Set `1` if `route:cache` breaks tenancy |
| `RUN_TENANT_MIGRATE` | `0` | Set `1` to run `tenants:migrate --force` |
| `RSYNC_DELETE` | `0` | Set `1` to `rsync --delete` (removes extra files in `public_html`) |

## GitHub Actions

1. Copy `.github/workflows/deploy-hostinger.yml.example` to `deploy-hostinger.yml`.
2. Add secrets: `HOSTINGER_SSH_HOST`, `HOSTINGER_SSH_USER`, `HOSTINGER_SSH_KEY`, optional `DEPLOY_DOMAIN`.
3. On the server, **clone once** and configure `.env`; future runs can `git pull` + `deploy-hostinger.sh`.

## Verification

- Script logs HTTP status from `curl` to `DEPLOY_DOMAIN`.
- For Laravel errors, always check `laravel_app/storage/logs/laravel.log` on the server.

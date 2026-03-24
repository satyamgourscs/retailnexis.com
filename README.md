# retailnexis.com

Laravel multi-tenant SaaS (Retail / POS) application.

## Requirements

- PHP 8.x, Composer
- MySQL
- Node / npm (if building front-end assets)

## Setup (local)

1. Copy `.env.example` to `.env` and configure database and app URL.
2. `composer install`
3. `php artisan key:generate`
4. `php artisan migrate` (and tenant migrations as documented for your install)
5. `php artisan storage:link` if needed

## Production (Hostinger / shared hosting)

1. Clone outside `public_html` (e.g. `~/laravel_app`), set `.env` (`APP_ENV=production`, `APP_DEBUG=false`, `DB_*`, `APP_URL`).
2. Run `bash scripts/deploy-hostinger.sh` (see `docs/AUTOMATED_DEPLOY.md`) or follow `docs/HOSTINGER_DEPLOYMENT.md`.
3. Web root must use `public/index.shared-hosting.php` as `public_html/index.php` when the app lives beside `public_html`.

## Security

Do not commit `.env` or production secrets. This repository is configured to ignore them via `.gitignore`.

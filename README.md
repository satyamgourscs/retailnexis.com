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

## Security

Do not commit `.env` or production secrets. This repository is configured to ignore them via `.gitignore`.

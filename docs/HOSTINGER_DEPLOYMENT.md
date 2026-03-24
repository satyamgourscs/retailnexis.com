# Hostinger (hPanel) — Laravel SaaS production deployment

This project is a **Laravel multi-tenant SaaS** (Stancl Tenancy). Use SSH if Hostinger provides it (Business/Cloud plans often do). Without SSH, use **File Manager + Composer locally** (build `vendor` on your PC, upload).

**Repository:** `git@github.com:satyamgourscs/retailnexis.com.git`

---

## Target layout

```
/home/USERNAME/
├── laravel_app/                 ← Full clone (NOT web-accessible)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/                  ← Do NOT serve this URL directly in production
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/                  ← From composer install OR upload
│   ├── .env
│   └── artisan
└── public_html/                 ← Document root (only “public” assets + index)
    ├── .htaccess
    ├── index.php                ← Use template from repo: public/index.shared-hosting.php
    ├── css/, js/, images/…     ← Copy from laravel_app/public/
```

---

## Step-by-step log (actions to perform on the server)

### 1) Clone outside `public_html`

SSH:

```bash
cd ~
git clone git@github.com:satyamgourscs/retailnexis.com.git laravel_app
cd laravel_app
```

No Git on hosting: zip the repo locally, upload and unzip into `~/laravel_app`.

---

### 2) Environment file

```bash
cd ~/laravel_app
cp .env.example .env
php artisan key:generate
```

Edit `.env` (hPanel **MySQL** credentials from **Databases**):

| Variable | Production value |
|----------|------------------|
| `APP_NAME` | Your app name |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://yourdomain.com` |
| `DB_*` | Hostinger MySQL host (often `localhost` or `mysql.hostinger.com`), DB name, user, password |
| `CACHE_DRIVER` | `file` (default on shared hosting) |
| `SESSION_DRIVER` | `file` |
| `QUEUE_CONNECTION` | `sync` (or database + cron if you configure queues) |

**Never commit `.env`** — it stays only on the server.

---

### 3) Composer dependencies

**With SSH + Composer:**

```bash
cd ~/laravel_app
composer install --no-dev --optimize-autoloader
```

**Without SSH:** run the same on your PC, then upload the **`vendor/`** folder into `laravel_app/vendor/` (large upload).

---

### 4) Point `public_html` to Laravel (do not replace unrelated files blindly)

- Copy **contents** of `laravel_app/public/` into `public_html/` (merge).
- Replace `public_html/index.php` with the contents of **`public/index.shared-hosting.php`** from this repo (rename logic: it must live as `public_html/index.php`).
- If `$laravelRoot` does not match `../laravel_app`, edit the `$laravelRoot` line in `index.php` to your real path.

---

### 5) Permissions

SSH:

```bash
cd ~/laravel_app
chmod -R u+rwX storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

If the host runs PHP as a specific user, ensure that user can write `storage` and `bootstrap/cache`.

---

### 6) Storage symlink

SSH from `laravel_app`:

```bash
php artisan storage:link
```

If `public_html` is separate from `laravel_app/public`, link into **`public_html`**:

```bash
ln -s ~/laravel_app/storage/app/public ~/public_html/storage
```

(Adjust paths to match your panel.)

---

### 7) Database

1. Create database + user in hPanel → **MySQL Databases**.
2. Put credentials in `.env`.
3. Import SQL only if you have a **known-good dump** for this app; otherwise:

```bash
cd ~/laravel_app
php artisan migrate --force
```

**Tenancy (this SaaS):** after landlord DB is configured, run tenant migrations as documented for your install, e.g.:

```bash
php artisan tenants:migrate --force
```

Run **once** when the landlord/tenant DBs exist.

---

### 8) Optimize (after `.env` is correct)

```bash
cd ~/laravel_app
php artisan config:cache
php artisan view:cache
```

**Route cache:** `php artisan route:cache` can break some multi-tenant or module setups. Run it only if the site works without it; if you get 404/odd routes, run:

```bash
php artisan route:clear
```

---

### 9) `public_html/.htaccess`

Keep Laravel’s default rewrite (from `public/.htaccess`). Optional **HTTPS** (after SSL is active in hPanel):

```apache
# Optional: force HTTPS (uncomment when SSL works)
# RewriteCond %{HTTPS} !=on
# RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Or enable **“Force HTTPS”** in hPanel if available instead of editing `.htaccess`.

---

### 10) Trust proxies (HTTPS / Hostinger)

If `URL::asset()` or redirects use `http://` behind SSL, set in `AppServiceProvider` or ensure `TrustProxies` middleware trusts `*` or Hostinger’s proxy headers (Laravel 10+ often uses `$middleware->trustProxies(...)` in `bootstrap/app.php`). Test after going live.

---

## Hostinger: `symlink()` disabled (storage:link fails)

If logs show `Call to undefined function ... symlink()` or `storage:link` fails, **do not** rely on `php artisan storage:link`. Use the shell instead:

```bash
bash scripts/link-storage-shared-hosting.sh \
  "$HOME/domains/YOURDOMAIN.com/public_html" \
  "$HOME/laravel_app"
```

Or manually (example: `public_html` under `~/domains/retailnexis.com/`):

```bash
cd ~/domains/retailnexis.com/public_html
rm -rf storage
ln -sfn "$HOME/laravel_app/storage/app/public" storage
ls -la storage
```

(Use your real domain folder name; `$HOME/laravel_app` must point at the Laravel project.)

---

## Database connection fails (migrate / PDO)

1. Confirm `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` in `.env` match **hPanel → Databases** (user must be **assigned** to that database).
2. Try `DB_HOST=localhost` if `127.0.0.1` fails (or vice versa).
3. Test from SSH: `mysql -u u612565959_ss -p -h 127.0.0.1 u612565959_delta`

**Never paste `.env` lines into the bash terminal** — paste only in `nano .env` or File Manager; otherwise you get `syntax error` / `command not found`.

---

## Common errors

| Symptom | Fix |
|--------|-----|
| 500 + blank page | `APP_DEBUG=true` briefly; check `storage/logs/laravel.log` |
| `symlink()` / storage:link | Use `scripts/link-storage-shared-hosting.sh` or `ln -s` (see above) |
| PDO / SQLSTATE | Fix `.env` DB_*; verify user has access to DB in hPanel |
| “No application encryption key” | `php artisan key:generate` in `laravel_app` |
| Permission denied on storage | chmod/chown `storage`, `bootstrap/cache` |
| 404 on all routes except `/` | `.htaccess` missing or `mod_rewrite` off; confirm `RewriteBase /` if in subdirectory |
| Wrong paths to vendor | `index.php` in `public_html` must use `$laravelRoot` pointing to full Laravel tree |

---

## Security checklist

- `APP_DEBUG=false` in production.
- Restrict file permissions; never expose `.env` (must not be under `public_html`).
- Keep `vendor` only on server; use `composer install --no-dev` for production.
- Enable SSL and use `APP_URL=https://...`.

---

## Confirm go-live

1. Open `https://yourdomain.com` — login/landing loads.
2. Hit a tenant URL if you use domains/subdomains — tenancy routes resolve.
3. Upload a test file through the app — `storage` writable.
4. Check `storage/logs/laravel.log` for new errors after smoke test.

---

## What was NOT done automatically here

This document and `public/index.shared-hosting.php` were added in the repository. **No commands were run on Hostinger** (no access). Follow the steps on your account to complete deployment.

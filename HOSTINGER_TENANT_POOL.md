# Hostinger: tenant database pool (Stancl Tenancy)

Shared hosting MySQL users usually **cannot** run `CREATE DATABASE` or `GRANT`. This app assigns **pre-created** empty databases from the landlord table `tenant_databases` so provisioning stays automatic and avoids `SQLSTATE[1044]`.

## 1. hPanel: create empty databases

1. **Websites → Manage → Databases → Create database** (repeat for each tenant slot you want).
2. Name pattern is often `u123456789_something` — note each **full database name**.
3. For **each** database, open **Privileged users** (or **Manage**) and assign your Laravel MySQL user (same as `DB_USERNAME` in `.env`) with **All privileges**.

If the user is not attached to the schema, you get **1044** when the app connects to that tenant DB.

## 2. Landlord table `tenant_databases`

Columns:

| Column       | Purpose                                                |
|-------------|---------------------------------------------------------|
| `id`        | Auto increment                                          |
| `name`      | Exact MySQL database name from hPanel                   |
| `is_used`   | `0` = free, `1` = assigned to a tenant                  |
| `tenant_id` | Stancl tenant id when assigned (nullable)               |
| `assigned_at` | When the row was claimed                            |

Migration: `database/migrations/landlord/2026_03_25_100000_create_tenant_databases_table.php`

Run landlord migrations:

```bash
php artisan migrate --path=database/migrations/landlord
```

## 3. Register pool rows (preferred on Hostinger)

The command is **only** in `routes/console.php` (no `app/Console/Commands/SeedTenantDatabasePoolCommand.php`). Upload that file plus `app/Services/TenantDatabasePoolTableSeeder.php` and `config/tenant_database_pool.php`.

```bash
php artisan tenant:seed-database-pool
```

Set exact MySQL names in `.env` (comma-separated):

```env
TENANT_DATABASE_POOL_NAMES=u612565959_pool_01,u612565959_pool_02,u612565959_pool_03
```

Or one-off without `.env`:

```bash
php artisan tenant:seed-database-pool --names=u612565959_pool_01,u612565959_pool_02
```

Or insert manually in phpMyAdmin / Adminer on the **landlord** database:

```sql
INSERT INTO tenant_databases (`name`, `is_used`, `created_at`, `updated_at`)
VALUES
  ('u612565959_tenant_pool_01', 0, NOW(), NOW()),
  ('u612565959_tenant_pool_02', 0, NOW(), NOW());
```

## 4. Environment

```env
SERVER_TYPE=hostinger
DB_CONNECTION=saleprosaas_landlord
DB_USERNAME=u612565959_NEXA
DB_PASSWORD=...
# Pool defaults ON when SERVER_TYPE=hostinger. Override if needed:
# TENANT_DATABASE_POOL_ENABLED=true
```

`TENANT_DATABASE_POOL_ENABLED=true` enables:

- `AllocateTenantDatabaseFromPool` on `CreatingTenant` (transaction + `lockForUpdate()`).
- `CustomMySQLDatabaseManager` **does not** call `CREATE DATABASE`; it only checks the schema exists and the app user can `USE` it.
- `PoolAwareCreateDatabase` job skips Stancl’s “database must not exist” check (required because pool DBs already exist).

## 5. Operations

- **Add capacity**: create new empty DBs in hPanel, grant user, insert rows with `is_used = 0`.
- **Logs**: successful assignments log `tenant_database_pool.assigned` with `tenant_id`, `database`, `pool_row_id`.
- **Pool exhausted**: users see a clear message; add more pool rows and DBs in hPanel.

## 6. Local development

Set `SERVER_TYPE=localhost` and either:

- `TENANT_DATABASE_POOL_ENABLED=false` and a user that may `CREATE DATABASE`, **or**
- Keep pool enabled and seed `tenant_databases` with local empty databases your dev user can access.

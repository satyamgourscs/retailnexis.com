<?php

declare(strict_types=1);

namespace App\CustomMySQLDatabaseManager;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager;
use Throwable;

/**
 * Tenant database lifecycle for Stancl Tenancy.
 *
 * Hostinger shared hosting (SERVER_TYPE=hostinger): one "master" MySQL user (DB_USERNAME / DB_PASSWORD)
 * is used for landlord + all tenants. New tenant DBs are created with CREATE DATABASE over the
 * saleprosaas_tenant template connection; the same user must retain ALL privileges on databases it creates.
 * Hostinger may require CREATE DATABASE privilege — if denied, create DBs in hPanel and assign the user.
 *
 * SQLSTATE[HY000] [1044]: MySQL user has no privilege on the new DB. After CREATE DATABASE, some hosts do not
 * auto-assign the panel user; we try GRANT (requires GRANT OPTION). If that fails, link DB + user in hPanel
 * (Databases → select DB → assign user with ALL privileges).
 */
class CustomMySQLDatabaseManager extends MySQLDatabaseManager
{
    protected function serverType(): string
    {
        return (string) config('app.server_type', '');
    }

    /**
     * Ensure the same MySQL user used for tenant connections can use this schema (fixes 1044 on some shared hosts).
     */
    protected function grantMysqlUserAccessToDatabase(string $database): void
    {
        $username = (string) $this->database()->getConfig('username');
        if ($username === '') {
            return;
        }

        $db = str_replace('`', '``', $database);
        $user = str_replace('`', '``', $username);

        foreach (['127.0.0.1', 'localhost', '%'] as $host) {
            $h = str_replace('`', '``', $host);
            try {
                $this->database()->statement("GRANT ALL PRIVILEGES ON `{$db}`.* TO `{$user}`@`{$h}`");
            } catch (Throwable $e) {
                Log::debug('Tenant DB GRANT not applied (expected on hosts without GRANT OPTION)', [
                    'database' => $database,
                    'mysql_user' => $username,
                    'mysql_host' => $host,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        try {
            $this->database()->statement('FLUSH PRIVILEGES');
        } catch (Throwable $e) {
            // Often denied without RELOAD privilege; hPanel assignment still works.
        }
    }

    /**
     * Fail fast with a clear message if the app user still cannot USE the tenant DB (MySQL 1044 on shared hosts).
     */
    protected function assertTenantDatabaseUsable(string $database): void
    {
        $base = $this->database()->getConfig();
        $base['database'] = $database;
        $connName = '_tenant_access_check_' . substr(md5($database), 0, 16);
        config(["database.connections.{$connName}" => $base]);
        DB::purge($connName);

        try {
            DB::connection($connName)->getPdo()->query('SELECT 1');
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            DB::purge($connName);
            $user = (string) ($base['username'] ?? '');
            if (str_contains($msg, '1044') || str_contains($msg, 'Access denied for user')) {
                throw new RuntimeException(
                    "MySQL 1044: user [{$user}] cannot access database [{$database}]. "
                    . 'Hostinger: hPanel → Websites → Databases → open this database → Privileged users → '
                    . "add [{$user}] with ALL privileges. Then retry client provisioning. Raw error: {$msg}"
                );
            }
            throw $e;
        }
        DB::purge($connName);
    }

    public function createDatabase(TenantWithDatabase $tenant): bool
    {
        $database = $tenant->database()->getName();
        $charset = $this->database()->getConfig('charset');
        $collation = $this->database()->getConfig('collation');

        if ($this->serverType() === 'cpanel') {
            $headers = array(
                "Authorization: cpanel " . env('CPANEL_USER_NAME') . ":" . env('CPANEL_API_KEY'),
                "Content-Type: text/plain"
            );

            //custom code for creating DB in a cPanel based server
            $url = "https://" . config('app.central_domain') . ":2083/execute/Mysql/create_database?name=" . $database;

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($curl);
            curl_close($curl);

            //custom code for assigning user to DB in a cPanel based server
            $url = "https://" . config('app.central_domain') . ":2083/execute/Mysql/set_privileges_on_database?user=" . env('DB_USERNAME') . "&database=" . $database . "&privileges=ALL";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($curl);
            curl_close($curl);

            return true;

        } elseif ($this->serverType() === 'plesk') {
            $host = config('app.central_domain');
            $username = env('PLESK_USER_NAME');
            $password = env('PLESK_PASSWORD');
            $server_id = env('PLESK_DATABASE_SERVER_ID');
            $pleskApiUrl = 'https://' . $host . ':8443/api/v2/databases';
            $databaseData = [
                'name' => $database, // Name of the database
                'type' => 'mysql', // Database type (mysql, postgres, etc.)
                'access' => 'localhost', // Access type (usually 'localhost')
                'parent_domain' => [
                    'name' => $host,
                ],
                'server_id' => $server_id
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $pleskApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($databaseData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode("$username:$password"),
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable SSL hostname verification if needed
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL certificate verification if needed

            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response);
            $tenant->setInternal('db_id', $response->id);

            return true;
        }

        // localhost, hostinger (hPanel), VPS: master MySQL user runs CREATE DATABASE; tenant connection reuses same user.
        if (in_array($this->serverType(), ['localhost', 'hostinger', 'vps'], true)) {
            if (! $this->databaseExists($database)) {
                $ok = $this->database()->statement("CREATE DATABASE `{$database}` CHARACTER SET `{$charset}` COLLATE `{$collation}`");
                if (! $ok) {
                    return false;
                }
            }
            $this->grantMysqlUserAccessToDatabase($database);
            $this->assertTenantDatabaseUsable($database);

            return true;
        }

        // Default: same as Stancl — master user must have CREATE DATABASE (typical on VPS; verify on Hostinger plan).
        if (! $this->databaseExists($database)) {
            $ok = $this->database()->statement("CREATE DATABASE `{$database}` CHARACTER SET `{$charset}` COLLATE `{$collation}`");
            if (! $ok) {
                return false;
            }
        }
        $this->grantMysqlUserAccessToDatabase($database);
        $this->assertTenantDatabaseUsable($database);

        return true;
    }

    public function deleteDatabase(TenantWithDatabase $tenant): bool
    {
        $database = $tenant->database()->getName();
        $charset = $this->database()->getConfig('charset');
        $collation = $this->database()->getConfig('collation');

        if ($this->serverType() === 'cpanel') {
            $headers = array(
                "Authorization: cpanel " . env('CPANEL_USER_NAME') . ":" . env('CPANEL_API_KEY'),
                "Content-Type: text/plain"
            );
            //custom code for creating DB in a cPanel based server
            $url = "https://" . config('app.central_domain') . ":2083/execute/Mysql/delete_database?name=" . $database;
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($curl);
            curl_close($curl);
            return true;
        } elseif ($this->serverType() === 'plesk') {
            $host = config('app.central_domain');
            $username = env('PLESK_USER_NAME');
            $password = env('PLESK_PASSWORD');
            $db_id = $tenant->getInternal('db_id');
            $pleskApiUrl = 'https://' . $host . ':8443/api/v2/databases/' . $db_id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $pleskApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode("$username:$password"),
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable SSL hostname verification if needed
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL certificate verification if needed
            curl_exec($ch);
            curl_close($ch);
            return true;
        } elseif (in_array($this->serverType(), ['localhost', 'hostinger', 'vps'], true)) {
            return $this->database()->statement("DROP DATABASE `{$tenant->database()->getName()}`");
        }

        return $this->database()->statement("DROP DATABASE `{$tenant->database()->getName()}`");
    }
}

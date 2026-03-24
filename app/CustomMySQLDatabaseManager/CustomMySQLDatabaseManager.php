<?php

declare(strict_types=1);

namespace App\CustomMySQLDatabaseManager;

use Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

/**
 * Tenant database lifecycle for Stancl Tenancy.
 *
 * Hostinger shared hosting (SERVER_TYPE=hostinger): one "master" MySQL user (DB_USERNAME / DB_PASSWORD)
 * is used for landlord + all tenants. New tenant DBs are created with CREATE DATABASE over the
 * saleprosaas_tenant template connection; the same user must retain ALL privileges on databases it creates.
 * Hostinger may require CREATE DATABASE privilege — if denied, create DBs in hPanel and assign the user.
 */
class CustomMySQLDatabaseManager extends MySQLDatabaseManager
{
    protected function serverType(): string
    {
        return (string) config('app.server_type', '');
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
            return $this->database()->statement("CREATE DATABASE `{$database}` CHARACTER SET `{$charset}` COLLATE `{$collation}`");
        }

        // Default: same as Stancl — master user must have CREATE DATABASE (typical on VPS; verify on Hostinger plan).
        return $this->database()->statement("CREATE DATABASE `{$database}` CHARACTER SET `{$charset}` COLLATE `{$collation}`");
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

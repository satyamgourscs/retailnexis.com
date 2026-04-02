<?php

declare(strict_types=1);

namespace App\CustomMySQLDatabaseManager;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Contracts\TenantDatabaseManager;
use Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Exceptions\NoConnectionSetException;

class CustomMySQLDatabaseManager extends MySQLDatabaseManager
{

    public function createDatabase(TenantWithDatabase $tenant): bool
    {
        $database = $tenant->database()->getName();
        $charset = $this->database()->getConfig('charset');
        $collation = $this->database()->getConfig('collation');

        if (env('SERVER_TYPE') == 'cpanel') {
            $headers = array(
                "Authorization: cpanel " . env('CPANEL_USER_NAME') . ":" . env('CPANEL_API_KEY'),
                "Content-Type: text/plain"
            );

            //custom code for creating DB in a cPanel based server
            $url = "https://" . env('CENTRAL_DOMAIN') . ":2083/execute/Mysql/create_database?name=" . $database;

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
            $url = "https://" . env('CENTRAL_DOMAIN') . ":2083/execute/Mysql/set_privileges_on_database?user=" . env('DB_USERNAME') . "&database=" . $database . "&privileges=ALL";

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

        } elseif (env('SERVER_TYPE') == 'plesk') {
            $host = env('CENTRAL_DOMAIN');
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
        } elseif (env('SERVER_TYPE') == 'localhost') {
            $this->database()->statement("CREATE DATABASE `{$database}` CHARACTER SET `$charset` COLLATE `$collation`");
        }
        return true;
    }

    public function deleteDatabase(TenantWithDatabase $tenant): bool
    {
        $database = $tenant->database()->getName();
        $charset = $this->database()->getConfig('charset');
        $collation = $this->database()->getConfig('collation');

        if (env('SERVER_TYPE') == 'cpanel') {
            $headers = array(
                "Authorization: cpanel " . env('CPANEL_USER_NAME') . ":" . env('CPANEL_API_KEY'),
                "Content-Type: text/plain"
            );
            //custom code for creating DB in a cPanel based server
            $url = "https://" . env('CENTRAL_DOMAIN') . ":2083/execute/Mysql/delete_database?name=" . $database;
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
        } elseif (env('SERVER_TYPE') == 'plesk') {
            $host = env('CENTRAL_DOMAIN');
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
        } elseif (env('SERVER_TYPE') == 'localhost')
            return $this->database()->statement("DROP DATABASE `{$tenant->database()->getName()}`");
    }
}

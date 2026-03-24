<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaasInstallationRequest;
use App\Traits\ENVFilePutContent;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SaasInstallController extends Controller
{
    use ENVFilePutContent;

    public function saasInstallStep1()
    {
        $baseUrl = url('/');
        $host = parse_url($baseUrl, PHP_URL_HOST);
        $path = parse_url($baseUrl, PHP_URL_PATH);
        // Check if it's NOT a subdomain (e.g., only one dot in host like saleprosaas.test)
        $isNotSubdomain = substr_count($host, '.') <= 1;
        // Check if it's NOT a subfolder (path is just "/" or empty)
        $isNotSubfolder = $path === '/' || $path === '' || is_null($path);

        $allowed = 1;
        // This product prefers root-domain installs, but local/XAMPP setups commonly run in a subfolder.
        // Allow subfolder installs on localhost/local environment.
        $allowSubfolderOnLocalhost = (env('SERVER_TYPE') === 'localhost') || app()->environment('local') || in_array($host, ['localhost', '127.0.0.1'], true);

        if ($isNotSubdomain && ($isNotSubfolder || $allowSubfolderOnLocalhost)) {
            $this->dataWriteInENVFile('APP_URL', $baseUrl);
            $this->dataWriteInENVFile('CENTRAL_DOMAIN', $host);
        }
        else {
            $allowed = 0;
        }
        return view('saas.step_1', compact('allowed'));
    }

    public function saasInstallStep2()
    {
        return view('saas.step_2');
    }
    public function saasInstallStep3()
    {
        return view('saas.step_3');
    }

    public function saasInstallProcess(SaasInstallationRequest $request)
    {
      $purchaseCode = 'MDCHJWY4-MR24-5HYG-QVZY-GI30CFW48Y5V';

      $dataServer = self::purchaseVerify($purchaseCode);

        if (! is_object($dataServer) || empty($dataServer->dbdata)) {
            return redirect()->back()->withErrors(['errors' => ['Wrong Purchase Code !']]);
        }

        $envPath = base_path('.env');
        if (!file_exists($envPath))
            return redirect()->back()->withErrors(['errors' => ['.env file does not exist.']]);
        elseif (!is_readable($envPath))
            return redirect()->back()->withErrors(['errors' => ['.env file is not readable.']]);
        elseif (!is_writable($envPath))
            return redirect()->back()->withErrors(['errors' => ['.env file is not writable.']]);
        else {
            try {
                $normalized = $this->normalizeDbInput($request);
                $this->envSetDatabaseCredentials((object) $normalized);
                self::switchToNewDatabaseConnection((object) $normalized);

                // Validate DB connectivity before importing SQL dump.
                $this->assertLandlordDbConnection((object) $normalized);

                self::importCentralDatabase($dataServer->dbdata);
                self::optimizeClear();

                // Never use $request->central_domain (not present on step-3 form). Build URL from this
                // request so subfolder installs (e.g. /saas/public/) and production domains both work.
                $target = rtrim($request->getBaseUrl(), '/').'/saas/install/step-4';

                return new RedirectResponse($target);

            } catch (Exception $e) {

                return redirect()->back()->withErrors(['errors' => [$this->formatInstallDbError($e)]]);
            }
        }
    }

    protected function normalizeDbInput(SaasInstallationRequest $request): array
    {
        $host = trim((string) $request->db_host);
        if ($host === '' || strtolower($host) === 'localhost') {
            $host = '127.0.0.1';
        }

        $port = (int) $request->db_port;
        if ($port <= 0) {
            $port = 3306;
        }

        return [
            'server_type' => $request->server_type,
            'cpanel_api_key' => $request->cpanel_api_key,
            'cpanel_username' => $request->cpanel_username,
            'plesk_username' => $request->plesk_username,
            'plesk_password' => $request->plesk_password,
            'plesk_database_server_id' => $request->plesk_database_server_id,
            'db_host' => $host,
            'db_port' => $port,
            'db_username' => (string) $request->db_username,
            'db_password' => (string) ($request->db_password ?? ''),
            'db_name' => (string) $request->db_name,
        ];
    }

    protected static function purchaseVerify(string $purchaseCode) : object
    {
        $post_string = urlencode($purchaseCode);
        $url = 'https://lion-coders.com/api/sale-pro-saas-purchase/verify/install/'.$post_string;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $response = json_decode($result, false);

        return is_object($response) ? $response : (object) ['dbdata' => null];

    }

    protected function envSetDatabaseCredentials($request): void
    {
        $this->dataWriteInENVFile('SERVER_TYPE', $request->server_type);

        if ($request->server_type==='cpanel') {
            $this->dataWriteInENVFile('CPANEL_USER_NAME', $request->cpanel_username);
            $this->dataWriteInENVFile('CPANEL_API_KEY', $request->cpanel_api_key);
            $this->dataWriteInENVFile('DB_PREFIX', $request->cpanel_username.'_');
        }
        elseif ($request->server_type==='plesk'){
            $this->dataWriteInENVFile('PLESK_USER_NAME', $request->plesk_username);
            $this->dataWriteInENVFile('PLESK_PASSWORD', $request->plesk_password);
            $this->dataWriteInENVFile('PLESK_DATABASE_SERVER_ID', $request->plesk_database_server_id);
        }

        $this->dataWriteInENVFile('DB_CONNECTION', 'saleprosaas_landlord');
        $this->dataWriteInENVFile('DB_HOST', $request->db_host);
        $this->dataWriteInENVFile('DB_PORT', $request->db_port);
        $this->dataWriteInENVFile('DB_DATABASE', null);
        $this->dataWriteInENVFile('LANDLORD_DB', $request->db_name);
        $this->dataWriteInENVFile('DB_USERNAME', $request->db_username);
        $this->dataWriteInENVFile('DB_PASSWORD', $request->db_password);
    }

    public function switchToNewDatabaseConnection($request): void
    {
        DB::purge('saleprosaas_landlord');
        Config::set('database.connections.saleprosaas_landlord.host', $request->db_host);
        Config::set('database.connections.saleprosaas_landlord.port', $request->db_port);
        Config::set('database.connections.saleprosaas_landlord.database', $request->db_name);
        Config::set('database.connections.saleprosaas_landlord.username', $request->db_username);
        Config::set('database.connections.saleprosaas_landlord.password', $request->db_password);
    }

    protected function assertLandlordDbConnection(object $normalized): void
    {
        try {
            DB::purge('saleprosaas_landlord');
            DB::connection('saleprosaas_landlord')->getPdo();
        } catch (\Throwable $e) {
            // Throw a clearer exception while keeping the original cause.
            $context = sprintf(
                'DB connection failed (host=%s port=%s db=%s user=%s). ',
                $normalized->db_host,
                $normalized->db_port,
                $normalized->db_name,
                $normalized->db_username
            );
            throw new Exception($context . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    protected static function importCentralDatabase($dbdata): void
    {
        // SQL dump import can exceed PHP's default execution time (30s) on local machines.
        // Lift limits only for this install/import request.
        try {
            @set_time_limit(0);
            @ini_set('max_execution_time', '0');
            // Keep memory bounded but practical for large dumps.
            if (function_exists('ini_get') && function_exists('ini_set')) {
                $current = ini_get('memory_limit');
                // If it's very low (common), raise it for install only.
                if (is_string($current) && preg_match('/^\s*(\d+)\s*M\s*$/i', $current, $m) && (int) $m[1] < 512) {
                    @ini_set('memory_limit', '1024M');
                }
            }
        } catch (\Throwable $e) {
            // Ignore ini failures; import may still succeed.
        }

        $connectionName = 'saleprosaas_landlord';

        // Only skip when the landlord schema looks complete (core SaaS tables present).
        // Skipping on "any table" breaks partial/failed imports: leftover tables blocked the full dump.
        if (self::landlordInstallLooksComplete($connectionName)) {
            return;
        }

        $conn = DB::connection($connectionName);

        try {
            $conn->unprepared('SET FOREIGN_KEY_CHECKS=0');
            $conn->unprepared($dbdata);
        } catch (\Throwable $e) {
            if (self::isDuplicateOrExistsSqlError($e)) {
                self::importCentralDatabaseStatementWise($dbdata, $connectionName);
            } else {
                throw $e;
            }
        } finally {
            try {
                $conn->unprepared('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Throwable $e) {
            }
        }
    }

    /**
     * True when core landlord tables from the install dump exist (successful prior install).
     * Do not use "any table" — a partial/failed import can leave a few tables and must re-run import.
     */
    protected static function landlordInstallLooksComplete(string $connectionName): bool
    {
        $required = ['tenants', 'domains', 'general_settings'];

        try {
            $rows = DB::connection($connectionName)->select('SHOW TABLES');
            if (! is_array($rows) || $rows === []) {
                return false;
            }

            $names = [];
            foreach ($rows as $row) {
                $vals = array_values((array) $row);
                if ($vals !== []) {
                    $names[strtolower((string) $vals[0])] = true;
                }
            }

            foreach ($required as $table) {
                if (! isset($names[$table])) {
                    return false;
                }
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected static function isDuplicateOrExistsSqlError(\Throwable $e): bool
    {
        $m = $e->getMessage();

        return str_contains($m, '42S01')
            || str_contains($m, '1050')
            || str_contains($m, 'Base table or view already exists')
            || str_contains($m, 'already exists')
            // Dump often does CREATE then ALTER ADD PRIMARY KEY; table may already have PK (1068).
            || str_contains($m, '1068')
            || str_contains($m, 'Multiple primary key');
    }

    /**
     * Fallback when the single-shot dump fails: run statement chunks and ignore "already exists" /
     * duplicate row so a re-run on a partly filled DB can complete.
     */
    protected static function importCentralDatabaseStatementWise(string $dbdata, string $connectionName): void
    {
        $sql = preg_replace('/\/\*[\s\S]*?\*\//', '', $dbdata);
        $sql = preg_replace('/^\s*--.*$/m', '', (string) $sql);
        $parts = preg_split('/;\s*[\r\n]+/', (string) $sql);
        $conn = DB::connection($connectionName);

        foreach ($parts as $part) {
            $stmt = trim($part);
            if ($stmt === '') {
                continue;
            }
            $head = strtoupper(substr(ltrim($stmt), 0, 12));
            if (str_starts_with($head, 'DELIMITER ')) {
                continue;
            }

            try {
                $conn->unprepared($stmt);
            } catch (\Throwable $e) {
                if (self::isIgnorableStatementImportError($e)) {
                    continue;
                }
                throw $e;
            }
        }
    }

    protected static function isIgnorableStatementImportError(\Throwable $e): bool
    {
        $m = $e->getMessage();

        if (self::isDuplicateOrExistsSqlError($e)) {
            return true;
        }
        if (str_contains($m, '1062') || str_contains($m, 'Duplicate entry')) {
            return true;
        }
        if (str_contains($m, '1068') || str_contains($m, 'Multiple primary key')) {
            return true;
        }

        return false;
    }

    protected static function optimizeClear(): void
    {
        Artisan::call('optimize:clear');
    }

    public function saasInstallStep4()
    {
        return view('saas.step_4');
    }

    protected function formatInstallDbError(\Throwable $e): string
    {
        $message = $e->getMessage();

        // Common MySQL/PDO error codes:
        // 1045: bad credentials, 1049: unknown database, 2002: can't connect to server.
        $code = (int) $e->getCode();

        if ($code === 1045 || str_contains($message, 'SQLSTATE[HY000] [1045]')) {
            return 'MySQL authentication failed (wrong username/password or user lacks privileges). '
                . 'If you are using XAMPP and root has NO password, leave Database Password blank. '
                . 'Otherwise verify Database Username/Password.';
        }

        if ($code === 1049 || str_contains($message, 'SQLSTATE[HY000] [1049]')) {
            return 'Database does not exist. Create the landlord database first (the Database Name you entered), then retry.';
        }

        if ($code === 2002 || str_contains($message, 'SQLSTATE[HY000] [2002]')) {
            return 'Cannot reach MySQL server. Confirm MySQL is running and Host/Port are correct (use 127.0.0.1 and 3306 for XAMPP).';
        }

        if (str_contains($message, '42S01') || str_contains($message, 'Base table or view already exists') || str_contains($message, '1050')) {
            return 'This database already contains landlord tables (e.g. from a previous install). '
                . 'Use a new empty database, or drop all tables in the selected database, then run the installer again.';
        }

        return $message;
    }
}

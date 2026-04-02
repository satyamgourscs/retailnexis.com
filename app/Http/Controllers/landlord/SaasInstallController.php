<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaasInstallationRequest;
use App\Traits\ENVFilePutContent;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SaasInstallController extends Controller
{
    use ENVFilePutContent;

    protected function landlordAlreadyProvisioned(): bool
    {
        try {
            return Schema::hasTable('general_settings')
                && DB::table('general_settings')->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function saasInstallStep1()
    {
        if ($this->landlordAlreadyProvisioned()) {
            return redirect('/');
        }

        $baseUrl = url('/');
        $host = parse_url($baseUrl, PHP_URL_HOST);
        $path = parse_url($baseUrl, PHP_URL_PATH);
        // Check if it's NOT a subdomain (e.g., only one dot in host like retailnexis.test)
        $isNotSubdomain = substr_count($host, '.') <= 1;
        // Check if it's NOT a subfolder (path is just "/" or empty)
        $isNotSubfolder = $path === '/' || $path === '' || is_null($path);

        $allowed = 1;
        if ($isNotSubdomain && $isNotSubfolder) {
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
        if ($this->landlordAlreadyProvisioned()) {
            return redirect('/');
        }

        return view('saas.step_2');
    }
    public function saasInstallStep3()
    {
        if ($this->landlordAlreadyProvisioned()) {
            return redirect('/');
        }

        return view('saas.step_3');
    }

    public function saasInstallProcess(SaasInstallationRequest $request)
    {
        if ($this->landlordAlreadyProvisioned()) {
            return redirect('/');
        }

      $purchaseCode = 'MDCHJWY4-MR24-5HYG-QVZY-GI30CFW48Y5V';

      $dataServer = self::purchaseVerify($purchaseCode);

        if (!$dataServer->dbdata) {
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
                $this->envSetDatabaseCredentials($request);
                self::switchToNewDatabaseConnection($request);
                self::importCentralDatabase($dataServer->dbdata);
                self::optimizeClear();

                return redirect()->route('saas-install-step-4');

            } catch (Exception $e) {

                return redirect()->back()->withErrors(['errors' => [$e->getMessage()]]);
            }
        }
    }

    protected static function purchaseVerify(string $purchaseCode) : object
    {
        $post_string = urlencode($purchaseCode);
        $url = 'https://tryonedigital.com/api/retail-nexis-saas-purchase/verify/install/'.$post_string;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $response = json_decode($result, false);

        return $response;

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

        $this->dataWriteInENVFile('DB_CONNECTION', 'retailnexis_landlord');
        $this->dataWriteInENVFile('DB_HOST', $request->db_host);
        $this->dataWriteInENVFile('DB_PORT', $request->db_port);
        $this->dataWriteInENVFile('DB_DATABASE', null);
        $this->dataWriteInENVFile('LANDLORD_DB', $request->db_name);
        $this->dataWriteInENVFile('DB_USERNAME', $request->db_username);
        $this->dataWriteInENVFile('DB_PASSWORD', $request->db_password);
    }

    public function switchToNewDatabaseConnection($request): void
    {
        DB::purge('retailnexis_landlord');
        Config::set('database.connections.retailnexis_landlord.host', $request->db_host);
        Config::set('database.connections.retailnexis_landlord.database', $request->db_name);
        Config::set('database.connections.retailnexis_landlord.username', $request->db_username);
        Config::set('database.connections.retailnexis_landlord.password', $request->db_password);
    }

    protected static function importCentralDatabase($dbdata): void
    {
        DB::unprepared($dbdata);
    }

    protected static function optimizeClear(): void
    {
        Artisan::call('optimize:clear');
    }

    public function saasInstallStep4()
    {
        if ($this->landlordAlreadyProvisioned()) {
            return redirect('/');
        }

        return view('saas.step_4');
    }

}

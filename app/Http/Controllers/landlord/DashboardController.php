<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\landlord\Tenant;
use Illuminate\Support\Facades\Artisan;
use App\Traits\AutoUpdateTrait;
use App\Traits\ENVFilePutContent;
use Exception;
use ZipArchive;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    use AutoUpdateTrait, ENVFilePutContent;

    private $versionUpgradeInfo = [];

	public function __construct()
    {
		$this->middleware(['auth']);
        $this->versionUpgradeInfo = $this->isUpdateAvailable();
	}

    public function index()
    {

        $versionUpgradeData = [];
        $versionUpgradeData = $this->versionUpgradeInfo;

        $tenants = Tenant::all();
        $active_tenants = $tenants->where('expiry_date', '>=', date('Y-m-d'));
        $packages = DB::table('packages')->where('is_active', 1)->get();
        $package_count = count($packages);
        $received_amount = DB::table('payments')->sum('amount');

        $subscription_value = 0;
        foreach ($tenants as $tenant) {
            if(isset($tenant->package_id) && isset($tenant->subscription_type)) {
                $package_id = $tenant->package_id;
                $subscription_type = $tenant->subscription_type;
                $package = $packages->where('id',$package_id)->first();
                if($package) {
                    if($subscription_type == 'monthly') {
                        $subscription_value += $package->monthly_fee;
                    }
                    elseif($subscription_type == 'yearly') {
                        $subscription_value += $package->yearly_fee;
                    }
                }
            }
        }
        return view('landlord.dashboard',compact('tenants', 'active_tenants', 'package_count',
        'subscription_value', 'received_amount', 'versionUpgradeData'));
    }

    public function newVersionReleasePage()
    {
		// Below line is deprecated, this code is needed for the client version 1.5.1 and below
        $this->dataWriteInENVFile('APP_ENV', 'local');
		// Below line is deprecated, this code is needed for the client version 1.5.1 and below

        $versionUpgradeData = [];
        $versionUpgradeData = $this->versionUpgradeInfo;
        return view('version_upgrade.index', compact('versionUpgradeData'));
    }


    public function versionUpgrade(Request $request) {
        $versionUpgradeData = [];
        $versionUpgradeData = $this->versionUpgradeInfo;
        $version_upgrade_file_url = $this->versionUpgradeFileUrl($request->purchasecode);

        if (!$version_upgrade_file_url) {
            return redirect()->back()->with('not_permitted', 'Wrong Purchase Code !');
        }

        try {
            //Check file is exist
            $header_array = @get_headers($version_upgrade_file_url);
            if(!strpos($header_array[0], '200')) {
                throw new Exception("Something wrong. Please contact with support team.");
            }

            $this->fileTransferProcess($version_upgrade_file_url);

            if ($versionUpgradeData['latest_version_db_migrate_enable']==true){
                Artisan::call('migrate --path=database/migrations/landlord');
                Artisan::call('db:seed');
                $tenant_all = Tenant::all();
                if(count($tenant_all)) {
                    Artisan::call('tenants:migrate');
                    Artisan::call('tenants:seed');
                }
            }

            Artisan::call('optimize:clear');

            $this->dataWriteInENVFile('VERSION', $versionUpgradeData['demo_version']);

            return redirect()->back()->with('message', 'Version Upgraded Successfully !!!');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function fileTransferProcess($version_upgrade_file_url)
    {
        $remote_file_name = pathinfo($version_upgrade_file_url)['basename'];
        $local_file = base_path('/'.$remote_file_name);
        $copy = copy($version_upgrade_file_url, $local_file);
        if ($copy) {
            // ****** Unzip ********
            $zip = new ZipArchive;
            $file = base_path($remote_file_name);
            $res = $zip->open($file);
            if ($res === TRUE) {
                $zip->extractTo(base_path('/'));
                $zip->close();

                // ****** Delete Zip File ******
                File::delete(base_path($remote_file_name));
            }
        }
    }
}

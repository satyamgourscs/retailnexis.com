<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\Package;
use App\Models\landlord\Tenant;
use App\Traits\TenantInfo;
use App\Traits\CacheForget;
use DB;

class PackageController extends Controller
{
    use TenantInfo, CacheForget;

    public function index()
    {
        $lims_package_all = Package::where('is_active', true)->get();
        return view('landlord.package.index', compact('lims_package_all'));
    }

    public function create()
    {
        $features = $this->features();
        return view('landlord.package.create', compact('features'));
    }

    public function store(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $data = $request->all();
        $data['features'] = json_encode($data['features']);
        if($data['permission_id']) {
            $permission_ids = explode(",", $data['permission_id']);
            foreach ($permission_ids as $key => $permission_id) {
                if($key)
                    $data['role_permission_values'] .= ',(' . $permission_id . ',1)';
                else
                    $data['role_permission_values'] = '(' . $permission_id . ',1)';
            }
        }
        $data['is_active'] = true;
        if(!isset($data['is_free_trial']))
            $data['is_free_trial'] = false;
        $this->cacheForget('packages');
        Package::create($data);
        return redirect()->route('packages.index')->with('message', 'Package created successfully!');
    }

    public function fetchPackageData($id)
    {
        return Package::find($id);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $features = $this->features();
        $packageData = Package::find($id);
        return view('landlord.package.edit', compact('packageData', 'features'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        $packageData = Package::find($id);
        if(!isset($data['is_free_trial']))
            $data['is_free_trial'] = false;
        $data['features'] = json_encode($data['features']);

        $abandoned_permission_ids = [];
        $permission_ids = [];
        $prev_permission_ids = [];

        if (!empty($packageData->permission_id)) {
            $prev_permission_ids = explode(",", $packageData->permission_id);
        }

        if($data['permission_id']) {
            $permission_ids = explode(",", $data['permission_id']);
            //searching for abandoned permission ids
            foreach ($prev_permission_ids as $key => $prev_permission_id) {
                if(!in_array($prev_permission_id, $permission_ids))
                    $abandoned_permission_ids[] = $prev_permission_id;
            }
            //creating role permission values
            foreach ($permission_ids as $key => $permission_id) {
                if($key)
                    $data['role_permission_values'] .= ',(' . $permission_id . ',1)';
                else
                    $data['role_permission_values'] = '(' . $permission_id . ',1)';
            }
        }
        else {
            $abandoned_permission_ids = $prev_permission_ids;
        }

        //updating package
        $packageData->update($data);
        $this->cacheForget('packages');

        if(isset($request->is_update_existing)) {
            //updating permission of previous tenant
            $search = '"package_id": '.'"'.$id.'"';
            $tenants = Tenant::where('data', 'LIKE', "%{$search}%")->get();

            if(count($tenants)) {
                $features = json_decode($packageData->features);
                $modules = [];
                if (in_array('manufacturing', $features)) {
                    $modules[] = 'manufacturing';
                }
                if(in_array('ecommerce', $features))
                    $modules[] = 'ecommerce';
                if(in_array('woocommerce', $features))
                    $modules[] = 'woocommerce';
                if(count($modules))
                    $modules = implode(",", $modules);
                else
                    $modules = Null;

                foreach ($tenants as $tenant) {
                    $this->changePermission($tenant, json_encode($abandoned_permission_ids), json_encode($permission_ids), $id, $modules);
                }
            }
        }
        return redirect()->route('packages.index')->with('message', 'Package updated successfully');
    }

    public function destroy($id)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        Package::find($id)->update(['is_active' => false]);
        $this->cacheForget('packages');
        return redirect()->back()->with('not_permitted', 'Package deleted successfully');
    }
}

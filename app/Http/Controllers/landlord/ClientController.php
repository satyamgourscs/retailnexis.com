<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use App\Models\landlord\Package;
use App\Models\landlord\Tenant;
use App\Traits\TenantInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    use TenantInfo;

    public function index()
    {
        if (cache()->has('general_setting')) {
            $general_setting = cache()->get('general_setting');
        } else {
            $general_setting = DB::table('general_settings')->latest()->first();
        }
        $lims_client_all = Tenant::all();
        $lims_package_all = Package::where('is_active', true)->get();

        return view('landlord.client.index', compact('lims_client_all', 'lims_package_all', 'general_setting'));
    }

    public function store(Request $request)
    {
        if (! config('app.demo_unlocked')) {
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        }

        if (cache()->has('general_setting')) {
            $general_setting = cache()->get('general_setting');
        } else {
            $general_setting = DB::table('general_settings')->latest()->first();
        }

        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'subscription_type' => 'required|in:free trial,monthly,yearly',
            'company_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|max:255',
            'email' => 'required|email|max:255',
        ]);

        $package = Package::select('is_free_trial', 'features', 'role_permission_values')->find($request->package_id);
        if (! $package) {
            return redirect()->back()->with('not_permitted', 'Invalid package selected.');
        }

        $features = json_decode($package->features ?? '[]', true);
        if (! is_array($features)) {
            $features = [];
        }

        $modules = [];
        foreach (['manufacturing', 'ecommerce', 'woocommerce', 'restaurant'] as $feature) {
            if (in_array($feature, $features, true)) {
                $modules[] = $feature;
            }
        }
        $modules = count($modules) ? implode(',', $modules) : null;

        if ($request->subscription_type === 'free trial') {
            $days = (int) ($general_setting->free_trial_limit ?? 0);
        } elseif ($request->subscription_type === 'monthly') {
            $days = 30;
        } elseif ($request->subscription_type === 'yearly') {
            $days = 365;
        } else {
            $days = 0;
        }

        $days = $days > 0 ? $days : 30;
        $expiryDateFormatted = now()->addDays($days)->format('Y-m-d');

        $rolePermissionValues = $package->role_permission_values ?? '';
        $pack_perm_role_pairs = explode('),(', trim((string) $rolePermissionValues, '()'));
        if ($pack_perm_role_pairs !== ['']) {
            $package_permissions_role = array_map(function ($pk_perm_role_p) {
                [$permission_id, $role_id] = explode(',', $pk_perm_role_p);

                return [
                    'permission_id' => (int) $permission_id,
                    'role_id' => (int) $role_id,
                ];
            }, $pack_perm_role_pairs);
        } else {
            $package_permissions_role = [];
        }

        $tenantData = [
            'site_title' => $general_setting->site_title,
            'site_logo' => $general_setting->site_logo,
            'package_id' => $request->package_id,
            'subscription_type' => $request->subscription_type,
            'developed_by' => $general_setting->developed_by,
            'modules' => $modules,
            'expiry_date' => $expiryDateFormatted,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone_number,
            'company_name' => $request->company_name,
            'package_permissions_role' => $package_permissions_role,
        ];

        $base = Str::slug((string) $request->company_name);
        if (! $base) {
            $base = 'store';
        }
        $base = substr($base, 0, 20);

        $prefix = (string) (config('tenancy.database.prefix') ?? '');
        $suffix = (string) (config('tenancy.database.suffix') ?? '');
        $tenantDatabaseName = static fn (string $id): string => $prefix.$id.$suffix;

        $tenantKey = $base;
        $counter = 1;
        while (
            Tenant::query()->where('id', $tenantKey)->exists()
            || DB::select(
                'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ? LIMIT 1',
                [$tenantDatabaseName($tenantKey)]
            ) !== []
        ) {
            $tenantKey = $base.$counter;
            $counter++;
            if ($counter > 10000) {
                return redirect()->back()->withInput()->with(
                    'not_permitted',
                    'Could not allocate a unique subdomain. Try a different company name.'
                );
            }
        }

        $tenantKey = strtolower($tenantKey);

        if (tenancy()->initialized) {
            tenancy()->end();
        }

        $finalizePayload = [
            'package_id' => (int) $request->package_id,
            'subscription_type' => (string) $request->subscription_type,
            'tenantData' => $tenantData,
            'modules' => $modules,
            'site_logo' => $general_setting->site_logo ?? '',
            'company_name' => $request->company_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'name' => $request->name,
            'password_plain' => (string) $request->password,
            'tenant_subdomain' => $tenantKey,
            'site_title' => $general_setting->site_title ?? '',
            'superadmin_email' => $general_setting->email ?? '',
            'expiry_date' => $expiryDateFormatted,
        ];

        Cache::store('central')->put('tenant_finalize_'.$tenantKey, $finalizePayload, now()->addMinutes(10));

        $tenant = Tenant::create([
            'id' => $tenantKey,
            'package_id' => (int) $request->package_id,
            'subscription_type' => (string) $request->subscription_type,
            'expiry_date' => $expiryDateFormatted,
            'company_name' => $request->company_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
        ]);

        $centralDomain = (string) config('app.central_domain');
        if ($centralDomain === '') {
            abort(503, 'Configure CENTRAL_DOMAIN or APP_URL in .env for tenant subdomain creation.');
        }
        $fullDomain = $tenantKey.'.'.$centralDomain;
        $tenant->domains()->firstOrCreate([
            'domain' => $fullDomain,
        ]);

        $message = 'Client created. Provisioning runs in the background (queue worker). You will receive a welcome email when the tenant database is ready. Use QUEUE_CONNECTION=database or redis and `php artisan queue:work` for async provisioning.';

        return redirect()->back()->with('message', $message);
    }

    public function checkSubdomain(Request $request)
    {
        $request->validate([
            'value' => ['required', 'string', 'max:120'],
            'ignoreTenantId' => ['nullable', 'string', 'max:120'],
        ]);

        $raw = (string) $request->query('value');
        $sanitized = Str::slug($raw, '');
        $sanitized = $sanitized !== '' ? $sanitized : 'client';

        $ignoreStr = $request->query('ignoreTenantId') ? (string) $request->query('ignoreTenantId') : null;

        $suggested = $sanitized;
        $n = 0;
        while (Tenant::query()
            ->where('id', $suggested)
            ->when($ignoreStr, fn ($q) => $q->where('id', '!=', $ignoreStr))
            ->exists()) {
            $n++;
            $suggested = substr($sanitized, 0, 100).$n;
        }

        $taken = Tenant::query()
            ->where('id', $sanitized)
            ->when($ignoreStr, fn ($q) => $q->where('id', '!=', $ignoreStr))
            ->exists();

        return response()->json([
            'ok' => true,
            'sanitized_subdomain' => $sanitized,
            'suggested_subdomain' => $suggested,
            'is_available' => ! $taken,
        ]);
    }

    public function addCustomDomain(Request $request)
    {
        if (! config('app.demo_unlocked')) {
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        }

        $request->validate([
            'domain' => 'required|string|max:255',
            'id' => 'required|string|exists:tenants,id',
        ]);

        DB::table('domains')->insert([
            'domain' => $request->domain,
            'tenant_id' => $request->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('message', 'Custom domain created successfully');
    }

    public function renew(Request $request)
    {
        if (! config('app.demo_unlocked')) {
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        }

        $request->validate([
            'id' => 'required|string|exists:tenants,id',
            'expiry_date' => 'required|string',
            'subscription_type' => ['required', Rule::in(['monthly', 'yearly'])],
        ]);

        $tenant = Tenant::find($request->id);
        if (! $tenant) {
            return redirect()->back()->with('error', 'Tenant not found!');
        }

        try {
            $formattedDate = Carbon::parse($request->expiry_date)->format('Y-m-d');
        } catch (\Throwable $e) {
            return redirect()->back()->with('not_permitted', 'Invalid expiry date.');
        }

        $tenant->update([
            'expiry_date' => $formattedDate,
            'subscription_type' => $request->subscription_type,
        ]);

        $subscriptionType = (string) $request->subscription_type;
        $tenantKey = (string) $tenant->id;

        $tenant->run(function () use ($formattedDate, $subscriptionType, $tenantKey) {
            try {
                if (! Schema::hasTable('general_settings')) {
                    Log::warning('Tenant expiry sync skipped: general_settings table missing', [
                        'tenant_id' => $tenantKey,
                    ]);

                    return;
                }

                if (! Schema::hasColumn('general_settings', 'expiry_date')) {
                    Log::warning('Tenant expiry sync skipped: expiry_date column missing on general_settings', [
                        'tenant_id' => $tenantKey,
                    ]);

                    return;
                }

                $updates = ['expiry_date' => $formattedDate];

                if (Schema::hasColumn('general_settings', 'subscription_type')) {
                    $updates['subscription_type'] = $subscriptionType;
                }

                if (! DB::table('general_settings')->exists()) {
                    Log::warning('Tenant expiry sync skipped: general_settings has no rows', [
                        'tenant_id' => $tenantKey,
                    ]);

                    return;
                }

                DB::table('general_settings')->update($updates);
            } catch (\Throwable $e) {
                Log::warning('Tenant expiry sync failed', [
                    'tenant_id' => $tenantKey,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        return redirect()->back()->with('message', 'Subscription renewed successfully!');
    }

    public function changePackage(Request $request)
    {
        if (! config('app.demo_unlocked')) {
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        }

        $validated = $request->validate([
            'client_id' => ['required', 'string', 'exists:tenants,id'],
            'package_id' => ['required', 'integer', 'exists:packages,id'],
        ]);

        $packageData = Package::select('permission_id', 'features')->find($validated['package_id']);
        if (! $packageData) {
            return redirect()->back()->with('not_permitted', 'Selected package was not found.');
        }

        $features = json_decode($packageData->features, true) ?: [];
        $modules = [];
        foreach (['manufacturing', 'ecommerce', 'woocommerce', 'restaurant'] as $feature) {
            if (in_array($feature, $features, true)) {
                $modules[] = $feature;
            }
        }
        $modules = count($modules) ? implode(',', $modules) : null;

        $abandoned_permission_ids = [];
        $permission_ids = [];
        $prev_permission_ids = [];

        if (! empty($packageData->permission_id)) {
            $permission_ids = explode(',', $packageData->permission_id);
        }

        $tenant = Tenant::find($validated['client_id']);
        if (! $tenant) {
            return redirect()->back()->with('not_permitted', 'Selected tenant was not found.');
        }

        $prevPackageData = Package::select('permission_id')->find($tenant->package_id);
        if (! $prevPackageData) {
            return redirect()->back()->with('not_permitted', 'Previous package was not found.');
        }
        if (! empty($prevPackageData->permission_id)) {
            $prev_permission_ids = explode(',', $prevPackageData->permission_id);
        }

        foreach ($prev_permission_ids as $prev_permission_id) {
            if (! in_array($prev_permission_id, $permission_ids)) {
                $abandoned_permission_ids[] = $prev_permission_id;
            }
        }

        try {
            $tenant->update(['package_id' => (int) $validated['package_id']]);
            $this->changePermission(
                $tenant,
                json_encode($abandoned_permission_ids),
                json_encode($permission_ids),
                (int) $validated['package_id'],
                $modules
            );

            return redirect()->back()->with('message', 'Package changed successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('not_permitted', 'Package change failed: '.$e->getMessage());
        }
    }

    protected function deleteTenantRecordAndDomains(Tenant $tenant): void
    {
        foreach ($tenant->domains as $domain) {
            $domain->delete();
        }
        $tenant->delete();
    }

    public function destroy($id)
    {
        if (! config('app.demo_unlocked')) {
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        }

        $tenant = Tenant::find($id);
        if (! $tenant) {
            return redirect()->back()->with('not_permitted', 'Selected tenant was not found.');
        }

        try {
            $this->deleteTenantRecordAndDomains($tenant);

            if (! env('WILDCARD_SUBDOMAIN')) {
                try {
                    $this->deleteSubdomain($tenant);
                } catch (\Throwable $e) {
                }
            }

            return redirect()->back()->with('message', 'Client deleted successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('not_permitted', 'Client deletion failed: '.$e->getMessage());
        }
    }

    public function deleteBySelection(Request $request)
    {
        if (! config('app.demo_unlocked')) {
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        }

        $validated = $request->validate([
            'clientsIdArray' => ['required', 'array', 'min:1'],
            'clientsIdArray.*' => ['required', 'string', 'exists:tenants,id'],
        ]);

        $deleted = 0;
        foreach ($validated['clientsIdArray'] as $tid) {
            $tenant = Tenant::find($tid);
            if (! $tenant) {
                continue;
            }
            $this->deleteTenantRecordAndDomains($tenant);
            $deleted++;
            if (! env('WILDCARD_SUBDOMAIN')) {
                try {
                    $this->deleteSubdomain($tenant);
                } catch (\Throwable $e) {
                }
            }
        }

        return response()->json([
            'message' => $deleted ? 'Clients deleted successfully' : 'No clients were deleted',
        ]);
    }
}

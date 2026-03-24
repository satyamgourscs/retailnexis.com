<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\UniqueConstraintViolationException;
use App\Models\landlord\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Models\landlord\GeneralSetting;
use App\Models\landlord\Package;
use App\Traits\TenantInfo;
use App\Services\SubdomainService;
use DB;
use Illuminate\Support\Str;
use App\Mail\TenantCreate;
use App\Models\landlord\MailSetting;
use Mail;
use Database\Seeders\Tenant\TenantDatabaseSeeder;
use Modules\Ecommerce\Database\Seeders\EcommerceDatabaseSeeder;
use Modules\Restaurant\Database\Seeders\RestaurantDatabaseSeeder;
use Illuminate\Validation\Rule;
use App\Jobs\ProvisionTenantJob;

class ClientController extends Controller
{
    use TenantInfo;
    use \App\Traits\MailInfo;

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
        // Tenant create + migrate + tenants:seed can exceed PHP's default 30s limit on local XAMPP.
        $this->raiseRuntimeLimitsForLongOperations();

        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        if (cache()->has('general_setting')) {
            $general_setting = cache()->get('general_setting');
        } else {
            $general_setting = DB::table('general_settings')->latest()->first();
        }

        $validated = $request->validate([
            'package_id' => ['required', 'integer', 'exists:packages,id'],
            'subscription_type' => ['required', Rule::in(['free trial', 'monthly', 'yearly'])],
            'company_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'tenant' => ['nullable', 'string', 'max:120'],
        ]);

        $subdomainService = app(SubdomainService::class);

        $package = Package::select('is_free_trial', 'features', 'role_permission_values')->find($validated['package_id']);
        $features = json_decode($package->features, true) ?: [];
        $modules = [];
        if (in_array('manufacturing', $features, true)) {
            $modules[] = 'manufacturing';
        }
        if (in_array('ecommerce', $features, true)) {
            $modules[] = 'ecommerce';
        }
        if (in_array('woocommerce', $features, true)) {
            $modules[] = 'woocommerce';
        }
        if (in_array('restaurant', $features, true)) {
            $modules[] = 'restaurant';
        }

        $modules = count($modules) ? implode(',', $modules) : null;

        if ($validated['subscription_type'] === 'free trial') {
            $numberOfDaysToExpired = (int) ($general_setting->free_trial_limit ?? 0);
        } elseif ($validated['subscription_type'] === 'monthly') {
            $numberOfDaysToExpired = 30;
        } else {
            $numberOfDaysToExpired = 365;
        }

        $expiryDate = date('Y-m-d', strtotime('+' . $numberOfDaysToExpired . ' days'));

        // Backend hardening: subdomain may be empty/invalid; generate from company name.
        $tenantSeed = trim((string) ($validated['tenant'] ?? ''));
        if ($tenantSeed === '') {
            $tenantSeed = (string) $validated['company_name'];
        }

        $tenantId = $subdomainService->generateUniqueSubdomain($tenantSeed);
        $centralDomain = $subdomainService->getCentralDomain();
        if ($centralDomain === '') {
            return redirect()->back()->withInput()->with(
                'not_permitted',
                'CENTRAL_DOMAIN is missing or invalid in .env. Set it (e.g. tryonedigital.com) and try again.'
            );
        }

        $fullDomain = $tenantId . '.' . $centralDomain;

        // Reuse validated inputs later in the method.
        $subscriptionType = (string) $validated['subscription_type'];
        $packageId = (int) $validated['package_id'];
        $companyName = (string) $validated['company_name'];
        $phoneNumber = (string) $validated['phone_number'];
        $userName = (string) $validated['name'];
        $password = (string) $validated['password'];
        $email = (string) $validated['email'];

        $tenant = null;
        $subdomainProvisioned = false;
        try {
            //creating tenant
            // Ensure central records are created in a DB transaction.
            for ($attempt = 0; $attempt < 3; $attempt++) {
                try {
                    DB::transaction(function () use (&$tenant, &$tenantId, &$fullDomain) {
                        $tenant = Tenant::create(['id' => $tenantId]);
                        $tenant->domains()->create(['domain' => $fullDomain]);
                    });
                    break;
                } catch (UniqueConstraintViolationException $e) {
                    // Race condition: generate next available suffix and retry.
                    $tenantId = $subdomainService->generateUniqueSubdomain($tenantSeed);
                    $fullDomain = $tenantId . '.' . $centralDomain;
                    $tenant = null;
                }
            }

            if (!($tenant instanceof Tenant)) {
                return redirect()->back()->withInput()->with(
                    'not_permitted',
                    'Client creation failed: unable to reserve a unique subdomain/domain.'
                );
            }

            // FAST RESPONSE: stop here and dispatch heavy provisioning in background.
            // Central tenant/domain records are created synchronously and marked as pending.
            $tenant->update([
                'package_id' => $packageId,
                'subscription_type' => $subscriptionType,
                'company_name' => $companyName,
                'phone_number' => $phoneNumber,
                'email' => $email,
                'expiry_date' => $expiryDate,
                'username' => $userName,
                'provisioning_status' => 'pending',
                'provisioning_error' => null,
                'provisioning_started_at' => null,
                'provisioning_completed_at' => null,
            ]);

            $tenantPayload = [
                'package_id' => $packageId,
                'subscription_type' => $subscriptionType,
                'company_name' => $companyName,
                'phone_number' => $phoneNumber,
                'username' => $userName,
                'email' => $email,
                // Plain password is kept for the existing welcome-email behavior.
                'password' => (string) $password,
                'expiry_date' => $expiryDate,
                'modules' => $modules,
                'site_title' => (string) $general_setting->site_title,
                'site_logo' => (string) $general_setting->site_logo,
                'developed_by' => (string) $general_setting->developed_by,
                'superadmin_email' => (string) $general_setting->email,
            ];

            ProvisionTenantJob::dispatch($tenantId, $fullDomain, $tenantPayload);

            return redirect()->back()->with('message', 'Client created and provisioning started.');

            // Re-run migrations after tenant creation. The automatic MigrateDatabase job can be interrupted
            // (e.g. PHP max_execution_time), leaving a DB without Spatie permission tables before seed runs.
            $migrateExit = Artisan::call('tenants:migrate', [
                '--tenants' => $tenantId,
                '--force' => true,
            ]);
            if ($migrateExit !== 0) {
                throw new \RuntimeException('tenants:migrate failed with exit code ' . $migrateExit);
            }

            $missingPermissionTables = [];
            $tenant->run(function () use (&$missingPermissionTables) {
                foreach (['roles', 'permissions', 'role_has_permissions'] as $table) {
                    if (! Schema::hasTable($table)) {
                        $missingPermissionTables[] = $table;
                    }
                }
            });

            if ($missingPermissionTables !== []) {
                if ($subdomainProvisioned && !env('WILDCARD_SUBDOMAIN')) {
                    try {
                        $this->deleteSubdomain($tenant);
                    } catch (\Throwable $e) {
                        // Best-effort external cleanup.
                    }
                }
                $this->deleteTenantRecordAndDomains($tenant);

                return redirect()->back()->withInput()->with(
                    'not_permitted',
                    'Tenant database setup failed: missing table(s) '.implode(', ', $missingPermissionTables)
                    .'. This often happens if the first migration step was cut off by a timeout. '
                    .'The incomplete client was removed — submit the form again. '
                    .'If the problem persists, from the project folder run: php artisan tenants:migrate --tenants='.$tenantId
                );
            }

        ///////////////Start if someone wants ecommerce demo as his own demo////////////////
        // if (isset($modules) && str_contains($modules, "ecommerce") && file_exists(public_path('ecommerce_demo.sql'))) {
        //     $tenant->run(function () {
        //         DB::unprepared(file_get_contents(public_path('ecommerce_demo.sql')));
        //     });
        // }
        ///////////////End if someone wants ecommerce demo as his own demo////////////////

            //Start set tenant specific data for TenantDatabaseSeeder
            $packageData = Package::find($packageId);
            $pack_perm_role_pairs = explode('),(', trim((string) $packageData->role_permission_values, '()'));
        // Convert each pair into an associative array
            if ($pack_perm_role_pairs != [""]) {
                $package_permissions_role = array_map(function ($pk_perm_role_p) {
                    [$permission_id, $role_id] = explode(',', $pk_perm_role_p); // Split the pair
                    return [
                        'permission_id' => (int) $permission_id, // Cast to int
                        'role_id' => (int) $role_id,             // Cast to int
                    ];
                }, $pack_perm_role_pairs);
            } else {
                $package_permissions_role = [];
            }

            $tenantData = [
            //set general_setting information
            'site_title' => $general_setting->site_title,
            'site_logo' => $general_setting->site_logo,
            'package_id' => $packageId,
            'subscription_type' => $subscriptionType,
            'developed_by' => $general_setting->developed_by,
            'modules' => $modules,
            'expiry_date' => $expiryDate,
            //set user information
            'name' => $userName,
            'email' => $email,
            'password' => bcrypt($password),
            'phone' => $phoneNumber,
            'company_name' => $companyName,
            //set permission info
            'package_permissions_role' => $package_permissions_role,
        ];
        //End set tenant specific data for TenantDatabaseSeeder and call running TenantDatabaseSeeder

        //Start running TenantDatabaseSeeder
            TenantDatabaseSeeder::$tenantData = $tenantData;
            $seedExit = Artisan::call('tenants:seed', [
                '--tenants' => $tenantId,
                '--force' => true,
            ]);
            if ($seedExit !== 0) {
                throw new \RuntimeException('tenants:seed failed with exit code ' . $seedExit);
            }
        //End running TenantDatabaseSeeder

            copy(public_path("landlord/images/logo/") . $general_setting->site_logo, public_path("logo/") . $general_setting->site_logo);

        //Start running Ecommerce seeder for tenant if package has ecommerce module
            if (isset($modules) && str_contains($modules, 'ecommerce')) {
                $ecommerceExit = Artisan::call('tenants:seed', [
                    '--tenants' => $tenantId,
                    '--class' => EcommerceDatabaseSeeder::class,
                    '--force' => true,
                ]);
                if ($ecommerceExit !== 0) {
                    throw new \RuntimeException('EcommerceDatabaseSeeder failed with exit code ' . $ecommerceExit);
                }

            //Update slug column on category,brand,product table as this is needed for ecommerce
                $tenant->run(function () {
                    $this->brandSlug();
                    $this->categorySlug();
                    $this->productSlug();

                DB::table('categories')->whereIn('id', [1, 6, 12, 23, 29, 30, 31, 33, 39])->update([
                    'icon' => DB::raw("
                        CASE
                            WHEN id = 1 THEN '20240117121500.png'
                            WHEN id = 6 THEN '20240117121330.png'
                            WHEN id = 12 THEN '20240117121400.png'
                            WHEN id = 23 THEN '20240117121523.png'
                            WHEN id = 29 THEN '20240117121304.png'
                            WHEN id = 30 THEN '20240117121238.png'
                            WHEN id = 31 THEN '20240117122452.png'
                            WHEN id = 33 THEN '20240117121224.png'
                            WHEN id = 39 THEN '20240204050037.png'
                        END
                    ")
                ]);

                DB::table('products')->update(['is_online' => 1]);
            });

                copy(public_path("logo/") . $general_setting->site_logo, public_path("frontend/images/") . $general_setting->site_logo);
            }
        //End running Ecommerce seeder if package has ecommerce module

        //Start running Restaurant seeder for tenant if package has Restaurant module
            if (isset($modules) && str_contains($modules, 'restaurant')) {
                $restaurantExit = Artisan::call('tenants:seed', [
                    '--tenants' => $tenantId,
                    '--class' => RestaurantDatabaseSeeder::class,
                    '--force' => true,
                ]);
                if ($restaurantExit !== 0) {
                    throw new \RuntimeException('RestaurantDatabaseSeeder failed with exit code ' . $restaurantExit);
                }
            }
        //End running Restaurant seeder if package has Restaurant module


            if (!env('WILDCARD_SUBDOMAIN')) {
                $this->addSubdomain($tenant);
                $subdomainProvisioned = true;
            }

        //updating tenant others information on landlord DB
            $tenant->update([
                'package_id' => $packageId,
                'subscription_type' => $subscriptionType,
                'company_name' => $companyName,
                'phone_number' => $phoneNumber,
                'email' => $email,
                'expiry_date' => $expiryDate,
                // Useful for uniqueness checks and admin display/analytics.
                'username' => $userName,
            ]);


            $message = 'Client created successfully.';
            //sending welcome message to tenant
            $mail_setting = MailSetting::latest()->first();
            if ($mail_setting) {
                $this->setMailInfo($mail_setting);
                $mail_data['email'] = $email;
                $mail_data['company_name'] = $companyName;
                $mail_data['superadmin_company_name'] = $general_setting->site_title;
                $mail_data['subdomain'] = $tenantId;
                $mail_data['name'] = $userName;
                $mail_data['password'] = $password;
                $mail_data['superadmin_email'] = $general_setting->email;
                try {
                    Mail::to($mail_data['email'])->send(new TenantCreate($mail_data));
                } catch (\Exception $e) {
                    $message = 'Client created successfully. Please setup your <a href="mail_setting">mail setting</a> to send mail.';
                }
            }

            return redirect()->back()->with('message', $message);
        } catch (\Throwable $e) {
            // On failure, remove half-created tenant/domain records to avoid broken clients.
            if ($tenant instanceof Tenant) {
                try {
                    if ($subdomainProvisioned && !env('WILDCARD_SUBDOMAIN')) {
                        try {
                            $this->deleteSubdomain($tenant);
                        } catch (\Throwable $apiEx) {
                            // Best-effort external cleanup.
                        }
                    }
                    $this->deleteTenantRecordAndDomains($tenant);
                } catch (\Throwable $cleanupEx) {
                    // Ignore cleanup failure and report original error to user.
                }
            }

            return redirect()->back()->withInput()->with(
                'not_permitted',
                'Client creation failed on server: '.$e->getMessage()
            );
        }
    }

    public function checkSubdomain(Request $request)
    {
        $request->validate([
            'value' => ['required', 'string', 'max:120'],
            'ignoreTenantId' => ['nullable', 'string', 'max:120'],
        ]);

        $subdomainService = app(SubdomainService::class);

        $value = (string) $request->query('value');
        $sanitized = $subdomainService->sanitizeSubdomain($value);
        if ($sanitized === '') {
            $sanitized = 'client';
        }

        $ignoreTenantId = $request->query('ignoreTenantId');
        $suggested = $subdomainService->generateUniqueSubdomain($value, $ignoreTenantId);

        return response()->json([
            'ok' => true,
            'sanitized_subdomain' => $sanitized,
            'suggested_subdomain' => $suggested,
            'is_available' => $suggested === $sanitized,
        ]);
    }

    /**
     * Allow long-running superadmin operations (tenant DB + migrations + seeders).
     */
    /**
     * Remove tenant row + domains (triggers tenant DB deletion via Tenancy events).
     */
    protected function deleteTenantRecordAndDomains(Tenant $tenant): void
    {
        foreach ($tenant->domains as $domain) {
            $domain->delete();
        }
        $tenant->delete();
    }

    protected function raiseRuntimeLimitsForLongOperations(): void
    {
        try {
            @set_time_limit(0);
            @ini_set('max_execution_time', '0');
            if (function_exists('ini_get') && function_exists('ini_set')) {
                $current = ini_get('memory_limit');
                if (is_string($current) && preg_match('/^\s*(\d+)\s*M\s*$/i', $current, $m) && (int) $m[1] < 512) {
                    @ini_set('memory_limit', '1024M');
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }

    public function addCustomDomain(Request $request)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $validated = $request->validate([
            'id' => ['required', 'string', 'exists:tenants,id'],
            'domain' => ['required', 'string', 'max:255'],
        ]);

        $tenantId = trim((string) $validated['id']);
        $rawDomain = trim((string) $validated['domain']);

        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            return redirect()->back()->with('not_permitted', 'Selected tenant was not found.');
        }

        // Normalize user input: remove protocol, path, port, and lowercase.
        $domain = strtolower($rawDomain);
        $domain = preg_replace('#^https?://#i', '', $domain);
        $domain = preg_replace('#/.*$#', '', $domain);
        $domain = preg_replace('/:\d+$/', '', $domain);
        $domain = trim($domain, " \t\n\r\0\x0B.");

        // Basic host validation.
        if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) || !str_contains($domain, '.')) {
            return redirect()->back()->with('not_permitted', 'Please enter a valid domain (example.com).');
        }

        // Architecture guard: never allow central host to be registered as a tenant custom domain.
        $subdomainService = app(SubdomainService::class);
        $centralDomain = $subdomainService->getCentralDomain();
        if ($centralDomain !== '') {
            $domainLower = strtolower($domain);
            if ($domainLower === strtolower($centralDomain) || $domainLower === 'www.' . strtolower($centralDomain)) {
                return redirect()->back()->with('not_permitted', 'Central domain cannot be used as a tenant custom domain.');
            }
        }

        $exists = DB::table('domains')->where('domain', $domain)->exists();
        if ($exists) {
            return redirect()->back()->with('not_permitted', 'This custom domain is already registered.');
        }

        $insertedDomainId = null;
        DB::transaction(function () use ($tenantId, $domain, &$insertedDomainId) {
            $insertedDomainId = DB::table('domains')->insertGetId([
                'domain' => $domain,
                'tenant_id' => $tenantId,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        });

        // Try creating cPanel addon domain when credentials are present.
        if (env('SERVER_TYPE') === 'cpanel' && env('CPANEL_USER_NAME') && env('CPANEL_API_KEY') && env('CENTRAL_DOMAIN')) {
            $panelResult = $this->createAddonDomainInCpanel($domain);
            if (!($panelResult['ok'] ?? false)) {
                // Roll back the app-level domain insert so we don't leave inconsistent state.
                if ($insertedDomainId) {
                    DB::table('domains')->where('id', $insertedDomainId)->delete();
                }

                return redirect()->back()->with(
                    'not_permitted',
                    'Custom domain not fully created: cPanel addon-domain creation failed: '.($panelResult['message'] ?? 'Unknown error')
                );
            }
        }

        return redirect()->back()->with('message', 'Custom domain created successfully');
    }

    /**
     * Try to create addon domain in cPanel (best effort).
     */
    protected function createAddonDomainInCpanel(string $domain): array
    {
        try {
            $host = trim((string) env('CENTRAL_DOMAIN'));
            $cpUser = trim((string) env('CPANEL_USER_NAME'));
            $cpKey = trim((string) env('CPANEL_API_KEY'));
            if ($host === '' || $cpUser === '' || $cpKey === '') {
                return ['ok' => false, 'message' => 'Missing cPanel credentials in .env'];
            }

            // cPanel requires a unique subdomain label for addon-domain mapping.
            $firstLabel = explode('.', $domain)[0] ?? 'site';
            $subdomainLabel = Str::slug($firstLabel, '') ?: 'site';
            $subdomainLabel = substr($subdomainLabel, 0, 16) . rand(10, 99);

            // Keep document root simple and deterministic.
            $docRoot = 'public_html/' . $domain;
            $url = "https://{$host}:2083/json-api/cpanel"
                . "?cpanel_jsonapi_apiversion=2"
                . "&cpanel_jsonapi_module=AddonDomain"
                . "&cpanel_jsonapi_func=addaddondomain"
                . "&newdomain=" . urlencode($domain)
                . "&subdomain=" . urlencode($subdomainLabel)
                . "&dir=" . urlencode($docRoot);

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Authorization: cpanel {$cpUser}:{$cpKey}",
                "Content-Type: text/plain",
            ]);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            if ($resp === false) {
                $err = curl_error($curl);
                curl_close($curl);
                return ['ok' => false, 'message' => $err ?: 'Unknown cURL error'];
            }
            curl_close($curl);

            $decoded = json_decode($resp, true);
            if (is_array($decoded)) {
                $resultData = $decoded['cpanelresult']['data'][0] ?? [];
                $ok = isset($resultData['result']) ? (int) $resultData['result'] === 1 : true;
                if (!$ok) {
                    $reason = $resultData['reason'] ?? 'Unknown cPanel API response';
                    return ['ok' => false, 'message' => (string) $reason];
                }
            }

            return ['ok' => true, 'message' => 'ok'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public function renew(Request $request)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $validated = $request->validate([
            'id' => ['required', 'string', 'exists:tenants,id'],
            'expiry_date' => ['required', 'string'],
            'subscription_type' => ['required', Rule::in(['monthly', 'yearly'])],
        ]);

        $tenant = Tenant::find($validated['id']);
        if (! $tenant) {
            return redirect()->back()->with('not_permitted', 'Selected tenant was not found.');
        }

        try {
            $expiryDate = date('Y-m-d', strtotime((string) $validated['expiry_date']));
            if ($expiryDate === '1970-01-01' && !str_contains((string) $validated['expiry_date'], '1970')) {
                // Best-effort safeguard against invalid date strings.
                return redirect()->back()->with('not_permitted', 'Please enter a valid expiry date.');
            }

            $subscriptionType = (string) $validated['subscription_type'];

            $tenant->update(['expiry_date' => $expiryDate, 'subscription_type' => $subscriptionType]);
            $tenant->run(function () use ($expiryDate, $subscriptionType) {
                GeneralSetting::latest()->first()->update([
                    'expiry_date' => $expiryDate,
                    'subscription_type' => $subscriptionType,
                ]);
            });

            return redirect()->back()->with('message', 'Subscription renewed successfully!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('not_permitted', 'Subscription renewal failed: '.$e->getMessage());
        }
    }

    public function changePackage(Request $request)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
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
        if (in_array('manufacturing', $features, true)) {
            $modules[] = 'manufacturing';
        }
        if (in_array('ecommerce', $features, true)) {
            $modules[] = 'ecommerce';
        }
        if (in_array('woocommerce', $features, true)) {
            $modules[] = 'woocommerce';
        }
        if (in_array('restaurant', $features, true)) {
            $modules[] = 'restaurant';
        }

        $modules = count($modules) ? implode(',', $modules) : null;

        $abandoned_permission_ids = [];
        $permission_ids = [];
        $prev_permission_ids = [];

        if (!empty($packageData->permission_id)) {
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
        if (!empty($prevPackageData->permission_id)) {
            $prev_permission_ids = explode(',', $prevPackageData->permission_id);
        }

        //collecting permission ids which needs to be deleted
        foreach ($prev_permission_ids as $key => $prev_permission_id) {
            if (!in_array($prev_permission_id, $permission_ids)) {
                $abandoned_permission_ids[] = $prev_permission_id;
            }
        }
        //updating tenant package id in superadmin db
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

    public function destroy($id)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $tenant = Tenant::find($id);
        if (! $tenant) {
            return redirect()->back()->with('not_permitted', 'Selected tenant was not found.');
        }

        try {
            $this->deleteTenantRecordAndDomains($tenant);

            if (!env('WILDCARD_SUBDOMAIN')) {
                try {
                    $this->deleteSubdomain($tenant);
                } catch (\Throwable $e) {
                    // External API failure shouldn't block DB deletion.
                }
            }

            return redirect()->back()->with('message', 'Client deleted successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('not_permitted', 'Client deletion failed: '.$e->getMessage());
        }
    }

    public function deleteBySelection(Request $request)
    {
        if (!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');

        $validated = $request->validate([
            'clientsIdArray' => ['required', 'array', 'min:1'],
            'clientsIdArray.*' => ['required', 'string', 'exists:tenants,id'],
        ]);

        $deleted = 0;
        foreach ($validated['clientsIdArray'] as $id) {
            $tenant = Tenant::find($id);
            if (! $tenant) {
                continue;
            }

            $this->deleteTenantRecordAndDomains($tenant);
            $deleted++;

            if (!env('WILDCARD_SUBDOMAIN')) {
                try {
                    $this->deleteSubdomain($tenant);
                } catch (\Throwable $e) {
                    // External API failure shouldn't block DB deletion.
                }
            }
        }

        return response()->json([
            'message' => $deleted ? 'Clients deleted successfully' : 'No clients were deleted',
        ]);
    }
}

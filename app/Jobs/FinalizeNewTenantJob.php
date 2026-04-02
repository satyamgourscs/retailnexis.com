<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\TenantCreate;
use App\Models\landlord\MailSetting;
use App\Models\landlord\Tenant;
use App\Traits\MailInfo;
use App\Traits\TenantInfo;
use Database\Seeders\Tenant\TenantDatabaseSeeder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use RuntimeException;

class FinalizeNewTenantJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use MailInfo;
    use TenantInfo;

    public int $timeout = 600;

    public function __construct(
        public string $tenantId
    ) {
    }

    public function handle(): void
    {
        $centralConn = (string) config('tenancy.database.central_connection');

        try {
            Log::info('FINALIZE START', ['tenant' => $this->tenantId]);

            @set_time_limit(0);

            Log::info('FINAL STEP 1 - JOB START', [
                'tenant' => $this->tenantId,
            ]);

            if (tenancy()->initialized) {
                tenancy()->end();
            }

            $tenantKey = (string) $this->tenantId;
            $cacheKey = 'tenant_finalize_'.$tenantKey;

            $data = Cache::store('central')->get($cacheKey);

            Log::info('FINAL STEP 2 - CACHE DATA', [
                'key' => $cacheKey,
                'data' => $data,
            ]);

            if (! $data || ! is_array($data)) {
                throw new RuntimeException(
                    "Finalize: central cache miss for key [{$cacheKey}] tenant [{$tenantKey}]"
                );
            }

            if (! isset($data['package_id']) || ! isset($data['subscription_type'])) {
                throw new RuntimeException(
                    "Finalize: invalid payload for tenant [{$tenantKey}] (missing package_id or subscription_type)"
                );
            }

            $tenant = Tenant::on($centralConn)->find($tenantKey);
            if (! $tenant) {
                throw new RuntimeException("Finalize: landlord tenant row not found [{$tenantKey}]");
            }

            Log::info('FINAL STEP 3 - BEFORE UPDATE', $tenant->toArray());

            $subscriptionType = (string) $data['subscription_type'];
            if (! empty($data['expiry_date'])) {
                $expiryDate = Carbon::parse((string) $data['expiry_date'])->format('Y-m-d');
            } elseif ($subscriptionType === 'yearly') {
                $expiryDate = now()->addDays(365)->format('Y-m-d');
            } elseif ($subscriptionType === 'monthly') {
                $expiryDate = now()->addDays(30)->format('Y-m-d');
            } else {
                $expiryDate = now()->addDays(30)->format('Y-m-d');
            }

            $packagesInt = (int) $data['package_id'];

            $saved = $tenant->forceFill([
                'package_id' => $packagesInt,
                'subscription_type' => $subscriptionType,
                'expiry_date' => $expiryDate,
                'company_name' => $data['company_name'] ?? null,
                'phone_number' => $data['phone_number'] ?? null,
                'email' => $data['email'] ?? null,
            ])->setConnection($centralConn)->save();

            Log::info('FINAL STEP 3b - ELOQUENT SAVE', ['saved' => $saved]);

            $row = DB::connection($centralConn)->table('tenants')->where('id', $tenantKey)->first();
            $dataColumn = [];
            if ($row && $row->data !== null && $row->data !== '') {
                $decoded = json_decode((string) $row->data, true);
                $dataColumn = is_array($decoded) ? $decoded : [];
            }

            $dataColumn['package_id'] = $packagesInt;
            $dataColumn['subscription_type'] = $subscriptionType;
            $dataColumn['company_name'] = $data['company_name'] ?? null;
            $dataColumn['phone_number'] = $data['phone_number'] ?? null;
            $dataColumn['email'] = $data['email'] ?? null;

            $affected = DB::connection($centralConn)->table('tenants')->where('id', $tenantKey)->update([
                'data' => json_encode($dataColumn),
                'expiry_date' => $expiryDate,
                'updated_at' => now(),
            ]);

            Log::info('FINAL STEP 3c - RAW LANDLORD UPDATE', ['rows' => $affected]);

            $updated = Tenant::on($centralConn)->find($tenantKey);
            Log::info('FINAL STEP 4 - AFTER UPDATE', $updated ? $updated->toArray() : ['_missing' => true]);

            $tenant = $updated ?? Tenant::on($centralConn)->find($tenantKey);
            if (! $tenant) {
                throw new RuntimeException("Finalize: tenant missing after update [{$tenantKey}]");
            }

            // Safety net: apply any pending migrations before seed (never rely on partial DBs).
            $migrateExit = Artisan::call('tenants:migrate', [
                '--tenants' => [$tenantKey],
                '--force' => true,
            ]);
            if ($migrateExit !== 0) {
                throw new RuntimeException(
                    'Finalize: tenants:migrate before seed failed (code '.$migrateExit.'): '.Artisan::output()
                );
            }

            $tenant->run(function () use ($tenantKey): void {
                if (config('cache.default') === 'database' && ! Schema::hasTable('cache')) {
                    throw new RuntimeException(
                        "Finalize: tenant [{$tenantKey}] is missing the cache table after migrate."
                    );
                }
            });

            tenancy()->initialize($tenant);

            try {
                $modules = $data['modules'] ?? null;
                $tenantData = $data['tenantData'] ?? [];
                $siteLogo = (string) ($data['site_logo'] ?? '');

                TenantDatabaseSeeder::$tenantData = is_array($tenantData) ? $tenantData : [];

                $seedExit = Artisan::call('tenants:seed', [
                    '--tenants' => [$tenantKey],
                    '--force' => true,
                ]);
                Log::info('FINALIZE SEED PRIMARY', [
                    'tenant' => $tenantKey,
                    'exit' => $seedExit,
                    'output' => Artisan::output(),
                ]);
                if ($seedExit !== 0) {
                    throw new RuntimeException('tenants:seed exited '.$seedExit.': '.Artisan::output());
                }

                if ($siteLogo !== '' && file_exists(public_path('landlord/images/logo/').$siteLogo)) {
                    @copy(
                        public_path('landlord/images/logo/').$siteLogo,
                        public_path('logo/').$siteLogo
                    );
                }

                $modulesStr = $modules !== null && $modules !== '' ? (string) $modules : '';
                $ecomClass = 'Modules\Ecommerce\Database\Seeders\EcommerceDatabaseSeeder';
                if ($modulesStr !== '' && str_contains($modulesStr, 'ecommerce') && class_exists($ecomClass)) {
                    $eExit = Artisan::call('tenants:seed', [
                        '--tenants' => [$tenantKey],
                        '--class' => $ecomClass,
                        '--force' => true,
                    ]);
                    Log::info('FINALIZE SEED ECOMMERCE', ['exit' => $eExit, 'output' => Artisan::output()]);
                    if ($eExit !== 0) {
                        throw new RuntimeException('ecommerce seed exited '.$eExit.': '.Artisan::output());
                    }

                    $tenant->run(function (): void {
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
                            "),
                        ]);

                        DB::table('products')->update(['is_online' => 1]);
                    });

                    if ($siteLogo !== '' && file_exists(public_path('logo/').$siteLogo)) {
                        @copy(
                            public_path('logo/').$siteLogo,
                            public_path('frontend/images/').$siteLogo
                        );
                    }
                }

                $restClass = 'Modules\Restaurant\Database\Seeders\RestaurantDatabaseSeeder';
                if ($modulesStr !== '' && str_contains($modulesStr, 'restaurant') && class_exists($restClass)) {
                    $rExit = Artisan::call('tenants:seed', [
                        '--tenants' => [$tenantKey],
                        '--class' => $restClass,
                        '--force' => true,
                    ]);
                    Log::info('FINALIZE SEED RESTAURANT', ['exit' => $rExit, 'output' => Artisan::output()]);
                    if ($rExit !== 0) {
                        throw new RuntimeException('restaurant seed exited '.$rExit.': '.Artisan::output());
                    }
                }
            } finally {
                tenancy()->end();
            }

            $rowAfterSeed = DB::connection($centralConn)->table('tenants')->where('id', $tenantKey)->first();
            $dataAfterSeed = [];
            if ($rowAfterSeed && $rowAfterSeed->data !== null && $rowAfterSeed->data !== '') {
                $decodedAfter = json_decode((string) $rowAfterSeed->data, true);
                $dataAfterSeed = is_array($decodedAfter) ? $decodedAfter : [];
            }
            $dataAfterSeed['package_id'] = $packagesInt;
            $dataAfterSeed['subscription_type'] = $subscriptionType;
            $dataAfterSeed['company_name'] = $data['company_name'] ?? null;
            $dataAfterSeed['phone_number'] = $data['phone_number'] ?? null;
            $dataAfterSeed['email'] = $data['email'] ?? null;

            DB::connection($centralConn)->table('tenants')->where('id', $tenantKey)->update([
                'data' => json_encode($dataAfterSeed),
                'expiry_date' => $expiryDate,
                'updated_at' => now(),
            ]);

            Log::info('FINAL STEP 5 - AFTER SEED LANDLORD REAPPLY', ['tenant' => $tenantKey]);

            if (! env('WILDCARD_SUBDOMAIN')) {
                $fresh = Tenant::on($centralConn)->find($tenantKey);
                if ($fresh) {
                    $this->addSubdomain($fresh);
                }
            }

            $mail_setting = MailSetting::on($centralConn)->latest('id')->first();
            if (! $mail_setting) {
                throw new RuntimeException('Finalize: MailSetting row missing on landlord DB (cannot send welcome mail)');
            }

            $this->setMailInfo($mail_setting);
            $mail_data = [
                'email' => $data['email'] ?? '',
                'company_name' => $data['company_name'] ?? '',
                'superadmin_company_name' => $data['site_title'] ?? '',
                'subdomain' => $data['tenant_subdomain'] ?? $tenantKey,
                'name' => $data['name'] ?? '',
                'password' => $data['password_plain'] ?? '',
                'superadmin_email' => $data['superadmin_email'] ?? '',
            ];

            Mail::to($mail_data['email'])->send(new TenantCreate($mail_data));

            Cache::store('central')->forget($cacheKey);

            Log::info('FINALIZE DONE', ['tenant' => $tenantKey]);
        } catch (\Throwable $e) {
            Log::error('FINALIZE ERROR', [
                'tenant' => $this->tenantId,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

<?php

namespace App\Jobs;

use App\Models\landlord\GeneralSetting;
use App\Models\landlord\Package;
use App\Models\landlord\Tenant;
use App\Mail\TenantCreate;
use App\Models\landlord\MailSetting;
use App\Traits\TenantInfo;
use App\Traits\MailInfo;
use Database\Seeders\Tenant\TenantDatabaseSeeder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Modules\Ecommerce\Database\Seeders\EcommerceDatabaseSeeder;
use Modules\Restaurant\Database\Seeders\RestaurantDatabaseSeeder;
use RuntimeException;
use Throwable;

class ProvisionTenantJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use TenantInfo;
    use MailInfo;

    public int $tries = 5;
    public int $timeout = 1800; // seconds

    /**
     * @param array<string,mixed> $tenantPayload
     */
    public function __construct(
        public string $tenantId,
        public string $fullDomain,
        public array $tenantPayload
    ) {
    }

    public function handle(): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (! $tenant) {
            return;
        }

        DB::table('tenants')->where('id', $this->tenantId)->update([
            'provisioning_status' => 'provisioning',
            'provisioning_started_at' => now(),
            'provisioning_error' => null,
        ]);

        try {
            // Ensure DB exists + migrations were not interrupted.
            $missingTables = [];
            $tenant->run(function () use (&$missingTables) {
                try {
                    foreach (['roles', 'permissions', 'general_settings', 'users'] as $table) {
                        if (! Schema::hasTable($table)) {
                            $missingTables[] = $table;
                        }
                    }
                } catch (Throwable $e) {
                    // Tenant DB/connection may not exist yet.
                    $missingTables = ['roles', 'permissions', 'general_settings', 'users'];
                }
            });

            if ($missingTables !== []) {
                $migrateExit = Artisan::call('tenants:migrate', [
                    '--tenants' => $this->tenantId,
                    '--force' => true,
                ]);

                if ($migrateExit !== 0) {
                    throw new RuntimeException('tenants:migrate failed with exit code ' . $migrateExit);
                }
            }

            $packageId = (int) ($this->tenantPayload['package_id'] ?? 0);
            $subscriptionType = (string) ($this->tenantPayload['subscription_type'] ?? '');
            $companyName = (string) ($this->tenantPayload['company_name'] ?? '');
            $phoneNumber = (string) ($this->tenantPayload['phone_number'] ?? '');
            $userName = (string) ($this->tenantPayload['username'] ?? '');
            $email = (string) ($this->tenantPayload['email'] ?? '');
            $plainPassword = (string) ($this->tenantPayload['password'] ?? '');
            $expiryDate = (string) ($this->tenantPayload['expiry_date'] ?? '');
            $modules = (string) ($this->tenantPayload['modules'] ?? '');
            $siteTitle = (string) ($this->tenantPayload['site_title'] ?? '');
            $siteLogo = (string) ($this->tenantPayload['site_logo'] ?? '');
            $developedBy = (string) ($this->tenantPayload['developed_by'] ?? '');

            $package = Package::find($packageId);
            if (! $package) {
                throw new RuntimeException('Package not found for provisioning.');
            }

            // Build modules string from package features if not provided.
            if ($modules === '') {
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
            }

            // Prepare role_permission mapping for TenantDatabaseSeeder.
            $pack_perm_role_pairs = explode('),(', trim((string) $package->role_permission_values, '()'));
            if ($pack_perm_role_pairs != ['']) {
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

            if ($plainPassword === '') {
                throw new RuntimeException('Tenant password missing for provisioning.');
            }

            $tenantData = [
                'site_title' => $siteTitle,
                'site_logo' => $siteLogo,
                'package_id' => $packageId,
                'subscription_type' => $subscriptionType,
                'developed_by' => $developedBy,
                'modules' => $modules,
                'expiry_date' => $expiryDate,
                'name' => $userName,
                'email' => $email,
                'password' => bcrypt($plainPassword),
                'phone' => $phoneNumber,
                'company_name' => $companyName,
                'package_permissions_role' => $package_permissions_role,
            ];

            TenantDatabaseSeeder::$tenantData = $tenantData;
            $seedExit = Artisan::call('tenants:seed', [
                '--tenants' => $this->tenantId,
                '--force' => true,
            ]);
            if ($seedExit !== 0) {
                throw new RuntimeException('tenants:seed failed with exit code ' . $seedExit);
            }

            // Copy logos (best-effort; should not mark provisioning failed on filesystem issues).
            try {
                copy(public_path('landlord/images/logo/') . $siteLogo, public_path('logo/') . $siteLogo);
            } catch (Throwable $e) {
                // ignore
            }

            // Optional module seeders.
            if (isset($modules) && is_string($modules) && str_contains($modules, 'ecommerce')) {
                $ecommerceExit = Artisan::call('tenants:seed', [
                    '--tenants' => $this->tenantId,
                    '--class' => EcommerceDatabaseSeeder::class,
                    '--force' => true,
                ]);
                if ($ecommerceExit !== 0) {
                    throw new RuntimeException('EcommerceDatabaseSeeder failed with exit code ' . $ecommerceExit);
                }

                // Needed after ecommerce seeding.
                $tenant->run(function () {
                    $this->brandSlug();
                    $this->categorySlug();
                    $this->productSlug();

                    DB::table('categories')
                        ->whereIn('id', [1, 6, 12, 23, 29, 30, 31, 33, 39])
                        ->update([
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

                try {
                    copy(public_path('logo/') . $siteLogo, public_path('frontend/images/') . $siteLogo);
                } catch (Throwable $e) {
                    // ignore
                }
            }

            if (isset($modules) && is_string($modules) && str_contains($modules, 'restaurant')) {
                $restaurantExit = Artisan::call('tenants:seed', [
                    '--tenants' => $this->tenantId,
                    '--class' => RestaurantDatabaseSeeder::class,
                    '--force' => true,
                ]);
                if ($restaurantExit !== 0) {
                    throw new RuntimeException('RestaurantDatabaseSeeder failed with exit code ' . $restaurantExit);
                }
            }

            if (! env('WILDCARD_SUBDOMAIN')) {
                // External provisioning can fail; if it fails, mark tenant as failed.
                $this->addSubdomain($tenant);
            }

            DB::table('tenants')->where('id', $this->tenantId)->update([
                'package_id' => $packageId,
                'subscription_type' => $subscriptionType,
                'company_name' => $companyName,
                'phone_number' => $phoneNumber,
                'email' => $email,
                'expiry_date' => $expiryDate,
                'username' => $userName,
                'provisioning_status' => 'active',
                'provisioning_completed_at' => now(),
                'provisioning_error' => null,
            ]);

            // Send welcome email (best-effort).
            $mail_setting = MailSetting::latest()->first();
            if ($mail_setting) {
                try {
                    $this->setMailInfo($mail_setting);
                    $mail_data['email'] = $email;
                    $mail_data['company_name'] = $companyName;
                    $mail_data['superadmin_company_name'] = $siteTitle;
                    $mail_data['subdomain'] = $this->tenantId;
                    $mail_data['name'] = $userName;
                    $mail_data['password'] = $plainPassword;
                    $mail_data['superadmin_email'] = (string) ($this->tenantPayload['superadmin_email'] ?? env('APP_EMAIL', ''));
                    Mail::to($mail_data['email'])->send(new TenantCreate($mail_data));
                } catch (Throwable $e) {
                    // ignore
                }
            }
        } catch (Throwable $e) {
            Log::error('ProvisionTenantJob failed', [
                'tenant_id' => $this->tenantId,
                'error' => $e->getMessage(),
            ]);

            DB::table('tenants')->where('id', $this->tenantId)->update([
                'provisioning_status' => 'failed',
                'provisioning_completed_at' => now(),
                'provisioning_error' => mb_substr($e->getMessage(), 0, 2000),
            ]);

            // Let the queue retry.
            throw $e;
        }
    }
}


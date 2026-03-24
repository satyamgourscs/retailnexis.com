<?php

namespace App\Console\Commands;

use App\Models\landlord\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BrandingCleanupCommand extends Command
{
    protected $signature = 'branding:cleanup-tenants
                            {--dry-run : Only report changes, do not write}
                            {--only-demo : Clean only tenant with id or domain containing "demo"}';

    protected $description = 'Remove legacy Lioncoders/Superadmin branding from tenant DBs';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $onlyDemo = (bool) $this->option('only-demo');

        $targetDevelopedBy = (string) (env('APP_NAME') ?: config('app.name'));
        $targetDevelopedBy = trim($targetDevelopedBy) !== '' ? $targetDevelopedBy : 'TryOneDigital';

        $tenants = Tenant::query()
            ->when($onlyDemo, function ($q) {
                $q->where('id', 'like', '%demo%');
            })
            ->get();

        if ($tenants->isEmpty()) {
            $this->warn('No tenants found to cleanup.');
            return self::SUCCESS;
        }

        $this->info('Tenants to clean: ' . $tenants->count());

        $counts = [
            'tenant_user_name_fixed' => 0,
            'tenant_user_company_fixed' => 0,
            'tenant_general_settings_developed_by_fixed' => 0,
            'tenant_general_settings_company_fixed' => 0,
        ];

        foreach ($tenants as $tenant) {
            $tenantId = (string) $tenant->id;

            // company_name may live in the tenant "data" json; prefer central tenant payload.
            $centralTenantCompany = '';
            try {
                $centralTenantCompany = (string) ($tenant->company_name ?? '');
            } catch (\Throwable $e) {
                $centralTenantCompany = '';
            }
            if (trim($centralTenantCompany) === '' && is_array($tenant->data ?? null)) {
                $centralTenantCompany = (string) ($tenant->data['company_name'] ?? '');
            }

            $tenant->run(function () use ($tenantId, $dryRun, $targetDevelopedBy, &$counts, $centralTenantCompany) {
                // Tables might not exist for partially provisioned tenants.
                if (!Schema::hasTable('users') || !Schema::hasTable('general_settings')) {
                    return;
                }

                $user = DB::table('users')->where('id', 1)->first();
                if (! $user) {
                    return;
                }

                $currentUserName = (string) $user->name;
                $currentUserCompany = (string) ($user->company_name ?? '');

                $userNameLower = strtolower(trim($currentUserName));
                $userCompanyLower = strtolower(trim($currentUserCompany));

                $tenantCompany = $centralTenantCompany;

                // Fallbacks from tenant DB itself.
                $gsCompany = null;
                try {
                    if (Schema::hasColumn('general_settings', 'company_name')) {
                        $gsCompany = DB::table('general_settings')->value('company_name');
                    }
                } catch (\Throwable $e) {
                    $gsCompany = null;
                }

                if (is_string($gsCompany) && trim($gsCompany) !== '') {
                    $tenantCompany = $gsCompany;
                }

                if (trim($tenantCompany) === '' || strtolower(trim($tenantCompany)) === 'lioncoders') {
                    if ($userCompanyLower !== 'lioncoders' && $userCompanyLower !== '') {
                        $tenantCompany = $currentUserCompany;
                    } else {
                        $tenantCompany = 'Tenant';
                    }
                }

                $shouldRenameSuperadmin = in_array($userNameLower, ['superadmin'], true);
                $shouldFixUserCompany = in_array($userCompanyLower, ['lioncoders'], true);
                $currentDevelopedByLower = strtolower(trim((string) DB::table('general_settings')->value('developed_by')));
                $shouldFixDevelopedBy = in_array($currentDevelopedByLower, ['lioncoders'], true);

                $newUserName = $shouldRenameSuperadmin ? $tenantCompany : $currentUserName;
                $newUserCompany = $shouldFixUserCompany ? $tenantCompany : $currentUserCompany;

                // general_settings.developed_by + company_name cleanup (if columns exist).
                $updates = [];
                if ($shouldFixDevelopedBy) {
                    $updates['developed_by'] = $targetDevelopedBy;
                }
                if (Schema::hasColumn('general_settings', 'company_name')) {
                    // If legacy values exist or field is empty, make it match tenant company label.
                    $existingGsCompany = (string) (DB::table('general_settings')->value('company_name') ?? '');
                    $existingGsCompanyLower = strtolower(trim($existingGsCompany));
                    if ($existingGsCompanyLower === '' || $existingGsCompanyLower === 'lioncoders') {
                        $updates['company_name'] = $tenantCompany;
                    }
                }

                if (! $dryRun) {
                    if ($newUserName !== $currentUserName || $newUserCompany !== $currentUserCompany) {
                        DB::table('users')->where('id', 1)->update([
                            'name' => $newUserName,
                            'company_name' => $newUserCompany,
                        ]);
                    }

                    if ($updates !== []) {
                        DB::table('general_settings')->where('id', 1)->update($updates);
                    }
                }

                if ($shouldRenameSuperadmin) {
                    if ($newUserName !== $currentUserName) {
                        $counts['tenant_user_name_fixed']++;
                    }
                }
                if ($shouldFixUserCompany) {
                    if ($newUserCompany !== $currentUserCompany) {
                        $counts['tenant_user_company_fixed']++;
                    }
                }
                if ($shouldFixDevelopedBy) {
                    $counts['tenant_general_settings_developed_by_fixed']++;
                }
                if (isset($updates['company_name'])) {
                    $counts['tenant_general_settings_company_fixed']++;
                }
            });
        }

        $this->line('Cleanup counts:');
        foreach ($counts as $k => $v) {
            $this->line('- ' . $k . ': ' . $v);
        }

        $this->info($dryRun ? 'Dry-run complete.' : 'Cleanup complete.');
        return self::SUCCESS;
    }
}


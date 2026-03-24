<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TenancyCleanupCentralDomains extends Command
{
    protected $signature = 'tenancy:cleanup-central-domains
                            {--dry-run : Only report how many rows would be deleted}
                            {--list : List matching central-domain rows (domain => tenant_id)}';

    protected $description = 'Remove incorrect central-domain rows from tenants domains table.';

    public function handle(): int
    {
        $centralDomains = config('tenancy.central_domains', []);
        if (!is_array($centralDomains) || empty($centralDomains)) {
            $this->error('No central domains found in config(tenancy.central_domains).');
            return self::FAILURE;
        }

        $query = DB::table('domains')->whereIn('domain', $centralDomains);
        $count = (int) $query->count();

        if ($this->option('list')) {
            $rows = $query->get(['domain', 'tenant_id']);
            foreach ($rows as $row) {
                $this->line($row->domain . ' => ' . $row->tenant_id);
            }
        }

        if ($this->option('dry-run')) {
            $this->info('Dry-run: would delete ' . $count . ' row(s).');
            $this->line('Central domains checked: ' . implode(', ', $centralDomains));
            return self::SUCCESS;
        }

        if ($count === 0) {
            $this->info('Nothing to clean. No matching rows found.');
            return self::SUCCESS;
        }

        $query->delete();
        $this->info('Deleted ' . $count . ' incorrect central-domain row(s).');
        return self::SUCCESS;
    }
}


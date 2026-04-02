<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class ConditionalMigrateTenantDatabase implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public function __construct(
        protected TenantWithDatabase $tenant
    ) {
    }

    public function handle(): void
    {
        $tenantKey = (string) $this->tenant->getTenantKey();

        try {
            @set_time_limit(0);

            Log::info('PIPELINE START', ['tenant' => $tenantKey]);

            // Always run tenant migrations: skipping when `users` existed left DBs without
            // newer tables (cache, etc.). tenants:migrate only applies pending migrations.
            Log::info('PIPELINE MIGRATE BEGIN', ['tenant' => $tenantKey]);
            $exitCode = Artisan::call('tenants:migrate', [
                '--tenants' => [$tenantKey],
                '--force' => true,
            ]);
            Log::info('MIGRATION DONE', [
                'tenant' => $tenantKey,
                'exit_code' => $exitCode,
                'output' => Artisan::output(),
            ]);
            if ($exitCode !== 0) {
                throw new \RuntimeException('tenants:migrate exited with code '.$exitCode.': '.Artisan::output());
            }

            if (tenancy()->initialized) {
                tenancy()->end();
            }

            FinalizeNewTenantJob::dispatch($tenantKey);
            Log::info('PIPELINE FINALIZE DISPATCHED', ['tenant' => $tenantKey]);
        } catch (\Throwable $e) {
            Log::error('PIPELINE ERROR', [
                'tenant' => $tenantKey,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}

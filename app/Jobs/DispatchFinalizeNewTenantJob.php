<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

/**
 * Post-create seed/setup (expects landlord central cache from signup). Sync/async matches provisioning_uses_queue.
 */
class DispatchFinalizeNewTenantJob implements ShouldQueue
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
        @set_time_limit(0);

        $tenantKey = (string) $this->tenant->getTenantKey();

        if (config('tenancy.provisioning_uses_queue', false)) {
            FinalizeNewTenantJob::dispatch($tenantKey);
        } else {
            (new FinalizeNewTenantJob($tenantKey))->handle();
        }

        Log::info('TENANT FINALIZE DISPATCHED', ['tenant' => $tenantKey]);
    }
}

<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\TenantDatabasePoolAllocator;
use Stancl\Tenancy\Events\CreatingTenant;

/**
 * When pool mode is enabled, assign a pre-created MySQL database name before Stancl persists the tenant.
 */
final class AllocateTenantDatabaseFromPool
{
    public function __construct(
        private readonly TenantDatabasePoolAllocator $allocator
    ) {
    }

    public function handle(CreatingTenant $event): void
    {
        if (! config('tenancy.database.pool_enabled')) {
            return;
        }

        $this->allocator->allocateForTenant($event->tenant);
    }
}

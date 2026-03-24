<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when TENANT_DATABASE_POOL_ENABLED is true and no free pool row exists.
 */
class TenantDatabasePoolExhaustedException extends RuntimeException
{
    public function __construct(
        string $message = 'All tenant database slots are in use. Please add more empty MySQL databases in Hostinger hPanel, grant your app user ALL privileges on each, then insert one row per database into the tenant_databases table (is_used = 0). Contact support if this message persists.'
    ) {
        parent::__construct($message);
    }
}

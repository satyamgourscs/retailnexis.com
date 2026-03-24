<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\DatabaseManager;
use Stancl\Tenancy\Events\CreatingDatabase;
use Stancl\Tenancy\Events\DatabaseCreated;

/**
 * Same as Stancl {@see \Stancl\Tenancy\Jobs\CreateDatabase}, but when the tenant database pool is enabled
 * the MySQL schema already exists — Stancl's {@see DatabaseManager::ensureTenantCanBeCreated} would throw
 * {@see \Stancl\Tenancy\Exceptions\TenantDatabaseAlreadyExistsException}. Pool mode skips that check.
 */
final class PoolAwareCreateDatabase implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var TenantWithDatabase|Model */
    protected $tenant;

    public function __construct(TenantWithDatabase $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle(DatabaseManager $databaseManager): void
    {
        event(new CreatingDatabase($this->tenant));

        if ($this->tenant->getInternal('create_database') === false) {
            return;
        }

        $this->tenant->database()->makeCredentials();

        if (! config('tenancy.database.pool_enabled')) {
            $databaseManager->ensureTenantCanBeCreated($this->tenant);
        }

        $this->tenant->database()->manager()->createDatabase($this->tenant);

        event(new DatabaseCreated($this->tenant));
    }
}

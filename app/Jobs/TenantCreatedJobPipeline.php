<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Stancl\JobPipeline\JobPipeline;
use Throwable;

class TenantCreatedJobPipeline extends JobPipeline
{
    public int $timeout = 900;

    public int $tries = 1;

    public function handle(): void
    {
        try {
            Log::info('TENANT PIPELINE JOB START');
            $this->ensureLandlordSchemaSafe();
            parent::handle();
            Log::info('TENANT PIPELINE JOB SUCCESS');
        } catch (Throwable $e) {
            Log::error('TENANT PIPELINE JOB EXCEPTION', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Landlord UI / jobs may require core tables (e.g. languages). Attempt migrate; never block tenant DB work.
     */
    protected function ensureLandlordSchemaSafe(): void
    {
        $conn = (string) config('tenancy.database.central_connection', 'retailnexis_landlord');

        try {
            if (Schema::connection($conn)->hasTable('languages')) {
                return;
            }

            Log::warning('TENANT PIPELINE: landlord missing languages; running landlord migrations', [
                'connection' => $conn,
            ]);

            $code = Artisan::call('migrate', [
                '--database' => $conn,
                '--path' => 'database/migrations/landlord',
                '--force' => true,
            ]);

            Log::info('TENANT PIPELINE: landlord migrate finished', [
                'exit' => $code,
                'output' => Artisan::output(),
            ]);
        } catch (Throwable $e) {
            Log::error('TENANT PIPELINE: landlord migrate attempt failed (tenant pipeline continues)', [
                'connection' => $conn,
                'message' => $e->getMessage(),
            ]);
        }
    }
}

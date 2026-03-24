<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * One-shot maintenance for Hostinger shared hosting: clear stale caches and ensure writable dirs.
 */
class SaasHostingerOptimizeCommand extends Command
{
    protected $signature = 'saas:hostinger-optimize {--skip-config-cache : Do not run config:cache after clear}';

    protected $description = 'Clear config/route/view/cache, optionally re-cache config; ensure bootstrap/cache and storage dirs exist.';

    public function handle(): int
    {
        $this->ensureWritableDirectories();
        $this->info('Running optimize:clear...');
        Artisan::call('optimize:clear');
        $this->output->write(Artisan::output());

        if (! $this->option('skip-config-cache')) {
            $this->info('Running config:cache...');
            Artisan::call('config:cache');
            $this->output->write(Artisan::output());
        }

        $this->info('saas:hostinger-optimize finished.');

        return self::SUCCESS;
    }

    protected function ensureWritableDirectories(): void
    {
        $paths = [
            base_path('bootstrap/cache'),
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
        ];
        foreach ($paths as $path) {
            if (! is_dir($path)) {
                if (@mkdir($path, 0755, true)) {
                    $this->line('Created directory: '.$path);
                }
            }
        }
    }
}

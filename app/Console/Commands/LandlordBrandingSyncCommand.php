<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LandlordBrandingSyncCommand extends Command
{
    protected $signature = 'landlord:sync-branding
                            {--dry-run : Show target values without writing}';

    protected $description = 'Set landlord general_settings to Nexa technologies POS SaaS / Try One Digital (fixes legacy SalePro/LionCoders DB text)';

    public function handle(): int
    {
        if (! config('database.connections.saleprosaas_landlord')) {
            $this->error('Landlord connection saleprosaas_landlord is not configured (.env / config/database.php).');

            return self::FAILURE;
        }

        $conn = DB::connection('saleprosaas_landlord');

        try {
            $conn->getPdo();
        } catch (\Throwable $e) {
            $this->error('Cannot connect to landlord database: '.$e->getMessage());

            return self::FAILURE;
        }

        if (! Schema::connection('saleprosaas_landlord')->hasTable('general_settings')) {
            $this->error('Table general_settings does not exist on the landlord connection.');

            return self::FAILURE;
        }

        $payload = [
            'site_title' => 'Nexa technologies POS SaaS',
            'meta_title' => 'Nexa technologies POS SaaS — inventory, POS, accounting & HRM',
            'meta_description' => 'Nexa technologies POS SaaS — inventory, POS, accounting & HRM solutions',
            'og_title' => 'Nexa technologies POS SaaS — inventory, POS, accounting & HRM',
            'og_description' => 'Nexa technologies POS SaaS — inventory, POS, accounting & HRM solutions',
            'developed_by' => 'Try One Digital',
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];

        $row = $conn->table('general_settings')->orderByDesc('id')->first();
        if (! $row) {
            $this->error('No row in landlord general_settings.');

            return self::FAILURE;
        }

        $this->table(
            ['Field', 'Current', 'New'],
            collect($payload)->except('updated_at')->map(function ($new, $field) use ($row) {
                $cur = isset($row->{$field}) ? (string) $row->{$field} : '';

                return [$field, mb_substr($cur, 0, 80).(mb_strlen($cur) > 80 ? '…' : ''), $new];
            })->values()->all()
        );

        if ($this->option('dry-run')) {
            $this->warn('Dry-run: no changes written.');

            return self::SUCCESS;
        }

        $conn->table('general_settings')->where('id', $row->id)->update($payload);

        try {
            Cache::forget('general_setting');
            Cache::forget('general_settings');
        } catch (\Throwable $e) {
            // ignore
        }

        $this->info('Landlord branding updated (id='.$row->id.'). Run php artisan optimize:clear on the server if views are cached.');

        return self::SUCCESS;
    }
}

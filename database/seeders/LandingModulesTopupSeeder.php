<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LandingModulesTopupSeeder extends Seeder
{
    /**
     * Ensure landlord landing has at least 8 module cards.
     * Keeps DB as dynamic source (no static blade fallback).
     */
    public function run()
    {
        $langId = 1;

        // Keep existing module records; append only if needed.
        $moduleCount = DB::table('modules')->where('lang_id', $langId)->count();
        if ($moduleCount >= 8) {
            return;
        }

        $maxOrder = DB::table('modules')->where('lang_id', $langId)->max('order');
        $nextOrder = is_null($maxOrder) ? 1 : ((int) $maxOrder + 1);

        $candidates = [
            [
                'name' => 'Multi Store Management',
                'description' => 'Run multiple stores and warehouses from one centralized dashboard with branch-level control.',
                'icon' => 'fa fa-building-o',
            ],
            [
                'name' => 'Barcode & Label Printing',
                'description' => 'Generate barcode labels and print them instantly for fast billing and inventory tracking.',
                'icon' => 'fa fa-barcode',
            ],
            [
                'name' => 'Customer & Supplier Management',
                'description' => 'Maintain customer and supplier profiles, due records, and transaction history in one place.',
                'icon' => 'fa fa-users',
            ],
            [
                'name' => 'Sales Analytics Dashboard',
                'description' => 'Track sales trends, top items, and business performance with clear visual reports.',
                'icon' => 'fa fa-line-chart',
            ],
            [
                'name' => 'Role Based Access',
                'description' => 'Control team permissions securely by role, warehouse, and module access.',
                'icon' => 'fa fa-lock',
            ],
        ];

        foreach ($candidates as $candidate) {
            if ($moduleCount >= 8) {
                break;
            }

            $exists = DB::table('modules')
                ->where('lang_id', $langId)
                ->where('name', $candidate['name'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('modules')->insert([
                'name' => $candidate['name'],
                'description' => $candidate['description'],
                'icon' => $candidate['icon'],
                'order' => $nextOrder++,
                'lang_id' => $langId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $moduleCount++;
        }
    }
}


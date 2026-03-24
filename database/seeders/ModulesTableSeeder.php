<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $modules = DB::table('modules')->count();
        if (!$modules) {
            DB::table('modules')->insert(array(
                0 =>
                array(
                    'id' => 1,
                    'name' => 'Sales & POS',
                    'description' => 'Manage your daily sales from beautifully designed POS page with lots of exclusive features.',
                    'icon' => 'fa fa-briefcase',
                    'created_at' => '2023-03-08 23:09:10',
                    'updated_at' => '2023-03-09 00:30:13',
                ),
                1 =>
                array(
                    'id' => 2,
                    'name' => 'Stock & Inventory',
                    'description' => 'Manage standard/combo/digital/service products with variants, IMEI, batches and expiry dates.',
                    'icon' => 'fa fa-cubes',
                    'created_at' => '2023-03-08 23:09:10',
                    'updated_at' => '2023-03-08 23:09:10',
                ),
                2 =>
                array(
                    'id' => 3,
                    'name' => 'Purchase',
                    'description' => 'Create purchase order and automatically update your daily stocks.',
                    'icon' => 'fa fa-book',
                    'created_at' => '2023-03-08 23:09:10',
                    'updated_at' => '2023-03-08 23:09:10',
                ),
                3 =>
                array(
                    'id' => 4,
                    'name' => 'Accounts & HRM',
                    'description' => 'Manage accounts, account statement, balance sheet, employees, payroll, attendance, holidays and lots more.',
                    'icon' => 'fa fa-credit-card-alt',
                    'created_at' => '2023-03-08 23:09:10',
                    'updated_at' => '2023-03-08 23:09:10',
                ),
                4 =>
                array(
                    'id' => 5,
                    'name' => 'Expense',
                    'description' => 'Manage all your warehouse expense',
                    'icon' => 'fa fa-calculator',
                    'created_at' => '2023-11-13 13:30:37',
                    'updated_at' => '2023-11-13 13:30:37',
                ),
            ));
        }
    }
}

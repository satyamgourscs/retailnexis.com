<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $packages = DB::table('packages')->count();
        if (!$packages) {
            DB::table('packages')->insert(array(
                0 =>
                array(
                    'id' => 1,
                    'name' => 'Basic',
                    'is_free_trial' => 1,
                    'monthly_fee' => 20.0,
                    'yearly_fee' => 200.0,
                    'number_of_warehouse' => 1,
                    'number_of_product' => 100,
                    'number_of_invoice' => 10000,
                    'number_of_user_account' => 2,
                    'number_of_employee' => 0,
                    'features' => '["product_and_categories","purchase_and_sale","sale_return","purchase_return","expense"]',
                    'permission_id' => '24,25,26,27,63,64,65,66,55,56,57,58',
                    'role_permission_values' => '(24,1),(25,1),(26,1),(27,1),(63,1),(64,1),(65,1),(66,1),(55,1),(56,1),(57,1),(58,1)',
                    'is_active' => 1,
                    'created_at' => '2023-02-22 12:12:59',
                    'updated_at' => '2023-11-13 12:17:06',
                ),
                1 =>
                array(
                    'id' => 2,
                    'name' => 'Standard',
                    'is_free_trial' => 0,
                    'monthly_fee' => 39.0,
                    'yearly_fee' => 380.0,
                    'number_of_warehouse' => 0,
                    'number_of_product' => 0,
                    'number_of_invoice' => 0,
                    'number_of_user_account' => 0,
                    'number_of_employee' => 0,
                    'features' => '["product_and_categories","purchase_and_sale","sale_return","purchase_return","expense","transfer","quotation","delivery","stock_count_and_adjustment","report"]',
                    'permission_id' => '24,25,26,27,63,64,65,66,55,56,57,58,20,21,22,23,16,17,18,19,99,78,79,36,37,38,39,40,45,46,47,48,49,50,51,52,53,54,77,90,112,122,123,125',
                    'role_permission_values' => '(24,1),(25,1),(26,1),(27,1),(63,1),(64,1),(65,1),(66,1),(55,1),(56,1),(57,1),(58,1),(20,1),(21,1),(22,1),(23,1),(16,1),(17,1),(18,1),(19,1),(99,1),(78,1),(79,1),(36,1),(37,1),(38,1),(39,1),(40,1),(45,1),(46,1),(47,1),(48,1),(49,1),(50,1),(51,1),(52,1),(53,1),(54,1),(77,1),(90,1),(112,1),(122,1),(123,1),(125,1)',
                    'is_active' => 1,
                    'created_at' => '2023-02-22 17:53:11',
                    'updated_at' => '2023-06-07 12:08:55',
                ),
                2 =>
                array(
                    'id' => 3,
                    'name' => 'Premium',
                    'is_free_trial' => 0,
                    'monthly_fee' => 59.0,
                    'yearly_fee' => 600.0,
                    'number_of_warehouse' => 0,
                    'number_of_product' => 0,
                    'number_of_invoice' => 0,
                    'number_of_user_account' => 0,
                    'number_of_employee' => 0,
                    'features' => '["product_and_categories","purchase_and_sale","sale_return","purchase_return","expense","transfer","quotation","delivery","stock_count_and_adjustment","report","hrm","accounting","ecommerce","woocommerce"]',
                    'permission_id' => '24,25,26,27,63,64,65,66,55,56,57,58,20,21,22,23,16,17,18,19,99,78,79,36,37,38,39,40,45,46,47,48,49,50,51,52,53,54,77,90,112,122,123,125,62,70,71,72,73,74,75,76,89,67,68,69,97',
                    'role_permission_values' => '(24,1),(25,1),(26,1),(27,1),(63,1),(64,1),(65,1),(66,1),(55,1),(56,1),(57,1),(58,1),(20,1),(21,1),(22,1),(23,1),(16,1),(17,1),(18,1),(19,1),(99,1),(78,1),(79,1),(36,1),(37,1),(38,1),(39,1),(40,1),(45,1),(46,1),(47,1),(48,1),(49,1),(50,1),(51,1),(52,1),(53,1),(54,1),(77,1),(90,1),(112,1),(122,1),(123,1),(125,1),(62,1),(70,1),(71,1),(72,1),(73,1),(74,1),(75,1),(76,1),(89,1),(67,1),(68,1),(69,1),(97,1)',
                    'is_active' => 1,
                    'created_at' => '2023-02-22 17:54:21',
                    'updated_at' => '2024-02-14 12:43:26',
                ),
            ));
        }
    }
}

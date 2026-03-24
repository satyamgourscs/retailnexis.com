<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSignupDescriptionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $tenant_signup_descriptions = DB::table('tenant_signup_descriptions')->count();
        if (!$tenant_signup_descriptions) {
            DB::table('tenant_signup_descriptions')->insert(array(
                0 =>
                array(
                    'id' => 1,
                    'heading' => 'Customer SignUp',
                    'sub_heading' => 'Experience the best POS system now!',
                    'lang_id' => 1,
                    'created_at' => '2023-05-24 12:25:00',
                    'updated_at' => '2023-06-26 15:51:04',
                ),
                1 =>
                array(
                    'id' => 4,
                    'heading' => 'Registro de cliente',
                    'sub_heading' => 'Experimente el mejor sistema POS ahora!',
                    'lang_id' => 5,
                    'created_at' => '2023-11-13 15:10:51',
                    'updated_at' => '2023-11-13 15:10:51',
                ),
            ));
        }
    }
}

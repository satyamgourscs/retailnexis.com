<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $languages = DB::table('languages')->count();
        if (!$languages) {
            DB::table('languages')->insert(array(
                0 =>
                array(
                    'id' => 1,
                    'code' => 'en',
                    'name' => 'English',
                    'is_default' => 1,
                    'created_at' => '2023-05-26 21:11:26',
                    'updated_at' => '2024-03-19 11:41:56',
                    'is_active' => 1,
                ),
                1 =>
                array(
                    'id' => 5,
                    'code' => 'Es',
                    'name' => 'Spanish',
                    'is_default' => 0,
                    'created_at' => '2023-11-13 13:13:58',
                    'updated_at' => '2023-11-13 13:13:58',
                    'is_active' => 1,
                ),
            ));
        }
    }
}

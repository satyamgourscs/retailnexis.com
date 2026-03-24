<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqDescriptionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $faq_descriptions = DB::table('faq_descriptions')->count();
        if (!$faq_descriptions) {
            DB::table('faq_descriptions')->insert(array(
                0 =>
                array(
                    'id' => 1,
                    'heading' => 'Frequently Asked Questions',
                    'sub_heading' => 'Have questions? We have answered common ones below.',
                    'lang_id' => 1,
                    'created_at' => '2023-05-23 17:19:25',
                    'updated_at' => '2023-05-23 17:19:38',
                ),
                1 =>
                array(
                    'id' => 4,
                    'heading' => 'Preguntas frecuentes',
                    'sub_heading' => 'Tiene preguntas? Hemos respondido las más comunes a continuación.',
                    'lang_id' => 5,
                    'created_at' => '2023-11-13 14:06:28',
                    'updated_at' => '2023-11-13 14:06:28',
                ),
            ));
        }
    }
}

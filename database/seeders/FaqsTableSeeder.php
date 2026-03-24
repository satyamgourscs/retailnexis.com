<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $faqs = DB::table('faqs')->count();
        if (!$faqs) {
            DB::table('faqs')->insert(array(
                0 =>
                array(
                    'id' => 1,
                    'question' => 'Do I need to purchase hosting?',
                    'answer' => 'No, you don\'t need to purchase hosting or install anything anywhere. Just log into your designated admin panel from anywhere, anytime and you\'ll be to access all your software.',
                    'faq_group_id' => 1,
                    'created_at' => '2023-03-08 13:12:41',
                    'updated_at' => '2023-03-08 13:12:41',
                ),
                1 =>
                array(
                    'id' => 2,
                    'question' => 'What hardware does it support?',
                    'answer' => 'Barcode Scanner, barcode label printer, Receipt or Thermal Printer (Printer with ESC/POS commands), digital weighing scale etc.',
                    'faq_group_id' => 2,
                    'created_at' => '2023-03-08 13:12:41',
                    'updated_at' => '2023-03-08 14:10:01',
                ),
                2 =>
                array(
                    'id' => 3,
                    'question' => 'Can I renew my subscription on my own',
                    'answer' => 'Yes you can',
                    'faq_group_id' => 3,
                    'created_at' => '2023-11-13 14:06:28',
                    'updated_at' => '2023-11-13 14:06:28',
                ),
            ));
        }
    }
}

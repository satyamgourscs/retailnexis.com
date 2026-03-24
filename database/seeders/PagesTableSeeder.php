<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $pages = DB::table('pages')->count();
        if (!$pages) {
            DB::table('pages')->insert(array(
                0 =>
                array(
                    'id' => 2,
                    'text' => '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>',
                    'title' => 'Terms & Conditions',
                    'slug' => 'terms-conditions',
                    'meta_title' => 'terms and conditions',
                    'meta_description' => 'terms and conditions...',
                    'created_at' => '2023-05-06 12:00:32',
                    'updated_at' => '2023-11-13 15:40:31',
                ),
                1 =>
                array(
                    'id' => 3,
                    'text' => '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>',
                    'title' => 'Privacy Policy',
                    'slug' => 'privacy-policy',
                    'meta_title' => 'privacy policy',
                    'meta_description' => 'privacy policy meta description',
                    'created_at' => '2023-05-06 12:39:53',
                    'updated_at' => '2023-05-06 13:39:08',
                ),
                2 =>
                array(
                    'id' => 5,
                    'text' => '<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>',
                    'title' => 'Refund Policy',
                    'slug' => 'refund-policy',
                    'meta_title' => NULL,
                    'meta_description' => NULL,
                    'created_at' => '2023-11-13 15:40:58',
                    'updated_at' => '2023-11-13 15:40:58',
                ),
            ));
        }
    }
}

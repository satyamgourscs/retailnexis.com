<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $socials = DB::table('socials')->count();
        if (!$socials) {
            DB::table('socials')->insert(array(
                0 =>
                array(
                    'id' => 1,
                    'name' => 'facebook',
                    // Remove legacy vendor handle.
                    'link' => 'https://facebook.com/',
                    'icon' => 'fa fa-facebook',
                    'order' => 1,
                    'created_at' => '2023-03-11 16:35:05',
                    'updated_at' => '2023-03-11 16:35:05',
                ),
                1 =>
                array(
                    'id' => 2,
                    'name' => 'twitter',
                    // Remove legacy vendor handle.
                    'link' => 'https://twitter.com/',
                    'icon' => 'fa fa-twitter',
                    'order' => 2,
                    'created_at' => '2023-03-11 16:35:05',
                    'updated_at' => '2023-03-11 16:51:32',
                ),
                2 =>
                array(
                    'id' => 3,
                    'name' => 'youtube',
                    // Remove legacy vendor handle.
                    'link' => 'https://youtube.com/',
                    'icon' => 'fa fa-youtube',
                    'order' => 3,
                    'created_at' => '2023-03-11 16:35:05',
                    'updated_at' => '2023-03-11 16:35:05',
                ),
            ));
        }
    }
}

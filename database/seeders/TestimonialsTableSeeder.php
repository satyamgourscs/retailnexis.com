<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestimonialsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $testimonials = DB::table('testimonials')->count();
        if (!$testimonials) {
            DB::table('testimonials')->insert(array(
                0 =>
                array(
                    'id' => 1,
                    'name' => 'Mark Williamson',
                    'text' => 'Great Customer Support Ever! The support guy was really helpful!',
                    'business_name' => NULL,
                    'image' => '20230310060318.jpg',
                    'order' => 2,
                    'created_at' => '2023-03-10 18:03:19',
                    'updated_at' => '2023-03-10 18:03:19',
                ),
                1 =>
                array(
                    'id' => 2,
                    'name' => 'Joe Saint',
                    'text' => 'Can be set up very well and efficiently. Best POS software. I like it. We hope this will help us to succeed in our future endeavors. No bugs found so far. Support is best.',
                    'business_name' => NULL,
                    'image' => '20230310060319.jpg',
                    'order' => 1,
                    'created_at' => '2023-03-10 18:03:19',
                    'updated_at' => '2023-03-10 18:03:19',
                ),
                2 =>
                array(
                    'id' => 3,
                    'name' => 'Brenda Ballion',
                    'text' => 'This is perfect product for your shop',
                    'business_name' => 'BDS',
                    'image' => '20230313031955.jpg',
                    'order' => 3,
                    'created_at' => '2023-03-13 15:19:56',
                    'updated_at' => '2023-03-13 15:19:56',
                ),
                3 =>
                array(
                    'id' => 4,
                    'name' => 'James Bond',
                    'text' => 'This is the best POS system of the world',
                    'business_name' => 'MI6',
                    'image' => '20231113030911.png',
                    'order' => 1,
                    'created_at' => '2023-11-13 15:09:12',
                    'updated_at' => '2023-11-13 15:09:12',
                ),
            ));
        }
    }
}

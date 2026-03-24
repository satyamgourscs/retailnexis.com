<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        $blogs = DB::table('blogs')->count();
        if (!$blogs) {
            DB::table('blogs')->insert(array(
                0 =>
                array(
                    'id' => 1,
                    'title' => 'Palestine is at war!',
                    'slug' => 'this-is-a-test-post-title',
                    'description' => '<p>This is a test post description...</p>',
                    'featured_image' => '20231113033500.jpg',
                    'meta_title' => 'This is a test post title',
                    'meta_description' => 'This is a test post description',
                    'og_title' => 'This is a test post title',
                    'og_description' => 'This is a test post description',
                    'og_image' => NULL,
                    'created_at' => '2023-03-11 13:05:35',
                    'updated_at' => '2023-11-13 15:35:00',
                ),
                1 =>
                array(
                    'id' => 3,
                    'title' => 'This is test post title 3',
                    'slug' => 'this-is-a-test-post-title-3',
                    'description' => '<p>This is test post title 3</p>',
                    'featured_image' => '20230313114921.jpg',
                    'meta_title' => 'This is test post title 3',
                    'meta_description' => 'This is test post title 3',
                    'og_title' => 'This is test post title 3',
                    'og_description' => 'This is test post title 3',
                    'og_image' => NULL,
                    'created_at' => '2023-03-13 11:49:21',
                    'updated_at' => '2023-03-13 11:49:21',
                ),
                2 =>
                array(
                    'id' => 4,
                    'title' => 'This is test post title 4',
                    'slug' => 'this-is-a-test-post-title-4',
                    'description' => '<p>This is test post title 4</p>',
                    'featured_image' => '20230313114947.jpg',
                    'meta_title' => 'This is test post title 4',
                    'meta_description' => 'This is test post title 4',
                    'og_title' => 'This is test post title 4',
                    'og_description' => 'This is test post title 4',
                    'og_image' => NULL,
                    'created_at' => '2023-03-13 11:49:47',
                    'updated_at' => '2023-03-13 11:49:47',
                ),
                3 =>
                array(
                    'id' => 5,
                    'title' => 'Blow your mind!',
                    'slug' => 'blow-your-mind',
                    'description' => '<p>You mind is blown away!</p>',
                    'featured_image' => '20230313040337.jpg',
                    'meta_title' => 'Blow your mind!',
                    'meta_description' => 'Blow your mind!',
                    'og_title' => 'Blow your mind!',
                    'og_description' => 'Blow your mind!',
                    'og_image' => NULL,
                    'created_at' => '2023-03-13 16:03:38',
                    'updated_at' => '2023-03-13 16:03:38',
                ),
                4 =>
                array(
                    'id' => 7,
                    'title' => 'Chicken Roast Receipe',
                    'slug' => 'chicken-roast-receipe',
                    'description' => '<p>How to cook chicker roast...</p>',
                    'featured_image' => '20231113033658.jpg',
                    'meta_title' => 'chicken roast',
                    'meta_description' => 'Chicken Roast Receipe',
                    'og_title' => 'chicken roast',
                    'og_description' => 'Chicken Roast Receipe',
                    'og_image' => NULL,
                    'created_at' => '2023-11-13 15:36:58',
                    'updated_at' => '2023-11-13 15:36:58',
                ),
            ));
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HeroesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $heroes = DB::table('heroes')->count();
        if (!$heroes) {
            DB::table('heroes')->insert(array(
                0 =>
                array(
                    'id' => 1,
                    'heading' => 'Retail Nexis is an all-in-one inventory management & POS software.',
                    'sub_heading' => 'Take care of all your products, sales, purchases, stores related tasks from an easy-to-use platform, from anywhere you want, anytime you want',
                    'image' => 'hero-image.jpg',
                    'button_text' => 'Try for free',
                    'lang_id' => 1,
                    'created_at' => NULL,
                    'updated_at' => '2023-05-22 13:16:14',
                ),
                1 =>
                array(
                    'id' => 5,
                    'heading' => 'Retail Nexis es un software POS y de gestión de inventario todo en uno.',
                    'sub_heading' => 'Ocúpese de todos sus productos, ventas, compras y tareas relacionadas con tiendas desde una plataforma fácil de usar, desde cualquier lugar que desee, en cualquier momento que desee.',
                    'image' => '20231113012229.jpg',
                    'button_text' => 'Prueba gratis',
                    'lang_id' => 5,
                    'created_at' => '2023-11-13 13:22:29',
                    'updated_at' => '2023-11-13 13:22:29',
                ),
            ));
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $users = DB::table('users')->count();
        if (!$users) {
            DB::table('users')->insert(array(
                0 =>
                array(
                    'id' => 1,
                    'name' => 'superadmin',
                    'email' => 'superadmin@gmail.com',
                    'password' => bcrypt('superadmin'),
                    'remember_token' => 'suEwoXbnvMFGTnndo7IynMuColhxOp3ueweV8c7Cbu39KReGw2AVcslcWNEh',
                    'phone' => '+8801911111111',
                    // Avoid legacy vendor branding.
                    'company_name' => env('APP_NAME') ?: config('app.name', 'TryOneDigital'),
                    'role_id' => 1,
                    'is_active' => 1,
                    'created_at' => '2018-06-02 03:24:15',
                    'updated_at' => '2023-05-22 15:14:10',
                ),
            ));
        }
    }
}

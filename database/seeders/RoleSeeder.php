<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert([
            ['name' => 'Super Admin', 'is_access' => 1],
            ['name' => 'Admin', 'is_access' => 0],
            ['name' => 'Editor', 'is_access' => 0],
            ['name' => 'User', 'is_access' => 0],
        ]);
    }
}

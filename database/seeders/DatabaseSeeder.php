<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\RankTypeSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        DB::table('roles')->insert([
            ['name' => 'Super Admin', 'is_access' => 1],
            ['name' => 'Admin', 'is_access' => 0],
            ['name' => 'Manager', 'is_access' => 0],
            ['name' => 'User', 'is_access' => 0],
        ]);

        User::insert([
            [
                'name' => 'Super Admin',
                'email'=>'spadmin@gmail.com',
                'password'=>Hash::make('sb123'),
                'role_id'=>1,
                'is_active'=>1,
                'created_by' => 1,
                'created_at' => now(),
            ],
        ]);

        Menu::insert([
            ['name' => 'Setting', 'title' => 'Setting', 'icon' => 'fas fa-cog', 'route' => null, 'parent_id' => null, 'order' => 1, 'is_active' => 1],
            ['name' => 'Role Management', 'title' => 'Role Management', 'icon' => 'fas fa-user-lock', 'route' => null, 'parent_id' => 1, 'order' => 2, 'is_active' => 1],
            ['name' => 'Roles ', 'title' => 'Roles', 'icon' => 'fas fa-user-tag', 'route' => 'roles.index', 'parent_id' => 2, 'order' => 3, 'is_active' => 1],
            ['name' => 'Permission', 'title' => 'Permission', 'icon' => 'fas fa-key', 'route' => 'role_permissions.index', 'parent_id' => 2, 'order' => 4, 'is_active' => 1],
            ['name' => 'Users', 'title' => 'Users', 'icon' => 'fas fa-users', 'route' => 'users.index', 'parent_id' => 1, 'order' => 5, 'is_active' => 1],
            ['name' => 'Menus', 'title' => 'Users', 'icon' => 'fas fa-bars', 'route' => 'menus.index', 'parent_id' => 1, 'order' => 6, 'is_active' => 1],
        ]);

        $this->call([
            RankTypeSeeder::class,
        ]);
    }
}

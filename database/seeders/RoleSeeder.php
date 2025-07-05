<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Tạo roles - KHÔNG TẠO PERMISSIONS
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'specialist']);

        // Tạo user admin mặc định
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        // Tạo user specialist mặc định
        $specialist = User::create([
            'name' => 'Specialist',
            'email' => 'specialist@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $specialist->assignRole('specialist');
    }
}
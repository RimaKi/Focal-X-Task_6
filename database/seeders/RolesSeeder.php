<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(["name" => 'admin']);
        $user_1 = User::create([
            "name" => "admin",
            "email" => "admin@gmail.com",
            'national_id' => '111222333',
            "password" => '111222333'
        ]);
        $user_1->assignRole($admin);


    }
}

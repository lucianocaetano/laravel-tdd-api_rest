<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create(["name" => "admin", "email"=>"admin@gmail.com", "password"=>"admin"]);

        $admin->assignRole(Roles::ADMIN);

        User::factory()->create(["name" => "mauro", "email"=>"lucianocaetano@gmail.com", "password"=>"password123"]);

        User::factory()->create(["name" => "user", "email"=>"user@gmail.com", "password"=>"password123"]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'User_Name' => 'Admin',
            'User_Email' => 'admin@example.com',
            'User_Password' => '88888888',
            'User_Role' => 'Admin',
            'Status' => 'Active',
        ]);
    }
}
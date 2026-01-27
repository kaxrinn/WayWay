<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin WayWay',
            'email' => 'waywaypolibatam@gmail.com',
            'password' => Hash::make('Admin12345'), // Password: Admin12345
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }
}
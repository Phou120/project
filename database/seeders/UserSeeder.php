<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creating Super Admin User
        $superAdmin = User::create([
            'user_name' => 'super-admin',
            'email' => 'super_admin@gmail.com',
            'password' => Hash::make('sa1234')
        ]);
        $superAdmin->assignRole('super-admin');

        // Creating Admin User
        $admin = User::create([
            'user_name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin')
        ]);
        $admin->assignRole('Admin');

        // Creating Product Manager User
        $productManager = User::create([
            'user_name' => 'user',
            'email' => 'user@gmail.com',
            'password' => Hash::make('user1234')
        ]);
        $productManager->assignRole('user');
    }
}

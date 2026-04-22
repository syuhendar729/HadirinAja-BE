<?php

namespace Database\Seeders;
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
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'nik' => '111222333444',
            'password' => 'admin123',
            'role_id' => 1, 
        ]);
        
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'nik' => '123456789',
            'password' => 'test123',
            'role_id' => 2,
        ]);

    }
}

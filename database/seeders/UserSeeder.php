<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'nik' => 'ADM-001',
                'position' => 'Administrator',
                'phone' => '081100000001',
                'alamat' => 'Kantor HadirinAja',
                'password' => 'admin123',
                'role_id' => 1,
                'created_at' => '2026-01-01 08:00:00',
                'updated_at' => '2026-01-01 08:00:00',
            ],
            [
                'name' => 'fulan',
                'email' => 'fulan@example.com',
                'nik' => 'KRY-001',
                'position' => 'Staff Operasional',
                'phone' => '081200000001',
                'alamat' => 'Bandar Lampung',
                'password' => 'fulan123',
                'role_id' => 2,
                'created_at' => '2026-01-01 08:00:00',
                'updated_at' => '2026-01-01 08:00:00',
            ],
            [
                'name' => 'fulana',
                'email' => 'fulana@example.com',
                'nik' => 'KRY-002',
                'position' => 'Staff Administrasi',
                'phone' => '081200000002',
                'alamat' => 'Metro',
                'password' => 'fulana123',
                'role_id' => 2,
                'created_at' => '2026-03-02 08:00:00',
                'updated_at' => '2026-03-02 08:00:00',
            ],
            [
                'name' => 'fulano',
                'email' => 'fulano@example.com',
                'nik' => 'KRY-003',
                'position' => 'Staff Lapangan',
                'phone' => '081200000003',
                'alamat' => 'Lampung Selatan',
                'password' => 'fulano123',
                'role_id' => 2,
                'created_at' => '2026-05-01 08:00:00',
                'updated_at' => '2026-05-01 08:00:00',
            ],
        ];

        User::unguarded(function () use ($users) {
            User::whereNotIn('email', array_column($users, 'email'))->delete();

            foreach ($users as $user) {
                User::updateOrCreate(
                    ['email' => $user['email']],
                    $user
                );
            }
        });
    }
}

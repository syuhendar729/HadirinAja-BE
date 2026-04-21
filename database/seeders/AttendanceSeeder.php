<?php

namespace Database\Seeders;

use App\Models\Attendance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Attendance::insert([
            [
                'user_id' => 2,
                'status' => 'HADIR',
                'location' => 'Office',
                'notes' => 'On time',
                'url_image' => 'https://example.com/image1.jpg',
                'created_at' => '2026-04-01 08:00:00',
            ],
            [
                'user_id' => 2,
                'status' => 'IZIN',
                'location' => 'Home',
                'notes' => 'Sick leave',
                'url_image' => 'https://example.com/image2.jpg',
                'created_at' => '2026-04-02 09:00:00',
            ],
            [
                'user_id' => 2,
                'status' => 'ALPHA',
                'location' => '',
                'notes' => '',
                'url_image' => null,
                'created_at' => '2026-04-03 10:00:00',
            ]
        ]);

        // $attendances = [
        //     [
        //         'user_id' => 2,
        //         'status' => 'present',
        //         'location' => 'Office',
        //         'notes' => 'On time',
        //         'url_image' => 'https://example.com/image1.jpg',
        //     ],
        //     [
        //         'user_id' => 2,
        //         'status' => 'izin',
        //         'location' => 'Home',
        //         'notes' => 'Sick leave',
        //         'url_image' => 'https://example.com/image2.jpg',
        //     ],
        //     [
        //         'user_id' => 2,
        //         'status' => 'alpha',
        //         'location' => '',
        //         'notes' => '',
        //         'url_image' => null,
        //     ]
        // ];
        
        // foreach ($attendances as $attendance) {
        //     Attendance::create($attendance);
        // }
    }
}


    


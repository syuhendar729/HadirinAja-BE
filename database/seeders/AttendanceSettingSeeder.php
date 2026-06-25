<?php

namespace Database\Seeders;

use App\Models\AttendanceSetting;
use Illuminate\Database\Seeder;

class AttendanceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AttendanceSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'workday_start' => 1,
                'workday_end' => 5,
                'work_start_time' => '08:00:00',
                'work_end_time' => '16:00:00',
                'late_deadline' => '08:30:00',
                'location_name' => 'GK-1 Itera',
                'latitude' => -5.3600000,
                'longitude' => 105.3150000,
                'radius_meters' => 100,
            ]
        );
    }
}

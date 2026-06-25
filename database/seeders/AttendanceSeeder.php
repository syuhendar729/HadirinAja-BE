<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    private const END_DATE = '2026-06-19';
    private const PROOF_IMAGE_URL = 'http://localhost:8000/storage/attendances/contoh-bukti.jpg';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attendance::query()->delete();

        $employees = [
            'fulan@example.com' => [
                'start_date' => '2026-01-01',
                'alpha_days' => [8, 29, 52, 74, 95],
                'permission_days' => [16, 43, 68, 101],
                'late_days' => [6, 22, 39, 57, 83, 109],
            ],
            'fulana@example.com' => [
                'start_date' => '2026-03-02',
                'alpha_days' => [9, 36, 59],
                'permission_days' => [18, 44],
                'late_days' => [5, 21, 33, 51, 70],
            ],
            'fulano@example.com' => [
                'start_date' => '2026-05-01',
                'alpha_days' => [7],
                'permission_days' => [14],
                'late_days' => [4, 18, 27],
            ],
        ];

        foreach ($employees as $email => $config) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                continue;
            }

            $this->seedEmployeeAttendances($user, $config);
        }
    }

    private function seedEmployeeAttendances(User $user, array $config): void
    {
        $workdayIndex = 0;
        $period = CarbonPeriod::create($config['start_date'], self::END_DATE);

        foreach ($period as $date) {
            if ($date->isWeekend()) {
                continue;
            }

            $workdayIndex++;
            $attendance = $this->buildAttendanceData($user, $date, $workdayIndex, $config);

            Attendance::query()->insert($attendance);
        }
    }

    private function buildAttendanceData(User $user, Carbon $date, int $workdayIndex, array $config): array
    {
        $status = Attendance::STATUS_PRESENT;
        $permissionStatus = null;
        $location = 'Office';
        $notes = 'On time';
        $urlImage = null;
        $time = '08:00:00';

        if (in_array($workdayIndex, $config['late_days'], true)) {
            $status = Attendance::STATUS_LATE;
            $notes = 'Late check-in';
            $time = '09:15:00';
        }

        if (in_array($workdayIndex, $config['permission_days'], true)) {
            $status = Attendance::STATUS_LEAVE;
            $permissionStatus = 'approved';
            $location = 'Home';
            $notes = 'Personal leave';
            $urlImage = self::PROOF_IMAGE_URL;
            $time = '08:30:00';
        }

        if (in_array($workdayIndex, $config['alpha_days'], true)) {
            $status = Attendance::STATUS_ABSENT;
            $location = '';
            $notes = 'No attendance record';
            $time = '17:00:00';
        }

        if ($user->email === 'fulana@example.com' && $date->toDateString() === self::END_DATE) {
            $status = Attendance::STATUS_LEAVE;
            $permissionStatus = 'pending';
            $location = 'Home';
            $notes = 'Sick leave, waiting for admin approval';
            $urlImage = self::PROOF_IMAGE_URL;
            $time = '08:30:00';
        }

        $timestamp = $date->format('Y-m-d') . ' ' . $time;

        return [
            'user_id' => $user->id,
            'status' => $status,
            'permission_status' => $permissionStatus,
            'location' => $location,
            'notes' => $notes,
            'url_image' => $urlImage,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }
}

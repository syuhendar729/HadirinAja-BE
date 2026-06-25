<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    // ========================
    // 1. GET ATTENDANCES
    // ========================
    public function getAttendance(Request $request)
    {
        $user = $request->user();

        $attendances = Attendance::where('user_id', $user->id)->get();

        $formatted = [];

        foreach ($attendances as $attendance) {
            $formatted[] = [
                "id" => $attendance->id,
                "created_at" => $attendance->created_at,
                "status" => $attendance->status->value
            ];
        }

        return response()->json([
            'data' => $formatted,
            'message' => 'Success get attendance!',
        ], 200);
    }

    // ========================
    // 2. CREATE ATTENDANCES
    // ========================
    public function createAttendance(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in(Attendance::statuses())],
            'location' => 'required|string|max:255',
            'notes' => 'required|string|max:255',
            'url_image' => 'required|string|max:1024',
            'latitude' => 'required_if:status,PRESENT,LATE|numeric|between:-90,90',
            'longitude' => 'required_if:status,PRESENT,LATE|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Failed create attendance! Invalid input.',
            ], 422);
        }

        $setting = AttendanceSetting::current();

        if (in_array($request->status, [Attendance::STATUS_PRESENT, Attendance::STATUS_LATE], true)) {
            $ruleError = $this->validateAttendanceRule($request, $setting);

            if ($ruleError) {
                return response()->json([
                    'data' => [],
                    'message' => $ruleError,
                ], 422);
            }
        }

        // Cek apakah user sudah melakukan absensi pada hari ini
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->first();

        if ($todayAttendance) {
            return response()->json([
                'data' => [],
                'message' => 'Failed create attendance! You already attendance today.',
            ], 422);
        }

        $status = match ($request->status) {
            Attendance::STATUS_LEAVE => Attendance::STATUS_LEAVE,
            Attendance::STATUS_ABSENT => Attendance::STATUS_ABSENT,
            default => $this->resolveAttendanceStatus($setting),
        };
        $permissionStatus = $request->status === Attendance::STATUS_LEAVE ? 'pending' : null;

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status' => $status,
            'permission_status' => $permissionStatus,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'notes' => $request->notes,
            'url_image' => $request->url_image
        ]);

        return response()->json([
            'data' => [
                'id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'status' => $attendance->status->value,
                'location' => $attendance->location,
                'latitude' => $attendance->latitude,
                'longitude' => $attendance->longitude,
                'notes' => $attendance->notes,
                'created_at' => $attendance->created_at,
                'url_image' => $attendance->url_image,
                'permission_status' => $attendance->permission_status,
            ],
            'message' => 'Success create attendance!',
        ], 201);
    }

    // ========================
    // 3. UPLOAD IMAGE
    // ========================
    public function uploadImage(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => [],
                'message' => 'Failed upload image! Invalid image type.',
            ], 422);
        }

        /* // Ambil attendance terbaru */
        /* $latestAttendance = Attendance::where('user_id', $user->id) */
        /*     ->latest('created_at') */
        /*     ->first(); */
        /**/
        /* // Cek apakah attendance ditemukan */
        /* if (!$latestAttendance) { */
        /*     return response()->json([ */
        /*         'data' => [], */
        /*         'message' => 'Failed upload image! Attendance not found.', */
        /*     ], 404); */
        /* } */
        /**/
        /* // Jika attendance sudah memiliki URL image, maka hapus image lama */
        /* if ($latestAttendance->url_image) { */
        /*     $oldImagePath = str_replace(asset('storage/'), '', $latestAttendance->url_image); */
        /*     Storage::disk('public')->delete($oldImagePath); */
        /* } */

        // Ambil image dari request
        $file = $request->file('image');
        // Simpan image ke storage
        $path = $file->store('attendances', 'public');
        // Ambil URL image yang disimpan
        $urlImage = asset('storage/' . $path);

        // Update URL image pada table attendance
        /* $latestAttendance->update([ */
        /*     'url_image' => $urlImage, */
        /* ]); */

        return response()->json([
            'data' => [
                'url_image' => $urlImage,
            ],
            'message' => 'Success upload image!',
        ], 200);
    }

    private function validateAttendanceRule(Request $request, AttendanceSetting $setting): ?string
    {
        $now = now();

        if (! $this->isWorkday($now, $setting)) {
            return 'Failed create attendance! Today is not a workday.';
        }

        if (! $this->isTimeConfigurationValid($setting)) {
            return 'Failed create attendance! Attendance time setting is invalid.';
        }

        $workStart = $this->timeToday($setting->work_start_time);
        $workEnd = $this->timeToday($setting->work_end_time);

        if ($now->lt($workStart) || $now->gt($workEnd)) {
            return 'Failed create attendance! Attendance is only allowed during work hours.';
        }

        if ($setting->latitude === null || $setting->longitude === null) {
            return 'Failed create attendance! Attendance location is not configured.';
        }

        $distance = $this->distanceInMeters(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $setting->latitude,
            (float) $setting->longitude
        );

        if ($distance > $setting->radius_meters) {
            return 'Failed create attendance! You are outside the allowed attendance radius.';
        }

        return null;
    }

    private function resolveAttendanceStatus(AttendanceSetting $setting): string
    {
        return now()->gt($this->timeToday($setting->late_deadline))
            ? Attendance::STATUS_LATE
            : Attendance::STATUS_PRESENT;
    }

    private function isTimeConfigurationValid(AttendanceSetting $setting): bool
    {
        return $setting->work_start_time <= $setting->late_deadline
            && $setting->late_deadline <= $setting->work_end_time
            && $setting->work_start_time < $setting->work_end_time;
    }

    private function isWorkday(Carbon $date, AttendanceSetting $setting): bool
    {
        $day = $date->dayOfWeek;

        if ($setting->workday_start <= $setting->workday_end) {
            return $day >= $setting->workday_start && $day <= $setting->workday_end;
        }

        return $day >= $setting->workday_start || $day <= $setting->workday_end;
    }

    private function timeToday(string $time): Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', now()->toDateString() . ' ' . $time);
    }

    private function distanceInMeters(float $latitudeFrom, float $longitudeFrom, float $latitudeTo, float $longitudeTo): float
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            sin($latDelta / 2) ** 2 +
            cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2
        ));

        return $angle * $earthRadius;
    }
}

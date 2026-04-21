<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;

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
            $date = $attendance->created_at->format('Y-m-d');
            $formatted[$date] = $attendance->status;
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
            'status' => 'required|in:HADIR,IZIN,ALPHA',
            'location' => 'required|string|max:255',
            'notes' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Failed create attendance! Invalid input.',
            ], 422);
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

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status' => $request->status,
            'location' => $request->location,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'data' => [
                'status' => $attendance->status,
                'location' => $attendance->location,
                'notes' => $attendance->notes,
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

        // Ambil attendance terbaru
        $latestAttendance = Attendance::where('user_id', $user->id)
            ->latest('created_at')
            ->first();

        // Cek apakah attendance ditemukan
        if (!$latestAttendance) {
            return response()->json([
                'data' => [],
                'message' => 'Failed upload image! Attendance not found.',
            ], 404);
        }

        // Jika attendance sudah memiliki URL image, maka hapus image lama
        if ($latestAttendance->url_image) {
            $oldImagePath = str_replace(asset('storage/'), '', $latestAttendance->url_image);
            Storage::disk('public')->delete($oldImagePath);
        }

        // Ambil image dari request
        $file = $request->file('image');
        // Simpan image ke storage
        $path = $file->store('attendances', 'public');
        // Ambil URL image yang disimpan
        $urlImage = asset('storage/' . $path);

        // Update URL image pada table attendance
        $latestAttendance->update([
            'url_image' => $urlImage,
        ]);

        return response()->json([
            'data' => [
                'url_image' => $urlImage,
            ],
            'message' => 'Success upload image!',
        ], 200);
    }
}

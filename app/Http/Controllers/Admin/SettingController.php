<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', [
            'setting' => AttendanceSetting::current(),
            'days' => $this->days(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'workday_start' => ['required', 'integer', 'between:0,6'],
            'workday_end' => ['required', 'integer', 'between:0,6'],
            'work_start_time' => ['required', 'date_format:H:i'],
            'work_end_time' => ['required', 'date_format:H:i', 'after:work_start_time'],
            'late_deadline' => ['required', 'date_format:H:i', 'after_or_equal:work_start_time', 'before_or_equal:work_end_time'],
            'location_name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        AttendanceSetting::current()->update($validated);

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Settings berhasil disimpan.');
    }

    private function days(): array
    {
        return [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
    }
}

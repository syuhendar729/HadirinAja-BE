<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::query()
            ->select('id', 'name', 'email', 'nik', 'position', 'phone', 'alamat', 'role_id', 'created_at')
            ->where('role_id', 2)
            ->withCount([
                'attendances',
                'attendances as present_attendances_count' => function ($query) {
                    $query->where('status', Attendance::STATUS_PRESENT);
                },
            ])
            ->orderBy('name')
            ->get()
            ->map(function (User $user) {
                $attendancePercentage = $user->attendances_count > 0
                    ? round(($user->present_attendances_count / $user->attendances_count) * 100, 1)
                    : 0;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'nik' => $user->nik,
                    'position' => $user->position,
                    'phone' => $user->phone,
                    'alamat' => $user->alamat,
                    'role_id' => $user->role_id,
                    'attendance_count' => $user->attendances_count,
                    'present_attendance_count' => $user->present_attendances_count,
                    'attendance_percentage' => $attendancePercentage,
                    'created_at' => $user->created_at,
                ];
            });

        return response()->json([
            'data' => $users,
            'message' => 'Success get users!',
        ]);
    }

    public function show(User $user)
    {
        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    public function user(User $user)
    {
        $permissionRequests = Attendance::query()
            ->where('user_id', $user->id)
            ->whereNotNull('permission_status')
            ->latest('created_at')
            ->get()
            ->map(function (Attendance $attendance) {
                return [
                    'id' => $attendance->id,
                    'date' => optional($attendance->created_at)->toDateString(),
                    'status' => $attendance->status->value,
                    'permission_status' => $attendance->permission_status,
                    'notes' => $attendance->notes,
                    'proof_url' => $attendance->url_image,
                    'created_at' => $attendance->created_at,
                ];
            });

        $calendarAttendances = Attendance::query()
            ->where('user_id', $user->id)
            ->orderBy('created_at')
            ->get()
            ->map(function (Attendance $attendance) {
                return [
                    'id' => $attendance->id,
                    'date' => optional($attendance->created_at)->toDateString(),
                    'status' => $attendance->status->value,
                    'notes' => $attendance->notes,
                ];
            });

        return response()->json([
            'data' => $user->detailData([
                'started_at' => optional($user->created_at)->toDateString(),
                'total_weekday' => $this->countWorkdays($user->created_at ?? now(), now()),
                'permission_requests' => $permissionRequests,
                'calendar_attendances' => $calendarAttendances,
            ]),
            'message' => 'Success get user detail!',
        ]);
    }

    public function approvePermission(User $user, Attendance $attendance)
    {
        $this->ensurePendingPermissionBelongsToUser($user, $attendance);

        $attendance->update([
            'status' => Attendance::STATUS_LEAVE,
            'permission_status' => 'approved',
        ]);

        return response()->json([
            'data' => [
                'id' => $attendance->id,
                'status' => $attendance->status->value,
                'permission_status' => $attendance->permission_status,
            ],
            'message' => 'Leave request approved!',
        ]);
    }

    public function rejectPermission(User $user, Attendance $attendance)
    {
        $this->ensurePendingPermissionBelongsToUser($user, $attendance);

        $attendance->update([
            'status' => Attendance::STATUS_ABSENT,
            'permission_status' => 'rejected',
        ]);

        return response()->json([
            'data' => [
                'id' => $attendance->id,
                'status' => $attendance->status->value,
                'permission_status' => $attendance->permission_status,
            ],
            'message' => 'Leave request rejected!',
        ]);
    }

    private function ensurePendingPermissionBelongsToUser(User $user, Attendance $attendance): void
    {
        abort_unless(
            $attendance->user_id === $user->id && $attendance->permission_status === 'pending',
            404
        );
    }

    private function countWorkdays($startDate, $endDate): int
    {
        $holidays = [];
        $period = CarbonPeriod::create(
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->startOfDay()
        );

        $total = 0;

        foreach ($period as $date) {
            if (! $date->isWeekend() && ! in_array($date->toDateString(), $holidays, true)) {
                $total++;
            }
        }

        return $total;
    }
}

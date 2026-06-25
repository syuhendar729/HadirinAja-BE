<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Table('attendances')]
#[Fillable(['user_id', 'status', 'permission_status', 'location', 'latitude', 'longitude', 'notes', 'url_image'])]
class Attendance extends Model
{
    public const STATUS_PRESENT = 'PRESENT';
    public const STATUS_LATE = 'LATE';
    public const STATUS_LEAVE = 'LEAVE';
    public const STATUS_ABSENT = 'ABSENT';

    public static function statuses(): array
    {
        return AttendanceStatus::values();
    }

    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }
}

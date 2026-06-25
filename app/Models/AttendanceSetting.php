<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'workday_start',
    'workday_end',
    'work_start_time',
    'work_end_time',
    'late_deadline',
    'location_name',
    'latitude',
    'longitude',
    'radius_meters',
])]
class AttendanceSetting extends Model
{
    protected $casts = [
        'workday_start' => 'integer',
        'workday_end' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'radius_meters' => 'integer',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'workday_start' => 1,
            'workday_end' => 5,
            'work_start_time' => '08:00:00',
            'work_end_time' => '16:00:00',
            'late_deadline' => '08:30:00',
            'location_name' => 'GK-1 Itera',
            'radius_meters' => 100,
        ]);
    }
}

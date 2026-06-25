<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Present = 'PRESENT';
    case Late = 'LATE';
    case Leave = 'LEAVE';
    case Absent = 'ABSENT';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

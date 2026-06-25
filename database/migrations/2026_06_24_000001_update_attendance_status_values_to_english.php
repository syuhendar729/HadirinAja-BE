<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('attendances')->where('status', 'HADIR')->update(['status' => 'PRESENT']);
        DB::table('attendances')->where('status', 'TERLAMBAT')->update(['status' => 'LATE']);
        DB::table('attendances')->where('status', 'IZIN')->update(['status' => 'LEAVE']);
        DB::table('attendances')->where('status', 'ALPHA')->update(['status' => 'ABSENT']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('attendances')->where('status', 'PRESENT')->update(['status' => 'HADIR']);
        DB::table('attendances')->where('status', 'LATE')->update(['status' => 'TERLAMBAT']);
        DB::table('attendances')->where('status', 'LEAVE')->update(['status' => 'IZIN']);
        DB::table('attendances')->where('status', 'ABSENT')->update(['status' => 'ALPHA']);
    }
};

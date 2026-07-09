<?php

use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\User;
use Carbon\Carbon;



pest()->use(RefreshDatabase::class);

// ========================
// 1. TEST GET ATTENDANCE (GET /api/attendance)
// ========================

test('get attendance success', function () {

    $this->seed(DatabaseSeeder::class);

    $loginResponse = $this->postJson('/api/login', [
        'email' => 'fulan@example.com',
        'password' => 'fulan123'
    ]);

    $token = $loginResponse->json('data.token');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->getJson("/api/attendance");

    // Assertion
    $response
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'Success get attendance!')
                ->has('data')
                ->has('data.0', fn ($json) =>
                    $json->has('created_at')
                         ->has('status')
                         ->has('id')
                         ->etc()
                )
        );
});

// ========================
// 2. TEST CREATE ATTENDANCE (POST /api/attendance)
// ========================

test('create attendance success', function () {

    $this->seed(DatabaseSeeder::class);
    Carbon::setTestNow(Carbon::parse('2026-06-24 08:15:00', 'Asia/Jakarta'));

    $loginResponse = $this->postJson('/api/login', [
        'email' => 'fulan@example.com',
        'password' => 'fulan123',
    ]);

    $token = $loginResponse->json('data.token');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->postJson('/api/attendance', [
        'status' => Attendance::STATUS_PRESENT,
        'location' => 'Institut Teknologi Sumatera',
        'latitude' => -5.3600000,
        'longitude' => 105.3150000,
        'notes' => 'Present on time',
        'url_image' => 'http://localhost:8000/test.jpg',
    ]);

    // $response->ddJson();
    // Assertion response
    $response
        ->assertCreated()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'Success create attendance!')
                ->has('data')
                ->has('data.id')
                ->where('data.status', Attendance::STATUS_PRESENT)
                ->where('data.location', 'Institut Teknologi Sumatera')
                ->where('data.latitude', -5.36)
                ->where('data.longitude', 105.315)
                ->where('data.notes', 'Present on time')
                ->where('data.url_image', 'http://localhost:8000/test.jpg')
                ->has('data.created_at')
                ->etc()
        );

    // Ambil data user yang login
    $user = \App\Models\User::where('email', 'fulan@example.com')->first();
    // Assertion database
    $this->assertDatabaseHas('attendances', [
        'id' => $response->json('data.id'),
        'user_id' => $user->id,
        'status' => Attendance::STATUS_PRESENT,
        'location' => 'Institut Teknologi Sumatera',
        'latitude' => -5.3600000,
        'longitude' => 105.3150000,
        'notes' => 'Present on time',
        'url_image' => 'http://localhost:8000/test.jpg',
        'created_at' => '2026-06-24 08:15:00',
        'updated_at' => '2026-06-24 08:15:00',
    ]);

    Carbon::setTestNow();
});

test('create attendance allows saturday configured as workday even when friday attendance exists', function () {

    $this->seed(DatabaseSeeder::class);

    AttendanceSetting::current()->update([
        'workday_start' => 1,
        'workday_end' => 6,
    ]);

    $user = User::where('email', 'fulan@example.com')->first();

    $fridayAttendanceTime = Carbon::parse('2026-06-26 08:00:00', 'Asia/Jakarta')->toDateTimeString();

    Attendance::query()->insert([
        'user_id' => $user->id,
        'status' => Attendance::STATUS_PRESENT,
        'location' => 'Institut Teknologi Sumatera',
        'latitude' => -5.3600000,
        'longitude' => 105.3150000,
        'notes' => 'Friday attendance',
        'url_image' => 'http://localhost:8000/friday.jpg',
        'created_at' => $fridayAttendanceTime,
        'updated_at' => $fridayAttendanceTime,
    ]);

    Carbon::setTestNow(Carbon::parse('2026-06-27 08:15:00', 'Asia/Jakarta'));

    $loginResponse = $this->postJson('/api/login', [
        'email' => 'fulan@example.com',
        'password' => 'fulan123',
    ]);

    $token = $loginResponse->json('data.token');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->postJson('/api/attendance', [
        'status' => Attendance::STATUS_PRESENT,
        'location' => 'Institut Teknologi Sumatera',
        'latitude' => -5.3600000,
        'longitude' => 105.3150000,
        'notes' => 'Saturday attendance',
        'url_image' => 'http://localhost:8000/saturday.jpg',
    ]);

    $response
        ->assertCreated()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'Success create attendance!')
                ->where('data.status', Attendance::STATUS_PRESENT)
                ->where('data.notes', 'Saturday attendance')
                ->etc()
        );

    $this->assertDatabaseHas('attendances', [
        'user_id' => $user->id,
        'notes' => 'Saturday attendance',
        'created_at' => '2026-06-27 08:15:00',
        'updated_at' => '2026-06-27 08:15:00',
    ]);

    Carbon::setTestNow();
});

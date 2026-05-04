<?php

use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use App\Models\Attendance;
use Carbon\Carbon;


 
pest()->use(RefreshDatabase::class);

// ========================
// 1. TEST GET ATTENDANCE (GET /api/attendance)
// ========================

test('get attendance success', function () {

    $this->seed(DatabaseSeeder::class);

    $loginResponse = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'test123'
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

    $loginResponse = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'test123',
    ]);

    $token = $loginResponse->json('data.token');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->postJson('/api/attendance', [
        'status' => 'HADIR', 
        'location' => 'Institut Teknologi Sumatera',
        'notes' => 'Hadir tepat waktu',
    ]);

    // $response->ddJson();
    // Assertion response
    $response
        ->assertCreated()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'Success create attendance!')
                ->has('data')
                ->has('data.id')
                ->where('data.status', 'HADIR')
                ->where('data.location', 'Institut Teknologi Sumatera')
                ->where('data.notes', 'Hadir tepat waktu')
                ->has('data.created_at')
                ->etc()
        );

    // Ambil data user yang login
    $user = \App\Models\User::where('email', 'test@example.com')->first();
    $created_at = Carbon::parse($response->json('data.created_at'))
    ->format('Y-m-d H:i:s');


    // Assertion database
    $this->assertDatabaseHas('attendances', [
        'id' => $response->json('data.id'),
        'user_id' => $user->id,
        'status' => 'HADIR',
        'location' => 'Institut Teknologi Sumatera',
        'notes' => 'Hadir tepat waktu',
        'created_at' => $created_at,
    ]);
});
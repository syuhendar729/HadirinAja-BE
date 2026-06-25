<?php

use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use App\Models\User;

 
pest()->use(RefreshDatabase::class);

// ========================
// 1. TEST GET USER (GET /api/user)
// ========================

test('get user success', function () {

    $this->seed(DatabaseSeeder::class);

    $loginResponse = $this->postJson('/api/login', [
        'email' => 'fulan@example.com',
        'password' => 'fulan123'
    ]);

    $token = $loginResponse->json('data.token');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->getJson("/api/user");

    // $response->ddJson();
    // $response->ddBody();

    $response
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'Success get user!')
                ->has('data.id')
                ->where('data.name', 'fulan')
                ->where('data.email', 'fulan@example.com')
                ->has('data.created_at')
                ->has('data.updated_at')
                ->has('data.profile_picture')
                ->has('data.nik')
                ->has('data.role_id')
                ->has('data.position')
                ->has('data.phone')
                ->has('data.alamat')
                ->has('data.total.present')
                ->has('data.total.late')
                ->has('data.total.leave')
                ->has('data.total.absent')
                ->etc()
        );
});


// ========================
// 2. TEST UPDATE USER (PATCH /api/user)
// ========================

test('update user success', function () {
    $this->seed(DatabaseSeeder::class);

    User::create([
            'name' => 'Tester',
            'email' => 'tester@mail.com',
            'nik' => '000111222333',
            'password' => 'tester123',
            'role_id' => 2,
    ]);

    $loginResponse = $this->postJson('/api/login', [
        'email' => 'tester@mail.com',
        'password' => 'tester123'
    ]);

    $token = $loginResponse->json('data.token');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->patchJson("/api/user", [
        'name' => 'Tester Update', 
        'email' => 'update@mail.com',
        'nik' => '333222111000',
    ]);

    $response
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'Success update user!')
                ->has('data.id')
                ->where('data.name', 'Tester Update')
                ->where('data.email', 'update@mail.com')
                ->has('data.created_at')
                ->has('data.updated_at')
                ->has('data.profile_picture')
                ->where('data.nik', '333222111000')
                ->has('data.role_id')
                ->has('data.position')
                ->has('data.phone')
                ->has('data.alamat')
                ->has('data.total.present')
                ->has('data.total.late')
                ->has('data.total.leave')
                ->has('data.total.absent')
                ->etc()
        );


});

// ========================
// 2. TEST DELETE USER (DELETE /api/user)
// ========================

test('delete user success', function () {
    $this->seed(DatabaseSeeder::class);

    $loginResponse = $this->postJson('/api/login', [
        'email' => 'fulan@example.com',
        'password' => 'fulan123'
    ]);

    $token = $loginResponse->json('data.token');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->deleteJson("/api/user");

    $response
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'Success delete user!')
                ->etc()
        );


});

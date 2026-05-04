<?php

use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;

 
pest()->use(RefreshDatabase::class);

// ========================
// 1. TEST REGISTER (POST /api/register)
// ========================

test('register success', function () {

    $this->seed(DatabaseSeeder::class);

    $response = $this->postJson('/api/register', [
        'name' => 'Tester', 
        'email' => 'tester@mail.com',
        'password' => 'tester123',
        'password_confirmation' => 'tester123'
    ]);

    $response
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('success', true)
                ->where('message', 'Success create user!')
                ->has('data.token')
                ->where('data.name', 'Tester')
                ->where('data.email', 'tester@mail.com')
                ->etc()
        );

    $this->assertDatabaseHas('users', [
        'email' => 'tester@mail.com'
    ]);
});

// ========================
// 2. TEST LOGIN
// ========================

test('login success', function () {
    $this->seed(DatabaseSeeder::class);

    $response = $this->postJson("/api/login", [
        'email' => 'test@example.com',
        'password' => 'test123'
    ]);

    $response
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('success', true)
                ->where('message', 'Success login!')
                ->has('data.token')
                ->where('data.email', 'test@example.com')
                ->etc()
        );
    
    $token = $response->json('data.token');
    [$id, $plainTextToken] = explode('|', $token, 2);
    
    $this->assertDatabaseHas('personal_access_tokens', [
        'token' => hash('sha256', $plainTextToken),
    ]);
});

// ========================
// 3. TEST LOGOUT
// ========================

test('logout success', function () {

    $this->seed(DatabaseSeeder::class);

    $loginResponse = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'test123',
    ]);

    $response = $this->withHeaders([
        'Authorization' => "Bearer " . $loginResponse->json('data.token')
    ])->deleteJson("/api/logout");

    // $response->ddJson();
    // $response->ddBody();
    $response
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'Success logout!')
                ->etc()
        );

    $token = $loginResponse->json('data.token');
    [$id, $plainTextToken] = explode('|', $token, 2);
    
    $this->assertDatabaseMissing('personal_access_tokens', [
        'token' => hash('sha256', $plainTextToken),
    ]);
});


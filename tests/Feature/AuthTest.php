<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;

pest()->use(RefreshDatabase::class);

// ========================
// 1. TEST HALAMAN LOGIN ADMIN
// ========================

test('admin login page can be opened', function () {
    $response = $this->get('/admin/login');

    $response
        ->assertOk()
        ->assertSee('Admin Login')
        ->assertSee('Email')
        ->assertSee('Password')
        ->assertSee('Login');
});

// ========================
// 2. TEST ADMIN LOGIN SUCCESS
// ========================

test('admin login success when role_id is 1', function () {
    $admin = User::factory()->create([
        'name' => 'Admin Test',
        'email' => 'admin@test.com',
        'password' => bcrypt('admin123'),
        'role_id' => 1,
    ]);

    $response = $this->post('/admin/login', [
        'email' => 'admin@test.com',
        'password' => 'admin123',
    ]);

    $response->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

// ========================
// 3. TEST USER BIASA TIDAK BISA LOGIN ADMIN
// ========================

test('non admin cannot login to admin dashboard', function () {
    User::factory()->create([
        'name' => 'User Test',
        'email' => 'user@test.com',
        'password' => bcrypt('user123'),
        'role_id' => 2,
    ]);

    $response = $this->post('/admin/login', [
        'email' => 'user@test.com',
        'password' => 'user123',
    ]);

    $response
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

// ========================
// 4. TEST LOGIN GAGAL PASSWORD SALAH
// ========================

test('admin login failed with wrong password', function () {
    User::factory()->create([
        'name' => 'Admin Test',
        'email' => 'admin@test.com',
        'password' => bcrypt('admin123'),
        'role_id' => 1,
    ]);

    $response = $this->post('/admin/login', [
        'email' => 'admin@test.com',
        'password' => 'wrongpassword',
    ]);

    $response
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

// ========================
// 5. TEST VALIDASI INPUT KOSONG
// ========================

test('admin login failed when input is empty', function () {
    $response = $this->post('/admin/login', [
        'email' => '',
        'password' => '',
    ]);

    $response
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors(['email', 'password']);

    $this->assertGuest();
});

// ========================
// 6. TEST ADMIN LOGOUT
// ========================

test('admin logout success', function () {
    $admin = User::factory()->create([
        'name' => 'Admin Test',
        'email' => 'admin@test.com',
        'password' => bcrypt('admin123'),
        'role_id' => 1,
    ]);

    $this->actingAs($admin);

    $response = $this->post('/admin/logout');

    $response->assertRedirect(route('admin.login'));

    $this->assertGuest();
});

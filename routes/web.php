<?php

// File: routes/web.php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('admin.login');
});

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])
    ->name('admin.login')
    ->middleware('guest');

Route::get('/login', function () {
    return Auth::check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('admin.login');
})->name('login');

Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->name('admin.login.process')
    ->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/admin/users/create', [AdminUserController::class, 'create'])
        ->name('admin.users.create');

    Route::post('/admin/users', [AdminUserController::class, 'store'])
        ->name('admin.users.store');

    Route::get('/admin/users/{user}', [AdminController::class, 'show'])
        ->name('admin.users.show');

    Route::get('/admin/users/{user}/edit', [AdminUserController::class, 'edit'])
        ->name('admin.users.edit');

    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])
        ->name('admin.users.update');

    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])
        ->name('admin.users.destroy');

    Route::get('/admin/settings', [SettingController::class, 'edit'])
        ->name('admin.settings.edit');

    Route::put('/admin/settings', [SettingController::class, 'update'])
        ->name('admin.settings.update');

    Route::get('/admin/api/users', [AdminController::class, 'users'])
        ->name('admin.api.users');

    Route::post('/admin/api/users', [AdminUserController::class, 'store'])
        ->name('admin.api.users.store');

    Route::get('/admin/api/users/{user}', [AdminController::class, 'user'])
        ->name('admin.api.users.show');

    Route::put('/admin/api/users/{user}', [AdminUserController::class, 'update'])
        ->name('admin.api.users.update');

    Route::delete('/admin/api/users/{user}', [AdminUserController::class, 'destroy'])
        ->name('admin.api.users.destroy');

    Route::post('/admin/api/users/{user}/permissions/{attendance}/approve', [AdminController::class, 'approvePermission'])
        ->name('admin.api.users.permissions.approve');

    Route::post('/admin/api/users/{user}/permissions/{attendance}/reject', [AdminController::class, 'rejectPermission'])
        ->name('admin.api.users.permissions.reject');

    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])
        ->name('admin.logout');
});

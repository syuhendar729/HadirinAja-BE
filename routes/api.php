<?php

// File: routes/api.php

use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Access /api to test if API is working
Route::get('/', function () {
    return response()->json([
        'message' => 'API is working!',
    ], 200);
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('admin-login', [AuthController::class, 'adminLogin']);

Route::middleware('auth:sanctum')->group(function () {
    // Route User and Auth
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'getUser']);
    Route::patch('/user', [UserController::class, 'updateUser']);
    Route::delete('/user', [UserController::class, 'deleteUser']);
    // Route Attendance
    Route::get('/attendance', [AttendanceController::class, 'getAttendance']);
    Route::post('/attendance', [AttendanceController::class, 'createAttendance']);
    Route::post('/attendance/image', [AttendanceController::class, 'uploadImage']);
});



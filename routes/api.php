<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Route User and Auth
    Route::get('/user', [UserController::class, 'getUser']);
    Route::patch('/user', [UserController::class, 'updateUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    // Route Attendance
    Route::get('/attendances', [AttendanceController::class, 'getAttendance']);
    Route::post('/attendances', [AttendanceController::class, 'createAttendance']);
    Route::post('/attendances/image', [AttendanceController::class, 'uploadImage']);
});



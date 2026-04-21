<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Failed create user!',
            ], 422);
        }

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Login user dan buat token
        Auth::login($user);
        $responseData = [
            'token' => $user->createToken($user->email)->plainTextToken,
            'name' => $user->name,
            'email' => $user->email,
        ];

        // Kirim respons sukses
        return response()->json([
            'success' => true,
            'data' => $responseData,
            'message' => 'Success create user!',
        ]);
    }

    public function logout(Request $request)
    {
        // Ambil pengguna yang sedang login
        $user = Auth::user();

        // Periksa apakah pengguna memiliki token
        if ($user->tokens->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active tokens founds!',
            ], 404);
        }

        // Hapus semua token yang terkait dengan pengguna
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        // Kirim respons sukses
        return response()->json([
            'success' => true,
            'message' => 'Success logout!',
        ], 200);
    }

    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed login! Invalid input.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verifikasi kredensial
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $responseData = [
                'token' => $user->createToken($user->name)->plainTextToken,
                'name' => $user->name,
                'email' => $user->email,
            ];

            // Kirim respons sukses
            return response()->json([
                'success' => true,
                'data' => $responseData,
                'message' => 'Success login!',
            ], 200);
        }

        // Kredensial salah
        return response()->json([
            'success' => false,
            'message' => 'Failed login! Wrong email or password.',
        ], 401);
    }



}

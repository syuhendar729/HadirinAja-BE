<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        return response()->json([
            'data' => $request->user(),
            'message' => 'Success get user!',
        ], 200);
    }

    public function updateUser(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'nik' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Failed update user! Invalid input.',
            ], 422);
        }

        $user = Auth::user();

        // print_r($request->all());
        // print_r($user);

        $user->update([
            'email' => $request->email ?? $user->email,
            'name' => $request->name ?? $user->name,
            'nik' => $request->nik ?? $user->nik,
        ]);

        return response()->json([
            'data' => $user,
            'message' => 'Success update user!',
        ], 200);
    }

    public function deleteUser(Request $request)
    {
        $user = Auth::user();
        $user->delete();

        return response()->json([
            'message' => 'Success delete user!',
        ], 200);
    }
    
}

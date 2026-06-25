<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function create()
    {
        return view('admin.users.form', [
            'user' => new User(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateUser($request);
        $validated['role_id'] = 2;
        $startedAt = $validated['started_at'] ?? null;
        unset($validated['started_at']);

        $user = User::create($validated);
        $this->updateStartedAt($user, $startedAt);

        return $this->userResponse($request, $user, 'User berhasil ditambahkan.', 201);
    }

    public function edit(User $user)
    {
        $this->ensureEmployee($user);

        return view('admin.users.form', [
            'user' => $user,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->ensureEmployee($user);

        $validated = $this->validateUser($request, $user);
        $validated['role_id'] = 2;

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $startedAt = $validated['started_at'] ?? null;
        unset($validated['started_at']);

        $user->update($validated);
        $this->updateStartedAt($user, $startedAt);

        return $this->userResponse($request, $user, 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        $this->ensureEmployee($user);
        abort_if(Auth::id() === $user->id, 403, 'Tidak bisa menghapus akun yang sedang login.');

        $user->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [],
                'message' => 'User berhasil dihapus.',
            ]);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'User berhasil dihapus.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $userId = $user?->id;
        $passwordRules = $user ? ['nullable', Password::min(8)] : ['required', Password::min(8)];

        return $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
                'password' => [...$passwordRules, 'confirmed'],
                'nik' => ['nullable', 'string', 'max:20', Rule::unique('users', 'nik')->ignore($userId)],
                'profile_picture' => ['nullable', 'string', 'max:1024'],
                'position' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'digits_between:8,20'],
                'alamat' => ['nullable', 'string', 'max:1000'],
                'started_at' => ['nullable', 'date'],
            ],
            [
                'email.unique' => 'Email sudah digunakan oleh user lain.',
                'nik.unique' => 'NIK / NPW sudah digunakan oleh user lain.',
                'phone.digits_between' => 'Nomor telepon harus berupa angka dengan panjang 8 sampai 20 digit.',
                'password.confirmed' => 'Konfirmasi password tidak sama.',
            ]
        );
    }

    private function ensureEmployee(User $user): void
    {
        abort_unless((int) $user->role_id === 2, 404);
    }

    private function updateStartedAt(User $user, ?string $startedAt): void
    {
        if (! $startedAt) {
            return;
        }

        $user->forceFill([
            'created_at' => $startedAt . ' 08:00:00',
        ])->save();
    }

    private function userResponse(Request $request, User $user, string $message, int $status = 200)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'data' => $user->detailData(),
                'message' => $message,
            ], $status);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', $message);
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'nik', 'profile_picture', 'position', 'phone', 'alamat', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function detailData(array $extra = []): array
    {
        $this->loadAttendanceTotalCounts();

        return array_merge([
            'id' => $this->id,
            'role_id' => $this->role_id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'profile_picture' => $this->profilePictureUrl(),
            'nik' => $this->nik,
            'position' => $this->position,
            'phone' => $this->phone,
            'alamat' => $this->alamat,
            'total' => [
                'present' => $this->present_attendances_count ?? 0,
                'late' => $this->late_attendances_count ?? 0,
                'leave' => $this->leave_attendances_count ?? 0,
                'absent' => $this->absent_attendances_count ?? 0,
            ],
        ], $extra);
    }

    private function loadAttendanceTotalCounts(): void
    {
        $this->loadCount([
            'attendances as present_attendances_count' => function ($query) {
                $query->where('status', Attendance::STATUS_PRESENT);
            },
            'attendances as late_attendances_count' => function ($query) {
                $query->where('status', Attendance::STATUS_LATE);
            },
            'attendances as leave_attendances_count' => function ($query) {
                $query->where('status', Attendance::STATUS_LEAVE);
            },
            'attendances as absent_attendances_count' => function ($query) {
                $query->where('status', Attendance::STATUS_ABSENT);
            },
        ]);
    }

    private function profilePictureUrl(): ?string
    {
        if (! $this->profile_picture) {
            return null;
        }

        if (Str::startsWith($this->profile_picture, ['http://', 'https://'])) {
            return $this->profile_picture;
        }

        return asset('storage/' . ltrim($this->profile_picture, '/'));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

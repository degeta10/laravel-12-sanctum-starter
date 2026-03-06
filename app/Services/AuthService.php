<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function authenticate(array $data): ?User
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return null;
        }

        if (!Hash::check($data['password'], $user->password)) {
            return null;
        }

        if (!$user->hasVerifiedEmail()) {
            return null;
        }

        return $user;
    }

    public function generateToken(User $user): string
    {
        return $user->createToken("auth_token_{$user->id}")
            ->plainTextToken;
    }

    public function createUser(array $data): User
    {
        $data['email_verified_at'] = now();
        return User::create($data);
    }

    public function updateUser(User $user, array $data): User
    {
        $user->update($data);
        return $user->refresh();
    }
}

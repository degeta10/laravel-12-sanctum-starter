<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class ApiLoginUserAction
{
    /**
     * Authenticate the user and return an access token.
     *
     * @return array{user: User, access_token: string}|null
     */
    public function execute(array $credentials): ?array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        $token = $user->createToken('auth_token_'.$user->id)
            ->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $token,
        ];
    }
}

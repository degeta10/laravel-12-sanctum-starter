<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class RegisterUserAction
{
    /**
     * Register a new user with the given data.
     *
     * @param  array  $data  The data to register the user with.
     * @return User The registered user instance.
     */
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $data['email_verified_at'] = now();

            return User::create($data);
        });
    }
}

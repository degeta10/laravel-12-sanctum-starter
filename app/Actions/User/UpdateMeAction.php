<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateMeAction
{
    /**
     * Update the given user's profile with the provided data.
     *
     * @param  User  $user  The user to update.
     * @param  array  $data  The data to update the user with.
     * @return User The updated user instance.
     */
    public function execute(User $user, array $data): User
    {
        return DB::transaction(
            function () use ($user, $data) {
                $user->update($data);

                return $user->refresh();
            }
        );
    }
}

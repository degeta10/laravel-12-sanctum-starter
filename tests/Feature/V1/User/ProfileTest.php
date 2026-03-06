<?php

namespace Tests\Feature\V1\User;

use PHPUnit\Framework\Attributes\Test;
use Tests\ApiV1TestCase;

class ProfileTest extends ApiV1TestCase
{
    #[Test]
    public function user_can_update_name_in_profile(): void
    {
        $this->authUser();
        $updatedName = 'Jane Doe';

        $this->putApi('/user/profile', [
            'name' => $updatedName,
        ])
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'email',
                    'name',
                ]
            ])
            ->assertJsonPath('data.name', $updatedName);
    }

    #[Test]
    public function user_can_update_password_in_profile(): void
    {
        $user = $this->authUser();
        $updatedPassword = 'new-password';

        $this->putApi('/user/profile', [
            'name' => $user->name,
            'password' => $updatedPassword,
            'password_confirmation' => $updatedPassword,
        ])
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'email',
                    'name',
                ]
            ]);
    }
}

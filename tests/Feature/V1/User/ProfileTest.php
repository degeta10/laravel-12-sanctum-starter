<?php

declare(strict_types=1);

namespace Tests\Feature\V1\User;

use PHPUnit\Framework\Attributes\Test;
use Tests\ApiV1TestCase;

final class ProfileTest extends ApiV1TestCase
{
    #[Test]
    public function user_can_view_profile(): void
    {
        $user = $this->authUser();

        $this->getApi('/me')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'email',
                    'name',
                ],
            ])
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.name', $user->name);
    }

    #[Test]
    public function user_can_update_name_in_profile(): void
    {
        $this->authUser();
        $updatedName = 'Jane Doe';

        $this->patchApi('/me', [
            'name' => $updatedName,
        ])
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'email',
                    'name',
                ],
            ])
            ->assertJsonPath('data.name', $updatedName);
    }

    #[Test]
    public function user_can_update_password_in_profile(): void
    {
        $user = $this->authUser();
        $updatedPassword = 'new-password';

        $this->patchApi('/me', [
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
                ],
            ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_view_profile(): void
    {
        $this->getApi('/me')
            ->assertUnauthorized();
    }

    #[Test]
    public function unauthenticated_user_cannot_update_profile(): void
    {
        $this->patchApi('/me', [
            'name' => 'Updated Name',
        ])
            ->assertUnauthorized();
    }
}

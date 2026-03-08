<?php

namespace Tests\Feature\V1\Auth;

use PHPUnit\Framework\Attributes\Test;
use Tests\ApiV1TestCase;

class MeTest extends ApiV1TestCase
{
    #[Test]
    public function authenticated_user_can_retrieve_me(): void
    {
        $user = $this->authUser();

        $response = $this->getApi('/me');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'name',
                    'email',
                ],
            ])
            ->assertJsonPath('data.name', $user->name)
            ->assertJsonPath('data.email', $user->email);
    }

    #[Test]
    public function guest_cannot_access_me_endpoint(): void
    {
        $response = $this->getApi('/me');
        $response->assertUnauthorized();
    }
}

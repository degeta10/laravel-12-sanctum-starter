<?php

declare(strict_types=1);

namespace Tests\Feature\V1\Auth;

use PHPUnit\Framework\Attributes\Test;
use Tests\ApiV1TestCase;

final class LogoutTest extends ApiV1TestCase
{
    #[Test]
    public function api_user_can_logout_and_revoke_token(): void
    {
        $user = $this->authUser();

        $response = $this->postApi('/logout');

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertCount(0, $user->fresh()->tokens);
        $this->assertGuest('api');
    }

    #[Test]
    public function web_user_can_logout_and_clear_session(): void
    {
        $user = $this->createUser();

        $this->get('/sanctum/csrf-cookie');
        $this->actingAs($user, 'web');

        $response = $this->postWeb('/logout');

        $response->assertOk()
            ->assertJson(['success' => true]);
        $this->assertGuest('web');
    }
}

<?php

namespace Tests\Feature\V1\Auth;

use PHPUnit\Framework\Attributes\Test;
use Tests\ApiV1TestCase;


class LoginTest extends ApiV1TestCase
{
    #[Test]
    public function api_user_can_login_with_token(): void
    {
        $user = $this->createUser();

        $this->postApi('/auth/login', [
            'email' => $user->email,
            'password' => $this->userPassword,
        ])
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token_type',
                    'access_token',
                    'user' => [
                        'name',
                        'email'
                    ]
                ]
            ]);
    }

    #[Test]
    public function web_user_can_login_with_session(): void
    {
        $user = $this->createUser();

        $this->postWeb('/auth/login', [
            'email' => $user->email,
            'password' => $this->userPassword,
        ])
            ->assertOk()
            ->assertJsonMissing(['data.access_token']);

        $this->assertAuthenticated();
    }

    #[Test]
    public function login_fails_with_invalid_credentials(): void
    {
        $user = $this->createUser();

        $payloads = [
            ['email' => 'wrong@email.com', 'password' => $this->userPassword],
            ['email' => $user->email, 'password' => 'wrong-pass'],
        ];

        foreach ($payloads as $payload) {
            $this->postApi('/auth/login', $payload)->assertUnauthorized();
        }
    }
}

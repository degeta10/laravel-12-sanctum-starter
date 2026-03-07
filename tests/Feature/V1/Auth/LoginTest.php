<?php

namespace Tests\Feature\V1\Auth;

use PHPUnit\Framework\Attributes\Test;
use Tests\ApiV1TestCase;

class LoginTest extends ApiV1TestCase
{
    private const LOGIN_URL = '/auth/login';

    #[Test]
    public function login_returns_bearer_token_for_stateless_requests(): void
    {
        $user = $this->createUser();

        $this->postApi(self::LOGIN_URL, [
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
                        'email',
                    ],
                ],
            ]);
    }

    #[Test]
    public function login_creates_session_for_stateful_requests(): void
    {
        $user = $this->createUser();

        $this->postWeb(self::LOGIN_URL, [
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
            [
                'email' => 'wrong@email.com',
                'password' => $this->userPassword,
            ],
            [
                'email' => $user->email,
                'password' => 'wrongpass',
            ],
        ];

        foreach ($payloads as $payload) {
            $this->postApi(self::LOGIN_URL, $payload)->assertUnauthorized();
        }
    }
}

<?php

namespace Tests\Feature\V1\Auth;

use PHPUnit\Framework\Attributes\Test;
use Tests\ApiV1TestCase;

class RegisterTest extends ApiV1TestCase
{
    #[Test]
    public function user_can_register_successfully(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => $this->userPassword,
            'password_confirmation' => $this->userPassword,
        ];

        $response = $this->postApi('/auth/register', $payload);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);
    }
}

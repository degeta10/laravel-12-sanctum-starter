<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticate_returns_user_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $service = new AuthService(app(UserService::class));

        $authenticated = $service->authenticate([
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertNotNull($authenticated);
        $this->assertTrue($authenticated->is($user));
    }

    #[Test]
    public function authenticate_returns_null_when_user_not_found(): void
    {
        $service = new AuthService(app(UserService::class));

        $authenticated = $service->authenticate([
            'email' => 'missing@example.com',
            'password' => 'password123',
        ]);

        $this->assertNull($authenticated);
    }

    #[Test]
    public function authenticate_returns_null_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $service = new AuthService(app(UserService::class));

        $authenticated = $service->authenticate([
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertNull($authenticated);
    }

    #[Test]
    public function generate_token_returns_plain_text_token(): void
    {
        $user = User::factory()->create();
        $service = new AuthService(app(UserService::class));

        $token = $service->generateToken($user);

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertStringContainsString('|', $token);
    }

    #[Test]
    public function register_user_sets_email_verified_at_and_delegates_to_user_service(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $expectedUser = new User([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => $payload['password'],
            'email_verified_at' => now(),
        ]);

        $userService = Mockery::mock(UserService::class);
        $userService
            ->shouldReceive('createUser')
            ->once()
            ->withArgs(function (array $data) use ($payload): bool {
                return $data['name'] === $payload['name']
                    && $data['email'] === $payload['email']
                    && $data['password'] === $payload['password']
                    && isset($data['email_verified_at']);
            })
            ->andReturn($expectedUser);

        $service = new AuthService($userService);

        $created = $service->registerUser($payload);

        $this->assertSame($expectedUser, $created);
    }
}

<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class ApiV1TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected string $baseUrl = '/api/v1';

    protected string $userPassword = 'password123';

    protected function authUser(?User $user = null): User
    {
        $user ??= User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        return $user;
    }

    protected function getApi(string $uri, array $headers = [])
    {
        return $this->getJson($this->baseUrl.$uri, $headers);
    }

    protected function postApi(string $uri, array $data = [], array $headers = [])
    {
        return $this->postJson($this->baseUrl.$uri, $data, $headers);
    }

    protected function postWeb(string $uri, array $data = [])
    {
        $this->get('/sanctum/csrf-cookie');

        return $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            'Referer' => config('app.url'),
            'Accept' => 'application/json',
        ])->post($this->baseUrl.$uri, $data);
    }

    protected function putApi(string $uri, array $data = [], array $headers = [])
    {
        return $this->putJson($this->baseUrl.$uri, $data, $headers);
    }

    protected function patchApi(string $uri, array $data = [], array $headers = [])
    {
        return $this->patchJson($this->baseUrl.$uri, $data, $headers);
    }

    protected function deleteApi(string $uri, array $headers = [])
    {
        return $this->deleteJson($this->baseUrl.$uri, [], $headers);
    }

    /**
     * Create a user for testing.
     */
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'password' => $this->userPassword,
        ], $attributes));
    }

    /**
     * Generate Sanctum token for testing.
     */
    protected function createAuthToken(User $user): string
    {
        return $user->createToken('test-token')->plainTextToken;
    }
}

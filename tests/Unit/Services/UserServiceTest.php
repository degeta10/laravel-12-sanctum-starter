<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function create_user_persists_user_data(): void
    {
        $service = new UserService;

        $user = $service->createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    #[Test]
    public function update_user_updates_name_and_returns_refreshed_model(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
        ]);

        $service = new UserService;

        $updated = $service->updateUser($user, [
            'name' => 'New Name',
        ]);

        $this->assertSame('New Name', $updated->name);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    #[Test]
    public function update_user_hashes_password_via_model_cast(): void
    {
        $user = User::factory()->create([
            'password' => 'initial-pass',
        ]);

        $service = new UserService;

        $updated = $service->updateUser($user, [
            'password' => 'new-password123',
        ]);

        $this->assertTrue(Hash::check('new-password123', $updated->password));
    }
}

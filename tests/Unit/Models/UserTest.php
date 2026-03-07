<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function password_is_hashed_on_create(): void
    {
        $user = User::factory()->create([
            'password' => 'plain-password',
        ]);

        $this->assertNotSame('plain-password', $user->password);
        $this->assertTrue(Hash::check('plain-password', $user->password));
    }

    #[Test]
    public function verified_scope_returns_only_verified_users(): void
    {
        $verified = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        User::factory()->unverified()->create();

        $users = User::query()->verified()->get();

        $this->assertCount(1, $users);
        $this->assertTrue($users->first()->is($verified));
    }

    #[Test]
    public function unverified_scope_returns_only_unverified_users(): void
    {
        User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $unverified = User::factory()->unverified()->create();

        $users = User::query()->unverified()->get();

        $this->assertCount(1, $users);
        $this->assertTrue($users->first()->is($unverified));
    }

    #[Test]
    public function fillable_hidden_and_casts_are_configured(): void
    {
        $user = new User;

        $this->assertEquals([
            'name',
            'email',
            'password',
            'email_verified_at',
        ], $user->getFillable());

        $this->assertContains('password', $user->getHidden());
        $this->assertContains('remember_token', $user->getHidden());

        $casts = $user->getCasts();
        $this->assertSame('datetime', $casts['email_verified_at']);
        $this->assertSame('hashed', $casts['password']);
    }
}

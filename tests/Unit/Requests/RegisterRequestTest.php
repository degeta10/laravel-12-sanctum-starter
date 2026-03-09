<?php

declare(strict_types=1);

namespace Tests\Unit\Requests;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

final class RegisterRequestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function validation_passes_with_valid_payload(): void
    {
        $request = new RegisterRequest;

        $validator = Validator::make([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $request->rules(), $request->messages());

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function validation_fails_for_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'taken@example.com',
        ]);

        $request = new RegisterRequest;

        $validator = Validator::make([
            'name' => 'Test User',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function validation_fails_for_password_confirmation_mismatch(): void
    {
        $request = new RegisterRequest;

        $validator = Validator::make([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password999',
        ], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function prepare_for_validation_normalizes_email(): void
    {
        $request = RegisterRequest::create('/register', 'POST', [
            'email' => '  USER@Example.COM ',
            'name' => 'User Name',
        ]);

        $method = new ReflectionMethod(RegisterRequest::class, 'prepareForValidation');
        $method->invoke($request);

        $this->assertSame('user@example.com', $request->input('email'));
    }
}

<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    #[Test]
    public function validation_passes_with_valid_payload(): void
    {
        $request = new LoginRequest;

        $validator = Validator::make([
            'email' => 'test@example.com',
            'password' => 'password123',
        ], $request->rules(), $request->messages());

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function validation_fails_with_missing_fields(): void
    {
        $request = new LoginRequest;

        $validator = Validator::make([], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function prepare_for_validation_normalizes_email(): void
    {
        $request = LoginRequest::create('/login', 'POST', [
            'email' => '  USER@Example.COM ',
            'password' => 'password123',
        ]);

        $method = new ReflectionMethod(LoginRequest::class, 'prepareForValidation');
        $method->invoke($request);

        $this->assertSame('user@example.com', $request->input('email'));
    }
}

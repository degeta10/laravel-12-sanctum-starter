<?php

declare(strict_types=1);

namespace Tests\Unit\Requests;

use App\Http\Requests\Auth\UpdateProfileRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UpdateProfileRequestTest extends TestCase
{
    #[Test]
    public function validation_passes_with_empty_payload_due_to_sometimes_rules(): void
    {
        $request = new UpdateProfileRequest;

        $validator = Validator::make([], $request->rules(), $request->messages());

        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function validation_fails_for_short_password(): void
    {
        $request = new UpdateProfileRequest;

        $validator = Validator::make([
            'password' => 'short',
            'password_confirmation' => 'short',
        ], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function validation_fails_for_password_confirmation_mismatch(): void
    {
        $request = new UpdateProfileRequest;

        $validator = Validator::make([
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ], $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    #[Test]
    public function validation_passes_for_valid_name_and_password(): void
    {
        $request = new UpdateProfileRequest;

        $validator = Validator::make([
            'name' => 'Updated Name',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $request->rules(), $request->messages());

        $this->assertTrue($validator->passes());
    }
}

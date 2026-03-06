<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Api\V1\AuthResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Response;


class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->createUser($request->validated());

        if (!$user) {
            return response()->error(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'Registration failed. Please try again.',
            );
        }

        return response()->success(
            Response::HTTP_CREATED,
            'Registration successful. Please verify your email before logging in.'
        );
    }

    public function login(LoginRequest $request)
    {
        $user = $this->authService->authenticate($request->validated());

        if (!$user) {
            return response()->error(
                Response::HTTP_UNAUTHORIZED,
                'Invalid credentials',

            );
        }

        $token = $this->authService->generateToken($user);

        return response()->success(
            Response::HTTP_OK,
            'Login successful',
            new AuthResource([
                'access_token' => $token,
                'user' => $user
            ])
        );
    }

    public function me()
    {
        $user = auth()->user();
        return response()->success(
            Response::HTTP_OK,
            'User details retrieved successfully',
            new UserResource($user),
        );
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->success(
            Response::HTTP_OK,
            'Logged out successfully',
        );
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Api\V1\AuthResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
        return $request->hasSession()
            ? $this->webLogin($request)
            : $this->apiLogin($request);
    }

    private function apiLogin(LoginRequest $request)
    {
        $user = $this->authService->authenticate($request->validated());

        if (!$user) {
            return response()->error(401, 'Invalid credentials');
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

    public function webLogin(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return response()->error(
                Response::HTTP_UNAUTHORIZED,
                'Invalid credentials',
            );
        }

        $request->session()->regenerate();

        return response()->success(
            Response::HTTP_OK,
            'Login successful',
            new UserResource($request->user())
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

    public function logout(Request $request)
    {
        return $request->hasSession()
            ? $this->webLogout($request)
            : $this->apiLogout();
    }

    public function apiLogout()
    {
        auth()->user()->tokens()->delete();
        return response()->success(
            Response::HTTP_OK,
            'Logged out successfully',
        );
    }

    public function webLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->success(
            Response::HTTP_OK,
            'Logged out successfully',
        );
    }
}

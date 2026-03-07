<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Api\V1\AuthResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $this->authService->registerUser($request->validated());

        return response()->success(
            Response::HTTP_CREATED,
            'Registration successful. Please verify your email before logging in.'
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $request->hasSession()
            ? $this->webLogin($request)
            : $this->apiLogin($request);
    }

    private function apiLogin(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->authenticate($request->validated());

        if (! $user) {
            return response()->error(Response::HTTP_UNAUTHORIZED, 'Invalid credentials');
        }

        $token = $this->authService->generateToken($user);

        return response()->success(
            Response::HTTP_OK,
            'Login successful',
            new AuthResource([
                'access_token' => $token,
                'user' => $user,
            ])
        );
    }

    public function webLogin(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->validated())) {
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

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->success(
            Response::HTTP_OK,
            'User details retrieved successfully',
            new UserResource($user),
        );
    }

    public function logout(Request $request): JsonResponse
    {
        return $request->hasSession()
            ? $this->webLogout($request)
            : $this->apiLogout($request);
    }

    public function apiLogout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->success(
            Response::HTTP_OK,
            'Logged out successfully',
        );
    }

    public function webLogout(Request $request): JsonResponse
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

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\ApiLoginUserAction;
use App\Actions\Auth\ApiLogoutUserAction;
use App\Actions\Auth\WebLoginUserAction;
use App\Actions\Auth\WebLogoutUserAction;
use App\Actions\User\RegisterUserAction;
use App\Actions\User\UpdateMeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\Api\V1\AuthResource;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class AuthController extends Controller
{
    public function __construct(
        private readonly ApiLoginUserAction $loginUserAction,
        private readonly WebLoginUserAction $webLoginAction,
        private readonly ApiLogoutUserAction $apiLogoutAction,
        private readonly WebLogoutUserAction $webLogoutAction,
        private readonly UpdateMeAction $updateProfileAction,
    ) {}

    public function register(RegisterRequest $request, RegisterUserAction $action): JsonResponse
    {
        $action->execute($request->validated());

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

    public function webLogin(LoginRequest $request): JsonResponse
    {
        $user = $this->webLoginAction->execute($request->validated(), $request);

        if (! $user instanceof \App\Models\User) {
            return response()->error(
                Response::HTTP_UNAUTHORIZED,
                'Invalid credentials',
            );
        }

        return response()->success(
            Response::HTTP_OK,
            'Login successful',
            new UserResource($user)
        );
    }

    public function getMe(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->success(
            Response::HTTP_OK,
            'User details retrieved successfully',
            new UserResource($user),
        );
    }

    public function updateMe(UpdateProfileRequest $request): JsonResponse
    {
        $updatedUser = $this->updateProfileAction->execute(
            $request->user(),
            $request->validated()
        );

        return response()->success(
            Response::HTTP_OK,
            'User details updated successfully',
            new UserResource($updatedUser),
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
        $this->apiLogoutAction->execute($request);

        return response()->success(
            Response::HTTP_OK,
            'Logged out successfully',
        );
    }

    public function webLogout(Request $request): JsonResponse
    {
        $this->webLogoutAction->execute($request);

        return response()->success(
            Response::HTTP_OK,
            'Logged out successfully',
        );
    }

    private function apiLogin(LoginRequest $request): JsonResponse
    {
        $result = $this->loginUserAction->execute($request->validated());

        if (! $result) {
            return response()->error(Response::HTTP_UNAUTHORIZED, 'Invalid credentials');
        }

        return response()->success(
            Response::HTTP_OK,
            'Login successful',
            new AuthResource([
                'access_token' => $result['token'],
                'user' => $result['user'],
            ])
        );
    }
}

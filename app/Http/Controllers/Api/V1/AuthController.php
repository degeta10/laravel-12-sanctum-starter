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

final class AuthController extends Controller
{
    public function __construct(
        private readonly ApiLoginUserAction $apiLoginAction,
        private readonly WebLoginUserAction $webLoginAction,
        private readonly ApiLogoutUserAction $apiLogoutAction,
        private readonly WebLogoutUserAction $webLogoutAction,
    ) {}

    public function register(RegisterRequest $request, RegisterUserAction $action): JsonResponse
    {
        $action->execute($request->validated());

        return $this->created();
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $request->hasSession()
            ? $this->webLogin($request)
            : $this->apiLogin($request);
    }

    public function getMe(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->success(
            new UserResource($user)
        );
    }

    public function updateMe(UpdateProfileRequest $request, UpdateMeAction $action): JsonResponse
    {
        $updatedUser = $action->execute(
            $request->user(),
            $request->validated()
        );

        return $this->success(
            new UserResource($updatedUser),
        );
    }

    public function logout(Request $request): JsonResponse
    {
        if ($request->hasSession()) {
            $this->webLogoutAction->execute($request);
        } else {
            $this->apiLogoutAction->execute($request);
        }

        return $this->success();
    }

    private function apiLogin(LoginRequest $request): JsonResponse
    {
        $result = $this->apiLoginAction->execute($request->validated());

        if (! $result) {
            return $this->unauthorized('Invalid credentials');
        }

        return $this->success(
            new AuthResource($result)
        );
    }

    private function webLogin(LoginRequest $request): JsonResponse
    {
        $user = $this->webLoginAction->execute($request->validated(), $request);

        if (!$user instanceof \App\Models\User) {
            return $this->unauthorized('Invalid credentials');
        }

        return $this->success(
            new UserResource($user)
        );
    }
}

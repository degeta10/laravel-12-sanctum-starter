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
    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request, RegisterUserAction $action): JsonResponse
    {
        $action->execute($request->validated());

        return $this->created();
    }

    /**
     * Handle user login for both API and Web clients.
     * Determines the type of login based on the presence of a session.
     */
    public function login(
        LoginRequest $request,
        ApiLoginUserAction $apiLoginAction,
        WebLoginUserAction $webLoginAction
    ): JsonResponse {
        $isWeb = $request->hasSession();

        $result = $isWeb
            ? $webLoginAction->execute($request->validated(), $request)
            : $apiLoginAction->execute($request->validated());

        if (! $result) {
            return $this->unauthorized('Invalid credentials');
        }

        return $this->success(
            $isWeb ? new UserResource($result) : new AuthResource($result)
        );
    }

    /**
     * Retrieve the authenticated user's details.
     */
    public function getMe(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->success(
            new UserResource($user)
        );
    }

    /**
     * Update the authenticated user's profile.
     */
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

    /**
     * Logout the user by determining the request type (API or Web) and executing the appropriate logout action.
     */
    public function logout(
        Request $request,
        ApiLogoutUserAction $apiLogoutAction,
        WebLogoutUserAction $webLogoutAction
    ): JsonResponse {
        $request->hasSession()
            ? $webLogoutAction->execute($request)
            : $apiLogoutAction->execute($request);

        return $this->success();
    }
}

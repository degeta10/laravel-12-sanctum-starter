<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $updatedUser = $this->userService->updateUser(
            $request->user(),
            $request->validated()
        );

        return response()->success(
            Response::HTTP_OK,
            'Profile updated successfully',
            new UserResource($updatedUser),
        );
    }
}

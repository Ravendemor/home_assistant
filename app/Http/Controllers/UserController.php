<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\Authorize\AuthorizeRequest;
use App\Http\Requests\User\Authorize\GoogleAuthorizeRequest;
use App\Http\Requests\User\EditProfileRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function auth(AuthorizeRequest $request): JsonResponse
    {
        return $this->provideServiceResponse($this->userService->authorizeByPassword($request->all()));
    }

    public function authWithGoogle(GoogleAuthorizeRequest $request): JsonResponse
    {
        return $this->provideServiceResponse($this->userService->authorizeByGoogle($request->all()));
    }

    public function editProfile(EditProfileRequest $request): JsonResponse
    {
        return $this->provideServiceResponse($this->userService->editProfile($request->all()));
    }

    public function logout(): JsonResponse
    {
        return $this->provideServiceResponse($this->userService->logout());
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseApiController
{
    public function register(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = User::create($request->validated());
            return $this->successResponse(UserResource::make($user), 'User register successfully.', 201);
        } catch (\Throwable  $e) {
            Log::error('AuthController::register(): ' . $e->getMessage());
            return $this->errorResponse('$e->getMessage()', 500, $e);
        }
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->errorResponse('Wrong email or password', 401);
        }

        /** @var User $user */

        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken("Token of user: {$user->name}")->plainTextToken;

        return $this->successResponse([
            'user' => UserResource::make($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful.');
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
        }

        return $this->successResponse(null, 'Logged out successfully.');
    }
}

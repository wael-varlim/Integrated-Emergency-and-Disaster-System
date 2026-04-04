<?php


namespace App\Services;

use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\KnownUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    use ApiResponseTrait;

    public function attemptLogin(LoginRequest $request): JsonResponse
    {
        $knownUser =KnownUser::where('email', $request->email)->first();

        if(! $knownUser || ! Hash::check($request->password, $knownUser->password))
            return $this->apiResponse(null, 'Invalid credentials', 401);

        $user = $knownUser->user;

        if(! $user->hasRole('mobile_user'))
            return $this->apiResponse(null, 'Unauthorized', 403);

        return $this->apiResponse($user->createToken($request->device_name)->plainTextToken, 'login Successfully', 200);
    }
}
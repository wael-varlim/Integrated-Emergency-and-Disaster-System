<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct(protected AuthService $auth_service)
    {
        
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $response = $this->auth_service->attemptLogin($request);

        return $response;
    }

    public function logout()
    {
        return 'done ;)';
    }
}

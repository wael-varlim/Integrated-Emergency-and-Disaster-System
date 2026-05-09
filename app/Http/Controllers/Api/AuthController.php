<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Requests\Auth\RegisterRequest;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;

class AuthController extends Controller
{
    use ApiResponseTrait;


    public function __construct(protected AuthService $auth_service)
    {
        
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns',
        ]);
    
        return $this->auth_service->sendOtp($request);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email'         => 'required|email',
            'otp'           => 'required|digits:6',
            'device_name'   => 'required|string|max:255',
        ]);

        return $this->auth_service->verifyOtp($request);
    }

    public function register(RegisterRequest $request)
    {
        return $this->auth_service->attemptRegister($request);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->auth_service->attemptLogin($request);
    }

    public function logout(Request $request)
    {
        return $this->auth_service->attemptLogout($request);
    }
}

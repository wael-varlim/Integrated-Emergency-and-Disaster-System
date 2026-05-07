<?php


namespace App\Services;

use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\OtpMail;
use App\Models\City;
use App\Models\KnownUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    use ApiResponseTrait;


    private function sendOtp(Request $request)
    {
        $otp = rand(100000, 999999);

        Cache::put("email_otp_{$request->email}", $otp, now()-> addMinute(10));

        Mail::to($request->email)->send(new OtpMail($otp));

        return $this->apiResponse(null, 'code sent successfully', 200);
    }

    public function verifyOtp(Request $request)
    {
        $stored = Cache::get("email_otp_{$request->email}");
        if ($stored != $request->otp)
            return $this->apiResponse(null, 'Incorrect code', 422);

        Cache::forget("email_otp_{$request->email}");
        
        return $this->apiResponse(null, 'verification done successfully', 200);
    }

    public function verifyEmail(Request $request)
    {
        if(KnownUser::where('email', $request->email)->first() != null)
            return $this->apiResponse(null, 'this email is already used', 404);

        return $this->sendOtp($request);
    }

    public function attemptRegister(RegisterRequest $request)
    {

        $city = City::where('name', $request->address)->firstOrFail();
        $regionId = $city->region_id;

        $user = User::create([
            'user_type' => 'Known user'
        ]);
        $user->assignRole('mobile_user');

        $user->region()->attach($regionId);

        $knownUser  = KnownUser::create([
            'user_id'                    => $user->id, 
            'first_name'                 => $request->first_name,
            'last_name'                  => $request->last_name,
            'email'                      => $request->email,
            'password'                   => Hash::make($request->password),
            'official_identifier_method' => $request->official_identifier_method,
            'official_identifier'        => $request->official_identifier,
        ]);



        $token = $user->createToken($request->device_name)->plainTextToken;

        return $this->apiResponse(['token' => $token, 'user' => $knownUser], 'Registered Successfully', 201);

        // return $this->apiResponse([
        //     'token' => $token,
        //     'user'  => $user
        // ], 'Registered successfully', 201);
    }

    public function attemptLogin(LoginRequest $request)
    {
        $knownUser =KnownUser::where('email', $request->email)->first();

        if(! $knownUser || ! Hash::check($request->password, $knownUser->password))
            return $this->apiResponse(null, 'Invalid credentials', 401);

        $user = $knownUser->user;

        if(! $user->hasRole('mobile_user'))
            return $this->apiResponse(null, 'Unauthorized', 403);

        return $this->apiResponse($user->createToken($request->device_name)->plainTextToken, 'login Successfully', 200);
    }


    public function attemptLogout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->apiResponse(null, 'Logged out successfully', 200);
    }
}
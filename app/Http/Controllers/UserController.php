<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegionsRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function updateLanguage(Request $request)
    {
        $request->validate([
            'language' => 'required|in:ar,en',
        ], [
            'language.in' => 'This language is not supported.',
        ]);

        return $this->userService->updateUserLanguage($request->user(), $request->language);
    }

    public function updateRegionPreferences(RegionsRequest $request)
    {
        return $this->userService->updateUserRegionPreferences(
            $request->user(),
            $request->validated('regions')
        );
    }

    public function storeFcmToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:255',
        ]);

        return $this->userService->storeUserFcmToken($request->user(), $request->token);
    }
}

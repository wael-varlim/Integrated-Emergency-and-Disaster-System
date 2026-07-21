<?php

namespace App\Http\Controllers;

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
}

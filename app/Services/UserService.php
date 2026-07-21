<?php

namespace App\Services;

use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserService
{
    use ApiResponseTrait;


    public function updateUserLanguage(User $user, string $language): JsonResponse
    {
        $user->update([
            'preferred_language' => $language,
        ]);

        return $this->apiResponse($language, 'language updated successfully', 200);

    }
}
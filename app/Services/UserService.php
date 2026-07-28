<?php

namespace App\Services;

use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Resources\RegionsResource;
use App\Models\City;
use App\Models\FcmToken;
use App\Models\Governorate;
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

    public function updateUserRegionPreferences(User $user, array $regionNames): JsonResponse
    {
        $cityRegionIds = City::whereIn('name', $regionNames)->pluck('region_id');
        $governorateRegionIds = Governorate::whereIn('name', $regionNames)->pluck('region_id');

        $regionIds = $cityRegionIds->merge($governorateRegionIds)->unique();

        $user->regions()->sync($regionIds);

        return $this->apiResponse(
            new RegionsResource($user->load(['regions.city', 'regions.governorate'])),
            'regions updated successfully',
            200
        );
    }

    public function storeUserFcmToken(User $user, string $token): JsonResponse
    {
        FcmToken::updateOrCreate(
            ['token' => $token],
            ['user_id' => $user->id]
        );

        return $this->apiResponse(
            null,
            'fcm token registered successfully',
            200
        );
    }
}
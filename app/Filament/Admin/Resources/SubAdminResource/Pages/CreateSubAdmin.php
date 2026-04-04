<?php

namespace App\Filament\Admin\Resources\SubAdminResource\Pages;

use App\Filament\Admin\Resources\SubAdminResource;
use App\Models\KnownUser;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CreateSubAdmin extends CreateRecord
{
    protected static string $resource = SubAdminResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // 1. Create and SAVE the User first
        $user = new User();
        $user->user_type = 'known';
        $user->save();

        // 2. Now $user->id is available
        KnownUser::create([
            'user_id'         => $user->id,  //  guaranteed to exist
            'national_number' => $data['national_number'],
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'email'           => $data['email'],
            'password'        => Hash::make($data['password']),
        ]);

        // 3. Assign roles
        if (! empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
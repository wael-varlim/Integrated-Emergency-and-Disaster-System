<?php

namespace App\Filament\Admin\Resources\SubAdminResource\Pages;

use App\Filament\Admin\Resources\SubAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EditSubAdmin extends EditRecord
{
    protected static string $resource = SubAdminResource::class;

    // Load KnownUser data into the form
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $knownUser = $this->record->knownUser;

        if ($knownUser) {
            $data['first_name']      = $knownUser->first_name;
            $data['last_name']       = $knownUser->last_name;
            $data['national_number'] = $knownUser->national_number;
            $data['email']           = $knownUser->email;
        }

        return $data;
    }

    // Save to both User and KnownUser
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $knownUserData = [
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'national_number' => $data['national_number'],
            'email'           => $data['email'],
        ];

        if (! empty($data['password'])) {
            $knownUserData['password'] = Hash::make($data['password']);
        }

        $record->knownUser->update($knownUserData);

        if (isset($data['roles'])) {
            $record->syncRoles($data['roles']);
        }

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn () => $this->record->knownUser?->delete()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
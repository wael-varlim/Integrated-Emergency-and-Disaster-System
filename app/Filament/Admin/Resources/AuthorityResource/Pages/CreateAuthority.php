<?php

namespace App\Filament\Admin\Resources\AuthorityResource\Pages;

use App\Filament\Admin\Resources\AuthorityResource;
use App\Models\Authority;
use App\Models\AuthorityType;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAuthority extends CreateRecord
{
    protected static string $resource = AuthorityResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // 1. Create AuthorityType
        $authorityType = AuthorityType::create([
            'type_name' => $data['authorityType']['type_name'],
        ]);

        // 2. Create Translations
        foreach ($data['authorityType']['authorityTranslation'] ?? [] as $translation) {
            $authorityType->authorityTranslation()->create([
                'languahe_code'    => $translation['languahe_code'],
                'translation'      => $translation['translation'],
                'authority_type_id'=> $authorityType->id,
            ]);
        }

        // 3. Create Authority linked to the new AuthorityType
        return Authority::create([
            'authority_type_id' => $authorityType->id,
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
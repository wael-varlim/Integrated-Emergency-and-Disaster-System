<?php

namespace App\Filament\Admin\Resources\AuthorityResource\Pages;

use App\Filament\Admin\Resources\AuthorityResource\AuthorityResource;
use App\Models\Authority;
use App\Models\AuthorityTranslation;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAuthority extends CreateRecord
{
    protected static string $resource = AuthorityResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract nested data from dot notation
        $processedData = [
            'name'    => $data['name']['en'] ?? $data['name']['ar'] ?? '',
            'name_ar' => $data['name']['ar'] ?? null,
            'name_en' => $data['name']['en'] ?? null,
            'authority_type_id' => $data['authority_type_id'],
        ];

        return $processedData;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Create Authority with English name as primary
        $authority = Authority::create([
            'name'              => $data['name'],
            'authority_type_id' => $data['authority_type_id'],
        ]);

        // Create Authority Translations
        if (!empty($data['name_ar'])) {
            AuthorityTranslation::create([
                'language_code' => 'ar',
                'translation'   => $data['name_ar'],
                'authority_id'  => $authority->id,
            ]);
        }
        
        if (!empty($data['name_en'])) {
            AuthorityTranslation::create([
                'language_code' => 'en',
                'translation'   => $data['name_en'],
                'authority_id'  => $authority->id,
            ]);
        }

        return $authority;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

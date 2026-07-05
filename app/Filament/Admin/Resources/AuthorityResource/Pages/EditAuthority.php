<?php

namespace App\Filament\Admin\Resources\AuthorityResource\Pages;

use App\Filament\Admin\Resources\AuthorityResource\AuthorityResource;
use App\Models\AuthorityTranslation;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAuthority extends EditRecord
{
    protected static string $resource = AuthorityResource::class;

    // Load existing authority data into the form with dot notation
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $authority = $this->record;
        
        // Load translations
        $arTranslation = $authority->authorityTranslation()->where('language_code', 'ar')->first();
        $enTranslation = $authority->authorityTranslation()->where('language_code', 'en')->first();
        
        $data['name'] = [
            'ar' => $arTranslation?->translation ?? '',
            'en' => $enTranslation?->translation ?? $authority->name,
        ];
        
        $data['authority_type_id'] = $authority->authority_type_id;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    // Save changes to Authority and its translations
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Update authority name
        $record->update([
            'name'              => $data['name'],
            'authority_type_id' => $data['authority_type_id'],
        ]);

        // Delete old translations
        $record->authorityTranslation()->delete();

        // Create new translations
        if (!empty($data['name_ar'])) {
            AuthorityTranslation::create([
                'language_code' => 'ar',
                'translation'   => $data['name_ar'],
                'authority_id'  => $record->id,
            ]);
        }
        
        if (!empty($data['name_en'])) {
            AuthorityTranslation::create([
                'language_code' => 'en',
                'translation'   => $data['name_en'],
                'authority_id'  => $record->id,
            ]);
        }

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasPermissionTo('delete_authority')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

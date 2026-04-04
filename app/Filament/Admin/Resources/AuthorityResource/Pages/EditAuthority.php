<?php

namespace App\Filament\Admin\Resources\AuthorityResource\Pages;

use App\Filament\Admin\Resources\AuthorityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAuthority extends EditRecord
{
    protected static string $resource = AuthorityResource::class;

    // Load existing AuthorityType data into the form
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $authorityType = $this->record->authorityType;

        if ($authorityType) {
            $data['authorityType'] = [
                'type_name' => $authorityType->type_name,
                'authorityTranslation' => $authorityType->authorityTranslation
                    ->map(fn ($t) => [
                        'languahe_code' => $t->languahe_code,
                        'translation'   => $t->translation,
                    ])
                    ->toArray(),
            ];
        }

        return $data;
    }

    // Save changes to AuthorityType and its translations
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $authorityType = $record->authorityType;

        if ($authorityType) {
            // Update type name
            $authorityType->update([
                'type_name' => $data['authorityType']['type_name'],
            ]);

            // Sync translations
            $authorityType->authorityTranslation()->delete();

            foreach ($data['authorityType']['authorityTranslation'] ?? [] as $translation) {
                $authorityType->authorityTranslation()->create([
                    'languahe_code'     => $translation['languahe_code'],
                    'translation'       => $translation['translation'],
                    'authority_type_id' => $authorityType->id,
                ]);
            }
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
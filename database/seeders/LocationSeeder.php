<?php
// database/seeders/LocationSeeder.php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Governorate;
use App\Models\Region;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        if (Governorate::count() > 0) {
            $this->command->info('Locations already seeded, skipping.');
            return;
        }

        $data = [
            'governorates' => [
                [
                    'name' => 'Damascus',
                    'name_ar' => 'دمشق',
                    'cities' => [
                        ['name' => 'Kafr Sousa',     'name_ar' => 'كفرسوسة'],
                        ['name' => 'Al-Mazza',           'name_ar' => 'المزة'],
                        ['name' => 'As-Salihiya',       'name_ar' => 'الصالحية'],
                        ['name' => 'Rukn ad-Din',            'name_ar' => 'ركن الدين'],
                        ['name' => 'Al-Muhajerin',        'name_ar' => 'المهاجرين'],
                        ['name' => 'Al-Midan',       'name_ar' => 'الميدان'],
                        ['name' => 'Ash-Shaghour',       'name_ar' => 'الشاغور'],
                        ['name' => 'Ancient City of Damascus',       'name_ar' => 'دمشق القديمة'],
                        ['name' => 'Sarouja',       'name_ar' => 'ساروجة'],
                        ['name' => 'Al-Qaboun',       'name_ar' => 'القابون'],
                        ['name' => 'Barza',       'name_ar' => 'برزة'],
                        ['name' => 'Dummar',       'name_ar' => 'دمر'],
                        ['name' => 'Al-Qanawat',       'name_ar' => 'القنوات'],
                        ['name' => 'Al-Yarmuk',       'name_ar' => 'اليرموك'],
                        ['name' => 'Joubar',       'name_ar' => 'جوبر'],
                        ['name' => 'Al-Qadam',       'name_ar' => 'القدم'],
                    ],
                ]
            ]
        ];


        foreach ($data['governorates'] as $govData) {
            $region = Region::create([]);

            // Create Governorate
            $governorate = Governorate::create([
                'name'      => $govData['name'],
                'region_id' => $region->id,
            ]);

            // Create Governorate Translation (Arabic)
            $governorate->governorateTranslation()->create([
                'languahe_code' => 'ar',
                'translation'   => $govData['name_ar'],
                'governorate_id'=> $governorate->id,
            ]);

            // Create Governorate Translation (English)
            $governorate->governorateTranslation()->create([
                'languahe_code' => 'en',
                'translation'   => $govData['name'],
                'governorate_id'=> $governorate->id,
            ]);

            foreach ($govData['cities'] as $cityData) {
                $region = Region::create([]);

                // Create City
                $city = City::create([
                    'name'           => $cityData['name'],
                    'governorate_id' => $governorate->id,
                    'region_id'      => $region->id,
                ]);

                // Create City Translation (Arabic)
                $city->cityTranslation()->create([
                    'languahe_code' => 'ar',
                    'translation'   => $cityData['name_ar'],
                    'city_id'       => $city->id,
                ]);

                // Create City Translation (English)
                $city->cityTranslation()->create([
                    'languahe_code' => 'en',
                    'translation'   => $cityData['name'],
                    'city_id'       => $city->id,
                ]);
            }
        }
        

        $this->command->info('✅ Locations seeded successfully!');
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Regions',      Region::count()],
                ['Governorates', Governorate::count()],
                ['Cities',       City::count()],
            ]
        );
    }
}
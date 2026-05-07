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
        $data = [
            'governorates' => [
                [
                    'name' => 'Damascus',
                    'name_ar' => 'دمشق',
                    'cities' => [
                        ['name' => 'Old Damascus',     'name_ar' => 'دمشق القديمة'],
                        ['name' => 'Mezzeh',           'name_ar' => 'المزة'],
                        ['name' => 'Kafr Sousa',       'name_ar' => 'كفر سوسة'],
                        ['name' => 'Malki',            'name_ar' => 'المالكي'],
                        ['name' => 'Bab Touma',        'name_ar' => 'باب توما'],
                    ],
                ],
                [
                    'name' => 'Rif Dimashq',
                    'name_ar' => 'ريف دمشق',
                    'cities' => [
                        ['name' => 'Douma',            'name_ar' => 'دوما'],
                        ['name' => 'Jaramana',         'name_ar' => 'جرمانا'],
                        ['name' => 'Harasta',          'name_ar' => 'حرستا'],
                        ['name' => 'Zabadani',         'name_ar' => 'الزبداني'],
                        ['name' => 'Qudsaya',          'name_ar' => 'قدسيا'],
                    ],
                ],
                [
                    'name' => 'Homs',
                    'name_ar' => 'حمص',
                    'cities' => [
                        ['name' => 'Homs City',        'name_ar' => 'مدينة حمص'],
                        ['name' => 'Rastan',           'name_ar' => 'الرستن'],
                        ['name' => 'Talbisah',         'name_ar' => 'تلبيسة'],
                        ['name' => 'Qusayr',           'name_ar' => 'القصير'],
                        ['name' => 'Palmyra',          'name_ar' => 'تدمر'],
                    ],
                ],
                [
                    'name' => 'Hama',
                    'name_ar' => 'حماة',
                    'cities' => [
                        ['name' => 'Hama City',        'name_ar' => 'مدينة حماة'],
                        ['name' => 'Mhardeh',          'name_ar' => 'محردة'],
                        ['name' => 'Salamiyah',        'name_ar' => 'سلمية'],
                        ['name' => 'Suqaylabiyah',     'name_ar' => 'صوران'],
                        ['name' => 'Kafr Nbodah',      'name_ar' => 'كفر نبودة'],
                    ],
                ],
                [
                    'name' => 'Aleppo',
                    'name_ar' => 'حلب',
                    'cities' => [
                        ['name' => 'Aleppo City',      'name_ar' => 'مدينة حلب'],
                        ['name' => 'Azaz',             'name_ar' => 'اعزاز'],
                        ['name' => 'Afrin',            'name_ar' => 'عفرين'],
                        ['name' => 'Al-Bab',           'name_ar' => 'الباب'],
                        ['name' => 'Manbij',           'name_ar' => 'منبج'],
                    ],
                ],
                [
                    'name' => 'Idlib',
                    'name_ar' => 'إدلب',
                    'cities' => [
                        ['name' => 'Idlib City',       'name_ar' => 'مدينة إدلب'],
                        ['name' => 'Jisr al-Shughur',  'name_ar' => 'جسر الشغور'],
                        ['name' => 'Maarat al-Numan',  'name_ar' => 'معرة النعمان'],
                        ['name' => 'Saraqib',          'name_ar' => 'سراقب'],
                        ['name' => 'Ariha',            'name_ar' => 'أريحا'],
                    ],
                ],
                [
                    'name' => 'Latakia',
                    'name_ar' => 'اللاذقية',
                    'cities' => [
                        ['name' => 'Latakia City',     'name_ar' => 'مدينة اللاذقية'],
                        ['name' => 'Jableh',           'name_ar' => 'جبلة'],
                        ['name' => 'Qardaha',          'name_ar' => 'القرداحة'],
                        ['name' => 'Haffah',           'name_ar' => 'الحفة'],
                        ['name' => 'Kasab',            'name_ar' => 'كسب'],
                    ],
                ],
                [
                    'name' => 'Tartus',
                    'name_ar' => 'طرطوس',
                    'cities' => [
                        ['name' => 'Tartus City',      'name_ar' => 'مدينة طرطوس'],
                        ['name' => 'Baniyas',          'name_ar' => 'بانياس'],
                        ['name' => 'Safita',           'name_ar' => 'صافيتا'],
                        ['name' => 'Dreikish',         'name_ar' => 'دريكيش'],
                        ['name' => 'Arwad',            'name_ar' => 'أرواد'],
                    ],
                ],
                [
                    'name' => 'Daraa',
                    'name_ar' => 'درعا',
                    'cities' => [
                        ['name' => 'Daraa City',       'name_ar' => 'مدينة درعا'],
                        ['name' => 'Nawa',             'name_ar' => 'نوى'],
                        ['name' => 'Izraa',            'name_ar' => 'إزرع'],
                        ['name' => 'Bosra',            'name_ar' => 'بصرى'],
                        ['name' => 'Tafas',            'name_ar' => 'طفس'],
                    ],
                ],
                [
                    'name' => 'As-Suwayda',
                    'name_ar' => 'السويداء',
                    'cities' => [
                        ['name' => 'As-Suwayda City',  'name_ar' => 'مدينة السويداء'],
                        ['name' => 'Shahba',           'name_ar' => 'شهبا'],
                        ['name' => 'Salkhad',          'name_ar' => 'صلخد'],
                        ['name' => 'Qanawat',          'name_ar' => 'قنوات'],
                        ['name' => 'Rasas',            'name_ar' => 'الرساس'],
                    ],
                ],
                [
                    'name' => 'Quneitra',
                    'name_ar' => 'القنيطرة',
                    'cities' => [
                        ['name' => 'Quneitra City',    'name_ar' => 'مدينة القنيطرة'],
                        ['name' => 'Fiq',              'name_ar' => 'فيق'],
                        ['name' => 'Hadar',            'name_ar' => 'حضر'],
                        ['name' => 'Khan Arnabah',     'name_ar' => 'خان أرنبة'],
                        ['name' => 'Masa\'adah',       'name_ar' => 'مسعدة'],
                    ],
                ],
                [
                    'name' => 'Deir ez-Zor',
                    'name_ar' => 'دير الزور',
                    'cities' => [
                        ['name' => 'Deir ez-Zor City', 'name_ar' => 'مدينة دير الزور'],
                        ['name' => 'Albu Kamal',       'name_ar' => 'البوكمال'],
                        ['name' => 'Mayadin',          'name_ar' => 'الميادين'],
                        ['name' => 'Al-Quriyah',       'name_ar' => 'القورية'],
                        ['name' => 'Al-Ashara',        'name_ar' => 'العشارة'],
                    ],
                ],
                [
                    'name' => 'Raqqa',
                    'name_ar' => 'الرقة',
                    'cities' => [
                        ['name' => 'Raqqa City',       'name_ar' => 'مدينة الرقة'],
                        ['name' => 'Tabqa',            'name_ar' => 'الطبقة'],
                        ['name' => 'Tal Abyad',        'name_ar' => 'تل أبيض'],
                        ['name' => 'Suluk',            'name_ar' => 'سلوك'],
                        ['name' => 'Maadan',           'name_ar' => 'المعدان'],
                    ],
                ],
                [
                    'name' => 'Al-Hasakah',
                    'name_ar' => 'الحسكة',
                    'cities' => [
                        ['name' => 'Al-Hasakah City',  'name_ar' => 'مدينة الحسكة'],
                        ['name' => 'Qamishli',         'name_ar' => 'القامشلي'],
                        ['name' => 'Ras al-Ayn',       'name_ar' => 'رأس العين'],
                        ['name' => 'Derek',            'name_ar' => 'ديريك'],
                        ['name' => 'Al-Malikiyah',     'name_ar' => 'المالكية'],
                    ],
                ],
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
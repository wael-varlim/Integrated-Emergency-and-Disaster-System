<?php

namespace Database\Seeders;

use App\Models\NewsType;
use App\Models\NewsTypeTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $news_types = [
            // direct
            ['name' => 'Fire',             'name_ar' => 'حريق',            'post_visibility' => 'direct'],
            ['name' => 'Explosion',        'name_ar' => 'انفجار',           'post_visibility' => 'direct'],
            ['name' => 'Flood',            'name_ar' => 'فيضان',            'post_visibility' => 'direct'],
            ['name' => 'Earthquake',       'name_ar' => 'زلزال',            'post_visibility' => 'direct'],
            ['name' => 'Building collapse','name_ar' => 'انهيار مبنى',      'post_visibility' => 'direct'],
            ['name' => 'Armed robbery',    'name_ar' => 'سطو مسلح',         'post_visibility' => 'direct'],
            ['name' => 'Murder',           'name_ar' => 'قتل',              'post_visibility' => 'direct'],
            ['name' => 'Kidnapping',       'name_ar' => 'خطف',              'post_visibility' => 'direct'],
            ['name' => 'Gang violence',    'name_ar' => 'عنف عصابات',       'post_visibility' => 'direct'],
            ['name' => 'Missing person',   'name_ar' => 'شخص مفقود',        'post_visibility' => 'direct'],

            // ai
            ['name' => 'Injury',           'name_ar' => 'إصابة جسدية',      'post_visibility' => 'ai'],
            ['name' => 'Traffic accident', 'name_ar' => 'حادث مرور',        'post_visibility' => 'ai'],
            ['name' => 'Theft',            'name_ar' => 'سرقة',             'post_visibility' => 'ai'],

            // never
            ['name' => 'Assault',          'name_ar' => 'اعتداء',           'post_visibility' => 'never'],
            ['name' => 'Medical emergency','name_ar' => 'طوارئ طبية',       'post_visibility' => 'never'],
        ];

        foreach ($news_types as $data) {
            $type = NewsType::create([
                'type_name'    => $data['name'],
                'post_visibility' => $data['post_visibility'],
            ]);

            NewsTypeTranslation::create([
                'news_type_id' => $type->id,
                'language_code'  => 'en',
                'translation'       => $data['name'],
            ]);

            NewsTypeTranslation::create([
                'news_type_id' => $type->id,
                'language_code'  => 'ar',
                'translation'       => $data['name_ar'],
            ]);

        }



        $this->command->info('News types seeded successfully!');
        $this->command->table(
            ['Model', 'Count'],
            [
                ['News type translations',      NewsTypeTranslation::count()],
                ['News types',      NewsType::count()],
            ]
        );
    }
}
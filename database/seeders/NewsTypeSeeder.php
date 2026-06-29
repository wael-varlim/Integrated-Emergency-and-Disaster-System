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
            ['name' => 'Fire',                      'name_ar' => 'حريق'],
            ['name' => 'Explosion',                 'name_ar' => 'انفجار'],
            ['name' => 'Flood',                     'name_ar' => 'فيضان'],
            ['name' => 'Earthquake',                'name_ar' => 'زلزال'],
            ['name' => 'Traffic accident',          'name_ar' => 'حادث مرور'],
            ['name' => 'Building collapse',         'name_ar' => 'انهيار مبنى'],
            ['name' => 'Medical emergency',         'name_ar' => 'طوارئ طبية'],
            ['name' => 'Injury',                    'name_ar' => 'إصابة جسدية'],
            ['name' => 'Theft',                     'name_ar' => 'سرقة'],
            ['name' => 'Armed robbery',             'name_ar' => 'سطو مسلح'],
            ['name' => 'Assault',                   'name_ar' => 'اعتداء'],
            ['name' => 'Murder',                    'name_ar' => 'قتل'],
            ['name' => 'Kidnapping',                'name_ar' => 'خطف'],
            ['name' => 'Gang violence',             'name_ar' => 'عنف عصابات'],
            ['name' => 'Missing person',            'name_ar' => 'شخص مفقود'],
        ];

        foreach ($news_types as $data) {
            $type = NewsType::create([
                'type_name'    => $data['name'],
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
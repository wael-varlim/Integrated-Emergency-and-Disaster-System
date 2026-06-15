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
            ['name' => 'fire'                   ,'name_ar' => 'حريق'],              
            ['name' => 'flood'                  ,'name_ar' => 'فيضان'],              
            ['name' => 'theft'                  ,'name_ar' => 'سرقة'],              
            ['name' => 'murder'                 ,'name_ar' => 'قتل'],              
            ['name' => 'injury'                 ,'name_ar' => 'اصابة جسدية'],
            ['name' => 'traffic accident'       ,'name_ar' => 'حادث مرور'],              
            ['name' => 'earthquake'             ,'name_ar' => 'زلزال'],              
            ['name' => 'building collapsing'    ,'name_ar' => 'انهيار مبنى'],
            ['name' => 'drowning'              ,'name_ar' => 'غرق'],
            ['name' => 'kidnapping'              ,'name_ar' => 'خطف'],
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
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
            ['name' => 'Fire'                   ,'name_ar' => 'حريق'],              
            ['name' => 'Flood'                  ,'name_ar' => 'فيضان'],              
            ['name' => 'Theft'                  ,'name_ar' => 'سرقة'],              
            ['name' => 'Murder'                 ,'name_ar' => 'قتل'],              
            ['name' => 'Injury'                 ,'name_ar' => 'اصابة جسدية'],
            ['name' => 'Traffic accident'       ,'name_ar' => 'حادث مرور'],              
            ['name' => 'Earthquake'             ,'name_ar' => 'زلزال'],              
            ['name' => 'Building collapsing'    ,'name_ar' => 'انهيار مبنى'],
            ['name' => 'Drowning'              ,'name_ar' => 'غرق'],
            ['name' => 'Kidnapping'              ,'name_ar' => 'خطف'],
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
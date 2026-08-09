<?php

namespace Database\Seeders;

use App\Models\Authority;
use App\Models\AuthorityTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthoritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authorities = [
            // Hospitals (type 4)
            ['name' => 'Al Mouwasat Hospital',              'name_ar' => 'مشفى المواساة',                      'authority_type_id' => 4,     'lng' => '36.263171',   'lat' => '33.512743'],
            ['name' => 'The national University Hospital',  'name_ar' => 'مشفى الوطني الجامعي',                 'authority_type_id' => 4,     'lng' => '',   'lat' => ''],
            ['name' => 'Damascus Hospital (Al Mujtahid)',   'name_ar' => 'مشفى دمشق (المجتهد)',                'authority_type_id' => 4,     'lng' => '',   'lat' => ''],
            ['name' => 'Red Crescent Hospital',             'name_ar' => 'مشفى الهلال الأحمر',                 'authority_type_id' => 4,     'lng' => '',   'lat' => ''],
            ['name' => 'Ibn Al-Nafees Hospital (Rukn Eddin)','name_ar' => 'مشفى ابن النفيس (ركن الدين)',       'authority_type_id' => 4,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Zahrawi Hospital (Al Qassaa)',   'name_ar' => 'مشفى الزهراوي (القصاع)',             'authority_type_id' => 4,     'lng' => '',   'lat' => ''],
            ['name' => 'Surgical Eye Hospital',             'name_ar' => 'مشفى العيون الجراحي',                'authority_type_id' => 4,     'lng' => '',   'lat' => ''],
            ['name' => 'Italian Hospital',                  'name_ar' => 'المشفى الإيطالي',                    'authority_type_id' => 4,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Muhayni Modern Hospital',        'name_ar' => 'مشفى المهايني الحديث',               'authority_type_id' => 4,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Fayha Hospital',                 'name_ar' => 'مشفى الفيحاء',                       'authority_type_id' => 4,     'lng' => '',   'lat' => ''],
            ['name' => 'Saint Louis Hospital',              'name_ar' => 'مشفى القديس لويس',                   'authority_type_id' => 4,     'lng' => '',   'lat' => ''],
            ['name' => 'Dar Al Shifa Hospital',             'name_ar' => 'مشفى دار الشفاء',                    'authority_type_id' => 4,     'lng' => '',   'lat' => ''],
            ['name' => 'Umayyad Hospital',                  'name_ar' => 'مشفى أمية',                          'authority_type_id' => 4,     'lng' => '',   'lat' => ''],
            ['name' => 'Modern Medicine Hospital (Hisham Sinan)', 'name_ar' => 'مشفى الطب الحديث (هشام سنان)','authority_type_id' => 4,     'lng' => '',   'lat' => ''],

            // Fire Departments (type 1)
            ['name' => 'Regiment Command Center and Operations Room', 'name_ar' => 'مركز قيادة الفوج وغرفة العمليات', 'authority_type_id' => 1,     'lng' => '',   'lat' => ''],
            ['name' => 'Old Damascus Fire Station',         'name_ar' => 'مركز إطفاء دمشق القديمة',            'authority_type_id' => 1,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Midan Fire Station',             'name_ar' => 'مركز إطفاء الميدان',                 'authority_type_id' => 1,     'lng' => '',   'lat' => ''],
            ['name' => 'Eastern Neighborhoods Coverage Centers', 'name_ar' => 'مراكز تغطية الأحياء الشرقية',  'authority_type_id' => 1,     'lng' => '',   'lat' => ''],

            // Police (type 2)
            ['name' => 'Damascus Governorate Police Command','name_ar' => 'قيادة شرطة محافظة دمشق',            'authority_type_id' => 2,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Marjeh Police Station',          'name_ar' => 'مخفر شرطة المرجة',                   'authority_type_id' => 2,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Midan Police Station',           'name_ar' => 'مخفر الميدان',                       'authority_type_id' => 2,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Salihiyeh Police Department',    'name_ar' => 'قسم شرطة الصالحية',                  'authority_type_id' => 2,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Mazzeh Police Department',       'name_ar' => 'قسم شرطة المزة',                     'authority_type_id' => 2,     'lng' => '',   'lat' => ''],
            ['name' => 'Political Security Branch',         'name_ar' => 'فرع الأمن السياسي',                  'authority_type_id' => 2,     'lng' => '',   'lat' => ''],
            ['name' => 'Political Investigations Branch',   'name_ar' => 'فرع التحقيقات السياسية',             'authority_type_id' => 2,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Maysat Branch',                  'name_ar' => 'فرع الميسات',                        'authority_type_id' => 2,     'lng' => '',   'lat' => ''],

            // Traffic Police (type 6)
            ['name' => 'Al Marjeh Square',                  'name_ar' => 'ساحة المرجة',                        'authority_type_id' => 6,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Thawra Street',                  'name_ar' => 'شارع الثورة',                        'authority_type_id' => 6,     'lng' => '',   'lat' => ''],
            ['name' => 'Victoria Bridge',                   'name_ar' => 'جسر فكتوريا',                        'authority_type_id' => 6,     'lng' => '',   'lat' => ''],
            ['name' => 'Bab Musalla Roundabout',            'name_ar' => 'دوار باب مصلى',                      'authority_type_id' => 6,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Mujtahid Area',                  'name_ar' => 'منطقة المجتهد',                      'authority_type_id' => 6,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Baramkeh Area',                  'name_ar' => 'منطقة البرامكة',                     'authority_type_id' => 6,     'lng' => '',   'lat' => ''],

            // General Security (type 8)
            ['name' => 'Ministry of Interior Headquarters', 'name_ar' => 'مقر وزارة الداخلية',                 'authority_type_id' => 8,     'lng' => '',   'lat' => ''],
            ['name' => 'General Security Administration',   'name_ar' => 'مقر إدارة الأمن العام',              'authority_type_id' => 8,     'lng' => '',   'lat' => ''],
            ['name' => 'Political Investigations Branch (GS)', 'name_ar' => 'فرع التحقيقات السياسية',          'authority_type_id' => 8,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Maysat Branch (GS)',              'name_ar' => 'فرع الميسات',                        'authority_type_id' => 8,     'lng' => '',   'lat' => ''],

            // Civil Defense (type 3)
            ['name' => 'Jobar Center',                      'name_ar' => 'مركز جوبر',                          'authority_type_id' => 3,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Qaboun and Barzeh Center',       'name_ar' => 'مركز القابون وبرزة',                 'authority_type_id' => 3,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Midan Center',                   'name_ar' => 'مركز الميدان',                       'authority_type_id' => 3,     'lng' => '',   'lat' => ''],
            ['name' => 'Old Damascus Center',               'name_ar' => 'مركز دمشق القديمة',                  'authority_type_id' => 3,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Mazzeh Center',                  'name_ar' => 'مركز المزة',                         'authority_type_id' => 3,     'lng' => '',   'lat' => ''],

            // Municipalities (type 5)
            ['name' => 'Old Damascus',                      'name_ar' => 'دمشق القديمة',                       'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Jawra',                          'name_ar' => 'الجورة',                             'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Imara (Al Jawaniyeh)',            'name_ar' => 'العمارة (الجوانية)',                  'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Bab Touma',                         'name_ar' => 'باب توما',                           'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Qaymariyeh',                     'name_ar' => 'القيمرية',                           'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Hamidiyeh',                      'name_ar' => 'الحميدية',                           'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Hariqah',                        'name_ar' => 'الحريقة',                            'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Amin',                           'name_ar' => 'الأمين',                             'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Mazanat Al Shahm',                  'name_ar' => 'مأذنة الشحم',                        'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Shaghour Jawani',                   'name_ar' => 'شاغور جواني',                        'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Souq Sarouja',                      'name_ar' => 'سوق ساروجة',                        'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Aqeibeh',                        'name_ar' => 'العقيبة',                            'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Imara (Al Baraniyeh)',            'name_ar' => 'العمارة (البرانية)',                  'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Masjid Al Aqsab',                   'name_ar' => 'مسجد الأقصاب',                      'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Qassaa',                         'name_ar' => 'القصاع',                             'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
            ['name' => 'Al Adawi',                          'name_ar' => 'العدوي',                             'authority_type_id' => 5,     'lng' => '',   'lat' => ''],
        ];


        foreach ($authorities as $authority) {
            $lng = $authority['lng'];
            $lat = $authority['lat'];
            $new_authority = Authority::create([
                'name'              => $authority['name'],
                'location'          => DB::raw("ST_GeomFromText('POINT({$lng} {$lat})', 4326)"),
                'authority_type_id' => $authority['authority_type_id'],
            ]);

            AuthorityTranslation::create([
                'authority_id' => $new_authority->id,
                'language_code'    => 'en',
                'translation'         => $authority['name'],
            ]);

            AuthorityTranslation::create([
                'authority_id' => $new_authority->id,
                'language_code'    => 'ar',
                'translation'         => $authority['name_ar'],
            ]);
        }


        $this->command->info('Authorities seeded successfully!');
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Authority Translations',      AuthorityTranslation::count()],
                ['Authorities',      Authority::count()],
            ]
        );

    }
}

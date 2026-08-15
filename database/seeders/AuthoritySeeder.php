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
            ['name' => 'The national University Hospital',  'name_ar' => 'مشفى الوطني الجامعي',                 'authority_type_id' => 4,     'lng' => '36.269882',   'lat' => '33.505716'],
            ['name' => 'Damascus Hospital (Al Mujtahid)',   'name_ar' => 'مشفى دمشق (المجتهد)',                'authority_type_id' => 4,     'lng' => '36.295980',   'lat' => '33.500409'],
            ['name' => 'Red Crescent Hospital',             'name_ar' => 'مشفى الهلال الأحمر',                 'authority_type_id' => 4,     'lng' => '36.310214',   'lat' => '33.520391'],
            ['name' => 'Ibn Al-Nafees Hospital (Rukn Eddin)','name_ar' => 'مشفى ابن النفيس (ركن الدين)',       'authority_type_id' => 4,     'lng' => '36.306596',   'lat' => '33.547185'],
            ['name' => 'Al Zahrawi Hospital (Al Qassaa)',   'name_ar' => 'مشفى الزهراوي (القصاع)',             'authority_type_id' => 4,     'lng' => '36.316874',   'lat' => '33.520865'],
            ['name' => 'Italian Hospital',                  'name_ar' => 'المشفى الإيطالي',                    'authority_type_id' => 4,     'lng' => '36.28941',   'lat' => '33.52295'],
            ['name' => 'Al Muhayni Modern Hospital',        'name_ar' => 'مشفى المهايني الحديث',               'authority_type_id' => 4,     'lng' => '36.294700',   'lat' => '33.488706'],
            ['name' => 'Al Fayha Hospital',                 'name_ar' => 'مشفى الفيحاء',                       'authority_type_id' => 4,     'lng' => '36.300260',   'lat' => '33.490182'],
            ['name' => 'The French Hospital',              'name_ar' => 'المشفى الفرنسي',                   'authority_type_id' => 4,     'lng' => '36.316158',   'lat' => '33.518618'],
            ['name' => 'Dar Al Shifa Hospital',             'name_ar' => 'مشفى دار الشفاء',                    'authority_type_id' => 4,     'lng' => '36.305775',   'lat' => '33.525288'],
            ['name' => 'Umayyad Hospital',                  'name_ar' => 'مشفى أمية',                          'authority_type_id' => 4,     'lng' => '36.292756',   'lat' => '33.529319'],
            ['name' => 'Modern Medicine Hospital (Hisham Sinan)', 'name_ar' => 'مشفى الطب الحديث (هشام سنان)','authority_type_id' => 4,     'lng' => '36.298407',   'lat' => '33.532997'],

            // Fire Departments (type 1)
            [
                'name'              => 'Regiment Command Center and Operations Room',
                'name_ar'           => 'مركز قيادة الفوج وغرفة العمليات',
                'authority_type_id' => 1,
                'lng'               => '36.2917',
                'lat'               => '33.5079'
            ],
            [
                'name'              => 'Old Damascus Fire Station',
                'name_ar'           => 'مركز إطفاء دمشق القديمة',
                'authority_type_id' => 1,
                'lng'               => '36.3101',
                'lat'               => '33.5105'
            ],
            [
                'name'              => 'Al Midan Fire Station',
                'name_ar'           => 'مركز إطفاء الميدان',
                'authority_type_id' => 1,
                'lng'               => '36.2997',
                'lat'               => '33.4833'
            ],
            [
                'name'              => 'Eastern Neighborhoods Coverage Centers',
                'name_ar'           => 'مراكز تغطية الأحياء الشرقية',
                'authority_type_id' => 1,
                'lng'               => '36.3236',
                'lat'               => '33.5354'
            ],

            // Police (type 2)
            [
                'name'              => 'Damascus Governorate Police Command',
                'name_ar'           => 'قيادة شرطة محافظة دمشق',
                'authority_type_id' => 2,
                'lng'               => '36.2994',
                'lat'               => '33.5122'
            ],
            [
                'name'              => 'Al Marjeh Police Station',
                'name_ar'           => 'مخفر شرطة المرجة',
                'authority_type_id' => 2,
                'lng'               => '36.2982',
                'lat'               => '33.5136'
            ],
            [
                'name'              => 'Al Midan Police Station',
                'name_ar'           => 'مخفر الميدان',
                'authority_type_id' => 2,
                'lng'               => '36.2997',
                'lat'               => '33.4833'
            ],
            [
                'name'              => 'Al Salihiyeh Police Department',
                'name_ar'           => 'قسم شرطة الصالحية',
                'authority_type_id' => 2,
                'lng'               => '36.2913',
                'lat'               => '33.5235'
            ],
            [
                'name'              => 'Al Mazzeh Police Department',
                'name_ar'           => 'قسم شرطة المزة',
                'authority_type_id' => 2,
                'lng'               => '36.2492',
                'lat'               => '33.5049'
            ],
            [
                'name'              => 'Political Security Branch',
                'name_ar'           => 'فرع الأمن السياسي',
                'authority_type_id' => 2,
                'lng'               => '36.2922',
                'lat'               => '33.5242'
            ],
            [
                'name'              => 'Political Investigations Branch',
                'name_ar'           => 'فرع التحقيقات السياسية',
                'authority_type_id' => 2,
                'lng'               => '36.2925',
                'lat'               => '33.5244'
            ],
            [
                'name'              => 'Al Maysat Branch',
                'name_ar'           => 'فرع الميسات',
                'authority_type_id' => 2,
                'lng'               => '36.2922',
                'lat'               => '33.5242'
            ],
            // Traffic Police (type 6)
            [
                'name'              => 'Al Marjeh Square',
                'name_ar'           => 'ساحة المرجة',
                'authority_type_id' => 6,
                'lng'               => '36.2981',
                'lat'               => '33.5132'
            ],
            [
                'name'              => 'Al Thawra Street',
                'name_ar'           => 'شارع الثورة',
                'authority_type_id' => 6,
                'lng'               => '36.3012',
                'lat'               => '33.5165'
            ],
            [
                'name'              => 'Victoria Bridge',
                'name_ar'           => 'جسر فكتوريا',
                'authority_type_id' => 6,
                'lng'               => '36.2941',
                'lat'               => '33.5135'
            ],
            [
                'name'              => 'Bab Musalla Roundabout',
                'name_ar'           => 'دوار باب مصلى',
                'authority_type_id' => 6,
                'lng'               => '36.2974',
                'lat'               => '33.4988'
            ],
            [
                'name'              => 'Al Mujtahid Area',
                'name_ar'           => 'منطقة المجتهد',
                'authority_type_id' => 6,
                'lng'               => '36.2954',
                'lat'               => '33.5009'
            ],
            [
                'name'              => 'Al Baramkeh Area',
                'name_ar'           => 'منطقة البرامكة',
                'authority_type_id' => 6,
                'lng'               => '36.2872',
                'lat'               => '33.5098'
            ],

            // General Security (type 8)
            [
                'name'              => 'Al Marjeh Square',
                'name_ar'           => 'ساحة المرجة',
                'authority_type_id' => 6,
                'lng'               => '36.2981',
                'lat'               => '33.5132'
            ],
            [
                'name'              => 'Al Thawra Street',
                'name_ar'           => 'شارع الثورة',
                'authority_type_id' => 6,
                'lng'               => '36.3012',
                'lat'               => '33.5165'
            ],
            [
                'name'              => 'Victoria Bridge',
                'name_ar'           => 'جسر فكتوريا',
                'authority_type_id' => 6,
                'lng'               => '36.2941',
                'lat'               => '33.5135'
            ],
            [
                'name'              => 'Bab Musalla Roundabout',
                'name_ar'           => 'دوار باب مصلى',
                'authority_type_id' => 6,
                'lng'               => '36.2974',
                'lat'               => '33.4988'
            ],
            [
                'name'              => 'Al Mujtahid Area',
                'name_ar'           => 'منطقة المجتهد',
                'authority_type_id' => 6,
                'lng'               => '36.2954',
                'lat'               => '33.5009'
            ],
            [
                'name'              => 'Al Baramkeh Area',
                'name_ar'           => 'منطقة البرامكة',
                'authority_type_id' => 6,
                'lng'               => '36.2872',
                'lat'               => '33.5098'
            ],

            // Civil Defense (type 3)
            [
                'name'              => 'Jobar Center',
                'name_ar'           => 'مركز جوبر',
                'authority_type_id' => 3,
                'lng'               => '36.3315',
                'lat'               => '33.5283'
            ],
            [
                'name'              => 'Al Qaboun and Barzeh Center',
                'name_ar'           => 'مركز القابون وبرزة',
                'authority_type_id' => 3,
                'lng'               => '36.3225',
                'lat'               => '33.5458'
            ],
            [
                'name'              => 'Al Midan Center',
                'name_ar'           => 'مركز الميدان',
                'authority_type_id' => 3,
                'lng'               => '36.2997',
                'lat'               => '33.4833'
            ],
            [
                'name'              => 'Old Damascus Center',
                'name_ar'           => 'مركز دمشق القديمة',
                'authority_type_id' => 3,
                'lng'               => '36.3101',
                'lat'               => '33.5105'
            ],
            [
                'name'              => 'Al Mazzeh Center',
                'name_ar'           => 'مركز المزة',
                'authority_type_id' => 3,
                'lng'               => '36.2520',
                'lat'               => '33.5012'
            ],

            // Municipalities (type 5)
            [
                'name'              => 'Old Damascus',
                'name_ar'           => 'دمشق القديمة',
                'authority_type_id' => 5,
                'lng'               => '36.3080',
                'lat'               => '33.5115'
            ],
            [
                'name'              => 'Al Jawra',
                'name_ar'           => 'الجورة',
                'authority_type_id' => 5,
                'lng'               => '36.3130',
                'lat'               => '33.5110'
            ],
            [
                'name'              => 'Al Imara (Al Jawaniyeh)',
                'name_ar'           => 'العمارة (الجوانية)',
                'authority_type_id' => 5,
                'lng'               => '36.3075',
                'lat'               => '33.5135'
            ],
            [
                'name'              => 'Bab Touma',
                'name_ar'           => 'باب توما',
                'authority_type_id' => 5,
                'lng'               => '36.3155',
                'lat'               => '33.5138'
            ],
            [
                'name'              => 'Al Qaymariyeh',
                'name_ar'           => 'القيمرية',
                'authority_type_id' => 5,
                'lng'               => '36.3095',
                'lat'               => '33.5118'
            ],
            [
                'name'              => 'Al Hamidiyeh',
                'name_ar'           => 'الحميدية',
                'authority_type_id' => 5,
                'lng'               => '36.3015',
                'lat'               => '33.5108'
            ],
            [
                'name'              => 'Al Hariqah',
                'name_ar'           => 'الحريقة',
                'authority_type_id' => 5,
                'lng'               => '36.3033',
                'lat'               => '33.5092'
            ],
            [
                'name'              => 'Al Amin',
                'name_ar'           => 'الأمين',
                'authority_type_id' => 5,
                'lng'               => '36.3110',
                'lat'               => '33.5085'
            ],
            [
                'name'              => 'Mazanat Al Shahm',
                'name_ar'           => 'مأذنة الشحم',
                'authority_type_id' => 5,
                'lng'               => '36.3082',
                'lat'               => '33.5078'
            ],
            [
                'name'              => 'Shaghour Jawani',
                'name_ar'           => 'شاغور جواني',
                'authority_type_id' => 5,
                'lng'               => '36.3060',
                'lat'               => '33.5065'
            ],
            [
                'name'              => 'Souq Sarouja',
                'name_ar'           => 'سوق ساروجة',
                'authority_type_id' => 5,
                'lng'               => '36.2995',
                'lat'               => '33.5168'
            ],
            [
                'name'              => 'Al Aqeibeh',
                'name_ar'           => 'العقيبة',
                'authority_type_id' => 5,
                'lng'               => '36.3045',
                'lat'               => '33.5152'
            ],
            [
                'name'              => 'Al Imara (Al Baraniyeh)',
                'name_ar'           => 'العمارة (البرانية)',
                'authority_type_id' => 5,
                'lng'               => '36.3070',
                'lat'               => '33.5158'
            ],
            [
                'name'              => 'Masjid Al Aqsab',
                'name_ar'           => 'مسجد الأقصاب',
                'authority_type_id' => 5,
                'lng'               => '36.3065',
                'lat'               => '33.5175'
            ],
            [
                'name'              => 'Al Qassaa',
                'name_ar'           => 'القصاع',
                'authority_type_id' => 5,
                'lng'               => '36.3185',
                'lat'               => '33.5215'
            ],
            [
                'name'              => 'Al Adawi',
                'name_ar'           => 'العدوي',
                'authority_type_id' => 5,
                'lng'               => '36.3120',
                'lat'               => '33.5290'
            ],
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

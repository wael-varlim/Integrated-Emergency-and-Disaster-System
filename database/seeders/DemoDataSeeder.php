<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\AddressTranslation;
use App\Models\Authority;
use App\Models\AuthorityTranslation;
use App\Models\AuthorityType;
use App\Models\AwarenessArticle;
use App\Models\City;
use App\Models\KnownUser;
use App\Models\MediaType;
use App\Models\News;
use App\Models\NewsTranslation;
use App\Models\NewsType;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\Region;
use App\Models\Report;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (KnownUser::where('email', 'user0@demo.com')->exists()) {
            $this->command->warn('Demo data already seeded, skipping.');
            return;
        }

        $cities = City::where('governorate_id', 1);
        $newsTypes = NewsType::all();
        $authorities = Authority::all();

        if (!$cities || $newsTypes->isEmpty()) {
            $this->command->error('Run LocationSeeder and NewsTypeSeeder first.');
            return;
        }

        $this->command->info('Seeding demo data...');

        // ─────────── 1. Create 10 mobile users ───────────
        $users = [];
        $fakerNames = [
            ['Ahmad', 'Hassan'], ['Layla', 'Khalid'], ['Omar', 'Saeed'],
            ['Noor', 'Ali'], ['Huda', 'Youssef'], ['Rami', 'Salem'],
            ['Sara', 'Nassar'], ['Tariq', 'Adeeb'], ['Dima', 'Shawkat'],
            ['Bassam', 'Khoury'],
        ];

        $cityIds = $cities->pluck('id')->toArray();
        $regionIds = Region::pluck('id')->toArray();

        foreach ($fakerNames as $i => [$first, $last]) {
            $user = User::create(['user_type' => 'Known user']);
            $user->assignRole('mobile_user');
            $user->region()->attach($regionIds[array_rand($regionIds)]);

            $knownUser = $user->knownUser()->create([
                'first_name'                 => $first,
                'last_name'                  => $last,
                'email'                      => "user{$i}@demo.com",
                'password'                   => Hash::make('password'),
                'official_identifier_method' => 'national_id',
                'official_identifier'        => str_pad((string) (10000000000 + $i), 11, '0', STR_PAD_LEFT),
                'is_verified'                => true,
            ]);

            $users[] = $knownUser;
        }

        $this->command->info('✓ 10 mobile users created');

        // ─────────── 2. Create 20 addresses across Damascus cities ───────────
        $addresses = [];

        $streetNames = [
            'Straight Street'               => 'الشارع المستقيم',
            'Baghdad Street'                => 'شارع بغداد',
            'Beirut Street'                 => 'شارع بيروت',
            'Mezza Highway'                 => 'طريق المزة السريع',
            'Airport Road'                  => 'طريق المطار',
            'Al-Mutanabbi Street'           => 'شارع المتنبي',
            'Al-Nasr Street'                => 'شارع النصر',
            'Al-Mazzah Street'              => 'شارع المزة',
            'Thawra Street'                 => 'شارع الثورة',
            'Abu Rummaneh Street'           => 'شارع أبو رمانة',
            'Al-Jalaa Street'               => 'شارع الجلاء',
            'Al-Rabwa Street'               => 'شارع الربوة',
            'Al-Baramkeh Street'            => 'شارع البرامكة',
            'Al-Malki Street'               => 'شارع المالكي',
            'Al-Shafi\'i Street'            => 'شارع الشافعي',
            'Al-Hamra Street'               => 'شارع الحمراء',
            'Al-Khaled Ibn Al-Walid Street' => 'شارع خالد بن الوليد',
            'Al-Watani Street'              => 'شارع الوطني',
            'Al-Qudsi Street'               => 'شارع القدسي',
            'Al-Kouatli Street'             => 'شارع القوتلي',
            'Al-Yarmouk Street'             => 'شارع اليرموك',
            'Al-Qadam Street'               => 'شارع القدم',
            'Al-Abbasiyeen Street'          => 'شارع العباسيين',
            'Al-Hijaz Street'               => 'شارع الحجاز',
            'Palestine Street'              => 'شارع فلسطين',
            'Al-Salhiyeh Street'            => 'شارع الصالحية',
            'Al-Shaghour Street'            => 'شارع الشاغور',
            'Al-Qanawat Street'             => 'شارع القنوات',
            'Al-Midan Street'               => 'شارع الميدان',
            'Damascus-Beirut Highway'       => 'طريق دمشق-بيروت السريع',
            'Al-Mazraa Street'              => 'شارع المزرعة',
            'Bab Touma Street'              => 'شارع باب توما',
            'Al-Muhajireen Street'          => 'شارع المهاجرين',
            'Ibn Asaker Street'             => 'شارع ابن عساكر',
            'Al-Zeitoun Street'             => 'شارع الزيتون',
            'Al-Qimarieh Street'            => 'شارع القيمرية',
            'Rukn al-Din Street'            => 'شارع ركن الدين',
            'Bab al-Jabiya Street'          => 'شارع باب الجابية',
            'Al-Qaboun Road'                => 'طريق القابون',
            'Al-Hurriyah Street'            => 'شارع الحرية',
            'Ibn Khaldoun Street'           => 'شارع ابن خلدون',
            'Al-Arnous Street'              => 'شارع الأرنوس',
            'Damascus-Homs Highway'         => 'طريق دمشق-حمص السريع',
            'Al-Nour Street'                => 'شارع النور',
            'Al-Joura Street'               => 'شارع الجورة',
            'Al-Ameen Street'               => 'شارع الأمين',
            'Al-Zablatani Street'           => 'شارع الزبلطاني',
            'Al-Kasaa Street'               => 'شارع القصاع',
            'Al-Baraka Street'              => 'شارع البركة',
            'Al-Kamaliyeh Street'           => 'شارع الكاملية',
            'Umayyad Square Road'           => 'طريق ساحة الأمويين',
            'Al-Qassa Street'               => 'شارع القصة',
        ];

        foreach ($streetNames as $en => $ar) {
            $cityId = $cityIds[array_rand($cityIds)];
            $address = Address::create([
                'street'  => $en,
                'city_id' => $cityId,
            ]);
            AddressTranslation::create([
                'address_id'    => $address->id,
                'language_code' => 'en',
                'translation'   => $en,
            ]);
            AddressTranslation::create([
                'address_id'    => $address->id,
                'language_code' => 'ar',
                'translation'   => $ar,
            ]);
            $addresses[] = $address;
        }

        $this->command->info('20 addresses created');

        // ─────────── 3. Create 1 extra authority type for variety ───────────
        if (Authority::count() < 3) {
            $extraTypes = ['Fire Department', 'Ambulance Service', 'Military Police'];
            foreach ($extraTypes as $i => $typeName) {
                $at = AuthorityType::create(['type_name' => $typeName]);
                AuthorityTranslation::create([
                    'authority_type_id' => $at->id,
                    'language_code'     => 'en',
                    'translation'       => $typeName,
                ]);
                AuthorityTranslation::create([
                    'authority_type_id' => $at->id,
                    'language_code'     => 'ar',
                    'translation'       => $typeName,
                ]);
                Authority::create(['authority_type_id' => $at->id]);
            }
            $authorities = Authority::all();
        }

        $this->command->info('Authorities created');

        // ─────────── 4. Create 18 news items ───────────
        // CHANGED: each entry now has 'en' and 'ar' keys
        $newsBodies = [
            ['en' => 'Severe thunderstorm warning issued for Damascus region.',         'ar' => 'صدر تحذير من عاصفة رعدية شديدة في منطقة دمشق.'],
            ['en' => 'Road closure on Mezza Highway due to flooding.',                  'ar' => 'إغلاق طريق المزة السريع بسبب الفيضانات.'],
            ['en' => 'Emergency response drill scheduled for next week.',                'ar' => 'تدريب على الاستجابة للطوارئ مقرر الأسبوع القادم.'],
            ['en' => 'Power outage reported in Al-Mazzah district.',                    'ar' => 'تم الإبلاغ عن انقطاع في التيار الكهربائي في حي المزة.'],
            ['en' => 'Water supply interruption in Rukn al-Din area.',                  'ar' => 'انقطاع مياه في منطقة ركن الدين.'],
            ['en' => 'Medical emergency response team deployed to Barza.',               'ar' => 'تم نشر فريق الاستجابة الطبية للطوارئ في برزة.'],
            ['en' => 'Fire contained in Al-Qaboun industrial area.',                    'ar' => 'تم إخماد حريق في المنطقة الصناعية بالقابون.'],
            ['en' => 'Traffic accident on Airport Road causing delays.',                 'ar' => 'حادث سير على طريق المطار يتسبب في تأخيرات.'],
            ['en' => 'Flash flood warning for Damascus countryside.',                    'ar' => 'تحذير من فيضانات مفاجئة في ريف دمشق.'],
            ['en' => 'Cold weather advisory: temperatures to drop below freezing.',      'ar' => 'تحذير من طقس بارد: درجات الحرارة ستنخفض دون الصفر.'],
            ['en' => 'Public transport strike affecting major routes.',                  'ar' => 'إضراب في وسائل النقل العام يؤثر على المسارات الرئيسية.'],
            ['en' => 'Gas leak contained in Al-Midan district.',                        'ar' => 'تم احتواء تسرب غاز في حي الميدان.'],
            ['en' => 'Search and rescue operation underway in Al-Yarmouk.',              'ar' => 'عملية بحث وإنقاذ جارية في اليرموك.'],
            ['en' => 'Health advisory: air quality warning due to dust storm.',          'ar' => 'تنبيه صحي: تحذير من جودة الهواء بسبب عاصفة ترابية.'],
            ['en' => 'Community cleanup campaign in Kafr Sousa.',                       'ar' => 'حملة تنظيف مجتمعي في كفر سوسة.'],
            ['en' => 'Bridge inspection causing delays on Baghdad Street.',              'ar' => 'فحص الجسر يتسبب في تأخيرات على شارع بغداد.'],
            ['en' => 'Emergency shelter activated for displaced families.',              'ar' => 'تم تفعيل ملجأ طارئ للعائلات النازحة.'],
            ['en' => 'Civil defense conducts safety awareness workshop.',                'ar' => 'الدفاع المدني ينظم ورشة توعية حول السلامة.'],
            ['en' => 'Flooding reported in Bab Touma old city area.',                   'ar' => 'الإبلاغ عن فيضان في منطقة باب توما في المدينة القديمة.'],
            ['en' => 'Wildfire reported near Qasioun Mountain slopes.',                 'ar' => 'الإبلاغ عن حريق في الغابات بالقرب من منحدرات جبل قاسيون.'],
            ['en' => 'Chemical spill on industrial road in Adra.',                      'ar' => 'تسرب كيميائي على الطريق الصناعي في عدرا.'],
            ['en' => 'Earthquake tremor felt across Damascus suburbs.',                  'ar' => 'شعور بهزة أرضية في ضواحي دمشق.'],
            ['en' => 'School evacuated due to gas smell in Al-Midan.',                  'ar' => 'إخلاء مدرسة بسبب رائحة غاز في الميدان.'],
            ['en' => 'Partial building collapse reported in old city district.',         'ar' => 'الإبلاغ عن انهيار جزئي لمبنى في حي المدينة القديمة.'],
            ['en' => 'Road cave-in near Umayyad Square causing disruption.',            'ar' => 'انهسار في الطريق قرب ساحة الأمويين يتسبب في اضطرابات.'],
            ['en' => 'Burst water pipe floods Al-Hamra Street.',                        'ar' => 'كسر في أنبوب مياه يُغرق شارع الحمراء.'],
            ['en' => 'Power transformer explosion in Kafr Sousa neighborhood.',         'ar' => 'انفجار محول كهربائي في حي كفر سوسة.'],
            ['en' => 'Toxic smoke from factory fire reported in Qudsaya.',              'ar' => 'دخان سام من حريق مصنع في قدسيا.'],
            ['en' => 'Landslide blocking mountain road to Zabadani.',                   'ar' => 'انهيار أرضي يسد طريق الجبل إلى الزبداني.'],
            ['en' => 'Heavy snow warning issued for Damascus highlands.',               'ar' => 'تحذير من ثلوج كثيفة في مرتفعات دمشق.'],
            ['en' => 'Pedestrian bridge closed for emergency inspection in Al-Mezze.',  'ar' => 'إغلاق جسر للمشاة لإجراء فحص طارئ في المزة.'],
            ['en' => 'Storm drains overflowing in Bab Sharqi area.',                   'ar' => 'فيضان مصارف الأمطار في منطقة باب شرقي.'],
            ['en' => 'Wildfire approaching residential area in Dummar.',                'ar' => 'حريق في الغابات يقترب من منطقة سكنية في دمر.'],
            ['en' => 'Fuel shortage alert at Damascus filling stations.',               'ar' => 'تنبيه بنقص الوقود في محطات التعبئة بدمشق.'],
            ['en' => 'Mass casualty drill held at Al-Mouwasat Hospital.',               'ar' => 'إجراء تدريب على الكوارث الجماعية في مستشفى المواساة.'],
            ['en' => 'Sinkhole opens on Thawra Street near city center.',               'ar' => 'انفتاح حفرة هبوط في شارع الثورة قرب مركز المدينة.'],
            ['en' => 'Electrical fire breaks out in Rukn al-Din market.',               'ar' => 'اندلاع حريق كهربائي في سوق ركن الدين.'],
            ['en' => 'Ambulance response delayed due to road blockage in Jobar.',       'ar' => 'تأخر سيارة الإسعاف بسبب انسداد الطريق في جوبر.'],
            ['en' => 'Flash flood sweeps vehicles in Barada River valley.',             'ar' => 'فيضان مفاجئ يجرف مركبات في وادي نهر بردى.'],
            ['en' => 'Crane collapse at construction site in Al-Salhiyeh.',             'ar' => 'سقوط رافعة في موقع بناء في الصالحية.'],
            ['en' => 'Smoke detected in Damascus International Airport terminal.',      'ar' => 'اكتشاف دخان في صالة مطار دمشق الدولي.'],
            ['en' => 'Hail storm damages vehicles across city parking areas.',          'ar' => 'عاصفة برد تتلف المركبات في مواقف السيارات في أنحاء المدينة.'],
            ['en' => 'Sewage overflow reported in Al-Qaboun neighborhood.',             'ar' => 'الإبلاغ عن فيضان مجاري في حي القابون.'],
            ['en' => 'Train derailment reported near Al-Qadam station.',                'ar' => 'الإبلاغ عن خروج قطار عن مساره قرب محطة القدم.'],
            ['en' => 'Stampede reported at crowded market in Bab al-Jabiya.',           'ar' => 'الإبلاغ عن تدافع في سوق مكتظ في باب الجابية.'],
            ['en' => 'Water contamination warning issued for Jobar area.',              'ar' => 'صدر تحذير من تلوث المياه في منطقة جوبر.'],
            ['en' => 'Emergency road repairs on Beirut Street causing closures.',       'ar' => 'أعمال إصلاح طارئة في شارع بيروت تتسبب في إغلاقات.'],
            ['en' => 'Dust storm reduces visibility to near zero across Damascus.',     'ar' => 'عاصفة ترابية تقلص مدى الرؤية إلى شبه صفر في دمشق.'],
            ['en' => 'Fire alarm triggered at Damascus University main campus.',        'ar' => 'تفعيل إنذار حريق في الحرم الرئيسي لجامعة دمشق.'],
            ['en' => 'Collapsed retaining wall blocks road in Al-Muhajireen.',          'ar' => 'جدار استناد منهار يسد الطريق في المهاجرين.'],
        ];

        $newsRecords = [];
        foreach ($newsBodies as $i => $body) {
            // CHANGED: $body['en'] instead of $body
            $news = News::create([
                'body'          => $body['en'],
                'address_id'    => $addresses[$i % count($addresses)]->id,
                'known_user_id' => $users[$i % count($users)]->id,
            ]);

            // ADDED: news translations
            NewsTranslation::create([
                'news_id'       => $news->id,
                'language_code' => 'en',
                'translation'   => $body['en'],
            ]);
            NewsTranslation::create([
                'news_id'       => $news->id,
                'language_code' => 'ar',
                'translation'   => $body['ar'],
            ]);

            $news->media()->create([
                'media_url'     => "images/image{$i}.jpg",
                'media_type_id' => rand(1, 3),
            ]);
            $type = $newsTypes[$i % $newsTypes->count()];
            $news->newsType()->attach($type->id);

            if (isset($authorities[$i % $authorities->count()])) {
                $news->authority()->attach($authorities[$i % $authorities->count()]->id);
            }

            $newsRecords[] = $news;
        }

        $this->command->info('18 news items created');

        // ─────────── 5. Create posts with notifications ───────────
        $posts = [];

        foreach ($newsRecords as $i => $news) {
            // CHANGED: $newsBodies[$i]['en'] instead of $newsBodies[$i]
            $post = Post::create([
                'title'    => "Post #{$i}: " . mb_substr($newsBodies[$i]['en'], 0, 10),
                'by_admin' => rand(0, 1),
                'news_id'  => $news->id,
            ]);

            // ADDED: post translations
            PostTranslation::create([
                'post_id'       => $post->id,
                'language_code' => 'en',
                'translation'   => "Post #{$i}: " . mb_substr($newsBodies[$i]['en'], 0, 10),
            ]);
            PostTranslation::create([
                'post_id'       => $post->id,
                'language_code' => 'ar',
                'translation'   => "المنشور #{$i}: " . mb_substr($newsBodies[$i]['ar'], 0, 10),
            ]);

            $posts[] = $post;
        }

        // Notifications for first 10 posts
        foreach (array_slice($posts, 0, 10) as $i => $post) {
            Notification::create([
                'title'     => 'Notification #' . ($i + 1),
                // CHANGED: $newsBodies[$i]['en'] instead of $newsBodies[$i]
                'body'      => 'Alert: ' . substr($newsBodies[$i]['en'], 0, 10),
                'post_id'   => $post->id,
                'region_id' => $regionIds[array_rand($regionIds)],
            ]);
        }

        $this->command->info('Posts + notifications created');

        // ─────────── 6. Create 20 reports spread across months ───────────
        $damascusCityIds = City::whereHas('governorate', function ($q) {
            $q->where('name', 'Damascus');
        })->pluck('id')->toArray();

        $baseLat = 33.5;
        $baseLng = 36.3;

        for ($i = 0; $i < 20; $i++) {
            $news = $newsRecords[$i % count($newsRecords)];
            $monthOffset = $i % 12;
            $createdAt = now()->subMonths($monthOffset)->subDays(rand(0, 27));

            $lng = $baseLng + mt_rand(-100, 100) / 1000;
            $lat = $baseLat + mt_rand(-100, 100) / 1000;

            Report::create([
                'location'   => DB::raw("ST_GeomFromText('POINT({$lng} {$lat})', 4326)"),
                'news_id'    => $news->id,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('20 reports created across different months');

        // ─────────── 7. Create 6 awareness articles ───────────
        $articleData = [
            ['Earthquake Preparedness Guide', 'Learn how to prepare for earthquakes and stay safe during seismic events. This guide covers emergency kits, evacuation plans, and safety procedures.'],
            ['First Aid Basics', 'Essential first aid techniques for emergency situations including wound care, CPR, and treating burns.'],
            ['Flood Safety Tips', 'Important safety measures to take before, during, and after flooding. Includes information on evacuation routes and emergency supplies.'],
            ['Fire Prevention and Safety', 'Comprehensive guide on preventing fires at home and work, plus what to do if a fire breaks out.'],
            ['Heatwave Survival Guide', 'How to stay safe during extreme heat conditions. Covers hydration, cooling centers, and heat-related illness recognition.'],
            ['Winter Storm Preparedness', 'Prepare for winter storms with tips on heating, insulation, emergency supplies, and travel safety.'],
        ];

        foreach ($articleData as $i => [$title, $body]) {
            AwarenessArticle::create([
                'title'        => $title,
                'body'         => $body,
                'icon_url'     => 'awareness-icons/default.svg',
                'news_type_id' => $newsTypes[$i % $newsTypes->count()]->id,
            ]);
        }

        $this->command->info('6 awareness articles created');

        // ─────────── 8. Create 8 suggestions ───────────
        $suggestionTexts = [
            'Please add more emergency shelters in Al-Mazzah area.',
            'The app needs a dark mode option.',
            'Can we receive push notifications for weather alerts?',
            'Consider adding a panic button feature.',
            'Improve the map interface for better navigation.',
            'Add support for uploading photos in reports.',
            'The response time for emergencies needs improvement.',
            'Great app! Very useful for the community.',
        ];

        foreach ($suggestionTexts as $i => $text) {
            Suggestion::create([
                'content'          => $text,
                'is_read_by_admin' => $i >= 4,
            ]);
        }

        $this->command->info('8 suggestions created');

        $this->command->newLine();
        $this->command->info('Demo data seeded successfully!');
    }
}
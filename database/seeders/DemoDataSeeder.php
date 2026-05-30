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
use App\Models\NewsType;
use App\Models\Notification;
use App\Models\Post;
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

        $cities = City::all();
        $newsTypes = NewsType::all();
        $authorities = Authority::all();

        if ($cities->isEmpty() || $newsTypes->isEmpty()) {
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
        $streetNames = ['Straight Street', 'Baghdad Street', 'Beirut Street', 'Mezza Highway',
            'Airport Road', 'Al-Mutanabbi Street', 'Al-Nasr Street', 'Al-Mazzah Street',
            'Thawra Street', 'Abu Rummaneh Street', 'Al-Jalaa Street', 'Al-Rabwa Street',
            'Al-Baramkeh Street', 'Al-Malki Street', 'Al-Shafi\'i Street', 'Al-Hamra Street',
            'Al-Khaled Ibn Al-Walid Street', 'Al-Watani Street', 'Al-Qudsi Street', 'Al-Kouatli Street',
        ];

        foreach ($streetNames as $i => $street) {
            $cityId = $cityIds[array_rand($cityIds)];
            $address = Address::create([
                'street'  => $street,
                'city_id' => $cityId,
            ]);
            AddressTranslation::create([
                'address_id'    => $address->id,
                'languahe_code' => 'en',
                'translation'   => $street,
            ]);
            AddressTranslation::create([
                'address_id'    => $address->id,
                'languahe_code' => 'ar',
                'translation'   => 'شارع ' . $street,
            ]);
            $addresses[] = $address;
        }

        $this->command->info('✓ 20 addresses created');

        // ─────────── 3. Create 1 extra authority type for variety ───────────
        if (Authority::count() < 3) {
            $extraTypes = ['Fire Department', 'Ambulance Service', 'Military Police'];
            foreach ($extraTypes as $i => $typeName) {
                $at = AuthorityType::create(['type_name' => $typeName]);
                AuthorityTranslation::create([
                    'authority_type_id' => $at->id,
                    'languahe_code'     => 'en',
                    'translation'       => $typeName,
                ]);
                AuthorityTranslation::create([
                    'authority_type_id' => $at->id,
                    'languahe_code'     => 'ar',
                    'translation'       => $typeName,
                ]);
                Authority::create(['authority_type_id' => $at->id]);
            }
            $authorities = Authority::all();
        }

        $this->command->info('✓ Authorities created');

        // ─────────── 4. Create 18 news items ───────────
        $newsBodies = [
            'Severe thunderstorm warning issued for Damascus region.',
            'Road closure on Mezza Highway due to flooding.',
            'Emergency response drill scheduled for next week.',
            'Power outage reported in Al-Mazzah district.',
            'Water supply interruption in Rukn al-Din area.',
            'Medical emergency response team deployed to Barza.',
            'Fire contained in Al-Qaboun industrial area.',
            'Traffic accident on Airport Road causing delays.',
            'Flash flood warning for Damascus countryside.',
            'Cold weather advisory: temperatures to drop below freezing.',
            'Public transport strike affecting major routes.',
            'Gas leak contained in Al-Midan district.',
            'Search and rescue operation underway in Al-Yarmouk.',
            'Health advisory: air quality warning due to dust storm.',
            'Community cleanup campaign in Kafr Sousa.',
            'Bridge inspection causing delays on Baghdad Street.',
            'Emergency shelter activated for displaced families.',
            'Civil defense conducts safety awareness workshop.',
        ];

        $newsRecords = [];
        foreach ($newsBodies as $i => $body) {
            $news = News::create([
                'body'          => $body,
                'address_id'    => $addresses[$i % count($addresses)]->id,
                'known_user_id' => $users[$i % count($users)]->id,
            ]);

            $type = $newsTypes[$i % $newsTypes->count()];
            $news->newsType()->attach($type->id);

            if (isset($authorities[$i % $authorities->count()])) {
                $news->authority()->attach($authorities[$i % $authorities->count()]->id);
            }

            $newsRecords[] = $news;
        }

        $this->command->info('✓ 18 news items created');

        // ─────────── 5. Create posts with notifications ───────────
        $ownerRoles = ['admin', 'news_manager', 'content_manager'];
        $posts = [];

        foreach ($newsRecords as $i => $news) {
            $post = Post::create([
                'title'      => "Post #{$i}: " . substr($newsBodies[$i], 0, 40),
                'owner_role' => $ownerRoles[$i % count($ownerRoles)],
                'news_id'    => $news->id,
            ]);
            $posts[] = $post;
        }

        // Notifications for first 10 posts
        foreach (array_slice($posts, 0, 10) as $i => $post) {
            Notification::create([
                'title'     => 'Notification #' . ($i + 1),
                'body'      => 'Alert: ' . substr($newsBodies[$i], 0, 50),
                'post_id'   => $post->id,
                'region_id' => $regionIds[array_rand($regionIds)],
            ]);
        }

        $this->command->info('✓ Posts + notifications created');

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

        $this->command->info('✓ 20 reports created across different months');

        // ─────────── 7. Create 6 awareness articles ───────────
        $articleData = [
            ['Earthquake Preparedness Guide', 'Learn how to prepare for earthquakes and stay safe during seismic events. This guide covers emergency kits, evacuation plans, and safety procedures.',],
            ['First Aid Basics', 'Essential first aid techniques for emergency situations including wound care, CPR, and treating burns.',
            ],
            ['Flood Safety Tips', 'Important safety measures to take before, during, and after flooding. Includes information on evacuation routes and emergency supplies.',
            ],
            ['Fire Prevention and Safety', 'Comprehensive guide on preventing fires at home and work, plus what to do if a fire breaks out.',
            ],
            ['Heatwave Survival Guide', 'How to stay safe during extreme heat conditions. Covers hydration, cooling centers, and heat-related illness recognition.',
            ],
            ['Winter Storm Preparedness', 'Prepare for winter storms with tips on heating, insulation, emergency supplies, and travel safety.',
            ],
        ];

        foreach ($articleData as $i => [$title, $body]) {
            AwarenessArticle::create([
                'title'       => $title,
                'body'        => $body,
                'icon_url'    => 'awareness-icons/default.svg',
                'news_type_id' => $newsTypes[$i % $newsTypes->count()]->id,
            ]);
        }

        $this->command->info('✓ 6 awareness articles created');

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

        $this->command->info('✓ 8 suggestions created');

        $this->command->newLine();
        $this->command->info('✅ Demo data seeded successfully!');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\AddressTranslation;
use App\Models\Authority;
use App\Models\City;
use App\Models\KnownUser;
use App\Models\News;
use App\Models\NewsType;
use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
               // ── Prerequisites ──────────────────────────────────────────
        $cityIds        = City::pluck('id')->toArray();
        $knownUserIds   = KnownUser::pluck('id')->toArray();
        $newsTypeIds    = NewsType::pluck('id')->toArray();
        $authorityIds   = Authority::pluck('id')->toArray();

        // ── Addresses ──────────────────────────────────────────────
        $addressData = [
            ['street' => 'Street One',   'city_id' => $cityIds[0] ?? 1],
            ['street' => 'Street Two',   'city_id' => $cityIds[1] ?? 1],
            ['street' => 'Street Three', 'city_id' => $cityIds[2] ?? 1],
        ];

        $addressRecords = [];
        foreach ($addressData as $data) {
            $address = Address::create($data);

            // English translation
            AddressTranslation::create([
                'address_id'    => $address->id,
                'languahe_code' => 'en',
                'translation'   => $data['street'],
            ]);

            // Arabic translation
            AddressTranslation::create([
                'address_id'    => $address->id,
                'languahe_code' => 'ar',
                'translation'   => 'اسم العنوان',
            ]);

            $addressRecords[] = $address;
        }

        // ── News ───────────────────────────────────────────────────
        $newsRecords = [];
        for ($i = 1; $i <= 3; $i++) {
            $news = News::create([
                'body'       => "This is the body of news number {$i}.",
                'address_id' => $addressRecords[$i - 1]->id,
                'known_user_id' => $knownUserIds[($i - 1) % count($knownUserIds)],
            ]);

            // pivot: news_news_types
            if (isset($newsTypeIds[$i - 1])) {
                $news->newsType()->attach($newsTypeIds[$i - 1]);
            }

            // pivot: news_auth (authorities)
            if (isset($authorityIds[$i - 1])) {
                $news->authority()->attach($authorityIds[$i - 1]);
            }

            $newsRecords[] = $news;
        }

        // ── Posts ──────────────────────────────────────────────────
        $ownerRoles = ['admin', 'user'];

        for ($i = 1; $i <= 3; $i++) {
            Post::create([
                'title'      => "Post Title {$i}", // change
                'owner_role' => $ownerRoles[$i % 2], // alternates between admin and user
                'news_id'    => $newsRecords[$i - 1]->id,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\AddressTranslation;
use App\Models\Authority;
use App\Models\City;
use App\Models\KnownUser;
use App\Models\Media;
use App\Models\News;
use App\Models\News as ModelsNews;
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

        // ── Media ──────────────────────────────────────────────────
        $mediaTypeIds = [1, 2, 3]; // 1=image, 2=video, 3=audio — change if different

        $mediaRecords = [];
        for ($i = 1; $i <= 3; $i++) {
            $mediaRecords[] = Media::create([
                'url'           => "https://example.com/media/file{$i}.jpg", // change URLs
                'media_type_id' => $mediaTypeIds[$i - 1],
            ]);
        }

        // ── Addresses ──────────────────────────────────────────────
        $addressData = [
            ['street' => 'Street One',   'city_id' => $cityIds[0]],
            ['street' => 'Street Two',   'city_id' => $cityIds[1]],
            ['street' => 'Street Three', 'city_id' => $cityIds[2]],
        ];

        $addressRecords = [];
        foreach ($addressData as $data) {
            $address = Address::create($data);

            // English translation
            AddressTranslation::create([
                'address_id' => $address->id,
                'lang_code'  => 'en',
                'name'       => $data['street'],
            ]);

            // Arabic translation
            AddressTranslation::create([
                'address_id' => $address->id,
                'lang_code'  => 'ar',
                'name'       => 'اسم العنوان', // change to real arabic name
            ]);

            $addressRecords[] = $address;
        }

        // ── News ───────────────────────────────────────────────────
        $newsRecords = [];
        for ($i = 1; $i <= 3; $i++) {
            $news = News::create([
                'body'       => "This is the body of news number {$i}.", // change
                'media_id'   => $mediaRecords[$i - 1]->id,
                'address_id' => $addressRecords[$i - 1]->id,
                'known_user_id' => $knownUserIds[$i - 1],
            ]);

            // pivot: news_news_types
            $news->newsTypes()->attach($newsTypeIds[$i - 1]);

            // pivot: news_auth (authorities)
            $news->authorities()->attach($authorityIds[$i - 1]);

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

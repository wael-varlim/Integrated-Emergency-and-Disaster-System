<?php

namespace Database\Seeders;

use App\Models\NewsType;
use Illuminate\Database\Seeder;

class NewsTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Breaking News',
            'Weather Alert',
            'Traffic',
            'Safety Advisory',
            'Community Announcement',
            'Emergency Notice',
        ];

        foreach ($types as $name) {
            NewsType::create(['type_name' => $name]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\MediaType;
use Illuminate\Database\Seeder;

class MediaTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['image', 'video', 'audio'];

        foreach ($types as $name) {
            MediaType::firstOrCreate(['type_name' => $name]);
        }
    }
}

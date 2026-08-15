<?php

namespace Database\Seeders;

use App\Models\AuthorityType;
use App\Models\NewsType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authority_types = [
            ['name' => 'Fire Department'],
            ['name' => 'Police'],
            ['name' => 'Civil Defense'],
            ['name' => 'Hopital'],
            ['name' => 'Municipality'],
            ['name' => 'Traffic Police'],
            ['name' => 'Emergency Services'],
            ['name' => 'General Security'],
        ];

        foreach ($authority_types as $authority_type) {
            AuthorityType::create([
                'type_name' => $authority_type['name'],
            ]);
        }

        $news_type_authorities = [
            'Fire'               => ['Fire Department'],
            'Explosion'          => ['Fire Department', 'General Security'],
            'Flood'              => ['Emergency Services'],
            'Earthquake'         => ['Emergency Services'],
            'Building collapse'  => ['Civil Defense', 'Hopital'],
            'Armed robbery'      => ['Police'],
            'Murder'             => ['General Security'],
            'Kidnapping'         => ['Civil Defense'],
            'Gang violence'      => ['Police'],
            'Missing person'     => ['Civil Defense'],
            'Injury'             => ['Hopital'],
            'Traffic accident'   => ['Traffic Police'],
            'Theft'              => ['Police'],
            'Assault'            => ['Police'],
            'Medical emergency'  => ['Hopital'],
        ];

        foreach ($news_type_authorities as $newsTypeName => $authorityNames) {
            $newsType = NewsType::where('type_name', $newsTypeName)->first();

            if (!$newsType) {
                $this->command->warn("News type '{$newsTypeName}' not found — skipping.");
                continue;
            }

            $authorityIds = AuthorityType::whereIn('type_name', $authorityNames)->pluck('id');

            $newsType->authorityType()->attach($authorityIds);
        }

        $this->command->info('Authority types seeded and linked to news types successfully!');
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Authority types', AuthorityType::count()],
            ]
        );
    }
}

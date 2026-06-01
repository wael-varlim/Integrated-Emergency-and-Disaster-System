<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            LocationSeeder::class,
            PermissionSeeder::class,
            AdminSeeder::class,
            AuthorityTypeSeeder::class,
            AuthoritySeeder::class,
            MobileUserSeeder::class,
            MediaTypeSeeder::class,
            NewsTypeSeeder::class,
            //PostSeeder::class,
            DemoDataSeeder::class,
        ]);

    }
}

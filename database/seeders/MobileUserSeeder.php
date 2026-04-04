<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KnownUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MobileUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate([
            'user_type' => 'Known user',
        ]);

        KnownUser::firstOrCreate(
            ['email' => 'user@test.com'],
            [
                'user_id'         => $user->id,
                'national_number' => '98765432101',
                'first_name'      => 'Test',
                'last_name'       => 'User',
                'email'           => 'user@test.com',
                'password'        => Hash::make('password'),
            ]
        );

        $user->assignRole('mobile_user');

        $this->command->info('Test MobileUser created: user@test.com / password');
    }
}

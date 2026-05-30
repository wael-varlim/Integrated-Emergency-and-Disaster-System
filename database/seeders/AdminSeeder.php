<?php
// database/seeders/AdminSeeder.php

namespace Database\Seeders;

use App\Models\KnownUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        //  FIX: Use 'web' guard (Spatie uses the model's default guard)
        Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $user = User::firstOrCreate([
            'user_type' => 'Admin',
        ]);

        KnownUser::updateOrCreate([
            'official_identifier' => '12345678901',
        ], [
            'user_id'         => $user->id,
            'official_identifier_method' => 'passport',
            'first_name'      => 'Super',
            'last_name'       => 'Admin',
            'email'           => 'admin@admin.com',
            'password'        => Hash::make('password'),
            'is_verified'     => true
        ]);

        $user->assignRole('admin');

        $this->command->info(' Admin created: admin@admin.com / password');
    }
}
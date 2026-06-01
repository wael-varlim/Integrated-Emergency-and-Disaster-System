<?php

namespace Database\Seeders;

use App\Models\AuthorityType;
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
            $auth = AuthorityType::create([
                'type_name' => $authority_type['name'],
            ]);
        }




        $this->command->info('Authority types seeded successfully!');
        $this->command->table(
            ['Model', 'Count'],
            [
                ['Authority types',      AuthorityType::count()],
            ]
        );
    }
}

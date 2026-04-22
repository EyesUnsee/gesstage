<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Candidat Test',
                'email' => 'candidat@gesstage.com',
                'password' => Hash::make('password'),
                'role' => 'candidat',
            ],
            [
                'name' => 'Tuteur Test',
                'email' => 'tuteur@gesstage.com',
                'password' => Hash::make('password'),
                'role' => 'tuteur',
            ],
            [
                'name' => 'Responsable Test',
                'email' => 'responsable@gesstage.com',
                'password' => Hash::make('password'),
                'role' => 'responsable',
            ],
            [
                'name' => 'Chef Service Test',
                'email' => 'chef@gesstage.com',
                'password' => Hash::make('password'),
                'role' => 'chef-service',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Compte Manager

        User::create([
            'name' => 'Manager Admin',
            'email' => 'manager@demo.com',
            'password' => bcrypt('password@@'),
            'role' => 'MANAGER'
        ]);

        //Compte Employé 1

        User::create([
            'name' => 'Employee Bob',
            'email' => 'bob@demo.com',
            'password' => bcrypt('password@@'),
            'role' => 'EMPLOYEE'
        ]);

        //Compte Employé 2
        User::create([
            'name' => 'Employé Alice',
            'email' => 'alice@demo.com',
            'password' => bcrypt('password@@'),
            'role' => 'EMPLOYEE'
        ]);
    }
}

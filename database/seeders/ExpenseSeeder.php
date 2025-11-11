<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // On récupère les 2 employés
        $alice = User::where('email', 'alice@demo.com')->first();
        $bob = User::where('email', 'bob@demo.com')->first();

        // On crée 5 dépenses pour Alice
        Expense::factory()->count(5)->create([
            'user_id' => $alice->id,
        ]);

        // On crée 5 dépenses pour Bob
        Expense::factory()->count(5)->create([
            'user_id' => $bob->id,
        ]);
    }
}

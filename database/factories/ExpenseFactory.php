<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'amount' => $this->faker->randomFloat(2, 5, 500), //Montant entre 5 et 500
            'spent_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'category' =>$this->faker->randomElement(['MEAL', 'TRAVEL', 'HOTEL', 'OTHER']),
            'status' => $this->faker->randomElement(['DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED']),
        ];
    }
}

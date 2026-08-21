<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = \App\Models\Customer::class;

    public function definition(): array
    {
        return [
            'Company_Name'  => fake()->unique()->company(),
            'Company_Email' => fake()->unique()->companyEmail(),
            'Company_No'    => fake()->numerify('#########'),
            'Status'        => 'Active',
        ];
    }
}

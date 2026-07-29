<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Leads>
 */
class LeadsFactory extends Factory
{
    protected $model = \App\Models\Leads::class;

    public function definition(): array
    {
        return [
            'Lead_Name'       => fake()->catchPhrase(),
            'Source'          => fake()->randomElement(['Referral', 'Website', 'Cold Call', 'Event']),
            'Status'          => 'New',
            'Estimated_Value' => fake()->randomFloat(2, 500, 50000),
            'Company_ID'      => Customer::factory(),
        ];
    }
}

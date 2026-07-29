<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = \App\Models\Activity::class;

    public function definition(): array
    {
        return [
            'Activity_Type'   => fake()->randomElement(['Call', 'Email', 'Meeting', 'Follow-Up', 'Other']),
            'Subject'         => fake()->sentence(4),
            'Activity_Detail' => fake()->paragraph(),
            'Status'          => 'Pending',
            'Dead_Line'       => fake()->dateTimeBetween('now', '+2 weeks'),
            'Contact_ID'      => Contact::factory(),
        ];
    }
}

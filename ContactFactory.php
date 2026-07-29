<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    protected $model = \App\Models\Contact::class;

    public function definition(): array
    {
        return [
            'Contact_Name'  => fake()->name(),
            'Contact_Email' => fake()->unique()->safeEmail(),
            'Contact_No'    => fake()->phoneNumber(),
            'Contact_Role'  => fake()->jobTitle(),
            'Contact_Note'  => null,
            'Country_Code'  => '+60',
            'Company_ID'    => Customer::factory(),
        ];
    }
}

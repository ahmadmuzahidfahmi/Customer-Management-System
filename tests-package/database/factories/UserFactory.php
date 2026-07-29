<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'User_Name'     => fake()->unique()->userName(),
            'User_Email'    => fake()->unique()->safeEmail(),
            'User_Password' => static::$password ??= Hash::make('password'),
            'User_Role'     => 'Admin',
            'Status'        => 'Active',
            'Last_Login'    => null,
        ];
    }
}

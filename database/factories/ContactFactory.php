<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'surname' => fake()->lastName(),
            'phone' => fake()->phoneNumber(),
            'secondary_phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'label_id' => fake()->randomDigitNotZero(),
            'image' => Str::lower(fake()->firstName . '.png'),
            'group_id' => 1
        ];
    }
}

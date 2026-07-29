<?php

namespace Database\Factories;

use App\Models\Pitch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pitch>
 */
class PitchFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->city().' 5v5';

        return [
            'owner_id' => User::factory(),
            'slug' => \Illuminate\Support\Str::slug($name).'-'.fake()->unique()->numberBetween(1, 100000),
            'name' => ['fr' => $name, 'en' => $name],
            'description' => ['fr' => fake()->sentence(), 'en' => fake()->sentence()],
            'country' => 'CI',
            'city' => fake()->city(),
            'address' => fake()->streetAddress(),
            'surface_type' => 'synthetic_turf',
            'capacity' => 5,
            'amenities' => ['lighting', 'parking'],
            'price_per_hour' => fake()->numberBetween(5000, 20000),
            'currency' => 'XOF',
            'is_active' => true,
        ];
    }
}

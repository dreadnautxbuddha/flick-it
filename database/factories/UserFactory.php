<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'flickr_id' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'nickname' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'flickr_token' => $this->faker->text(34),
            'flickr_refresh_token' => $this->faker->text(16),
        ];
    }
}

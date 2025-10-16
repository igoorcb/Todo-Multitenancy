<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();
        return [
            'name' => $name,
            'slug' => fake()->unique()->slug() . '-' . strtolower(substr(md5($name), 0, 6)),
        ];
    }
}

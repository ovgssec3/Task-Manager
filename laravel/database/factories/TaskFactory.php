<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'task_name' => ucfirst($this->faker->words(3, true)),
            'description' => $this->faker->boolean(70) ? $this->faker->sentence() : null,
            'status' => $this->faker->randomElement(['Pending', 'Completed']),
            'due_date' => $this->faker->boolean(80) ? $this->faker->dateTimeBetween('-5 days', '+14 days') : null,
        ];
    }
}
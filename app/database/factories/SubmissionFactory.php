<?php

namespace Database\Factories;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => 0,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'message' => Str::random(10),
        ];
    }
}

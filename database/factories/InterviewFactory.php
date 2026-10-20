<?php

namespace Database\Factories;

use App\Enums\InterviewMode;
use App\Models\Interview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interview>
 */
class InterviewFactory extends Factory
{
    public function definition(): array
    {
        $mode = fake()->randomElement(InterviewMode::cases());

        return [
            'scheduled_at' => fake()->dateTimeBetween('+1 day', '+14 days'),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90]),
            'mode' => $mode,
            'meeting_url' => $mode === InterviewMode::Online ? 'https://meet.google.com/'.fake()->slug(3) : null,
            'location' => $mode === InterviewMode::Onsite ? fake()->address() : null,
            'instructions' => fake('id_ID')->sentence(10),
        ];
    }
}

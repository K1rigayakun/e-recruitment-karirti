<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cover_letter' => fake()->boolean(70) ? fake('id_ID')->paragraph(2) : null,
            'status' => ApplicationStatus::Applied,
        ];
    }
}

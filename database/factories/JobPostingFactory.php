<?php

namespace Database\Factories;

use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Enums\WorkMode;
use App\Models\JobPosting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JobPosting>
 */
class JobPostingFactory extends Factory
{
    private static array $titles = [
        'Junior Laravel Developer', 'Frontend Developer (React)', 'Backend Developer PHP',
        'Mobile Developer Flutter', 'UI/UX Designer', 'IT Support Technician',
        'Junior Android Developer', 'Junior Data Analyst', 'Network Engineer',
        'DevOps Engineer Junior', 'Programmer PHP Junior', 'Web Developer Full Stack',
        'Junior Software Engineer', 'IT Helpdesk Specialist',
    ];

    public function definition(): array
    {
        $title = fake()->randomElement(self::$titles);
        $slug = Str::slug($title).'-'.Str::random(6);

        return [
            'title' => $title,
            'slug' => $slug,
            'summary' => fake('id_ID')->sentence(12),
            'description' => fake('id_ID')->paragraphs(3, true),
            'requirements' => fake('id_ID')->paragraphs(2, true),
            'location' => fake()->randomElement(['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Remote']),
            'job_type' => fake()->randomElement(JobType::cases()),
            'work_mode' => fake()->randomElement(WorkMode::cases()),
            'salary_min' => fake()->boolean(60) ? fake()->randomElement([3000000, 4000000, 5000000]) : null,
            'salary_max' => null,
            'show_salary' => false,
            'deadline_at' => fake()->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
            'status' => JobStatus::Draft,
        ];
    }

    public function open(): static
    {
        return $this->state(fn () => [
            'status' => JobStatus::Open,
            'published_at' => now()->subDays(rand(1, 14)),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => JobStatus::Closed,
            'closed_at' => now()->subDays(rand(1, 7)),
        ]);
    }
}

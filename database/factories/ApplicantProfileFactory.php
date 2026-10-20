<?php

namespace Database\Factories;

use App\Models\ApplicantProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ApplicantProfile>
 */
class ApplicantProfileFactory extends Factory
{
    private static array $cities = ['Medan', 'Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang', 'Makassar', 'Palembang'];

    private static array $headlines = [
        'Fresh Graduate D3 TI siap kerja',
        'Junior Web Developer | Laravel & Vue',
        'Backend Developer | PHP & MySQL',
        'Frontend Developer | React & Tailwind',
        'IT Support & Network Enthusiast',
        'Full Stack Developer in Progress',
        'Mobile Developer | Flutter & Android',
        'UI/UX Designer & Frontend Developer',
    ];

    public function definition(): array
    {
        $name = fake('id_ID')->name();
        $slug = Str::slug($name).'-'.Str::random(5);

        return [
            'slug' => $slug,
            'nim' => fake()->numerify('##########'),
            'study_program' => 'D3 Teknik Informatika',
            'graduation_year' => fake()->numberBetween(2020, 2025),
            'phone' => '08'.fake()->numerify('#########'),
            'city' => fake()->randomElement(self::$cities),
            'headline' => fake()->randomElement(self::$headlines),
            'about' => fake('id_ID')->paragraph(3),
            'github_url' => 'https://github.com/'.Str::slug($name).fake()->numerify('##'),
            'linkedin_url' => null,
            'is_public' => fake()->boolean(40),
        ];
    }
}

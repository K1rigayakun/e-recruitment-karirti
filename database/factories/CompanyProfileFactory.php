<?php

namespace Database\Factories;

use App\Enums\CompanyVerificationStatus;
use App\Models\CompanyProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CompanyProfile>
 */
class CompanyProfileFactory extends Factory
{
    private static array $companies = [
        'PT Solusi Digital Nusantara', 'CV Inovasi Teknologi Indonesia', 'PT Kreasi Kode Mandiri',
        'PT Data Cerdas Indonesia', 'CV Mitra Sistem Informasi', 'PT Teknologi Maju Bersama',
        'PT Infrastruktur Cloud Nasional', 'CV Studio Aplikasi Modern', 'PT Konsultan IT Terpadu',
        'PT Platform Digital Kreatif',
    ];

    private static array $industries = ['Teknologi Informasi', 'E-Commerce', 'Fintech', 'Edukasi', 'Kesehatan Digital', 'Logistik'];

    private static array $sizes = ['1-10', '11-50', '51-200', '201-500'];

    public function definition(): array
    {
        $name = fake()->randomElement(self::$companies).' '.fake()->numerify('##');
        $slug = Str::slug($name).'-'.Str::random(4);

        return [
            'slug' => $slug,
            'company_name' => $name,
            'industry' => fake()->randomElement(self::$industries),
            'size' => fake()->randomElement(self::$sizes),
            'description' => fake('id_ID')->paragraph(2),
            'website' => 'https://'.Str::slug($name).'.co.id',
            'city' => fake()->randomElement(['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Yogyakarta']),
            'verification_status' => CompanyVerificationStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'verification_status' => CompanyVerificationStatus::Approved,
            'verified_at' => now()->subDays(rand(5, 30)),
        ]);
    }
}

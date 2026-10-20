<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        $uuid = Str::uuid();

        return [
            'type' => 'cv',
            'original_name' => 'CV_'.fake('id_ID')->lastName().'.pdf',
            'path' => "cv/1/{$uuid}.pdf",
            'mime_type' => 'application/pdf',
            'size_bytes' => fake()->numberBetween(100000, 2000000),
            'sha256' => Str::random(64),
        ];
    }
}

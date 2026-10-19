<?php

namespace Database\Factories;

use App\Models\PortfolioProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PortfolioProject>
 */
class PortfolioProjectFactory extends Factory
{
    private static array $projects = [
        'Sistem Informasi Akademik', 'Aplikasi Manajemen Inventori', 'Platform E-Learning Sederhana',
        'REST API Toko Online', 'Dashboard Monitoring Server', 'Aplikasi Kasir Mobile',
        'Website Portofolio Personal', 'Sistem Absensi QR Code', 'Bot Telegram Notifikasi',
        'Aplikasi Todo dengan Auth',
    ];

    private static array $stacks = [
        ['Laravel', 'PHP', 'MySQL', 'Tailwind CSS'],
        ['React', 'Node.js', 'MongoDB', 'Express'],
        ['Vue.js', 'Laravel', 'MySQL'],
        ['Flutter', 'Dart', 'Firebase'],
        ['Python', 'Django', 'PostgreSQL'],
        ['PHP', 'JavaScript', 'Bootstrap', 'MySQL'],
        ['Laravel', 'Livewire', 'Alpine.js', 'MySQL'],
        ['Android', 'Kotlin', 'SQLite'],
    ];

    public function definition(): array
    {
        $title = fake()->randomElement(self::$projects).' '.fake()->numerify('v#.#');
        $techStack = fake()->randomElement(self::$stacks);

        return [
            'title' => $title,
            'description' => fake('id_ID')->paragraph(2),
            'github_url' => 'https://github.com/user/'.fake()->slug(3),
            'demo_url' => fake()->boolean(40) ? 'https://'.fake()->slug(2).'.vercel.app' : null,
            'tech_stack' => $techStack,
            'sort_order' => 0,
        ];
    }
}

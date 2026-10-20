<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'Laravel', 'PHP', 'JavaScript', 'TypeScript', 'React', 'Vue.js', 'Node.js', 'Express',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Git', 'GitHub', 'REST API', 'HTML & CSS', 'Tailwind CSS',
            'Bootstrap', 'UI/UX Design', 'Figma', 'Flutter', 'Android (Kotlin)', 'Java', 'Python',
            'C#', 'Docker', 'Linux', 'Jaringan Komputer', 'Mikrotik', 'Cisco', 'Keamanan Siber',
            'Cloud (AWS/GCP)', 'Analisis Data', 'Excel', 'QA Testing', 'Technical Support',
            'IT Helpdesk', 'DevOps', 'Scrum/Agile',
        ];

        foreach ($skills as $name) {
            Skill::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        $this->command->info(count($skills).' skill di-seed.');
    }
}

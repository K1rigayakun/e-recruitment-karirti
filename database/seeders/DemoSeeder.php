<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\CompanyVerificationStatus;
use App\Enums\InterviewMode;
use App\Enums\InterviewStatus;
use App\Enums\JobStatus;
use App\Models\ApplicantProfile;
use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Models\CompanyProfile;
use App\Models\Document;
use App\Models\Interview;
use App\Models\JobPosting;
use App\Models\PortfolioProject;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            $this->command->warn('DemoSeeder hanya berjalan di environment local.');

            return;
        }

        $this->command->info('Membuat data demo...');
        $skills = Skill::all()->keyBy('slug');

        // ── 4 Perusahaan (3 approved, 1 pending) ──────────────────────
        $companies = [];
        $companyData = [
            ['PT Solusi Digital Nusantara', 'approved', 'Jakarta'],
            ['CV Inovasi Teknologi Bandung', 'approved', 'Bandung'],
            ['PT Data Cerdas Surabaya', 'approved', 'Surabaya'],
            ['CV Startup Teknologi Baru', 'pending', 'Medan'],
        ];

        foreach ($companyData as [$name, $status, $city]) {
            $user = User::factory()->company()->create([
                'name' => 'HRD '.$name,
                'email' => 'hrd@'.Str::slug($name).'.test',
                'password' => 'Password123!',
            ]);
            $profile = CompanyProfile::create([
                'user_id' => $user->id,
                'slug' => Str::slug($name).'-'.Str::random(4),
                'company_name' => $name,
                'industry' => 'Teknologi Informasi',
                'size' => '11-50',
                'description' => fake('id_ID')->paragraph(2),
                'website' => 'https://'.Str::slug($name).'.co.id',
                'city' => $city,
            ]);
            $profile->verification_status = CompanyVerificationStatus::from($status);
            if ($status === 'approved') {
                $profile->verified_at = now()->subDays(rand(5, 30));
            }
            $profile->save();
            $companies[] = $profile;
        }

        // ── 14 Lowongan (8 open, 3 closed, 3 draft) ───────────────────
        $approvedCompanies = array_slice($companies, 0, 3);
        $jobTitles = [
            ['Junior Laravel Developer', JobStatus::Open],
            ['Frontend Developer React', JobStatus::Open],
            ['Backend Developer PHP', JobStatus::Open],
            ['Mobile Developer Flutter', JobStatus::Open],
            ['UI/UX Designer', JobStatus::Open],
            ['IT Support Technician', JobStatus::Open],
            ['Junior Android Developer', JobStatus::Open],
            ['Junior Data Analyst', JobStatus::Open],
            ['Network Engineer', JobStatus::Closed],
            ['DevOps Engineer Junior', JobStatus::Closed],
            ['Web Developer Full Stack', JobStatus::Closed],
            ['Senior Laravel Dev', JobStatus::Draft],
            ['Product Manager IT', JobStatus::Draft],
            ['QA Engineer', JobStatus::Draft],
        ];

        $jobPostings = [];
        foreach ($jobTitles as $i => [$title, $status]) {
            $company = $approvedCompanies[$i % 3];
            $slug = Str::slug($title).'-'.Str::random(6);
            $job = new JobPosting([
                'company_profile_id' => $company->id,
                'title' => $title,
                'slug' => $slug,
                'summary' => fake('id_ID')->sentence(10),
                'description' => fake('id_ID')->paragraphs(3, true),
                'requirements' => fake('id_ID')->paragraphs(2, true),
                'location' => fake()->randomElement(['Jakarta', 'Bandung', 'Remote']),
                'job_type' => fake()->randomElement(['internship', 'full_time', 'contract']),
                'work_mode' => fake()->randomElement(['onsite', 'hybrid', 'remote']),
                'salary_min' => 4000000,
                'salary_max' => 8000000,
                'show_salary' => true,
                'deadline_at' => now()->addMonths(2)->format('Y-m-d'),
            ]);
            $job->status = $status;
            if ($status === JobStatus::Open) {
                $job->published_at = now()->subDays(rand(1, 14));
            } elseif ($status === JobStatus::Closed) {
                $job->closed_at = now()->subDays(rand(1, 7));
            }
            $job->save();

            // Attach 2-4 skill
            $randomSkills = $skills->random(rand(2, 4))->pluck('id')->toArray();
            $job->skills()->attach($randomSkills);

            $jobPostings[] = $job;
        }

        // ── 18 Pelamar ─────────────────────────────────────────────────
        $applicants = [];
        $openJobs = array_filter($jobPostings, fn ($j) => $j->status === JobStatus::Open);
        $openJobs = array_values($openJobs);

        for ($i = 0; $i < 18; $i++) {
            $user = User::factory()->create([
                'email' => "pelamar{$i}@karirti.test",
                'password' => 'Password123!',
            ]);
            $name = $user->name;
            $profile = ApplicantProfile::create([
                'user_id' => $user->id,
                'slug' => Str::slug($name).'-'.Str::random(4),
                'nim' => ''.rand(2019001, 2023999),
                'graduation_year' => rand(2022, 2025),
                'phone' => '0812'.rand(10000000, 99999999),
                'city' => fake()->randomElement(['Jakarta', 'Bandung', 'Surabaya', 'Medan']),
                'headline' => fake()->randomElement(['Junior Web Developer', 'Fresh Graduate D3 TI', 'Backend Developer PHP']),
                'about' => fake('id_ID')->paragraph(2),
                'github_url' => 'https://github.com/'.Str::slug($name).rand(10, 99),
                'is_public' => $i < 8,
            ]);

            // CV dokumen fiktif
            $doc = Document::create([
                'user_id' => $user->id,
                'type' => 'cv',
                'original_name' => 'CV_'.str_replace(' ', '_', $name).'.pdf',
                'path' => 'cv/'.$user->id.'/'.Str::uuid().'.pdf',
                'mime_type' => 'application/pdf',
                'size_bytes' => rand(200000, 1500000),
                'sha256' => Str::random(64),
            ]);
            $profile->current_cv_document_id = $doc->id;
            $profile->save();

            // 1-4 proyek portofolio
            $numProjects = rand(1, 4);
            for ($p = 0; $p < $numProjects; $p++) {
                PortfolioProject::create([
                    'applicant_profile_id' => $profile->id,
                    'title' => fake()->randomElement(['Sistem Informasi', 'Aplikasi Web', 'REST API', 'Dashboard']).fake()->numerify(' v#.#'),
                    'description' => fake('id_ID')->paragraph(2),
                    'github_url' => 'https://github.com/'.Str::slug($name).'/'.fake()->slug(2),
                    'tech_stack' => fake()->randomElement([['Laravel', 'PHP', 'MySQL'], ['React', 'Node.js'], ['Flutter', 'Dart']]),
                    'sort_order' => $p,
                ]);
            }

            // Skill acak 2-5
            $randSkills = $skills->random(rand(2, 5))->pluck('id')->toArray();
            $profile->skills()->attach($randSkills);

            $applicants[] = ['user' => $user, 'profile' => $profile, 'doc' => $doc];
        }

        // ── ~30 Lamaran ─────────────────────────────────────────────────
        $statuses = [
            ApplicationStatus::Applied, ApplicationStatus::Applied,
            ApplicationStatus::Viewed, ApplicationStatus::Viewed,
            ApplicationStatus::Interview,
            ApplicationStatus::Accepted,
            ApplicationStatus::Rejected,
            ApplicationStatus::Withdrawn,
        ];

        $applicationCount = 0;
        $interviewCount = 0;

        foreach ($applicants as $idx => $applicant) {
            $numApplications = rand(1, 3);
            $usedJobs = [];

            for ($a = 0; $a < $numApplications && $applicationCount < 30; $a++) {
                // Pilih lowongan open yang belum dilamar pelamar ini
                $availableJobs = array_filter($openJobs, fn ($j) => ! in_array($j->id, $usedJobs));
                if (empty($availableJobs)) {
                    break;
                }
                $job = fake()->randomElement(array_values($availableJobs));
                $usedJobs[] = $job->id;

                $targetStatus = $statuses[$applicationCount % count($statuses)];

                $application = new Application([
                    'job_posting_id' => $job->id,
                    'applicant_profile_id' => $applicant['profile']->id,
                    'cv_document_id' => $applicant['doc']->id,
                    'cover_letter' => fake('id_ID')->paragraph(2),
                ]);
                $application->status = ApplicationStatus::Applied;
                $application->save();

                // Rekam history awal
                ApplicationStatusHistory::create([
                    'application_id' => $application->id,
                    'from_status' => null,
                    'to_status' => ApplicationStatus::Applied->value,
                    'created_at' => now()->subDays(rand(3, 14)),
                ]);

                // Transisi ke status target
                if ($targetStatus !== ApplicationStatus::Applied) {
                    $transitions = match ($targetStatus) {
                        ApplicationStatus::Viewed => [ApplicationStatus::Viewed],
                        ApplicationStatus::Interview => [ApplicationStatus::Viewed, ApplicationStatus::Interview],
                        ApplicationStatus::Accepted => [ApplicationStatus::Viewed, ApplicationStatus::Accepted],
                        ApplicationStatus::Rejected => [ApplicationStatus::Viewed, ApplicationStatus::Rejected],
                        ApplicationStatus::Withdrawn => [ApplicationStatus::Withdrawn],
                        default => [],
                    };

                    $prev = ApplicationStatus::Applied;
                    foreach ($transitions as $next) {
                        ApplicationStatusHistory::create([
                            'application_id' => $application->id,
                            'from_status' => $prev->value,
                            'to_status' => $next->value,
                            'created_at' => now()->subDays(rand(1, 3)),
                        ]);
                        $prev = $next;
                    }

                    $application->status = $targetStatus;
                    if (in_array($targetStatus, [ApplicationStatus::Accepted, ApplicationStatus::Rejected])) {
                        $application->decided_at = now()->subDays(rand(0, 2));
                    }
                    if ($targetStatus !== ApplicationStatus::Applied) {
                        $application->viewed_at = now()->subDays(rand(2, 5));
                    }
                    $application->save();

                    // Buat interview jika status interview dan belum 5
                    if ($targetStatus === ApplicationStatus::Interview && $interviewCount < 5) {
                        $companyUser = $job->companyProfile->user;
                        $interview = new Interview([
                            'application_id' => $application->id,
                            'scheduled_at' => now()->addDays(rand(3, 14)),
                            'duration_minutes' => 60,
                            'mode' => InterviewMode::Online->value,
                            'meeting_url' => 'https://meet.google.com/'.fake()->slug(3),
                            'instructions' => 'Hadir tepat waktu. Siapkan laptop.',
                            'created_by' => $companyUser->id,
                        ]);
                        $interview->status = InterviewStatus::Scheduled;
                        $interview->save();
                        $interviewCount++;
                    }
                }

                $applicationCount++;
            }
        }

        $this->command->info("Demo selesai: {$applicationCount} lamaran, {$interviewCount} interview.");
    }
}

<?php

namespace App\Services;

use App\Models\ApplicantProfile;

/**
 * Hitung kelengkapan profil pelamar dengan bobot sesuai plan.md Fase 6.
 * Dipakai di dashboard dan halaman profil.
 */
class ProfileCompleteness
{
    /** @return array{score: int, max: int, percent: int, items: array} */
    public function calculate(ApplicantProfile $profile): array
    {
        $items = [];
        $score = 0;

        // Nama + headline (15)
        $nameOk = filled($profile->user->name) && filled($profile->headline);
        $items[] = ['label' => 'Nama & headline', 'weight' => 15, 'done' => $nameOk, 'href' => route('applicant.profile.edit')];
        if ($nameOk) {
            $score += 15;
        }

        // About (15)
        $aboutOk = filled($profile->about);
        $items[] = ['label' => 'Tentang saya', 'weight' => 15, 'done' => $aboutOk, 'href' => route('applicant.profile.edit')];
        if ($aboutOk) {
            $score += 15;
        }

        // Tahun lulus + NIM (10)
        $eduOk = filled($profile->graduation_year) && filled($profile->nim);
        $items[] = ['label' => 'Tahun lulus & NIM', 'weight' => 10, 'done' => $eduOk, 'href' => route('applicant.profile.edit')];
        if ($eduOk) {
            $score += 10;
        }

        // Minimal 3 skill (15)
        $skillOk = $profile->skills->count() >= 3;
        $items[] = ['label' => 'Minimal 3 keahlian', 'weight' => 15, 'done' => $skillOk, 'href' => route('applicant.profile.edit')];
        if ($skillOk) {
            $score += 15;
        }

        // CV aktif (25)
        $cvOk = ! is_null($profile->current_cv_document_id);
        $items[] = ['label' => 'Upload CV PDF', 'weight' => 25, 'done' => $cvOk, 'href' => route('applicant.cv.index')];
        if ($cvOk) {
            $score += 25;
        }

        // Minimal 1 project (20)
        $projectOk = $profile->portfolioProjects->count() >= 1;
        $items[] = ['label' => 'Minimal 1 project portofolio', 'weight' => 20, 'done' => $projectOk, 'href' => route('applicant.projects.index')];
        if ($projectOk) {
            $score += 20;
        }

        return [
            'score' => $score,
            'max' => 100,
            'percent' => $score,
            'items' => $items,
        ];
    }
}

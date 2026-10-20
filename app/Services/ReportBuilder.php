<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\JobPosting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Query laporan teragregasi. Semua filter divalidasi di controller sebelum masuk ke sini.
 */
class ReportBuilder
{
    /**
     * Funnel status untuk admin (semua perusahaan) atau satu perusahaan.
     */
    public function statusFunnel(?int $companyProfileId = null): array
    {
        $query = Application::selectRaw('status, count(*) as total')->groupBy('status');

        if ($companyProfileId) {
            $query->forCompany($companyProfileId);
        }

        return $query->pluck('total', 'status')->toArray();
    }

    /**
     * Tren lamaran per bulan (12 bulan terakhir).
     */
    public function monthlyTrend(?int $companyProfileId = null): array
    {
        $query = Application::selectRaw("strftime('%Y-%m', created_at) as month, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(12)->startOfMonth())
            ->groupByRaw("strftime('%Y-%m', created_at)")
            ->orderByRaw("strftime('%Y-%m', created_at)");

        if ($companyProfileId) {
            $query->forCompany($companyProfileId);
        }

        return $query->get()->mapWithKeys(fn ($r) => [$r->month => $r->total])->toArray();
    }

    /**
     * Tabel pelamar untuk ekspor CSV.
     */
    public function applicantTable(?int $companyProfileId = null, array $filters = []): Builder
    {
        $query = Application::with([
            'applicantProfile.user',
            'jobPosting.companyProfile',
        ]);

        if ($companyProfileId) {
            $query->forCompany($companyProfileId);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['from'])->startOfDay());
        }

        if (! empty($filters['to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['to'])->endOfDay());
        }

        return $query->latest();
    }

    /**
     * Jumlah diterima per tahun lulus (untuk admin).
     */
    public function acceptedByGraduationYear(): array
    {
        return Application::selectRaw('applicant_profiles.graduation_year, count(*) as total')
            ->join('applicant_profiles', 'applications.applicant_profile_id', '=', 'applicant_profiles.id')
            ->where('status', ApplicationStatus::Accepted->value)
            ->whereNotNull('applicant_profiles.graduation_year')
            ->groupBy('applicant_profiles.graduation_year')
            ->orderBy('applicant_profiles.graduation_year')
            ->pluck('total', 'graduation_year')
            ->toArray();
    }

    /**
     * 10 lowongan dengan pelamar terbanyak.
     */
    public function topJobs(?int $companyProfileId = null): Collection
    {
        $query = JobPosting::withCount('applications')
            ->orderByDesc('applications_count')
            ->take(10);

        if ($companyProfileId) {
            $query->where('company_profile_id', $companyProfileId);
        }

        return $query->get();
    }
}

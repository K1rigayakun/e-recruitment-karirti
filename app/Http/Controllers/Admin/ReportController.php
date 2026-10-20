<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CsvExporter;
use App\Services\ReportBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportBuilder $report,
        private readonly CsvExporter $csv
    ) {}

    public function index(Request $request): View
    {
        $funnel = $this->report->statusFunnel();
        $trend = $this->report->monthlyTrend();
        $byYear = $this->report->acceptedByGraduationYear();
        $topJobs = $this->report->topJobs();

        return view('admin.reports.index', compact('funnel', 'trend', 'byYear', 'topJobs'));
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->report->applicantTable(null, $request->only('status', 'from', 'to'))->get();

        $data = $rows->map(fn ($app) => [
            $app->applicantProfile?->user?->name,
            $app->applicantProfile?->nim,
            $app->applicantProfile?->graduation_year,
            $app->jobPosting?->title,
            $app->jobPosting?->companyProfile?->company_name,
            $app->status?->label(),
            $app->created_at?->format('Y-m-d'),
        ]);

        return $this->csv->stream('laporan-pelamar-'.now()->format('Ymd').'.csv', [
            'Nama', 'NIM', 'Tahun Lulus', 'Posisi', 'Perusahaan', 'Status', 'Tanggal Lamar',
        ], $data);
    }
}

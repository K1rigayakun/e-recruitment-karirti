<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportFilterRequest;
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
        $profile = $request->user()->companyProfile;
        $funnel = $this->report->statusFunnel($profile->id);
        $topJobs = $this->report->topJobs($profile->id);

        return view('company.reports.index', compact('funnel', 'topJobs', 'profile'));
    }

    public function export(ReportFilterRequest $request): StreamedResponse
    {
        $profile = $request->user()->companyProfile;
        $cursor = $this->report->applicantTable($profile->id, $request->validated())->cursor();

        $rows = (function () use ($cursor) {
            foreach ($cursor as $app) {
                yield [
                    $app->applicantProfile?->user?->name,
                    $app->applicantProfile?->nim,
                    $app->applicantProfile?->graduation_year,
                    $app->jobPosting?->title,
                    $app->status?->label(),
                    $app->created_at?->format('Y-m-d'),
                ];
            }
        })();

        return $this->csv->stream('laporan-'.now()->format('Ymd').'.csv', [
            'Nama', 'NIM', 'Tahun Lulus', 'Posisi', 'Status', 'Tanggal Lamar',
        ], $rows);
    }
}

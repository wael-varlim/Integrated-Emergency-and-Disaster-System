<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Requests\Report\StoreReportRequest;
use App\Models\Report;
use App\Services\GeminiService;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ReportService $reportService,
        protected GeminiService $geminiService,
    ) {}

    public function store(StoreReportRequest $request): JsonResponse
    {
        $knownUser = $request->user()->knownUser;

        $data = $request->validated();

        if ($request->hasFile("media")) {
            $data["media"] = $request->file("media");
        }

        $reportResource = $this->reportService->createReport($data, $knownUser);

        // Get preferred language from Content-Language header (e.g., 'ar' or 'en')
        $preferredLanguage = $request->header('Content-Language', 'en');

        $advice = $this->geminiService->getAdvice(
            $data["news_type"],
            $data["body"] ?? "",
            $data["media"] ?? null,
            $preferredLanguage,
        );

        return $this->apiResponse(
            [
                "report" => $reportResource,
                "advice" => $advice,
            ],
            "report created successfully",
            201,
        );
    }

    public function show(string $id): JsonResponse
    {
        $report = Report::findOrFail($id);
        $this->authorize("view", $report);

        $reportResource = $this->reportService->getReportWithResponse((int)$id);

        return $this->apiResponse(
            [
                "report" => $reportResource,
            ],
            __("report.fetched_successfully"),
            200,
        );
    }

    public function index()
    {
        $knownUser = auth()->user()->knownUser;
        $reports = $this->reportService->getUserReports($knownUser->id);

        return $reports;
    }
}

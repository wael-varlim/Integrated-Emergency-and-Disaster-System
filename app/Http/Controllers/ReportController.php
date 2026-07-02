<?php

namespace App\Http\Controllers;

use App\Events\ReportCreated;
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

        $report = $reportResource->resource;

        $types = $report->news->newsType;

        $isDirectPost  = $types->contains('post_visibility', 'direct');        
        $needsDecision = !$isDirectPost && $types->contains('post_visibility', 'ai');


        $preferredLanguage = $request->header('Accept-Language', 'en');

        $advice = $this->geminiService->getAdvice(
            $data["news_type"],
            $data["body"] ?? "",
            $data["media"] ?? null,
            $preferredLanguage,
            $needsDecision,
        );

        $isPublic = $needsDecision ? ($advice['is_public'] ?? false) : null;

        event(new ReportCreated($report, $isPublic, $isDirectPost));

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

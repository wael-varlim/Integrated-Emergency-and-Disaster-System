<?php

namespace App\Services;

use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Resources\ReportResource;
use App\Http\Resources\ReportCollection;
use App\Models\Address;
use App\Models\KnownUser;
use App\Models\News;
use App\Models\NewsType;
use App\Models\Report;
use Brick\Math\BigInteger;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportService
{
    use ApiResponseTrait;


    public function __construct(
        protected AddressService $addressService,
        protected MediaService $mediaService,
    ) {}

    public function createReport(array $reportData, KnownUser $knownUser)
    {
        $mediaPath = null;
        $mediaMime = null;
        if (!empty($reportData["media"])) {
            $mediaMime = $reportData["media"]->getMimeType();
            if($mediaMime)    
                $mediaPath = $this->mediaService->storeMediaFile($reportData["media"], $mediaMime);
            else
                Log::warning("Could not determine MIME type for uploaded file, skipping.");
        }

        $location = $this->addressService->reverseGeocode(
            $reportData["latitude"],
            $reportData["longitude"],
        );

        $reportData["city_id"] = $location["city_id"];
        $reportData["street"] = $location["street"];
        $reportData["street_translations"] = $location["street_translations"];

        $reportModel = $this->storeReportRecord($reportData, $knownUser, $mediaPath, $mediaMime, $reportData["media"]);

        $reportData = $this->getReport($reportModel->id);

        return (new ReportResource($reportData))->additional([
            "message" => __("report.created_successfully"),
            "status" => 201,
        ]);
    }


    private function storeReportRecord(array $reportData, KnownUser $knownUser,
            ?string $mediaPath, ?string $mediaMime, UploadedFile $file)
    {
        return DB::transaction(function () use ($reportData, $knownUser, $mediaPath, $mediaMime, $file) 
        {
            $address = Address::firstOrCreate([
                "street" => $reportData["street"],
                "city_id" => $reportData["city_id"],
            ]);
            $this->addressService->storeStreetTranslations($address, $reportData["street_translations"] ?? []);

            $news = News::create([
                "body" => $reportData["body"] ?? "",
                "known_user_id" => $knownUser->id,
                "address_id" => $address->id,
            ]);

            $reportModel = Report::create([
                "news_id" => $news->id,
                "location" => DB::raw(
                    "ST_GeomFromText('POINT({$reportData["longitude"]} {$reportData["latitude"]})', 4326)"
                ),
            ]);

            foreach ($reportData["news_type"] as $type) {
                $newsType = NewsType::firstWhere(["type_name" => $type]);
                if ($newsType) {
                    $news->newsType()->attach($newsType->id);
                }
            }

            if ($mediaPath) {
                $this->mediaService->saveMediaRecord($mediaPath, $mediaMime, $news, $file);
            }

            return $reportModel;  
        });      
    } 


    public function getReportWithResponse(int $id)
    {
        $report = $this->getReport($id);

        return (new ReportResource($report))->additional([
            "message" => __("report.fetched_successfully"),
            "status" => 200,
        ]);
    }

    public function getUserReports(int $knownUserId)
    {
        $reports = Report::with([
            "news:id,body,address_id,known_user_id",
            "news.newsType:id,type_name",
            "news.newsType.currentTranslation:id,news_type_id,translation",
            "news.address:id,street,city_id",
            "news.address.currentTranslation:id,address_id,translation",
            "news.address.city:id,governorate_id",
            "news.address.city.currentTranslation:id,city_id,translation",
            "news.address.city.governorate.currentTranslation:id,governorate_id,translation",
            "news.media" => function ($query) {
                $query->select("id", "model_id", "media_url");
            },
            "news.report" => function ($q) {
                $q->selectRaw(
                    'news_id,
                    ST_X(location) as longitude,
                    ST_Y(location) as latitude',
                );
            },
        ])
            ->whereHas("news", function ($query) use ($knownUserId) {
                $query->where("known_user_id", $knownUserId);
            })
            ->latest()
            ->paginate(10);

        return (new ReportCollection($reports))->additional([
            "message" => __("report.list_fetched_successfully"),
            "status" => 200,
        ]);
    }

    protected function getReport(int $reportId)
    {
        return Report::with([
            "news:id,body,address_id,known_user_id",
            "news.newsType:id,type_name,post_visibility",
            "news.newsType.currentTranslation:id,news_type_id,translation",
            "news.address:id,street,city_id",
            "news.address.currentTranslation:id,address_id,translation",
            "news.address.city:id,governorate_id",
            "news.address.city.currentTranslation:id,city_id,translation",
            "news.address.city.governorate.currentTranslation:id,governorate_id,translation",
            "news.media" => function ($query) {
                $query->select("id", "model_id", "media_url");
            },
            "news.report" => function ($q) {
                $q->selectRaw(
                    'news_id,
                    ST_X(location) as longitude,
                    ST_Y(location) as latitude',
                );
            },
        ])->findOrFail($reportId);
    }

}

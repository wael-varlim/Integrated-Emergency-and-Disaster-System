<?php

namespace App\Services;

use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Resources\ReportResource;
use App\Http\Resources\ReportCollection;
use App\Models\Address;
use App\Models\City;
use App\Models\KnownUser;
use App\Models\Media;
use App\Models\MediaType;
use App\Models\News;
use App\Models\NewsType;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    use ApiResponseTrait;

    /**
     * Create a new report and return the formatted response.
     */
    public function createReport(array $data, KnownUser $knownUser)
    {
        // Resolve address from coordinates if city_id/street not provided
        if (!isset($data["city_id"]) || !isset($data["street"])) {
            $location = $this->reverseGeocode(
                $data["latitude"],
                $data["longitude"],
            );
            $data["city_id"] = $location["city_id"];
            $data["street"] = $location["street"];
        }

        $report = DB::transaction(function () use ($data, $knownUser) {
            // Create or get Address
            $address = Address::firstOrCreate([
                "city_id" => $data["city_id"],
                "street" => $data["street"],
            ]);

            // Create News
            $news = News::create([
                "body" => $data["body"] ?? "",
                "known_user_id" => $knownUser->id,
                "address_id" => $address->id,
            ]);

            // Create Report with geographic location
            // Convert string coordinates to geographic POINT
            $report = Report::create([
                "news_id" => $news->id,
                "location" => DB::raw(
                    "ST_GeomFromText('POINT({$data["longitude"]} {$data["latitude"]})', 4326)"
                ),
            ]);

            // Get or create News Type by name, then attach
            $newsType = NewsType::firstOrCreate([
                "type_name" => $data["news_type"],
            ]);
            $news->newsType()->attach($newsType->id);

            // Handle single Media File Upload if provided
            if (!empty($data["media"])) {
                $this->uploadMedia($data["media"], $news);
            }

            return $report;
        });

        // Load relationships
        $report = $this->getReportWithRelations($report->id);

        return (new ReportResource($report))->additional([
            "message" => __("report.created_successfully"),
            "status" => 201,
        ]);
    }

    /**
     * Get a single report by ID and return the formatted response.
     */
    public function getReport($id)
    {
        $report = $this->getReportWithRelations($id);

        return (new ReportResource($report))->additional([
            "message" => __("report.fetched_successfully"),
            "status" => 200,
        ]);
    }

    /**
     * Get paginated reports for a known user and return the formatted response.
     */
    public function getUserReports($knownUserId)
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

    /**
     * Load all relationships needed for ReportResource.
     */
    protected function getReportWithRelations($reportId)
    {
        return Report::with([
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
        ])->findOrFail($reportId);
    }

    /**
     * Upload and store media file linked to a news record.
     */
    protected function uploadMedia($file, News $news): void
    {
        $mimeType = $file->getMimeType();
        $mediaType = MediaType::firstOrCreate([
            "type_name" => $this->getMediaTypeName($mimeType),
        ]);
        $path = $file->store("images" . date("Y/m"), "public");

        Media::create([
            "media_url" => Storage::url($path),
            "media_type_id" => $mediaType->id,
            "model_type" => News::class,
            "model_id" => $news->id,
        ]);
    }

    /**
     * Determine media type name from mime type.
     */
    protected function getMediaTypeName(string $mimeType): string
    {
        if (str_starts_with($mimeType, "image/")) {
            return "image";
        }
        if (str_starts_with($mimeType, "video/")) {
            return "video";
        }
        if (str_starts_with($mimeType, "audio/")) {
            return "audio";
        }

        return "other";
    }

    /**
     * Reverse geocode lat/lon via Nominatim to get city_id and street name.
     */
    protected function reverseGeocode(float $lat, float $lon): array
    {
        $response = Http::withOptions(["verify" => false])
            ->withUserAgent("IntegratedEmergencySystem/1.0")
            ->timeout(10)
            ->get("https://nominatim.openstreetmap.org/reverse", [
                "format" => "json",
                "lat" => $lat,
                "lon" => $lon,
                "accept-language" =>
                    app()->getLocale() === "ar" ? "ar,en" : "en,ar",
            ]);

        if (!$response->successful()) {
            Log::error("Nominatim request failed", [
                "status" => $response->status(),
            ]);
            throw new \Exception(__("report.failed_to_geocode"));
        }

        $body = $response->json();

        if (!$body || !isset($body["address"])) {
            Log::error("Nominatim returned no address", ["response" => $body]);
            throw new \Exception(__("report.no_address_found"));
        }

        $address = $body["address"];
        $rawCityName =
            $address["city"] ??
            ($address["town"] ??
                ($address["village"] ??
                    ($address["municipality"] ??
                        ($address["county"] ?? null))));
        $street =
            $address["road"] ??
            ($address["street"] ?? ($address["suburb"] ?? ""));

        if (!$rawCityName) {
            Log::error("Nominatim returned no city name", [
                "address" => $address,
            ]);
            throw new \Exception(__("report.no_city_found"));
        }

        $city = $this->findCity($rawCityName);

        if (!$city) {
            Log::error("City not found in database", [
                "cityName" => $rawCityName,
            ]);
            throw new \Exception(
                __("report.unsupported_location", ["city" => $rawCityName]),
            );
        }

        return [
            "city_id" => $city->id,
            "street" => $street,
        ];
    }

    /**
     * Try to find a City record by matching the name flexibly.
     */
    protected function findCity(string $rawName): ?City
    {
        $normalize = fn(string $s) => trim(preg_replace("/\s+/", " ", $s));

        // Try exact match first
        $city = City::whereHas(
            "cityTranslation",
            fn($q) => $q->where("translation", $rawName),
        )
            ->orWhere("name", $rawName)
            ->first();
        if ($city) {
            return $city;
        }

        // Remove common suffixes and try again
        $suffixes = [
            " Municipality",
            " City",
            " Subdistrict",
            " District",
            " Governorate",
            " بلدية ",
            " مدينة ",
            " ناحية ",
            " منطقة ",
            " محافظة ",
        ];
        $cleaned = str_replace($suffixes, "", $rawName);
        $cleaned = $normalize($cleaned);

        if ($cleaned !== $rawName) {
            $city = City::whereHas(
                "cityTranslation",
                fn($q) => $q->where("translation", $cleaned),
            )
                ->orWhere("name", $cleaned)
                ->first();
            if ($city) {
                return $city;
            }
        }

        // Try matching by partial name — check if DB name contains the raw name or vice versa
        $allCities = City::with("cityTranslation")->get();
        foreach ($allCities as $c) {
            $dbNames = collect([$c->name]);
            foreach ($c->cityTranslation as $t) {
                $dbNames->push($t->translation);
            }
            foreach ($dbNames as $dbName) {
                $dbNorm = $normalize($dbName);
                if (
                    str_contains($dbNorm, $cleaned) ||
                    str_contains($cleaned, $dbNorm)
                ) {
                    return $c;
                }
            }
        }

        return null;
    }
}

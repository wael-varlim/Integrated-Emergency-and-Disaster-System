<?php

namespace App\Services;

use App\Models\Address;
use App\Models\City;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddressService
{
    public function reverseGeocode(float $lat, float $lon): array
    {
        $response = Http::withOptions(["verify" => false])
            ->withUserAgent("IntegratedEmergencySystem/1.0")
            ->timeout(10)
            ->get("https://nominatim.openstreetmap.org/reverse", [
                "format" => "json",
                "lat" => $lat,
                "lon" => $lon,
                "accept-language" =>"en",
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
            ($address["street"] ??
                ($address["neighbourhood"] ??
                    ($address["quarter"] ??
                        ($address["suburb"] ?? ""))));

        $street = preg_replace('/[\x{200E}\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', $street);
        $street = trim($street);

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
        $streetResults = $this->getStreetTranslations($street, $rawCityName);

        return [
            "city_id" => $city->id,
            "street" => $street,
            "street_translations" => $streetResults,
        ];
    }

    private function findCity(string $rawName): ?City
    {
        $normalize = fn(string $s) => trim(preg_replace("/\s+/", " ", $s));

        // Strategy 1: Exact match on name column
        $city = City::where("name", $rawName)->first();
        if ($city) {
            return $city;
        }

        // Strategy 2: Strip suffixes and try again
        $suffixes = [
            " Municipality",
            " City",
            " Subdistrict",
            " District",
            " Governorate",
        ];
        $cleaned = str_replace($suffixes, "", $rawName);
        $cleaned = $normalize($cleaned);

        if ($cleaned !== $rawName) {
            $city = City::where("name", $cleaned)->first();
            if ($city) {
                return $city;
            }
        }

        // Strategy 3: Partial match
        $allCities = City::all();
        foreach ($allCities as $c) {
            $dbNorm = $normalize($c->name);
            if (str_contains($dbNorm, $cleaned) || str_contains($cleaned, $dbNorm)) {
                return $c;
            }
        }

        return null;
    }

    private function getStreetTranslations(string $streetName, string $cityName): array
    {
        $response = Http::withOptions(["verify" => false])
            ->withUserAgent("IntegratedEmergencySystem/1.0")
            ->timeout(10)
            ->get("https://nominatim.openstreetmap.org/search", [
                "q"           => "{$streetName}, {$cityName}, Syria",
                "format"      => "json",
                "namedetails" => 1,
                "featuretype" => "street",
                "limit"       => 5,
            ]);

        if (!$response->successful() || empty($response->json())) {
            Log::warning("Nominatim street search returned nothing", [
                "street" => $streetName,
                "city"   => $cityName,
            ]);
            return [];
        }

        $results = $response->json();

        // Find the result whose name most closely matches our street
        foreach ($results as $result) {
            $nameDetails = $result["namedetails"] ?? [];
            $resultName  = $nameDetails["name:en"] ?? ($nameDetails["name"] ?? "");

            if (stripos($resultName, $streetName) !== false || stripos($streetName, $resultName) !== false) {
                $translations = [];
                foreach ($nameDetails as $key => $value) {
                    if (str_starts_with($key, "name:")) {
                        $lang = str_replace("name:", "", $key);
                        $value = preg_replace('/[\x{200E}\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', $value);
                        $value = trim($value);
                        $translations[$lang] = $value;
                    }
                }

                Log::info("Street translations found", [
                    "street"       => $streetName,
                    "translations" => $translations,
                ]);

                return $translations;
            }
        }

        Log::warning("Nominatim street search: no matching result found", [
            "street"  => $streetName,
            "results" => array_column($results, "display_name"),
        ]);

        return [];
    }

    public function storeStreetTranslations(Address $address, array $streetTranslations): void
    {
        foreach ($streetTranslations as $lang => $translation) {
            if (empty($translation)) {
                continue;
            }

            $address->addressTranslation()->updateOrCreate(
                ["language_code" => $lang],
                ["translation" => $translation],
            );
        }   
    }
}
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config("services.gemini.api_key");
        $this->model = "gemini-2.5-flash";
    }

    public function getAdvice(string $newsType, string $body): string
    {
        $prompt = <<<PROMPT
        You are an intelligent emergency assistant. Provide urgent safety instructions based on the hazard type and the user's description.

        Hazard type: {$newsType}
        User description: {$body}

        Give appropriate safety instructions for this situation. Be precise and clear, order steps by priority.
        Respond in the same language the user used (if they wrote in English, answer in English; if in Arabic, answer in Arabic).
        Do not ask questions, just provide the instructions.
        PROMPT;

        $response = Http::timeout(30)
            ->withHeader("X-goog-api-key", $this->apiKey)
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent",
                [
                    "contents" => [["parts" => [["text" => $prompt]]]],
                    "generationConfig" => [
                        "temperature" => 0.7,
                        "maxOutputTokens" => 1024,
                    ],
                ],
            );

        if (!$response->successful()) {
            Log::error("Gemini API error", [
                "status" => $response->status(),
                "body" => $response->body(),
            ]);

            return "Sorry, unable to get safety instructions right now. Please try again.";
        }

        return $response->json()["candidates"][0]["content"]["parts"][0][
            "text"
        ] ?? "Sorry, unable to get safety instructions right now.";
    }
}

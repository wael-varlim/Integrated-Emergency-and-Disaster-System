<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = "https://generativelanguage.googleapis.com";
    protected string $uploadUrl = "https://generativelanguage.googleapis.com/upload/v1beta/files";

    public function __construct()
    {
        $this->apiKey = config("services.gemini.api_key");
        $this->model = "gemini-2.5-flash";
    }

    public function getAdvice(
        string $newsType,
        string $body,
        ?UploadedFile $media = null,
        string $language = 'en',
    ): array {
        $description = $body ?: "No text description provided.";

        // Determine language instructions based on preferred language
        $languageInstruction = match(strtolower($language)) {
            'ar', 'arabic' => '- Respond ENTIRELY in Arabic.',
            'en', 'english' => '- Respond ENTIRELY in English.',
            default => '- Detect the language from the "Hazard type" and "User description" fields and respond in that language.',
        };

        $prompt = <<<PROMPT
        You are an intelligent emergency assistant. Provide urgent safety instructions based on the hazard type and the user's description.

        Hazard type: {$newsType}
        User description: {$description}

        Rules:
        - Return ONLY a valid JSON object, no markdown, no extra text.
        - Maximum 5 steps, each step maximum 15 words.
        {$languageInstruction}
        - If a media file is attached (image/audio/video), also consider its content for safety instructions.

        Required JSON format:
        {
            "title": "short title describing the emergency (max 6 words)",
            "steps": [
                "step one instruction",
                "step two instruction",
                "step three instruction"
            ]
        }
        PROMPT;

        $parts = [["text" => $prompt]];

        if ($media) {
            $mediaPart = $this->buildMediaPart($media);
            if ($mediaPart) {
                $parts[] = $mediaPart;
            }
        }

        $requestPayload = [
            "contents" => [["parts" => $parts]],
            "generationConfig" => [
                "temperature" => 0.4,
                "maxOutputTokens" => 2048, // Increased from 512 to handle video processing
                "responseMimeType" => "application/json",
            ],
        ];

        // Log the request being sent to Gemini
        Log::info("Gemini API Request", [
            "model" => $this->model,
            "endpoint" => "{$this->baseUrl}/v1beta/models/{$this->model}:generateContent",
            "news_type" => $newsType,
            "body" => $body,
            "language" => $language,
            "has_media" => $media !== null,
            "media_type" => $media ? $media->getMimeType() : null,
            "media_size" => $media ? $media->getSize() : null,
            "parts_count" => count($parts),
            "request_payload" => $this->sanitizeRequestForLog($requestPayload),
        ]);

        $response = Http::timeout(120)
            ->retry(3, 1000, function ($exception, $request) {
                // Retry on 503 (service unavailable) and 429 (rate limit)
                if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                    $status = $exception->response->status();
                    if (in_array($status, [429, 503])) {
                        Log::info("Gemini API: retrying after {$status} error");
                        return true;
                    }
                }
                return false;
            })
            ->withHeader("X-goog-api-key", $this->apiKey)
            ->post(
                "{$this->baseUrl}/v1beta/models/{$this->model}:generateContent",
                $requestPayload,
            );

        if (!$response->successful()) {
            Log::error("Gemini API error (after retries)", [
                "status" => $response->status(),
                "body" => $response->body(),
                "headers" => $response->headers(),
                "news_type" => $newsType,
                "media_type" => $media ? $media->getMimeType() : null,
            ]);

            return $this->fallbackAdvice();
        }

        $responseData = $response->json();

        // Log the response from Gemini
        Log::info("Gemini API Response", [
            "status" => $response->status(),
            "response_data" => $responseData,
        ]);

        // Check for finish reason issues
        $finishReason = $responseData["candidates"][0]["finishReason"] ?? null;
        if ($finishReason === "MAX_TOKENS") {
            Log::warning("Gemini: response truncated due to MAX_TOKENS", [
                "finish_reason" => $finishReason,
                "usage" => $responseData["usageMetadata"] ?? null,
            ]);
        }

        $text =
            $responseData["candidates"][0]["content"]["parts"][0]["text"] ??
            null;

        if (!$text) {
            return $this->fallbackAdvice();
        }

        $parsed = json_decode($text, true);

        if (
            !$parsed ||
            !isset($parsed["title"], $parsed["steps"]) ||
            !is_array($parsed["steps"]) ||
            empty($parsed["steps"])
        ) {
            Log::warning("Gemini: unexpected JSON structure.", [
                "raw" => $text,
            ]);
            return $this->fallbackAdvice();
        }

        return [
            "title" => trim($parsed["title"]),
            "steps" => array_values(array_map("trim", $parsed["steps"])),
        ];
    }

    /**
     * Fallback when Gemini is unavailable or returns unexpected data.
     *
     * @return array<string, mixed>
     */
    protected function fallbackAdvice(): array
    {
        return [
            "title" => __("report.fallback_title"),
            "steps" => [
                __("report.fallback_step_1"),
                __("report.fallback_step_2"),
                __("report.fallback_step_3"),
            ],
        ];
    }

    /**
     * Sanitize request payload for logging (truncate base64 data).
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    protected function sanitizeRequestForLog(array $payload): array
    {
        $sanitized = $payload;

        if (isset($sanitized["contents"][0]["parts"])) {
            foreach ($sanitized["contents"][0]["parts"] as $index => $part) {
                // Truncate inline base64 data for logging
                if (isset($part["inline_data"]["data"])) {
                    $dataLength = strlen($part["inline_data"]["data"]);
                    $sanitized["contents"][0]["parts"][$index]["inline_data"]["data"] = 
                        "[BASE64_DATA_TRUNCATED: {$dataLength} bytes]";
                }
                
                // Keep file_data as is (just URI reference)
                if (isset($part["file_data"])) {
                    $sanitized["contents"][0]["parts"][$index]["file_data"] = $part["file_data"];
                }
            }
        }

        return $sanitized;
    }

    /**
     * Build the correct part array depending on the media type.
     *
     * @return array<string, mixed>|null
     */
    protected function buildMediaPart(UploadedFile $media): ?array
    {
        $mimeType = $media->getMimeType();

        if (!$mimeType) {
            Log::warning(
                "Gemini: could not determine MIME type of uploaded file.",
            );
            return null;
        }

        // Images → inline base64 (fast, no extra API call, limit ~20 MB)
        if (str_starts_with($mimeType, "image/")) {
            $contents = file_get_contents($media->getRealPath());

            if ($contents === false) {
                Log::error("Gemini: failed to read image file.", [
                    "path" => $media->getRealPath(),
                ]);
                return null;
            }

            return [
                "inline_data" => [
                    "mime_type" => $mimeType,
                    "data" => base64_encode($contents),
                ],
            ];
        }

        // Audio → use File API for larger files, inline for small ones
        if (str_starts_with($mimeType, "audio/")) {
            $fileSize = $media->getSize();
            
            // If audio file is larger than 10MB, use File API
            if ($fileSize > 10 * 1024 * 1024) {
                Log::info("Gemini: uploading large audio file via File API", [
                    "size" => $fileSize,
                    "mime_type" => $mimeType,
                ]);
                
                $fileUri = $this->uploadFile($media);

                if (!$fileUri) {
                    return null;
                }

                return [
                    "file_data" => [
                        "mime_type" => $mimeType,
                        "file_uri" => $fileUri,
                    ],
                ];
            }
            
            // For smaller audio files, use inline base64
            $contents = file_get_contents($media->getRealPath());

            if ($contents === false) {
                Log::error("Gemini: failed to read audio file.", [
                    "path" => $media->getRealPath(),
                ]);
                return null;
            }

            return [
                "inline_data" => [
                    "mime_type" => $mimeType,
                    "data" => base64_encode($contents),
                ],
            ];
        }

        // Videos → must go through the File API
        if (str_starts_with($mimeType, "video/")) {
            $fileUri = $this->uploadFile($media);

            if (!$fileUri) {
                return null;
            }

            return [
                "file_data" => [
                    "mime_type" => $mimeType,
                    "file_uri" => $fileUri,
                ],
            ];
        }

        Log::warning("Gemini: unsupported media type skipped.", [
            "mime_type" => $mimeType,
        ]);

        return null;
    }

    /**
     * Upload a video to the Gemini File API using a resumable upload
     * and return its hosted URI once processing is complete.
     */
    protected function uploadFile(UploadedFile $uploadedFile): ?string
    {
        try {
            $fileSize = $uploadedFile->getSize();
            $mimeType = $uploadedFile->getMimeType();
            $displayName = $uploadedFile->getClientOriginalName();

            Log::info("Gemini File Upload: Starting", [
                "display_name" => $displayName,
                "mime_type" => $mimeType,
                "file_size" => $fileSize,
            ]);

            // ── Step 1: Initiate resumable upload session ──────────────────
            $initResponse = Http::withHeaders([
                "X-Goog-Upload-Protocol" => "resumable",
                "X-Goog-Upload-Command" => "start",
                "X-Goog-Upload-Header-Content-Length" => (string) $fileSize,
                "X-Goog-Upload-Header-Content-Type" => $mimeType,
                "Content-Type" => "application/json",
                "X-goog-api-key" => $this->apiKey,
            ])->post($this->uploadUrl, [
                "file" => ["display_name" => $displayName],
            ]);

            if (!$initResponse->successful()) {
                Log::error("Gemini File API: upload initiation failed.", [
                    "status" => $initResponse->status(),
                    "body" => $initResponse->body(),
                ]);
                return null;
            }

            $resumableUrl = $initResponse->header("X-Goog-Upload-URL");

            if (!$resumableUrl) {
                Log::error("Gemini File API: no resumable URL returned.", [
                    "headers" => $initResponse->headers(),
                ]);
                return null;
            }

            Log::info("Gemini File Upload: Session initiated", [
                "resumable_url" => $resumableUrl,
            ]);

            // ── Step 2: Upload raw bytes ───────────────────────────────────
            $fileContents = file_get_contents($uploadedFile->getRealPath());

            if ($fileContents === false) {
                Log::error("Gemini File API: failed to read file.", [
                    "path" => $uploadedFile->getRealPath(),
                ]);
                return null;
            }

            $uploadResponse = Http::timeout(300)
                ->withHeaders([
                    "Content-Length" => (string) $fileSize,
                    "Content-Type" => $mimeType,
                    "X-Goog-Upload-Protocol" => "resumable",
                    "X-Goog-Upload-Command" => "upload, finalize",
                    "X-Goog-Upload-Offset" => "0",
                ])
                ->withBody($fileContents, $mimeType)
                ->put($resumableUrl);

            if (!$uploadResponse->successful()) {
                Log::error("Gemini File API: byte upload failed.", [
                    "status" => $uploadResponse->status(),
                    "body" => $uploadResponse->body(),
                ]);
                return null;
            }

            $responseJson = $uploadResponse->json();

            // The response may nest data under a "file" key or return it flat
            $fileData = $responseJson["file"] ?? ($responseJson ?? null);

            if (!$fileData || !isset($fileData["uri"], $fileData["name"])) {
                Log::error("Gemini File API: unexpected upload response.", [
                    "response" => $responseJson,
                ]);
                return null;
            }

            Log::info("Gemini File Upload: Bytes uploaded successfully", [
                "file_name" => $fileData["name"],
                "file_uri" => $fileData["uri"],
                "state" => $fileData["state"] ?? "unknown",
            ]);

            // ── Step 3: Wait until the video is processed ──────────────────
            $readyFile = $this->waitForFileReady($fileData["name"]);

            if (!$readyFile) {
                return null;
            }

            Log::info("Gemini File Upload: File ready", [
                "file_uri" => $readyFile["uri"],
                "file_name" => $readyFile["name"],
            ]);

            return $readyFile["uri"];
        } catch (\Exception $e) {
            Log::error("Gemini File API: unexpected exception.", [
                "message" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Poll the File API until the file state is ACTIVE (or fail on timeout).
     *
     * @return array<string, mixed>|null
     */
    protected function waitForFileReady(
        string $fileName,
        int $maxRetries = 30,
        int $delaySeconds = 2,
    ): ?array {
        // $fileName is already in the form "files/{id}"
        $url = "{$this->baseUrl}/v1beta/{$fileName}";

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $response = Http::withHeader("X-goog-api-key", $this->apiKey)->get(
                $url,
            );

            if (!$response->successful()) {
                Log::error("Gemini File API: status check failed.", [
                    "status" => $response->status(),
                    "body" => $response->body(),
                    "attempt" => $attempt,
                ]);
                return null;
            }

            $responseJson = $response->json();
            // GET /files/{name} returns the file object directly (not nested)
            $state = $responseJson["state"] ?? "UNKNOWN";

            if ($state === "ACTIVE") {
                return $responseJson;
            }

            if ($state === "FAILED") {
                Log::error("Gemini File API: server-side processing failed.", [
                    "file" => $responseJson,
                ]);
                return null;
            }

            Log::info("Gemini File API: waiting for file to become active.", [
                "attempt" => $attempt,
                "state" => $state,
                "fileName" => $fileName,
            ]);

            sleep($delaySeconds);
        }

        Log::error("Gemini File API: timed out waiting for file.", [
            "fileName" => $fileName,
            "maxRetries" => $maxRetries,
        ]);

        return null;
    }
}

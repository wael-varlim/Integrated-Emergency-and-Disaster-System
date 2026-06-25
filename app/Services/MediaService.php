<?php

namespace App\Services;

use App\Models\Media;
use App\Models\MediaType;
use App\Models\News;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function storeMediaFile(UploadedFile $file, $mimeType): string
    {
        $folder = $this->getMediaTypeName($mimeType) . "s/" . date("Y/m");
        return $file->store($folder, "public");
    }

    public function saveMediaRecord(string $path, string $mimeType, News $news): void
    {
        $mediaType = MediaType::firstOrCreate([
            "type_name" => $this->getMediaTypeName($mimeType),
        ]);

        Media::create([
            "media_url"     => Storage::url($path),
            "media_type_id" => $mediaType->id,
            "model_type"    => News::class,
            "model_id"      => $news->id,
        ]);
    }

    public function getMediaTypeName(string $mimeType): ?string
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

        return null;
    }
}
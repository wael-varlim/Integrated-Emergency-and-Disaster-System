<?php

namespace App\Services;

use App\Models\Media;
use App\Models\MediaType;
use App\Models\News;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function storeMediaFile(UploadedFile $file, $mimeType): string
    {
        $extension =  $file->getClientOriginalExtension();
        $filename = \Illuminate\Support\Str::uuid() . '.' . $extension;
        $folder = $this->getMediaTypeName($mimeType, $extension) . "s/" . date("Y/m");
        return $file->storeAs($folder, $filename, "public");
        //return $file->store($folder, "public");
    }

    public function saveMediaRecord(string $path, string $mimeType, News $news, UploadedFile $file): void
    {
        $mediaType = MediaType::firstOrCreate([
            "type_name" => $this->getMediaTypeName($mimeType, $file->getClientOriginalExtension()),
        ]);

        Media::create([
            "media_url"     => Storage::url($path),
            "media_type_id" => $mediaType->id,
            "model_type"    => News::class,
            "model_id"      => $news->id,
        ]);
    }

    public function getMediaTypeName(string $mimeType, string $extension): ?string
    {
        $audioExtensions = ['m4a', 'aac', 'mp3', 'wav', 'ogg', 'flac', 'wma', 'opus'];
        if (in_array(strtolower($extension), $audioExtensions)) {
            return 'audio';
        }


        if (str_starts_with($mimeType, "image/"))   return "image";
        
        if (str_starts_with($mimeType, "video/"))   return "video";
        
        if (str_starts_with($mimeType, "audio/"))   return "audio";
        

        return null;
    }
}
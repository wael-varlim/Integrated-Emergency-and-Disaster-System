<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // News Type (required) - string value (type name)
            'news_type'   => 'required|array',
            'news_type.*' => 'string',
            // Text (nullable)
            "body" => ["nullable", "string", "max:5000"],

            // Location (required for report)
            "latitude" => ["required", "numeric", "between:-90,90"],
            "longitude" => ["required", "numeric", "between:-180,180"],

            // Media file (nullable) - single file only (image OR video OR audio)
            "media" => [
                "nullable",
                "file",
                "mimes:jpeg,jpg,png,gif,mp4,mov,avi,mp3,wav,m4a",
                "max:51200",
            ], // Max 50MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'news_type.required'   => __("report.news_type_required"),
            'news_type.array'      => __("report.news_type_array"),
            'news_type.*.string'   => __("report.news_type_string"),

            "latitude.required" => __("report.latitude_required"),
            "latitude.between" => __("report.latitude_between"),
            "longitude.required" => __("report.longitude_required"),
            "longitude.between" => __("report.longitude_between"),

            "media.file" => __("report.media_file"),
            "media.mimes" => __("report.media_mimes"),
            "media.max" => __("report.media_max"),
        ];
    }
}

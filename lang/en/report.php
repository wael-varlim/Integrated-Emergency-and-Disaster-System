<?php

return [
    // Status messages
    "created_successfully" => "Report created successfully",
    "fetched_successfully" => "Report fetched successfully",
    "list_fetched_successfully" => "Reports fetched successfully",

    // Exception / error messages
    "failed_to_geocode" => "Failed to determine location from coordinates",
    "no_address_found" => "Could not determine address from location",
    "no_city_found" => "Could not determine city from location",
    "unsupported_location" => "Unsupported location: ':city' is not recognized",

    // Validation messages
    "news_type_required" => "The news type is required.",
    "news_type_string" => "The news type must be a string.",

    "latitude_required" => "The latitude is required.",
    "latitude_between" => "The latitude must be between -90 and 90.",
    "longitude_required" => "The longitude is required.",
    "longitude_between" => "The longitude must be between -180 and 180.",

    "media_file" => "The field must be a file.",
    "media_mimes" =>
        "Unsupported file type. Supported types: images (jpeg, jpg, png, gif), videos (mp4, mov, avi), audio (mp3, wav, m4a).",
    "media_max" => "The file size must not exceed 50MB.",

    // Fallback safety advice
    "fallback_title" => "Safety Instructions Unavailable",
    "fallback_step_1" => "Move to a safe location immediately.",
    "fallback_step_2" => "Call local emergency services.",
    "fallback_step_3" => "Follow instructions from authorities.",
];

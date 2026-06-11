<?php

return [
    // Status messages
    'created_successfully' => 'تم إنشاء البلاغ بنجاح',
    'fetched_successfully' => 'تم جلب البلاغ بنجاح',
    'list_fetched_successfully' => 'تم جلب البلاغات بنجاح',

    // Exception / error messages
    'failed_to_geocode' => 'فشل في تحديد الموقع من الإحداثيات',
    'no_address_found'  => 'تعذر تحديد العنوان من الموقع',
    'no_city_found'     => 'تعذر تحديد المدينة من الموقع',
    'unsupported_location' => "الموقع غير مدعوم: ':city' غير معروف",

    // Validation messages
    'news_type_required' => 'نوع الخبر مطلوب',
    'news_type_string'   => 'نوع الخبر يجب أن يكون نص',

    'latitude_required' => 'خط العرض مطلوب',
    'latitude_between'  => 'خط العرض يجب أن يكون بين -90 و 90',
    'longitude_required' => 'خط الطول مطلوب',
    'longitude_between'  => 'خط الطول يجب أن يكون بين -180 و 180',

    'media_file'    => 'يجب أن يكون العنصر ملف',
    'media_mimes'   => 'نوع الملف غير مدعوم. الأنواع المدعومة: صور (jpeg, jpg, png, gif), فيديو (mp4, mov, avi), صوت (mp3, wav, m4a)',
    'media_max'     => 'حجم الملف يجب ألا يتجاوز 50 ميجابايت',
];

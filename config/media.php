<?php

return [
    'disk' => env('MEDIA_DISK', 'public'),
    'max_upload_size' => 10240, // 10MB in KB
    'allowed_extensions' => [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt',
        'mp4', 'mov', 'avi',
        'mp3', 'wav'
    ],
    'image_sizes' => [
        'thumb' => [
            'width' => 150,
            'height' => 150,
            'crop' => true
        ],
        'small' => [
            'width' => 300,
            'height' => null,
            'crop' => false
        ],
        'medium' => [
            'width' => 600,
            'height' => null,
            'crop' => false
        ],
        'large' => [
            'width' => 1200,
            'height' => null,
            'crop' => false
        ],
    ],
    'collections' => [
        'blog' => 'Blog Yazıları',
        'teacher' => 'Öğretmenler',
        'page' => 'Sayfalar',
        'gallery' => 'Galeri',
        'slider' => 'Slider',
        'branding' => 'Branding Assets',
        'course' => 'Kurslar',
        'general' => 'Genel Yüklemeler',
    ],
];

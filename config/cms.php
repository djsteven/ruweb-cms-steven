<?php

return [
    'upload' => [
        'allowed_image_mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
        'allowed_document_mimes' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx'],
        'image_max_size' => env('CMS_IMAGE_MAX_SIZE', 5120), // KB
        'document_max_size' => env('CMS_DOCUMENT_MAX_SIZE', 10240), // KB
    ],

    'roles' => ['admin', 'editor'],

    'statuses' => ['draft', 'published'],

    'templates' => [
        'default' => [
            'name' => 'Default Page',
            'sections' => ['content'],
        ],
        'home' => [
            'name' => 'Homepage',
            'sections' => ['hero', 'features', 'cta'],
        ],
    ],

    'default_admin' => [
        'name' => env('CMS_ADMIN_NAME', 'Admin'),
        'email' => env('CMS_ADMIN_EMAIL', 'admin@flaxt.local'),
        'password' => env('CMS_ADMIN_PASSWORD', 'password'),
    ],

    'menu_locations' => [
        'header' => 'Header Navigation',
        'footer' => 'Footer Navigation',
    ],

    'mcp' => [
        'per_page' => 20,
        'settings_writable_keys' => [
            'site_name',
            'site_description',
            'site_favicon',
            'footer_text',
            'default_meta_title',
            'default_meta_description',
            'social_facebook',
            'social_twitter',
            'social_instagram',
        ],
    ],
];

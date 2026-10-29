<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Frontend
    |--------------------------------------------------------------------------
    |
    | The Next.js site that consumes this API. It is allowed through CORS and
    | is told to revalidate its cached pages whenever content changes.
    |
    */

    'frontend_url' => rtrim((string) env('FRONTEND_URL', 'http://localhost:3000'), '/'),

    'revalidate_secret' => env('REVALIDATE_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Contact form
    |--------------------------------------------------------------------------
    */

    'admin_email' => env('ADMIN_EMAIL'),

    'turnstile_secret' => env('TURNSTILE_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    |
    | Disk that uploaded images and the resume are stored on. Use "public" for
    | local storage or "s3" for S3 / Cloudflare R2.
    |
    */

    'media_disk' => env('MEDIA_DISK', 'public'),

];

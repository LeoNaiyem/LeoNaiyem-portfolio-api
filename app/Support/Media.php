<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media
{
    public static function disk(): string
    {
        return config('portfolio.media_disk');
    }

    /**
     * Public URL for a stored file path.
     */
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return Storage::disk(static::disk())->url($path);
    }
}

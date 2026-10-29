<?php

namespace App\Models;

use App\Models\Concerns\RevalidatesFrontend;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Site-wide profile and SEO defaults. There is only ever one row.
 */
#[Fillable(['name', 'title', 'bio', 'email', 'avatar', 'resume', 'socials', 'seo_title', 'seo_description'])]
class Setting extends Model
{
    use RevalidatesFrontend;

    public static function current(): self
    {
        return static::query()->firstOrCreate([], ['name' => config('app.name')]);
    }

    protected function casts(): array
    {
        return [
            'socials' => 'array',
        ];
    }
}

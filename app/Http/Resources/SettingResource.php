<?php

namespace App\Http\Resources;

use App\Models\Setting;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Setting
 */
class SettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'title' => $this->title,
            'bio' => $this->bio,
            'email' => $this->email,
            'avatar' => Media::url($this->avatar),
            'resume' => Media::url($this->resume),
            'socials' => $this->socials ?? [],
            'seo' => [
                'title' => $this->seo_title,
                'description' => $this->seo_description,
            ],
        ];
    }
}

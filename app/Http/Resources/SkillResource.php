<?php

namespace App\Http\Resources;

use App\Models\Skill;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Skill
 */
class SkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => Media::url($this->icon),
            'bg_color' => $this->bg_color,
        ];
    }
}

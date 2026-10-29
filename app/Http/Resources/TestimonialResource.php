<?php

namespace App\Http\Resources;

use App\Models\Testimonial;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Testimonial
 */
class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'company' => $this->company,
            'feedback' => $this->feedback,
            'image' => Media::url($this->image),
        ];
    }
}

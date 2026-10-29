<?php

namespace App\Http\Resources;

use App\Models\Project;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'body' => $this->when($request->routeIs('api.v1.projects.show'), $this->body),
            'image' => Media::url($this->image),
            'project_url' => $this->project_url,
            'code_url' => $this->code_url,
            'is_featured' => $this->is_featured,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'published_at' => $this->published_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

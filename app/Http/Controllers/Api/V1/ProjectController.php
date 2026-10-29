<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = Project::published()
            ->with(['tags' => fn ($query) => $query->ordered()])
            ->when($request->query('tag'), fn ($query, $tag) => $query->whereRelation('tags', 'slug', $tag))
            ->ordered()
            ->get();

        return ProjectResource::collection($projects);
    }

    public function show(string $slug): ProjectResource
    {
        $project = Project::published()
            ->with(['tags' => fn ($query) => $query->ordered()])
            ->where('slug', $slug)
            ->firstOrFail();

        return new ProjectResource($project);
    }
}

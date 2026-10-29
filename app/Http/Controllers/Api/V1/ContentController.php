<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\ExperienceResource;
use App\Http\Resources\SettingResource;
use App\Http\Resources\SkillResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\TestimonialResource;
use App\Models\About;
use App\Models\Brand;
use App\Models\Experience;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Read-only endpoints for the simple portfolio sections.
 */
class ContentController extends Controller
{
    public function settings(): SettingResource
    {
        return new SettingResource(Setting::current());
    }

    public function abouts(): AnonymousResourceCollection
    {
        return AboutResource::collection(About::ordered()->get());
    }

    /**
     * Only tags that are attached to at least one published project.
     */
    public function tags(): AnonymousResourceCollection
    {
        return TagResource::collection(
            Tag::whereHas('projects', fn ($query) => $query->published())->ordered()->get()
        );
    }

    public function skills(): AnonymousResourceCollection
    {
        return SkillResource::collection(Skill::ordered()->get());
    }

    /**
     * Experiences grouped by year, newest year first.
     */
    public function experiences(): JsonResponse
    {
        $groups = Experience::query()
            ->orderByDesc('year')
            ->ordered()
            ->get()
            ->groupBy('year')
            ->map(fn ($items, $year) => [
                'year' => (string) $year,
                'items' => ExperienceResource::collection($items),
            ])
            ->values();

        return response()->json(['data' => $groups]);
    }

    public function testimonials(): AnonymousResourceCollection
    {
        return TestimonialResource::collection(Testimonial::ordered()->get());
    }

    public function brands(): AnonymousResourceCollection
    {
        return BrandResource::collection(Brand::ordered()->get());
    }
}

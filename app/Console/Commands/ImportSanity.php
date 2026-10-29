<?php

namespace App\Console\Commands;

use App\Models\About;
use App\Models\Brand;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\Testimonial;
use App\Support\Media;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * One-off migration of the old Sanity dataset. Safe to run more than once:
 * records are matched on their natural keys and images are only downloaded
 * if they are missing.
 */
#[Signature('import:sanity
    {project : The Sanity project ID}
    {--dataset=production : The Sanity dataset}
    {--token= : API token (only needed if the dataset is private)}')]
#[Description('Import portfolio content and images from Sanity')]
class ImportSanity extends Command
{
    public function handle(): int
    {
        $this->importAbouts();
        $this->importProjects();
        $this->importSkills();
        $this->importExperiences();
        $this->importTestimonials();
        $this->importBrands();
        $this->importContactMessages();

        $this->info('Import finished.');

        return self::SUCCESS;
    }

    private function importAbouts(): void
    {
        $rows = $this->query('*[_type == "abouts"] | order(_createdAt asc) {title, description, "image": imgUrl.asset->url}');

        foreach ($rows as $index => $row) {
            About::updateOrCreate(['title' => $row['title']], [
                'description' => $row['description'] ?? '',
                'image' => $this->download($row['image'] ?? null, 'abouts'),
                'sort_order' => $index,
            ]);
        }

        $this->line('Abouts: '.count($rows));
    }

    private function importProjects(): void
    {
        $rows = $this->query('*[_type == "works"] | order(_createdAt asc) {title, description, projectLink, codeLink, tags, _createdAt, "image": imgUrl.asset->url}');

        foreach ($rows as $index => $row) {
            $project = Project::updateOrCreate(['slug' => Str::slug($row['title'])], [
                'title' => $row['title'],
                'description' => $row['description'] ?? '',
                'project_url' => $row['projectLink'] ?? null,
                'code_url' => $row['codeLink'] ?? null,
                'image' => $this->download($row['image'] ?? null, 'projects'),
                'sort_order' => $index,
                'published_at' => Carbon::parse($row['_createdAt']),
            ]);

            $tagIds = collect($row['tags'] ?? [])
                ->filter()
                ->map(fn (string $name) => Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id);

            $project->tags()->sync($tagIds);
        }

        $this->line('Projects: '.count($rows));
    }

    private function importSkills(): void
    {
        $rows = $this->query('*[_type == "skills"] | order(_createdAt asc) {name, bgColor, "image": icon.asset->url}');

        foreach ($rows as $index => $row) {
            Skill::updateOrCreate(['name' => $row['name']], [
                'bg_color' => $row['bgColor'] ?? null,
                'icon' => $this->download($row['image'] ?? null, 'skills'),
                'sort_order' => $index,
            ]);
        }

        $this->line('Skills: '.count($rows));
    }

    private function importExperiences(): void
    {
        $rows = $this->query('*[_type == "experiences"] | order(year desc) {year, works[]{name, company, desc}}');
        $count = 0;

        foreach ($rows as $row) {
            foreach ($row['works'] ?? [] as $index => $work) {
                Experience::updateOrCreate([
                    'year' => $row['year'],
                    'role' => $work['name'] ?? '',
                    'company' => $work['company'] ?? '',
                ], [
                    'description' => $work['desc'] ?? null,
                    'sort_order' => $index,
                ]);
                $count++;
            }
        }

        $this->line("Experiences: {$count}");
    }

    private function importTestimonials(): void
    {
        $rows = $this->query('*[_type == "testimonials"] | order(_createdAt asc) {name, company, feedback, "image": imgurl.asset->url}');

        foreach ($rows as $index => $row) {
            Testimonial::updateOrCreate(['name' => $row['name']], [
                'company' => $row['company'] ?? null,
                'feedback' => $row['feedback'] ?? '',
                'image' => $this->download($row['image'] ?? null, 'testimonials'),
                'sort_order' => $index,
            ]);
        }

        $this->line('Testimonials: '.count($rows));
    }

    private function importBrands(): void
    {
        $rows = $this->query('*[_type == "brands"] | order(_createdAt asc) {name, "image": imgUrl.asset->url}');

        foreach ($rows as $index => $row) {
            Brand::updateOrCreate(['name' => $row['name']], [
                'image' => $this->download($row['image'] ?? null, 'brands'),
                'sort_order' => $index,
            ]);
        }

        $this->line('Brands: '.count($rows));
    }

    private function importContactMessages(): void
    {
        $rows = $this->query('*[_type == "contact"] | order(_createdAt asc) {name, email, message, _createdAt}');

        foreach ($rows as $row) {
            $message = ContactMessage::firstOrNew([
                'email' => $row['email'] ?? '',
                'message' => $row['message'] ?? '',
            ]);

            if (! $message->exists) {
                $message->fill(['name' => $row['name'] ?? 'Unknown', 'read_at' => now()]);
                $message->created_at = Carbon::parse($row['_createdAt']);
                $message->save();
            }
        }

        $this->line('Contact messages: '.count($rows));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function query(string $groq): array
    {
        $project = $this->argument('project');
        $dataset = $this->option('dataset');

        return Http::baseUrl("https://{$project}.api.sanity.io/v2021-10-21")
            ->when($this->option('token'), fn ($http, $token) => $http->withToken($token))
            ->timeout(30)
            ->get("/data/query/{$dataset}", ['query' => $groq])
            ->throw()
            ->json('result', []);
    }

    /**
     * Copy a Sanity CDN image to the media disk and return its stored path.
     */
    private function download(?string $url, string $directory): ?string
    {
        if (blank($url)) {
            return null;
        }

        $path = $directory.'/'.basename(parse_url($url, PHP_URL_PATH));
        $disk = Storage::disk(Media::disk());

        if (! $disk->exists($path)) {
            $disk->put($path, Http::timeout(60)->get($url)->throw()->body(), 'public');
        }

        return $path;
    }
}

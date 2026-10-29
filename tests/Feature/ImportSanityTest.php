<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportSanityTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_content_and_images_and_can_run_twice(): void
    {
        Queue::fake();
        Storage::fake('public');

        Http::fake([
            'cdn.sanity.io/*' => Http::response('image-bytes'),
            'abc123.api.sanity.io/*' => function ($request) {
                $query = urldecode($request->url());

                $result = match (true) {
                    str_contains($query, '"works"') => [[
                        'title' => 'Shop App',
                        'description' => 'An online shop',
                        'projectLink' => 'https://shop.example',
                        'codeLink' => 'https://github.com/x/shop',
                        'tags' => ['REACT JS', 'MERN'],
                        '_createdAt' => '2023-01-10T10:00:00Z',
                        'image' => 'https://cdn.sanity.io/images/abc123/production/shop-800x600.png',
                    ]],
                    str_contains($query, '"experiences"') => [[
                        'year' => '2023',
                        'works' => [['name' => 'Developer', 'company' => 'Acme', 'desc' => 'Built things']],
                    ]],
                    str_contains($query, '"contact"') => [[
                        'name' => 'Jane',
                        'email' => 'jane@example.com',
                        'message' => 'Hello there',
                        '_createdAt' => '2023-05-01T08:00:00Z',
                    ]],
                    default => [],
                };

                return Http::response(['result' => $result]);
            },
        ]);

        $this->artisan('import:sanity', ['project' => 'abc123'])->assertSuccessful();
        $this->artisan('import:sanity', ['project' => 'abc123'])->assertSuccessful();

        $project = Project::with('tags')->sole();
        $this->assertSame('shop-app', $project->slug);
        $this->assertSame('projects/shop-800x600.png', $project->image);
        $this->assertEqualsCanonicalizing(['react-js', 'mern'], $project->tags->pluck('slug')->all());
        Storage::disk('public')->assertExists('projects/shop-800x600.png');

        $this->assertSame('Acme', Experience::sole()->company);

        $message = ContactMessage::sole();
        $this->assertNotNull($message->read_at);
        $this->assertSame('2023-05-01', $message->created_at->toDateString());
    }
}

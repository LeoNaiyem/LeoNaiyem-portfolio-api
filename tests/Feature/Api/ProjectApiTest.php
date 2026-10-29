<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    public function test_index_lists_published_projects_with_tags_but_without_body(): void
    {
        $tag = Tag::factory()->create(['name' => 'LARAVEL', 'slug' => 'laravel']);
        Project::factory()->create(['slug' => 'shop', 'body' => 'Long write-up'])->tags()->attach($tag);
        Project::factory()->draft()->create();
        Project::factory()->create(['published_at' => now()->addWeek()]);

        $this->getJson('/api/v1/projects')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'shop')
            ->assertJsonPath('data.0.tags.0.slug', 'laravel')
            ->assertJsonMissingPath('data.0.body');
    }

    public function test_index_can_be_filtered_by_tag(): void
    {
        $react = Tag::factory()->create(['slug' => 'react']);
        Project::factory()->create(['slug' => 'react-app'])->tags()->attach($react);
        Project::factory()->create(['slug' => 'other']);

        $this->getJson('/api/v1/projects?tag=react')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'react-app');
    }

    public function test_show_returns_a_published_project_with_body(): void
    {
        Project::factory()->create(['slug' => 'shop', 'body' => '## Hello']);

        $this->getJson('/api/v1/projects/shop')
            ->assertOk()
            ->assertJsonPath('data.slug', 'shop')
            ->assertJsonPath('data.body', '## Hello');
    }

    public function test_show_hides_drafts_and_unknown_slugs(): void
    {
        Project::factory()->draft()->create(['slug' => 'secret']);

        $this->getJson('/api/v1/projects/secret')->assertNotFound();
        $this->getJson('/api/v1/projects/missing')->assertNotFound();
    }
}

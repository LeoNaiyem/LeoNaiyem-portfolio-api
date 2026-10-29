<?php

namespace Tests\Feature\Api;

use App\Models\About;
use App\Models\Brand;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ContentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    public function test_settings_returns_the_profile_with_media_urls(): void
    {
        Setting::current()->update([
            'name' => 'Naiyem',
            'resume' => 'profile/resume.pdf',
            'socials' => [['platform' => 'github', 'url' => 'https://github.com/leonaiyem']],
        ]);

        $this->getJson('/api/v1/settings')
            ->assertOk()
            ->assertHeader('ETag')
            ->assertJsonPath('data.name', 'Naiyem')
            ->assertJsonPath('data.resume', url('storage/profile/resume.pdf'))
            ->assertJsonPath('data.socials.0.platform', 'github');
    }

    public function test_sections_are_returned_in_sort_order(): void
    {
        About::factory()->create(['title' => 'Second', 'sort_order' => 2]);
        About::factory()->create(['title' => 'First', 'sort_order' => 1]);
        Skill::factory()->count(2)->create();
        Testimonial::factory()->create();
        Brand::factory()->create(['image' => 'brands/logo.png']);

        $this->getJson('/api/v1/abouts')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'First')
            ->assertJsonPath('data.1.title', 'Second');

        $this->getJson('/api/v1/skills')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/v1/testimonials')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/brands')
            ->assertOk()
            ->assertJsonPath('data.0.image', url('storage/brands/logo.png'));
    }

    public function test_experiences_are_grouped_by_year_newest_first(): void
    {
        Experience::factory()->create(['year' => '2022', 'role' => 'Junior Developer']);
        Experience::factory()->create(['year' => '2024', 'role' => 'Senior Developer', 'sort_order' => 2]);
        Experience::factory()->create(['year' => '2024', 'role' => 'Tech Lead', 'sort_order' => 1]);

        $this->getJson('/api/v1/experiences')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.year', '2024')
            ->assertJsonPath('data.0.items.0.role', 'Tech Lead')
            ->assertJsonPath('data.0.items.1.role', 'Senior Developer')
            ->assertJsonPath('data.1.year', '2022');
    }

    public function test_tags_only_include_tags_used_by_published_projects(): void
    {
        $used = Tag::factory()->create();
        $draftOnly = Tag::factory()->create();
        Tag::factory()->create();

        Project::factory()->create()->tags()->attach($used);
        Project::factory()->draft()->create()->tags()->attach($draftOnly);

        $this->getJson('/api/v1/tags')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', $used->slug);
    }
}

<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public static function adminPages(): array
    {
        return [
            ['/admin'],
            ['/admin/projects'],
            ['/admin/projects/create'],
            ['/admin/tags'],
            ['/admin/abouts'],
            ['/admin/skills'],
            ['/admin/experiences'],
            ['/admin/testimonials'],
            ['/admin/brands'],
            ['/admin/contact-messages'],
            ['/admin/settings'],
        ];
    }

    #[DataProvider('adminPages')]
    public function test_admin_pages_load(string $uri): void
    {
        $this->actingAs(User::factory()->create())->get($uri)->assertOk();
    }

    public function test_project_edit_page_loads(): void
    {
        $project = Project::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get("/admin/projects/{$project->slug}/edit")
            ->assertOk()
            ->assertSee($project->title);
    }

    public function test_viewing_a_message_marks_it_as_read(): void
    {
        $message = ContactMessage::create(['name' => 'Jane', 'email' => 'jane@example.com', 'message' => 'Hello there']);

        $this->actingAs(User::factory()->create())
            ->get("/admin/contact-messages/{$message->getKey()}")
            ->assertOk()
            ->assertSee('Hello there');

        $this->assertNotNull($message->fresh()->read_at);
    }
}

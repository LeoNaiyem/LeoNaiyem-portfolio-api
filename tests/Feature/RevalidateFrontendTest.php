<?php

namespace Tests\Feature;

use App\Jobs\RevalidateFrontend;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RevalidateFrontendTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_and_deleting_content_queues_revalidation(): void
    {
        Queue::fake();

        $project = Project::factory()->create();
        Queue::assertPushed(RevalidateFrontend::class, fn ($job) => $job->tag === 'projects');

        Queue::fake();
        $project->delete();
        Queue::assertPushed(RevalidateFrontend::class, fn ($job) => $job->tag === 'projects');
    }

    public function test_changing_a_tag_also_revalidates_projects(): void
    {
        Queue::fake();

        Tag::factory()->create();

        Queue::assertPushed(RevalidateFrontend::class, fn ($job) => $job->tag === 'tags');
        Queue::assertPushed(RevalidateFrontend::class, fn ($job) => $job->tag === 'projects');
    }

    public function test_the_job_calls_the_frontend_with_the_secret(): void
    {
        config(['portfolio.frontend_url' => 'https://naiyem.dev', 'portfolio.revalidate_secret' => 's3cret']);
        Http::fake();

        (new RevalidateFrontend('projects'))->handle();

        Http::assertSent(fn (Request $request) => $request->url() === 'https://naiyem.dev/api/revalidate'
            && $request->header('x-revalidate-secret') === ['s3cret']
            && $request['tag'] === 'projects');
    }

    public function test_the_job_does_nothing_without_a_secret(): void
    {
        config(['portfolio.revalidate_secret' => null]);
        Http::fake();

        (new RevalidateFrontend('projects'))->handle();

        Http::assertNothingSent();
    }
}

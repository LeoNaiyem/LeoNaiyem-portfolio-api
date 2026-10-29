<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\DebounceFor;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Http;

/**
 * Calls the Next.js on-demand revalidation endpoint for a cache tag.
 *
 * Debounced so that saving several records in a row triggers one request.
 */
#[DebounceFor(5)]
#[Tries(3)]
#[Backoff(10)]
class RevalidateFrontend implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $tag)
    {
        //
    }

    public function debounceId(): string
    {
        return $this->tag;
    }

    public function handle(): void
    {
        $url = config('portfolio.frontend_url');
        $secret = config('portfolio.revalidate_secret');

        if (blank($url) || blank($secret)) {
            return;
        }

        Http::timeout(10)
            ->withHeader('x-revalidate-secret', $secret)
            ->post("{$url}/api/revalidate", ['tag' => $this->tag])
            ->throw();
    }
}

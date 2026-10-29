<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

/**
 * Verifies a Cloudflare Turnstile token. Skipped when no secret is configured
 * (local development and tests).
 */
class Turnstile implements ValidationRule
{
    public function __construct(private ?string $ip = null)
    {
        //
    }

    public static function enabled(): bool
    {
        return filled(config('portfolio.turnstile_secret'));
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! static::enabled()) {
            return;
        }

        $response = Http::asForm()
            ->timeout(10)
            ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('portfolio.turnstile_secret'),
                'response' => $value,
                'remoteip' => $this->ip,
            ]);

        if (! $response->successful() || ! $response->json('success')) {
            $fail('The captcha verification failed. Please try again.');
        }
    }
}

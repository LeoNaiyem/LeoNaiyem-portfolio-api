<?php

namespace Tests\Feature\Api;

use App\Notifications\ContactMessageReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'message' => 'I would like to hire you for a project.',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        config(['portfolio.admin_email' => 'owner@example.com', 'portfolio.turnstile_secret' => null]);
    }

    public function test_a_message_is_stored_and_the_owner_is_notified(): void
    {
        $this->postJson('/api/v1/contact', $this->payload)->assertCreated();

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'jane@example.com',
            'read_at' => null,
        ]);

        Notification::assertSentTo(
            new AnonymousNotifiable,
            ContactMessageReceived::class,
            fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'owner@example.com'
        );
    }

    public function test_invalid_input_is_rejected(): void
    {
        $this->postJson('/api/v1/contact', ['email' => 'not-an-email', 'message' => 'short'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_the_honeypot_blocks_bots(): void
    {
        $this->postJson('/api/v1/contact', [...$this->payload, 'website' => 'http://spam.example'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('website');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_the_turnstile_token_is_verified_when_configured(): void
    {
        config(['portfolio.turnstile_secret' => 'secret']);
        Http::fake(['challenges.cloudflare.com/*' => Http::sequence()
            ->push(['success' => false])
            ->push(['success' => true]),
        ]);

        $this->postJson('/api/v1/contact', [...$this->payload, 'turnstile_token' => 'bad'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('turnstile_token');

        $this->postJson('/api/v1/contact', [...$this->payload, 'turnstile_token' => 'good'])
            ->assertCreated();
    }

    public function test_submissions_are_rate_limited(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/v1/contact', $this->payload)->assertCreated();
        }

        $this->postJson('/api/v1/contact', $this->payload)->assertTooManyRequests();
    }
}

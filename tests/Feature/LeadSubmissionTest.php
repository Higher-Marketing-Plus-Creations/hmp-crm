<?php

namespace Tests\Feature;

use App\Jobs\SendLeadNotificationJob;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LeadSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_submission_requires_valid_api_key(): void
    {
        $response = $this->postJson('/api/leads/submit', [
            'website_name' => 'Demo Site',
            'page_url' => 'https://example.com/contact',
            'form_name' => 'Contact Form',
            'fields' => ['name' => 'John'],
        ], [
            'Origin' => 'https://example.com',
        ]);

        $response->assertUnauthorized();
    }

    public function test_lead_submission_creates_lead_and_dispatches_job(): void
    {
        Queue::fake();

        $client = Client::query()->create(['name' => 'Test Client']);
        $website = Website::query()->create([
            'client_id' => $client->id,
            'website_name' => 'Demo Site',
            'website_url' => 'https://example.com',
            'allowed_domains' => 'example.com',
            'api_key' => 'lead_test_key_123456',
            'notification_emails' => ['sales@example.com'],
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/leads/submit', [
            'website_name' => 'Demo Site',
            'page_url' => 'https://example.com/contact',
            'form_name' => 'Contact Form',
            'fields' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '123456789',
                'message' => 'Need quote',
            ],
        ], [
            'Origin' => 'https://example.com',
            'Referer' => 'https://example.com/contact',
            'X-API-KEY' => $website->api_key,
        ]);

        $response->assertCreated()->assertJson(['success' => true]);

        $this->assertDatabaseHas('leads', [
            'website_id' => $website->id,
            'visitor_email' => 'john@example.com',
            'email_status' => 'pending',
        ]);

        Queue::assertPushed(SendLeadNotificationJob::class);
        $this->assertSame(1, Lead::count());
    }
}

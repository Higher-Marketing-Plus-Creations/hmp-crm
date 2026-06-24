<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Lead;
use App\Models\User;
use App\Models\Website;
use App\Services\WebsiteMonitoring\SslCertificateInspector;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class ClientMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_workspace_shows_isolated_monitoring_metrics(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'is_admin' => true,
        ]);

        $client = Client::query()->create([
            'name' => 'Client One',
            'company_name' => 'Client One LLC',
        ]);

        $website = Website::query()->create([
            'client_id' => $client->id,
            'website_name' => 'Client One Site',
            'website_url' => 'https://client-one.test',
            'allowed_domains' => 'client-one.test',
            'api_key' => 'lead_client_one_key_12345',
            'notification_emails' => ['sales@client-one.test'],
            'status' => 'active',
        ]);

        Lead::query()->create([
            'website_id' => $website->id,
            'form_id' => null,
            'website_name' => 'Client One Site',
            'page_url' => 'https://client-one.test/contact',
            'form_name' => 'Contact Form',
            'visitor_name' => 'John Doe',
            'visitor_email' => 'john@example.com',
            'visitor_phone' => '123456789',
            'message' => 'Need quote',
            'form_data' => ['name' => 'John Doe'],
            'status' => 'new',
            'email_status' => 'sent',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        Lead::query()->create([
            'website_id' => $website->id,
            'form_id' => null,
            'website_name' => 'Client One Site',
            'page_url' => 'https://client-one.test/contact',
            'form_name' => 'Quote Form',
            'visitor_name' => 'Jane Doe',
            'visitor_email' => 'jane@example.com',
            'visitor_phone' => '987654321',
            'message' => 'Second lead',
            'form_data' => ['name' => 'Jane Doe'],
            'status' => 'new',
            'email_status' => 'failed',
            'created_at' => now()->subHours(6),
            'updated_at' => now()->subHours(6),
        ]);

        $this->actingAs($user)
            ->get(route('admin.clients.workspace', $client))
            ->assertOk()
            ->assertSee('Client One Site')
            ->assertSee('Forms This Month')
            ->assertSee('Failed Forms');
    }

    public function test_run_test_creates_a_monitoring_snapshot_for_the_correct_website(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'is_admin' => true,
        ]);

        $client = Client::query()->create([
            'name' => 'Client One',
            'company_name' => 'Client One LLC',
        ]);

        $website = Website::query()->create([
            'client_id' => $client->id,
            'website_name' => 'Client One Site',
            'website_url' => 'https://client-one.test',
            'allowed_domains' => 'client-one.test',
            'api_key' => 'lead_client_one_key_12345',
            'notification_emails' => ['sales@client-one.test'],
            'status' => 'active',
        ]);

        Lead::query()->create([
            'website_id' => $website->id,
            'form_id' => null,
            'website_name' => 'Client One Site',
            'page_url' => 'https://client-one.test/contact',
            'form_name' => 'Contact Form',
            'visitor_name' => 'John Doe',
            'visitor_email' => 'john@example.com',
            'visitor_phone' => '123456789',
            'message' => 'Need quote',
            'form_data' => ['name' => 'John Doe'],
            'status' => 'new',
            'email_status' => 'sent',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        Http::fake([
            'https://client-one.test' => Http::response('OK', 200),
        ]);

        $inspector = Mockery::mock(SslCertificateInspector::class);
        $inspector->shouldReceive('inspect')
            ->once()
            ->with('https://client-one.test')
            ->andReturn([
                'status' => 'valid',
                'summary' => 'SSL certificate expires on 30 Jun 2026 10:00',
                'expires_at' => now()->addDays(30),
            ]);

        $this->app->instance(SslCertificateInspector::class, $inspector);

        $this->actingAs($user)
            ->post(route('admin.websites.run-test', $website))
            ->assertRedirect();

        $this->assertDatabaseHas('website_monitor_checks', [
            'website_id' => $website->id,
            'website_status' => 'online',
            'email_delivery_status' => 'healthy',
            'forms_submitted_this_month' => 1,
            'failed_form_count' => 0,
            'ssl_status' => 'valid',
        ]);
    }

    public function test_run_test_retries_local_issuer_ssl_errors_without_marking_site_offline(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'is_admin' => true,
        ]);

        $client = Client::query()->create([
            'name' => 'Client Two',
            'company_name' => 'Client Two LLC',
        ]);

        $website = Website::query()->create([
            'client_id' => $client->id,
            'website_name' => 'SSL Retry Site',
            'website_url' => 'https://ssl-retry.test',
            'allowed_domains' => 'ssl-retry.test',
            'api_key' => 'lead_ssl_retry_key_12345',
            'notification_emails' => ['sales@ssl-retry.test'],
            'status' => 'active',
        ]);

        $attempts = 0;

        Http::fake([
            'https://ssl-retry.test' => function () use (&$attempts) {
                $attempts++;

                if ($attempts === 1) {
                    $guzzleException = new ConnectException(
                        'cURL error 60: SSL certificate problem: unable to get local issuer certificate',
                        new Request('GET', 'https://ssl-retry.test')
                    );

                    throw new ConnectionException($guzzleException->getMessage(), 0, $guzzleException);
                }

                return Http::response('OK', 200);
            },
        ]);

        $inspector = Mockery::mock(SslCertificateInspector::class);
        $inspector->shouldReceive('inspect')
            ->once()
            ->with('https://ssl-retry.test')
            ->andReturn([
                'status' => 'valid',
                'summary' => 'SSL certificate expires on 30 Jun 2026 10:00',
                'expires_at' => now()->addDays(30),
            ]);

        $this->app->instance(SslCertificateInspector::class, $inspector);

        $this->actingAs($user)
            ->post(route('admin.websites.run-test', $website))
            ->assertRedirect();

        $check = $website->monitorChecks()->latest('id')->first();

        $this->assertSame(2, $attempts);
        $this->assertNotNull($check);
        $this->assertSame('online', $check->website_status);
        $this->assertSame(200, $check->http_status_code);
        $this->assertNotContains(
            'Primary SSL verification failed on this monitoring machine, so the availability check retried without local CA validation.',
            $check->issues ?? []
        );
    }
}

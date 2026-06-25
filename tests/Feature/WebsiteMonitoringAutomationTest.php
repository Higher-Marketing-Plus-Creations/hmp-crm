<?php

namespace Tests\Feature;

use App\Jobs\SendWebsiteMonitoringAlertJob;
use App\Models\Client;
use App\Models\Website;
use App\Services\WebsiteMonitoring\SslCertificateInspector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class WebsiteMonitoringAutomationTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitor_command_creates_automated_snapshot_with_new_fields(): void
    {
        Queue::fake();

        $client = Client::query()->create(['name' => 'Client One']);
        $website = Website::query()->create([
            'client_id' => $client->id,
            'website_name' => 'Demo Site',
            'website_url' => 'https://demo-site.test',
            'allowed_domains' => 'demo-site.test',
            'api_key' => 'lead_demo_site_key_12345',
            'notification_emails' => ['alerts@demo-site.test'],
            'status' => 'active',
        ]);

        Http::fake([
            'https://demo-site.test' => Http::response('OK', 200),
        ]);

        $inspector = Mockery::mock(SslCertificateInspector::class);
        $inspector->shouldReceive('inspect')
            ->once()
            ->andReturn([
                'status' => 'valid',
                'summary' => 'SSL certificate expires on 30 Jul 2026 10:00',
                'expires_at' => now()->addDays(35),
                'days_left' => 35,
            ]);

        $this->app->instance(SslCertificateInspector::class, $inspector);

        $this->artisan('monitor:websites', ['--trigger' => 'scheduled'])
            ->expectsOutput('Running monitoring for 1 website(s).')
            ->expectsOutputToContain('website=online')
            ->expectsOutput('Website monitoring completed.')
            ->assertSuccessful();

        $check = $website->fresh()->monitorChecks()->latest('id')->first();

        $this->assertNotNull($check);
        $this->assertSame('scheduled', $check->run_test_status);
        $this->assertSame('online', $check->website_status);
        $this->assertSame('valid', $check->ssl_status);
        $this->assertSame(35, $check->ssl_days_left);
        $this->assertNotNull($check->last_checked_at);
        $this->assertNotNull($check->response_time_ms);

        Queue::assertNothingPushed();
    }

    public function test_duplicate_monitoring_alerts_are_not_queued_twice_for_same_open_issue(): void
    {
        Queue::fake();

        $client = Client::query()->create(['name' => 'Client Two']);
        $website = Website::query()->create([
            'client_id' => $client->id,
            'website_name' => 'Offline Site',
            'website_url' => 'https://offline-site.test',
            'allowed_domains' => 'offline-site.test',
            'api_key' => 'lead_offline_site_key_12345',
            'notification_emails' => ['alerts@offline-site.test'],
            'status' => 'active',
        ]);

        Http::fake([
            'https://offline-site.test' => Http::response('Down', 500),
        ]);

        $inspector = Mockery::mock(SslCertificateInspector::class);
        $inspector->shouldReceive('inspect')
            ->twice()
            ->andReturn([
                'status' => 'valid',
                'summary' => 'SSL certificate expires on 30 Jul 2026 10:00',
                'expires_at' => now()->addDays(35),
                'days_left' => 35,
            ]);

        $this->app->instance(SslCertificateInspector::class, $inspector);

        $this->artisan('monitor:websites', ['--website_id' => $website->id, '--trigger' => 'scheduled'])->assertSuccessful();
        $this->artisan('monitor:websites', ['--website_id' => $website->id, '--trigger' => 'scheduled'])->assertSuccessful();

        $this->assertDatabaseCount('website_monitor_alerts', 1);

        Queue::assertPushed(SendWebsiteMonitoringAlertJob::class, 1);
    }
}

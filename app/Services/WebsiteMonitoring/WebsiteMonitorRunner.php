<?php

namespace App\Services\WebsiteMonitoring;

use App\Models\Website;
use App\Models\WebsiteMonitorCheck;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebsiteMonitorRunner
{
    public function __construct(
        protected SslCertificateInspector $sslInspector
    ) {
    }

    public function run(Website $website): WebsiteMonitorCheck
    {
        $issues = [];
        $httpStatusCode = null;
        $websiteStatus = 'offline';
        $siteLoadTimeMs = null;

        $requestStartedAt = microtime(true);

        try {
            $response = Http::timeout(15)->get($website->website_url);
            $siteLoadTimeMs = (int) round((microtime(true) - $requestStartedAt) * 1000);
            $httpStatusCode = $response->status();
            $websiteStatus = 'online';

            if ($response->serverError()) {
                $websiteStatus = 'offline';
                $issues[] = 'Website returned a server error (' . $httpStatusCode . ').';
            } elseif ($response->clientError()) {
                $issues[] = 'Website returned a client error (' . $httpStatusCode . ').';
            }
        } catch (\Throwable $exception) {
            $siteLoadTimeMs = (int) round((microtime(true) - $requestStartedAt) * 1000);
            $issues[] = 'Website check failed: ' . Str::limit($exception->getMessage(), 160, '');
        }

        $ssl = $this->sslInspector->inspect($website->website_url);

        if ($ssl['status'] !== 'valid') {
            $issues[] = $ssl['summary'];
        }

        $formsSubmittedThisMonth = $website->leads()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $lastSuccessfulLead = $website->leads()
            ->where('email_status', 'sent')
            ->latest('created_at')
            ->first();

        $failedFormCount = $website->leads()
            ->where('email_status', 'failed')
            ->count();

        $recentEmailSentCount = $website->leads()
            ->where('email_status', 'sent')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $recentEmailFailedCount = $website->leads()
            ->where('email_status', 'failed')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $emailDeliveryStatus = $this->resolveEmailDeliveryStatus(
            $website,
            $recentEmailSentCount,
            $recentEmailFailedCount
        );

        if ($emailDeliveryStatus === 'not_configured') {
            $issues[] = 'Notification email addresses are not configured for this website.';
        }

        if ($failedFormCount > 0) {
            $issues[] = 'Failed form submissions recorded: ' . $failedFormCount . '.';
        }

        if ($formsSubmittedThisMonth === 0) {
            $issues[] = 'No forms submitted this month yet.';
        }

        $uptimePercentage = $this->resolveUptimePercentage($website, $websiteStatus);

        return $website->monitorChecks()->create([
            'website_status' => $websiteStatus,
            'email_delivery_status' => $emailDeliveryStatus,
            'forms_submitted_this_month' => $formsSubmittedThisMonth,
            'last_successful_form_submitted_at' => $lastSuccessfulLead?->created_at,
            'failed_form_count' => $failedFormCount,
            'site_load_time_ms' => $siteLoadTimeMs,
            'issues' => array_values(array_unique(array_filter($issues))),
            'run_test_status' => 'completed',
            'ssl_status' => $ssl['status'],
            'uptime_percentage' => $uptimePercentage,
            'http_status_code' => $httpStatusCode,
            'check_summary' => $this->buildSummary($websiteStatus, $emailDeliveryStatus, $ssl['status']),
            'tested_at' => now(),
        ]);
    }

    protected function resolveEmailDeliveryStatus(Website $website, int $recentEmailSentCount, int $recentEmailFailedCount): string
    {
        if ($website->recipientList() === []) {
            return 'not_configured';
        }

        if ($recentEmailFailedCount > 0 && $recentEmailSentCount === 0) {
            return 'failing';
        }

        if ($recentEmailFailedCount > 0) {
            return 'warning';
        }

        if ($recentEmailSentCount > 0) {
            return 'healthy';
        }

        return 'pending';
    }

    protected function resolveUptimePercentage(Website $website, string $currentWebsiteStatus): ?float
    {
        $recentChecks = $website->monitorChecks()
            ->where('tested_at', '>=', now()->subDays(30))
            ->get(['website_status']);

        $totalChecks = $recentChecks->count() + 1;

        if ($totalChecks === 0) {
            return null;
        }

        $onlineChecks = $recentChecks->where('website_status', 'online')->count();

        if ($currentWebsiteStatus === 'online') {
            $onlineChecks++;
        }

        return round(($onlineChecks / $totalChecks) * 100, 2);
    }

    protected function buildSummary(string $websiteStatus, string $emailDeliveryStatus, string $sslStatus): string
    {
        return sprintf(
            'Website is %s, email delivery is %s, and SSL status is %s.',
            str_replace('_', ' ', $websiteStatus),
            str_replace('_', ' ', $emailDeliveryStatus),
            str_replace('_', ' ', $sslStatus)
        );
    }
}

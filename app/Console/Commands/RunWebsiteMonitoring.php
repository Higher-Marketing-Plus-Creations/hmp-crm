<?php

namespace App\Console\Commands;

use App\Models\Website;
use App\Services\WebsiteMonitoring\WebsiteMonitorRunner;
use App\Services\WebsiteMonitoring\WebsiteMonitoringReportMailer;
use Illuminate\Console\Command;

class RunWebsiteMonitoring extends Command
{
    protected $signature = 'monitor:websites {--website_id=} {--trigger=scheduled}';

    protected $description = 'Run automatic website uptime and SSL monitoring checks.';

    public function handle(WebsiteMonitorRunner $runner, WebsiteMonitoringReportMailer $reportMailer): int
    {
        $query = Website::query()->where('status', 'active');

        if ($websiteId = $this->option('website_id')) {
            $query->whereKey($websiteId);
        }

        $websites = $query->orderBy('id')->get();

        if ($websites->isEmpty()) {
            $this->info('No active websites found for monitoring.');

            return self::SUCCESS;
        }

        $this->info('Running monitoring for ' . $websites->count() . ' website(s).');

        foreach ($websites as $website) {
            $check = $runner->run($website, (string) $this->option('trigger'));
            $reportMailer->send($website, $check);

            $this->line(sprintf(
                '[%d] %s | website=%s | ssl=%s | issues=%d',
                $website->id,
                $website->website_name,
                $check->website_status,
                $check->ssl_status,
                count($check->issues ?? [])
            ));
        }

        $this->info('Website monitoring completed.');

        return self::SUCCESS;
    }
}

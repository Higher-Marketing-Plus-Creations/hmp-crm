<?php

namespace App\Services\WebsiteMonitoring;

use App\Jobs\SendWebsiteMonitoringAlertJob;
use App\Models\Website;
use App\Models\WebsiteMonitorCheck;

class WebsiteMonitoringAlertManager
{
    public function process(Website $website, WebsiteMonitorCheck $check): void
    {
        $this->processAlertType(
            website: $website,
            check: $check,
            type: 'uptime',
            stateKey: $check->website_status === 'online' ? null : 'website_' . $check->website_status,
            stateLabel: $check->website_status === 'online'
                ? null
                : 'Website is currently ' . str_replace('_', ' ', $check->website_status) . '.'
        );

        $this->processAlertType(
            website: $website,
            check: $check,
            type: 'ssl',
            stateKey: in_array($check->ssl_status, ['valid'], true) ? null : 'ssl_' . $check->ssl_status,
            stateLabel: in_array($check->ssl_status, ['valid'], true)
                ? null
                : 'SSL status is ' . str_replace('_', ' ', $check->ssl_status) . '.'
        );
    }

    protected function processAlertType(
        Website $website,
        WebsiteMonitorCheck $check,
        string $type,
        ?string $stateKey,
        ?string $stateLabel
    ): void {
        $activeAlerts = $website->monitorAlerts()
            ->where('type', $type)
            ->whereNull('resolved_at')
            ->get();

        if ($stateKey === null) {
            foreach ($activeAlerts as $alert) {
                $alert->update([
                    'resolved_at' => now(),
                    'last_seen_at' => now(),
                ]);
            }

            return;
        }

        $existing = $activeAlerts->firstWhere('state_key', $stateKey);

        if ($existing) {
            $existing->update([
                'website_monitor_check_id' => $check->id,
                'last_seen_at' => now(),
            ]);

            return;
        }

        foreach ($activeAlerts as $alert) {
            $alert->update([
                'resolved_at' => now(),
                'last_seen_at' => now(),
            ]);
        }

        $alert = $website->monitorAlerts()->create([
            'website_monitor_check_id' => $check->id,
            'type' => $type,
            'state_key' => $stateKey,
            'state_label' => $stateLabel,
            'recipients' => $website->recipientList(),
            'send_status' => $website->recipientList() === [] ? 'skipped' : 'pending',
            'last_seen_at' => now(),
        ]);

        if ($website->recipientList() !== []) {
            SendWebsiteMonitoringAlertJob::dispatch($alert);
        }
    }
}

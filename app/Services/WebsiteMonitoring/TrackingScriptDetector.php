<?php

namespace App\Services\WebsiteMonitoring;

class TrackingScriptDetector
{
    public function detect(?string $html): array
    {
        $html = (string) $html;
        $normalizedHtml = strtolower($html);

        $googleAnalyticsMatches = $this->matchedLabels($html, $normalizedHtml, [
            '/googletagmanager\.com\/gtag\/js/i' => 'gtag.js loader',
            '/\bgtag\s*\(/i' => 'gtag() call',
            '/\bG-[A-Z0-9]{4,}\b/' => 'GA4 measurement ID',
            '/google-analytics\.com\/analytics\.js/i' => 'analytics.js loader',
            '/google-analytics\.com\/ga\.js/i' => 'ga.js loader',
            '/\bUA-\d{4,}-\d+\b/i' => 'Universal Analytics ID',
            '/\bga\s*\(\s*[\'\"]create[\'\"]/i' => 'ga() create call',
        ]);

        $googleTagManagerMatches = $this->matchedLabels($html, $normalizedHtml, [
            '/googletagmanager\.com\/gtm\.js/i' => 'gtm.js loader',
            '/\bGTM-[A-Z0-9]+\b/i' => 'GTM container ID',
            '/gtm\.start/i' => 'gtm.start bootstrap',
            '/googletagmanager\.com\/ns\.html/i' => 'GTM noscript iframe',
        ]);

        $googleSearchConsoleMatches = $this->matchedLabels($html, $normalizedHtml, [
            '/<meta[^>]+name=[\'\"]google-site-verification[\'\"][^>]*>/i' => 'google-site-verification meta tag',
            '/google-site-verification/i' => 'Google site verification marker',
        ]);

        $microsoftTrackingMatches = $this->matchedLabels($html, $normalizedHtml, [
            '/clarity\.ms\/tag/i' => 'Microsoft Clarity tag',
            '/\bclarity\s*\(/i' => 'clarity() call',
            '/bat\.bing\.com\/bat\.js/i' => 'Bing UET script',
            '/\buetq\b/i' => 'UET queue variable',
            '/\bmsvalidate\.01\b/i' => 'Bing Webmaster verification meta',
            '/bing-site-verification/i' => 'Bing verification marker',
        ]);

        return [
            'google_analytics_detected' => $googleAnalyticsMatches !== [],
            'google_tag_manager_detected' => $googleTagManagerMatches !== [],
            'google_search_console_detected' => $googleSearchConsoleMatches !== [],
            'microsoft_tracking_detected' => $microsoftTrackingMatches !== [],
            'tracking_detection_details' => [
                'google_analytics' => $googleAnalyticsMatches,
                'google_tag_manager' => $googleTagManagerMatches,
                'google_search_console' => $googleSearchConsoleMatches,
                'microsoft_tracking' => $microsoftTrackingMatches,
            ],
        ];
    }

    protected function matchedLabels(string $html, string $normalizedHtml, array $patterns): array
    {
        $matches = [];

        foreach ($patterns as $pattern => $label) {
            if (preg_match($pattern, $html) === 1 || preg_match($pattern, $normalizedHtml) === 1) {
                $matches[] = $label;
            }
        }

        return array_values(array_unique($matches));
    }
}

<?php

namespace App\Services\WebsiteMonitoring;

use Carbon\CarbonImmutable;

class SslCertificateInspector
{
    public function inspect(string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST);
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if (! $host) {
            return [
                'status' => 'unknown',
                'summary' => 'Website host is missing from the configured URL.',
                'expires_at' => null,
            ];
        }

        if ($scheme !== 'https') {
            return [
                'status' => 'not_secure',
                'summary' => 'Website is not using HTTPS.',
                'expires_at' => null,
            ];
        }

        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $client = @stream_socket_client(
            'ssl://' . $host . ':443',
            $errorNumber,
            $errorMessage,
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (! $client) {
            return [
                'status' => 'unknown',
                'summary' => 'Unable to read the SSL certificate: ' . trim($errorMessage ?: ('Error ' . $errorNumber)),
                'expires_at' => null,
            ];
        }

        $params = stream_context_get_params($client);
        $certificate = $params['options']['ssl']['peer_certificate'] ?? null;

        if (! $certificate) {
            return [
                'status' => 'unknown',
                'summary' => 'SSL certificate details were not returned by the website.',
                'expires_at' => null,
            ];
        }

        $parsedCertificate = @openssl_x509_parse($certificate);
        $validTo = $parsedCertificate['validTo_time_t'] ?? null;

        if (! $validTo) {
            return [
                'status' => 'unknown',
                'summary' => 'SSL certificate expiry could not be determined.',
                'expires_at' => null,
            ];
        }

        $expiresAt = CarbonImmutable::createFromTimestampUTC((int) $validTo);

        if ($expiresAt->isPast()) {
            $status = 'expired';
        } elseif ($expiresAt->lte(now()->addDays(14))) {
            $status = 'expiring_soon';
        } else {
            $status = 'valid';
        }

        return [
            'status' => $status,
            'summary' => 'SSL certificate expires on ' . $expiresAt->setTimezone(config('app.timezone'))->format('d M Y H:i'),
            'expires_at' => $expiresAt,
        ];
    }
}

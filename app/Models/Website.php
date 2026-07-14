<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Website extends Model
{
    protected $fillable = [
        'client_id',
        'website_name',
        'website_url',
        'allowed_domains',
        'api_key',
        'notification_emails',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'notification_emails' => 'array',
        ];
    }

    protected function allowedDomains(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value,
            set: function (string|array|null $value) {
                if (is_array($value)) {
                    $value = implode(PHP_EOL, array_filter(array_map('trim', $value)));
                }

                return $value;
            }
        );
    }

    public static function generateApiKey(): string
    {
        return 'lead_' . Str::random(40);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function forms(): HasMany
    {
        return $this->hasMany(Form::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function monitorChecks(): HasMany
    {
        return $this->hasMany(WebsiteMonitorCheck::class);
    }

    public function monitorAlerts(): HasMany
    {
        return $this->hasMany(WebsiteMonitorAlert::class);
    }

    public function latestMonitorCheck(): HasOne
    {
        return $this->hasOne(WebsiteMonitorCheck::class)->latestOfMany('tested_at');
    }

    public function domainList(): array
    {
        $domains = preg_split('/[\r\n,]+/', (string) $this->getRawOriginal('allowed_domains')) ?: [];

        $normalized = array_filter(array_map(function (string $domain): ?string {
            $domain = trim(Str::lower($domain));

            if ($domain === '') {
                return null;
            }

            return preg_replace('#^https?://#', '', $domain);
        }, $domains));

        if ($normalized === []) {
            $host = parse_url($this->website_url, PHP_URL_HOST);

            return $host ? [Str::lower($host)] : [];
        }

        return array_values(array_unique($normalized));
    }

    public function matchesDomain(?string $domain): bool
    {
        if (! $domain) {
            return false;
        }

        $domain = Str::lower($domain);

        foreach ($this->domainList() as $allowedDomain) {
            if ($domain === $allowedDomain || Str::endsWith($domain, '.' . $allowedDomain)) {
                return true;
            }
        }

        return false;
    }

    public function recipientList(): array
    {
        $recipients = array_values(array_filter(array_map(
            fn (mixed $email) => filter_var(trim((string) $email), FILTER_VALIDATE_EMAIL) ?: null,
            $this->notification_emails ?? []
        )));

        $clientEmail = $this->client?->email;

        if (is_string($clientEmail) && filter_var(trim($clientEmail), FILTER_VALIDATE_EMAIL)) {
            $recipients[] = trim($clientEmail);
        }

        $adminEmail = (string) config('mail.from.address', env('ADMIN_EMAIL'));

        if ($adminEmail !== '' && filter_var(trim($adminEmail), FILTER_VALIDATE_EMAIL)) {
            $recipients[] = trim($adminEmail);
        }

        return array_values(array_unique($recipients));
    }
}

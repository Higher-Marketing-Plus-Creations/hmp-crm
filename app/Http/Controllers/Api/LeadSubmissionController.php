<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitLeadRequest;
use App\Jobs\SendLeadNotificationJob;
use App\Models\Form as LeadForm;
use App\Models\Lead;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LeadSubmissionController extends Controller
{
    public function preflight(): JsonResponse
    {
        return response()->json(['message' => 'OK']);
    }

    public function store(SubmitLeadRequest $request): JsonResponse
    {
        /** @var Website $website */
        $website = $request->attributes->get('website');
        $payload = $request->validated();
        $fields = $this->sanitizeFields($payload['fields']);
        $isSpam = $this->isSpam($payload, $fields);

        if (! $this->passesRecaptcha($request)) {
            return response()->json([
                'message' => 'Unable to process the request right now.',
            ], 422);
        }

        $form = LeadForm::query()->firstOrCreate(
            [
                'website_id' => $website->id,
                'form_identifier' => $payload['form_identifier'] ?? null,
                'form_name' => $payload['form_name'],
                'page_url' => $payload['page_url'],
            ],
            [
                'status' => 'active',
            ]
        );

        $lead = Lead::query()->create([
            'website_id' => $website->id,
            'form_id' => $form->id,
            'website_name' => $payload['website_name'],
            'page_url' => $payload['page_url'],
            'form_name' => $payload['form_name'],
            'visitor_name' => $this->extractField($fields, ['name', 'full_name', 'fullname']),
            'visitor_email' => $this->extractField($fields, ['email', 'email_address']),
            'visitor_phone' => $this->extractField($fields, ['phone', 'mobile', 'telephone']),
            'message' => $this->extractField($fields, ['message', 'comment', 'comments', 'notes', 'inquiry']),
            'form_data' => $fields,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 65535, ''),
            'referrer' => $request->headers->get('referer'),
            'status' => $isSpam ? 'spam' : ($payload['status'] ?? 'new'),
            'email_status' => $isSpam ? 'failed' : 'pending',
        ]);

        if (! $isSpam) {
            SendLeadNotificationJob::dispatch($lead);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lead submitted successfully.',
            'lead_id' => $lead->id,
        ], 201);
    }

    protected function sanitizeFields(array $fields): array
    {
        $clean = [];

        foreach ($fields as $key => $value) {
            $normalizedKey = Str::slug((string) $key, '_');

            if (is_array($value)) {
                $clean[$normalizedKey] = array_values(array_filter(array_map(function ($item) {
                    return is_scalar($item) ? trim(strip_tags((string) $item)) : null;
                }, $value)));

                continue;
            }

            $clean[$normalizedKey] = is_scalar($value)
                ? trim(strip_tags((string) $value))
                : null;
        }

        return Arr::where($clean, fn ($value) => $value !== null && $value !== '');
    }

    protected function extractField(array $fields, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (! empty($fields[$key])) {
                return is_array($fields[$key])
                    ? implode(', ', array_filter($fields[$key]))
                    : (string) $fields[$key];
            }
        }

        return null;
    }

    protected function isSpam(array $payload, array $fields): bool
    {
        if (! empty($payload['honeypot'])) {
            return true;
        }

        if (collect($fields)->flatten()->contains(fn ($value) => is_string($value) && Str::length($value) > 5000)) {
            return true;
        }

        return false;
    }

    protected function passesRecaptcha(Request $request): bool
    {
        if (! config('services.recaptcha.enabled')) {
            return true;
        }

        $token = (string) $request->input('recaptcha_token');

        if ($token === '') {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret'),
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            return (bool) data_get($response->json(), 'success', false);
        } catch (\Throwable) {
            return false;
        }
    }
}

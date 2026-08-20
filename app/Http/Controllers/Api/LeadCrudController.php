<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConversationLead;
use App\Models\Conversation;
use App\Models\ConversationSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadCrudController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Leads fetched successfully.',
            'data' => ConversationLead::latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatePayload($request, false);
        $lead = ConversationLead::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully.',
            'data' => $lead,
        ], 201);
    }

    public function show(ConversationLead $lead): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Lead fetched successfully.',
            'data' => $lead,
        ]);
    }

    public function update(Request $request, ConversationLead $lead): JsonResponse
    {
        $data = $this->validatePayload($request, true);
        $lead->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully.',
            'data' => $lead->fresh(),
        ]);
    }

    public function destroy(ConversationLead $lead): JsonResponse
    {
        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully.',
            'data' => null,
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'string'],
            'session_id' => ['required', 'string'],
            'intent' => ['nullable', 'string'],
            'source_page' => ['nullable', 'string'],
            'full_name' => ['nullable', 'string'],
            'email' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'business_name' => ['nullable', 'string'],
            'website_url' => ['nullable', 'string'],
            'service_interest' => ['nullable', 'string'],
            'custom_data' => ['nullable', 'array'],
        ]);

        $result = DB::transaction(function () use ($data) {
            $lead = ConversationLead::query()
                ->where('client_id', $data['client_id'])
                ->where('session_id', $data['session_id'])
                ->first();

            $action = $lead ? 'updated' : 'created';

            $payload = array_filter($data, static fn ($value) => $value !== null);

            if ($lead) {
                $payload['custom_data'] = $this->mergeCustomData($lead->custom_data ?? [], $data['custom_data'] ?? []);
                $lead->fill($payload)->save();
                $lead = $lead->fresh();
            } else {
                $payload['custom_data'] = $data['custom_data'] ?? [];
                $lead = ConversationLead::create($payload);
            }

            $sessionLinked = false;
            $sessionLinked = ConversationSession::query()
                ->where('client_id', $data['client_id'])
                ->where('session_id', $data['session_id'])
                ->update([
                    'lead_id' => $lead->id,
                ]);

            $conversationsUpdated = DB::table('conversations')
                ->where('client_id', $data['client_id'])
                ->where('session_id', $data['session_id'])
                ->update([
                    'lead_id' => $lead->id,
                ]);

            return [
                'action' => $action,
                'lead' => $lead->fresh(),
                'session_linked' => (bool) $sessionLinked,
                'conversations_updated' => $conversationsUpdated,
            ];
        });

        return response()->json([
            'success' => true,
            'action' => $result['action'],
            'lead' => $result['lead'],
            'session_linked' => $result['session_linked'],
            'conversations_updated' => $result['conversations_updated'],
        ], $result['action'] === 'created' ? 201 : 200);
    }

    public function qualify(Request $request, ConversationLead $lead): JsonResponse
    {
        $lead->update([
            'is_qualified' => true,
            'qualified_at' => $lead->qualified_at ?? now(),
        ]);

        return response()->json([
            'lead_id' => $lead->id,
            'is_qualified' => true,
            'qualified_at' => optional($lead->fresh()->qualified_at)?->toISOString(),
            'should_send_notification' => ! (bool) $lead->notification_sent,
        ]);
    }

    public function notificationSent(Request $request, ConversationLead $lead): JsonResponse
    {
        $lead->update([
            'notification_sent' => true,
            'notification_sent_at' => $lead->notification_sent_at ?? now(),
        ]);

        return response()->json([
            'lead_id' => $lead->id,
            'notification_sent' => true,
            'notification_sent_at' => optional($lead->fresh()->notification_sent_at)?->toISOString(),
        ]);
    }

    private function validatePayload(Request $request, bool $partial): array
    {
        $rules = [
            'session_id' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'full_name' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'email' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'phone' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'intent' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'source_page' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'notes' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'client_id' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'business_name' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'website_url' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'service_interest' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'custom_data' => [$partial ? 'sometimes' : 'nullable', 'array'],
            'emailed_at' => [$partial ? 'sometimes' : 'nullable'],
            'is_qualified' => [$partial ? 'sometimes' : 'nullable', 'boolean'],
            'qualified_at' => [$partial ? 'sometimes' : 'nullable'],
            'notification_sent' => [$partial ? 'sometimes' : 'nullable', 'boolean'],
            'notification_sent_at' => [$partial ? 'sometimes' : 'nullable'],
            'status' => [$partial ? 'sometimes' : 'nullable', 'string'],
        ];

        return $request->validate($rules);
    }

    private function mergeCustomData(array $existing, array $incoming): array
    {
        foreach ($incoming as $key => $value) {
            if (is_array($value) && array_key_exists($key, $existing) && is_array($existing[$key])) {
                $existing[$key] = $this->mergeCustomData($existing[$key], $value);
                continue;
            }

            $existing[$key] = $value;
        }

        return $existing;
    }
}

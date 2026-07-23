<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientConversationSetting;
use App\Models\ClientSetting;
use App\Models\Conversation;
use App\Models\ConversationLead;
use App\Models\ConversationSession;
use App\Models\ErrorLog;
use App\Models\KnowledgeBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkspaceApiController extends Controller
{
    public function docs(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Workspace API map fetched successfully.',
            'data' => [
                'overview' => [
                    'method' => 'GET',
                    'path' => '/api/workspaces/{client_id}/overview',
                    'tables' => [
                        'client_settings',
                        'client_conversation_settings',
                        'conversation_sessions',
                        'knowledge_base',
                        'conversation_leads',
                        'conversations',
                        'error_logs',
                    ],
                    'purpose' => 'One-shot workspace summary for a client.',
                ],
                'settings' => [
                    'method' => 'GET',
                    'path' => '/api/workspaces/{client_id}/settings',
                    'tables' => ['client_settings'],
                    'purpose' => 'Business level settings for one client.',
                ],
                'conversation_settings' => [
                    'method' => 'GET',
                    'path' => '/api/workspaces/{client_id}/conversation-settings',
                    'tables' => ['client_conversation_settings'],
                    'purpose' => 'AI conversation rules and behavior settings.',
                ],
                'knowledge_base' => [
                    'method' => 'GET',
                    'path' => '/api/workspaces/{client_id}/knowledge-base',
                    'tables' => ['knowledge_base'],
                    'purpose' => 'Client knowledge articles and references.',
                ],
                'sessions' => [
                    'method' => 'GET',
                    'path' => '/api/workspaces/{client_id}/sessions',
                    'tables' => ['conversation_sessions'],
                    'purpose' => 'Conversation session records.',
                ],
                'conversations' => [
                    'method' => 'GET',
                    'path' => '/api/workspaces/{client_id}/conversations',
                    'tables' => ['conversations'],
                    'purpose' => 'Message log tied to sessions/leads.',
                ],
                'leads' => [
                    'method' => 'GET',
                    'path' => '/api/workspaces/{client_id}/leads',
                    'tables' => ['conversation_leads'],
                    'purpose' => 'Qualified or collected leads for a client.',
                ],
                'error_logs' => [
                    'method' => 'GET',
                    'path' => '/api/workspaces/{client_id}/error-logs',
                    'tables' => ['error_logs'],
                    'purpose' => 'Errors and workflow failures for a client.',
                ],
                'bootstrap' => [
                    'method' => 'POST',
                    'path' => '/api/workspaces/{client_id}/bootstrap',
                    'tables' => [
                        'client_settings',
                        'client_conversation_settings',
                        'knowledge_base',
                    ],
                    'purpose' => 'Create the main client workspace setup in one request.',
                ],
                'conversation_start' => [
                    'method' => 'POST',
                    'path' => '/api/workspaces/{client_id}/conversation-start',
                    'tables' => [
                        'conversation_sessions',
                        'conversations',
                    ],
                    'purpose' => 'Create a new session and optional first conversation message.',
                ],
                'conversation_event' => [
                    'method' => 'POST',
                    'path' => '/api/workspaces/{client_id}/conversation-event',
                    'tables' => [
                        'conversations',
                        'conversation_leads',
                        'error_logs',
                    ],
                    'purpose' => 'Store a conversation message and optional lead or error log in one request.',
                ],
            ],
        ]);
    }

    public function overview(string $clientId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Workspace overview fetched successfully.',
            'data' => [
                'client_id' => $clientId,
                'tables' => [
                    'client_settings' => ClientSetting::query()->where('client_id', $clientId)->first(),
                    'client_conversation_settings' => ClientConversationSetting::query()->where('client_id', $clientId)->first(),
                    'knowledge_base_count' => KnowledgeBase::query()->where('client_id', $clientId)->count(),
                    'sessions_count' => ConversationSession::query()->where('client_id', $clientId)->count(),
                    'conversations_count' => Conversation::query()->where('client_id', $clientId)->count(),
                    'leads_count' => ConversationLead::query()->where('client_id', $clientId)->count(),
                    'error_logs_count' => ErrorLog::query()->where('client_id', $clientId)->count(),
                ],
            ],
        ]);
    }

    public function settings(string $clientId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Client settings fetched successfully.',
            'data' => ClientSetting::query()->where('client_id', $clientId)->first(),
        ]);
    }

    public function conversationSettings(string $clientId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Conversation settings fetched successfully.',
            'data' => ClientConversationSetting::query()->where('client_id', $clientId)->first(),
        ]);
    }

    public function knowledgeBase(string $clientId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Knowledge base entries fetched successfully.',
            'data' => KnowledgeBase::query()->where('client_id', $clientId)->orderBy('sort_order')->get(),
        ]);
    }

    public function sessions(string $clientId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Sessions fetched successfully.',
            'data' => ConversationSession::query()->where('client_id', $clientId)->latest('id')->get(),
        ]);
    }

    public function conversations(string $clientId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Conversations fetched successfully.',
            'data' => Conversation::query()->where('client_id', $clientId)->latest('id')->get(),
        ]);
    }

    public function leads(string $clientId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Leads fetched successfully.',
            'data' => ConversationLead::query()->where('client_id', $clientId)->latest('id')->get(),
        ]);
    }

    public function errorLogs(string $clientId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Error logs fetched successfully.',
            'data' => ErrorLog::query()->where('client_id', $clientId)->latest('id')->get(),
        ]);
    }

    public function bootstrap(Request $request, string $clientId): JsonResponse
    {
        $data = $request->validate([
            'client_settings' => ['nullable', 'array'],
            'client_conversation_settings' => ['nullable', 'array'],
            'knowledge_base' => ['nullable', 'array'],
            'knowledge_base.*.section_title' => ['nullable', 'string'],
            'knowledge_base.*.section_type' => ['nullable', 'string'],
            'knowledge_base.*.content' => ['nullable', 'string'],
            'knowledge_base.*.is_active' => ['nullable', 'boolean'],
            'knowledge_base.*.sort_order' => ['nullable', 'integer'],
        ]);

        $result = DB::transaction(function () use ($data, $clientId) {
            $payload = [
                'client_id' => $clientId,
            ];

            $clientSettings = null;
            if (!empty($data['client_settings'])) {
                $clientSettings = ClientSetting::updateOrCreate(
                    ['client_id' => $clientId],
                    array_merge($payload, $data['client_settings'])
                );
            }

            $conversationSettings = null;
            if (!empty($data['client_conversation_settings'])) {
                $conversationSettings = ClientConversationSetting::updateOrCreate(
                    ['client_id' => $clientId],
                    array_merge($payload, $data['client_conversation_settings'])
                );
            }

            $knowledgeBaseItems = [];
            if (!empty($data['knowledge_base']) && is_array($data['knowledge_base'])) {
                foreach ($data['knowledge_base'] as $item) {
                    $knowledgeBaseItems[] = KnowledgeBase::create(array_merge($payload, $item));
                }
            }

            return [
                'client_settings' => $clientSettings,
                'client_conversation_settings' => $conversationSettings,
                'knowledge_base' => $knowledgeBaseItems,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Workspace bootstrap saved successfully.',
            'data' => $result,
        ], 201);
    }

    public function conversationStart(Request $request, string $clientId): JsonResponse
    {
        $data = $request->validate([
            'session' => ['nullable', 'array'],
            'session.session_id' => ['required', 'string'],
            'session.current_url' => ['nullable', 'string'],
            'session.page_title' => ['nullable', 'string'],
            'session.page_summary' => ['nullable', 'string'],
            'session.intent' => ['nullable', 'string'],
            'session.lead_id' => ['nullable', 'integer'],
            'session.is_active' => ['nullable', 'boolean'],
            'session.last_activity_at' => ['nullable'],
            'session.selected_intent' => ['nullable', 'string'],
            'session.last_event_type' => ['nullable', 'string'],
            'conversation' => ['nullable', 'array'],
            'conversation.role' => ['nullable', 'string'],
            'conversation.message' => ['nullable', 'string'],
            'conversation.intent' => ['nullable', 'string'],
            'conversation.page_url' => ['nullable', 'string'],
            'conversation.page_title' => ['nullable', 'string'],
            'conversation.collecting_field' => ['nullable', 'string'],
            'conversation.selected_intent' => ['nullable', 'string'],
            'conversation.event_type' => ['nullable', 'string'],
        ]);

        $result = DB::transaction(function () use ($data, $clientId) {
            $sessionData = array_merge(
                ['client_id' => $clientId],
                $data['session'] ?? []
            );

            $session = ConversationSession::create($sessionData);

            $conversation = null;
            if (!empty($data['conversation'])) {
                $conversation = Conversation::create(array_merge([
                    'client_id' => $clientId,
                    'session_id' => $session->session_id,
                ], $data['conversation']));
            }

            return [
                'session' => $session,
                'conversation' => $conversation,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Conversation session created successfully.',
            'data' => $result,
        ], 201);
    }

    public function conversationEvent(Request $request, string $clientId): JsonResponse
    {
        $data = $request->validate([
            'conversation' => ['required', 'array'],
            'conversation.session_id' => ['required', 'string'],
            'conversation.lead_id' => ['nullable', 'integer'],
            'conversation.role' => ['required', 'string'],
            'conversation.message' => ['required', 'string'],
            'conversation.intent' => ['nullable', 'string'],
            'conversation.page_url' => ['nullable', 'string'],
            'conversation.page_title' => ['nullable', 'string'],
            'conversation.collecting_field' => ['nullable', 'string'],
            'conversation.selected_intent' => ['nullable', 'string'],
            'conversation.event_type' => ['nullable', 'string'],
            'lead' => ['nullable', 'array'],
            'lead.full_name' => ['nullable', 'string'],
            'lead.email' => ['nullable', 'string'],
            'lead.phone' => ['nullable', 'string'],
            'lead.intent' => ['nullable', 'string'],
            'lead.source_page' => ['nullable', 'string'],
            'lead.status' => ['nullable', 'string'],
            'lead.notes' => ['nullable', 'string'],
            'lead.business_name' => ['nullable', 'string'],
            'lead.website_url' => ['nullable', 'string'],
            'lead.service_interest' => ['nullable', 'string'],
            'lead.custom_data' => ['nullable', 'array'],
            'error_log' => ['nullable', 'array'],
            'error_log.workflow_name' => ['nullable', 'string'],
            'error_log.workflow_id' => ['nullable', 'string'],
            'error_log.execution_id' => ['nullable', 'string'],
            'error_log.failed_node' => ['nullable', 'string'],
            'error_log.error_message' => ['nullable', 'string'],
            'error_log.error_stack' => ['nullable', 'string'],
            'error_log.last_user_message' => ['nullable', 'string'],
            'error_log.page_url' => ['nullable', 'string'],
            'error_log.page_title' => ['nullable', 'string'],
            'error_log.severity' => ['nullable', 'string'],
            'error_log.status' => ['nullable', 'string'],
            'error_log.raw_error' => ['nullable', 'array'],
        ]);

        $result = DB::transaction(function () use ($data, $clientId) {
            $conversation = Conversation::create(array_merge([
                'client_id' => $clientId,
            ], $data['conversation']));

            $lead = null;
            if (!empty($data['lead'])) {
                $lead = ConversationLead::create(array_merge([
                    'client_id' => $clientId,
                    'session_id' => $data['conversation']['session_id'],
                ], $data['lead']));
            }

            $errorLog = null;
            if (!empty($data['error_log'])) {
                $errorLog = ErrorLog::create(array_merge([
                    'client_id' => $clientId,
                    'session_id' => $data['conversation']['session_id'],
                    'lead_id' => $data['conversation']['lead_id'] ?? ($lead->id ?? null),
                    'last_user_message' => $data['conversation']['message'],
                ], $data['error_log']));
            }

            return [
                'conversation' => $conversation,
                'lead' => $lead,
                'error_log' => $errorLog,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Conversation event saved successfully.',
            'data' => $result,
        ], 201);
    }
}

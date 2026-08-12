<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RealtimeSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class RealtimeSessionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'string', 'max:255'],
            'session_id' => ['required', 'string', 'max:255'],
            'current_url' => ['nullable', 'url', 'max:2048'],
            'page_title' => ['nullable', 'string', 'max:255'],
        ]);

        $apiKey = (string) config('services.openai.api_key');
        $model = (string) config('services.openai.realtime_model');


        if ($apiKey === '' || $model === '') {
            return response()->json([
                'success' => false,
                'message' => 'Unable to create realtime session.',
            ], 500);
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->post('https://api.openai.com/v1/realtime/client_secrets', [
                    'session' => [
                        'type' => 'realtime',
                        'model' => $model,
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('OpenAI realtime session creation failed.', [
                    'status' => $response->status(),
                    'client_id' => $data['client_id'],
                    'session_id' => $data['session_id'],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to create realtime session.',
                ], 502);
            }

            $json = $response->json();

            $clientSecret = data_get($json, 'value');
            $expiresAt = data_get($json, 'expires_at');
            $realtimeSessionId = data_get($json, 'session.id');

            RealtimeSession::query()->create([
                'client_id' => $data['client_id'],
                'session_id' => $data['session_id'],
                'current_url' => $data['current_url'] ?? null,
                'page_title' => $data['page_title'] ?? null,
                'realtime_session_id' => $realtimeSessionId,
                'client_secret' => null,
                'expires_at' => $expiresAt,
                'request_payload' => $data,
                'response_payload' => [
                    'expires_at' => $expiresAt,
                    'realtime_session_id' => $realtimeSessionId,
                ],
            ]);
            return response()->json([
                'success' => true,
                'data' => [
                    'client_secret' => $clientSecret,
                    'expires_at' => $expiresAt,
                    'realtime_session_id' => $realtimeSessionId,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Unable to create realtime session.', [
                'client_id' => $data['client_id'],
                'session_id' => $data['session_id'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to create realtime session.',
            ], 500);
        }
    }
}

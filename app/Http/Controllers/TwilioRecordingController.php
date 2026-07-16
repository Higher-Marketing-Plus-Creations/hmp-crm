<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioRecordingController extends Controller
{
    private function twilio(): Client
    {
        return new Client(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        );
    }

    public function index(Request $request): View
    {
        $limit = (int) $request->input('limit', 10);
        $allowedLimits = [10, 50, 500];
        if (!in_array($limit, $allowedLimits, true)) {
            $limit = 10;
        }

        $selectedReceiver = trim((string) $request->input('receiver'));

        try {
            $twilio = $this->twilio();

            $twilioRecordings = $twilio->recordings->read([], $limit, $limit);

            $recordings = collect($twilioRecordings)->map(
                function ($recording) use ($twilio) {
                    $from = null;
                    $to = null;
                    $callStatus = null;

                    /*
                     * Recording object mein caller number nahi hota.
                     * Is liye CallSid se call details fetch kar rahe hain.
                     */
                    if (!empty($recording->callSid)) {
                        try {
                            $call = $twilio
                                ->calls($recording->callSid)
                                ->fetch();

                            $from = $call->from;
                            $to = $call->to;
                            $callStatus = $call->status;
                        } catch (TwilioException $exception) {
                            Log::warning(
                                'Twilio call details could not be fetched',
                                [
                                    'call_sid' => $recording->callSid,
                                    'message' => $exception->getMessage(),
                                ]
                            );
                        }
                    }

                    return [
                        'sid' => $recording->sid,
                        'call_sid' => $recording->callSid,
                        'account_sid' => $recording->accountSid,
                        'status' => $recording->status,
                        'duration' => (int) ($recording->duration ?? 0),
                        'channels' => $recording->channels,
                        'source' => $recording->source,
                        'date_created' => $recording->dateCreated,
                        'from' => $from,
                        'to' => $to,
                        'call_status' => $callStatus,
                    ];
                }
            );

            $receivers = $this->loadReceiverNumbers($twilio);

            if ($selectedReceiver !== '') {
                $recordings = $recordings
                    ->filter(function (array $recording) use ($selectedReceiver) {
                        return (string) ($recording['to'] ?? '') === $selectedReceiver;
                    })
                    ->values();
            }

            $error = null;
        } catch (\Throwable $exception) {
            Log::error('Twilio recordings fetch error', [
                'message' => $exception->getMessage(),
            ]);

            $recordings = collect();
            $receivers = [];
            $error = 'Twilio se recordings fetch nahi ho sakin.';
        }

        return view('recording', [
            'recordings' => $recordings,
            'error' => $error,
            'receivers' => $receivers ?? [],
            'selectedReceiver' => $selectedReceiver,
            'selectedLimit' => $limit,
            'limitOptions' => [10, 50, 500],
        ]);
    }

    public function audio(
        Request $request,
        string $recordingSid
    ): StreamedResponse {
        $this->validateRecordingSid($recordingSid);

        /*
         * MP3 format:
         * https://api.twilio.com/.../Recordings/RE....mp3
         */
        $url = $this->recordingMediaUrl(
            $recordingSid,
            'mp3'
        );

        $headers = [];

        /*
         * Browser audio seeking ke liye Range header forward karein.
         */
        if ($request->hasHeader('Range')) {
            $headers['Range'] = $request->header('Range');
        }

        $response = Http::withBasicAuth(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        )
            ->withHeaders($headers)
            ->timeout(60)
            ->get($url);

        abort_unless(
            $response->successful()
            || $response->status() === 206,
            $response->status() ?: 404,
            'Recording audio could not be loaded.'
        );

        $responseHeaders = [
            'Content-Type' => $response->header(
                'Content-Type',
                'audio/mpeg'
            ),
            'Content-Disposition' =>
                'inline; filename="' . $recordingSid . '.mp3"',
            'Accept-Ranges' => $response->header(
                'Accept-Ranges',
                'bytes'
            ),
            'Cache-Control' => 'private, no-store',
        ];

        if ($response->header('Content-Length')) {
            $responseHeaders['Content-Length'] =
                $response->header('Content-Length');
        }

        if ($response->header('Content-Range')) {
            $responseHeaders['Content-Range'] =
                $response->header('Content-Range');
        }

        return response()->stream(
            function () use ($response) {
                echo $response->body();
            },
            $response->status(),
            $responseHeaders
        );
    }

    public function download(
        string $recordingSid
    ): StreamedResponse {
        $this->validateRecordingSid($recordingSid);

        $url = $this->recordingMediaUrl(
            $recordingSid,
            'mp3'
        );

        $response = Http::withBasicAuth(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        )
            ->timeout(60)
            ->get($url);

        abort_unless(
            $response->successful(),
            404,
            'Recording could not be downloaded.'
        );

        return response()->streamDownload(
            function () use ($response) {
                echo $response->body();
            },
            $recordingSid . '.mp3',
            [
                'Content-Type' => 'audio/mpeg',
                'Cache-Control' => 'private, no-store',
            ]
        );
    }

    private function recordingMediaUrl(
        string $recordingSid,
        string $format = 'mp3'
    ): string {
        $accountSid = config(
            'services.twilio.account_sid'
        );

        return sprintf(
            'https://api.twilio.com/2010-04-01/Accounts/%s/Recordings/%s.%s',
            $accountSid,
            $recordingSid,
            $format
        );
    }

    private function validateRecordingSid(
        string $recordingSid
    ): void {
        abort_unless(
            preg_match('/^RE[a-fA-F0-9]{32}$/', $recordingSid),
            404,
            'Invalid recording SID.'
        );
    }

    private function loadReceiverNumbers(Client $twilio): array
    {
        try {
            $records = $twilio->recordings->read([], 500, 500);

            return collect($records)
                ->map(function ($recording) use ($twilio) {
                    if (empty($recording->callSid)) {
                        return null;
                    }

                    try {
                        return $twilio->calls($recording->callSid)->fetch()->to;
                    } catch (TwilioException $exception) {
                        Log::warning('Twilio receiver could not be fetched', [
                            'call_sid' => $recording->callSid,
                            'message' => $exception->getMessage(),
                        ]);

                        return null;
                    }
                })
                ->filter()
                ->unique()
                ->values()
                ->all();
        } catch (\Throwable $exception) {
            Log::warning('Twilio receivers list could not be loaded', [
                'message' => $exception->getMessage(),
            ]);

            return [];
        }
    }
}

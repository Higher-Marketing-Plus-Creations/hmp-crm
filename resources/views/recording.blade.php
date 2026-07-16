@extends('layouts.app', ['title' => 'Call Records', 'heading' => 'Call Records'])

@section('content')
<div class="crm-card p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-xl font-black text-slate-900">Twilio Call Recordings</h3>
            <p class="text-sm text-slate-500">Recordings are loaded directly from the Twilio API.</p>
        </div>

        <span class="crm-badge-info">Showing: {{ $recordings->count() }}</span>
    </div>

    @if($error)
        <div class="crm-card mb-4 border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            {{ $error }}
        </div>
    @endif

    <div class="crm-card mb-6 border border-slate-200 bg-slate-50 p-4">
        <form method="GET" action="{{ route('admin.twilio.recordings.index') }}">
            <div class="grid gap-3 md:grid-cols-3 xl:grid-cols-4">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600">Show records</label>
                    <select name="limit" class="crm-select">
                        @foreach($limitOptions as $option)
                            <option value="{{ $option }}" @selected((int) request('limit', $selectedLimit ?? 10) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-slate-600">Receiver</label>
                    <select name="receiver" class="crm-select">
                        <option value="">All receivers</option>
                        @foreach($receivers as $receiver)
                            <option value="{{ $receiver }}" @selected(request('receiver') === $receiver)>{{ $receiver }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="crm-button w-full">Filter</button>
                </div>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="text-slate-400">
                <tr>
                    <th class="pb-3">#</th>
                    <th class="pb-3">Caller</th>
                    <th class="pb-3">Receiver</th>
                    <th class="pb-3">Date</th>
                    <th class="pb-3">Duration</th>
                    <th class="pb-3">Channels</th>
                    <th class="pb-3">Status</th>
                    <th class="pb-3" style="min-width: 320px;">Recording</th>
                    <th class="pb-3">Download</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse($recordings as $recording)
                    @php
                        $minutes = floor($recording['duration'] / 60);
                        $seconds = $recording['duration'] % 60;
                    @endphp
                    <tr>
                        <td class="py-4 align-top">{{ $loop->iteration }}</td>
                        <td class="py-4 align-top">
                            <p class="font-semibold text-slate-900">{{ $recording['from'] ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-500">{{ $recording['call_sid'] }}</p>
                        </td>
                        <td class="py-4 align-top">{{ $recording['to'] ?? 'Unknown' }}</td>
                        <td class="py-4 align-top">
                            @if($recording['date_created'])
                                {{ \Carbon\Carbon::parse($recording['date_created'])->setTimezone(config('app.timezone'))->format('d M Y, h:i A') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-4 align-top">{{ sprintf('%02d:%02d', $minutes, $seconds) }}</td>
                        <td class="py-4 align-top">{{ $recording['channels'] ?? 1 }}</td>
                        <td class="py-4 align-top">
                            @if($recording['status'] === 'completed')
                                <span class="crm-badge-success">Completed</span>
                            @else
                                <span class="crm-badge-warning">{{ ucfirst($recording['status']) }}</span>
                            @endif
                        </td>
                        <td class="py-4 align-top">
                            @if($recording['status'] === 'completed')
                                <audio controls preload="none" style="width: 290px;">
                                    <source src="{{ route('admin.twilio.recordings.audio', $recording['sid']) }}" type="audio/mpeg">
                                    Your browser does not support audio playback.
                                </audio>
                            @else
                                <span class="text-muted">Recording is still processing</span>
                            @endif
                        </td>
                        <td class="py-4 align-top">
                            @if($recording['status'] === 'completed')
                                <a href="{{ route('admin.twilio.recordings.download', $recording['sid']) }}" class="crm-button-secondary">Download</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-5 text-center text-slate-500">No recordings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@extends('layouts.app', ['title' => 'Posts', 'heading' => 'Posts'])

@section('content')
    @php
        $apiWebsite = $website ?? ($posts->first()?->website);
        $apiKey = $apiWebsite?->api_key;
        $embedCode = $apiKey
            ? '<script data-continer=".post" src="' . url('/api/posts/widget-script') . '?api_key=' . $apiKey . '"></script>'
            : '<script data-continer=".post" src="' . url('/api/posts/widget-script') . '?api_key=YOUR_API_KEY"></script>';
    @endphp

    <div class="crm-card p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Blog Posts</h3>
                <p class="text-sm text-slate-500">
                    {{ isset($website) && $website ? $website->website_name . ' posts only.' : 'Create posts for a website and display them with the embed script.' }}
                </p>
            </div>
            <div class="flex gap-2">
                @if(isset($website) && $website)
                    <a href="{{ route('admin.websites.posts.create', $website) }}" class="crm-button">Add Post</a>
                @else
                    <a href="{{ route('admin.posts.create') }}" class="crm-button">Add Post</a>
                @endif
            </div>
        </div>

        @if($apiWebsite && $apiKey)
            <div class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Embed / All Posts API</p>
                    <input class="crm-input mt-2 font-mono text-xs" readonly value="{{ url('/api/posts/widget') }}?api_key={{ $apiKey }}">
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Single Post</p>
                    <input class="crm-input mt-2 font-mono text-xs" readonly value="{{ url('/api/posts/detail') }}?api_key={{ $apiKey }}&slug={SLUG}">
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 md:col-span-2 xl:col-span-1">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Embed Code</p>
                    <textarea class="crm-input mt-2 h-24 font-mono text-xs" readonly>{{ $embedCode }}</textarea>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="text-slate-400">
                <tr>
                    <th class="pb-3">Title</th>
                    <th class="pb-3">Slug</th>
                    <th class="pb-3">Website</th>
                    <th class="pb-3">Category</th>
                    <th class="pb-3">Created</th>
                    <th class="pb-3"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @foreach ($posts as $post)
                    <tr>
                        <td class="py-4 align-top">
                            <p class="font-semibold text-slate-900">{{ $post->title }}</p>
                        </td>
                        <td class="py-4 align-top">{{ $post->slug }}</td>
                        <td class="py-4 align-top">{{ $post->website?->website_name ?? 'All websites' }}</td>
                        <td class="py-4 align-top">{{ $post->category ?? '—' }}</td>
                        <td class="py-4 align-top">{{ $post->created_at->format('d M Y') }}</td>
                        <td class="py-4 align-top">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="crm-button-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="crm-button-secondary">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $posts->links() }}</div>
    </div>
@endsection

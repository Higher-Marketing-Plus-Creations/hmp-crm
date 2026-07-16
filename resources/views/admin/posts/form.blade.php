@extends('layouts.app', ['title' => isset($post->id) ? 'Edit Post' : 'Create Post', 'heading' => isset($post->id) ? 'Edit Post' : 'Create Post'])

@php
    $selectedWebsite = $website ?? $websites->firstWhere('id', old('website_id', $post->website_id));
    $allPostsWidgetApiUrl = $selectedWebsite
        ? url('/api/posts/widget') . '?api_key=' . $selectedWebsite->api_key
        : url('/api/posts/widget') . '?api_key=YOUR_API_KEY';
    $singlePostApiUrl = $selectedWebsite
        ? url('/api/posts/detail') . '?api_key=' . $selectedWebsite->api_key . '&slug=' . ($post->slug ?: '{SLUG}')
        : url('/api/posts/detail') . '?api_key=YOUR_API_KEY&slug={SLUG}';
@endphp

@section('content')
<div class="crm-card p-6">
    <form method="POST" action="{{ isset($post->id) ? route('admin.posts.update', $post) : route('admin.posts.store') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @if (isset($post->id))
        @method('PUT')
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700" for="title">Title</label>
                <input id="title" name="title" value="{{ old('title', $post->title) }}" class="crm-input" required>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700" for="slug">Slug</label>
                <input id="slug" name="slug" value="{{ old('slug', $post->slug) }}" class="crm-input" placeholder="Leave blank to auto-generate">
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700" for="feature_image">Feature Image</label>
                <input id="feature_image" name="feature_image" type="file" value="{{ old('feature_image', $post->feature_image) }}" class="crm-input" placeholder="https://example.com/image.jpg">
                @if ($post->feature_image)
                @if($post->feature_image)
                <img
                    src="{{ asset('storage/' . $post->feature_image) }}"
                    alt="Feature Image"
                    class="mt-2 h-32 w-32 rounded object-cover">
                @endif @endif
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700" for="category">Category</label>
                <input id="category" name="category" value="{{ old('category', $post->category) }}" class="crm-input" placeholder="news, updates, seo">
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700" for="website_id">Website</label>
            @if($websites->count() === 1)
                <input type="hidden" name="website_id" value="{{ $websites->first()->id }}">
                <input class="crm-input" value="{{ $websites->first()->website_name }}" readonly>
            @else
                <select id="website_id" name="website_id" class="crm-input" required>
                    <option value="">Select website</option>
                    @foreach($websites as $websiteOption)
                        <option value="{{ $websiteOption->id }}" {{ old('website_id', $post->website_id) == $websiteOption->id ? 'selected' : '' }}>{{ $websiteOption->website_name }}</option>
                    @endforeach
                </select>
            @endif
            <script>
                const websites = @json($websites->map(function ($website) {
                    return [
                        'id' => $website->id,
                        'api_key' => $website->api_key
                    ];
                }));
            </script>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700" for="content">Content</label>
            <textarea id="content" name="content" class="crm-input min-h-[320px]">
            {{ old('content', $post->content) }}
            </textarea>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
            <div class="flex items-center justify-between gap-3">
                <p class="font-semibold text-slate-700">All Posts API</p>
                <button type="button" class="crm-button-secondary px-3 py-2 text-xs" data-copy-target="all-posts-widget-api-url">Copy URL</button>
            </div>
            <p class="mt-2">Use this endpoint to fetch all posts for the selected website.</p>
            <input id="all-posts-widget-api-url" type="text" class="mt-3 crm-input font-mono text-xs" value="{{ $allPostsWidgetApiUrl }}" readonly>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
            <div class="flex items-center justify-between gap-3">
                <p class="font-semibold text-slate-700">Single Post API</p>
                <button type="button" class="crm-button-secondary px-3 py-2 text-xs" data-copy-target="single-post-api-url">Copy URL</button>
            </div>
            <p class="mt-2">Use this endpoint to fetch one post by API key and slug.</p>
            <input id="single-post-api-url" type="text" class="mt-3 crm-input font-mono text-xs" value="{{ $singlePostApiUrl }}" readonly>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="crm-button">Save Post</button>
            <a href="{{ route('admin.posts.index') }}" class="crm-button-secondary">Cancel</a>
        </div>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>
<script>
    $('#content').summernote({
        height: 360,
        placeholder: 'Write your post content here...',
        dialogsInBody: true,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture']],
            ['view', ['codeview']]
        ]
    });
</script>
<script>
    document.querySelectorAll('[data-copy-target]').forEach(function (button) {
        button.addEventListener('click', async function () {
            var targetId = button.getAttribute('data-copy-target');
            var target = document.getElementById(targetId);

            if (!target) {
                return;
            }

            var value = 'value' in target ? target.value : target.textContent;
            var originalText = button.textContent;

            try {
                await navigator.clipboard.writeText(value);
                button.textContent = 'Copied';
            } catch (error) {
                target.focus();
                target.select();
                document.execCommand('copy');
                button.textContent = 'Copied';
            }

            setTimeout(function () {
                button.textContent = originalText;
            }, 1500);
        });
    });

    (function () {
        var titleInput = document.getElementById('title');
        var slugInput = document.getElementById('slug');
        var slugTouched = false;

        if (!titleInput || !slugInput) {
            return;
        }

        slugInput.addEventListener('input', function () {
            slugTouched = slugInput.value.trim() !== '';
        });

        function toSlug(value) {
            return value
                .toString()
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        function syncSlug() {
            if (slugTouched) {
                return;
            }

            slugInput.value = toSlug(titleInput.value);
        }

        titleInput.addEventListener('input', syncSlug);
        titleInput.addEventListener('blur', syncSlug);

        syncSlug();
    })();
</script>
@endsection

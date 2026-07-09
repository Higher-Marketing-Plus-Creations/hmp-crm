@extends('layouts.app', ['title' => isset($post->id) ? 'Edit Post' : 'Create Post', 'heading' => isset($post->id) ? 'Edit Post' : 'Create Post'])

@php
    $selectedWebsite = $website ?? $websites->firstWhere('id', old('website_id', $post->website_id));
    $allPostsWidgetApiUrl = $selectedWebsite
        ? url('/api/posts/widget') . '?api_key=' . $selectedWebsite->api_key
        : url('/api/posts/widget') . '?api_key=YOUR_API_KEY';
    $singlePostApiUrl = $selectedWebsite
        ? url('/api/posts/detail') . '?api_key=' . $selectedWebsite->api_key . '&post_id=' . ($post->id ?: '{POST_ID}')
        : url('/api/posts/detail') . '?api_key=YOUR_API_KEY&post_id={POST_ID}';
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
            <p class="font-semibold text-slate-700">Embed script</p>
            <p class="mt-2">Use this on any website to render posts:</p>
            <code id="embedScript" class="mt-3 block overflow-x-auto rounded  p-3 text-xs text-slate-100">
                &lt;script data-continer=".post" src="{{ url('/api/posts/widget-script') }}?api_key={{ optional($websites->firstWhere('id', old('website_id', $post->website_id)))->api_key ?? 'YOUR_API_KEY' }}"&gt;&lt;/script&gt;
            </code>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
            <div class="flex items-center justify-between gap-3">
                <p class="font-semibold text-slate-700">All Posts Widget API</p>
                <button type="button" class="crm-button-secondary px-3 py-2 text-xs" data-copy-target="all-posts-widget-api-url">Copy URL</button>
            </div>
            <p class="mt-2">Use this endpoint to fetch all posts for the selected website.</p>
            <input id="all-posts-widget-api-url" type="text" class="mt-3 crm-input font-mono text-xs" value="{{ $allPostsWidgetApiUrl }}" readonly>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
            <div class="flex items-center justify-between gap-3">
                <p class="font-semibold text-slate-700">Widget Script API</p>
                <button type="button" class="crm-button-secondary px-3 py-2 text-xs" data-copy-target="widget-script-api-url">Copy URL</button>
            </div>
            <p class="mt-2">Use this endpoint when you want the widget script loader for posts.</p>
            <input id="widget-script-api-url" type="text" class="mt-3 crm-input font-mono text-xs" value="{{ url('/api/posts/widget-script') }}?api_key={{ $selectedWebsite->api_key ?? 'YOUR_API_KEY' }}" readonly>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
            <div class="flex items-center justify-between gap-3">
                <p class="font-semibold text-slate-700">Single Post API</p>
                <button type="button" class="crm-button-secondary px-3 py-2 text-xs" data-copy-target="single-post-api-url">Copy URL</button>
            </div>
            <p class="mt-2">Use this endpoint to fetch one post by API key and post ID.</p>
            <input id="single-post-api-url" type="text" class="mt-3 crm-input font-mono text-xs" value="{{ $singlePostApiUrl }}" readonly>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="crm-button">Save Post</button>
            <a href="{{ route('admin.posts.index') }}" class="crm-button-secondary">Cancel</a>
        </div>
    </form>
</div>

<script src="https://cdn.tiny.cloud/1/o0ha19018mpresx2wuhby39ymhf9r9op3tek3wzwzypz97us/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 360,
        menubar: false,
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen',
        toolbar: 'undo redo | blocks | bold italic underline | link image | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat code',
        content_style: 'body { font-family: Inter, Arial, sans-serif; font-size: 14px; }'
    });

    
</script>
<script>
    const websiteSelect = document.getElementById('website_id');
    const embedScript = document.getElementById('embedScript');

    function updateEmbedScript() {
        if (!websiteSelect || !embedScript) {
            return;
        }

        const website = websites.find(w => w.id == websiteSelect.value) || websites[0];

        const apiKey = website ? website.api_key : 'YOUR_API_KEY';

        // Default container selector
        const container = '.post';

        embedScript.textContent =
`<script src="{{ url('/api/posts/widget-script') }}?api_key=${apiKey}" data-container="${container}"><\/script>`;
    }

    if (websiteSelect) {
        websiteSelect.addEventListener('change', updateEmbedScript);
    }

    updateEmbedScript();

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
</script>
@endsection

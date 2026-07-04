@extends('layouts.app', ['title' => isset($post->id) ? 'Edit Post' : 'Create Post', 'heading' => isset($post->id) ? 'Edit Post' : 'Create Post'])

@section('content')
<div class="crm-card p-6">
    <form method="POST" action="{{ isset($post->id) ? route('admin.posts.update', $post) : route('admin.posts.store') }}" class="space-y-6"     enctype="multipart/form-data">
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
@endif                @endif
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700" for="category">Category</label>
                <input id="category" name="category" value="{{ old('category', $post->category) }}" class="crm-input" placeholder="news, updates, seo">
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700" for="website_id">Website</label>
            <select id="website_id" name="website_id" class="crm-input">
                <option value="">All websites</option>
                @foreach($websites as $website)
                <option value="{{ $website->id }}" {{ old('website_id', $post->website_id) == $website->id ? 'selected' : '' }}>{{ $website->website_name }}</option>
                @endforeach
            </select>
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
            <code class="mt-3 block overflow-x-auto rounded bg-slate-900 p-3 text-xs text-slate-100">&lt;script src="{{ url('/api/posts/widget-script') }}?website_id={{ $post->website_id ?? 'YOUR_WEBSITE_ID' }}"&gt;&lt;/script&gt;</code>
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
@endsection
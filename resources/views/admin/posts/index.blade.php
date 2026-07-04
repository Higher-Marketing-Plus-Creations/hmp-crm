@extends('layouts.app', ['title' => 'Posts', 'heading' => 'Posts'])

@section('content')
    <div class="crm-card p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Blog Posts</h3>
                <p class="text-sm text-slate-500">Create posts for a website and display them with the embed script.</p>
            </div>
            <a href="{{ route('admin.posts.create') }}" class="crm-button">Add Post</a>
        </div>

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

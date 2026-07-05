<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public function index(): View
    {
        return view('admin.posts.index', [
            'posts' => Post::query()
                ->with('website')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.form', [
            'post' => new Post(),
            'websites' => Website::query()->orderBy('website_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $post = Post::query()->create($this->validatedData($request));

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('status', 'Post created successfully.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.form', [
            'post' => $post,
            'websites' => Website::query()->orderBy('website_name')->get(),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $post->update($this->validatedData($request, $post));

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('status', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Post deleted successfully.');
    }
public function widget(Request $request): JsonResponse
{
    $apiKey = $request->get('api_key');

    if (!$apiKey) {
        return response()->json([
            'message' => 'API Key is required.'
        ], 400);
    }

    $website = Website::where('api_key', $apiKey)->first();

    if (!$website) {
        return response()->json([
            'message' => 'Invalid API Key.'
        ], 404);
    }

    $posts = Post::where('website_id', $website->id)
        ->latest()
        ->limit(10)
        ->get([
            'id',
            'title',
            'slug',
            'feature_image',
            'content',
            'category',
            'created_at',
        ])
        ->map(function ($post) {
            $post->feature_image = $post->feature_image
                ? asset('storage/public/' . str_replace('public/', '', $post->feature_image))
                : null;

            return $post;
        });

    return response()->json([
        'api_key' => $apiKey,
        'posts' => $posts,
    ]);
}

    public function widgetScript(Request $request): Response
{
    return response()
        ->view('posts.widget-script', [
            'apiKey' => $request->api_key
        ])
        ->header('Content-Type', 'application/javascript');
}

    protected function validatedData(Request $request, ?Post $post = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'feature_image' => ['nullable', 'mimes:jpeg,png,jpg', 'max:2048'],
            'content' => ['required', 'string'],
            'website_id' => ['nullable', 'exists:websites,id'],
            'category' => ['nullable', 'string', 'max:100'],
            
        ]);

        $data['slug'] = $this->normalizeSlug($data['slug'] ?? null, (string) ($data['title'] ?? ''), $post);
        $data['feature_image'] = trim((string) ($data['feature_image'] ?? '')) !== '' ? trim((string) $data['feature_image']) : null;
        $data['category'] = trim((string) ($data['category'] ?? '')) !== '' ? trim((string) $data['category']) : null;
        $data['website_id'] = $data['website_id'] ?? null;
        if($data['feature_image']) {
            $data['feature_image'] = $request->file('feature_image')->store('public/feature_images');
        }

        return $data;
    }

    protected function normalizeSlug(?string $slug, string $title, ?Post $post): string
    {
        $baseSlug = trim((string) $slug);

        if ($baseSlug === '') {
            $baseSlug = Str::slug($title);
        }

        $baseSlug = Str::slug($baseSlug);

        if ($baseSlug === '') {
            $baseSlug = 'post';
        }

        $candidate = $baseSlug;
        $counter = 1;

        while (Post::query()
            ->where('slug', $candidate)
            ->when($post, fn ($query) => $query->whereKeyNot($post->getKey()))
            ->exists()) {
            $candidate = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }
}

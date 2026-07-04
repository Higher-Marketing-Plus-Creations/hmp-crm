<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Post;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_endpoint_returns_posts_for_a_website(): void
    {
        $client = Client::create([
            'name' => 'Acme Agency',
            'email' => 'agency@example.com',
            'phone' => '123456789',
            'company_name' => 'Acme',
        ]);

        $website = Website::create([
            'client_id' => $client->id,
            'website_name' => 'Example Site',
            'website_url' => 'https://example.com',
            'allowed_domains' => 'example.com',
            'notification_emails' => [],
            'status' => 'active',
            'api_key' => 'lead_test_1234567890',
        ]);

        Post::create([
            'website_id' => $website->id,
            'title' => 'Launch Post',
            'slug' => 'launch-post',
            'content' => '<p>Hello world</p>',
            'category' => 'news',
        ]);

        $response = $this->getJson('/api/posts/widget?website_id=' . $website->id);

        $response->assertOk()
            ->assertJsonCount(1, 'posts');
    }
}

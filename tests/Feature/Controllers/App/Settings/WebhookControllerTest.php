<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Settings;

use App\Models\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_lists_the_users_webhook_endpoints(): void
    {
        $user = $this->createUser();
        $endpoint = WebhookEndpoint::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://central-perk.test/webhooks',
        ]);

        $response = $this->actingAs($user)->get('/settings/webhooks');

        $response->assertOk();
        $response->assertSee('https://central-perk.test/webhooks');
        $response->assertSee($endpoint->secret);
    }

    #[Test]
    public function it_does_not_list_another_users_endpoints(): void
    {
        $user = $this->createUser();
        $otherEndpoint = WebhookEndpoint::factory()->create([
            'url' => 'https://gunther.test/webhooks',
        ]);

        $response = $this->actingAs($user)->get('/settings/webhooks');

        $response->assertOk();
        $response->assertDontSee('https://gunther.test/webhooks');
    }

    #[Test]
    public function it_shows_the_create_form(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/settings/webhooks/new');

        $response->assertOk();
        $response->assertSee('Endpoint URL');
    }

    #[Test]
    public function it_creates_a_webhook_endpoint(): void
    {
        Queue::fake();

        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->from('/settings/webhooks/new')
            ->post('/settings/webhooks', [
                'url' => 'https://central-perk.test/webhooks',
                'label' => 'Central Perk',
            ]);

        $response->assertRedirect('/settings/webhooks');
        $response->assertSessionHas('status', 'Webhook endpoint created');

        $this->assertDatabaseCount('webhook_endpoints', 1);
        $endpoint = WebhookEndpoint::query()->first();
        $this->assertSame($user->id, $endpoint->user_id);
        $this->assertSame('https://central-perk.test/webhooks', $endpoint->url);
        $this->assertSame('Central Perk', $endpoint->label);
        $this->assertNotEmpty($endpoint->secret);
    }

    #[Test]
    public function it_validates_the_url_when_creating(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->from('/settings/webhooks/new')
            ->post('/settings/webhooks', [
                'url' => 'not-a-valid-url',
            ]);

        $response->assertSessionHasErrors('url');
        $this->assertDatabaseCount('webhook_endpoints', 0);
    }

    #[Test]
    public function it_deletes_a_webhook_endpoint(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $endpoint = WebhookEndpoint::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->delete('/settings/webhooks/'.$endpoint->id);

        $response->assertRedirect('/settings/webhooks');
        $response->assertSessionHas('status', 'Webhook endpoint deleted');
        $this->assertModelMissing($endpoint);
    }

    #[Test]
    public function it_cannot_delete_another_users_endpoint(): void
    {
        $user = $this->createUser();
        $otherEndpoint = WebhookEndpoint::factory()->create();

        $response = $this->actingAs($user)
            ->delete('/settings/webhooks/'.$otherEndpoint->id);

        $response->assertNotFound();
        $this->assertModelExists($otherEndpoint);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Administration;

use App\Models\Log;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdministrationLogsControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'user_name',
            'vault_id',
            'vault_name',
            'action',
            'parameters',
            'description',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];

    #[Test]
    public function it_lists_the_logs_of_the_current_user(): void
    {
        Date::setTestNow('2025-06-30 12:00:00');
        $user = User::factory()->create([
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
        ]);
        $vault = Vault::factory()->create([
            'name' => 'Central Perk',
        ]);
        $log = Log::factory()->create([
            'vault_id' => $vault->id,
            'user_id' => $user->id,
            'user_name' => 'Chandler Bing',
            'action' => 'vault_created',
            'parameters' => ['name' => 'Central Perk'],
        ]);

        $anotherUser = User::factory()->create();
        $anotherLog = Log::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/administration/logs');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->jsonStructure,
                ],
                'links',
                'meta',
            ])
            ->assertJsonPath('data.0.id', (string) $log->id)
            ->assertJsonPath('data.0.attributes.user_name', 'Chandler Bing')
            ->assertJsonPath('data.0.attributes.vault_id', (string) $vault->id)
            ->assertJsonPath('data.0.attributes.vault_name', 'Central Perk')
            ->assertJsonPath('data.0.attributes.action', 'vault_created')
            ->assertJsonPath('data.0.attributes.parameters.name', 'Central Perk')
            ->assertJsonPath('data.0.attributes.description', 'Created a vault called Central Perk')
            ->assertJsonPath('data.0.attributes.created_at', 1751284800)
            ->assertJsonMissing(['id' => (string) $anotherLog->id]);
    }

    #[Test]
    public function it_paginates_the_logs(): void
    {
        $user = User::factory()->create();

        Log::factory()->count(15)->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/administration/logs');

        $response
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 15);
    }

    #[Test]
    public function it_shows_a_log(): void
    {
        Date::setTestNow('2025-06-30 12:00:00');
        $user = User::factory()->create([
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
        ]);
        $log = Log::factory()->create([
            'vault_id' => null,
            'user_id' => $user->id,
            'user_name' => 'Chandler Bing',
            'action' => 'user_profile_updated',
            'parameters' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/administration/logs/'.$log->id);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->jsonStructure,
            ])
            ->assertJsonPath('data.type', 'log')
            ->assertJsonPath('data.id', (string) $log->id)
            ->assertJsonPath('data.attributes.user_name', 'Chandler Bing')
            ->assertJsonPath('data.attributes.vault_id', null)
            ->assertJsonPath('data.attributes.vault_name', null)
            ->assertJsonPath('data.attributes.action', 'user_profile_updated')
            ->assertJsonPath('data.attributes.parameters', null)
            ->assertJsonPath('data.attributes.description', 'Updated their personal profile')
            ->assertJsonPath('data.attributes.created_at', 1751284800)
            ->assertJsonPath('data.attributes.updated_at', 1751284800)
            ->assertJsonPath('data.links.self', route('api.administration.logs.show', $log));
    }

    #[Test]
    public function it_cannot_show_another_users_log(): void
    {
        $user = User::factory()->create();
        $anotherLog = Log::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/administration/logs/'.$anotherLog->id);

        $response->assertNotFound();
    }
}

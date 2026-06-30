<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Adminland;

use App\Enums\PermissionEnum;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MemberControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'user_id',
            'name',
            'email',
            'timezone',
            'joined_at',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];

    #[Test]
    public function it_lists_the_members_of_a_vault(): void
    {
        $rachel = $this->createUser(['email' => 'rachel.green@friends.test']);
        $vault = $this->createVault();
        $member = $this->assignUserToVault($rachel, $vault, PermissionEnum::Owner->value);

        Sanctum::actingAs($rachel);

        $response = $this->json('GET', '/api/vaults/'.$vault->id.'/members');

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
            ->assertJsonPath('data.0.type', 'member')
            ->assertJsonPath('data.0.id', (string) $member->id)
            ->assertJsonPath('data.0.attributes.user_id', $rachel->id)
            ->assertJsonPath('data.0.attributes.email', 'rachel.green@friends.test');
    }

    #[Test]
    public function it_does_not_list_members_from_another_vault(): void
    {
        $ross = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($ross, $vault);

        $anotherVault = $this->createVault('Central Perk');
        $this->assignUserToVault($this->createUser(), $anotherVault);

        Sanctum::actingAs($ross);

        $response = $this->json('GET', '/api/vaults/'.$vault->id.'/members');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    #[Test]
    public function it_forbids_listing_members_of_a_vault_the_user_is_not_part_of(): void
    {
        $joey = $this->createUser();
        $vault = $this->createVault();

        Sanctum::actingAs($joey);

        $response = $this->json('GET', '/api/vaults/'.$vault->id.'/members');

        $response->assertForbidden();
    }

    #[Test]
    public function it_shows_a_member(): void
    {
        $monica = $this->createUser(['email' => 'monica.geller@friends.test']);
        $vault = $this->createVault();
        $member = $this->assignUserToVault($monica, $vault, PermissionEnum::Owner->value);

        Sanctum::actingAs($monica);

        $response = $this->json('GET', '/api/vaults/'.$vault->id.'/members/'.$member->id);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->jsonStructure,
            ])
            ->assertJsonPath('data.type', 'member')
            ->assertJsonPath('data.id', (string) $member->id)
            ->assertJsonPath('data.attributes.user_id', $monica->id)
            ->assertJsonPath('data.attributes.email', 'monica.geller@friends.test')
            ->assertJsonPath('data.links.self', route('api.vault.adminland.member.show', [
                'id' => $vault->id,
                'memberId' => $member->id,
            ]));
    }

    #[Test]
    public function it_returns_not_found_for_a_member_from_another_vault(): void
    {
        $phoebe = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($phoebe, $vault);

        $anotherVault = $this->createVault('Central Perk');
        $anotherMember = $this->assignUserToVault($this->createUser(), $anotherVault);

        Sanctum::actingAs($phoebe);

        $response = $this->json('GET', '/api/vaults/'.$vault->id.'/members/'.$anotherMember->id);

        $response->assertNotFound();
    }

    #[Test]
    public function it_lets_a_user_join_a_vault_with_an_invitation_code(): void
    {
        Queue::fake();

        $chandler = $this->createUser(['email' => 'chandler.bing@friends.test']);
        $vault = Vault::factory()->create([
            'invitation_code' => 'COULD-THIS-BE-ANY-MORE-OF-A-CODE',
        ]);

        Sanctum::actingAs($chandler);

        $response = $this->json('POST', '/api/vaults/join', [
            'invitation_code' => 'COULD-THIS-BE-ANY-MORE-OF-A-CODE',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'data' => $this->jsonStructure,
            ])
            ->assertJsonPath('data.type', 'member')
            ->assertJsonPath('data.attributes.user_id', $chandler->id)
            ->assertJsonPath('data.attributes.email', 'chandler.bing@friends.test');

        $this->assertDatabaseHas('members', [
            'vault_id' => $vault->id,
            'user_id' => $chandler->id,
        ]);
    }

    #[Test]
    public function it_validates_the_invitation_code_when_joining_a_vault(): void
    {
        $chandler = $this->createUser();

        Sanctum::actingAs($chandler);

        $response = $this->json('POST', '/api/vaults/join', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['invitation_code']);
    }

    #[Test]
    public function it_returns_a_validation_error_for_an_invalid_invitation_code(): void
    {
        $chandler = $this->createUser();
        $this->createVault();

        Sanctum::actingAs($chandler);

        $response = $this->json('POST', '/api/vaults/join', [
            'invitation_code' => 'NOPE',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['invitation_code']);
    }

    #[Test]
    public function it_returns_a_validation_error_when_the_user_already_belongs_to_the_vault(): void
    {
        $chandler = $this->createUser();
        $vault = Vault::factory()->create([
            'invitation_code' => 'ALREADY-IN',
        ]);
        $this->assignUserToVault($chandler, $vault);

        Sanctum::actingAs($chandler);

        $response = $this->json('POST', '/api/vaults/join', [
            'invitation_code' => 'ALREADY-IN',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['invitation_code']);
    }
}

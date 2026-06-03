<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Vault;

use App\Enums\PermissionEnum;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class JoinVaultControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_join_vault_page(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/vaults/join');

        $response->assertStatus(200);
        $response->assertViewIs('app.vault.join.create');
    }

    #[Test]
    public function it_joins_a_vault(): void
    {
        $user = $this->createUser();
        $vault = Vault::factory()->create([
            'invitation_code' => 'ABC123',
        ]);

        $response = $this->actingAs($user)->post('/vaults/join', [
            'invitation_code' => 'ABC123',
        ]);

        $response->assertRedirect('/vaults/'.$vault->id);
        $response->assertSessionHas('status');
    }

    #[Test]
    public function it_fails_if_invitation_code_is_missing(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/vaults/join', []);

        $response->assertSessionHasErrors('invitation_code');
    }

    #[Test]
    public function it_fails_if_invitation_code_is_invalid(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/vaults/join', [
            'invitation_code' => 'INVALID',
        ]);

        $response->assertSessionHasErrors('invitation_code');
    }

    #[Test]
    public function it_fails_if_user_is_already_a_member(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $vault->update(['invitation_code' => 'ABC123']);

        $response = $this->actingAs($user)->post('/vaults/join', [
            'invitation_code' => 'ABC123',
        ]);

        $response->assertSessionHasErrors('invitation_code');
    }
}

<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Vault\Adminland;

use App\Enums\PermissionEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandManageControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_destroy_vault_form(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->get("/vaults/{$vault->id}/adminland/manage");

        $response->assertOk();
        $response->assertSee(route('vault.adminland.manage.destroy', ['vaultId' => $vault->id]), false);
        $response->assertSee('value="delete"', false);
        $response->assertSee('onsubmit="return confirm', false);
    }

    #[Test]
    public function it_destroys_the_vault(): void
    {
        Queue::fake();
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->delete("/vaults/{$vault->id}/adminland/manage");

        $response->assertRedirect(route('vault.index'));
        $response->assertSessionHas('status', __('app/shared.changes_saved'));
        $this->assertDatabaseMissing('vaults', [
            'id' => $vault->id,
        ]);
    }
}

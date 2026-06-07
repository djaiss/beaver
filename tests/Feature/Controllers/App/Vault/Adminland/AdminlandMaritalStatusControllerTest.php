<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Vault\Adminland;

use App\Enums\PermissionEnum;
use App\Models\MaritalStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandMaritalStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_new_marital_status_form(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->get('/vaults/'.$vault->id.'/adminland/marital-statuses/new');

        $response->assertStatus(200);
        $response->assertViewIs('app.vault.adminland.manage._marital-status-new');
        $response->assertViewHas('vault');
    }

    #[Test]
    public function it_creates_a_marital_status(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->post('/vaults/'.$vault->id.'/adminland/marital-statuses', [
            'name' => 'Non-binary',
        ]);

        $maritalStatus = MaritalStatus::query()->where('vault_id', $vault->id)->first();
        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $response->assertSessionHas('status', __('app/shared.changes_saved'));
        $this->assertSame('Non-binary', $maritalStatus->name);
        $this->assertSame(1, $maritalStatus->position);
    }

    #[Test]
    public function it_shows_the_edit_marital_status_form(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $maritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Female',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->get('/vaults/'.$vault->id.'/adminland/marital-statuses/'.$maritalStatus->id.'/edit');

        $response->assertStatus(200);
        $response->assertViewIs('app.vault.adminland.manage._marital-status-edit');
        $response->assertViewHas('maritalStatus');
        $response->assertViewHas('vault');
        $response->assertSee('value="Female"', false);
    }

    #[Test]
    public function it_updates_a_marital_status(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $maritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Female',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->put('/vaults/'.$vault->id.'/adminland/marital-statuses/'.$maritalStatus->id, [
            'name' => 'Woman',
        ]);

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $response->assertSessionHas('status', __('app/shared.changes_saved'));
        $this->assertSame('Woman', $maritalStatus->refresh()->name);
        $this->assertSame(1, $maritalStatus->position);
    }

    #[Test]
    public function it_deletes_a_marital_status(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $maritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->delete('/vaults/'.$vault->id.'/adminland/marital-statuses/'.$maritalStatus->id);

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $response->assertSessionHas('status', __('app/shared.changes_saved'));
        $this->assertModelMissing($maritalStatus);
    }
}

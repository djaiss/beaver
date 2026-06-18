<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Vault\Adminland;

use App\Enums\PermissionEnum;
use App\Models\Gender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandGenderControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_new_gender_form(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->get('/vaults/'.$vault->id.'/adminland/genders/new');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_creates_a_gender(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->post('/vaults/'.$vault->id.'/adminland/genders', [
            'name' => 'Non-binary',
        ]);

        $gender = Gender::query()->where('vault_id', $vault->id)->first();
        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $response->assertSessionHas('status', __('app/shared.changes_saved'));
        $this->assertSame('Non-binary', $gender->name);
        $this->assertSame(1, $gender->position);
    }

    #[Test]
    public function it_shows_the_edit_gender_form(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Female',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->get('/vaults/'.$vault->id.'/adminland/genders/'.$gender->id.'/edit');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_updates_a_gender(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Female',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->put('/vaults/'.$vault->id.'/adminland/genders/'.$gender->id, [
            'name' => 'Woman',
        ]);

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $response->assertSessionHas('status', __('app/shared.changes_saved'));
        $this->assertSame('Woman', $gender->refresh()->name);
        $this->assertSame(1, $gender->position);
    }

    #[Test]
    public function it_deletes_a_gender(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->delete('/vaults/'.$vault->id.'/adminland/genders/'.$gender->id);

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $response->assertSessionHas('status', __('app/shared.changes_saved'));
        $this->assertModelMissing($gender);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Settings;

use App\Enums\PermissionEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_the_account_page(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->get('/settings/account');

        $response->assertOk();
        $response->assertViewIs('app.settings.account.index');
        $response->assertViewHas('vaultsToDelete');
        $response->assertViewHas('vaultsNotDeleted');
    }

    #[Test]
    public function it_shows_vaults_that_will_be_deleted(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)
            ->get('/settings/account');

        $response->assertViewHas(
            'vaultsToDelete',
            fn ($vaults): bool => $vaults->count() === 1
                && $vaults->first()->name === $vault->name,
        );
    }

    #[Test]
    public function it_shows_vaults_that_will_not_be_deleted(): void
    {
        $user = $this->createUser();
        $otherUser = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $this->assignUserToVault(
            user: $otherUser,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)
            ->get('/settings/account');

        $response->assertViewHas(
            'vaultsNotDeleted',
            fn ($vaults): bool => $vaults->count() === 1
                && $vaults->first()->name === $vault->name,
        );
    }

    #[Test]
    public function it_deletes_the_account(): void
    {
        Queue::fake();
        Mail::fake();

        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->delete('/settings/account', [
                'feedback' => 'I no longer need this service',
            ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}

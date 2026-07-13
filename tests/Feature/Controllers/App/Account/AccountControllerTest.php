<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Account;

use App\Enums\PermissionEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_lists_the_accounts_of_the_user(): void
    {
        $user = $this->createUser();
        $account = $this->createAccount(name: 'Central Perk');
        $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Owner->value);

        $response = $this->actingAs($user)->get('accounts');

        $response->assertOk();
        $response->assertViewIs('app.account.index');
        $response->assertViewHas('accounts');
    }

    #[Test]
    public function it_shows_the_create_account_page(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('accounts/new');

        $response->assertOk();
    }

    #[Test]
    public function it_creates_an_account_with_an_owner_membership(): void
    {
        Queue::fake();

        $user = $this->createUser();

        $response = $this->actingAs($user)->post('accounts', [
            'name' => 'Central Perk',
        ]);

        $account = $user->accounts()->firstOrFail();

        $response->assertRedirect(route('accounts.show', $account->id, absolute: false));
        $this->assertSame('Central Perk', $account->name);
        $this->assertDatabaseHas('account_user', [
            'account_id' => $account->id,
            'user_id' => $user->id,
            'role' => PermissionEnum::Owner->value,
        ]);
    }

    #[Test]
    public function it_shows_an_account_to_a_member(): void
    {
        $user = $this->createUser();
        $account = $this->createAccount();
        $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Viewer->value);

        $response = $this->actingAs($user)->get("accounts/{$account->id}");

        $response->assertOk();
        $response->assertViewIs('app.account.show');
    }

    #[Test]
    public function it_forbids_a_non_member_from_viewing_an_account(): void
    {
        $user = $this->createUser();
        $account = $this->createAccount();

        $response = $this->actingAs($user)->get("accounts/{$account->id}");

        $response->assertForbidden();
    }

    #[Test]
    public function it_returns_not_found_for_a_missing_account(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('accounts/999999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_renames_an_account_for_an_owner(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $account = $this->createAccount(name: 'Old name');
        $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Owner->value);

        $response = $this->actingAs($user)->put("accounts/{$account->id}", [
            'name' => 'Central Perk',
        ]);

        $response->assertRedirect(route('accounts.show', $account->id, absolute: false));
        $this->assertSame('Central Perk', $account->fresh()->name);
    }

    #[Test]
    public function it_forbids_a_non_owner_from_renaming_an_account(): void
    {
        $user = $this->createUser();
        $account = $this->createAccount();
        $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Viewer->value);

        $response = $this->actingAs($user)->put("accounts/{$account->id}", [
            'name' => 'Central Perk',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function it_deletes_an_account_for_an_owner(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $account = $this->createAccount();
        $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Owner->value);

        $response = $this->actingAs($user)->delete("accounts/{$account->id}");

        $response->assertRedirect(route('accounts.index', absolute: false));
        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    }

    #[Test]
    public function it_forbids_a_non_owner_from_deleting_an_account(): void
    {
        $user = $this->createUser();
        $account = $this->createAccount();
        $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Viewer->value);

        $response = $this->actingAs($user)->delete("accounts/{$account->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('accounts', ['id' => $account->id]);
    }
}

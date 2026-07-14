<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stamps the author when a record is created', function () {
    $user = $this->createUser(['first_name' => 'Rachel', 'last_name' => 'Green']);

    $account = $this->actingAs($user)->createAccount();

    $account->refresh();

    expect($account->created_by_id)->toBe($user->id);
    expect($account->created_by_name)->toBe('Rachel Green');
    expect($account->updated_by_id)->toBe($user->id);
    expect($account->updated_by_name)->toBe('Rachel Green');
});
it('refreshes only the updater when a record is updated', function () {
    $creator = $this->createUser(['first_name' => 'Rachel', 'last_name' => 'Green']);
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);

    $account = $this->actingAs($creator)->createAccount(name: 'Old name');

    $this->actingAs($editor);
    $account->name = 'Central Perk';
    $account->save();

    $account->refresh();

    expect($account->created_by_id)->toBe($creator->id);
    expect($account->created_by_name)->toBe('Rachel Green');
    expect($account->updated_by_id)->toBe($editor->id);
    expect($account->updated_by_name)->toBe('Ross Geller');
});
it('keeps the author name snapshot after the author is deleted', function () {
    $creator = $this->createUser(['first_name' => 'Rachel', 'last_name' => 'Green']);
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);

    $account = $this->actingAs($creator)->createAccount(name: 'Old name');

    $this->actingAs($editor);
    $account->name = 'Central Perk';
    $account->save();

    $creator->delete();

    $account->refresh();

    /*
     * The author id is wiped through the nullOnDelete foreign key when the
     * database enforces constraints. The sqlite testing connection leaves
     * foreign keys disabled, so we only assert on the encrypted name
     * snapshot, which is the behaviour that survives regardless of the
     * database in use.
     */
    $this->assertDatabaseMissing('users', ['id' => $creator->id]);
    expect($account->created_by_name)->toBe('Rachel Green');
});

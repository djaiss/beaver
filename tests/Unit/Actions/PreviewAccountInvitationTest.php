<?php

declare(strict_types=1);
use App\Actions\PreviewAccountInvitation;
use App\Enums\PermissionEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the account name without persisting or sending anything', function () {
    $account = $this->createAccount(name: 'Central Perk');

    $html = new PreviewAccountInvitation(
        account: $account,
        role: PermissionEnum::Editor->value,
    )->execute();

    expect($html)->toContain('Central Perk');
    $this->assertDatabaseCount('invitations', 0);
});

it('falls back to the viewer role when given an invalid role', function () {
    $account = $this->createAccount(name: 'Central Perk');

    $html = new PreviewAccountInvitation(
        account: $account,
        role: 'not-a-real-role',
    )->execute();

    expect($html)->toContain('Central Perk');
});

it('strips every link so the preview cannot be clicked through', function () {
    $account = $this->createAccount();

    $html = new PreviewAccountInvitation(
        account: $account,
        role: PermissionEnum::Viewer->value,
    )->execute();

    expect($html)->not->toContain('href=');
});

<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\CollectionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

function importPayload(): string
{
    return json_encode([
        'schemaVersion' => 1,
        'type' => [
            'name' => 'Comics',
            'color' => '#FB923C',
            'groups' => [
                ['name' => 'Publishing info', 'fields' => [['name' => 'Publisher', 'type' => 'text']]],
            ],
            'standaloneFields' => [],
        ],
    ]);
}

it('shows the import screen', function (): void {
    $user = $this->createUser();

    $this->actingAs($user)->get('/settings/types/import')
        ->assertOk()
        ->assertSee('Import a collection type')
        ->assertSee('Expected shape')
        ->assertSee('schemaVersion');
});

it('renders the import title help popover', function (): void {
    $user = $this->createUser();

    $this->actingAs($user)->get('/settings/types/import')
        ->assertOk()
        ->assertSee('Recreates a collection type from a JSON file');
});

it('links to the import screen from the collection types list', function (): void {
    $user = $this->createUser();

    $this->actingAs($user)->get('/settings/types')
        ->assertOk()
        ->assertSee('Import JSON')
        ->assertSee('/settings/types/import');
});

it('imports a type and redirects to it', function (): void {
    Queue::fake();

    $user = $this->createUser();

    $response = $this->actingAs($user)->post('/settings/types/import', ['json' => importPayload()]);

    $type = CollectionType::query()->latest('id')->sole();

    $response->assertRedirect('/settings/types/'.$type->id.'/edit');
    $response->assertSessionHas('status', 'Type imported');

    expect($type->name)->toBe('Comics');
    expect($type->account_id)->toBe($user->account_id);
    expect($type->customFieldGroups()->sole()->name)->toBe('Publishing info');
});

it('rejects a document it cannot trust', function (): void {
    $user = $this->createUser();

    $this->actingAs($user)->post('/settings/types/import', ['json' => '{"schemaVersion": 1, "type": {"name": "X", "groups": [{"name": "G", "fields": [{"name": "F", "type": "rce"}]}]}}'])
        ->assertSessionHasErrors('json');

    expect($user->account->collectionTypes()->count())->toBe(0);
});

it('requires a document to import', function (): void {
    $user = $this->createUser();

    $this->actingAs($user)->post('/settings/types/import', ['json' => ''])
        ->assertSessionHasErrors('json');
});

it('forbids viewers from reaching the import screen', function (): void {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)->get('/settings/types/import')->assertNotFound();
    $this->actingAs($viewer)->post('/settings/types/import', ['json' => importPayload()])->assertNotFound();
});

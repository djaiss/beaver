<?php

declare(strict_types=1);
use App\Actions\UpdateCollection;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Enums\VisibilityEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('updates a collection and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collection = Collection::factory()->create([
        'account_id' => $account->id,
        'name' => 'Old name',
        'visibility' => VisibilityEnum::Private->value,
    ]);

    $result = new UpdateCollection(
        user: $editor,
        collection: $collection,
        name: 'Marvel Comics 1990s',
        description: 'Updated description',
        emoji: '🦸',
        visibility: VisibilityEnum::Public->value,
        currency: 'EUR',
        settings: ['theme' => 'dark'],
    )->execute();

    expect($result)->toBeInstanceOf(Collection::class);
    expect($collection->fresh()->name)->toBe('Marvel Comics 1990s');
    expect($collection->fresh()->description)->toBe('Updated description');
    expect($collection->fresh()->emoji)->toBe('🦸');
    expect($collection->fresh()->visibility)->toBe(VisibilityEnum::Public);
    expect($collection->fresh()->currency)->toBe('EUR');
    expect($collection->fresh()->settings)->toBe(['theme' => 'dark']);
    expect($collection->fresh()->updated_by_id)->toBe($editor->id);
    expect($collection->fresh()->updated_by_name)->toBe('Monica Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CollectionUpdate,
    );
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    new UpdateCollection(
        user: $viewer,
        collection: $collection,
        name: 'New name',
    )->execute();
});

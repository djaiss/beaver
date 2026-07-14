<?php

declare(strict_types=1);
use App\Actions\UpdateType;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Type;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('updates a type and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $type = Type::factory()->create(['account_id' => $account->id, 'name' => 'Old name', 'color' => '#111111']);

    $result = new UpdateType(
        user: $editor,
        type: $type,
        name: 'Comics',
        color: '#1D4ED8',
    )->execute();

    expect($result)->toBeInstanceOf(Type::class);
    expect($type->fresh()->name)->toBe('Comics');
    expect($type->fresh()->color)->toBe('#1D4ED8');
    expect($type->fresh()->updated_by_id)->toBe($editor->id);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::TypeUpdate,
    );
});

it('throws when the color is not a valid hex', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $type = Type::factory()->create(['account_id' => $account->id]);

    new UpdateType(
        user: $owner,
        type: $type,
        name: 'Comics',
        color: 'not-a-color',
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $type = Type::factory()->create(['account_id' => $account->id]);

    new UpdateType(
        user: $viewer,
        type: $type,
        name: 'Comics',
    )->execute();
});

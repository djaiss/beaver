<?php

declare(strict_types=1);
use App\Actions\UpdateUserAvatar;
use App\Enums\UserActionEnum;
use App\Models\Collection;
use App\Models\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('shows the avatar of the author of an activity entry when they have one', function () {
    Storage::fake();

    $user = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);

    new UpdateUserAvatar(
        user: $user,
        file: UploadedFile::fake()->image('ross.jpg', 400, 400),
    )->execute();

    Collection::factory()->create(['account_id' => $user->account_id]);

    Log::factory()->create([
        'user_id' => $user->id,
        'action' => UserActionEnum::PersonalProfileUpdate->value,
    ]);

    $response = $this->actingAs($user->fresh())->get(route('dashboard.index'));

    $response->assertOk();
    $response->assertSee(route('profile.avatar.show', ['user' => $user, 'size' => 32]), escape: false);
});

it('falls back to the initials of the author when they have no avatar', function () {
    $user = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);

    Collection::factory()->create(['account_id' => $user->account_id]);

    Log::factory()->create([
        'user_id' => $user->id,
        'action' => UserActionEnum::PersonalProfileUpdate->value,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard.index'));

    $response->assertOk();
    $response->assertSee('RG');
});

<?php

declare(strict_types=1);
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs user action', function () {
    $user = User::factory()->create([
        'first_name' => 'Chandler',
        'last_name' => 'Bing',
        'last_activity_at' => null,
    ]);
    LogUserAction::dispatch(
        user: $user,
        action: UserActionEnum::PersonalProfileUpdate,
    );

    $log = Log::query()->first();

    expect($log->getUserName())->toEqual('Chandler Bing');
    expect($log->action)->toEqual('user_profile_updated');
    expect($log->getTranslatedDescription())->toEqual('Updated their personal profile');

    expect($user->refresh()->last_activity_at->timestamp)->toEqualWithDelta(now()->timestamp, 1);
});

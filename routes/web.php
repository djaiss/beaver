<?php

declare(strict_types=1);

use App\Http\Controllers\App\Account\AccountController;
use App\Http\Controllers\App\Account\InvitationController;
use App\Http\Controllers\App\Account\MemberController;
use App\Http\Controllers\App\CollectionController;
use App\Http\Controllers\App\CollectionTypeCollectionController;
use App\Http\Controllers\App\CollectionTypeController;
use App\Http\Controllers\App\CustomFieldController;
use App\Http\Controllers\App\CustomFieldGroupController;
use App\Http\Controllers\App\CustomFieldGroupFieldController;
use App\Http\Controllers\App\CustomFieldGroupOrderController;
use App\Http\Controllers\App\CustomFieldOrderController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\LocationController;
use App\Http\Controllers\App\Settings\ApiKeyController;
use App\Http\Controllers\App\Settings\AutoDeleteUserController;
use App\Http\Controllers\App\Settings\EmailSentController;
use App\Http\Controllers\App\Settings\LogController;
use App\Http\Controllers\App\Settings\PasswordController;
use App\Http\Controllers\App\Settings\RecoveryCodeController;
use App\Http\Controllers\App\Settings\SecurityController;
use App\Http\Controllers\App\Settings\SettingsController;
use App\Http\Controllers\App\Settings\TwoFAController;
use App\Http\Controllers\App\Settings\UserController;
use App\Http\Controllers\App\Settings\WebhookController;
use App\Http\Controllers\App\TagController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/marketing.php';

Route::put('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware(['auth', 'verified', 'throttle:60,1', 'set.locale'])->group(function (): void {
    // dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // placeholder sections for the future collection domain
    Route::get('collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::get('collections/{collection}', [CollectionController::class, 'show'])->where('collection', '[1-9][0-9]*')->name('collections.show');
    Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('search', fn () => view('app._placeholder', ['title' => __('Search'), 'body' => __('Search across everything in your account. This is coming soon.')]))->name('search.index');

    // collections — owners and editors may create new collections
    Route::middleware(['editor'])->group(function (): void {
        Route::get('collections/new', [CollectionController::class, 'new'])->name('collections.new');
        Route::post('collections', [CollectionController::class, 'create'])->name('collections.create');
    });

    // locations — owners and editors may create, update and delete locations
    Route::middleware(['editor'])->group(function (): void {
        Route::post('locations', [LocationController::class, 'create'])->name('locations.create');
        Route::put('locations/{location}', [LocationController::class, 'update'])->where('location', '[1-9][0-9]*')->name('locations.update');
        Route::delete('locations/{location}', [LocationController::class, 'destroy'])->where('location', '[1-9][0-9]*')->name('locations.destroy');
    });

    // personal profile — each user manages their own (any authenticated user)
    Route::get('profile', [SettingsController::class, 'index'])->name('profile.index');
    Route::put('profile', [SettingsController::class, 'update'])->name('profile.update');
    Route::get('profile/logs', [LogController::class, 'index'])->name('profile.logs.index');
    Route::get('profile/emails', [EmailSentController::class, 'index'])->name('profile.emails.index');

    // profile: security
    Route::get('profile/security', [SecurityController::class, 'index'])->name('profile.security.index');
    Route::put('profile/security/password', [PasswordController::class, 'update'])->name('profile.security.password.update');
    Route::get('profile/security/2fa/new', [TwoFAController::class, 'new'])->name('profile.security.2fa.new');
    Route::post('profile/security/2fa', [TwoFAController::class, 'create'])->name('profile.security.2fa.create');
    Route::delete('profile/security/2fa', [TwoFAController::class, 'destroy'])->name('profile.security.2fa.destroy');
    Route::get('profile/security/recovery-codes', [RecoveryCodeController::class, 'show'])->name('profile.security.recoverycodes.show');
    Route::put('profile/security/auto-delete-account', [AutoDeleteUserController::class, 'update'])->name('profile.security.auto-delete.update');

    // profile: api keys
    Route::get('profile/api-keys/new', [ApiKeyController::class, 'new'])->name('profile.api-keys.new');
    Route::post('profile/api-keys', [ApiKeyController::class, 'create'])->name('profile.api-keys.create');
    Route::delete('profile/api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('profile.api-keys.destroy');

    // profile: webhooks
    Route::get('profile/webhooks', [WebhookController::class, 'index'])->name('profile.webhooks.index');
    Route::get('profile/webhooks/new', [WebhookController::class, 'new'])->name('profile.webhooks.new');
    Route::post('profile/webhooks', [WebhookController::class, 'create'])->name('profile.webhooks.create');
    Route::delete('profile/webhooks/{webhookEndpoint}', [WebhookController::class, 'destroy'])->where('webhookEndpoint', '[1-9][0-9]*')->name('profile.webhooks.destroy');

    // profile: danger zone (delete your own user)
    Route::get('profile/user', [UserController::class, 'index'])->name('profile.user.index');
    Route::delete('profile/user', [UserController::class, 'destroy'])->name('profile.user.destroy');

    // account settings — the account and its members (owners only)
    Route::middleware(['owner'])->group(function (): void {
        Route::get('settings', [AccountController::class, 'index'])->name('settings.index');
        Route::put('settings', [AccountController::class, 'update'])->name('settings.update');
        Route::delete('settings', [AccountController::class, 'destroy'])->name('settings.destroy');

        Route::get('settings/members', [MemberController::class, 'index'])->name('settings.members.index');
        Route::post('settings/members', [MemberController::class, 'create'])->name('settings.members.create');
        Route::put('settings/members/{userId}', [MemberController::class, 'update'])->where('userId', '[1-9][0-9]*')->name('settings.members.update');
        Route::delete('settings/members/{userId}', [MemberController::class, 'destroy'])->where('userId', '[1-9][0-9]*')->name('settings.members.destroy');
    });

    // account settings: collection types — owners and editors define the custom fields available on items
    Route::middleware(['editor'])->group(function (): void {
        Route::get('settings/types', [CollectionTypeController::class, 'index'])->name('settings.types.index');
        Route::post('settings/types', [CollectionTypeController::class, 'create'])->name('settings.types.create');
        Route::get('settings/types/{collectionType}/edit', [CollectionTypeController::class, 'edit'])->where('collectionType', '[1-9][0-9]*')->name('settings.types.edit');
        Route::put('settings/types/{collectionType}', [CollectionTypeController::class, 'update'])->where('collectionType', '[1-9][0-9]*')->name('settings.types.update');
        Route::delete('settings/types/{collectionType}', [CollectionTypeController::class, 'destroy'])->where('collectionType', '[1-9][0-9]*')->name('settings.types.destroy');

        // a type's custom fields and the collections that may use it (edited inline, saved as you go)
        Route::post('settings/types/{collectionType}/fields', [CustomFieldController::class, 'create'])->where('collectionType', '[1-9][0-9]*')->name('settings.types.fields.create');
        Route::put('settings/types/{collectionType}/fields/{customField}', [CustomFieldController::class, 'update'])->where(['collectionType' => '[1-9][0-9]*', 'customField' => '[1-9][0-9]*'])->name('settings.types.fields.update');
        Route::delete('settings/types/{collectionType}/fields/{customField}', [CustomFieldController::class, 'destroy'])->where(['collectionType' => '[1-9][0-9]*', 'customField' => '[1-9][0-9]*'])->name('settings.types.fields.destroy');
        Route::put('settings/types/{collectionType}/fields/{customField}/order', [CustomFieldOrderController::class, 'update'])->where(['collectionType' => '[1-9][0-9]*', 'customField' => '[1-9][0-9]*'])->name('settings.types.fields.order.update');
        // the groups a type's fields can be organised into
        Route::post('settings/types/{collectionType}/groups', [CustomFieldGroupController::class, 'create'])->where('collectionType', '[1-9][0-9]*')->name('settings.types.groups.create');
        Route::put('settings/types/{collectionType}/groups/{group}', [CustomFieldGroupController::class, 'update'])->where(['collectionType' => '[1-9][0-9]*', 'group' => '[1-9][0-9]*'])->name('settings.types.groups.update');
        Route::delete('settings/types/{collectionType}/groups/{group}', [CustomFieldGroupController::class, 'destroy'])->where(['collectionType' => '[1-9][0-9]*', 'group' => '[1-9][0-9]*'])->name('settings.types.groups.destroy');
        Route::put('settings/types/{collectionType}/groups/{group}/order', [CustomFieldGroupOrderController::class, 'update'])->where(['collectionType' => '[1-9][0-9]*', 'group' => '[1-9][0-9]*'])->name('settings.types.groups.order.update');
        Route::post('settings/types/{collectionType}/groups/{group}/fields', [CustomFieldGroupFieldController::class, 'create'])->where(['collectionType' => '[1-9][0-9]*', 'group' => '[1-9][0-9]*'])->name('settings.types.groups.fields.create');

        Route::put('settings/types/{collectionType}/collections', [CollectionTypeCollectionController::class, 'update'])->where('collectionType', '[1-9][0-9]*')->name('settings.types.collections.update');
    });

    // account settings: tags — owners and editors define the labels items can carry
    Route::middleware(['editor'])->group(function (): void {
        Route::get('settings/tags', [TagController::class, 'index'])->name('settings.tags.index');
        Route::post('settings/tags', [TagController::class, 'create'])->name('settings.tags.create');
        Route::put('settings/tags/{tag}', [TagController::class, 'update'])->where('tag', '[1-9][0-9]*')->name('settings.tags.update');
        Route::delete('settings/tags/{tag}', [TagController::class, 'destroy'])->where('tag', '[1-9][0-9]*')->name('settings.tags.destroy');
    });
});

// invitations can be viewed and accepted by guests as well as logged-in users
Route::middleware(['set.locale'])->group(function (): void {
    Route::get('invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('invitations/{token}/accept', [InvitationController::class, 'create'])->name('invitations.create');
});

require __DIR__.'/auth.php';

<?php

declare(strict_types=1);

use App\Http\Controllers\App\Account\AccountController;
use App\Http\Controllers\App\Account\InvitationController;
use App\Http\Controllers\App\Account\MemberController;
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
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/marketing.php';

Route::put('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware(['auth', 'verified', 'throttle:60,1', 'set.locale'])->group(function (): void {
    // accounts
    Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('accounts/new', [AccountController::class, 'new'])->name('accounts.new');
    Route::post('accounts', [AccountController::class, 'create'])->name('accounts.create');

    Route::middleware(['account'])->where(['accountId' => '[1-9][0-9]*'])->group(function (): void {
        Route::get('accounts/{accountId}', [AccountController::class, 'show'])->name('accounts.show');

        Route::middleware(['account.owner'])->group(function (): void {
            Route::put('accounts/{accountId}', [AccountController::class, 'update'])->name('accounts.update');
            Route::delete('accounts/{accountId}', [AccountController::class, 'destroy'])->name('accounts.destroy');

            // members
            Route::get('accounts/{accountId}/members', [MemberController::class, 'index'])->name('accounts.members.index');
            Route::post('accounts/{accountId}/members', [MemberController::class, 'create'])->name('accounts.members.create');
            Route::put('accounts/{accountId}/members/{memberId}', [MemberController::class, 'update'])->where('memberId', '[1-9][0-9]*')->name('accounts.members.update');
            Route::delete('accounts/{accountId}/members/{memberId}', [MemberController::class, 'destroy'])->where('memberId', '[1-9][0-9]*')->name('accounts.members.destroy');
        });
    });

    // settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings/profile', [SettingsController::class, 'update'])->name('settings.profile.update');

    // log dedicated page
    Route::get('settings/logs', [LogController::class, 'index'])->name('settings.logs.index');

    // emails dedicated page
    Route::get('settings/emails', [EmailSentController::class, 'index'])->name('settings.emails.index');

    // security
    Route::get('settings/security', [SecurityController::class, 'index'])->name('settings.security.index');
    Route::put('settings/security/password', [PasswordController::class, 'update'])->name('settings.security.password.update');

    // 2fa
    Route::get('settings/security/2fa/new', [TwoFAController::class, 'new'])->name('settings.security.2fa.new');
    Route::post('settings/security/2fa', [TwoFAController::class, 'create'])->name('settings.security.2fa.create');
    Route::delete('settings/security/2fa', [TwoFAController::class, 'destroy'])->name('settings.security.2fa.destroy');
    Route::get('settings/security/recovery-codes', [RecoveryCodeController::class, 'show'])->name('settings.security.recoverycodes.show');

    // auto delete account
    Route::put('settings/security/auto-delete-account', [AutoDeleteUserController::class, 'update'])->name('settings.security.auto-delete.update');

    // api
    Route::get('settings/api-keys/new', [ApiKeyController::class, 'new'])->name('settings.api-keys.new');
    Route::post('settings/api-keys', [ApiKeyController::class, 'create'])->name('settings.api-keys.create');
    Route::delete('settings/api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('settings.api-keys.destroy');

    // webhooks
    Route::get('settings/webhooks', [WebhookController::class, 'index'])->name('settings.webhooks.index');
    Route::get('settings/webhooks/new', [WebhookController::class, 'new'])->name('settings.webhooks.new');
    Route::post('settings/webhooks', [WebhookController::class, 'create'])->name('settings.webhooks.create');
    Route::delete('settings/webhooks/{webhookEndpoint}', [WebhookController::class, 'destroy'])->where('webhookEndpoint', '[1-9][0-9]*')->name('settings.webhooks.destroy');

    // user
    Route::get('settings/user', [UserController::class, 'index'])->name('settings.user.index');
    Route::delete('settings/user', [UserController::class, 'destroy'])->name('settings.user.destroy');
});

// invitations can be viewed and accepted by guests as well as logged-in users
Route::middleware(['set.locale'])->group(function (): void {
    Route::get('invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('invitations/{token}/accept', [InvitationController::class, 'create'])->name('invitations.create');
});

require __DIR__.'/auth.php';

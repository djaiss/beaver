<?php

declare(strict_types=1);

use App\Http\Controllers\App\Settings\AccountController;
use App\Http\Controllers\App\Settings\ApiKeyController;
use App\Http\Controllers\App\Settings\AutoDeleteAccountController;
use App\Http\Controllers\App\Settings\EmailSentController;
use App\Http\Controllers\App\Settings\LogController;
use App\Http\Controllers\App\Settings\PasswordController;
use App\Http\Controllers\App\Settings\RecoveryCodeController;
use App\Http\Controllers\App\Settings\SecurityController;
use App\Http\Controllers\App\Settings\SettingsController;
use App\Http\Controllers\App\Settings\TwoFAController;
use App\Http\Controllers\App\Vault\Adminland\AdminlandController;
use App\Http\Controllers\App\Vault\Adminland\AdminlandManageController;
use App\Http\Controllers\App\Vault\JoinVaultController;
use App\Http\Controllers\App\Vault\VaultController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/marketing.php';

Route::put('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware(['auth', 'verified', 'throttle:60,1', 'set.locale'])->group(function (): void {
    Route::get('vaults', [VaultController::class, 'index'])->name('vault.index');

    // create
    Route::get('vaults/create', [VaultController::class, 'create'])->name('vault.create');
    Route::post('vaults', [VaultController::class, 'store'])->name('vault.store');

    // join
    Route::get('vaults/join', [JoinVaultController::class, 'create'])->name('vault.join.create');
    Route::post('vaults/join', [JoinVaultController::class, 'store'])->name('vault.join.store');

    Route::middleware(['vault'])->group(function (): void {
        Route::get('vaults/{vaultId}', [VaultController::class, 'show'])->name('vault.show');

        // adminland
        Route::middleware(['vault.adminland'])->prefix('vaults/{vaultId}/adminland')->group(function (): void {
            Route::get('', [AdminlandController::class, 'index'])->name('vault.adminland.index');
            Route::put('', [AdminlandController::class, 'update'])->name('vault.adminland.update');
            Route::get('manage', [AdminlandManageController::class, 'index'])->name('vault.adminland.manage.index');
            Route::delete('manage', [AdminlandManageController::class, 'destroy'])->name('vault.adminland.manage.destroy');
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
    Route::get('settings/security/2fa/create', [TwoFAController::class, 'create'])->name('settings.security.2fa.create');
    Route::post('settings/security/2fa', [TwoFAController::class, 'store'])->name('settings.security.2fa.store');
    Route::delete('settings/security/2fa', [TwoFAController::class, 'destroy'])->name('settings.security.2fa.destroy');
    Route::get('settings/security/recovery-codes', [RecoveryCodeController::class, 'show'])->name('settings.security.recoverycodes.show');

    // auto delete account
    Route::put('settings/security/auto-delete-account', [AutoDeleteAccountController::class, 'update'])->name('settings.security.auto-delete.update');

    // api
    Route::get('settings/api-keys/create', [ApiKeyController::class, 'create'])->name('settings.api-keys.create');
    Route::post('settings/api-keys', [ApiKeyController::class, 'store'])->name('settings.api-keys.store');
    Route::delete('settings/api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('settings.api-keys.destroy');

    // account
    Route::get('settings/account', [AccountController::class, 'index'])->name('settings.account.index');
    Route::delete('settings/account', [AccountController::class, 'destroy'])->name('settings.account.destroy');
});

require __DIR__.'/auth.php';

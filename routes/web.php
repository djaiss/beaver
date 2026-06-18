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
use App\Http\Controllers\App\Vault\Adminland\AdminlandGenderController;
use App\Http\Controllers\App\Vault\Adminland\AdminlandGenderPositionController;
use App\Http\Controllers\App\Vault\Adminland\AdminlandManageController;
use App\Http\Controllers\App\Vault\Adminland\AdminlandRelationshipTypeCategoryController;
use App\Http\Controllers\App\Vault\Adminland\AdminlandRelationshipTypeController;
use App\Http\Controllers\App\Vault\Adminland\AdminlandRelationshipTypePositionController;
use App\Http\Controllers\App\Vault\JoinVaultController;
use App\Http\Controllers\App\Vault\PersonController;
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

    Route::middleware(['vault'])->where(['vaultId' => '[1-9][0-9]*'])->group(function (): void {
        Route::get('vaults/{vaultId}', [VaultController::class, 'show'])->name('vault.show');

        // persons
        Route::view('vaults/{vaultId}/persons', 'app.vault.person.index')->name('vault.person.index');
        Route::get('vaults/{vaultId}/persons/new', [PersonController::class, 'new'])->name('vault.person.new');
        Route::post('vaults/{vaultId}/persons', [PersonController::class, 'create'])->name('vault.person.create');

        // adminland
        Route::middleware(['vault.adminland'])->prefix('vaults/{vaultId}/adminland')->group(function (): void {
            Route::get('', [AdminlandController::class, 'index'])->name('vault.adminland.index');
            Route::put('', [AdminlandController::class, 'update'])->name('vault.adminland.update');

            // genders
            Route::get('genders/new', [AdminlandGenderController::class, 'new'])->name('vault.adminland.genders.new');
            Route::post('genders', [AdminlandGenderController::class, 'create'])->name('vault.adminland.genders.create');
            Route::get('genders/{gender}/edit', [AdminlandGenderController::class, 'edit'])->where('gender', '[1-9][0-9]*')->name('vault.adminland.genders.edit');
            Route::put('genders/{gender}', [AdminlandGenderController::class, 'update'])->where('gender', '[1-9][0-9]*')->name('vault.adminland.genders.update');
            Route::put('genders/{gender}/position', [AdminlandGenderPositionController::class, 'update'])->where('gender', '[1-9][0-9]*')->name('vault.adminland.genders.position.update');
            Route::delete('genders/{gender}', [AdminlandGenderController::class, 'destroy'])->where('gender', '[1-9][0-9]*')->name('vault.adminland.genders.destroy');

            // relationship type categories
            Route::get('relationship-type-categories/new', [AdminlandRelationshipTypeCategoryController::class, 'new'])->name('vault.adminland.relationship_type_categories.new');
            Route::post('relationship-type-categories', [AdminlandRelationshipTypeCategoryController::class, 'create'])->name('vault.adminland.relationship_type_categories.store');
            Route::get('relationship-type-categories/{relationshipTypeCategory}/edit', [AdminlandRelationshipTypeCategoryController::class, 'edit'])->where('relationshipTypeCategory', '[1-9][0-9]*')->name('vault.adminland.relationship_type_categories.edit');
            Route::put('relationship-type-categories/{relationshipTypeCategory}', [AdminlandRelationshipTypeCategoryController::class, 'update'])->where('relationshipTypeCategory', '[1-9][0-9]*')->name('vault.adminland.relationship_type_categories.update');
            Route::delete('relationship-type-categories/{relationshipTypeCategory}', [AdminlandRelationshipTypeCategoryController::class, 'destroy'])->where('relationshipTypeCategory', '[1-9][0-9]*')->name('vault.adminland.relationship_type_categories.destroy');

            // relationship types
            Route::get('relationship-type-categories/{relationshipTypeCategory}/relationship-types/new', [AdminlandRelationshipTypeController::class, 'new'])->where('relationshipTypeCategory', '[1-9][0-9]*')->name('vault.adminland.relationship_types.new');
            Route::post('relationship-type-categories/{relationshipTypeCategory}/relationship-types', [AdminlandRelationshipTypeController::class, 'create'])->where('relationshipTypeCategory', '[1-9][0-9]*')->name('vault.adminland.relationship_types.store');
            Route::get('relationship-type-categories/{relationshipTypeCategory}/relationship-types/{relationshipType}/edit', [AdminlandRelationshipTypeController::class, 'edit'])->where(['relationshipTypeCategory' => '[1-9][0-9]*', 'relationshipType' => '[1-9][0-9]*'])->name('vault.adminland.relationship_types.edit');
            Route::put('relationship-type-categories/{relationshipTypeCategory}/relationship-types/{relationshipType}', [AdminlandRelationshipTypeController::class, 'update'])->where(['relationshipTypeCategory' => '[1-9][0-9]*', 'relationshipType' => '[1-9][0-9]*'])->name('vault.adminland.relationship_types.update');
            Route::put('relationship-type-categories/{relationshipTypeCategory}/relationship-types/{relationshipType}/position', [AdminlandRelationshipTypePositionController::class, 'update'])->where(['relationshipTypeCategory' => '[1-9][0-9]*', 'relationshipType' => '[1-9][0-9]*'])->name('vault.adminland.relationship_types.position.update');
            Route::delete('relationship-type-categories/{relationshipTypeCategory}/relationship-types/{relationshipType}', [AdminlandRelationshipTypeController::class, 'destroy'])->where(['relationshipTypeCategory' => '[1-9][0-9]*', 'relationshipType' => '[1-9][0-9]*'])->name('vault.adminland.relationship_types.destroy');

            // manage vault
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

<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Administration\AdministrationApiController;
use App\Http\Controllers\Api\Administration\AdministrationLogsController;
use App\Http\Controllers\Api\Administration\MeController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\Vault\Adminland\AdminlandGenderController;
use App\Http\Controllers\Api\VaultController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function (): void {
    Route::get('health', [HealthController::class, 'show'])->middleware('throttle:60,1');

    // login
    Route::post('login', [LoginController::class, 'store'])->name('login');

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
        // logout
        Route::delete('logout', [LoginController::class, 'destroy'])->name('logout');

        // logged user
        Route::get('me', [MeController::class, 'show'])->name('me');
        Route::put('me', [MeController::class, 'update'])->name('me.update');

        // vaults
        Route::post('vaults', [VaultController::class, 'create'])->name('vault.create');
        Route::get('vaults', [VaultController::class, 'index'])->name('vault.index');
        Route::middleware(['vault.api'])->where(['id' => '[1-9][0-9]*'])->prefix('vaults/{id}')->group(function (): void {
            Route::get('', [VaultController::class, 'show'])->name('vault.show');
            Route::put('', [VaultController::class, 'update'])->name('vault.update');
            Route::delete('', [VaultController::class, 'destroy'])->name('vault.destroy');

            // genders
            Route::get('genders', [AdminlandGenderController::class, 'index'])->name('vault.gender');
            Route::get('genders/{gender}', [AdminlandGenderController::class, 'show'])->where('gender', '[1-9][0-9]*')->name('vault.gender.show');
            Route::post('genders', [AdminlandGenderController::class, 'create'])->name('vault.gender.create');
            Route::put('genders/{gender}', [AdminlandGenderController::class, 'update'])->where('gender', '[1-9][0-9]*')->name('vault.gender.update');
            Route::delete('genders/{gender}', [AdminlandGenderController::class, 'destroy'])->where('gender', '[1-9][0-9]*')->name('vault.gender.destroy');
        });

        // api keys
        Route::get('administration/api', [AdministrationApiController::class, 'index'])->name('administration.api');
        Route::get('administration/api/{id}', [AdministrationApiController::class, 'show'])->where('id', '[1-9][0-9]*')->name('administration.api.show');
        Route::post('administration/api', [AdministrationApiController::class, 'create'])->name('administration.api.create');
        Route::delete('administration/api/{id}', [AdministrationApiController::class, 'destroy'])->where('id', '[1-9][0-9]*')->name('administration.api.destroy');

        // logs
        Route::get('administration/logs', [AdministrationLogsController::class, 'index'])->name('administration.logs');
        Route::get('administration/logs/{log}', [AdministrationLogsController::class, 'show'])->where('log', '[1-9][0-9]*')->name('administration.logs.show');
    });
});

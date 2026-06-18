<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Administration\AdministrationApiController;
use App\Http\Controllers\Api\Administration\AdministrationLogsController;
use App\Http\Controllers\Api\Administration\MeController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\HealthController;
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
        Route::middleware(['vault.api'])->group(function (): void {
            Route::get('vaults/{id}', [VaultController::class, 'show'])->where('id', '[0-9]+')->name('vault.show');
            Route::put('vaults/{id}', [VaultController::class, 'update'])->where('id', '[0-9]+')->name('vault.update');
            Route::delete('vaults/{id}', [VaultController::class, 'destroy'])->where('id', '[0-9]+')->name('vault.destroy');
        });

        // api keys
        Route::get('administration/api', [AdministrationApiController::class, 'index'])->name('administration.api');
        Route::get('administration/api/{id}', [AdministrationApiController::class, 'show'])->where('id', '[0-9]+')->name('administration.api.show');
        Route::post('administration/api', [AdministrationApiController::class, 'create'])->name('administration.api.create');
        Route::delete('administration/api/{id}', [AdministrationApiController::class, 'destroy'])->where('id', '[0-9]+')->name('administration.api.destroy');

        // logs
        Route::get('administration/logs', [AdministrationLogsController::class, 'index'])->name('administration.logs');
        Route::get('administration/logs/{log}', [AdministrationLogsController::class, 'show'])->where('log', '[0-9]+')->name('administration.logs.show');
    });
});

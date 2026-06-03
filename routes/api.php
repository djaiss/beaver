<?php

declare(strict_types=1);

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\VaultController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function (): void {
    Route::get('health', [HealthController::class, 'show'])->middleware('throttle:60,1');

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
        // vaults
        Route::post('vaults', [VaultController::class, 'create'])->name('vault.create');
        Route::get('vaults', [VaultController::class, 'index'])->name('vault.index');
        Route::middleware(['vault.api'])->group(function (): void {
            Route::get('vaults/{id}', [VaultController::class, 'show'])->name('vault.show');
            Route::put('vaults/{id}', [VaultController::class, 'update'])->name('vault.update');
            Route::delete('vaults/{id}', [VaultController::class, 'destroy'])->name('vault.destroy');
        });
    });
});

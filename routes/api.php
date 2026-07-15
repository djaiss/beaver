<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Administration\AdministrationApiController;
use App\Http\Controllers\Api\Administration\AdministrationLogsController;
use App\Http\Controllers\Api\Administration\EmailSentController;
use App\Http\Controllers\Api\Administration\MeController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegistrationController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\CollectionTypeCollectionController;
use App\Http\Controllers\Api\CollectionTypeController;
use App\Http\Controllers\Api\ConditionController;
use App\Http\Controllers\Api\CustomFieldController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function (): void {
    Route::get('health', [HealthController::class, 'show'])->middleware('throttle:60,1');

    // registration
    Route::post('register', [RegistrationController::class, 'store'])->middleware('throttle:6,1')->name('register');

    // login
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:6,1')->name('login');

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
        // logout
        Route::delete('logout', [LoginController::class, 'destroy'])->name('logout');

        // logged user
        Route::get('me', [MeController::class, 'show'])->name('me');
        Route::put('me', [MeController::class, 'update'])->name('me.update');

        // collections
        Route::get('collections', [CollectionController::class, 'index'])->name('collections');
        Route::get('collections/{collection}', [CollectionController::class, 'show'])->where('collection', '[1-9][0-9]*')->name('collections.show');
        Route::post('collections', [CollectionController::class, 'create'])->name('collections.create');
        Route::put('collections/{collection}', [CollectionController::class, 'update'])->where('collection', '[1-9][0-9]*')->name('collections.update');
        Route::delete('collections/{collection}', [CollectionController::class, 'destroy'])->where('collection', '[1-9][0-9]*')->name('collections.destroy');

        // collection types
        Route::get('collection-types', [CollectionTypeController::class, 'index'])->name('collectionTypes');
        Route::get('collection-types/{collectionType}', [CollectionTypeController::class, 'show'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.show');
        Route::post('collection-types', [CollectionTypeController::class, 'create'])->name('collectionTypes.create');
        Route::put('collection-types/{collectionType}', [CollectionTypeController::class, 'update'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.update');
        Route::delete('collection-types/{collectionType}', [CollectionTypeController::class, 'destroy'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.destroy');

        // the collections a type applies to
        Route::put('collection-types/{collectionType}/collections', [CollectionTypeCollectionController::class, 'update'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.collections.update');

        // custom fields
        Route::get('collection-types/{collectionType}/custom-fields', [CustomFieldController::class, 'index'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.customFields');
        Route::get('collection-types/{collectionType}/custom-fields/{customField}', [CustomFieldController::class, 'show'])->where(['collectionType' => '[1-9][0-9]*', 'customField' => '[1-9][0-9]*'])->name('collectionTypes.customFields.show');
        Route::post('collection-types/{collectionType}/custom-fields', [CustomFieldController::class, 'create'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.customFields.create');
        Route::put('collection-types/{collectionType}/custom-fields/{customField}', [CustomFieldController::class, 'update'])->where(['collectionType' => '[1-9][0-9]*', 'customField' => '[1-9][0-9]*'])->name('collectionTypes.customFields.update');
        Route::delete('collection-types/{collectionType}/custom-fields/{customField}', [CustomFieldController::class, 'destroy'])->where(['collectionType' => '[1-9][0-9]*', 'customField' => '[1-9][0-9]*'])->name('collectionTypes.customFields.destroy');

        // locations
        Route::get('locations', [LocationController::class, 'index'])->name('locations');
        Route::get('locations/{location}', [LocationController::class, 'show'])->where('location', '[1-9][0-9]*')->name('locations.show');
        Route::post('locations', [LocationController::class, 'create'])->name('locations.create');
        Route::put('locations/{location}', [LocationController::class, 'update'])->where('location', '[1-9][0-9]*')->name('locations.update');
        Route::delete('locations/{location}', [LocationController::class, 'destroy'])->where('location', '[1-9][0-9]*')->name('locations.destroy');

        // conditions
        Route::get('conditions', [ConditionController::class, 'index'])->name('conditions');
        Route::get('conditions/{condition}', [ConditionController::class, 'show'])->where('condition', '[1-9][0-9]*')->name('conditions.show');
        Route::post('conditions', [ConditionController::class, 'create'])->name('conditions.create');
        Route::put('conditions/{condition}', [ConditionController::class, 'update'])->where('condition', '[1-9][0-9]*')->name('conditions.update');
        Route::delete('conditions/{condition}', [ConditionController::class, 'destroy'])->where('condition', '[1-9][0-9]*')->name('conditions.destroy');

        // tags
        Route::get('tags', [TagController::class, 'index'])->name('tags');
        Route::get('tags/{tag}', [TagController::class, 'show'])->where('tag', '[1-9][0-9]*')->name('tags.show');
        Route::post('tags', [TagController::class, 'create'])->name('tags.create');
        Route::put('tags/{tag}', [TagController::class, 'update'])->where('tag', '[1-9][0-9]*')->name('tags.update');
        Route::delete('tags/{tag}', [TagController::class, 'destroy'])->where('tag', '[1-9][0-9]*')->name('tags.destroy');

        // api keys
        Route::get('administration/api', [AdministrationApiController::class, 'index'])->name('administration.api');
        Route::get('administration/api/{id}', [AdministrationApiController::class, 'show'])->where('id', '[1-9][0-9]*')->name('administration.api.show');
        Route::post('administration/api', [AdministrationApiController::class, 'create'])->name('administration.api.create');
        Route::delete('administration/api/{id}', [AdministrationApiController::class, 'destroy'])->where('id', '[1-9][0-9]*')->name('administration.api.destroy');

        // logs
        Route::get('administration/logs', [AdministrationLogsController::class, 'index'])->name('administration.logs');
        Route::get('administration/logs/{log}', [AdministrationLogsController::class, 'show'])->where('log', '[1-9][0-9]*')->name('administration.logs.show');

        // emails
        Route::get('administration/emails', [EmailSentController::class, 'index'])->name('administration.emails');
        Route::get('administration/emails/{email}', [EmailSentController::class, 'show'])->where('email', '[1-9][0-9]*')->name('administration.emails.show');
    });
});

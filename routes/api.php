<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Administration\AdministrationApiController;
use App\Http\Controllers\Api\Administration\AdministrationLogsController;
use App\Http\Controllers\Api\Administration\EmailSentController;
use App\Http\Controllers\Api\Administration\MeController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegistrationController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\CollectionTypeCollectionController;
use App\Http\Controllers\Api\CollectionTypeController;
use App\Http\Controllers\Api\CollectionTypeExportController;
use App\Http\Controllers\Api\ConditionController;
use App\Http\Controllers\Api\CopyController;
use App\Http\Controllers\Api\CustomFieldController;
use App\Http\Controllers\Api\CustomFieldGroupController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ItemLogController;
use App\Http\Controllers\Api\ItemPhotoController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\SeriesController;
use App\Http\Controllers\Api\SetController;
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

        // the categories that group items within a collection
        Route::get('collections/{collection}/categories', [CategoryController::class, 'index'])->where('collection', '[1-9][0-9]*')->name('collections.categories');
        Route::get('collections/{collection}/categories/{category}', [CategoryController::class, 'show'])->where(['collection' => '[1-9][0-9]*', 'category' => '[1-9][0-9]*'])->name('collections.categories.show');
        Route::post('collections/{collection}/categories', [CategoryController::class, 'create'])->where('collection', '[1-9][0-9]*')->name('collections.categories.create');
        Route::put('collections/{collection}/categories/{category}', [CategoryController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'category' => '[1-9][0-9]*'])->name('collections.categories.update');
        Route::delete('collections/{collection}/categories/{category}', [CategoryController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'category' => '[1-9][0-9]*'])->name('collections.categories.destroy');

        // the items catalogued within a collection
        Route::get('collections/{collection}/items', [ItemController::class, 'index'])->where('collection', '[1-9][0-9]*')->name('collections.items');
        Route::get('collections/{collection}/items/{item}', [ItemController::class, 'show'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*'])->name('collections.items.show');
        Route::post('collections/{collection}/items', [ItemController::class, 'create'])->where('collection', '[1-9][0-9]*')->name('collections.items.create');
        Route::put('collections/{collection}/items/{item}', [ItemController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*'])->name('collections.items.update');
        Route::delete('collections/{collection}/items/{item}', [ItemController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*'])->name('collections.items.destroy');

        // collection types
        Route::get('collection-types', [CollectionTypeController::class, 'index'])->name('collectionTypes');
        Route::get('collection-types/{collectionType}', [CollectionTypeController::class, 'show'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.show');
        Route::post('collection-types', [CollectionTypeController::class, 'create'])->name('collectionTypes.create');
        Route::put('collection-types/{collectionType}', [CollectionTypeController::class, 'update'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.update');
        Route::delete('collection-types/{collectionType}', [CollectionTypeController::class, 'destroy'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.destroy');

        // the type's schema, as a portable JSON document
        Route::get('collection-types/{collectionType}/export', [CollectionTypeExportController::class, 'show'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.export.show');

        // the collections a type applies to
        Route::put('collection-types/{collectionType}/collections', [CollectionTypeCollectionController::class, 'update'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.collections.update');

        // the groups a type's custom fields can be organised into
        Route::get('collection-types/{collectionType}/custom-field-groups', [CustomFieldGroupController::class, 'index'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.customFieldGroups');
        Route::get('collection-types/{collectionType}/custom-field-groups/{group}', [CustomFieldGroupController::class, 'show'])->where(['collectionType' => '[1-9][0-9]*', 'group' => '[1-9][0-9]*'])->name('collectionTypes.customFieldGroups.show');
        Route::post('collection-types/{collectionType}/custom-field-groups', [CustomFieldGroupController::class, 'create'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.customFieldGroups.create');
        Route::put('collection-types/{collectionType}/custom-field-groups/{group}', [CustomFieldGroupController::class, 'update'])->where(['collectionType' => '[1-9][0-9]*', 'group' => '[1-9][0-9]*'])->name('collectionTypes.customFieldGroups.update');
        Route::delete('collection-types/{collectionType}/custom-field-groups/{group}', [CustomFieldGroupController::class, 'destroy'])->where(['collectionType' => '[1-9][0-9]*', 'group' => '[1-9][0-9]*'])->name('collectionTypes.customFieldGroups.destroy');

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

        // sets
        Route::get('sets', [SetController::class, 'index'])->name('sets');
        Route::get('sets/{set}', [SetController::class, 'show'])->where('set', '[1-9][0-9]*')->name('sets.show');
        Route::post('sets', [SetController::class, 'create'])->name('sets.create');
        Route::put('sets/{set}', [SetController::class, 'update'])->where('set', '[1-9][0-9]*')->name('sets.update');
        Route::delete('sets/{set}', [SetController::class, 'destroy'])->where('set', '[1-9][0-9]*')->name('sets.destroy');

        // series
        Route::get('series', [SeriesController::class, 'index'])->name('series');
        Route::get('series/{series}', [SeriesController::class, 'show'])->where('series', '[1-9][0-9]*')->name('series.show');
        Route::post('series', [SeriesController::class, 'create'])->name('series.create');
        Route::put('series/{series}', [SeriesController::class, 'update'])->where('series', '[1-9][0-9]*')->name('series.update');
        Route::delete('series/{series}', [SeriesController::class, 'destroy'])->where('series', '[1-9][0-9]*')->name('series.destroy');

        // the physical copies owned of an item
        Route::get('items/{item}/copies', [CopyController::class, 'index'])->where('item', '[1-9][0-9]*')->name('items.copies');
        Route::get('items/{item}/copies/{copy}', [CopyController::class, 'show'])->where(['item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*'])->name('items.copies.show');
        Route::post('items/{item}/copies', [CopyController::class, 'create'])->where('item', '[1-9][0-9]*')->name('items.copies.create');
        Route::put('items/{item}/copies/{copy}', [CopyController::class, 'update'])->where(['item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*'])->name('items.copies.update');
        Route::delete('items/{item}/copies/{copy}', [CopyController::class, 'destroy'])->where(['item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*'])->name('items.copies.destroy');

        // the photos of an item
        Route::get('items/{item}/photos', [ItemPhotoController::class, 'index'])->where('item', '[1-9][0-9]*')->name('items.photos');
        Route::get('items/{item}/photos/{photo}', [ItemPhotoController::class, 'show'])->where(['item' => '[1-9][0-9]*', 'photo' => '[1-9][0-9]*'])->name('items.photos.show');
        Route::post('items/{item}/photos', [ItemPhotoController::class, 'create'])->where('item', '[1-9][0-9]*')->name('items.photos.create');
        Route::put('items/{item}/photos/{photo}/main', [ItemPhotoController::class, 'main'])->where(['item' => '[1-9][0-9]*', 'photo' => '[1-9][0-9]*'])->name('items.photos.main');
        Route::delete('items/{item}/photos/{photo}', [ItemPhotoController::class, 'destroy'])->where(['item' => '[1-9][0-9]*', 'photo' => '[1-9][0-9]*'])->name('items.photos.destroy');

        // the activity trail of an item, written by the app as actions happen
        Route::get('items/{item}/logs', [ItemLogController::class, 'index'])->where('item', '[1-9][0-9]*')->name('items.logs');
        Route::get('items/{item}/logs/{log}', [ItemLogController::class, 'show'])->where(['item' => '[1-9][0-9]*', 'log' => '[1-9][0-9]*'])->name('items.logs.show');

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

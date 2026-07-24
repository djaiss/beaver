<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Account\AccountController;
use App\Http\Controllers\Api\Account\InvitationController;
use App\Http\Controllers\Api\Account\MemberController;
use App\Http\Controllers\Api\Administration\AdministrationApiController;
use App\Http\Controllers\Api\Administration\AdministrationLogsController;
use App\Http\Controllers\Api\Administration\EmailSentController;
use App\Http\Controllers\Api\Administration\MeController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegistrationController;
use App\Http\Controllers\Api\Collection\CategoryController;
use App\Http\Controllers\Api\Collection\CollectionController;
use App\Http\Controllers\Api\Collection\ItemController;
use App\Http\Controllers\Api\Collection\ItemLogController;
use App\Http\Controllers\Api\Collection\ItemPhotoController;
use App\Http\Controllers\Api\Collection\ItemPhotoSelectionController;
use App\Http\Controllers\Api\Collection\ItemTagController;
use App\Http\Controllers\Api\Collection\StatisticsController;
use App\Http\Controllers\Api\CollectionType\CollectionTypeCollectionController;
use App\Http\Controllers\Api\CollectionType\CollectionTypeController;
use App\Http\Controllers\Api\CollectionType\CollectionTypeExportController;
use App\Http\Controllers\Api\CollectionType\CollectionTypeImportController;
use App\Http\Controllers\Api\CollectionType\CustomFieldController;
use App\Http\Controllers\Api\CollectionType\CustomFieldGroupController;
use App\Http\Controllers\Api\CollectionType\CustomFieldGroupOrderController;
use App\Http\Controllers\Api\CollectionType\CustomFieldOrderController;
use App\Http\Controllers\Api\Copy\CopyController;
use App\Http\Controllers\Api\Copy\CopyHistoryController;
use App\Http\Controllers\Api\Copy\DocumentController;
use App\Http\Controllers\Api\Copy\InsuranceRecordController;
use App\Http\Controllers\Api\Copy\LoanController;
use App\Http\Controllers\Api\Copy\LocationHistoryController;
use App\Http\Controllers\Api\Copy\MaintenanceRecordController;
use App\Http\Controllers\Api\Copy\ProvenanceEventController;
use App\Http\Controllers\Api\Copy\TransactionController;
use App\Http\Controllers\Api\Copy\ValuationController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\ItemConditionController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\SeriesController;
use App\Http\Controllers\Api\SetController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TrashController;
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

        // the aggregates behind the statistics screen of a collection
        Route::get('collections/{collection}/statistics', [StatisticsController::class, 'index'])->where('collection', '[1-9][0-9]*')->name('collections.statistics');

        // collection types
        Route::get('collection-types', [CollectionTypeController::class, 'index'])->name('collectionTypes');
        Route::get('collection-types/{collectionType}', [CollectionTypeController::class, 'show'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.show');
        Route::post('collection-types', [CollectionTypeController::class, 'create'])->name('collectionTypes.create');
        Route::put('collection-types/{collectionType}', [CollectionTypeController::class, 'update'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.update');
        Route::delete('collection-types/{collectionType}', [CollectionTypeController::class, 'destroy'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.destroy');

        // the type's schema, as a portable JSON document
        Route::get('collection-types/{collectionType}/export', [CollectionTypeExportController::class, 'show'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.export.show');

        // a type rebuilt from an exported JSON document
        Route::post('collection-types/import', [CollectionTypeImportController::class, 'create'])->name('collectionTypes.import.create');

        // the collections a type applies to
        Route::put('collection-types/{collectionType}/collections', [CollectionTypeCollectionController::class, 'update'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.collections.update');

        // the groups a type's custom fields can be organised into
        Route::get('collection-types/{collectionType}/custom-field-groups', [CustomFieldGroupController::class, 'index'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.customFieldGroups');
        Route::get('collection-types/{collectionType}/custom-field-groups/{group}', [CustomFieldGroupController::class, 'show'])->where(['collectionType' => '[1-9][0-9]*', 'group' => '[1-9][0-9]*'])->name('collectionTypes.customFieldGroups.show');
        Route::post('collection-types/{collectionType}/custom-field-groups', [CustomFieldGroupController::class, 'create'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.customFieldGroups.create');
        Route::put('collection-types/{collectionType}/custom-field-groups/{group}', [CustomFieldGroupController::class, 'update'])->where(['collectionType' => '[1-9][0-9]*', 'group' => '[1-9][0-9]*'])->name('collectionTypes.customFieldGroups.update');
        Route::delete('collection-types/{collectionType}/custom-field-groups/{group}', [CustomFieldGroupController::class, 'destroy'])->where(['collectionType' => '[1-9][0-9]*', 'group' => '[1-9][0-9]*'])->name('collectionTypes.customFieldGroups.destroy');

        // where a group sits among the type's groups
        Route::put('collection-types/{collectionType}/custom-field-groups/{group}/order', [CustomFieldGroupOrderController::class, 'update'])->where(['collectionType' => '[1-9][0-9]*', 'group' => '[1-9][0-9]*'])->name('collectionTypes.customFieldGroups.order.update');

        // custom fields
        Route::get('collection-types/{collectionType}/custom-fields', [CustomFieldController::class, 'index'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.customFields');
        Route::get('collection-types/{collectionType}/custom-fields/{customField}', [CustomFieldController::class, 'show'])->where(['collectionType' => '[1-9][0-9]*', 'customField' => '[1-9][0-9]*'])->name('collectionTypes.customFields.show');
        Route::post('collection-types/{collectionType}/custom-fields', [CustomFieldController::class, 'create'])->where('collectionType', '[1-9][0-9]*')->name('collectionTypes.customFields.create');
        Route::put('collection-types/{collectionType}/custom-fields/{customField}', [CustomFieldController::class, 'update'])->where(['collectionType' => '[1-9][0-9]*', 'customField' => '[1-9][0-9]*'])->name('collectionTypes.customFields.update');
        Route::delete('collection-types/{collectionType}/custom-fields/{customField}', [CustomFieldController::class, 'destroy'])->where(['collectionType' => '[1-9][0-9]*', 'customField' => '[1-9][0-9]*'])->name('collectionTypes.customFields.destroy');

        // where a field sits among its group's fields
        Route::put('collection-types/{collectionType}/custom-fields/{customField}/order', [CustomFieldOrderController::class, 'update'])->where(['collectionType' => '[1-9][0-9]*', 'customField' => '[1-9][0-9]*'])->name('collectionTypes.customFields.order.update');

        // locations
        Route::get('locations', [LocationController::class, 'index'])->name('locations');
        Route::get('locations/{location}', [LocationController::class, 'show'])->where('location', '[1-9][0-9]*')->name('locations.show');
        Route::post('locations', [LocationController::class, 'create'])->name('locations.create');
        Route::put('locations/{location}', [LocationController::class, 'update'])->where('location', '[1-9][0-9]*')->name('locations.update');
        Route::delete('locations/{location}', [LocationController::class, 'destroy'])->where('location', '[1-9][0-9]*')->name('locations.destroy');

        // item conditions
        Route::get('item-conditions', [ItemConditionController::class, 'index'])->name('itemConditions');
        Route::get('item-conditions/{itemCondition}', [ItemConditionController::class, 'show'])->where('itemCondition', '[1-9][0-9]*')->name('itemConditions.show');
        Route::post('item-conditions', [ItemConditionController::class, 'create'])->name('itemConditions.create');
        Route::put('item-conditions/{itemCondition}', [ItemConditionController::class, 'update'])->where('itemCondition', '[1-9][0-9]*')->name('itemConditions.update');
        Route::delete('item-conditions/{itemCondition}', [ItemConditionController::class, 'destroy'])->where('itemCondition', '[1-9][0-9]*')->name('itemConditions.destroy');

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

        // the transactions recorded against a copy
        Route::get('copies/{copy}/transactions', [TransactionController::class, 'index'])->where('copy', '[1-9][0-9]*')->name('copies.transactions');
        Route::get('copies/{copy}/transactions/{transaction}', [TransactionController::class, 'show'])->where(['copy' => '[1-9][0-9]*', 'transaction' => '[1-9][0-9]*'])->name('copies.transactions.show');
        Route::post('copies/{copy}/transactions', [TransactionController::class, 'create'])->where('copy', '[1-9][0-9]*')->name('copies.transactions.create');
        Route::put('copies/{copy}/transactions/{transaction}', [TransactionController::class, 'update'])->where(['copy' => '[1-9][0-9]*', 'transaction' => '[1-9][0-9]*'])->name('copies.transactions.update');
        Route::delete('copies/{copy}/transactions/{transaction}', [TransactionController::class, 'destroy'])->where(['copy' => '[1-9][0-9]*', 'transaction' => '[1-9][0-9]*'])->name('copies.transactions.destroy');

        // the documented story of a copy
        Route::get('copies/{copy}/provenance-events', [ProvenanceEventController::class, 'index'])->where('copy', '[1-9][0-9]*')->name('copies.provenanceEvents');
        Route::get('copies/{copy}/provenance-events/{provenanceEvent}', [ProvenanceEventController::class, 'show'])->where(['copy' => '[1-9][0-9]*', 'provenanceEvent' => '[1-9][0-9]*'])->name('copies.provenanceEvents.show');
        Route::post('copies/{copy}/provenance-events', [ProvenanceEventController::class, 'create'])->where('copy', '[1-9][0-9]*')->name('copies.provenanceEvents.create');
        Route::put('copies/{copy}/provenance-events/{provenanceEvent}', [ProvenanceEventController::class, 'update'])->where(['copy' => '[1-9][0-9]*', 'provenanceEvent' => '[1-9][0-9]*'])->name('copies.provenanceEvents.update');
        Route::delete('copies/{copy}/provenance-events/{provenanceEvent}', [ProvenanceEventController::class, 'destroy'])->where(['copy' => '[1-9][0-9]*', 'provenanceEvent' => '[1-9][0-9]*'])->name('copies.provenanceEvents.destroy');

        // what a copy is reckoned to be worth over time
        Route::get('copies/{copy}/valuations', [ValuationController::class, 'index'])->where('copy', '[1-9][0-9]*')->name('copies.valuations');
        Route::get('copies/{copy}/valuations/{valuation}', [ValuationController::class, 'show'])->where(['copy' => '[1-9][0-9]*', 'valuation' => '[1-9][0-9]*'])->name('copies.valuations.show');
        Route::post('copies/{copy}/valuations', [ValuationController::class, 'create'])->where('copy', '[1-9][0-9]*')->name('copies.valuations.create');
        Route::put('copies/{copy}/valuations/{valuation}', [ValuationController::class, 'update'])->where(['copy' => '[1-9][0-9]*', 'valuation' => '[1-9][0-9]*'])->name('copies.valuations.update');
        Route::delete('copies/{copy}/valuations/{valuation}', [ValuationController::class, 'destroy'])->where(['copy' => '[1-9][0-9]*', 'valuation' => '[1-9][0-9]*'])->name('copies.valuations.destroy');

        // the insurance coverage held against a copy
        Route::get('copies/{copy}/insurance-records', [InsuranceRecordController::class, 'index'])->where('copy', '[1-9][0-9]*')->name('copies.insuranceRecords');
        Route::get('copies/{copy}/insurance-records/{insuranceRecord}', [InsuranceRecordController::class, 'show'])->where(['copy' => '[1-9][0-9]*', 'insuranceRecord' => '[1-9][0-9]*'])->name('copies.insuranceRecords.show');
        Route::post('copies/{copy}/insurance-records', [InsuranceRecordController::class, 'create'])->where('copy', '[1-9][0-9]*')->name('copies.insuranceRecords.create');
        Route::put('copies/{copy}/insurance-records/{insuranceRecord}', [InsuranceRecordController::class, 'update'])->where(['copy' => '[1-9][0-9]*', 'insuranceRecord' => '[1-9][0-9]*'])->name('copies.insuranceRecords.update');
        Route::delete('copies/{copy}/insurance-records/{insuranceRecord}', [InsuranceRecordController::class, 'destroy'])->where(['copy' => '[1-9][0-9]*', 'insuranceRecord' => '[1-9][0-9]*'])->name('copies.insuranceRecords.destroy');

        // the maintenance records logged against a copy
        Route::get('copies/{copy}/maintenance-records', [MaintenanceRecordController::class, 'index'])->where('copy', '[1-9][0-9]*')->name('copies.maintenanceRecords');
        Route::get('copies/{copy}/maintenance-records/{maintenanceRecord}', [MaintenanceRecordController::class, 'show'])->where(['copy' => '[1-9][0-9]*', 'maintenanceRecord' => '[1-9][0-9]*'])->name('copies.maintenanceRecords.show');
        Route::post('copies/{copy}/maintenance-records', [MaintenanceRecordController::class, 'create'])->where('copy', '[1-9][0-9]*')->name('copies.maintenanceRecords.create');
        Route::put('copies/{copy}/maintenance-records/{maintenanceRecord}', [MaintenanceRecordController::class, 'update'])->where(['copy' => '[1-9][0-9]*', 'maintenanceRecord' => '[1-9][0-9]*'])->name('copies.maintenanceRecords.update');
        Route::delete('copies/{copy}/maintenance-records/{maintenanceRecord}', [MaintenanceRecordController::class, 'destroy'])->where(['copy' => '[1-9][0-9]*', 'maintenanceRecord' => '[1-9][0-9]*'])->name('copies.maintenanceRecords.destroy');

        // the account-wide list of loans, across every copy; the Loans section reads the same data
        Route::get('loans', [LoanController::class, 'all'])->name('loans.index');
        // the loans recorded against a copy; custody moving out or in, without ownership
        Route::get('copies/{copy}/loans', [LoanController::class, 'index'])->where('copy', '[1-9][0-9]*')->name('copies.loans');
        Route::get('copies/{copy}/loans/{loan}', [LoanController::class, 'show'])->where(['copy' => '[1-9][0-9]*', 'loan' => '[1-9][0-9]*'])->name('copies.loans.show');
        Route::post('copies/{copy}/loans', [LoanController::class, 'create'])->where('copy', '[1-9][0-9]*')->name('copies.loans.create');
        Route::put('copies/{copy}/loans/{loan}', [LoanController::class, 'update'])->where(['copy' => '[1-9][0-9]*', 'loan' => '[1-9][0-9]*'])->name('copies.loans.update');
        Route::post('copies/{copy}/loans/{loan}/return', [LoanController::class, 'return'])->where(['copy' => '[1-9][0-9]*', 'loan' => '[1-9][0-9]*'])->name('copies.loans.return');
        Route::delete('copies/{copy}/loans/{loan}', [LoanController::class, 'destroy'])->where(['copy' => '[1-9][0-9]*', 'loan' => '[1-9][0-9]*'])->name('copies.loans.destroy');

        // the documents attached to a copy and every record on it; list and add under the copy, then reach one directly by its id
        Route::get('copies/{copy}/documents', [DocumentController::class, 'index'])->where('copy', '[1-9][0-9]*')->name('copies.documents');
        Route::post('copies/{copy}/documents', [DocumentController::class, 'create'])->where('copy', '[1-9][0-9]*')->name('copies.documents.create');
        Route::get('documents/{document}', [DocumentController::class, 'show'])->where('document', '[1-9][0-9]*')->name('documents.show');
        Route::put('documents/{document}', [DocumentController::class, 'update'])->where('document', '[1-9][0-9]*')->name('documents.update');
        Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->where('document', '[1-9][0-9]*')->name('documents.destroy');

        // the unified history of a copy; every record on it merged into one read
        Route::get('copies/{copy}/history', [CopyHistoryController::class, 'index'])->where('copy', '[1-9][0-9]*')->name('copies.history');

        // the location history of a copy; creating a record moves the copy
        Route::get('copies/{copy}/location-history', [LocationHistoryController::class, 'index'])->where('copy', '[1-9][0-9]*')->name('copies.locationHistory');
        Route::get('copies/{copy}/location-history/{locationHistory}', [LocationHistoryController::class, 'show'])->where(['copy' => '[1-9][0-9]*', 'locationHistory' => '[1-9][0-9]*'])->name('copies.locationHistory.show');
        Route::post('copies/{copy}/location-history', [LocationHistoryController::class, 'create'])->where('copy', '[1-9][0-9]*')->name('copies.locationHistory.create');
        Route::put('copies/{copy}/location-history/{locationHistory}', [LocationHistoryController::class, 'update'])->where(['copy' => '[1-9][0-9]*', 'locationHistory' => '[1-9][0-9]*'])->name('copies.locationHistory.update');
        Route::delete('copies/{copy}/location-history/{locationHistory}', [LocationHistoryController::class, 'destroy'])->where(['copy' => '[1-9][0-9]*', 'locationHistory' => '[1-9][0-9]*'])->name('copies.locationHistory.destroy');

        // the photos of an item
        Route::get('items/{item}/photos', [ItemPhotoController::class, 'index'])->where('item', '[1-9][0-9]*')->name('items.photos');
        Route::get('items/{item}/photos/{photo}', [ItemPhotoController::class, 'show'])->where(['item' => '[1-9][0-9]*', 'photo' => '[1-9][0-9]*'])->name('items.photos.show');
        Route::post('items/{item}/photos', [ItemPhotoController::class, 'create'])->where('item', '[1-9][0-9]*')->name('items.photos.create');
        Route::put('items/{item}/photos/{photo}/main', [ItemPhotoController::class, 'main'])->where(['item' => '[1-9][0-9]*', 'photo' => '[1-9][0-9]*'])->name('items.photos.main');
        Route::delete('items/{item}/photos/{photo}', [ItemPhotoController::class, 'destroy'])->where(['item' => '[1-9][0-9]*', 'photo' => '[1-9][0-9]*'])->name('items.photos.destroy');

        // the tags an item carries, drawn from the shared pool of the account
        Route::get('items/{item}/tags', [ItemTagController::class, 'index'])->where('item', '[1-9][0-9]*')->name('items.tags');
        Route::post('items/{item}/tags', [ItemTagController::class, 'create'])->where('item', '[1-9][0-9]*')->name('items.tags.create');
        Route::delete('items/{item}/tags/{tag}', [ItemTagController::class, 'destroy'])->where(['item' => '[1-9][0-9]*', 'tag' => '[1-9][0-9]*'])->name('items.tags.destroy');

        // several photos of the account deleted in one call
        Route::delete('photos', [ItemPhotoSelectionController::class, 'destroy'])->name('photos.destroy');

        // the activity trail of an item, written by the app as actions happen
        Route::get('items/{item}/logs', [ItemLogController::class, 'index'])->where('item', '[1-9][0-9]*')->name('items.logs');
        Route::get('items/{item}/logs/{log}', [ItemLogController::class, 'show'])->where(['item' => '[1-9][0-9]*', 'log' => '[1-9][0-9]*'])->name('items.logs.show');

        // what the account soft deleted, and still has time to restore
        Route::get('trash', [TrashController::class, 'index'])->name('trash');
        Route::put('trash', [TrashController::class, 'update'])->name('trash.update');
        Route::delete('trash', [TrashController::class, 'destroy'])->name('trash.destroy');

        // the account itself
        Route::get('account', [AccountController::class, 'show'])->name('account');
        Route::put('account', [AccountController::class, 'update'])->name('account.update');
        Route::delete('account', [AccountController::class, 'destroy'])->name('account.destroy');

        // the people who have access to the account
        Route::get('account/members', [MemberController::class, 'index'])->name('account.members');
        Route::get('account/members/{member}', [MemberController::class, 'show'])->where('member', '[1-9][0-9]*')->name('account.members.show');
        Route::post('account/members', [MemberController::class, 'create'])->name('account.members.create');
        Route::put('account/members/{member}', [MemberController::class, 'update'])->where('member', '[1-9][0-9]*')->name('account.members.update');
        Route::delete('account/members/{member}', [MemberController::class, 'destroy'])->where('member', '[1-9][0-9]*')->name('account.members.destroy');

        // the invitations still waiting to be claimed
        Route::get('account/invitations', [InvitationController::class, 'index'])->name('account.invitations');

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

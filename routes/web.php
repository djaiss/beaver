<?php

declare(strict_types=1);

use App\Http\Controllers\App\Account\AccountController;
use App\Http\Controllers\App\Account\InvitationController;
use App\Http\Controllers\App\Account\MemberController;
use App\Http\Controllers\App\CategoryController;
use App\Http\Controllers\App\CollectionController;
use App\Http\Controllers\App\CollectionItemViewController;
use App\Http\Controllers\App\CollectionTypeController;
use App\Http\Controllers\App\CollectionTypeExportController;
use App\Http\Controllers\App\CollectionTypeImportController;
use App\Http\Controllers\App\ConditionController;
use App\Http\Controllers\App\CustomFieldController;
use App\Http\Controllers\App\CustomFieldGroupController;
use App\Http\Controllers\App\CustomFieldGroupFieldController;
use App\Http\Controllers\App\CustomFieldGroupOrderController;
use App\Http\Controllers\App\CustomFieldOrderController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\DocumentController;
use App\Http\Controllers\App\DocumentDownloadController;
use App\Http\Controllers\App\GettingStartedController;
use App\Http\Controllers\App\Instance\AccountController as InstanceAccountController;
use App\Http\Controllers\App\Instance\OverviewController as InstanceOverviewController;
use App\Http\Controllers\App\Instance\ReviewController as InstanceReviewController;
use App\Http\Controllers\App\Instance\SupportController as InstanceSupportController;
use App\Http\Controllers\App\Instance\SupportMessageController as InstanceSupportMessageController;
use App\Http\Controllers\App\Instance\UserController as InstanceUserController;
use App\Http\Controllers\App\InsuranceRecordController;
use App\Http\Controllers\App\ItemActivitiesController;
use App\Http\Controllers\App\ItemController;
use App\Http\Controllers\App\ItemCopiesController;
use App\Http\Controllers\App\ItemHistoryController;
use App\Http\Controllers\App\ItemPhotoController;
use App\Http\Controllers\App\ItemRoadmapController;
use App\Http\Controllers\App\ItemTagController;
use App\Http\Controllers\App\LoanController;
use App\Http\Controllers\App\LoanReturnController;
use App\Http\Controllers\App\LocationController;
use App\Http\Controllers\App\LocationHistoryController;
use App\Http\Controllers\App\MaintenanceRecordController;
use App\Http\Controllers\App\ProvenanceEventController;
use App\Http\Controllers\App\SeriesController;
use App\Http\Controllers\App\SetController;
use App\Http\Controllers\App\Settings\ApiKeyController;
use App\Http\Controllers\App\Settings\AutoDeleteUserController;
use App\Http\Controllers\App\Settings\AvatarController;
use App\Http\Controllers\App\Settings\EmailSentController;
use App\Http\Controllers\App\Settings\GettingStartedController as SettingsGettingStartedController;
use App\Http\Controllers\App\Settings\LogController;
use App\Http\Controllers\App\Settings\PasswordController;
use App\Http\Controllers\App\Settings\PhotoController;
use App\Http\Controllers\App\Settings\PhotoCoverController;
use App\Http\Controllers\App\Settings\PhotoSelectionController;
use App\Http\Controllers\App\Settings\PhotoViewController;
use App\Http\Controllers\App\Settings\RecoveryCodeController;
use App\Http\Controllers\App\Settings\SecurityController;
use App\Http\Controllers\App\Settings\SettingsController;
use App\Http\Controllers\App\Settings\TwoFAController;
use App\Http\Controllers\App\Settings\UserController;
use App\Http\Controllers\App\Settings\WebhookController;
use App\Http\Controllers\App\StatisticsController;
use App\Http\Controllers\App\Support\MessageController as SupportMessageController;
use App\Http\Controllers\App\Support\TicketController as SupportTicketController;
use App\Http\Controllers\App\TagController;
use App\Http\Controllers\App\TransactionController;
use App\Http\Controllers\App\TrashController;
use App\Http\Controllers\App\ValuationController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/marketing.php';

Route::put('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware(['auth', 'verified', 'throttle:60,1', 'set.locale'])->group(function (): void {
    // dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // getting started — the screen a new account lands on until it has something in it.
    // Reading it is open to any role; dismissing it changes the whole account, so it is not.
    Route::get('getting-started', [GettingStartedController::class, 'index'])->name('gettingStarted.index');

    // placeholder sections for the future collection domain
    Route::get('collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::get('collections/{collection}', [CollectionController::class, 'show'])->where('collection', '[1-9][0-9]*')->name('collections.show');
    // remembering which items view a member last opened is a private preference, so any role may set it
    Route::put('collections/{collection}/item-view', [CollectionItemViewController::class, 'update'])->where('collection', '[1-9][0-9]*')->name('collections.item-view.update');
    // viewing an item is read only, so any role may do it
    // each tab of an item is its own page, with overview living on the item's own url
    Route::get('collections/{collection}/items/{item}', [ItemController::class, 'show'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*'])->name('items.show');
    Route::get('collections/{collection}/items/{item}/copies', [ItemCopiesController::class, 'index'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*'])->name('items.copies.index');
    // The history is read one copy at a time, so a copy lives in the url. The
    // bare url lands on the first copy; each copy pill links to its own, and each
    // section is its own url rather than a query parameter.
    Route::get('collections/{collection}/items/{item}/history', [ItemHistoryController::class, 'index'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*'])->name('items.history.index');
    Route::get('collections/{collection}/items/{item}/history/{copy}/{section?}', [ItemHistoryController::class, 'show'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'section' => '[a-z]+'])->name('items.history.show');
    Route::get('collections/{collection}/items/{item}/activities', [ItemActivitiesController::class, 'index'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*'])->name('items.activities.index');
    Route::get('collections/{collection}/items/{item}/roadmap', [ItemRoadmapController::class, 'index'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*'])->name('items.roadmap.index');
    Route::get('items/photos/{itemPhoto}', [ItemPhotoController::class, 'show'])->where('itemPhoto', '[1-9][0-9]*')->name('items.photos.show');
    // documents live on the private disk, so their files are streamed through here rather than served directly, and only to the account they belong to
    Route::get('documents/{document}', [DocumentDownloadController::class, 'show'])->where('document', '[1-9][0-9]*')->name('documents.show');
    // browsing the categories of a collection is read only, so any role may do it
    Route::get('collections/{collection}/categories', [CategoryController::class, 'index'])->where('collection', '[1-9][0-9]*')->name('categories.index');
    Route::get('collections/{collection}/categories/{category}', [CategoryController::class, 'show'])->where(['collection' => '[1-9][0-9]*', 'category' => '[1-9][0-9]*'])->name('categories.show');
    // browsing the sets of a collection is read only, so any role may do it
    Route::get('collections/{collection}/sets', [SetController::class, 'index'])->where('collection', '[1-9][0-9]*')->name('sets.index');
    // the statistics of a collection are read only, so any role may see them
    Route::get('collections/{collection}/statistics', [StatisticsController::class, 'index'])->where('collection', '[1-9][0-9]*')->name('statistics.index');
    // series are account-wide rather than per collection, so they hang off the dashboard
    // instead of a collection. Browsing them is read only, so any role may do it.
    Route::get('series', [SeriesController::class, 'index'])->name('series.index');
    Route::get('series/{series}', [SeriesController::class, 'show'])->where('series', '[1-9][0-9]*')->name('series.show');
    Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('search', fn () => view('app._placeholder', ['title' => __('Search'), 'body' => __('Search across everything in your account. This is coming soon.')]))->name('search.index');

    // collections — owners and editors may create new collections
    Route::middleware(['editor'])->group(function (): void {
        Route::get('collections/new', [CollectionController::class, 'new'])->name('collections.new');
        Route::post('collections', [CollectionController::class, 'create'])->name('collections.create');
        Route::get('collections/{collection}/edit', [CollectionController::class, 'edit'])->where('collection', '[1-9][0-9]*')->name('collections.edit');
        Route::put('collections/{collection}', [CollectionController::class, 'update'])->where('collection', '[1-9][0-9]*')->name('collections.update');
        Route::delete('collections/{collection}', [CollectionController::class, 'destroy'])->where('collection', '[1-9][0-9]*')->name('collections.destroy');

        // items — owners and editors may add items to a collection and edit them
        Route::get('collections/{collection}/items/new', [ItemController::class, 'new'])->where('collection', '[1-9][0-9]*')->name('items.new');
        Route::post('collections/{collection}/items', [ItemController::class, 'create'])->where('collection', '[1-9][0-9]*')->name('items.create');
        Route::get('collections/{collection}/items/{item}/edit', [ItemController::class, 'edit'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*'])->name('items.edit');
        Route::put('collections/{collection}/items/{item}', [ItemController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*'])->name('items.update');
        Route::delete('collections/{collection}/items/{item}', [ItemController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*'])->name('items.destroy');

        // transactions — owners and editors record what a copy cost, sold for or was traded against
        Route::post('collections/{collection}/items/{item}/copies/{copy}/transactions', [TransactionController::class, 'create'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*'])->name('transactions.create');
        Route::put('collections/{collection}/items/{item}/copies/{copy}/transactions/{transaction}', [TransactionController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'transaction' => '[1-9][0-9]*'])->name('transactions.update');
        Route::delete('collections/{collection}/items/{item}/copies/{copy}/transactions/{transaction}', [TransactionController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'transaction' => '[1-9][0-9]*'])->name('transactions.destroy');

        // provenance events — owners and editors record the story of a copy: who owned it, where it was shown, how it was authenticated
        Route::post('collections/{collection}/items/{item}/copies/{copy}/provenance-events', [ProvenanceEventController::class, 'create'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*'])->name('provenanceEvents.create');
        Route::put('collections/{collection}/items/{item}/copies/{copy}/provenance-events/{provenanceEvent}', [ProvenanceEventController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'provenanceEvent' => '[1-9][0-9]*'])->name('provenanceEvents.update');
        Route::delete('collections/{collection}/items/{item}/copies/{copy}/provenance-events/{provenanceEvent}', [ProvenanceEventController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'provenanceEvent' => '[1-9][0-9]*'])->name('provenanceEvents.destroy');

        // valuations — owners and editors record what a copy is reckoned to be worth over time
        Route::post('collections/{collection}/items/{item}/copies/{copy}/valuations', [ValuationController::class, 'create'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*'])->name('valuations.create');
        Route::put('collections/{collection}/items/{item}/copies/{copy}/valuations/{valuation}', [ValuationController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'valuation' => '[1-9][0-9]*'])->name('valuations.update');
        Route::delete('collections/{collection}/items/{item}/copies/{copy}/valuations/{valuation}', [ValuationController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'valuation' => '[1-9][0-9]*'])->name('valuations.destroy');

        // insurance records — owners and editors record what a copy is insured for as policies and values change
        Route::post('collections/{collection}/items/{item}/copies/{copy}/insurance-records', [InsuranceRecordController::class, 'create'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*'])->name('insuranceRecords.create');
        Route::put('collections/{collection}/items/{item}/copies/{copy}/insurance-records/{insuranceRecord}', [InsuranceRecordController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'insuranceRecord' => '[1-9][0-9]*'])->name('insuranceRecords.update');
        Route::delete('collections/{collection}/items/{item}/copies/{copy}/insurance-records/{insuranceRecord}', [InsuranceRecordController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'insuranceRecord' => '[1-9][0-9]*'])->name('insuranceRecords.destroy');

        // maintenance records — owners and editors log the work done on a copy, its condition before and after, and when it is next due
        Route::post('collections/{collection}/items/{item}/copies/{copy}/maintenance-records', [MaintenanceRecordController::class, 'create'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*'])->name('maintenanceRecords.create');
        Route::put('collections/{collection}/items/{item}/copies/{copy}/maintenance-records/{maintenanceRecord}', [MaintenanceRecordController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'maintenanceRecord' => '[1-9][0-9]*'])->name('maintenanceRecords.update');
        Route::delete('collections/{collection}/items/{item}/copies/{copy}/maintenance-records/{maintenanceRecord}', [MaintenanceRecordController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'maintenanceRecord' => '[1-9][0-9]*'])->name('maintenanceRecords.destroy');

        // loans — owners and editors record custody moving out or in, mark a loan as returned, and correct or remove one
        Route::post('collections/{collection}/items/{item}/copies/{copy}/loans', [LoanController::class, 'create'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*'])->name('loans.create');
        Route::put('collections/{collection}/items/{item}/copies/{copy}/loans/{loan}', [LoanController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'loan' => '[1-9][0-9]*'])->name('loans.update');
        Route::put('collections/{collection}/items/{item}/copies/{copy}/loans/{loan}/return', [LoanReturnController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'loan' => '[1-9][0-9]*'])->name('loans.return.update');
        Route::delete('collections/{collection}/items/{item}/copies/{copy}/loans/{loan}', [LoanController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'loan' => '[1-9][0-9]*'])->name('loans.destroy');

        // documents — owners and editors attach a file or an external link to a copy or one of its records, correct its details, and remove it
        Route::post('collections/{collection}/items/{item}/copies/{copy}/documents', [DocumentController::class, 'create'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*'])->name('documents.create');
        Route::put('collections/{collection}/items/{item}/copies/{copy}/documents/{document}', [DocumentController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'document' => '[1-9][0-9]*'])->name('documents.update');
        Route::delete('collections/{collection}/items/{item}/copies/{copy}/documents/{document}', [DocumentController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'document' => '[1-9][0-9]*'])->name('documents.destroy');

        // location history — owners and editors move a copy between locations; creating a record is a move, update and destroy correct a past one
        Route::post('collections/{collection}/items/{item}/copies/{copy}/location-history', [LocationHistoryController::class, 'create'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*'])->name('locationHistory.create');
        Route::put('collections/{collection}/items/{item}/copies/{copy}/location-history/{locationHistory}', [LocationHistoryController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'locationHistory' => '[1-9][0-9]*'])->name('locationHistory.update');
        Route::delete('collections/{collection}/items/{item}/copies/{copy}/location-history/{locationHistory}', [LocationHistoryController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'copy' => '[1-9][0-9]*', 'locationHistory' => '[1-9][0-9]*'])->name('locationHistory.destroy');

        // tags are put on and taken off an item from the item screen itself, one at a time
        Route::post('collections/{collection}/items/{item}/tags', [ItemTagController::class, 'create'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*'])->name('items.tags.create');
        Route::delete('collections/{collection}/items/{item}/tags/{tag}', [ItemTagController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'item' => '[1-9][0-9]*', 'tag' => '[1-9][0-9]*'])->name('items.tags.destroy');
    });

    // categories — owners and editors may create, update and delete the categories of a collection
    Route::middleware(['editor'])->group(function (): void {
        Route::post('collections/{collection}/categories', [CategoryController::class, 'create'])->where('collection', '[1-9][0-9]*')->name('categories.create');
        Route::put('collections/{collection}/categories/{category}', [CategoryController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'category' => '[1-9][0-9]*'])->name('categories.update');
        Route::delete('collections/{collection}/categories/{category}', [CategoryController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'category' => '[1-9][0-9]*'])->name('categories.destroy');
    });

    // sets — owners and editors may create, update and delete the sets of a collection
    Route::middleware(['editor'])->group(function (): void {
        Route::post('collections/{collection}/sets', [SetController::class, 'create'])->where('collection', '[1-9][0-9]*')->name('sets.create');
        Route::put('collections/{collection}/sets/{set}', [SetController::class, 'update'])->where(['collection' => '[1-9][0-9]*', 'set' => '[1-9][0-9]*'])->name('sets.update');
        Route::delete('collections/{collection}/sets/{set}', [SetController::class, 'destroy'])->where(['collection' => '[1-9][0-9]*', 'set' => '[1-9][0-9]*'])->name('sets.destroy');
    });

    // series — owners and editors may create, update and delete the series of the account
    Route::middleware(['editor'])->group(function (): void {
        Route::post('series', [SeriesController::class, 'create'])->name('series.create');
        Route::put('series/{series}', [SeriesController::class, 'update'])->where('series', '[1-9][0-9]*')->name('series.update');
        Route::delete('series/{series}', [SeriesController::class, 'destroy'])->where('series', '[1-9][0-9]*')->name('series.destroy');
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
    Route::post('profile/avatar', [AvatarController::class, 'update'])->name('profile.avatar.update');
    Route::delete('profile/avatar', [AvatarController::class, 'destroy'])->name('profile.avatar.destroy');
    Route::get('profile/avatar/{user}/{size}', [AvatarController::class, 'show'])->where('user', '[1-9][0-9]*')->where('size', '[1-9][0-9]*')->name('profile.avatar.show');
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

    // support — a signed in user's own conversations with the instance team. The
    // whole section only exists when SUPPORT_ENABLED is on: the middleware answers
    // 404 otherwise, matching the sidebar that hides the link.
    Route::middleware(['support.enabled'])->prefix('support')->name('support.')->group(function (): void {
        Route::get('/', [SupportTicketController::class, 'index'])->name('tickets.index');
        Route::get('new', [SupportTicketController::class, 'new'])->name('tickets.new');
        Route::post('/', [SupportTicketController::class, 'create'])->name('tickets.create');
        Route::get('{supportTicket}', [SupportTicketController::class, 'show'])->where('supportTicket', '[1-9][0-9]*')->name('tickets.show');
        Route::put('{supportTicket}', [SupportTicketController::class, 'update'])->where('supportTicket', '[1-9][0-9]*')->name('tickets.update');
        Route::delete('{supportTicket}', [SupportTicketController::class, 'destroy'])->where('supportTicket', '[1-9][0-9]*')->name('tickets.destroy');
        Route::post('{supportTicket}/messages', [SupportMessageController::class, 'create'])->where('supportTicket', '[1-9][0-9]*')->name('tickets.messages.create');
    });

    // account settings — the account and its members (owners only)
    Route::middleware(['owner'])->group(function (): void {
        Route::delete('getting-started', [GettingStartedController::class, 'destroy'])->name('gettingStarted.destroy');

        Route::get('settings', [AccountController::class, 'index'])->name('settings.index');
        Route::put('settings/getting-started', [SettingsGettingStartedController::class, 'update'])->name('settings.gettingStarted.update');
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
        Route::get('settings/types/{collectionType}/export', [CollectionTypeExportController::class, 'show'])->where('collectionType', '[1-9][0-9]*')->name('settings.types.export.show');
        Route::get('settings/types/import', [CollectionTypeImportController::class, 'new'])->name('settings.types.import.new');
        Route::post('settings/types/import', [CollectionTypeImportController::class, 'create'])->name('settings.types.import.create');

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

    });

    // account settings: tags — owners and editors define the labels items can carry
    Route::middleware(['editor'])->group(function (): void {
        Route::get('settings/tags', [TagController::class, 'index'])->name('settings.tags.index');
        Route::post('settings/tags', [TagController::class, 'create'])->name('settings.tags.create');
        Route::put('settings/tags/{tag}', [TagController::class, 'update'])->where('tag', '[1-9][0-9]*')->name('settings.tags.update');
        Route::delete('settings/tags/{tag}', [TagController::class, 'destroy'])->where('tag', '[1-9][0-9]*')->name('settings.tags.destroy');
    });

    // account settings: conditions — owners and editors define the condition levels items can carry
    Route::middleware(['editor'])->group(function (): void {
        Route::get('settings/conditions', [ConditionController::class, 'index'])->name('settings.conditions.index');
        Route::post('settings/conditions', [ConditionController::class, 'create'])->name('settings.conditions.create');
        Route::put('settings/conditions/{condition}', [ConditionController::class, 'update'])->where('condition', '[1-9][0-9]*')->name('settings.conditions.update');
        Route::delete('settings/conditions/{condition}', [ConditionController::class, 'destroy'])->where('condition', '[1-9][0-9]*')->name('settings.conditions.destroy');
    });

    // account settings: photos — owners and editors manage every image in the account
    Route::middleware(['editor'])->group(function (): void {
        Route::get('settings/photos', [PhotoController::class, 'index'])->name('settings.photos.index');
        // remembering the layout is a private preference, so it is saved on its own
        // rather than carried in the query string
        Route::put('settings/photos/view', [PhotoViewController::class, 'update'])->name('settings.photos.view.update');
        Route::put('settings/photos/{itemPhoto}/cover', [PhotoCoverController::class, 'update'])->where('itemPhoto', '[1-9][0-9]*')->name('settings.photos.cover.update');
        Route::delete('settings/photos/{itemPhoto}', [PhotoController::class, 'destroy'])->where('itemPhoto', '[1-9][0-9]*')->name('settings.photos.destroy');
        Route::delete('settings/photos/selection', [PhotoSelectionController::class, 'destroy'])->name('settings.photos.selection.destroy');
    });

    // account settings: trash — owners and editors restore what has been deleted
    Route::middleware(['editor'])->group(function (): void {
        Route::get('settings/trash', [TrashController::class, 'index'])->name('settings.trash.index');
        Route::put('settings/trash', [TrashController::class, 'update'])->name('settings.trash.update');
        Route::delete('settings/trash', [TrashController::class, 'destroy'])->name('settings.trash.destroy');
    });
    // instance administration — spans every account on the instance, so it is
    // gated on the per user flag rather than on any role within an account
    Route::middleware(['instance.admin'])->prefix('instance-admin')->name('instanceAdmin.')->group(function (): void {
        Route::get('/', [InstanceOverviewController::class, 'index'])->name('index');

        Route::get('accounts', [InstanceAccountController::class, 'index'])->name('accounts.index');
        Route::get('accounts/{account}', [InstanceAccountController::class, 'show'])->where('account', '[1-9][0-9]*')->name('accounts.show');
        Route::delete('accounts/{account}', [InstanceAccountController::class, 'destroy'])->where('account', '[1-9][0-9]*')->name('accounts.destroy');

        Route::delete('users/{user}', [InstanceUserController::class, 'destroy'])->where('user', '[1-9][0-9]*')->name('users.destroy');
        Route::put('users/{user}/administrator', [InstanceUserController::class, 'update'])->where('user', '[1-9][0-9]*')->name('users.administrator.update');

        // the support inbox, spanning every account. Both the tab and the open
        // conversation live in the path (no query string), so each bucket and each
        // selected ticket is its own URL, and a bare /support lands on the open one.
        Route::get('support/{status?}/{ticket?}', [InstanceSupportController::class, 'index'])->where('status', 'open|all|closed')->where('ticket', '[1-9][0-9]*')->name('support.index');
        Route::post('support/{supportTicket}/messages', [InstanceSupportMessageController::class, 'create'])->where('supportTicket', '[1-9][0-9]*')->name('support.messages.create');
        Route::put('support/{supportTicket}', [InstanceSupportController::class, 'update'])->where('supportTicket', '[1-9][0-9]*')->name('support.update');

        // not built yet, this page says so
        Route::get('reviews', [InstanceReviewController::class, 'index'])->name('reviews.index');
    });
});

// invitations can be viewed and accepted by guests as well as logged-in users
Route::middleware(['set.locale'])->group(function (): void {
    Route::get('invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('invitations/{token}/accept', [InvitationController::class, 'create'])->name('invitations.create');
});

require __DIR__.'/auth.php';

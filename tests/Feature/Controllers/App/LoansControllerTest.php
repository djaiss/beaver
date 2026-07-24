<?php

declare(strict_types=1);

use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

if (! function_exists('webLoanCopy')) {
    function webLoanCopy(int $accountId): Copy
    {
        $catalog = Catalog::factory()->create(['account_id' => $accountId]);
        $item = Item::factory()->create(['catalog_id' => $catalog->id]);

        return Copy::factory()->create(['item_id' => $item->id]);
    }
}

it('redirects the bare loans url to the lent-out all tab', function () {
    $user = $this->createUser();

    $this->actingAs($user)
        ->get('/loans')
        ->assertRedirect(route('loans.show', ['direction' => 'lent-out', 'tab' => 'all']));
});

it('renders every tab for both directions', function () {
    $user = $this->createUser();
    Loan::factory()->create(['copy_id' => webLoanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active]);

    foreach (['lent-out', 'borrowed-in'] as $direction) {
        foreach (['all', 'due', 'risk', 'by-party', 'deposits', 'timeline'] as $tab) {
            $this->actingAs($user)
                ->get(route('loans.show', ['direction' => $direction, 'tab' => $tab]))
                ->assertOk();
        }
    }
});

it('shows a loan in the detail drawer', function () {
    $user = $this->createUser();
    $loan = Loan::factory()->create(['copy_id' => webLoanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active, 'party' => 'The Whitney Museum']);

    $this->actingAs($user)
        ->get(route('loans.show', ['direction' => 'lent-out', 'tab' => 'all', 'loan' => $loan->id]))
        ->assertOk()
        ->assertSee('The Whitney Museum');
});

it('does not show another account\'s loan', function () {
    $user = $this->createUser();
    $other = $this->createAccount();
    $loan = Loan::factory()->create(['copy_id' => webLoanCopy($other->id)->id, 'direction' => LoanDirection::Outgoing]);

    $this->actingAs($user)
        ->get(route('loans.show', ['direction' => 'lent-out', 'tab' => 'all', 'loan' => $loan->id]))
        ->assertNotFound();
});

it('does not list another account\'s loans', function () {
    $user = $this->createUser();
    $other = $this->createAccount();
    Loan::factory()->create(['copy_id' => webLoanCopy($other->id)->id, 'direction' => LoanDirection::Outgoing, 'party' => 'Secret Gallery']);

    $this->actingAs($user)
        ->get(route('loans.show', ['direction' => 'lent-out', 'tab' => 'all']))
        ->assertOk()
        ->assertDontSee('Secret Gallery');
});

it('opens the create drawer', function () {
    $user = $this->createUser();

    $this->actingAs($user)
        ->get(route('loans.new', ['direction' => 'lent-out']))
        ->assertOk()
        ->assertSee('New loan');
});

it('lets a viewer read the section', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount($viewer, $account, PermissionEnum::Viewer->value);

    $this->actingAs($viewer)
        ->get(route('loans.show', ['direction' => 'lent-out', 'tab' => 'all']))
        ->assertOk();
});

it('exports the open loans as csv', function () {
    $user = $this->createUser();
    Loan::factory()->create(['copy_id' => webLoanCopy($user->account_id)->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active]);

    $response = $this->actingAs($user)->get(route('loans.export.show', ['direction' => 'lent-out']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('text/csv');
});

it('rejects an unknown tab', function () {
    $user = $this->createUser();

    $this->actingAs($user)
        ->get('/loans/lent-out/nonsense')
        ->assertNotFound();
});

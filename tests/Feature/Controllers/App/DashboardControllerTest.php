<?php

declare(strict_types=1);
use App\Actions\UpdateUserAvatar;
use App\Enums\DashboardSectionEnum;
use App\Enums\UserActionEnum;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use App\Models\Log;
use App\Models\Valuation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('shows the avatar of the author of an activity entry when they have one', function () {
    Storage::fake();

    $user = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);

    new UpdateUserAvatar(
        user: $user,
        file: UploadedFile::fake()->image('ross.jpg', 400, 400),
    )->execute();

    Catalog::factory()->create(['account_id' => $user->account_id]);

    Log::factory()->create([
        'user_id' => $user->id,
        'action' => UserActionEnum::PersonalProfileUpdate->value,
    ]);

    $response = $this->actingAs($user->fresh())->get(route('dashboard.index'));

    $response->assertOk();
    $response->assertSee(route('profile.avatar.show', ['user' => $user, 'size' => 32]), escape: false);
});

it('falls back to the initials of the author when they have no avatar', function () {
    $user = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);

    Catalog::factory()->create(['account_id' => $user->account_id]);

    Log::factory()->create([
        'user_id' => $user->id,
        'action' => UserActionEnum::PersonalProfileUpdate->value,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard.index'));

    $response->assertOk();
    $response->assertSee('RG');
});

it('shows what the account holds, where it is, and what it is worth', function () {
    $user = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);

    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man #365']);
    $location = Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Display Case']);

    $copy = Copy::factory()->create(['item_id' => $item->id, 'current_location_id' => $location->id]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 68000, 'valued_at' => '2026-01-01']);

    $response = $this->actingAs($user)->get(route('dashboard.index'));

    $response->assertOk();
    $response->assertSee('Amazing Spider-Man #365');
    $response->assertSee('Marvel Comics');
    $response->assertSee('Display Case');
    $response->assertSee('Loan snapshot');
});

it('does not show another account\'s collection', function () {
    $user = $this->createUser();
    Catalog::factory()->create(['account_id' => $user->account_id]);

    $stranger = $this->createUser();
    Catalog::factory()->create(['account_id' => $stranger->account_id, 'name' => 'Central Perk Mugs']);

    $response = $this->actingAs($user)->get(route('dashboard.index'));

    $response->assertOk();
    $response->assertDontSee('Central Perk Mugs');
});

it('hides the sections the user turned off', function () {
    $user = $this->createUser(['hidden_dashboard_sections' => [DashboardSectionEnum::Loans->value]]);

    Catalog::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->get(route('dashboard.index'));

    $response->assertOk();

    // The block is still rendered, it just starts hidden, so the customize menu can
    // bring it back without a round trip.
    expect(openingTagOf($response->getContent(), 'dashboard-loans'))->toContain('display: none')
        ->and(openingTagOf($response->getContent(), 'dashboard-activity'))->not->toContain('display: none');
});

/**
 * The opening tag of the block carrying a given data-test attribute.
 */
function openingTagOf(string $html, string $test): string
{
    preg_match('/<div[^>]*data-test="'.$test.'"[^>]*>/', $html, $matches);

    return $matches[0] ?? '';
}

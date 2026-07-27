<?php

declare(strict_types=1);

use App\Models\UserDeletionReason;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists the deletion reasons, newest first', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    UserDeletionReason::factory()->create(['reason' => 'I moved my comics to a spreadsheet.']);
    UserDeletionReason::factory()->create(['reason' => 'We were on a break from collecting.']);

    $response = $this->actingAs($monica)
        ->get(route('instanceAdmin.deletionReasons.index'))
        ->assertOk()
        ->assertViewIs('app.instance.deletionReasons.index')
        ->assertSee('I moved my comics to a spreadsheet.')
        ->assertSee('We were on a break from collecting.');

    expect($response->viewData('reasons')->pluck('reason')->all())->toBe([
        'We were on a break from collecting.',
        'I moved my comics to a spreadsheet.',
    ]);
});

it('shows the page when nobody has left yet', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $this->actingAs($monica)
        ->get(route('instanceAdmin.deletionReasons.index'))
        ->assertOk()
        ->assertSee('Nobody has deleted their user on this instance.');
});

it('hides the deletion reasons from everybody else', function () {
    $rachel = $this->createUser(['is_instance_administrator' => false]);

    $this->actingAs($rachel)
        ->get(route('instanceAdmin.deletionReasons.index'))
        ->assertNotFound();
});

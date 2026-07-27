<?php

declare(strict_types=1);

use App\Models\UserDeletionReason;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('stores the reason encrypted', function () {
    $reason = UserDeletionReason::factory()->create([
        'reason' => 'I moved my comics to a spreadsheet.',
    ]);

    $raw = DB::table('user_deletion_reasons')->where('id', $reason->id)->value('reason');

    expect($raw)->not->toBe('I moved my comics to a spreadsheet.')
        ->and(decrypt($raw, false))->toBe('I moved my comics to a spreadsheet.')
        ->and($reason->fresh()->reason)->toBe('I moved my comics to a spreadsheet.');
});

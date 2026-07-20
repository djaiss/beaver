<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Copy;
use App\Models\LocationHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * The single path a copy's location travels through.
 *
 * A copy has at most one open location record, and copies.current_location_id
 * mirrors it. Every write of a copy's location goes through here so the two never
 * drift: moving closes the open record, opens a new one and updates the pointer,
 * all in one transaction. Setting the location to what it already is does nothing,
 * which keeps a save that did not move the copy from writing a spurious row.
 */
trait RecordsCopyMoves
{
    protected function recordCopyMove(
        Copy $copy,
        ?int $locationId,
        User $user,
        ?string $movedAt = null,
        ?string $reason = null,
        ?string $note = null,
    ): void {
        $movedAt ??= now()->toDateString();

        $open = $copy->locationHistory()->whereNull('moved_out_at')->latest('id')->first();

        if ($open instanceof LocationHistory && $open->location_id === $locationId) {
            return;
        }

        DB::transaction(function () use ($copy, $locationId, $user, $movedAt, $reason, $note, $open): void {
            if ($open instanceof LocationHistory) {
                $open->fill(['moved_out_at' => $movedAt]);
                $open->updated_by_id = $user->id;
                $open->updated_by_name = $user->getFullName();
                $open->save();
            }

            $copy->current_location_id = $locationId;
            $copy->save();

            if ($locationId === null) {
                return;
            }

            $record = LocationHistory::query()->create([
                'copy_id' => $copy->id,
                'location_id' => $locationId,
                'moved_at' => $movedAt,
                'moved_out_at' => null,
                'reason' => $reason,
                'note' => $note,
            ]);
            $record->created_by_id = $user->id;
            $record->created_by_name = $user->getFullName();
            $record->updated_by_id = $user->id;
            $record->updated_by_name = $user->getFullName();
            $record->save();
        });
    }

    /**
     * Re-point a copy's current location at its open record after a correction.
     *
     * Editing or deleting a past record can change which record is open, so the
     * pointer is recomputed from the history rather than trusted to still match.
     */
    protected function syncCurrentLocationFromOpenRecord(Copy $copy): void
    {
        $open = $copy->locationHistory()->whereNull('moved_out_at')->latest('id')->first();

        $copy->current_location_id = $open?->location_id;
        $copy->save();
    }
}

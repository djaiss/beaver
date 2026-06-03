<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Mail\AccountDestroyed;
use App\Models\AccountDeletionReason;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Before deleting the account, we need to check which vaults the user is owner
 * of. If the user is the only owner of a vault, we need to delete the vault as
 * well.
 */
class DestroyAccount
{
    public function __construct(
        private readonly User $user,
        private readonly string $reason,
    ) {}

    public function execute(): void
    {
        $this->deleteVaultsWhereUserIsOnlyOwner();
        $this->user->delete();
        $this->sendMail();
        $this->logAccountDeletion();
    }

    private function deleteVaultsWhereUserIsOnlyOwner(): void
    {
        // Get all vaults where the user is an owner
        $ownerMemberships = $this->user->memberships()
            ->where('role', PermissionEnum::Owner->value)
            ->with('vault')
            ->get();

        foreach ($ownerMemberships as $membership) {
            $vault = $membership->vault;

            // Count how many owners this vault has
            $ownerCount = $vault->members()
                ->where('role', PermissionEnum::Owner->value)
                ->count();

            // If this user is the only owner, delete the vault
            if ($ownerCount === 1) {
                $vault->delete();
            }
        }
    }

    private function sendMail(): void
    {
        Mail::to(config('app.account_deletion_notification_email'))
            ->queue(new AccountDestroyed(
                reason: $this->reason,
                activeSince: $this->user->created_at->format('Y-m-d'),
            ));
    }

    private function logAccountDeletion(): void
    {
        AccountDeletionReason::query()->create([
            'reason' => $this->reason,
        ]);
    }
}

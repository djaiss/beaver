<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Console\Command;

class MakeInstanceAdministratorCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'kollek:make-instance-administrator
        {email : The email of the user who should administer the instance}
        {--revoke : Take the instance administration away instead of granting it}';

    /**
     * @var string
     */
    protected $description = 'Grant or revoke access to the instance administration for a user';

    public function handle(): int
    {
        $user = User::query()
            ->where('email', $this->argument('email'))
            ->first();

        if ($user === null) {
            $this->error('No user found with this email address.');

            return self::FAILURE;
        }

        $grant = $this->option('revoke') === false;

        $user->is_instance_administrator = $grant;
        $user->save();

        // ToggleInstanceAdministrator is not reused here: it demands an acting
        // administrator, and this command exists precisely for the case where the
        // instance has none yet. The change is still logged against the user it
        // affects so the grant leaves a trail.
        LogUserAction::dispatch(
            user: $user,
            action: UserActionEnum::InstanceAdministratorUpdate,
            parameters: [
                'email' => $user->email,
                'status' => $grant ? 'granted' : 'revoked',
            ],
        )->onQueue('low');

        $this->info($grant
            ? 'This user now administers the instance.'
            : 'This user no longer administers the instance.');

        return self::SUCCESS;
    }
}

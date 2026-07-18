<?php

declare(strict_types=1);

use App\Jobs\DeleteInactiveUsers;
use App\Jobs\PurgeTrash;
use Illuminate\Support\Facades\Schedule;

Schedule::job(
    new DeleteInactiveUsers,
    'low',
)->dailyAt('00:30');

Schedule::job(
    new PurgeTrash,
    'low',
)->dailyAt('01:00');

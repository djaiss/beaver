<?php

declare(strict_types=1);

use App\Jobs\DeleteInactiveUsers;
use Illuminate\Support\Facades\Schedule;

Schedule::job(
    new DeleteInactiveUsers,
    'low',
)->dailyAt('00:30');

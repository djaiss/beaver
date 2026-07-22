<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Retention
    |--------------------------------------------------------------------------
    |
    | How many days a soft deleted object stays in the trash before the daily
    | PurgeTrash job removes it for good. Anyone with write access can restore
    | it up until then.
    |
    */

    'retention_days' => (int) env('TRASH_RETENTION_DAYS', 30),

];

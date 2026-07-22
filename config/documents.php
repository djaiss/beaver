<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Maximum stored file size
    |--------------------------------------------------------------------------
    |
    | The largest document file we accept, in kilobytes. A file above this is
    | rejected before it reaches the disk. External URL documents carry no file,
    | so this limit does not apply to them.
    |
    | Keep this at or below PHP's upload_max_filesize (12M in docker/php/php.ini),
    | otherwise a file between the two limits is killed by PHP before validation
    | runs and the user sees a raw server error instead of a friendly message.
    |
    */

    'max_size_in_kilobytes' => 12288,

    /*
    |--------------------------------------------------------------------------
    | Allowed mime types
    |--------------------------------------------------------------------------
    |
    | The mime types we accept for a stored document. Anything else is rejected,
    | whatever the extension of the uploaded file claims.
    |
    */

    'allowed_mime_types' => [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
        'image/heic',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
        'text/plain',
    ],

];

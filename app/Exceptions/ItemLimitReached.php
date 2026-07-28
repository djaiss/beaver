<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Thrown when an account on the managed instance has used its free allowance
 * and its grace, and cannot take another item until it is unlocked.
 *
 * It is deliberately not a ModelNotFoundException: a quota refusal is not the
 * same as "you may not see this", and the caller deserves to be told which one
 * it hit. See bootstrap/app.php for how it reaches the client.
 */
class ItemLimitReached extends Exception
{
    public function __construct()
    {
        parent::__construct('This account has reached the item limit of its free plan.');
    }
}

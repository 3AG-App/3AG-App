<?php

namespace App\Exceptions;

use Exception;

class DomainLimitReachedException extends Exception
{
    public function __construct(public readonly int $limit)
    {
        parent::__construct("Domain limit reached. Maximum {$limit} domain(s) allowed.");
    }
}

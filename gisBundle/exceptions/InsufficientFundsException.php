<?php

namespace GisBundle\Exceptions;

use Throwable;

/**
 * Class InsufficientFundsException
 * @package GisBundle\Exceptions
 */
class InsufficientFundsException extends GisException
{
    public function __construct(
        $message = "Not enough money to continue playing",
        $code = 0,
        Throwable $previous = null,
        $gisErrorCode = "INSUFFICIENT_FUNDS"
    )
    {
        parent::__construct($message, $code, $previous, $gisErrorCode);
    }
}

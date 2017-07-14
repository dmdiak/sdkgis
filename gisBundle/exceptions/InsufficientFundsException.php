<?php

namespace GisBundle\Exceptions;

use Throwable;

/**
 * Class InsufficientFundsException
 * @package GisBundle\Exceptions
 */
class InsufficientFundsException extends GisException
{
    /**
     * InsufficientFundsException constructor.
     * @param string $message [optional]
     * @param int $code [optional]
     * @param Throwable|null $previous [optional]
     * @param string $gisErrorCode [optional]
     */
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

<?php

namespace GisBundle\Exceptions;

use Throwable;

/**
 * Class InternalErrorException
 * @package GisBundle\Exceptions
 */
class InternalErrorException extends GisException
{
    /**
     * InternalErrorException constructor.
     * @param string $message [optional]
     * @param int $code [optional]
     * @param Throwable|null $previous [optional]
     * @param string $gisErrorCode [optional]
     */
    public function __construct(
        $message = "Something goes wrong",
        $code = 0,
        Throwable $previous = null,
        $gisErrorCode = "INTERNAL_ERROR"
    )
    {
        parent::__construct($message, $code, $previous, $gisErrorCode);
    }
}

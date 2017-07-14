<?php

namespace GisBundle\Exceptions;

use Throwable;

/**
 * Class GisException
 * @package GisBundle\Exceptions
 */
class GisException extends \Exception
{
    /**
     * @var string
     */
    protected $gisErrorCode;

    /**
     * @return string
     */
    public function getGisErrorCode()
    {
        return $this->gisErrorCode;
    }

    public function __construct($message = "", $code = 0, Throwable $previous = null, $gisErrorCode = "")
    {
        $this->gisErrorCode = $gisErrorCode;
        parent::__construct($message, $code, $previous);
    }
}

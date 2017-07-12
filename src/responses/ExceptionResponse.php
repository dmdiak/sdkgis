<?php

namespace SdkGis\Responses;

/**
 * Class ExceptionResponse
 * @package SdkGis\Responses
 */
class ExceptionResponse extends Response
{
    /**
     * @var string
     */
    public $error_code;

    /**
     * @var string
     */
    public $error_description;

    /**
     * @param string $error_code
     * @return ExceptionResponse
     */
    public function setErrorCode($error_code)
    {
        $this->error_code = $error_code;
        return $this;
    }

    /**
     * @param string $error_description
     * @return ExceptionResponse
     */
    public function setErrorDescription($error_description)
    {
        $this->error_description = $error_description;
        return $this;
    }
}

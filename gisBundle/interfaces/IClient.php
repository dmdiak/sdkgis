<?php

namespace GisBundle\Interfaces;

use GisBundle\Responses\BalanceResponse;
use GisBundle\Responses\BetResponse;
use GisBundle\Responses\WinResponse;
use GisBundle\Responses\RefundResponse;
use GisBundle\Responses\ErrorResponse;

/**
 * Interface IClient
 * @package GisBundle\Interfaces
 */
interface IClient
{

    /**
     * @param array $request
     * @return BalanceResponse|ErrorResponse
     */
    public function balance($request);

    /**
     * @param array $request
     * @return BetResponse|ErrorResponse
     */
    public function bet($request);

    /**
     * @param array $request
     * @return WinResponse|ErrorResponse
     */
    public function win($request);

    /**
     * @param array $request
     * @return RefundResponse|ErrorResponse
     */
    public function refund($request);

}

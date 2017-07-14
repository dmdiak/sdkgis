<?php

namespace GisBundle\Interfaces;

use GisBundle\Responses\BalanceResponse;
use GisBundle\Responses\BetResponse;
use GisBundle\Responses\WinResponse;
use GisBundle\Responses\RefundResponse;

/**
 * Interface IClient
 * @package GisBundle\Interfaces
 */
interface IClient
{

    /**
     * @param array $request
     * @return BalanceResponse
     */
    //public function balance($request);

    /**
     * @param array $request
     * @return BetResponse
     */
    //public function bet($request);

    /**
     * @param array $request
     * @return WinResponse
     */
    //public function win($request);

    /**
     * @param array $request
     * @return RefundResponse
     */
    //public function refund($request);

}

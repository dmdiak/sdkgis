<?php

namespace SdkGis\Interfaces;

use SdkGis\Responses\BalanceResponse;
use SdkGis\Responses\BetResponse;
use SdkGis\Responses\WinResponse;
use SdkGis\Responses\RefundResponse;
use SdkGis\Responses\ErrorResponse;

/**
 * Interface IClient
 * @package SdkGis\Interfaces
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

<?php

namespace SdkGis\Responses;

/**
 * Class BalanceResponse
 * @package SdkGis\Responses
 */
class BalanceResponse extends Response
{
    /**
     * @var double
     */
    public $balance;

    /**
     * @param double $balance
     * @return BalanceResponse
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }
}

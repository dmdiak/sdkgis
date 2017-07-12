<?php

namespace SdkGis\Responses;

/**
 * Class BetResponse
 * @package SdkGis\Responses
 */
class BetResponse extends Response
{
    /**
     * @var double
     */
    public $balance;

    /**
     * @var string
     */
    public $transaction_id;

    /**
     * @param double $balance
     * @return BetResponse
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @param string $transaction_id
     * @return BetResponse
     */
    public function setTransactionId($transaction_id)
    {
        $this->transaction_id = $transaction_id;
        return $this;
    }
}

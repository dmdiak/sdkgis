<?php

namespace GisBundle\Responses;

/**
 * Class WinResponse
 * @package GisBundle\Responses
 */
class WinResponse extends Response
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
     * @return WinResponse
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @param string $transaction_id
     * @return WinResponse
     */
    public function setTransactionId($transaction_id)
    {
        $this->transaction_id = $transaction_id;
        return $this;
    }

    /**
     * WinResponse constructor.
     * @param $balance
     * @param $transaction_id
     */
    public function __construct($balance, $transaction_id)
    {
        $this->setBalance($balance);
        $this->setTransactionId($transaction_id);
    }

}

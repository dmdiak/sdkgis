<?php

namespace GisBundle\Responses;

/**
 * Class BetResponse
 * @package GisBundle\Responses
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

    /**
     * BetResponse constructor.
     * @param $balance
     * @param $transaction_id
     */
    public function __construct($balance, $transaction_id)
    {
        $this->setBalance($balance);
        $this->setTransactionId($transaction_id);
    }

}

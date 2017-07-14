<?php

namespace GisBundle\Responses;

/**
 * Class BalanceResponse
 * @package GisBundle\Responses
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

    /**
     * BalanceResponse constructor.
     * @param $balance
     */
    public function __construct($balance)
    {
        $this->setBalance($balance);
    }

}

<?php

namespace GisBundle\MysqlExample;

use GisBundle\Interfaces\IBalance;

/**
 * Class Balance
 * @package GisBundle\MysqlExample
 */
class Balance implements IBalance
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $playerId;

    /**
     * @var double
     */
    protected $amount;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     * @return Balance
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @param int|string $playerId
     * @return Balance
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;
        return $this;
    }

    /**
     * @return double
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param double $amount
     * @return Balance
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Balance
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Balance constructor.
     * @param array $balanceData
     */
    public function __construct(array $balanceData)
    {
        $this->setId($balanceData['id']);
        $this->setId($balanceData['player_id']);
        $this->setId($balanceData['amount']);
        $this->setId($balanceData['currency']);
    }

}

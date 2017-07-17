<?php

namespace GisBundle\MysqlExample;

use GisBundle\Interfaces\IBalance;
use GisBundle\Exceptions\GisException;
use GisBundle\Exceptions\InternalErrorException;
use GisBundle\Exceptions\InsufficientFundsException;

/**
 * Class Balance
 * @package GisBundle\MysqlExample
 */
class Balance extends StorageModel implements IBalance
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
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set Balance if storage balance data exists.
     * @param string $playerId
     * @param string $currency
     * @throws GisException
     */
    public function setIfBalanceExists($playerId, $currency)
    {
        $balanceData = $this->getStorageBalanceData($playerId, $currency);
        if (is_array($balanceData) && !empty($balanceData)) {
            $this->setAllData($balanceData);
        } else {
            throw new InternalErrorException('Balance not found');
        }
    }

    /**
     * Set all object's properties.
     * @param array $balanceData
     */
    public function setAllData($balanceData)
    {
        $this->setId($balanceData['id']);
        $this->setPlayerId($balanceData['player_id']);
        $this->setAmount($balanceData['amount']);
        $this->setCurrency($balanceData['currency']);
    }

    /**
     * Get Balance data from storage by 'player_id' and 'currency'.
     * @param string $playerId
     * @param string $currency
     * @return mixed
     */
    public function getStorageBalanceData($playerId, $currency)
    {
        $query = "SELECT amount FROM casino.balances WHERE player_id = :player_id AND currency = :currency";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['player_id' => $playerId, 'currency' => $currency]);
        $balanceData = $stmt->fetch();

        return $balanceData;
    }

    public function checkFundsForBet($balance, $bet)
    {
        if ($balance < $bet) {
            throw new InsufficientFundsException();
        }
    }

    public function increaseBalance($amount)
    {
        $query = "UPDATE casino.balances SET amount = amount + :amount WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $isSuccess = $stmt->execute(['amount' => $amount, 'id' => $this->getId()]);
        if ($isSuccess === true) {
            $this->setIfBalanceExists($this->getPlayerId(), $this->getCurrency());
        }
    }

    public function decreaseBalance($amount)
    {
        $this->checkFundsForBet($this->getAmount(), $amount);
        $query = "UPDATE casino.balances SET amount = amount - :amount WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $isSuccess = $stmt->execute(['amount' => $amount, 'id' => $this->getId()]);
        if ($isSuccess === true) {
            $this->setIfBalanceExists($this->getPlayerId(), $this->getCurrency());
        }
    }
}

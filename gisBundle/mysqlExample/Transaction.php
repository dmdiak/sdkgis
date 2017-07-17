<?php

namespace GisBundle\MysqlExample;

use GisBundle\Interfaces\ITransaction;
use GisBundle\Exceptions\GisException;
use GisBundle\Exceptions\InternalErrorException;

/**
 * Class Transaction
 * @package GisBundle\MysqlExample
 */
class Transaction extends StorageModel implements ITransaction
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
     * @var int|string
     */
    protected $balanceId;

    /**
     * @var string
     */
    protected $transactionId;

    /**
     * @var string
     */
    protected $gameUuid;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var double
     */
    protected $amount;

    /**
     * @var string
     */
    protected $betTransactionId;

    /**
     * @return string
     */
    public function getBetTransactionId()
    {
        return $this->betTransactionId;
    }

    /**
     * @param string $betTransactionId
     * @return Transaction
     */
    public function setBetTransactionId($betTransactionId)
    {
        $this->betTransactionId = $betTransactionId;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     * @return Transaction
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
     * @return Transaction
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getBalanceId()
    {
        return $this->balanceId;
    }

    /**
     * @param int|string $balanceId
     * @return Transaction
     */
    public function setBalanceId($balanceId)
    {
        $this->balanceId = $balanceId;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     * @return Transaction
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    /**
     * @return string
     */
    public function getGameUuid()
    {
        return $this->gameUuid;
    }

    /**
     * @param string $gameUuid
     * @return Transaction
     */
    public function setGameUuid($gameUuid)
    {
        $this->gameUuid = $gameUuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     * @return Transaction
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return Transaction
     */
    public function setAction($action)
    {
        $this->action = $action;
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
     * @return Transaction
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
     * @return Transaction
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Transaction
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getTransactionData($transactionId, $action)
    {
        $query = "SELECT * FROM casino.transactions WHERE transaction_id = :transaction_id AND action = :action";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'transaction_id' => $transactionId,
            'action' => $action,
        ]);
        $transactionData = $stmt->fetch();

        return (is_array($transactionData) && !empty($transactionData)) ? $transactionData : false;
    }

    /**
     * Check amount format.
     * @param string $amount
     * @throws GisException
     */
    public function checkAmountFormat($amount)
    {
        if (!preg_match('/^\d+\.?\d+$|^\d+$/', $amount)) {
            throw new InternalErrorException('amount isn\'t correct');
        }
    }

    public function setAllData($data)
    {
        $this->setTransactionId($data['transaction_id']);
        $this->setAction($data['action']);
    }

    /**
     * @param bool $isCorrect
     */
    public function save($isCorrect)
    {
        $query = "INSERT INTO casino.transactions
                  (player_id, balance_id, game_uuid, session_id, transaction_id, action, amount, currency, type, bet_transaction_id, is_correct)
                  VALUES (:player_id, :balance_id, :game_uuid, :session_id, :transaction_id, :action, :amount, :currency, :type, :bet_transaction_id, :is_correct)";
        $stmt = $this->db->prepare($query);
        $isSuccess = $stmt->execute([
            'player_id' => $this->getPlayerId(),
            'balance_id' => $this->getBalanceId(),
            'game_uuid' => $this->getGameUuid(),
            'session_id' => $this->getSessionId(),
            'transaction_id' => $this->getTransactionId(),
            'action' => $this->getAction(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'type' => $this->getType(),
            'bet_transaction_id' => $this->getBetTransactionId(),
            'is_correct' => $isCorrect,
        ]);

        $id = $this->db->lastInsertId();
        if ($isSuccess === true) {
            $this->setId($id);
        }
    }
}

<?php

namespace GisBundle\MysqlExample;

use GisBundle\Exceptions\GisException;
use GisBundle\Exceptions\InternalErrorException;
use GisBundle\Interfaces\IBalance;
use GisBundle\Interfaces\IPlayer;
use GisBundle\Interfaces\ITransaction;
use GisBundle\Responses\BalanceResponse;
use GisBundle\Responses\BetResponse;
use GisBundle\Responses\WinResponse;
use GisBundle\Responses\RefundResponse;

/**
 * Class Player
 * @package GisBundle\MysqlExample
 */
class Player extends StorageModel implements IPlayer
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var Balance
     */
    protected $balance;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * Player constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setIfPlayerExists();
        $this->setBalance((new Balance()));
        $this->balance->setIfBalanceExists($_REQUEST['player_id'], $_REQUEST['currency']);
        if ($_REQUEST['action'] !== 'balance') {
            $this->setTransaction((new Transaction()));
            $this->transaction->setAllData($_REQUEST);
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->name;
    }

    /**
     * @param int $id
     * @return Player
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return Player
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $email
     * @return Player
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param IBalance $balance
     * @return Player
     */
    public function setBalance(IBalance $balance)
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @return Balance
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param ITransaction $transaction
     * @return Player
     */
    public function setTransaction(ITransaction $transaction)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set all object's properties.
     * @param array $playerData
     */
    public function setAllData($playerData)
    {
        $this->setId($playerData['id']);
        $this->setName($playerData['name']);
        $this->setEmail($playerData['email']);
    }

    /**
     * Set Player if storage player data exists
     * @throws GisException
     */
    public function setIfPlayerExists()
    {
        $this->checkPlayerIdFormat($_REQUEST['player_id']);
        $playerData = $this->getStoragePlayerData($_REQUEST['player_id']);
        if (is_array($playerData) && !empty($playerData)) {
            $this->setAllData($playerData);
        } else {
            throw new InternalErrorException('Player not found');
        }
    }

    /**
     * Get Player data from storage by id.
     * @param string $playerId
     * @return mixed
     */
    public function getStoragePlayerData($playerId)
    {
        $query = "SELECT * FROM casino.players WHERE id = :player_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['player_id' => $playerId]);
        $playerData = $stmt->fetch();

        return $playerData;
    }

    /**
     * Check player_id format.
     * @param string $playerId
     * @throws GisException
     */
    protected function checkPlayerIdFormat($playerId)
    {
        if (preg_match('/\D+/', $playerId)) {
            throw new InternalErrorException('player_id isn\'t correct');
        }
    }

    protected function processTransaction()
    {
        $transactionData = $this->transaction
            ->getTransactionData($this->transaction->getTransactionId(), $this->transaction->getAction());
        if ($transactionData === false) {
            $this->db->beginTransaction();
            $isCorrect = true;
            if (in_array($this->transaction->getAction(), ['bet', 'win'])) {
                $this->transaction->checkAmountFormat($_REQUEST['amount']);
                if ($this->transaction->getAction() === 'bet') {
                    $this->balance->decreaseBalance($_REQUEST['amount']);
                } else {
                    $this->balance->increaseBalance($_REQUEST['amount']);
                }
            } else {
                $transactionToRefund = new Transaction();
                $transactionToRefundData = $transactionToRefund->getTransactionData($_REQUEST['bet_transaction_id'], 'bet');
                if (is_array($transactionToRefundData) && !empty($transactionToRefundData)) {
                    $transactionToRefund->setAllData($transactionToRefundData);
                    $balanceToRefund = new Balance();
                    $balanceToRefund->setIfBalanceExists($transactionToRefundData['player_id'], $transactionToRefundData['currency']);
                    $balanceToRefund->increaseBalance($transactionToRefund->getAmount());
                } else {
                    $isCorrect = false;
                }
            }
            $this->transaction->save($isCorrect);
            $this->db->commit();
        }
    }

    /**
     * @return BalanceResponse
     */
    public function getBalanceResponse()
    {
        $response = new BalanceResponse($this->balance->getAmount());
        return $response;
    }

    /**
     * @return BetResponse
     */
    public function getBetResponse()
    {
        $this->processTransaction();
        $response = new BetResponse($this->balance->getAmount(), $this->transaction->getId());
        return $response;
    }

    /**
     * @return WinResponse
     */
    public function getWinResponse()
    {
        $this->processTransaction();
        $response = new WinResponse($this->balance->getAmount(), $this->transaction->getId());
        return $response;
    }

    /**
     * @return RefundResponse
     */
    public function getRefundResponse()
    {
        $this->processTransaction();
        $response = new RefundResponse($this->balance->getAmount(), $this->transaction->getId());
        return $response;
    }

}

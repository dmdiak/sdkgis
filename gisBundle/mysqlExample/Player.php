<?php

namespace GisBundle\MysqlExample;

use PDO;
use GisBundle\Exceptions\GisException;
use GisBundle\Exceptions\InsufficientFundsException;
use GisBundle\Exceptions\InternalErrorException;
use GisBundle\Interfaces\IPlayer;
use GisBundle\Responses\BalanceResponse;

/**
 * Class Player
 * @package GisBundle\MysqlExample
 */
class Player implements IPlayer
{
    /**
     * @var PDO
     */
    protected $db;

    /**
     * @var Balance
     */
    protected $balance;

    /**
     * Player constructor.
     */
    public function __construct()
    {
        $dbCfg = include(__DIR__ . '/config/db.php');
        $this->db = new PDO($dbCfg['dsn'], $dbCfg['username'], $dbCfg['password'], $dbCfg['options']);
        $this->setBalance($this->prepareBalance());
    }

    /**
     * @param Balance $balance
     * @return Player
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * Prepare Balance by POST params.
     * @return Balance
     * @throws GisException
     */
    public function prepareBalance()
    {
        if (preg_match('/\D+/', $_REQUEST['player_id'])) {
            throw new InternalErrorException('player_id isn\'t correct');
        }

        $query = "SELECT amount FROM casino.balances WHERE player_id = :player_id AND currency = :currency";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['player_id' => $_REQUEST['player_id'], 'currency' => $_REQUEST['currency']]);
        $balanceData = $stmt->fetch();

        if (is_array($balanceData) && !empty($balanceData)) {
            return (new Balance($balanceData));
        } else {
            throw new InternalErrorException('Balance not found');
        }
    }

    /**
     * @return Balance
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return BalanceResponse
     */
    public function getBalanceResponse()
    {
        $response = new BalanceResponse($this->balance->getAmount());
        return $response;
    }

}

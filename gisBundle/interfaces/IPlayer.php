<?php

namespace GisBundle\Interfaces;

use GisBundle\Responses\BalanceResponse;
use GisBundle\Responses\BetResponse;
use GisBundle\Responses\WinResponse;
use GisBundle\Responses\RefundResponse;

/**
 * Interface IPlayer
 * @package GisBundle\Interfaces
 */
interface IPlayer
{
    /**
     * @return int|string
     */
    public function getId();

    /**
     * @param int|string $id
     * @return IPlayer
     */
    public function setId($id);

    /**
     * @return IBalance
     */
    public function getBalance();

    /**
     * @param IBalance
     * @return IPlayer
     */
    public function setBalance(IBalance $balance);

    /**
     * @return ITransaction
     */
    public function getTransaction();

    /**
     * @param ITransaction
     * @return IPlayer
     */
    public function setTransaction(ITransaction $transaction);

    /**
     * @return BalanceResponse
     */
    public function getBalanceResponse();

    /**
     * @return BetResponse
     */
    public function getBetResponse();

    /**
     * @return WinResponse
     */
    public function getWinResponse();

    /**
     * @return RefundResponse
     */
    public function getRefundResponse();

}

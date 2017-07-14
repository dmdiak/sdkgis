<?php

namespace GisBundle\Interfaces;

use GisBundle\Responses\BalanceResponse;

/**
 * Interface IPlayer
 * @package GisBundle\Interfaces
 */
interface IPlayer
{

    /**
     * @return IBalance
     */
    public function getBalance();

    /**
     * @param IBalance
     * @return IPlayer
     */
    public function setBalance($balance);

    /**
     * @return BalanceResponse
     */
    public function getBalanceResponse();

}

<?php

namespace GisBundle\Interfaces;

/**
 * Interface IBalance
 * @package GisBundle\Interfaces
 */
interface IBalance
{

    /**
     * @return int|string
     */
    public function getId();

    /**
     * @param int|string $id
     * @return IBalance
     */
    public function setId($id);

    /**
     * @return int|string
     */
    public function getPlayerId();

    /**
     * @param int|string $playerId
     * @return IBalance
     */
    public function setPlayerId($playerId);

    /**
     * @return double
     */
    public function getAmount();

    /**
     * @param double $amount
     * @return IBalance
     */
    public function setAmount($amount);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string $currency
     * @return IBalance
     */
    public function setCurrency($currency);

    public function increaseBalance($amount);

    public function decreaseBalance($amount);

}

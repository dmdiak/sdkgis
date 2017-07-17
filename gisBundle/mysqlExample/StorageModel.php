<?php

namespace GisBundle\MysqlExample;

use PDO;

/**
 * Class StorageModel
 * @package GisBundle\MysqlExample
 */
class StorageModel
{
    /**
     * @var PDO
     */
    protected $db;

    /**
     * StorageModel constructor.
     */
    public function __construct()
    {
        $dbCfg = include(__DIR__ . '/config/db.php');
        $this->db = new PDO($dbCfg['dsn'], $dbCfg['username'], $dbCfg['password'], $dbCfg['options']);
    }
}

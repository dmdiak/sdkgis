<?php

require_once('models/CasinoApi.php');
require_once('interfaces/IClient.php');
require_once('mysqlExample/Client.php');
require_once('responses/Response.php');
require_once('responses/BalanceResponse.php');
require_once('responses/BetResponse.php');
require_once('responses/WinResponse.php');
require_once('responses/RefundResponse.php');
require_once('exceptions/GisException.php');
require_once('exceptions/InternalErrorException.php');
require_once('exceptions/InsufficientFundsException.php');

use GisBundle\Models\CasinoApi;
use GisBundle\MysqlExample\Client;
use GisBundle\Exceptions\GisException;

$casinoApi = new CasinoApi((new Client));

try {
    $casinoApi->processRequest();
} catch (GisException $e) {
    $casinoApi->errorResponse($e->getGisErrorCode(), $e->getMessage());
} catch (Exception $e) {
    $casinoApi->errorResponse('INTERNAL_ERROR', 'Something goes wrong');
}

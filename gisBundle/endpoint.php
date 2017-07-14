<?php

require_once('models/CasinoApi.php');
require_once('interfaces/IClient.php');
require_once('mysqlExample/Client.php');
require_once('responses/Response.php');
require_once('responses/BalanceResponse.php');
require_once('responses/BetResponse.php');
require_once('responses/WinResponse.php');
require_once('responses/RefundResponse.php');
require_once('responses/ErrorResponse.php');

use GisBundle\Models\CasinoApi;
use GisBundle\MysqlExample\Client;

$client = new Client;
$casinoApi = new CasinoApi($client);

$casinoApi->processRequest();

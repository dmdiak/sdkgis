<?php

require_once('src/CasinoApi.php');
require_once('src/interfaces/IClient.php');
require_once('src/mysqlExample/Client.php');
require_once('src/responses/Response.php');
require_once('src/responses/BalanceResponse.php');
require_once('src/responses/BetResponse.php');
require_once('src/responses/WinResponse.php');
require_once('src/responses/RefundResponse.php');
require_once('src/responses/ErrorResponse.php');

use SdkGis\CasinoApi;
use SdkGis\MysqlExample\Client;

$client = new Client;
$casinoApi = new CasinoApi($client);

$casinoApi->processRequest();

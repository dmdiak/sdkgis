<?php
require_once('src/CasinoApi.php');
require_once('src/mysqlExample/Client.php');

use SdkGis\CasinoApi;
use SdkGis\Client;

$client = new Client;
$casinoApi = new CasinoApi($client);

$casinoApi->processRequest();

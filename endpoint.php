<?php
require_once('src/CasinoApi.php');

use SdkGis\CasinoApi;

$casinoApi = new CasinoApi();

$casinoApi->processRequest();

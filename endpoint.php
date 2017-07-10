<?php

require_once('src/GisApi.php');

use SdkGis\GisApiClient;

$config = include('src/config/config.php');
$gisApiClient = new GisApiClient($config);

$gisApiClient->getGames();

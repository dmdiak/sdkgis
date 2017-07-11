<?php

require_once('src/GisApi.php');

use SdkGis\GisApi;

$config = include('src/config/config.php');
$gisApi = new GisApi($config);

var_dump($gisApi->getLimits());
<?php

require_once('src/GisApi.php');

use SdkGis\GisApi;

$config = include('src/config/config.php');
$gisApi = new GisApi($config);

var_dump($gisApi->initDemoGame('38651906921e4aec72793984a91127bfdc300885'));
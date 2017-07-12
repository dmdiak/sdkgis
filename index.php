<?php

require_once('src/GisApi.php');

use SdkGis\GisApi;

session_start();
$sessionId = session_id();

$gisApi = new GisApi();

$result = $gisApi->initGame('2dcab0ef68c26aceb2b3f139a44eaad08f1ecdfa', '1', 'player1', 'USD', $sessionId);

echo '<a href="' . $result['url'] . '">GO TO THE GAME!!!</a>';

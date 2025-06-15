<?php

use Proxy\Api\Api;

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/functions.php';
Api::__constructStatic();

$stats = null;
$res = Api::sendSync('GET', 'v2/stats/mini', null);
if ($res->getStatusCode() == 200) {
    $stats = json_decode($res->getBody(), false);
}

$stats->monthAllHours = is_null($stats->monthAllHours) ? 0 : $stats->monthAllHours;
$stats->totalAllMiles = is_null($stats->totalAllMiles) ? 0 : number_format($stats->totalAllMiles);
$stats->totalActivePilots = number_format($stats->totalActivePilots);
$stats->totalAllHours = is_null($stats->totalAllHours) ? 0 : $stats->totalAllHours;
$stats->totalPilots = number_format($stats->totalPilots);
$stats->totalAllFlights = number_format($stats->totalAllFlights);
$stats->monthFlights = number_format($stats->monthFlights);
$stats->monthMiles = number_format($stats->monthMiles);

echo json_encode($stats);

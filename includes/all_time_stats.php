<?php

use Proxy\Api\Api;

include '../config.php';
include '../lib/functions.php';
Api::__constructStatic();

$allStats = null;
$res = Api::sendSync('GET', 'v1/stats/alltimestats', null);
if ($res->getStatusCode() == 200) {
    $allStats = json_decode($res->getBody(), false);
}


$allStats->cargo = getCargoDisplayValue($allStats->cargo);
$allStats->fuel = getFuelDisplayValue($allStats->fuel);
$allStats->activePilots = number_format($allStats->activePilots);
$allStats->passengers = number_format($allStats->passengers);
$allStats->totalSchedules = number_format($allStats->totalSchedules);
$allStats->flights = number_format($allStats->flights);
$allStats->miles = number_format($allStats->miles);

echo json_encode($allStats);

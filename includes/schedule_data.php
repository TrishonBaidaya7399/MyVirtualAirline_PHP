<?php
use Proxy\Api\Api;
include '../config.php';
include '../lib/functions.php';
Api::__constructStatic();

$schedule = null;
$res = Api::sendSync('GET', 'v1/operations/schedule', null);
if ($res->getStatusCode() == 200) {
    $schedule = json_decode($res->getBody(), true);
}

if (!empty($schedule)) {
    foreach ($schedule as &$flight) {
        $flight["flightNumberDisplay"] = '<i class="fa fa-plane"></i> <a href="' . website_base_url . 'flight_info.php?id=' . $flight['id'] . '" target="_blank">' . $flight["flightNumber"] . '</a>';
    }
}

echo json_encode($schedule);
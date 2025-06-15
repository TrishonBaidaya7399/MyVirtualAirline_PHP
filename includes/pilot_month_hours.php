<?php
header('Content-Type: application/json; charset=utf-8');

use Proxy\Api\Api;

include '../config.php';
include '../lib/functions.php';
Api::__constructStatic();

$year = cleanString($_REQUEST['year']);
$month = cleanString($_REQUEST['month']);

$stats = null;
$res = Api::sendSync('GET', 'v1/stats/pom/' . $year . '/' . $month, null);
if ($res->getStatusCode() == 200) {
    $stats = json_decode($res->getBody(), true);
}

$counter = 1;
if (!empty($stats)) {
    foreach ($stats as &$stat) {
        if ($stat["monthHours"] > 0) {
            switch ($counter) {
                case 1:
                    $stat["badge"] = '<i class="fa fa-trophy golden" title="1st"></i>';
                    break;
                case 2:
                    $stat["badge"] = '<i class="fa fa-trophy silver" title="2nd"></i>';
                    break;
                case 3:
                    $stat["badge"] = '<i class="fa fa-trophy bronze" title="3rd"></i>';
                    break;
                default:
                    $stat["badge"] = $counter;
                    break;
            }
            $counter++;
        } else {
            $stat["badge"] = "&nbsp;";
        }
        $stat["callsign"] = '<a href="/profile.php?id=' . $stat["id"] . '" target="_blank">' . $stat["callsign"] . '</a>';
    }
}

echo json_encode($stats);

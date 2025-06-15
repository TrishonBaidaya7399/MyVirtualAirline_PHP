<?php

use Proxy\Api\Api;

require_once __DIR__ . '/../proxy/api.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/functions.php';
Api::__constructStatic();
$flights = null;
$res = Api::sendAsync('GET', 'v1/map/acars', null);
if ($res->getStatusCode() == 200) {
    $flights = json_decode($res->getBody(), true);
}

if (!empty($flights)) {
    foreach ($flights as &$flight) {
        if (!empty($flight["arrLong"]) && !empty($flight["arrLat"])) {
            $totalDistance = round(get_distance(floatval($flight["startLong"]), floatval($flight["startLat"]), floatval($flight["arrLong"]), floatval($flight["arrLat"])));
            $distanceFlown = round(get_distance(floatval($flight["startLong"]), floatval($flight["startLat"]), floatval($flight["presLong"]), floatval($flight["presLat"])));
            $distanceRemaining = get_distance(floatval($flight["presLong"]), floatval($flight["presLat"]), floatval($flight["arrLong"]), floatval($flight["arrLat"]));
            if (is_nan($distanceFlown)) {
                $distanceFlown = 0;
            }
            $etaMins = 0;
            $etaHours = 0;
            if ($flight["statSpeed"] > 30) {
                $etaMins = round($distanceRemaining) / $flight["statSpeed"] * 60;
                $etaHours = intdiv(intval($etaMins), 60);
                $etaMins -= ($etaHours * 60);
            }
            $flight["total_dist"] = $totalDistance;
            $flight["dist_flown"] = $distanceFlown;
            $flight["eta"] = str_pad($etaHours, 2, '0', STR_PAD_LEFT) . ':' . str_pad(round($etaMins), 2, '0', STR_PAD_LEFT);
            $flight["perc_complete"] = $totalDistance - $distanceFlown > 5 ? round($distanceFlown / $totalDistance * 100) : 100;
            $flight["dtg"] = round($distanceRemaining, 1) < 1 ? 0 : round($distanceRemaining, 1);
            $flight["statHdg"] = str_pad($flight["statHdg"], 3, '0', STR_PAD_LEFT);
        }
    }
    echo json_encode($flights);
}

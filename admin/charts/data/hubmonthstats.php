<?php

use Proxy\Api\Api;

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../lib/functions.php';
session_start();
Api::__constructStatic();
validateAdminSession();
$stats = null;
$res = Api::sendSync('GET', 'v1/stats/hubmonthly', null);
if ($res->getStatusCode() == 200) {
    $stats = json_decode($res->getBody(), true);
}

if (!empty($stats)) {
    foreach ($stats as &$stat) {
        $stat["cargo"] = cargo_weight_display == 0 ? $stat["cargo"] : $stat["cargo"] * 2.205;
        $stat["fuel"] = fuel_weight_display == 0 ? $stat["fuel"] : $stat["fuel"] * 2.205;
    }
}

echo json_encode($stats);

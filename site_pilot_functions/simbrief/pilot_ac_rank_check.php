<?php
use Proxy\Api\Api;
include '../../lib/functions.php';
include '../../config.php';
Api::__constructStatic();
session_start();
validateSession();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $icao = cleanString($_POST['type']);
    $res = Api::sendSync('GET', 'v1/rank/pilot-aircraft-valid/' . $_SESSION['pilotid'] . '/' . $icao, null);

    http_response_code($res->getStatusCode());
    echo $res->getBody();
    exit;
}
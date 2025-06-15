<?php
header('Content-Type: application/json; charset=utf-8');

use Proxy\Api\Api;

include '../config.php';
include '../lib/functions.php';
Api::__constructStatic();

$res = Api::sendSync('POST', 'v1/operations/roster-datatable', $_POST);

echo $res->getBody();

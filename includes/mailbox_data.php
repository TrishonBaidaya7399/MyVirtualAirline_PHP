<?php
header('Content-Type: application/json; charset=utf-8');

use Proxy\Api\Api;

include '../config.php';
include '../lib/functions.php';
Api::__constructStatic();

session_start();

$mailbox = null;
$res = Api::sendSync('GET', 'v1/mailbox', null);
if ($res->getStatusCode() == 200) {
    $mailbox = json_decode($res->getBody(), true);
}

echo json_encode($mailbox);

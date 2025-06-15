<?php
use Proxy\Api\Api;
require_once __DIR__ . '/../proxy/api.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/functions.php';
Api::__constructStatic();

$id = cleanString($_REQUEST['id']);

$res = Api::sendAsync('GET', 'v1/map/path-data/' . $id, null);
if ($res->getStatusCode() == 200) {
    echo $res->getBody();
}
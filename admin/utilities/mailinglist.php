<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();

$res = Api::sendSync('GET', 'v1/pilot/mailinglist', null);
if ($res->getStatusCode() == 200) {
    header('Content-type: application/csv');
    header('Content-Disposition: inline; filename="mailing-list.csv"');
    echo $res->getBody();
} else {
    header('Location: ' . website_base_url . 'admin');
}

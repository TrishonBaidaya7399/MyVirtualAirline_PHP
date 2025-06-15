<?php
use Proxy\Api\Api;
include '../lib/functions.php';
include '../config.php';
Api::__constructStatic();
session_start();
validateSession();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$icao = cleanString($_POST['icao']);

	$data = [
		'Icao' => $icao
	];
	$res = Api::sendSync('POST', 'v1/pilot/jumpseat/', $data);

	http_response_code($res->getStatusCode()); 
	echo $res->getBody();
	exit;
}
?>
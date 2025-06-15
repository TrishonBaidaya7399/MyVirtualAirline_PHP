<?php

use Proxy\Api\Api;

require_once __DIR__ . "/../proxy/api.php";

function userHasPermission($role_id)
{
    if (isset($_SESSION['permissions'])) {
        if (is_array($_SESSION['permissions'])) {
            if (in_array($role_id, $_SESSION['permissions']) || $_SESSION['owner'] == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            if ($_SESSION['owner'] == 1) {
                return true;
            } else {
                return false;
            }
        }
    } else {
        if ($_SESSION['owner'] == 1) {
            return true;
        } else {
            return false;
        }
    }
}

function validateSession()
{
    if (!isset($_SESSION['pilotid'])) {
        header('Location: /authentication/login.php');
        exit();
    }
    if (!isset($_SESSION['token'])) {
        session_destroy();
        header('Location: /authentication/login.php');
        exit();
    } else {
        $token = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $_SESSION['token'])[1]))));
        if (!isset($token->given_name)) {
            session_destroy();
            header('Location: /authentication/login.php');
            exit();
        }
    }
}

function validateAdminSession()
{
    if (!isset($_SESSION['site_level'])) {
        header('Location: /authentication/login.php');
        exit();
    }
    if (!isset($_SESSION['token'])) {
        session_destroy();
        header('Location: /authentication/login.php');
        exit();
    } else {
        $token = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $_SESSION['token'])[1]))));
        if (!isset($token->given_name)) {
            session_destroy();
            header('Location: /authentication/login.php');
            exit();
        }
    }
}

function store_uploaded_image($html_element_name, $new_img_width, $new_img_height, $uploads_folder)
{
    $target_dir = __DIR__ . '/../uploads/' . $uploads_folder . '/';
    $file_name = basename(date("YmdHis.") . explode('.', $_FILES[$html_element_name]["name"])[1]);
    $target_file = $target_dir . $file_name;

    $image = new SimpleImage();
    $image->load($_FILES[$html_element_name]['tmp_name']);
    $image->resizeToWidth($new_img_width, $new_img_height);
    $image->save($target_file);

    return $file_name;
}

function get_metar($location)
{
    $url = "https://aviationweather.gov/cgi-bin/data/metar.php?ids=" . $location . "&hours=0&order=id%2C-obs&sep=true&taf=true&format=html";
    $fileName = $url;
    $metar = '';
    $fileData = @file($fileName);
    if ($fileData != false) {
        $date = current($fileData);
        $utc = strtotime(trim($date));
        $time = date("D, F jS Y g:i A", $utc);
        while (next($fileData)) {
            $line = current($fileData);
            $metar .= ' ' . trim($line);
        }
        $metar = trim(str_replace('  ', ' ', $metar));
        $metar = str_replace("Aviation Digital Data Service (ADDS)", "", $metar);
        $metar = str_replace("Output produced by METARs form", "", $metar);
        $metar = str_replace("found at", "Data provider", $metar);
        $metar = str_replace("#9999CC", "Black", $metar);
        $metar = str_replace('<BR> Data provider <A HREF="http://aviationweather.gov/adds/metars/"> http://aviationweather.gov/adds/metars/</A>', "", $metar);

        if ($metar == '') {
            echo 'No METAR information available.';
        } else {
            echo $metar;
        }
    } else {
        echo 'No METAR information available.';
    }
}

function get_distance($start_long, $start_lat, $end_long, $end_lat)
{
    $t = ($start_long - $end_long);
    $distance = sin(degree_to_radius($start_lat)) * sin(degree_to_radius($end_lat)) + cos(degree_to_radius($start_lat)) * cos(degree_to_radius($end_lat)) * cos(degree_to_radius($t));
    $distance = acos($distance);
    $distance = radius_to_degree($distance);
    $distance = $distance * 60 * 1.1515;
    $distance = $distance * 1.1507;

    return $distance;
}

function degree_to_radius($deg)
{
    return ($deg * pi() / 180.0);
}

function radius_to_degree($rad)
{
    return ($rad / pi() * 180.0);
}

function limit($value, $limit = 100, $end = '...')
{
    if (mb_strwidth($value, 'UTF-8') <= $limit) {
        return $value;
    }

    return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
}

function getCargoDisplayValue($value)
{
    if ($value == 0 || $value == null) {
        return 0;
    }
    if (cargo_weight_display == 0) { //kg
        return number_format(round($value, 0)) . 'kg';
    } else { //lb
        return number_format(round(($value * 2.205), 0)) . 'lb';
    }
}

function convertLbToKg($value)
{
    return round(($value * 0.45359237), 2);
}

function getFuelDisplayValue($value)
{
    if ($value == 0 || $value == null) {
        return 0;
    }
    if (fuel_weight_display == 1) { //lb
        return number_format(round(intval($value), 0)) . 'lb';
    } else { //kg
        return number_format(round((intval($value) / 2.205), 0)) . 'kg';
    }
}

function formatHoursDisplay($hours)
{
    if ($hours == 0 || $hours == null) {
        return 0;
    }
    $hour = (int) explode(":", $hours)[0];
    $min = (int) explode(":", $hours)[1];

    if ($min > 0) {
        $hour = $hour + 1;
    }

    return number_format($hour);
}

function displayCleanHours($hours)
{
    if ($hours == 0 || $hours == null) {
        return 0;
    }
    $hour = (int) explode(":", $hours)[0];
    $min = (int) explode(":", $hours)[1];

    return number_format($hour) . ':' . $min;
}

function getAirlineDetails()
{
    Api::__constructStatic();
    return Api::sendSync('GET', 'v1/airline', null);
}

function cleanString($string)
{
    return trim(strip_tags($string));
}

function hasPropertyValue(array $objects, $property, $value): bool
{
    foreach ($objects as $object) {
        if (property_exists($object, $property) && $object->{$property} === $value) {
            return true;
        }
    }
    return false;
}

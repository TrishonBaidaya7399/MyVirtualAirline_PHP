<?php
use Proxy\Api\Api;
require_once __DIR__ . '/../proxy/api.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/functions.php';
Api::__constructStatic();
session_start();
validateSession();
$id = $_SESSION['pilotid'];
$logbook = null;
$res = Api::sendSync('GET', 'v1/pilot/logbook/map/' . $id, null);
if ($res->getStatusCode() == 200) {
    $logbook = json_decode($res->getBody(), true);
}
if (!empty($logbook)) {
    foreach ($logbook as &$item) {
        $item["logbookItem"]["date"] = (new DateTime($item["logbookItem"]["date"]))->format('Y-m-d');
        $item["logbookItem"]["aircraft"] = '<span title="' . $item["logbookItem"]["aircraft"] . '">' . limit($item["logbookItem"]["aircraft"], 15, "...") . '</span>';
        $item["logbookItem"]["flightNumber"] = '<a href="' . website_base_url . 'pirep_info.php?id=' . $item["logbookItem"]['id'] . '" target="_blank">' . $item["logbookItem"]["flightNumber"] . '</a>';
        switch ($item["logbookItem"]["approvedStatus"]) {
            case 0:
                $item["logbookItem"]["approvedStatusDescription"] = "<span style='color:orange;'>Pending Approval</span>";
                break;
            case 1:
                $item["logbookItem"]["approvedStatusDescription"] = "<span style='color:green;'>Approved</span>";
                break;
            case 2:
                $item["logbookItem"]["approvedStatusDescription"] = "<span style='color:red;'>Denied</span>";
                break;
        }
    }
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>

<?php include '../includes/header.php';?>
<link rel="stylesheet" type="text/css" href="<?php echo website_base_url; ?>assets/plugins/datatables/datatables.min.css" />
<link rel="stylesheet" href="<?php echo website_base_url; ?>assets/plugins/leaflet/leaflet.css" />
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/leaflet.js"></script>
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/arc.js" type="text/javascript"></script>
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/map.helpers.js" type="text/javascript"></script>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.route-map-section {
    position: relative;
    padding: 60px 0;
    min-height: calc(100vh - 128px);
    background-image: url('./assets/images/backgrounds/world_map2.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.route-map-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

.route-map-section .container {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.offset-header {
    padding-top: 100px;
}

.route-map-title-wrapper {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
    justify-content: center;
}

.route-map-title {
    font-size: 4rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    letter-spacing: 2px;
    font-family: 'Montserrat', sans-serif;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.route-map-icon {
    font-size: 3rem;
    color: rgba(255, 215, 0, 1);
    opacity: 0.9;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.route-map-glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 30px;
    width: 100%;
    max-width: 100%;
    color: #fff;
}

.route-map-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.route-map-header {
    padding: 30px;
    text-align: center;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.route-map-header h1 {
    font-size: 2.5rem;
    font-weight: 300;
    margin-bottom: 20px;
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.route-map-header hr {
    border: none;
    height: 2px;
    background: linear-gradient(45deg, rgba(255, 215, 0, 0.8), rgba(255, 255, 255, 0.3));
    margin: 15px auto;
    width: 80%;
    border-radius: 2px;
}

.route-map-header p {
    font-size: 2rem;
    line-height: 1.6;
    margin-bottom: 15px;
    color: rgba(0, 0, 0, 0.9);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.route-map-header strong {
    color: rgba(255, 215, 0, 1);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.route-map-header a {
    color: rgba(255, 215, 0, 1);
    text-decoration: none;
    transition: all 0.3s ease;
}

.route-map-header a:hover {
    color: rgba(255, 255, 255, 1);
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}

.route-map-controls {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 15px;
    margin-top: 10px;
    max-width: 50%;
}

.icao {
    width: 120px;
    height: 38px;
    display: inline-block;
    text-transform: uppercase;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    padding: 8px 12px;
    color: #333;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
}

.icao:focus {
    outline: none;
    background: rgba(255, 255, 255, 1);
    border-color: rgba(255, 215, 0, 0.8);
    box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
}

.btn-find {
    background: linear-gradient(45deg, rgba(255, 215, 0, 0.8), rgba(255, 193, 7, 0.9));
    border: 1px solid rgba(255, 215, 0, 0.5);
    color: #333;
    font-weight: 600;
    padding: 8px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-find:hover {
    background: linear-gradient(45deg, rgba(255, 215, 0, 1), rgba(255, 193, 7, 1));
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
    color: #222;
}

.find-msg {
    color: #ff6b6b;
    display: block;
    font-size: 14px;
    font-weight: 600;
    margin-top: 10px;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.map-container-glass {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 30px;
    transition: all 0.3s ease;
    width: 100%;
    max-width: 100%;
}

.map-container-glass:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

#map {
    height: 600px;
    border-radius: 15px;
    overflow: hidden;
}

.flights-table-glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    width: 100%;
    max-width: 100%;
}

.flights-table-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.flights-table-header {
    background: rgba(255, 255, 255, 0.9);
    color: rgba(255, 255, 255, 1);
    padding: 20px 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.flights-table-header h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    gap: 10px;
}

.flights-table-header .fa-plane {
    color: rgba(255, 215, 0, 1);
    font-size: 1.5rem;
}

.flights-table-wrapper {
    overflow-x: auto;
    max-height: 600px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

.flights-table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.flights-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

.flights-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 193, 7, 0.5);
    border-radius: 4px;
}

.flights-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 193, 7, 0.7);
}

.flights-table {
    margin: 0;
    background: transparent;
    color: rgba(255, 255, 255, 0.9);
    width: 100%;
}

.flights-table thead th {
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
    border: none !important;
    padding: 15px 12px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
    z-index: 10;
    text-wrap: nowrap;
}

.flights-table tbody tr {
    background: rgba(255, 255, 255, 0.8) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
    transition: all 0.3s ease;
}

.flights-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.95) !important;
    transform: scale(1.01);
}

.flights-table tbody td {
    padding: 15px 12px;
    border: none !important;
    vertical-align: middle;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9);
}

.flights-table tbody tr:last-child {
    border-bottom: none !important;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    color: rgba(255, 255, 255, 0.9);
    padding: 10px;
    margin: 0;
}

.dataTables_wrapper .dataTables_length {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.dataTables_wrapper .dataTables_filter {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.dataTables_wrapper .dataTables_info {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.dataTables_wrapper .dataTables_paginate {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.dataTables_wrapper .dataTables_filter input {
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid rgba(255, 215, 0, 0.3);
    border-radius: 6px;
    padding: 6px 10px;
    color: rgba(0, 0, 0, 0.9);
}

.paginate_button {
    color: #333 !important;
    border-radius: 4px !important;
    margin: 0 2px !important;
}

.paginate_button:hover {
    background: rgba(255, 215, 0, 1) !important;
    color: #222 !important;
}

.paginate_button.current {
    background: rgba(255, 193, 7, 1) !important;
    color: #222 !important;
}

.no-data-message {
    text-align: center;
    padding: 60px 20px;
    color: #333;
    font-style: italic;
    background: rgba(255, 255, 255, 0.8);
    font-size: 1.2rem;
}

#flights_wrapper .row .col-sm-6 {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

#flights_wrapper .row .col-sm-5 {
    padding-left: 0 !important;
    padding-right: 0 !important;
    background: transparent !important;
    color: rgba(0, 0, 0, 0.9);
}

#flights_wrapper .row .col-sm-7 {
    background: transparent !important;
    padding: 0 !important;
}

#flights_wrapper .row .col-sm-12 {
    overflow-x: auto;
}

@media (max-width: 1200px) {
    .route-map-section {
        padding: 50px 0;
    }
    .route-map-title {
        font-size: 2.5rem;
    }
    .route-map-icon {
        font-size: 2rem;
    }
    .route-map-header h1 {
        font-size: 2.2rem;
    }
    .flights-table thead th,
    .flights-table tbody td {
        padding: 12px 10px;
        font-size: 13px;
    }
}

@media (max-width: 992px) {
    .route-map-section {
        padding: 40px 0;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 80px;
    }
    .route-map-title {
        font-size: 2.2rem;
    }
    .route-map-icon {
        font-size: 2rem;
    }
    .route-map-header h1 {
        font-size: 2rem;
    }
    .route-map-glass-card,
    .map-container-glass,
    .flights-table-glass-card {
        margin: 0 15px 30px 15px;
        border-radius: 12px;
    }
    #map {
        height: 500px;
        border-radius: 12px;
    }
    .flights-table-wrapper {
        max-height: 500px;
    }
}

@media (max-width: 768px) {
    .route-map-section {
        padding: 30px 0;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 60px;
    }
    .route-map-title {
        font-size: 2rem;
    }
    .route-map-icon {
        font-size: 1.8rem;
    }
    .route-map-header {
        padding: 25px 20px;
    }
    .route-map-header h1 {
        font-size: 1.8rem;
    }
    .route-map-controls {
        flex-direction: row;
        gap: 10px;
        max-width: 70%;
    }
    .route-map-glass-card,
    .map-container-glass,
    .flights-table-glass-card {
        margin: 0 10px 25px 10px;
        border-radius: 10px;
    }
    #map {
        height: 400px;
        border-radius: 10px;
    }
    .flights-table-wrapper {
        max-height: 400px;
    }
    .flights-table {
        font-size: 12px;
    }
    .flights-table thead th {
        padding: 12px 8px;
        font-size: 11px;
        white-space: nowrap;
    }
    .flights-table tbody td {
        padding: 12px 8px;
        font-size: 12px;
        white-space: nowrap;
    }
    .flights-table-header {
        padding: 15px 20px;
    }
    .flights-table-header h3 {
        font-size: 1.5rem;
    }
}

@media (max-width: 576px) {
    .route-map-section {
        padding: 25px 0;
    }
    .offset-header {
        padding-top: 50px;
    }
    .route-map-title {
        font-size: 1.8rem;
    }
    .route-map-icon {
        font-size: 1.5rem;
    }
    .route-map-controls {
        max-width: 100%;
    }
    .route-map-title-wrapper {
        gap: 10px;
    }
    .route-map-header {
        padding: 20px 15px;
    }
    .route-map-header h1 {
        font-size: 1.6rem;
    }
    .route-map-glass-card,
    .map-container-glass,
    .flights-table-glass-card {
        margin: 0 5px 20px 5px;
        border-radius: 8px;
    }
    #map {
        height: 350px;
        border-radius: 8px;
    }
    .flights-table-wrapper {
        max-height: 350px;
    }
    .icao {
        width: 100px;
        height: 36px;
    }
    .btn-find {
        padding: 6px 15px;
    }
    .flights-table-header {
        padding: 12px 15px;
    }
    .flights-table-header h3 {
        font-size: 1.3rem;
    }
}

@media (max-width: 480px) {
    .route-map-section {
        padding: 20px 0;
    }
    .offset-header {
        padding-top: 40px;
    }
    .route-map-title {
        font-size: 1.6rem;
    }
    .route-map-icon {
        font-size: 1.3rem;
    }
    #map {
        height: 300px;
    }
    .flights-table-wrapper {
        max-height: 300px;
    }
    .no-data-message {
        padding: 40px 15px;
        font-size: 16px;
    }
}

@media print {
    .route-map-section {
        background: white;
        padding: 20px 0;
    }
    .route-map-section::before {
        display: none;
    }
    .route-map-title,
    .route-map-header h1,
    .route-map-header p {
        color: black;
        text-shadow: none;
    }
    .route-map-glass-card,
    .map-container-glass,
    .flights-table-glass-card {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
    }
    .flights-table thead th,
    .flights-table tbody td {
        color: white;
    }
}
</style>

<section id="content" class="section route-map-section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="route-map-glass-card">
                    <div class="route-map-header">
                        <div class="route-map-title-wrapper">
                            <h3 class="route-map-title">Logbook Map</h3>
                            <i class="fa fa-map route-map-icon" aria-hidden="true"></i>
                        </div>
                        <hr />
                        <p>Below is a map of all the airports you have visited. Select a location on the map to view your
                            previous flights to and from the airport. You can also
                            search previous flights by ICAO.</p>
                        <div class="route-map-controls">
                            <input type="text" maxlength="4" name="icao" id="icao" class="form-control icao" placeholder="ICAO" />
                            <input name="find" onclick="find()" type="submit" id="find" value="Find" class="btn btn-default btn-find">
                        </div>
                        <span class="find-msg"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="map-container-glass">
                    <div id="map"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="global-heading">
                    <h3 class="global-title"><i class="fa fa-plane"></i> Previous Flights</h3>
                </div>
                <div class="flights-table-glass-card">
                    <div class="flights-table-wrapper">
                        <table class="table table-striped flights-table" id="flights" width="100%">
                            <thead>
                                <tr>
                                    <th><strong>Date</strong></th>
                                    <th><strong>Flight No.</strong></th>
                                    <th><strong>Depart</strong></th>
                                    <th><strong>Arrive</strong></th>
                                    <th><strong>Duration</strong></th>
                                    <th><strong>Aircraft</strong></th>
                                    <th><strong>Status</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php';?>
<script type="text/javascript" src="<?php echo website_base_url; ?>assets/plugins/datatables/datatables.min.js"></script>
<script type="text/javascript">
var logbook = <?php echo json_encode($logbook); ?>;
var _0x277a74 = _0x4dc7;

function _0x4dc7(_0x336281, _0x31db0f) {
    var _0x994138 = _0x9941();
    return _0x4dc7 = function(_0x4dc7a6, _0x330a97) {
        _0x4dc7a6 = _0x4dc7a6 - 0x185;
        var _0x15f26b = _0x994138[_0x4dc7a6];
        return _0x15f26b;
    }, _0x4dc7(_0x336281, _0x31db0f);
}(function(_0x58cd4b, _0x3a562f) {
    var _0x2cba4e = _0x4dc7,
        _0x585085 = _0x58cd4b();
    while (!![]) {
        try {
            var _0x509d93 = -parseInt(_0x2cba4e(0x18a)) / 0x1 + parseInt(_0x2cba4e(0x1a2)) / 0x2 * (-parseInt(
                    _0x2cba4e(0x1ca)) / 0x3) + parseInt(_0x2cba4e(0x1ad)) / 0x4 + -parseInt(_0x2cba4e(0x19e)) /
                0x5 * (parseInt(_0x2cba4e(0x1b8)) / 0x6) + parseInt(_0x2cba4e(0x1a4)) / 0x7 * (parseInt(_0x2cba4e(
                    0x199)) / 0x8) + parseInt(_0x2cba4e(0x19f)) / 0x9 * (-parseInt(_0x2cba4e(0x1c5)) / 0xa) +
                parseInt(_0x2cba4e(0x19d)) / 0xb;
            if (_0x509d93 === _0x3a562f) break;
            else _0x585085['push'](_0x585085['shift']());
        } catch (_0x31122d) {
            _0x585085['push'](_0x585085['shift']());
        }
    }
}(_0x9941, 0xe3e1b));

function _0x9941() {
    var _0x340692 = ['ready', 'length', 'fitBounds', 'Location\x20not\x20found.', '</strong></p>', '29896kzPtdh',
        'removeLayer', 'featureGroup', '.find-msg', '36462690sccRom', '959970IFOTEV', '3249UlwKpR', 'filter', 'red',
        '178mCCeRY', 'mouseout', '791MoHaTT', 'polyline', 'lng', 'airportInfo', 'remove', 'html',
        '<p><strong\x20style=\x22color:black;\x20font-size:12px\x22>', 'bindPopup', 'setContent', '1072104WHwIFf',
        'destroy', 'map', 'divIcon', '</span></div>', 'push', 'find', 'geometries',
        'https://tile.openstreetmap.org/{z}/{x}/{y}.png', 'name', 'click', '48VYhDQc', 'GreatCircle', 'eachLayer',
        'arrIcao', 'DataTable', 'flightNumber', 'Arc', '_popup', 'toUpperCase', 'fire', 'popup', 'getMarkerById',
        'mouseover', '3070wriwMf', '<div\x20class=\x22label_content\x22><span>', 'date', 'hasLayer', 'data',
        '9864yUttKf', 'addTo', 'logbookItem', 'aircraft', 'getFeatureGroupById', 'openPopup', 'getBounds',
        'depIcao', 'Marker', '#icao', 'marker', '1132283ddMHtP', 'iconicon', 'coords', 'icao', '_map', 'closePopup',
        'lat', 'acarsId', 'tileLayer'
    ];
    _0x9941 = function() {
        return _0x340692;
    };
    return _0x9941();
}
var map, dataTable, dataSet = [],
    airports = [];
$(document)[_0x277a74(0x193)](function() {
    var _0x42afd3 = _0x277a74;
    map = L[_0x42afd3(0x1af)](_0x42afd3(0x1af), {
        'center': [0x0, 0x0],
        'zoom': 0x2,
        'worldCopyJump': !![],
        'attributionControl': ![]
    }), L[_0x42afd3(0x192)](_0x42afd3(0x1b5), {
        'maxZoom': 0x13,
        'attribution': ''
    })[_0x42afd3(0x1cb)](map), loadDestinations(), initFlightTable();
});

function find() {
    var _0x2df189 = _0x277a74;
    $(_0x2df189(0x19c))[_0x2df189(0x1a9)](''), removeActiveMarker();
    var _0x138f24 = map[_0x2df189(0x1c3)]($(_0x2df189(0x188))[_0x2df189(0x197)]()[_0x2df189(0x1c0)]());
    _0x138f24 != null ? _0x138f24[_0x2df189(0x1c1)](_0x2df189(0x1b7)) : $(_0x2df189(0x19c))[_0x2df189(0x1a9)](_0x2df189(
        0x196));
}

function removeActiveMarker() {
    var _0x47cd76 = _0x277a74;
    map[_0x47cd76(0x1ba)](function(_0x4e5b03) {
        var _0x4b8162 = _0x47cd76;
        _0x4e5b03 instanceof L[_0x4b8162(0x187)] && (marker = _0x4e5b03, marker[_0x4b8162(0x18e)][_0x4b8162(
            0x1c8)](marker[_0x4b8162(0x1bf)]) && marker[_0x4b8162(0x1c1)](_0x4b8162(0x1a8)));
    });
}

function exists(_0x23e165) {
    var _0x1eb988 = _0x277a74,
        _0x2b2c45, _0x54b3d6 = 0x0;
    while (_0x2b2c45 = airports[_0x54b3d6++])
        if (_0x2b2c45[_0x1eb988(0x18d)] == _0x23e165) return --_0x54b3d6;
    return -0x1;
}

function loadDestinations() {
    var _0xca68fd = _0x277a74,
        _0x423d36 = L[_0xca68fd(0x19b)]()[_0xca68fd(0x1cb)](map);
    _0x423d36['id'] = 0x1;
    if (airports[_0xca68fd(0x194)] < 0x1)
        for (var _0x4a4c6c in logbook) {
            exists(logbook[_0x4a4c6c][_0xca68fd(0x1a7)][0x0][_0xca68fd(0x18d)]) < 0x0 && airports[_0xca68fd(0x1b2)](
                logbook[_0x4a4c6c]['airportInfo'][0x0]), exists(logbook[_0x4a4c6c]['airportInfo'][0x1][_0xca68fd(
                0x18d)]) < 0x0 && airports[_0xca68fd(0x1b2)](logbook[_0x4a4c6c][_0xca68fd(0x1a7)][0x1]);
        }
    for (var _0x4a4c6c in airports) {
        var _0x49736d = L[_0xca68fd(0x1c2)]({
            'offset': [0x0, -0x14]
        })[_0xca68fd(0x1ac)](_0xca68fd(0x1aa) + airports[_0x4a4c6c][_0xca68fd(0x1b6)] + _0xca68fd(0x198));
        marker = new L[(_0xca68fd(0x189))]([airports[_0x4a4c6c][_0xca68fd(0x190)], airports[_0x4a4c6c][_0xca68fd(
                0x1a6)]], {
                'icon': L['divIcon']({
                    'className': _0xca68fd(0x18b),
                    'html': _0xca68fd(0x1c6) + airports[_0x4a4c6c][_0xca68fd(0x18d)] + _0xca68fd(0x1b1),
                    'iconAnchor': [0x14, 0x1e]
                })
            })['on'](_0xca68fd(0x1b7), markerClick)[_0xca68fd(0x1cb)](_0x423d36), marker['bindPopup'](_0x49736d),
            marker['on'](_0xca68fd(0x1c4), function(_0x1cefbb) {
                this['openPopup']();
            }), marker['on'](_0xca68fd(0x1a3), function(_0x25cc60) {
                var _0x51d82f = _0xca68fd;
                this[_0x51d82f(0x18f)]();
            }), marker[_0xca68fd(0x191)] = airports[_0x4a4c6c]['icao'], marker[_0xca68fd(0x18d)] = airports[_0x4a4c6c][
                _0xca68fd(0x18d)
            ], marker[_0xca68fd(0x1c9)] = airports[_0x4a4c6c];
    }
    map[_0xca68fd(0x195)](_0x423d36[_0xca68fd(0x185)]());
}

function clearDestinations() {
    var _0x5d3627 = _0x277a74,
        _0x326891 = map[_0x5d3627(0x1ce)](0x1);
    _0x326891 != null && map[_0x5d3627(0x19a)](_0x326891);
}

function clearDestinationFlights() {
    var _0x1ff307 = map['getFeatureGroupById'](0x2);
    _0x1ff307 != null && map['removeLayer'](_0x1ff307);
}

function markerPopupClose(_0x69d191) {
    clearDestinations(), clearDestinationFlights(), loadDestinations(), dataSet = [], initFlightTable();
}

function markerClick(_0xc4ea24) {
    var _0xae225c = _0x277a74,
        _0x38d6fb = L[_0xae225c(0x19b)]();
    _0x38d6fb['id'] = 0x2;
    var _0x2e5e78 = this['data'];
    clearDestinations();
    var _0x8937f7 = L[_0xae225c(0x1c2)]({
        'offset': [0x0, -0x14]
    })[_0xae225c(0x1ac)](_0xae225c(0x1aa) + _0x2e5e78[_0xae225c(0x1b6)] + '</strong></p>');
    marker = new L[(_0xae225c(0x189))]([_0x2e5e78[_0xae225c(0x190)], _0x2e5e78[_0xae225c(0x1a6)]], {
        'icon': L[_0xae225c(0x1b0)]({
            'className': _0xae225c(0x18b),
            'html': _0xae225c(0x1c6) + _0x2e5e78[_0xae225c(0x18d)] + _0xae225c(0x1b1),
            'iconAnchor': [0x14, 0x1e]
        })
    })['addTo'](_0x38d6fb), marker[_0xae225c(0x1ab)](_0x8937f7), marker['getPopup']()['on'](_0xae225c(0x1a8),
        markerPopupClose), _0x38d6fb[_0xae225c(0x1cb)](map), marker[_0xae225c(0x1cf)]();
    var _0x4d1ecc = logbook;
    _0x4d1ecc = _0x4d1ecc[_0xae225c(0x1a0)](function(_0x31dda3) {
        var _0x3510b2 = _0xae225c;
        return _0x31dda3[_0x3510b2(0x1cc)][_0x3510b2(0x186)] == _0x2e5e78[_0x3510b2(0x18d)][_0x3510b2(0x1c0)]
        () || _0x31dda3[_0x3510b2(0x1cc)]['arrIcao'] == _0x2e5e78[_0x3510b2(0x18d)][_0x3510b2(0x1c0)]();
    }), dataSet = [];
    for (var _0x36042e in _0x4d1ecc) {
        dataSet[_0xae225c(0x1b2)](_0x4d1ecc[_0x36042e][_0xae225c(0x1cc)]);
        var _0x200938 = _0x4d1ecc[_0x36042e]['airportInfo'][_0xae225c(0x1b3)](_0x7fb8f0 => _0x7fb8f0['icao'] !=
                _0x2e5e78[_0xae225c(0x18d)]),
            _0x2fc636 = {
                'x': _0x2e5e78[_0xae225c(0x1a6)],
                'y': _0x2e5e78[_0xae225c(0x190)]
            };
        if (_0x200938 != null) {
            var _0x502aec = {
                    'x': _0x200938[_0xae225c(0x1a6)],
                    'y': _0x200938['lat']
                },
                _0x3a52ff = new arc[(_0xae225c(0x1b9))](_0x2fc636, _0x502aec),
                _0x2198d4 = _0x3a52ff[_0xae225c(0x1be)](0x64, {
                    'offset': 0xa
                }),
                _0x1ce1e1 = [];
            for (var _0x36042e in _0x2198d4[_0xae225c(0x1b4)][0x0]['coords']) {
                _0x1ce1e1[_0xae225c(0x1b2)]([_0x2198d4['geometries'][0x0][_0xae225c(0x18c)][_0x36042e][0x1], _0x2198d4[
                    _0xae225c(0x1b4)][0x0][_0xae225c(0x18c)][_0x36042e][0x0]]);
            }
            var _0x14750f = L[_0xae225c(0x1a5)](_0x1ce1e1, {
                'color': _0xae225c(0x1a1),
                'weight': 0x2,
                'dashArray': [0x5, 0x5]
            })[_0xae225c(0x1cb)](_0x38d6fb);
            marker = new L[(_0xae225c(0x189))]([_0x200938[_0xae225c(0x190)], _0x200938['lng']], {
                'icon': L[_0xae225c(0x1b0)]({
                    'className': _0xae225c(0x18b),
                    'html': '<div\x20class=\x22label_content\x22><span\x20title=\x22' + _0x200938[
                            _0xae225c(0x1b6)] + '\x22>' + _0x200938[_0xae225c(0x18d)] + _0xae225c(
                        0x1b1),
                    'iconAnchor': [0x14, 0x1e]
                })
            })[_0xae225c(0x1cb)](_0x38d6fb);
        }
        _0x38d6fb[_0xae225c(0x1cb)](map);
    }
    initFlightTable(), _0x38d6fb['addTo'](map), map[_0xae225c(0x195)](_0x38d6fb[_0xae225c(0x185)]());
}

function initFlightTable() {
    var _0x46e7b2 = _0x277a74;
    dataTable != null && dataTable[_0x46e7b2(0x1ae)](), dataTable = $('#flights')[_0x46e7b2(0x1bc)]({
        'initComplete': function() {},
        'data': dataSet,
        'processing': ![],
        'serverSide': ![],
        'language': {
            'sEmptyTable': 'Select\x20a\x20location\x20on\x20the\x20map\x20to\x20list\x20your\x20previous\x20flights.'
        },
        'pageLength': 0x14,
        'columns': [{
            'data': _0x46e7b2(0x1c7)
        }, {
            'data': _0x46e7b2(0x1bd)
        }, {
            'data': _0x46e7b2(0x186)
        }, {
            'data': _0x46e7b2(0x1bb)
        }, {
            'data': 'duration'
        }, {
            'data': _0x46e7b2(0x1cd)
        }, {
            'data': 'approvedStatusDescription'
        }],
        'colReorder': ![],
        'order': [
            [0x0, 'desc']
        ]
    });
}
</script>
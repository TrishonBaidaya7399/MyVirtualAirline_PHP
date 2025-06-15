<?php
use Proxy\Api\Api;

include 'config.php';
include 'lib/functions.php';
session_start();
Api::__constructStatic();
$icao = cleanString(strtoupper($_GET['airport']));

$airport = null;
$res = Api::sendAsync('GET', 'v1/airport/' . $icao, null);
if ($res->getStatusCode() == 200) {
    $airport = json_decode($res->getBody());
} else {
    header('Location: ' . website_base_url);
    die();
}

$flights_coordinates[0] = array($airport->lat, $airport->lng, $airport->icao, $airport->name);

$flights = null;
$res = Api::sendAsync('GET', 'v1/map/acars/' . $icao, null);
if ($res->getStatusCode() == 200) {
    $flights = json_decode($res->getBody(), true);
}
if (!empty($flights)) {
    foreach ($flights as &$flight) {
        $totalDistance = round(get_distance(floatval($flight["startLong"]), floatval($flight["startLat"]), floatval($flight["arrLong"]), floatval($flight["arrLat"])));
        $distanceFlown = round(get_distance(floatval($flight["startLong"]), floatval($flight["startLat"]), floatval($flight["presLong"]), floatval($flight["presLat"])));
        $distanceRemaining = get_distance(floatval($flight["presLong"]), floatval($flight["presLat"]), floatval($flight["arrLong"]), floatval($flight["arrLat"]));
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

?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include 'includes/header.php';?>
<style>
#map {
    height: 400px;
}

.leftLabel {
    font-weight: bold;
}

.left {
    font-weight: 400;
}

.metar-table {
    font-weight: 200
}

.rawData {
    font-weight: bold;
    color: #0E9AC9;
}
</style>
<link rel="stylesheet" href="<?php echo website_base_url; ?>assets/plugins/leaflet/leaflet.css" />
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/leaflet.js"></script>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span class="text-right" style="clear:left;float:right;"><i><a
                                    href="<?php echo website_base_url; ?>flight_search.php">Search flights <i
                                        class="fa fa-arrow-right" aria-hidden="true"></i></a></i>
                        </span>
                        <h3 class="panel-title">
                            <?php echo $airport->name; ?>
                            Airport<?php echo !empty($airport->city) ? ', ' . $airport->city : ''; ?>
                            <i>(<?php echo $airport->icao; ?>)</i>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div id="map-outer" class="col-md-12">
                                <div id="map"></div>
                            </div><!-- /map-outer -->
                        </div> <!-- /row -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Airport Weather & NOTAM's</h3>
                    </div>
                    <div class="panel-body" style="max-height:400px;overflow-y:scroll;">
                        <div class=" row">
                            <div class="col-md-12 metar-table">
                                <?php
get_metar($airport->icao);
?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Live Departures & Arrivals</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?php
if (empty($flights)) {
    ?>
                                <p>There are currently no arrivals or departures for this airport.</p>

                                <?php
} else {
    ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Pilot</th>
                                            <th>Flight Number</th>
                                            <th>Departure</th>
                                            <th>Arrival</th>
                                            <th>Aircraft</th>
                                            <th>Stage</th>
                                            <th>DTG</th>
                                            <th>ETA</th>
                                            <th style="min-width:100px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($flights as &$flight) {?>
                                        <tr>
                                            <td><a href="<?php echo website_base_url; ?>profile.php?id=<?php echo $flight['pilotId']; ?>"
                                                    target="_blank"><i class="fa fa-external-link"></i>
                                                    <?php echo explode(" ", $flight['name'])[0]; ?>
                                                    <span class="small">(ID:
                                                        <?php echo $flight['callsign']; ?>)</span></a>
                                            </td>
                                            <td> <a href="live_flights.php" class="marker"
                                                    data-id="<?php echo $flight['actmpId']; ?>"><i
                                                        class="fa fa-external-link"></i>
                                                    <?php echo $flight['flightNumber']; ?></a>
                                            </td>
                                            <td><a href="<?php echo website_base_url; ?>airport_info.php?airport=<?php echo $flight['depIcao']; ?>"
                                                    target="_blank"><i class="fa fa-external-link"></i>
                                                    <?php echo $flight['depIcao']; ?></a>
                                            </td>
                                            <td><a href="<?php echo website_base_url; ?>airport_info.php?airport=<?php echo $flight['arrIcao']; ?>"
                                                    target="_blank"><i class="fa fa-external-link"></i>
                                                    <?php echo $flight['arrIcao']; ?></a>
                                            </td>
                                            <td><span
                                                    title="<?php echo $flight['aircraftTypeId']; ?>"><?php echo limit($flight['aircraftTypeId'], 20); ?></span>
                                            </td>
                                            <td><strong><?php echo $flight['statStage']; ?></strong>
                                            </td>
                                            <td><?php echo $flight['dtg']; ?>nm
                                            </td>
                                            <td><?php echo $flight['eta']; ?>
                                            </td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: <?php echo (round($flight['perc_complete'], 0)); ?>%"
                                                        aria-valuenow="<?php echo (round($flight['perc_complete'], 0)); ?>"
                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Departures History <i class="small">(last 12 months)</i></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12" style="max-height:400px;overflow-y:scroll;">
                                <table class="table table-hover">
                                    <?php if (empty($airport->departuresHistory)) {?>
                                    No departure history to display.
                                    <hr />
                                    <?php } else {?>
                                    <thead>
                                        <tr>
                                            <th><strong>Flight No.</strong></th>
                                            <th><strong>Dest.</strong></th>
                                            <th><strong>Aircraft</strong></th>
                                            <th><strong>Date</strong></th>
                                        </tr>
                                    </thead>
                                    <?php
foreach ($airport->departuresHistory as $dep) {
    ?>
                                    <tr>
                                        <td><strong><a
                                                    href="<?php echo website_base_url;?>pirep_info.php?id=<?php echo $dep->id; ?>"
                                                    class="js_showloader"><i class="fa fa-external-link"></i>
                                                    <?php echo $dep->flightNumber; ?></a></strong>
                                        </td>
                                        <td><a href="<?php echo website_base_url;?>airport_info.php?airport=<?php echo $dep->arrivalIcao; ?>"
                                                class="js_showloader"><i class="fa fa-external-link"></i>
                                                <?php echo $dep->arrivalIcao; ?></a>
                                        </td>
                                        <td><span
                                                title="<?php echo $dep->aircraft; ?>"><?php echo limit($dep->aircraft,10); ?></span>
                                        </td>
                                        <td><?php echo $dep->date . ' ' . $dep->deptime; ?></td>
                                    </tr>
                                    <?php } ?>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Arrivals History <i class="small">(last 12 months)</i></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12" style="max-height:400px;overflow-y:scroll;">
                                <table class="table table-hover">
                                    <?php if (empty($airport->arrivalsHistory)) {?>
                                    No arrival history to display.
                                    <hr />
                                    <?php } else {?>
                                    <thead>
                                        <tr>
                                            <th><strong>Flight No.</strong></th>
                                            <th><strong>Origin</strong></th>
                                            <th><strong>Aircraft</strong></th>
                                            <th><strong>Date</strong></th>
                                        </tr>
                                    </thead>
                                    <?php
foreach ($airport->arrivalsHistory as $arr) {
    ?>
                                    <tr>
                                        <td><strong><a
                                                    href="<?php echo website_base_url;?>pirep_info.php?id=<?php echo $arr->id; ?>"
                                                    class="js_showloader"><i class="fa fa-external-link"></i>
                                                    <?php echo $arr->flightNumber; ?></a></strong>
                                        </td>
                                        <td><a href="<?php echo website_base_url;?>airport_info.php?airport=<?php echo $arr->departureIcao; ?>"
                                                class="js_showloader"><i class="fa fa-external-link"></i>
                                                <?php echo $arr->departureIcao; ?></a>
                                        </td>
                                        <td><span
                                                title="<?php echo $arr->aircraft; ?>"><?php echo limit($arr->aircraft,10); ?></span>
                                        </td>
                                        <td><?php echo $arr->date . ' ' . $arr->arrtime; ?></td>
                                    </tr>
                                    <?php } ?>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Runway Information <i class="small">(Airport elevation:
                                <?php echo number_format($airport->altitude); ?>ft)</i></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-hover">
                                    <?php if (empty($airport->runways)) {?>
                                    No runways to display.
                                    <hr />
                                    <?php } else {?>
                                    <?php
        foreach ($airport->runways as $runway) {
    ?>
                                    <tr>
                                        <td><strong>Runway</strong></td>
                                        <td><?php echo $runway->name; ?>
                                        </td>
                                        <td><strong>Length</strong></td>
                                        <td><?php echo number_format($runway->length) . 'ft'; ?>
                                        </td>
                                        <td><strong>Width</strong></td>
                                        <td><?php echo number_format($runway->width) . 'ft'; ?>
                                        </td>
                                        <td><strong>Elevation</strong></td>
                                        <td><?php echo !empty($runway->elevation) ? number_format($runway->elevation) . 'ft' : "N/A"; ?>
                                        </td>
                                        <td><strong>Surface</strong></td>
                                        <td><?php echo $runway->surface; ?>
                                        </td>
                                        <td><strong>Heading</strong></td>
                                        <td><?php echo number_format($runway->heading, 0); ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Airport Frequencies</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12" style="max-height:400px;overflow-y:scroll;">
                                <table class="table table-hover">
                                    <?php if (empty($airport->airportFrequencies)) {?>
                                    No frequencies to display.
                                    <hr />
                                    <?php } else {?>
                                    <?php
foreach ($airport->airportFrequencies as $com) {
    ?>
                                    <tr>
                                        <td><strong>Type</strong></td>
                                        <td><?php echo $com->comType; ?>
                                        </td>
                                        <td><strong>Frequency</strong></td>
                                        <td><?php echo number_format($com->comFrequency,3) . ' Mhz'; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Airport Navaids</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12" style="max-height:400px;overflow-y:scroll;">
                                <table class="table table-hover">
                                    <?php if (empty($airport->navaids)) {?>
                                    No navaids to display.
                                    <hr />
                                    <?php } else {?>
                                    <?php
foreach ($airport->navaids as $nav) {
    ?>
                                    <tr>
                                        <td><strong>Type</strong></td>
                                        <td><?php echo $nav->type; ?>
                                        </td>
                                        <td><strong>Ident</strong></td>
                                        <td><?php echo $nav->ident ?>
                                        </td>
                                        <td><strong>Frequency</strong></td>
                                        <td><?php echo $nav->frequency . ' Khz'; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
$(document).ready(function() {
    var locations = <?php echo json_encode($flights_coordinates); ?>;

    var map = L.map('map', {
        'center': [locations[0][0],
            locations[0][1]
        ],
        'zoom': 14,
        'worldCopyJump': true,
        'attributionControl': false
    });
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: ''
    }).addTo(map);
    marker = new L.marker([
        locations[0][0],
        locations[0][1]
    ], {
        icon: L.divIcon({
            className: 'iconicon',
            html: '<div class="label_content"><span>' + locations[0][2] + '</span></div>',
            iconAnchor: [20, 30]
        })
    }).addTo(map);
});
</script>
<?php include 'includes/footer.php';
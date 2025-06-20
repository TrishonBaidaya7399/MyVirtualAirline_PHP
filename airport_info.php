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
    /* Parallax Background */
    .parallax {
        position: relative;
        padding: 60px 0;
        padding-top: 80px !important;
        min-height: calc(100vh - 128px);
        background-image: url('./assets/images/backgrounds/world_map2.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }

    .parallax::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1;
    }

    .parallax .container {
        position: relative;
        z-index: 2;
    }

    /* Mobile: Disable fixed background attachment */
    @media (max-width: 768px) {
        .parallax {
            background-attachment: scroll;
            padding: 30px 0;
            min-height: auto;
        }
    }

    /* Glass Backdrop for Cards */
    .glass-card {
        background: rgba(255, 255, 255, 0.20);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 30px;
    }

    .glass-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .glass-card .panel-heading {
        border-radius: 15px 15px 0 0;
        background: rgba(255, 255, 255, 0.3);
        padding: 20px;
        color: #fff !important;
    }

    .glass-card .panel-heading .panel-title {
        color: #fff;
        font-size: 2rem;
        font-weight: 300;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    .glass-card .panel-body {
        color: #fff;
        padding: 20px;
    }

    .glass-card .panel-body a {
        color: rgba(255, 215, 0, 1);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .glass-card .panel-body a:hover {
        color: #fff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
    }

    .glass-card .panel-body .table {
        color: #fff;
    }

    .glass-card .panel-body .table th,
    .glass-card .panel-body .table td {
        padding: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .glass-card .panel-body .table th {
        background: rgba(255, 255, 255, 0.1);
        font-weight: 600;
    }

    .glass-card .panel-body .progress {
        height: 20px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    .glass-card .panel-body .progress-bar {
        background: rgba(255, 215, 0, 0.8);
        border-radius: 10px;
    }

    /* Map Container */
    .map-container-glass {
        background: rgba(255, 255, 255, 0.20);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .map-container-glass:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    #map {
        height: 400px;
        border-radius: 15px;
        width: 100%;
    }

    /* Alerts and Buttons */
    .alert {
        background: rgba(255, 255, 255, 0.20);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        color: #fff;
        text-align: center;
        padding: 15px;
        margin-bottom: 20px;
    }

    .alert a {
        color: rgba(255, 215, 0, 1);
        text-decoration: none;
    }

    .alert a:hover {
        color: #fff;
    }

    .btn {
        background: linear-gradient(45deg, rgba(255, 215, 0, 0.8), rgba(255, 193, 7, 0.9));
        border: 1px solid rgba(255, 215, 0, 0.5);
        color: #333;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn:hover {
        background: linear-gradient(45deg, rgba(255, 215, 0, 1), rgba(255, 193, 7, 1));
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
        color: #222;
    }

    /* Custom Styles */
    .leftLabel {
        font-weight: bold;
    }

    .left {
        font-weight: 400;
    }

    .metar-table {
        font-weight: 200;
    }

    .rawData {
        font-weight: bold;
        color: #0E9AC9;
    }

    /* Responsive Adjustments */
    @media (max-width: 1200px) {
        .parallax {
            padding: 50px 0;
        }

        .glass-card .panel-heading .panel-title {
            font-size: 1.8rem;
        }

        #map {
            height: 350px;
        }
    }

    @media (max-width: 992px) {
        .parallax {
            padding: 40px 0;
            background-attachment: scroll;
        }

        .offset-header {
            padding-top: 80px;
        }

        .glass-card {
            margin: 0 15px 30px 15px;
            border-radius: 12px;
        }

        .glass-card .panel-body {
            padding: 15px;
        }

        #map {
            height: 300px;
            border-radius: 12px;
        }

        .glass-card .panel-body .table th,
        .glass-card .panel-body .table td {
            padding: 8px;
        }
    }

    @media (max-width: 768px) {
        .parallax {
            padding: 30px 0;
        }

        .offset-header {
            padding-top: 60px;
        }

        .glass-card {
            margin: 0 10px 25px 10px;
            border-radius: 10px;
        }

        .glass-card .panel-heading .panel-title {
            font-size: 1.6rem;
        }

        #map {
            height: 250px;
            border-radius: 10px;
        }

        .glass-card .panel-body .table th,
        .glass-card .panel-body .table td {
            padding: 6px;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        .parallax {
            padding: 25px 0;
        }

        .offset-header {
            padding-top: 50px;
        }

        .glass-card {
            margin: 0 5px 20px 5px;
            border-radius: 8px;
        }

        .glass-card .panel-heading .panel-title {
            font-size: 1.4rem;
        }

        #map {
            height: 200px;
            border-radius: 8px;
        }

        .glass-card .panel-body .table th,
        .glass-card .panel-body .table td {
            padding: 5px;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 480px) {
        .parallax {
            padding: 20px 0;
        }

        .offset-header {
            padding-top: 40px;
        }

        .glass-card .panel-heading .panel-title {
            font-size: 1.2rem;
        }

        #map {
            height: 150px;
        }

        .glass-card .panel-body .table th,
        .glass-card .panel-body .table td {
            padding: 4px;
            font-size: 0.7rem;
        }
    }

    @media print {
        .parallax {
            background: white;
            padding: 20px 0;
        }

        .parallax::before {
            display: none;
        }

        .glass-card {
            background: white;
            border: 1px solid #ccc;
            box-shadow: none;
        }

        .glass-card .panel-heading .panel-title,
        .glass-card .panel-body,
        .alert {
            color: black;
            text-shadow: none;
        }
    }
</style>
<link rel="stylesheet" href="<?php echo website_base_url; ?>assets/plugins/leaflet/leaflet.css" />
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/leaflet.js"></script>
<section id="content" class="section parallax offset-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <div class="d-flex flex-row align-items-center gap-1">
                            <span class="text-right"><i><a style="color: #fff" href="<?php echo website_base_url; ?>flight_search.php">Search flights <i class="fa fa-arrow-right" sty;e="color: #fff" aria-hidden="true"></i></a></i></span>
                            <h3 class="panel-title">
                        </div>
                            <?php echo $airport->name; ?>
                            Airport<?php echo !empty($airport->city) ? ', ' . $airport->city : ''; ?>
                            <i>(<?php echo $airport->icao; ?>)</i>
                        </h3>
                    </div>
                    <div class="panel-body" style="padding: 0px">
                        <div class="row">
                            <div class="col-12 map-container-glass">
                                <div id="map"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Airport Weather & NOTAM's</h3>
                    </div>
                    <div class="panel-body" style="max-height:400px;overflow-y:scroll;">
                        <div class="row">
                            <div class="col-12 metar-table">
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
            <div class="col-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Live Departures & Arrivals</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-12">
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
                                            <td><a href="<?php echo website_base_url; ?>profile.php?id=<?php echo $flight['pilotId']; ?>" target="_blank"><i class="fa fa-external-link" style="color: rgba(255, 215, 0, 1)"></i>
                                                    <?php echo explode(" ", $flight['name'])[0]; ?>
                                                    <span class="small">(ID: <?php echo $flight['callsign']; ?>)</span></a>
                                            </td>
                                            <td><a href="live_flights.php" class="marker" data-id="<?php echo $flight['actmpId']; ?>"><i class="fa fa-external-link" style="color: rgba(255, 215, 0, 1)"></i>
                                                    <?php echo $flight['flightNumber']; ?></a>
                                            </td>
                                            <td><a href="<?php echo website_base_url; ?>airport_info.php?airport=<?php echo $flight['depIcao']; ?>" target="_blank"><i class="fa fa-external-link" style="color: rgba(255, 215, 0, 1)"></i>
                                                    <?php echo $flight['depIcao']; ?></a>
                                            </td>
                                            <td><a href="<?php echo website_base_url; ?>airport_info.php?airport=<?php echo $flight['arrIcao']; ?>" target="_blank"><i class="fa fa-external-link" style="color: rgba(255, 215, 0, 1)"></i>
                                                    <?php echo $flight['arrIcao']; ?></a>
                                            </td>
                                            <td><span title="<?php echo $flight['aircraftTypeId']; ?>"><?php echo limit($flight['aircraftTypeId'], 20); ?></span>
                                            </td>
                                            <td><strong><?php echo $flight['statStage']; ?></strong>
                                            </td>
                                            <td><?php echo $flight['dtg']; ?>nm
                                            </td>
                                            <td><?php echo $flight['eta']; ?>
                                            </td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" style="width: <?php echo (round($flight['perc_complete'], 0)); ?>%" aria-valuenow="<?php echo (round($flight['perc_complete'], 0)); ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
            <div class="col-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Departures History <i class="small">(last 12 months)</i></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-12" style="max-height:400px;overflow-y:scroll;">
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
                                        <td><strong><a href="<?php echo website_base_url;?>pirep_info.php?id=<?php echo $dep->id; ?>" class="js_showloader"><i class="fa fa-external-link" style="color: rgba(255, 215, 0, 1)"></i>
                                                <?php echo $dep->flightNumber; ?></a></strong>
                                        </td>
                                        <td><a href="<?php echo website_base_url;?>airport_info.php?airport=<?php echo $dep->arrivalIcao; ?>" class="js_showloader"><i class="fa fa-external-link" style="color: rgba(255, 215, 0, 1)"></i>
                                                <?php echo $dep->arrivalIcao; ?></a>
                                        </td>
                                        <td><span title="<?php echo $dep->aircraft; ?>"><?php echo limit($dep->aircraft,10); ?></span>
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
        </div>
        <div class="row">
            <div class="col-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Arrivals History <i class="small">(last 12 months)</i></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-12" style="max-height:400px;overflow-y:scroll;">
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
                                        <td><strong><a href="<?php echo website_base_url;?>pirep_info.php?id=<?php echo $arr->id; ?>" class="js_showloader"><i class="fa fa-external-link" style="color: rgba(255, 215, 0, 1)"></i>
                                                <?php echo $arr->flightNumber; ?></a></strong>
                                        </td>
                                        <td><a href="<?php echo website_base_url;?>airport_info.php?airport=<?php echo $arr->departureIcao; ?>" class="js_showloader"><i class="fa fa-external-link" style="color: rgba(255, 215, 0, 1)"></i>
                                                <?php echo $arr->departureIcao; ?></a>
                                        </td>
                                        <td><span title="<?php echo $arr->aircraft; ?>"><?php echo limit($arr->aircraft,10); ?></span>
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
            <div class="col-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Runway Information <i class="small">(Airport elevation: <?php echo number_format($airport->altitude); ?>ft)</i></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-12">
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
                                        <td><?php echo $runway->name; ?></td>
                                        <td><strong>Length</strong></td>
                                        <td><?php echo number_format($runway->length) . 'ft'; ?></td>
                                        <td><strong>Width</strong></td>
                                        <td><?php echo number_format($runway->width) . 'ft'; ?></td>
                                        <td><strong>Elevation</strong></td>
                                        <td><?php echo !empty($runway->elevation) ? number_format($runway->elevation) . 'ft' : "N/A"; ?></td>
                                        <td><strong>Surface</strong></td>
                                        <td><?php echo $runway->surface; ?></td>
                                        <td><strong>Heading</strong></td>
                                        <td><?php echo number_format($runway->heading, 0); ?></td>
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
            <div class="col-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Airport Frequencies</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-12" style="max-height:400px;overflow-y:scroll;">
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
                                        <td><?php echo $com->comType; ?></td>
                                        <td><strong>Frequency</strong></td>
                                        <td><?php echo number_format($com->comFrequency, 3) . ' Mhz'; ?></td>
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
            <div class="col-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Airport Navaids</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-12" style="max-height:400px;overflow-y:scroll;">
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
                                        <td><?php echo $nav->type; ?></td>
                                        <td><strong>Ident</strong></td>
                                        <td><?php echo $nav->ident; ?></td>
                                        <td><strong>Frequency</strong></td>
                                        <td><?php echo $nav->frequency . ' Khz'; ?></td>
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
        'center': [locations[0][0], locations[0][1]],
        'zoom': 14,
        'worldCopyJump': true,
        'attributionControl': false
    });
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: ''
    }).addTo(map);
    marker = new L.marker([locations[0][0], locations[0][1]], {
        icon: L.divIcon({
            className: 'iconicon',
            html: '<div class="label_content"><span>' + locations[0][2] + '</span></div>',
            iconAnchor: [20, 30]
        })
    }).addTo(map);
});
</script>
<?php include 'includes/footer.php';?>
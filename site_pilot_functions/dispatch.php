<?php

use Proxy\Api\Api;

include '../lib/functions.php';
include '../config.php';

session_start();

validateSession();
Api::__constructStatic();
$pilotId = $_SESSION['pilotid'];
$bookingCancelled = false;
$hasBooking = false;
$path_data = null;
$sbDispatched = false;
$sbOfpId = null;

$res = Api::sendAsync('GET', 'v1/bid/' . $pilotId, null);
$status = "";
if ($res->getStatusCode() == 200) {
    $bid = json_decode($res->getBody());
    $hasBooking = true;
    $bidexpires = new DateTime($bid->dateBooked);
    $bidexpires->modify('+ ' . (int) $_SESSION['booking_expire_hours'] . ' hour');
    $depicao = $bid->departureIcao;
    $arricao = $bid->arrivalIcao;
    $sbDispatched = $bid->sbDispatched;
    $sbOfpId = $bid->sbOfpId;
    $mapData = $bid->airportInfo;
} else {
    $hasBooking = false;
}
if (isset($_POST['btncancel'])) {
    $res = Api::sendSync('DELETE', 'v1/bid/' . $pilotId, null);
    if ($res->getStatusCode() == 200) {
        $bookingcancelled = true;
        $hasBooking = false;
    }
}

if ($sbDispatched && !empty($sbOfpId)) {
    header('Location: simbrief/output.php?ofp_id=' . $sbOfpId);
}
if (!empty($bid)) {
    $depAirport = null;
    $arrAirport = null;
    $res = Api::sendAsync('GET', 'v1/airport/' . $bid->departureIcao, null);
    if ($res->getStatusCode() == 200) {
        $depAirport = json_decode($res->getBody());
    }
    $res = Api::sendAsync('GET', 'v1/airport/' . $bid->arrivalIcao, null);
    if ($res->getStatusCode() == 200) {
        $arrAirport = json_decode($res->getBody());
    }

    $flights_coordinates[0] = array($depAirport->lat, $depAirport->lng, $depAirport->icao, $depAirport->name);
    $flights_coordinates[1] = array($arrAirport->lat, $arrAirport->lng, $arrAirport->icao, $arrAirport->name);
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.hero-section {
    position: relative;
    min-height: calc(100vh - 128px);
    padding: 80px 0;
    background-image: url('../assets/images/backgrounds/world_map2.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.3); 
    z-index: 1;
}

.hero-section .container {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: center;
    align-items: center;
}

.offset-header {
    padding-top: 100px;
}

/* -------------------------------- Global title ------------------------------------ */
.global-heading {
    width: 100%;
    margin-bottom: 20px;
    text-align: center;
}
.global-heading .global-title {
    font-size: 40px;
    font-weight: 800;
    color: #fff;
    margin-top: 0px !important;
    text-transform: lowercase;
}
@media (max-width: 612px) {
    .global-heading .global-title {
        font-size: 30px;
        font-weight: 700;
    }
}
/* -------------------------------- X ------------------------------------ */

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
    min-width: 500px;
    max-width: 600px;
    color: #fff; /* All card text set to white */
}

.glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.glass-card .panel-heading {
    background: rgba(255, 255, 255, 0.3);
    border-bottom: 2px solid rgba(255, 215, 0, 1);
    padding: 15px;
}

.glass-card .panel-heading .panel-title {
    color: #fff;
    font-size: 1.5rem;
    margin: 0;
}

.glass-card .panel-body {
    padding: 20px;
    color: #fff; /* Ensure panel body text is white */
}

#map {
    height: 500px;
    border-radius: 15px;
    overflow: hidden;
}

.leftLabel {
    font-weight: bold;
    color: #fff;
}

.left {
    font-weight: 400;
    color: #fff;
}

.metar-table {
    font-weight: 200;
    color: #fff;
}

.rawData {
    font-weight: bold;
    color: #0E9AC9;
}

.table-hover tbody tr {
    color: #fff; /* Table rows text set to white */
}

.table-hover tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.3) !important;
}

.alert-success {
    background-color: rgba(40, 167, 69, 0.2);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    margin: 0 auto;
    display: block;
    max-width: 600px;
}

.btn-success {
    background-color: rgba(40, 167, 69, 0.8);
    border: none;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background-color: rgba(40, 167, 69, 1);
    transform: translateY(-2px);
}

.btn-default {
    background-color: rgba(255, 215, 0, 0.8);
    border: none;
    color: #fff; /* Button text set to white */
    padding: 10px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-default:hover {
    background-color: rgba(255, 215, 0, 1);
    transform: translateY(-2px);
}

.jumbotron {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 30px;
    color: #fff;
    text-align: center;
}

@media (max-width: 1200px) {
    .hero-section {
        padding: 80px 0;
    }
    .glass-card .panel-body {
        padding: 15px;
    }
    #map {
        height: 400px;
    }
}

@media (max-width: 992px) {
    .hero-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 80px;
    }
    .glass-card {
        margin: 0 15px 30px 15px;
        border-radius: 12px;
        min-width: 400px; /* Adjusted for smaller screens */
    }
    #map {
        height: 350px;
    }
}

@media (max-width: 768px) {
    .hero-section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 60px;
    }
    .glass-card {
        margin: 0 10px 25px 10px;
        border-radius: 10px;
        min-width: 350px; /* Further adjusted for smaller screens */
    }
    #map {
        height: 300px;
    }
    .glass-card .panel-body {
        padding: 10px;
    }
}

@media (max-width: 576px) {
    .hero-section {
        padding: 80px 0;
    }
    .offset-header {
        padding-top: 50px;
    }
    .glass-card {
        margin: 0 5px 20px 5px;
        border-radius: 8px;
        min-width: 300px; /* Minimum width for very small screens */
    }
    #map {
        height: 250px;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 80px 0;
    }
    .offset-header {
        padding-top: 40px;
    }
    .glass-card {
        min-width: 250px; /* Minimum width for extra small screens */
    }
    #map {
        height: 200px;
    }
}
/* -------------------------------- Global title ------------------------------------ */
.global-header{
    width: 100%;
    margin-bottom: 20px;
}
.global-heading .global-title{
    font-size: 40px;
    font-weight: 800;
    color: #fff;
    margin-top: 0px !important;
    text-transform: lowercase;
    text-align: center;
}
@media (max-width: 612px){
    .global-heading .global-title{
        font-size: 30px;
        font-weight: 700;
    }
}
/* -------------------------------- X ------------------------------------ */
</style>
<?php include '../includes/header.php'; ?>
<section id="content" class="section hero-section offset-header">
    <?php if ($bookingCancelled) { ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success">You have successfully cancelled this flight booking.</div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($hasBooking) { ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php if ($bid->status == 0) { ?>
                      
                        <div class="glass-card">
                            <div class="panel-body">
                                <?php if (simbrief_enabled) { ?>
                                    <p><i>You can dispatch this flight via SimBrief instead.</i></p>
                                    <p><a href="<?php echo website_base_url; ?>site_pilot_functions/simbrief/dispatch.php" class="btn btn-success">Dispatch via SimBrief</a></p>
                                    <?php } ?>
                                    <div class="text-right">
                                        <form method="post" class="form">
                                            <button name="btncancel" type="submit" id="btncancel" class="btn btn-default"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Cancel Flight</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php } else { ?>
                                <div class="global-header">
                                    <div class="global-title">Dispatch</div>
                                </div>
                        <div class="glass-card">
                            <div class="panel-body">
                                <div class="text-center">This flight is in progress.</div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <hr style="border: 1px solid rgba(255, 255, 255, 0.2);" />
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    
                    <div class="glass-card">
                 
                        <div class="panel-body row-space">
                            <div class="row">
                                <div class="col-md-2 col-xs-2 leftLabel">Flight No</div>
                                <div class="col-md-2 col-xs-2 leftLabel">Aircraft</div>
                                <div class="col-md-2 col-xs-2 leftLabel">Depart</div>
                                <div class="col-md-2 col-xs-2 leftLabel">Arrive</div>
                                <div class="col-md-2 col-xs-2 leftLabel">Depart Time</div>
                                <div class="col-md-2 col-xs-2 leftLabel">Arrive Time</div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-xs-2 left"><?php echo empty($bid->flightNumber) ? "N/A" : $bid->flightNumber; ?></div>
                                <div class="col-md-2 col-xs-2 left"><?php echo empty($bid->aircraft) ? "N/A" : $bid->aircraft; ?> (<?php echo empty($bid->aircraftReg) ? "N/A" : $bid->aircraftReg; ?>)</div>
                                <div class="col-md-2 col-xs-2 left"><?php echo empty($bid->departureIcao) ? "N/A" : $bid->departureIcao; ?></div>
                                <div class="col-md-2 col-xs-2 left"><?php echo empty($bid->arrivalIcao) ? "N/A" : $bid->arrivalIcao; ?></div>
                                <div class="col-md-2 col-xs-2 left"><?php echo empty($bid->departureTime) ? "N/A" : $bid->departureTime; ?></div>
                                <div class="col-md-2 col-xs-2 left"><?php echo empty($bid->arrivalTime) ? "N/A" : $bid->arrivalTime; ?></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-xs-2 leftLabel">Air Time</div>
                                <div class="col-md-2 col-xs-2 leftLabel">PAX</div>
                                <div class="col-md-2 col-xs-2 leftLabel">Cargo</div>
                                <div class="col-md-2 col-xs-2 leftLabel">Crew</div>
                                <div class="col-md-2 col-xs-2 leftLabel">Distance approx.</div>
                                <div class="col-md-2 col-xs-2 leftLabel">PIC</div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-xs-2 left"><?php echo empty($bid->duration) ? "N/A" : $bid->duration; ?></div>
                                <div class="col-md-2 col-xs-2 left"><?php echo empty($bid->totalPax) ? "N/A" : $bid->totalPax; ?></div>
                                <div class="col-md-2 col-xs-2 left"><?php echo getCargoDisplayValue($bid->cargo); ?></div>
                                <div class="col-md-2 col-xs-2 left"><?php echo empty($bid->fleetAircraft->totalCrew) ? "N/A" : $bid->fleetAircraft->totalCrew; ?></div>
                                <div class="col-md-2 col-xs-2 left"><?php echo (!empty($bid->arrivalIcao) && !empty($bid->departureIcao)) ? number_format(get_distance($bid->airportInfo[0]->lng, $bid->airportInfo[0]->lat, $bid->airportInfo[1]->lng, $bid->airportInfo[1]->lat), 1) . 'nm' : "N/A"; ?></div>
                                <div class="col-md-2 col-xs-2 left"><?php echo $_SESSION['name']; ?></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-xs-2 leftLabel">MZFW</div>
                                <div class="col-md-2 col-xs-2 leftLabel">MTOW</div>
                                <div class="col-md-2 col-xs-2 leftLabel">MLW</div>
                                <div class="col-md-2 col-xs-2 leftLabel"> </div>
                                <div class="col-md-2 col-xs-2 leftLabel"> </div>
                                <div class="col-md-2 col-xs-2 leftLabel"> </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-xs-2 left"><?php echo empty($bid->fleetAircraft->mzfw) ? "N/A" : $bid->fleetAircraft->mzfw; ?></div>
                                <div class="col-md-2 col-xs-2 left"><?php echo empty($bid->fleetAircraft->mtow) ? "N/A" : $bid->fleetAircraft->mtow; ?></div>
                                <div class="col-md-2 col-xs-2 left"><?php echo empty($bid->fleetAircraft->mlw) ? "N/A" : $bid->fleetAircraft->mlw; ?></div>
                                <div class="col-md-2 col-xs-2 left"> </div>
                                <div class="col-md-2 col-xs-2 left"> </div>
                                <div class="col-md-2 col-xs-2 left"> </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12 leftLabel">Routing</div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12 left"><?php echo empty($bid->route) ? "N/A" : $bid->route; ?></div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12 leftLabel">Remarks</div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12 comments"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="glass-card">
                        <div class="panel-heading">
                            <h3 class="panel-title">Current Winds</h3>
                        </div>
                        <div class="panel-body">
                            <iframe width="100%" height="450" src="https://embed.windy.com/embed2.html?lat=52.987&lon=-1.068&zoom=1&level=surface&overlay=wind&menu=&message=true&marker=&calendar=&pressure=true&type=map&location=coordinates&detail=&detailLat=52.987&detailLon=-1.068&metricWind=kt&metricTemp=%C2%B0C&radarRange=-1" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!empty($bid->departureIcao)) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="glass-card">
                            <div class="panel-heading">
                                <h3 class="panel-title">Departure Airport Briefing (<?php echo $bid->departureIcao; ?> - <?php echo $depAirport->name; ?>)</h3>
                            </div>
                            <div class="panel-body" style="max-height:500px; overflow-y:scroll;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 metar-table">
                                                <?php get_metar($bid->departureIcao); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr style="border: 1px solid rgba(255, 255, 255, 0.2);" />
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="glass-card">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Runway Information</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-hover">
                                                            <?php if (empty($depAirport->runways)) { ?>
                                                                <tr><td colspan="12">No runways to display.</td></tr>
                                                                <hr style="border: 1px solid rgba(255, 255, 255, 0.2);" />
                                                            <?php } else { ?>
                                                                <?php foreach ($depAirport->runways as $runway) { ?>
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
                                    <div class="col-md-6">
                                        <div class="glass-card">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Airport Frequencies</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-hover">
                                                            <?php if (empty($depAirport->airportFrequencies)) { ?>
                                                                <tr><td colspan="4">No frequencies to display.</td></tr>
                                                                <hr style="border: 1px solid rgba(255, 255, 255, 0.2);" />
                                                            <?php } else { ?>
                                                                <?php foreach ($depAirport->airportFrequencies as $com) { ?>
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
                                    <div class="col-md-6">
                                        <div class="glass-card">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Airport Navaids</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-hover">
                                                            <?php if (empty($depAirport->navaids)) { ?>
                                                                <tr><td colspan="5">No navaids to display.</td></tr>
                                                                <hr style="border: 1px solid rgba(255, 255, 255, 0.2);" />
                                                            <?php } else { ?>
                                                                <?php foreach ($depAirport->navaids as $nav) { ?>
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
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (!empty($bid->arrivalIcao)) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="glass-card">
                            <div class="panel-heading">
                                <h3 class="panel-title">Arrival Airport Briefing (<?php echo $bid->arrivalIcao; ?> - <?php echo $arrAirport->name; ?>)</h3>
                            </div>
                            <div class="panel-body" style="max-height:500px; overflow-y:scroll;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 metar-table">
                                                <?php get_metar($bid->arrivalIcao); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr style="border: 1px solid rgba(255, 255, 255, 0.2);" />
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="glass-card">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Runway Information</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-hover">
                                                            <?php if (empty($arrAirport->runways)) { ?>
                                                                <tr><td colspan="12">No runways to display.</td></tr>
                                                                <hr style="border: 1px solid rgba(255, 255, 255, 0.2);" />
                                                            <?php } else { ?>
                                                                <?php foreach ($arrAirport->runways as $runway) { ?>
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
                                    <div class="col-md-6">
                                        <div class="glass-card">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Airport Frequencies</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-hover">
                                                            <?php if (empty($arrAirport->airportFrequencies)) { ?>
                                                                <tr><td colspan="4">No frequencies to display.</td></tr>
                                                                <hr style="border: 1px solid rgba(255, 255, 255, 0.2);" />
                                                            <?php } else { ?>
                                                                <?php foreach ($arrAirport->airportFrequencies as $com) { ?>
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
                                    <div class="col-md-6">
                                        <div class="glass-card">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Airport Navaids</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-hover">
                                                            <?php if (empty($arrAirport->navaids)) { ?>
                                                                <tr><td colspan="5">No navaids to display.</td></tr>
                                                                <hr style="border: 1px solid rgba(255, 255, 255, 0.2);" />
                                                            <?php } else { ?>
                                                                <?php foreach ($arrAirport->navaids as $nav) { ?>
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
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="glass-card">
                        <?php include_once '../site_widgets/map_flight.php'; ?>
                        <div id="map" style="height:500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="global-heading">
                            <div class="global-title">Dispatch</div>
                        </div>
                    <div class="glass-card">
                        <div class="panel-body">
                            <div class="text-center">
                                <p>You don't have any active flight bookings.</p>
                                <p><a href="<?php echo website_base_url; ?>flight_search.php" class="btn btn-default"><i class="fa fa-search" aria-hidden="true"></i> Find Flights</a></p>
                                <?php if (simbrief_allow_charter) { ?>
                                    <hr style="border: 1px solid rgba(255, 255, 255, 0.2);" />
                                    <p>Or, you can dispatch a charter flight with SimBrief.</p>
                                    <p><a href="<?php echo website_base_url; ?>site_pilot_functions/simbrief/dispatch.php" class="btn btn-default">Dispatch Charter Flight</a></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</section>
<script src="https://cdn.jsdelivr.net/npm/editorjs-parser@1/build/Parser.browser.min.js"></script>
<script type="text/javascript">
    var commentsJson = '<?php echo addslashes(preg_replace("/\r|\n/", "", $bid->comments)); ?>';
    $(window).on('load', function() {
        try {
            var parser = new edjsParser({
                embed: {
                    useProvidedLength: false,
                }
            });
            var html = parser.parse(JSON.parse(commentsJson));
            $(".comments").html(html)
            console.log(html);
        } catch (e) {
            $(".comments").html(commentsJson);
        }
    });
</script>
<?php include '../includes/footer.php'; ?>
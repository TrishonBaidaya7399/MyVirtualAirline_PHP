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
<?php include '../includes/header.php'; ?>
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
<section id="content" class="cp section offset-header">
    <?php if ($bookingCancelled) { ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success text-center">You have successfully cancelled this flight booking.</div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($hasBooking) { ?>
        <div class="container">
            <div class="row">
                <?php if ($bid->status == 0) { ?>
                    <div class="col-md-9">
                        <?php if (simbrief_enabled) { ?>
                            <p>
                                <i>You can dispatch this flight via SimBrief instead.</i>
                            </p>
                            <p><a href="<?php echo website_base_url; ?>site_pilot_functions/simbrief/dispatch.php"
                                    class="btn btn-success">Dispatch via SimBrief</a>
                            </p>
                        <?php } ?>
                    </div>
                    <div class="col-md-3 text-right">
                        <p>&nbsp;</p>
                        <form method="post" class="form">
                            <button name="btncancel" type="submit" id="btncancel" class="btn btn-default"><i
                                    class="fa fa-exclamation-triangle" aria-hidden="true"></i> Cancel Flight</button>
                        </form>
                    </div>

                <?php } else { ?>
                    <div class="col-md-9">
                        This flight is in progress.
                    </div>
                <?php } ?>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <hr />
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo empty($bid->flightNumber) ? "N/A" : $bid->flightNumber; ?>
                                OFP Summary (<?php echo date("d-m-Y"); ?>)
                            </h3>
                        </div>
                        <div class="panel-body row-space">
                            <div class="row">
                                <div class="col-md-2 col-xs-2">
                                    <strong>Flight No</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <strong>Aircraft</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <strong>Depart</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <strong>Arrive</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <strong>Depart Time</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <strong>Arrive Time</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-xs-2">
                                    <?php echo empty($bid->flightNumber) ? "N/A" : $bid->flightNumber; ?>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <?php echo empty($bid->aircraft) ? "N/A" : $bid->aircraft; ?>
                                    (<?php echo empty($bid->aircraftReg) ? "N/A" : $bid->aircraftReg; ?>)
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <?php echo empty($bid->departureIcao) ? "N/A" : $bid->departureIcao; ?>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <?php echo empty($bid->arrivalIcao) ? "N/A" : $bid->arrivalIcao; ?>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <?php echo empty($bid->departureTime) ? "N/A" : $bid->departureTime; ?>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <?php echo empty($bid->arrivalTime) ? "N/A" : $bid->arrivalTime; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-xs-2">
                                    <strong>Air Time</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <strong>PAX</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <strong>Cargo</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <strong>Crew</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <strong>Distance approx.</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <strong>PIC</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-xs-2">
                                    <?php echo empty($bid->duration) ? "N/A" : $bid->duration; ?>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <?php echo empty($bid->totalPax) ? "N/A" : $bid->totalPax; ?>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <?php echo getCargoDisplayValue($bid->cargo); ?>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <?php echo empty($bid->fleetAircraft->totalCrew) ? "N/A" : $bid->fleetAircraft->totalCrew; ?>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <?php echo (!empty($bid->arrivalIcao) && !empty($bid->departureIcao)) ? number_format(get_distance($bid->airportInfo[0]->lng, $bid->airportInfo[0]->lat, $bid->airportInfo[1]->lng, $bid->airportInfo[1]->lat), 1) . 'nm' : "N/A"; ?>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <?php echo $_SESSION['name']; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-xs-2">
                                    <strong>MZFW</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <strong>MTOW</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <strong>MLW</strong>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    &nbsp;
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    &nbsp;
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-xs-2">
                                    <?php echo empty($bid->fleetAircraft->mzfw) ? "N/A" : $bid->fleetAircraft->mzfw; ?>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <?php echo empty($bid->fleetAircraft->mtow) ? "N/A" : $bid->fleetAircraft->mtow; ?>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    <?php echo empty($bid->fleetAircraft->mlw) ? "N/A" : $bid->fleetAircraft->mlw; ?>
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    &nbsp;
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    &nbsp;
                                </div>
                                <div class="col-md-2 col-xs-2">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <strong>Routing</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <?php echo empty($bid->route) ? "N/A" : $bid->route; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <strong>Remarks</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-xs-12 comments">

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
                            <h3 class="panel-title">Current Winds</h3>
                        </div>
                        <div class="panel-body">
                            <iframe width="100%" height="450"
                                src="https://embed.windy.com/embed2.html?lat=52.987&lon=-1.068&zoom=1&level=surface&overlay=wind&menu=&message=true&marker=&calendar=&pressure=true&type=map&location=coordinates&detail=&detailLat=52.987&detailLon=-1.068&metricWind=kt&metricTemp=%C2%B0C&radarRange=-1"
                                frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!empty($bid->departureIcao)) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Departure Airport Briefing (<?php echo $bid->departureIcao; ?> -
                                    <?php echo $depAirport->name; ?>)</h3>
                            </div>
                            <div class="panel-body" style="max-height:500px; overflow-y:scroll;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 metar-table">
                                                <?php
                                                get_metar($bid->departureIcao);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Runway Information</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-hover">
                                                            <?php if (empty($depAirport->runways)) { ?>
                                                                No runways to display.
                                                                <hr />
                                                            <?php } else { ?>
                                                                <?php
                                                                foreach ($depAirport->runways as $runway) {
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
                                                    <div class="col-md-12">
                                                        <table class="table table-hover">
                                                            <?php if (empty($depAirport->airportFrequencies)) { ?>
                                                                No frequencies to display.
                                                                <hr />
                                                            <?php } else { ?>
                                                                <?php
                                                                foreach ($depAirport->airportFrequencies as $com) {
                                                                ?>
                                                                    <tr>
                                                                        <td><strong>Type</strong></td>
                                                                        <td><?php echo $com->comType; ?>
                                                                        </td>
                                                                        <td><strong>Frequency</strong></td>
                                                                        <td><?php echo number_format($com->comFrequency, 3) . ' Mhz'; ?>
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
                                                    <div class="col-md-12">
                                                        <table class="table table-hover">
                                                            <?php if (empty($depAirport->navaids)) { ?>
                                                                No navaids to display.
                                                                <hr />
                                                            <?php } else { ?>
                                                                <?php
                                                                foreach ($depAirport->navaids as $nav) {
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
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (!empty($bid->arrivalIcao)) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Arrival Airport Briefing (<?php echo $bid->arrivalIcao; ?> -
                                    <?php echo $arrAirport->name; ?>)</h3>
                            </div>
                            <div class="panel-body" style="max-height:500px; overflow-y:scroll;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 metar-table">
                                                <?php
                                                get_metar($bid->arrivalIcao);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Runway Information</h3>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-hover">
                                                            <?php if (empty($arrAirport->runways)) { ?>
                                                                No runways to display.
                                                                <hr />
                                                            <?php } else { ?>
                                                                <?php
                                                                foreach ($arrAirport->runways as $runway) {
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
                                                    <div class="col-md-12">
                                                        <table class="table table-hover">
                                                            <?php if (empty($arrAirport->airportFrequencies)) { ?>
                                                                No frequencies to display.
                                                                <hr />
                                                            <?php } else { ?>
                                                                <?php
                                                                foreach ($arrAirport->airportFrequencies as $com) {
                                                                ?>
                                                                    <tr>
                                                                        <td><strong>Type</strong></td>
                                                                        <td><?php echo $com->comType; ?>
                                                                        </td>
                                                                        <td><strong>Frequency</strong></td>
                                                                        <td><?php echo number_format($com->comFrequency, 3) . ' Mhz'; ?>
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
                                                    <div class="col-md-12">
                                                        <table class="table table-hover">
                                                            <?php if (empty($arrAirport->navaids)) { ?>
                                                                No navaids to display.
                                                                <hr />
                                                            <?php } else { ?>
                                                                <?php
                                                                foreach ($arrAirport->navaids as $nav) {
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
                        </div>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        include_once '../site_widgets/map_flight.php'; ?>
                        <div id="map" style="height:500px;"></div>
                    </div>
                </div>
                </div>
        </div>

    <?php } else { ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="jumbotron text-center">
                        <p>You don't have any active flight bookings.</p>
                        <p><a href="<?php echo website_base_url; ?>flight_search.php" class="btn btn-default"><i
                                    class="fa fa-search" aria-hidden="true"></i> Find Flights</a></p>
                        <?php if (simbrief_allow_charter) { ?>
                            <hr />
                            <p>Or, you can dispatch a charter flight with SimBrief.</p>
                            <p><a href="<?php echo website_base_url; ?>site_pilot_functions/simbrief/dispatch.php"
                                    class="btn btn-default"> Dispatch Charter
                                    Flight</a>
                            </p>
                        <?php } ?>
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
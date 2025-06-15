<?php

use Proxy\Api\Api;

require_once __DIR__ . '/proxy/api.php';
include 'lib/functions.php';
include 'config.php';
Api::__constructStatic();
$path_data = null;
$id = cleanString($_GET['id']);
$pirep = null;
$res = Api::sendAsync('GET', 'v1/operations/pirep/' . $id, null);
if ($res->getStatusCode() == 200) {
    $pirep = json_decode($res->getBody());
    $pilotName = "NA";
    $profileImahe = "";
    $pilot = null;
    $res = Api::sendAsync('GET', 'v1/pilot/' . $pirep->pilotId, null);
    if ($res->getStatusCode() == 200) {
        $pilot = json_decode($res->getBody());
        $pilotName = $pilot->name;
        $profileImage = $pilot->profileImage;
    }
    $mapData = [];
    array_push($mapData, $pirep->departure);
    array_push($mapData, $pirep->arrival);
    array_push($mapData, $pirep->alternate);
    $path_data = $pirep->pathData;
    $perfdata = json_decode($pirep->perfData, true);
    $pirepStatus = "";
    switch ($pirep->approvedStatus) {
        case 0:
            $pirepStatus = "<span style='color:orange;'>Pending Approval</span>";
            break;
        case 1:
            $pirepStatus = "<span style='color:green;'>Approved</span>";
            break;
        case 2:
            $pirepStatus = "<span style='color:red;'>Denied</span>";
            break;
    }
} else {
    header('Location: ' . website_base_url);
    die();
}
?>
<?php
session_start();
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include 'includes/header.php'; ?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <?php if (!empty($pirep->adminComments) && isset($_SESSION['pilotid'])) {
            if ($_SESSION['pilotid'] == $pirep->pilotId) {
        ?>
        <div class="alert alert-info"><strong>Review Comments:</strong> <?php echo $pirep->adminComments; ?>
        </div>
        <?php
            }
        } ?>
        <?php if ($pirep->flightNumber != "TRX") { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Flight Report</h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Pilot</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php if ($profileImage != "") { ?>
                                    <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $profileImage ?>"
                                        class="img-circle pilot-profile-image-small" />
                                    <?php } else { ?>
                                    <i class="fa fa-user-circle profile-small" aria-hidden="true"></i>
                                    <?php } ?>&nbsp;
                                    <a
                                        href="<?php echo website_base_url; ?>profile.php?id=<?php echo $pirep->pilotId; ?>"><?php echo $pilotName; ?>
                                        (<?php echo $pirep->callsign; ?>)</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Flight Date</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <i class="fa fa-calendar"></i>
                                    <?php echo (new DateTime($pirep->date))->format('d M Y'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Review Status</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php echo $pirepStatus; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Flight Number</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php echo $pirep->flightNumber; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Departure ICAO</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <i class="fa fa-map-marker"></i> <a
                                        href="airport_info.php?airport=<?php echo $pirep->departureIcao; ?>"><?php echo $pirep->departureIcao; ?></a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Arrival ICAO</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <i class="fa fa-map-marker"></i> <a
                                        href="airport_info.php?airport=<?php echo $pirep->arrivalIcao; ?>"><?php echo $pirep->arrivalIcao; ?></a>
                                    <?php echo $pirep->arrivedAlternate ? "(diverted to alternate)" : ""; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Alternate ICAO</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php if (!empty($pirep->alternateIcao)) { ?>
                                    <i class="fa fa-map-marker"></i> <a
                                        href="airport_info.php?airport=<?php echo $pirep->alternateIcao; ?>"><?php echo $pirep->alternateIcao; ?></a>
                                    <?php } else { ?>
                                    N/A
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Route</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php echo $pirep->route; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Aircraft</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php echo $pirep->aircraft; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Fuel Used</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <i class="fa fa-flask"></i> <?php echo getFuelDisplayValue($pirep->fuel); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Distance</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <i class="fa fa-globe"></i> <?php echo number_format($pirep->distance); ?>nm
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Passengers</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php echo $pirep->pax; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Cargo</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php echo getCargoDisplayValue($pirep->cargo); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Departure Time</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <i class="fa fa-clock-o"></i> <?php echo $pirep->departureTime; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Arrival Time</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <i class="fa fa-clock-o"></i> <?php echo $pirep->arrivalTime; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Flight Duration</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <i class="fa fa-clock-o"></i> <?php echo $pirep->duration; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Earnings</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    $<?php echo number_format($pirep->pay, 2); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Flight XP</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php echo number_format($pirep->xp); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Performance Score</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php echo $pirep->score; ?>%
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Landing Rate</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php echo ($pirep->landingRate == 0.00 ? "N/A" : number_format($pirep->landingRate) . "fpm"); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Flight Type</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php if ($pirep->flightTypeDescription == "activity") { ?>
                                    Tour/Event
                                    <?php } elseif ($pirep->flightTypeDescription == "scheduled") { ?>
                                    Scheduled
                                    <?php } else { ?>
                                    Charter
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Comments</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php echo $pirep->comments; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($perfdata != "" && array_key_exists('Messages', $perfdata)) { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Skill Analysis <i>(<?php echo $pirep->score; ?>%)</i>
                        </h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Stall Detected</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo ((int) $perfdata["Stall"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>") ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Crash Detected</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo ((int) $perfdata["Crashed"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>") ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Landing Lights Below 10k</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo ((int) $perfdata["LandingLightsBelow10k"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>") ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Landing Lights Above 10k</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo ((int) $perfdata["LandingLightsAbove10k"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>") ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Overspeed / Stress Detected</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo ((int) $perfdata["Overspeed"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>") ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Taxi Overspeed</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo ((int) $perfdata["TaxiOverspeed"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>") ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Overspeed below 10k</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo ((int) $perfdata["OverspeedBelow10k"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>") ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Beacon Off Engine On</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo ((int) $perfdata["BeaconOffWhenEngineOn"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>") ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Slew Detected</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo ((int) $perfdata["Slew"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>") ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Pause Detected</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo ((int) $perfdata["FlightPause"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>") ?>
                                </div>
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
                        <h3 class="panel-title">Take-off/Landing Analysis <i>(Landing Rate
                                <?php echo ($pirep->landingRate == 0.00 ? "N/A" : number_format($pirep->landingRate) . "fpm"); ?>)</i>
                        </h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Take-off G-Force</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo $perfdata["TakeoffG"]; ?>g
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Rotate Speed</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo number_format($perfdata["TakeoffSpeed"]); ?>kt
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Rotate Pitch</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo $perfdata["TakeoffPitch"]; ?>&deg;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Rotate Bank</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo $perfdata["TakeOffBank"]; ?>&deg;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Gear-up Speed</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo empty($perfdata["GearupSpeed"]) ? "NA" : number_format($perfdata["GearupSpeed"]) . 'kt'; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Gear-up Altitude</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo empty($perfdata["GearupAltitude"]) ? "NA" : number_format($perfdata["GearupAltitude"]) . 'ft'; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Take-Off Winds</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo $perfdata["TakeOffWindDirection"]; ?>/<?php echo $perfdata["TakeOffWindSpeed"]; ?>kt
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>TAT Departure/Arrival</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo $perfdata["TakeOffAirTemp"]; ?>&deg;C/<?php echo $perfdata["LandingAirTemp"]; ?>&deg;C
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Touch-down G-Force</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo $perfdata["LandingG"]; ?>g
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Touch-down Speed</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo number_format($perfdata["LandingSpeed"]); ?>kt
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Touch-down Pitch</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo $perfdata["LandingPitch"]; ?>&deg;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Touch-down Bank</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo $perfdata["LandingBank"]; ?>&deg;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Gear-down Speed</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo empty($perfdata["GeardownSpeed"]) ? "NA" : number_format($perfdata["GeardownSpeed"]) . 'kt'; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Gear-down Altitude</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo empty($perfdata["GeardownAltitude"]) ? "NA" : number_format($perfdata["GeardownAltitude"]) . 'ft'; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Landing Winds</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo empty($perfdata["LandingWindDirection"]) ? "NA" : $perfdata["LandingWindDirection"] . '/' . $perfdata["LandingWindSpeed"] . 'kt'; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-6">
                                    <strong>Touch-Down Spoilers Deployed</strong>
                                </div>
                                <div class="col-md-4 col-xs-6">
                                    <?php echo ((int) $perfdata["LandingSpoilersDeployed"] == 0 ? "No" : "Yes") ?>
                                </div>
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
                        <h3 class="panel-title">Flight Log</h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-12">
                            <div class="flightlog">
                                <?php foreach ($perfdata["Messages"] as $item) { ?>
                                <br /><?php echo $item; ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!empty($pirep->pathData)) { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Altitude Profile</h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-12">
                            <canvas id="altChart" width="100%" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php } ?>
        <div class="row">
            <div class="col-md-12">
                <?php include_once 'site_widgets/map_flight.php'; ?>
                <div id="map"></div>
            </div>
        </div>
        <?php } else { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Hour Transfer</h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Pilot</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php if ($profileImage != "") { ?>
                                    <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $profileImage ?>"
                                        class="img-circle pilot-profile-image-small" />
                                    <?php } else { ?>
                                    <i class="fa fa-user-circle profile-small" aria-hidden="true"></i>
                                    <?php } ?>&nbsp;
                                    <a
                                        href="<?php echo website_base_url; ?>profile.php?id=<?php echo $pirep->pilotId; ?>"><?php echo $pilotName; ?>
                                        (<?php echo $pirep->callsign; ?>)</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Hours</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <i class="fa fa-clock-o"></i> <?php echo $pirep->duration; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Date</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <i class="fa fa-calendar"></i>
                                    <?php echo (new DateTime($pirep->dateFilled))->format('d M Y H:m'); ?>
                                    UTC
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-xs-6">
                                    <strong>Comments</strong>
                                </div>
                                <div class="col-md-8 col-xs-6">
                                    <?php echo $pirep->comments; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
</script>
<?php if (!empty($pirep->pathData)) { ?>
<script type="text/javascript">
var pathData = <?php echo json_encode($pirep->pathData); ?>;
var graphData = JSON.parse(pathData).map(e => e['Altitude']);
var ctx = document.getElementById("altChart");
AltChart = new Chart(ctx, {
    type: "line",
    data: {
        labels: graphData,
        datasets: [{
            label: "Alt",
            lineTension: 0.3,
            backgroundColor: "#eee",
            borderColor: "rgba(14, 154, 201)",
            pointRadius: 0,
            pointBackgroundColor: "rgba(14, 154, 201)",
            pointBorderColor: "rgba(14, 154, 201)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(14, 154, 201)",
            pointHoverBorderColor: "rgba(14, 154, 201)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: graphData
        }]
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7,
                    display: false
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return value;
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }]
        },
        legend: {
            display: false
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: "#6e707e",
            titleFontSize: 14,
            borderColor: "#dddfeb",
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: "index",
            caretPadding: 10,
            callbacks: {
                label: function() {},
                title: function(tooltipItem, chart) {
                    return tooltipItem[0].xLabel + 'ft';
                }
            },
        }
    }
});
</script>
<?php } ?>
<?php include 'includes/footer.php';
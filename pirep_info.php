<?php
use Proxy\Api\Api;

require_once __DIR__ . '/proxy/api.php';
include 'lib/functions.php';
include 'config.php';
Api::__constructStatic();

// Initialize variables
$path_data = null;
$id = cleanString($_GET['id']);
$pirep = null;

// Fetch PIREP data
$res = Api::sendAsync('GET', 'v1/operations/pirep/' . $id, null);
if ($res->getStatusCode() == 200) { // START: PIREP fetch block
    $pirep = json_decode($res->getBody());
    $pilotName = "NA";
    $profileImage = "";
    $pilot = null;

    // Fetch pilot data
    $res = Api::sendAsync('GET', 'v1/pilot/' . $pirep->pilotId, null);
    if ($res->getStatusCode() == 200) { // START: Pilot fetch block
        $pilot = json_decode($res->getBody());
        $pilotName = $pilot->name;
        $profileImage = $pilot->profileImage;
    } // END: Pilot fetch block

    $mapData = [];
    array_push($mapData, $pirep->departure);
    array_push($mapData, $pirep->arrival);
    array_push($mapData, $pirep->alternate);
    $path_data = $pirep->pathData;
    $perfdata = json_decode($pirep->perfData, true);

    // Set PIREP status
    $pirepStatus = "";
    switch ($pirep->approvedStatus) {
        case 0:
            $pirepStatus = "<span style='color:orange;'>Pending Approval</span>";
            break;
        case 1:
            $pirepStatus = "<span style='color:rgb(21, 182, 6);'>Approved</span>";
            break;
        case 2:
            $pirepStatus = "<span style='color:red;'>Denied</span>";
            break;
    }
} else { // START: PIREP fetch failure
    header('Location: ' . website_base_url);
    die();
} // END: PIREP fetch block
?>
<?php
session_start();
$MetaPageTitle = "Flight Report";
$MetaPageDescription = "Detailed flight report including pilot information, flight performance, and analysis.";
$MetaPageKeywords = "flight report, pilot, aviation, pirep, performance analysis";
?>
<?php include 'includes/header.php'; ?>
<style>
/* Flight Report Section Styles with Parallax Background */
.flight-section {
    position: relative;
    padding: 60px 0;
    min-height: calc(100vh - 128px);
    background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.flight-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.flight-section .container {
    position: relative;
    z-index: 2;
}

.offset-header {
    padding-top: 100px;
}

/* Glassmorphism Card */
.glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 20px;
}

.glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Panel Styles */
.panel-heading {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.panel-title {
    color: #ffffff;
    font-size: 2rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.panel-body {
    padding: 20px;
    color: #ffffff;
}

.row-space .row {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    flex-wrap: nowrap;
}

.row-space .row > div.label {
    width: 200px;
    flex-shrink: 0;
    font-size: 14px;
    color: #ffffff;
    text-align: start;
}

.row-space .row > div.data {
    flex-grow: 1;
    font-size: 14px;
    color: #ffffff;
}

.row-space .row > div strong {
    color: #ffffff;
    font-weight: 600;
}

.pass {
    color: rgb(21, 182, 6);
}

.fail {
    color: red;
}

/* Flight Log */
.flightlog {
    white-space: pre-wrap;
    font-size: 14px;
    color: #ffffff;
}

#map {
    margin-bottom: 0 !important;
}

/* Map and Chart */
#map, #altChart {
    width: 100%;
    height: 400px;
    border-radius: 0 0 10px 10px;
}

/* Alert */
.alert-info {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}

/* Profile Image */
.pilot-profile-image-small {
    width: 30px;
    height: 30px;
    max-width: 30px;
    max-height: 30px;
    border: 1px solid rgba(255, 215, 0, 1);
    background: gray;
    object-fit: cover;
    margin-right: 5px;
    vertical-align: middle;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .flight-section {
        padding: 80px 0 !important;
    }
    .offset-header {
        padding-top: 80px;
    }
    .panel-title {
        font-size: 1.5rem;
    }
    .panel-body {
        padding: 15px;
    }
    #map, #altChart {
        height: 350px;
    }
    .row-space .row > div.label {
        width: 200px;
        font-size: 13px;
    }
    .row-space .row > div.data {
        font-size: 13px;
    }
}

@media (max-width: 992px) {
    .flight-section {
        padding: 80px 0 !important;
        background-attachment: scroll;
    }
    .offset-header {
        padding-top: 60px;
    }
    .panel-title {
        font-size: 1.5rem;
    }
    .row-space .row > div.label {
        width: 200px;
        font-size: 12px;
    }
    .row-space .row > div.data {
        font-size: 12px;
    }
    #map, #altChart {
        height: 300px;
    }
}

@media (max-width: 768px) {
    .flight-section {
        padding: 80px 0 !important;
    }
    .offset-header {
        padding-top: 50px;
    }
    .panel-title {
        font-size: 1.5rem;
    }
    .panel-body {
        padding: 10px;
    }
    .row-space .row {
        margin-bottom: 8px;
    }
    .row-space .row > div.label {
        width: 200px;
        font-size: 11px;
    }
    .row-space .row > div.data {
        font-size: 11px;
    }
    #map, #altChart {
        height: 250px;
    }
    .pilot-profile-image-small {
        width: 25px;
        height: 25px;
        max-width: 25px;
        max-height: 25px;
    }
}

@media (max-width: 576px) {
    .flight-section {
        padding: 80px 0 !important;
    }
    .offset-header {
        padding-top: 40px;
    }
    .panel-title {
        font-size: 1.5rem;
    }
    .panel-body {
        padding: 8px;
    }
    .row-space .row > div.label {
        width: 200px;
        font-size: 10px;
    }
    .row-space .row > div.data {
        font-size: 10px;
    }
    #map, #altChart {
        height: 200px;
    }
    .alert-info {
        padding: 10px;
        font-size: 12px;
    }
    .pilot-profile-image-small {
        width: 20px;
        height: 20px;
        max-width: 20px;
        max-height: 20px;
    }
}

/* Print Styles */
@media print {
    .flight-section {
        background: white;
        padding: 20px 0;
    }
    .flight-section::before {
        display: none;
    }
    .glass-card, .panel-heading, .alert-info {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
    }
    .panel-title, .panel-body, .row-space .row > div, .flightlog, .alert-info {
        color: black;
        text-shadow: none;
    }
    .pilot-profile-image-small {
        border-color: #000;
    }
}
</style>
<section id="content" class="flight-section offset-header">
    <div class="container">
        <?php if (!empty($pirep->adminComments) && isset($_SESSION['pilotid']) && $_SESSION['pilotid'] == $pirep->pilotId) { // START: Admin comments block ?>
        <div class="alert alert-info"><strong>Review Comments:</strong> <?php echo htmlspecialchars($pirep->adminComments, ENT_QUOTES); ?></div>
        <?php } // END: Admin comments block ?>

        <?php if ($pirep->flightNumber != "TRX") { // START: Flight report block ?>
        <div class="row">
            <div class="col-md-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Flight Report</h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="label"><strong>Pilot</strong></div>
                                <div class="data">
                                    <img src="<?php echo !empty($profileImage) ? website_base_url . 'Uploads/profiles/' . $profileImage : website_base_url . 'images/avatar.webp'; ?>" class="img-circle pilot-profile-image-small" alt="Pilot Profile" />
                                    <a style="color: rgba(255, 215, 0, 1);" href="<?php echo website_base_url; ?>profile.php?id=<?php echo $pirep->pilotId; ?>"><?php echo htmlspecialchars($pilotName, ENT_QUOTES); ?> (<?php echo htmlspecialchars($pirep->callsign, ENT_QUOTES); ?>)</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Flight Date</strong></div>
                                <div class="data"><i class="fa fa-calendar"></i> <?php echo (new DateTime($pirep->date))->format('d M Y'); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Review Status</strong></div>
                                <div class="data"><?php echo $pirepStatus; ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Flight Number</strong></div>
                                <div class="data"><?php echo htmlspecialchars($pirep->flightNumber, ENT_QUOTES); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Departure ICAO</strong></div>
                                <div class="data">
                                    <i class="fa fa-map-marker" style="color: rgba(255, 215, 0, 1);"></i>
                                    <a style="color: rgba(255, 215, 0, 1);" href="airport_info.php?airport=<?php echo $pirep->departureIcao; ?>"><?php echo htmlspecialchars($pirep->departureIcao, ENT_QUOTES); ?></a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Arrival ICAO</strong></div>
                                <div class="data">
                                    <i class="fa fa-map-marker" style="color: rgba(255, 215, 0, 1);"></i>
                                    <a style="color: rgba(255, 215, 0, 1);" href="airport_info.php?airport=<?php echo $pirep->arrivalIcao; ?>"><?php echo htmlspecialchars($pirep->arrivalIcao, ENT_QUOTES); ?></a>
                                    <?php echo $pirep->arrivedAlternate ? "(diverted to alternate)" : ""; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Alternate ICAO</strong></div>
                                <div class="data">
                                    <?php if (!empty($pirep->alternateIcao)) { // START: Alternate ICAO check ?>
                                    <i class="fa fa-map-marker" style="color: rgba(255, 215, 0, 1);"></i>
                                    <a style="color: rgba(255, 215, 0, 1);" href="airport_info.php?airport=<?php echo $pirep->alternateIcao; ?>"><?php echo htmlspecialchars($pirep->alternateIcao, ENT_QUOTES); ?></a>
                                    <?php } else { // START: No alternate ICAO ?>
                                    N/A
                                    <?php } // END: Alternate ICAO check ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Route</strong></div>
                                <div class="data"><?php echo htmlspecialchars($pirep->route, ENT_QUOTES); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Aircraft</strong></div>
                                <div class="data"><?php echo htmlspecialchars($pirep->aircraft, ENT_QUOTES); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Fuel Used</strong></div>
                                <div class="data"><i class="fa fa-flask"></i> <?php echo getFuelDisplayValue($pirep->fuel); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Distance</strong></div>
                                <div class="data"><i class="fa fa-globe"></i> <?php echo number_format($pirep->distance); ?>nm</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="label"><strong>Passengers</strong></div>
                                <div class="data"><?php echo $pirep->pax; ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Cargo</strong></div>
                                <div class="data"><?php echo getCargoDisplayValue($pirep->cargo); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Departure Time</strong></div>
                                <div class="data"><i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($pirep->departureTime, ENT_QUOTES); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Arrival Time</strong></div>
                                <div class="data"><i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($pirep->arrivalTime, ENT_QUOTES); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Flight Duration</strong></div>
                                <div class="data"><i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($pirep->duration, ENT_QUOTES); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Earnings</strong></div>
                                <div class="data">$<?php echo number_format($pirep->pay, 2); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Flight XP</strong></div>
                                <div class="data"><?php echo number_format($pirep->xp); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Performance Score</strong></div>
                                <div class="data"><?php echo $pirep->score; ?>%</div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Landing Rate</strong></div>
                                <div class="data"><?php echo ($pirep->landingRate == 0.00 ? "N/A" : number_format($pirep->landingRate) . "fpm"); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Flight Type</strong></div>
                                <div class="data">
                                    <?php if ($pirep->flightTypeDescription == "activity") { // START: Flight type check ?>
                                    Tour/Event
                                    <?php } elseif ($pirep->flightTypeDescription == "scheduled") { // START: Scheduled check ?>
                                    Scheduled
                                    <?php } else { // START: Charter ?>
                                    Charter
                                    <?php } // END: Flight type check ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Comments</strong></div>
                                <div class="data"><?php echo htmlspecialchars($pirep->comments, ENT_QUOTES); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($perfdata != "" && array_key_exists('Messages', $perfdata)) { // START: Skill analysis block ?>
        <div class="row">
            <div class="col-md-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Skill Analysis <i>(<?php echo $pirep->score; ?>%)</i></h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="label"><strong>Stall Detected</strong></div>
                                <div class="data"><?php echo ((int) $perfdata["Stall"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>"); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Crash Detected</strong></div>
                                <div class="data"><?php echo ((int) $perfdata["Crashed"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>"); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Landing Lights Below 10k</strong></div>
                                <div class="data"><?php echo ((int) $perfdata["LandingLightsBelow10k"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>"); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Landing Lights Above 10k</strong></div>
                                <div class="data"><?php echo ((int) $perfdata["LandingLightsAbove10k"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>"); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Overspeed / Stress Detected</strong></div>
                                <div class="data"><?php echo ((int) $perfdata["Overspeed"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>"); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="label"><strong>Taxi Overspeed</strong></div>
                                <div class="data"><?php echo ((int) $perfdata["TaxiOverspeed"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>"); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Overspeed below 10k</strong></div>
                                <div class="data"><?php echo ((int) $perfdata["OverspeedBelow10k"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>"); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Beacon Off Engine On</strong></div>
                                <div class="data"><?php echo ((int) $perfdata["BeaconOffWhenEngineOn"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>"); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Slew Detected</strong></div>
                                <div class="data"><?php echo ((int) $perfdata["Slew"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>"); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Pause Detected</strong></div>
                                <div class="data"><?php echo ((int) $perfdata["FlightPause"] == 0 ? "<span class='pass'>OK</span>" : "<span class='fail'>FAIL</span>"); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Take-off/Landing Analysis <i>(Landing Rate <?php echo ($pirep->landingRate == 0.00 ? "N/A" : number_format($pirep->landingRate) . "fpm"); ?>)</i></h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="label"><strong>Take-off G-Force</strong></div>
                                <div class="data"><?php echo $perfdata["TakeoffG"]; ?>g</div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Rotate Speed</strong></div>
                                <div class="data"><?php echo number_format($perfdata["TakeoffSpeed"]); ?>kt</div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Rotate Pitch</strong></div>
                                <div class="data"><?php echo $perfdata["TakeoffPitch"]; ?>°</div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Rotate Bank</strong></div>
                                <div class="data"><?php echo $perfdata["TakeOffBank"]; ?>°</div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Gear-up Speed</strong></div>
                                <div class="data"><?php echo empty($perfdata["GearupSpeed"]) ? "NA" : number_format($perfdata["GearupSpeed"]) . 'kt'; ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Gear-up Altitude</strong></div>
                                <div class="data"><?php echo empty($perfdata["GearupAltitude"]) ? "NA" : number_format($perfdata["GearupAltitude"]) . 'ft'; ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Take-Off Winds</strong></div>
                                <div class="data"><?php echo $perfdata["TakeOffWindDirection"]; ?>/<?php echo $perfdata["TakeOffWindSpeed"]; ?>kt</div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>TAT Departure/Arrival</strong></div>
                                <div class="data"><?php echo $perfdata["TakeOffAirTemp"]; ?>°C/<?php echo $perfdata["LandingAirTemp"]; ?>°C</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="label"><strong>Touch-down G-Force</strong></div>
                                <div class="data"><?php echo $perfdata["LandingG"]; ?>g</div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Touch-down Speed</strong></div>
                                <div class="data"><?php echo number_format($perfdata["LandingSpeed"]); ?>kt</div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Touch-down Pitch</strong></div>
                                <div class="data"><?php echo $perfdata["LandingPitch"]; ?>°</div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Touch-down Bank</strong></div>
                                <div class="data"><?php echo $perfdata["LandingBank"]; ?>°</div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Gear-down Speed</strong></div>
                                <div class="data"><?php echo empty($perfdata["GeardownSpeed"]) ? "NA" : number_format($perfdata["GeardownSpeed"]) . 'kt'; ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Gear-down Altitude</strong></div>
                                <div class="data"><?php echo empty($perfdata["GeardownAltitude"]) ? "NA" : number_format($perfdata["GeardownAltitude"]) . 'ft'; ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Landing Winds</strong></div>
                                <div class="data"><?php echo empty($perfdata["LandingWindDirection"]) ? "NA" : $perfdata["LandingWindDirection"] . '/' . $perfdata["LandingWindSpeed"] . 'kt'; ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Touch-Down Spoilers Deployed</strong></div>
                                <div class="data"><?php echo ((int) $perfdata["LandingSpoilersDeployed"] == 0 ? "No" : "Yes"); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Flight Log</h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-12">
                            <div class="flightlog">
                                <?php foreach ($perfdata["Messages"] as $item) { // START: Flight log loop ?>
                                <br><?php echo htmlspecialchars($item, ENT_QUOTES); ?>
                                <?php } // END: Flight log loop ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } // END: Skill analysis block ?>
        <?php if (!empty($pirep->pathData)) { // START: Altitude profile block ?>
        <div class="row">
            <div class="col-md-12">
                <div class="glass-card">
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
        <?php } // END: Altitude profile block ?>
        <div class="row">
            <div class="col-md-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Flight Path</h3>
                    </div>
                    <div class="panel-body row-space" style="padding: 0">
                        <div class="col-md-12" style="padding-left: 0; padding-right: 0">
                            <?php include_once 'site_widgets/map_flight.php'; ?>
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } else { // START: Hour transfer block ?>
        <div class="row">
            <div class="col-md-12">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Hour Transfer</h3>
                    </div>
                    <div class="panel-body row-space">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="label"><strong>Pilot</strong></div>
                                <div class="data">
                                    <img src="<?php echo !empty($profileImage) ? website_base_url . 'Uploads/profiles/' . $profileImage : website_base_url . 'images/avatar.webp'; ?>" class="img-circle pilot-profile-image-small" alt="Pilot Profile" />
                                    <a style="color: rgba(255, 215, 0, 1);" href="<?php echo website_base_url; ?>profile.php?id=<?php echo $pirep->pilotId; ?>"><?php echo htmlspecialchars($pilotName, ENT_QUOTES); ?> (<?php echo htmlspecialchars($pirep->callsign, ENT_QUOTES); ?>)</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Hours</strong></div>
                                <div class="data"><i class="fa fa-clock-o"></i> <?php echo htmlspecialchars($pirep->duration, ENT_QUOTES); ?></div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Date</strong></div>
                                <div class="data"><i class="fa fa-calendar"></i> <?php echo (new DateTime($pirep->dateFilled))->format('d M Y H:i'); ?> UTC</div>
                            </div>
                            <div class="row">
                                <div class="label"><strong>Comments</strong></div>
                                <div class="data"><?php echo htmlspecialchars($pirep->comments, ENT_QUOTES); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } // END: Hour transfer block ?>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
<?php if (!empty($pirep->pathData)) { // START: Chart.js block ?>
<script type="text/javascript">
var pathData = <?php echo json_encode($pirep->pathData); ?>;
var graphData = JSON.parse(pathData).map(e => e['Altitude']);
var ctx = document.getElementById("altChart");
var AltChart = new Chart(ctx, {
    type: "line",
    data: {
        labels: graphData,
        datasets: [{
            label: "Alt",
            lineTension: 0.3,
            backgroundColor: "rgba(255, 255, 255, 0.2)",
            borderColor: "rgba(14, 154, 201, 1)",
            pointRadius: 0,
            pointBackgroundColor: "rgba(14, 154, 201, 1)",
            pointBorderColor: "rgba(14, 154, 201, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(14, 154, 201, 1)",
            pointHoverBorderColor: "rgba(14, 154, 201, 1)",
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
                    callback: function(value) {
                        return value;
                    }
                },
                gridLines: {
                    color: "rgba(255, 255, 255, 0.2)",
                    zeroLineColor: "rgba(255, 255, 255, 0.2)",
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
            backgroundColor: "rgba(255, 255, 255, 0.8)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: "#6e707e",
            titleFontSize: 14,
            borderColor: "rgba(255, 255, 255, 0.2)",
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: "index",
            caretPadding: 10,
            callbacks: {
                label: function() {},
                title: function(tooltipItem) {
                    return tooltipItem[0].xLabel + 'ft';
                }
            }
        }
    }
});
</script>
<?php } // END: Chart.js block ?>
<?php include 'includes/footer.php'; ?>
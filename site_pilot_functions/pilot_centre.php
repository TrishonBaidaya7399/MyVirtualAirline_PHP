<?php

use Proxy\Api\Api;

include '../lib/functions.php';
include '../config.php';
Api::__constructStatic();
session_start();
validateSession();

$pid = $_SESSION['pilotid'];
$hasactivebid = false;
$pilot = null;
$res = Api::sendAsync('GET', 'v1/pilot/' . $pid, null);
if ($res->getStatusCode() == 200) {
    $pilot = json_decode($res->getBody());
} else {
    header('Location: ' . website_base_url);
    die();
}
$settings = null;
$limitDepatureLocation = false;
$res = Api::sendSync('GET', 'v1/airline', null);
if ($res->getStatusCode() == 200) {
    $settings = json_decode($res->getBody(), false);
    $limitDepatureLocation = $settings->limitDepartureLocation;
}
$bestLanding = null;
$res = Api::sendAsync('GET', 'v1/operations/pirep/pilot/best-landing/' . $pid, null);
if ($res->getStatusCode() == 200) {
    $bestLanding = json_decode($res->getBody(), false);
}
$latestFlight = null;
$mapData = [];
$res = Api::sendAsync('GET', 'v1/operations/pirep/pilot/latest', null);
if ($res->getStatusCode() == 200) {
    $latestFlight = json_decode($res->getBody(), false);
    array_push($mapData, $latestFlight->departure);
    array_push($mapData, $latestFlight->arrival);
    array_push($mapData, $latestFlight->alternate);
    $path_data = $latestFlight->pathData;
}
$hasactivebid = false;
$res = Api::sendAsync('GET', 'v1/bid/' . $pid, null);
$status = "";
if ($res->getStatusCode() == 200) {
    $bid = json_decode($res->getBody());
    $hasactivebid = true;
    $bidexpires = new DateTime($bid->dateBooked);
    $bidexpires->modify('+ ' . (int) $_SESSION['booking_expire_hours'] . ' hour');
    $bdepicao = $bid->departureIcao;
    $barricao = $bid->arrivalIcao;
}

$function = null;
if (isset($_GET['function'])) {
    $function = cleanString($_GET['function']);
}

if ($function == 'logout') {
    session_destroy();
    header('Location: ' . website_base_url . 'authentication/login.php?logout=yes');
    exit();
}
$activities = null;
$res = Api::sendSync('GET', 'v1/activities/active-lite', null);
if ($res->getStatusCode() == 200) {
    $activities = json_decode($res->getBody(), false);
}
$suggestedFlights = null;
$res = Api::sendSync('GET', 'v1/operations/schedule/suggested', null);
if ($res->getStatusCode() == 200) {
    $suggestedFlights = json_decode($res->getBody(), false);
}
$notams = null;
$res = Api::sendSync('GET', 'v1/notams', null);
if ($res->getStatusCode() == 200) {
    $notams = json_decode($res->getBody(), false);
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

.pilot_dashboard_section {
    position: relative;
    padding: 80px 0;
    padding-bottom: 20px !important;
    min-height: calc(100vh - 128px);
    background-image: url('../assets/images/backgrounds/dashboard_pilot_bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    overflow-y: auto;
}

.pilot_dashboard_section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.3);
    z-index: 1;
}

.pilot_dashboard_section .container {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Global Heading */
.global-heading {
    width: 100%;
    margin-bottom: 20px;
    text-align: center;
}

.global-heading .global-title {
    font-size: 40px;
    font-weight: 800;
    color: #fff;
    margin-top: 0 !important;
    text-transform: lowercase;
    width: fit-content;
}

@media (max-width: 612px) {
    .global-heading .global-title {
        font-size: 30px;
        font-weight: 700;
    }
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
    width: 100%;
    max-width: 100%;
    color: #fff;
    padding: 40px;
}

.glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Panel Heading */
.panel-heading {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 15px;
    border-bottom: 1px solid rgba(255, 215, 0, 1);
}

.panel-title {
    color: #fff;
    font-size: 1.8rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

/* Panel Body */
.panel-body {
    padding: 20px;
    color: #fff;
}

/* Jumbotron Styles */
.jumbotron {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 0;
    color: #fff;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.jumbotron h1 {
    font-size: 2.5rem;
    margin-top: 0;
}

.jumbotron p {
    font-size: 1.1rem;
}

.pilot-profile-image, .profile {
    margin-left: 15px;
}

.help-block {
    font-size: 0.9rem;
    color: #ccc;
}

/* Table Styles */
.table {
    width: 100%;
    margin-bottom: 0;
}

.table thead th {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: #fff;
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.table tbody td {
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 12px;
    vertical-align: middle;
    color: #fff;
}

.table tbody tr:hover {
    background: rgba(255, 255, 255, 0.25);
}

/* Activity Card Styles */
.activity-card-container {
    background-size: cover;
    background-position: center;
    height: 200px;
    margin-bottom: 15px;
    border-radius: 15px;
    overflow: hidden;
    position: relative;
    cursor: pointer;
}

.activity-card-hidden {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    color: #fff;
    padding: 20px;
    text-align: center;
    justify-content: center;
    align-items: center;
    display: flex;
}

.activity-card-hidden a {
    color: #fff;
    text-decoration: none;
    font-size: 1.2rem;
}
..col-md-12{
    padding: 0 !important;
}
/* Map Styles */
#map {
    width: 100%;
    border-radius: 15px;
    overflow: hidden;
}
i.profile-img{
    font-size: 200px ;
}
/* Responsive Design */
@media (max-width: 1200px) {
    .pilot_dashboard_section {
        padding: 80px 0;
    }
    
    .jumbotron, .panel-body {
        padding: 20px;
    }
    .table thead th, .table tbody td {
        padding: 10px;
    }
    .activity-card-container {
        height: 180px;
    }
}

@media (max-width: 992px) {
    .pilot_dashboard_section {
        padding: 80px 0;
        background-attachment: scroll;
    }
    
    .jumbotron h1 {
        font-size: 2rem;
    }
    .jumbotron p {
        font-size: 1rem;
    }
    .panel-title {
        font-size: 1.5rem;
    }
    .activity-card-container {
        height: 150px;
    }
    .table {
        font-size: 0.95rem;
    }
}

@media (max-width: 768px) {
    .pilot_dashboard_section {
        padding: 80px 0;
    }
        i.profile-img{
            font-size: 130px;
        }
    .glass-card{
padding: 24px;
    }

    .jumbotron {
        padding: 15px;
    }
    .jumbotron h1 {
        font-size: 1.8rem;
    }
    .panel-title {
        font-size: 1.3rem;
    }
    .activity-card-container {
        height: 120px;
    }
    .table thead th, .table tbody td {
        font-size: 0.9rem;
        padding: 8px;
    }
}

@media (max-width: 576px) {
    .pilot_dashboard_section {
        padding: 80px 0;
    }
    .glass-card{
padding: 18px;
    }

    .jumbotron h1 {
        font-size: 1.5rem;
    }
    .panel-title {
        font-size: 1.2rem;
    }
    .activity-card-container {
        height: 100px;
    }
    .table {
        font-size: 0.8rem;
    }
    .table thead th, .table tbody td {
        padding: 6px;
    }
}

/* Print Styles */
@media print {
    .pilot_dashboard_section {
        background: white;
        padding: 20px 0;
    }
    .pilot_dashboard_section::before {
        display: none;
    }
    .glass-card, .jumbotron, .panel-heading {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
    }
    .panel-title, .panel-body, .jumbotron, .table thead th, .table tbody td {
        color: black;
        text-shadow: none;
    }
    .table thead th, .table tbody td {
        border: 1px solid #ccc;
    }
}
</style>

<?php include '../includes/header.php'; ?>
<section id="content" class="pilot_dashboard_section">
    <div class="container">
        <div class="row" style="width: 100%">
            <div class="col-12">
                <div class="global-heading w-full">
                    <h3 class="global-title w-full">Pilot Dashboard</h3>
                </div>
            </div>
        </div>
        <div class="row" style="width: 100%">
            <div class="col-12">
                <?php if ($hasactivebid) { ?>
                    <div class="glass-card">
                        <div class="">
                            <p class="text-center">You have a
                                flight scheduled from <strong><?php echo empty($bdepicao) ? 'Any' : $bdepicao; ?></strong>
                                to
                                <strong><?php echo empty($barricao) ? 'Any' : $barricao; ?></strong>.
                                Visit
                                the <a href="<?php echo website_base_url; ?>site_pilot_functions/dispatch.php"
                                    class="btn btn-default js_showloader">Dispatch
                                    Center</a>
                                for your flight briefing.
                            </p>
                        </div>
                    </div>
                <?php } ?>
                <div class="">
                    <div style="color: #fff; margin-bottom: 30px">
                        <div class="">
                            <?php if ($pilot->profileImage != "") { ?>
                                <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $pilot->profileImage ?>"
                                    width="200" style="float:right;" class="img-circle pilot-profile-image" />
                            <?php } else { ?>
                                <i class="fa fa-user-circle profile-img" aria-hidden="true" style="float:right;"></i>
                            <?php } ?>
                            <h1 style="text-transform: lowercase">Hello, <?php echo explode(" ", $_SESSION['name'])[0]; ?>.
                            </h1>
                            <p>Your Pilot ID: <strong><?php echo $_SESSION['callsign']; ?></strong>
                                | <?php if (!empty($pilot->rank->imageUrl)) { ?><img
                                    src="<?php echo website_base_url; ?>uploads/ranks/<?php echo $pilot->rank->imageUrl; ?>"
                                    width="80" /> <strong><?php echo $pilot->rank->name; ?></strong><?php } else { ?>No
                                Rank<?php } ?>
                            </p>
                            <p>
                                Base: <strong><?php echo $pilot->hub; ?></strong>
                            </p>
                            <p>
                                Total Hours:
                                <strong><?php echo empty($pilot->totalHours) ? "No Flights" : $pilot->totalHours; ?></strong> |
                                XP: <strong><?php echo number_format($pilot->xp); ?></strong>
                            </p>
                            <p>Wallet Balance: <strong>$<span
                                        class="balance"><?php echo number_format($pilot->wallet, 2); ?></span></strong></p>
                            <p><strong><i class="fa fa-trophy gold" aria-hidden="true"></i></strong> Best Landing:

                                <?php if (!empty($bestLanding)) {
                                    echo '<strong>' . $bestLanding->landingRate . 'fpm</strong> on ' . (new DateTime($bestLanding->date))->format('d M Y') . ' at <i class="fa fa-map-marker" aria-hidden="true"></i> <a href="' . website_base_url . 'pirep_info.php?id=' . $bestLanding->id . '">' . $bestLanding->arrivalIcao . '</a>';
                                } else {
                                    echo '<strong>NA</strong>';
                                } ?>
                            </p>
                            <p><i class="fa fa-map-marker" aria-hidden="true"></i> Virtual Location: <strong><span
                                        class="virlocation"><?php echo empty($pilot->currentLocation) ? "NA" : $pilot->currentLocation; ?></span></strong>
                                <?php if ($limitDepatureLocation) { ?>| <a href="" class="btn btn-default jump">Jump Seat <i
                                        class="fa fa-arrow-right" aria-hidden="true"></i><i class="fa fa-map-marker"
                                        aria-hidden="true"></i></a><?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($suggestedFlights != null) { ?>
            <div class="row" style="width: 100%">
                <div class="col-12">
                     <div class="global-heading w-full">
                    <h3 class="global-title w-full">Suggested Flights</h3>
                </div>
                    <div class="glass-card">
                        <div class="">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><strong>Flight Number</strong></th>
                                        <th><strong>Departure</strong></th>
                                        <th><strong>Arrival</strong></th>
                                        <th><strong>Duration</strong></th>
                                        <th><strong>Aircraft</strong></th>
                                        <th><strong>Operator</strong></th>
                                        <th> </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($suggestedFlights as $key => $flight) { ?>
                                        <tr>
                                            <td><?php echo $flight->flightNumber; ?></td>
                                            <td><a href="/airport_info.php?airport=<?php echo $flight->depIcao; ?>"
                                                    target="_blank"
                                                    title="<?php echo $flight->depCity; ?>"><?php echo $flight->depIcao; ?>
                                                </a></td>
                                            <td><a href="/airport_info.php?airport=<?php echo $flight->arrIcao; ?>"
                                                    target="_blank"
                                                    title="<?php echo $flight->arrCity; ?>"><?php echo $flight->arrIcao; ?>
                                                </a></td>
                                            <td><?php echo $flight->duration; ?></td>
                                            <td><?php echo $flight->aircraftTypeCommas; ?></td>
                                            <td><?php echo $flight->operator; ?></td>
                                            <td class="text-right"><a
                                                    href="<?php echo website_base_url; ?>flight_info.php?id=<?php echo $flight->id; ?>"
                                                    class="btn btn-success js_showloader">View</a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <p class="help-block">Suggested flights updated every hour.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="row" style="width: 100%">
            <div class="col-12">
                 <div class="global-heading">
                     <?php if (!empty($latestFlight)) { ?>
                            <span class="text-right" style="clear:left;float:right;"><a
                                    href="<?php echo website_base_url; ?>pirep_info.php?id=<?php echo $latestFlight->id; ?>">View
                                    Flight Report <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                            </span>
                        <?php } ?>
                    <h3 class="global-title w-full">Previous Flight</h3>
                </div>
                <div class="glass-card">
                    <div class="">
                        <?php if (!empty($latestFlight)) { ?>
                            <?php include_once '../site_widgets/map_flight.php'; ?>
                            <div id="map" style="height:400px;"></div>
                        <?php } else { ?>
                            <p>No recorded flights.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="width: 100%">
            <div class="col-12">
                <div class="global-heading">
                    <h3 class="global-title">Upcoming Events</h3>
                </div>
                <div class="glass-card">
                    <div class="">
                        <?php if (!empty($activities)) { ?>
                            <?php
                            $noEvents = true;
                            foreach ($activities as $key => $activity) {
                                if ($activity->type == "Event") {
                                    $noEvents = false; ?>
                                    <div class="activity-card-container" data-activityid="<?php echo $activity->id; ?>"
                                        style="background-image:url(<?php echo website_base_url; ?>uploads/activities/<?php echo $activity->banner; ?>);background-color:#ccc;">
                                        <div class="activity-card-hidden">
                                            <div>
                                                <p><a
                                                        href="<?php echo website_base_url; ?>activity.php?id=<?php echo $activity->id; ?>"><?php echo $activity->title; ?></a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            } ?>
                            <?php if ($noEvents) { ?>
                                <p>
                                    There are currently no events.
                                </p>
                            <?php } ?>
                        <?php } else { ?>
                            <p>There are currently no events.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($notams != null) { ?>
            <div class="row" style="width: 100%">
                <div class="col-12">
                    <div class="global-heading">
                        <h3 class="global-title">NOTAMs</h3>
                    </div>
                    <div class="glass-card">
                        <div class="">
                            <?php if (!empty($notams)) { ?>
                                <ul>
                                    <?php foreach ($notams as $notam) { ?>
                                        <li><?php echo htmlspecialchars($notam->message); ?> (Valid until: <?php echo (new DateTime($notam->validUntil))->format('d M Y H:i'); ?>)</li>
                                    <?php } ?>
                                </ul>
                            <?php } else { ?>
                                <p>No NOTAMs currently available.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="row" style="width: 100%">
            <?php include_once '../site_widgets/news_feed.php'; ?>
        </div>
    </div>
    <?php include_once '../site_widgets/jump_seat_modal.php'; ?>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        $(".activity-card-container").hover(function() {
            $(this).children('.activity-card-hidden').fadeIn();
        }, function() {
            $(this).children('.activity-card-hidden').hide();
        });
        $(".activity-card-container").click(function() {
            Loader.start();
            window.location.href = "/activity.php?id=" + $(this).data("activityid");
        });
    });
</script>
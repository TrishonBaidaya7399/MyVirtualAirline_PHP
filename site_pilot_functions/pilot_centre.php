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
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include '../includes/header.php'; ?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <?php if ($hasactivebid) { ?>
                    <p class="text-center">You have a
                        flight scheduled from <strong><?php echo empty($bdepicao) ? 'Any' : $bdepicao; ?></strong>
                        to
                        <strong><?php echo empty($barricao) ? 'Any' : $barricao; ?></strong>.
                        Visit
                        the <a href="<?php echo website_base_url; ?>site_pilot_functions/dispatch.php"
                            class="btn btn-default js_showloader">Dispatch
                            Center</a>
                        for your flight briefing.
                        <hr />
                    </p>
                <?php } ?>
                <div class="jumbotron">
                    <?php if ($pilot->profileImage != "") { ?>
                        <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $pilot->profileImage ?>"
                            width="200" style="float:right;" class="img-circle pilot-profile-image" />
                    <?php } else { ?>
                        <i class="fa fa-user-circle profile" aria-hidden="true" style="float:right;"></i>
                    <?php } ?>
                    <h1>Hello, <?php echo explode(" ", $_SESSION['name'])[0]; ?>.
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
                    <p>
                        <strong><i class="fa fa-trophy gold" aria-hidden="true"></i></strong> Best Landing:

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
        <?php if ($suggestedFlights != null) { ?>
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Suggested Flights</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><strong>Flight Number</strong></th>
                                        <th><strong>Departure</strong></th>
                                        <th><strong>Arrival</strong></th>
                                        <th><strong>Duration</strong></th>
                                        <th><strong>Aircraft</strong></th>
                                        <th><strong>Operator</strong></th>
                                        <th>&nbsp;</th>
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
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php if (!empty($latestFlight)) { ?>
                            <span class="text-right" style="clear:left;float:right;"><a
                                    href="<?php echo website_base_url; ?>pirep_info.php?id=<?php echo $latestFlight->id; ?>">View
                                    Flight Report <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                            </span>
                        <?php } ?>
                        <h3 class="panel-title">Previous Flight</h3>

                    </div>
                    <div class="panel-body">
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
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Upcoming Events</h3>
                    </div>
                    <div class="panel-body">
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
        <div class="row">
            <?php include_once '../site_widgets/news_feed.php'; ?>
        </div>

    </div>
    <?php include_once '../site_widgets/jump_seat_modal.php'; ?>
</section>
<?php include '../includes/footer.php'; ?>
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